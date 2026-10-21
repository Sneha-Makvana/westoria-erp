<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BranchCatalogSyncService
{
    public const STATUS_KEY = 'branch_catalog_sync_status';

    public function status(): array
    {
        return Cache::get(self::STATUS_KEY, [
            'running' => false,
            'done' => false,
            'message' => 'No sync started yet.',
            'source_branch_id' => null,
            'source_count' => 0,
            'processed' => 0,
            'created' => 0,
            'skipped' => 0,
            'failed' => 0,
            'after_id' => 0,
            'remaining' => 0,
            'target_counts' => [],
            'started_at' => null,
            'finished_at' => null,
            'error' => null,
        ]);
    }

    public function writeStatus(array $data): void
    {
        Cache::put(self::STATUS_KEY, array_merge($this->status(), $data), now()->addDays(14));
    }

    /**
     * Copy all products from source branch to every other admin/sub-admin branch (qty 0).
     */
    public function run(?int $sourceBranchId = null, int $chunk = 100, bool $reset = false, ?callable $onProgress = null): array
    {
        @set_time_limit(0);
        ignore_user_abort(true);

        if (Cache::get('branch_catalog_sync_lock')) {
            return $this->status();
        }
        Cache::put('branch_catalog_sync_lock', 1, now()->addHours(2));

        try {
            return $this->runLocked($sourceBranchId, $chunk, $reset, $onProgress);
        } finally {
            Cache::forget('branch_catalog_sync_lock');
        }
    }

    private function runLocked(?int $sourceBranchId = null, int $chunk = 100, bool $reset = false, ?callable $onProgress = null): array
    {
        if ($sourceBranchId === null || $sourceBranchId <= 0) {
            $sourceBranchId = (int) (User::query()
                ->where('role', 'admin')
                ->where('isDeleted', 0)
                ->orderBy('id')
                ->value('id') ?? 0);
        }

        if ($sourceBranchId <= 0) {
            throw new \RuntimeException('No admin/source branch found.');
        }

        $user = User::find($sourceBranchId);
        if (! $user) {
            throw new \RuntimeException('Source branch user not found.');
        }

        $targetBranchIds = User::query()
            ->whereIn('role', ['admin', 'sub-admin'])
            ->where('isDeleted', 0)
            ->where('id', '!=', $sourceBranchId)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $sourceCount = Product::query()
            ->where('branch_id', $sourceBranchId)
            ->where('isDeleted', 0)
            ->count();

        $progressKey = 'product_branch_sync_after_'.$sourceBranchId;
        $afterId = $reset ? 0 : max(0, (int) Cache::get($progressKey, 0));

        if ($reset) {
            Cache::forget($progressKey);
            $afterId = 0;
        }

        // Resume at the FIRST missing product (never MAX synced id — that skips gaps).
        if ($targetBranchIds !== [] && $sourceCount > 0) {
            $firstMissing = $this->firstMissingSourceIdAcrossTargets($sourceBranchId, $targetBranchIds);
            if ($firstMissing !== null) {
                // Move cursor back if it already skipped missing rows.
                if ($afterId >= $firstMissing) {
                    $afterId = max(0, $firstMissing - 1);
                }
            }
        }

        $totals = [
            'created' => 0,
            'skipped' => 0,
            'failed' => 0,
            'processed' => 0,
        ];

        $this->writeStatus([
            'running' => true,
            'done' => false,
            'message' => 'Sync running...',
            'source_branch_id' => $sourceBranchId,
            'source_count' => $sourceCount,
            'processed' => 0,
            'created' => 0,
            'skipped' => 0,
            'failed' => 0,
            'after_id' => $afterId,
            'remaining' => max(0, $sourceCount),
            'target_counts' => $this->branchCounts($targetBranchIds),
            'started_at' => now()->toDateTimeString(),
            'finished_at' => null,
            'error' => null,
        ]);

        if ($targetBranchIds === []) {
            $this->writeStatus([
                'running' => false,
                'done' => true,
                'message' => 'No other branches found.',
                'finished_at' => now()->toDateTimeString(),
            ]);

            return $this->status();
        }

        $prevSkip = Product::$skipBarcodeHistory;
        Product::$skipBarcodeHistory = true;

        try {
            do {
                $products = Product::query()
                    ->with(['variants', 'category', 'brand'])
                    ->where('branch_id', $sourceBranchId)
                    ->where('isDeleted', 0)
                    ->where('id', '>', $afterId)
                    ->orderBy('id')
                    ->limit($chunk)
                    ->get();

                if ($products->isEmpty()) {
                    break;
                }

                foreach ($products as $product) {
                    $afterId = (int) $product->id;
                    $totals['processed']++;

                    foreach ($targetBranchIds as $targetBranchId) {
                        try {
                            $result = $this->cloneProductToBranch($product, (int) $targetBranchId, $user);
                            if ($result === 'created') {
                                $totals['created']++;
                            } elseif ($result === 'skipped') {
                                $totals['skipped']++;
                            } else {
                                $totals['failed']++;
                            }
                        } catch (\Throwable $e) {
                            $totals['failed']++;
                            Log::warning('Branch catalog clone failed', [
                                'product_id' => $product->id,
                                'target_branch_id' => $targetBranchId,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }

                Cache::put($progressKey, $afterId, now()->addDays(14));

                $remaining = Product::query()
                    ->where('branch_id', $sourceBranchId)
                    ->where('isDeleted', 0)
                    ->where('id', '>', $afterId)
                    ->count();

                $this->writeStatus([
                    'running' => true,
                    'done' => false,
                    'message' => 'Sync running...',
                    'processed' => $totals['processed'],
                    'created' => $totals['created'],
                    'skipped' => $totals['skipped'],
                    'failed' => $totals['failed'],
                    'after_id' => $afterId,
                    'remaining' => $remaining,
                    'target_counts' => $this->branchCounts($targetBranchIds),
                ]);

                if ($onProgress) {
                    $onProgress($this->status());
                }
            } while (true);
        } catch (\Throwable $e) {
            Product::$skipBarcodeHistory = $prevSkip;
            $this->writeStatus([
                'running' => false,
                'done' => false,
                'message' => 'Sync stopped with error.',
                'error' => $e->getMessage(),
                'finished_at' => now()->toDateTimeString(),
            ]);
            throw $e;
        }

        Product::$skipBarcodeHistory = $prevSkip;

        // Second pass: any leftover missing SKUs (gaps / empty-cursor edge cases).
        $missingPass = $this->syncMissingBySku($sourceBranchId, $targetBranchIds, $user, $chunk, $onProgress, $totals);
        $afterId = max($afterId, (int) ($missingPass['after_id'] ?? $afterId));

        Cache::forget($progressKey);

        $targetCounts = $this->branchCounts($targetBranchIds);
        $minTarget = $targetCounts !== [] ? min($targetCounts) : 0;
        $fullySynced = $minTarget >= $sourceCount;

        $this->writeStatus([
            'running' => false,
            'done' => $fullySynced,
            'message' => $fullySynced
                ? 'All products copied to all branches.'
                : 'Sync finished but some branches still have fewer products. Re-run with --reset.',
            'processed' => $totals['processed'],
            'created' => $totals['created'],
            'skipped' => $totals['skipped'],
            'failed' => $totals['failed'],
            'after_id' => $afterId,
            'remaining' => max(0, $sourceCount - $minTarget),
            'target_counts' => $targetCounts,
            'finished_at' => now()->toDateTimeString(),
            'error' => null,
        ]);

        return $this->status();
    }

    /**
     * Lowest source product id that is missing (by SKU) from any target branch.
     */
    private function firstMissingSourceIdAcrossTargets(int $sourceBranchId, array $targetBranchIds): ?int
    {
        $first = null;
        foreach ($targetBranchIds as $targetBranchId) {
            $id = $this->firstMissingSourceId($sourceBranchId, (int) $targetBranchId);
            if ($id === null) {
                continue;
            }
            $first = $first === null ? $id : min($first, $id);
        }

        return $first;
    }

    private function firstMissingSourceId(int $sourceBranchId, int $targetBranchId): ?int
    {
        try {
            $id = DB::table('products as s')
                ->leftJoin('products as t', function ($join) use ($targetBranchId) {
                    $join->on('s.SKU', '=', 't.SKU')
                        ->where('t.branch_id', $targetBranchId)
                        ->where('t.isDeleted', 0);
                })
                ->where('s.branch_id', $sourceBranchId)
                ->where('s.isDeleted', 0)
                ->whereNotNull('s.SKU')
                ->where('s.SKU', '!=', '')
                ->whereNull('t.id')
                ->orderBy('s.id')
                ->value('s.id');

            return $id ? (int) $id : null;
        } catch (\Throwable $e) {
            Log::warning('firstMissingSourceId failed: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Clone any source products whose SKU is still missing on a target branch.
     */
    private function syncMissingBySku(
        int $sourceBranchId,
        array $targetBranchIds,
        $user,
        int $chunk,
        ?callable $onProgress,
        array &$totals
    ): array {
        $afterId = 0;
        $prevSkip = Product::$skipBarcodeHistory;
        Product::$skipBarcodeHistory = true;

        try {
            foreach ($targetBranchIds as $targetBranchId) {
                $targetBranchId = (int) $targetBranchId;
                $skuCursor = 0;
                do {
                    $missingIds = DB::table('products as s')
                        ->leftJoin('products as t', function ($join) use ($targetBranchId) {
                            $join->on('s.SKU', '=', 't.SKU')
                                ->where('t.branch_id', $targetBranchId)
                                ->where('t.isDeleted', 0);
                        })
                        ->where('s.branch_id', $sourceBranchId)
                        ->where('s.isDeleted', 0)
                        ->where('s.id', '>', $skuCursor)
                        ->whereNotNull('s.SKU')
                        ->where('s.SKU', '!=', '')
                        ->whereNull('t.id')
                        ->orderBy('s.id')
                        ->limit($chunk)
                        ->pluck('s.id')
                        ->map(fn ($id) => (int) $id)
                        ->all();

                    if ($missingIds === []) {
                        break;
                    }

                    $skuCursor = max($missingIds);

                    $products = Product::query()
                        ->with(['variants', 'category', 'brand'])
                        ->whereIn('id', $missingIds)
                        ->orderBy('id')
                        ->get();

                    $chunkCreated = 0;
                    foreach ($products as $product) {
                        $afterId = max($afterId, (int) $product->id);
                        $totals['processed']++;
                        try {
                            $result = $this->cloneProductToBranch($product, $targetBranchId, $user);
                            if ($result === 'created') {
                                $totals['created']++;
                                $chunkCreated++;
                            } elseif ($result === 'skipped') {
                                $totals['skipped']++;
                            } else {
                                $totals['failed']++;
                            }
                        } catch (\Throwable $e) {
                            $totals['failed']++;
                            Log::warning('Branch catalog missing-pass clone failed', [
                                'product_id' => $product->id,
                                'target_branch_id' => $targetBranchId,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }

                    $this->writeStatus([
                        'running' => true,
                        'done' => false,
                        'message' => 'Copying missing products...',
                        'processed' => $totals['processed'],
                        'created' => $totals['created'],
                        'skipped' => $totals['skipped'],
                        'failed' => $totals['failed'],
                        'after_id' => $afterId,
                        'target_counts' => $this->branchCounts($targetBranchIds),
                    ]);

                    if ($onProgress) {
                        $onProgress($this->status());
                    }
                } while (true);
            }
        } finally {
            Product::$skipBarcodeHistory = $prevSkip;
        }

        return ['after_id' => $afterId];
    }

    public function startInBackground(int $sourceBranchId, bool $reset = false): bool
    {
        $php = PHP_BINARY ?: 'php';
        $artisan = base_path('artisan');
        $resetFlag = $reset ? ' --reset' : '';
        $cmd = '"'.$php.'" "'.$artisan.'" products:sync-to-branches --source='.(int) $sourceBranchId.' --chunk=120'.$resetFlag;

        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            pclose(popen('start /B '.$cmd.' > NUL 2>&1', 'r'));
        } else {
            exec('nohup '.$cmd.' > /dev/null 2>&1 &');
        }

        return true;
    }

    private function branchCounts(array $branchIds): array
    {
        $out = [];
        foreach ($branchIds as $id) {
            $out[(string) $id] = Product::query()
                ->where('branch_id', $id)
                ->where('isDeleted', 0)
                ->count();
        }

        return $out;
    }

    public function cloneProductToBranch(Product $product, int $targetBranchId, $user): string
    {
        $sku = trim((string) ($product->SKU ?? ''));
        $barcode = trim((string) ($product->barcode ?? ''));

        $existsQuery = Product::query()
            ->where('branch_id', $targetBranchId)
            ->where('isDeleted', 0);

        if ($sku !== '' || $barcode !== '') {
            $existsQuery->where(function ($q) use ($sku, $barcode) {
                if ($sku !== '' && $barcode !== '') {
                    $q->where('SKU', $sku)->orWhere('barcode', $barcode);
                } elseif ($sku !== '') {
                    $q->where('SKU', $sku);
                } else {
                    $q->where('barcode', $barcode);
                }
            });
        } else {
            $existsQuery->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim((string) $product->name))]);
        }

        if ($existsQuery->exists()) {
            return 'skipped';
        }

        $product->loadMissing(['variants', 'category', 'brand']);

        $categoryId = $this->resolveCategoryForBranch($product->category_id, $targetBranchId, $product->category)
            ?: $product->category_id;
        $brandId = $this->resolveBrandForBranch($product->brand_id, $targetBranchId, $product->brand);
        if ($brandId === null && ! empty($product->brand_id)) {
            $brandId = $product->brand_id;
        }

        if (empty($categoryId)) {
            return 'failed';
        }

        $payload = [
            'vendor_id' => $product->vendor_id,
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'branch_id' => $targetBranchId,
            'name' => $product->name,
            'name_hindi' => $product->name_hindi,
            'description' => $product->description,
            'price' => $product->price,
            'selling_prices' => $product->selling_prices,
            'mrp' => $product->mrp,
            'barcode' => $product->barcode,
            'quantity' => 0,
            'imei_no' => null,
            'unit_id' => $product->unit_id,
            'status' => $product->status ?? 'active',
            'batch_no' => $product->batch_no,
            'label_code' => $product->label_code,
            'label_reference' => $product->label_reference,
            'specific_qty' => $product->specific_qty,
            'purchase_price' => $product->purchase_price,
            'cost_price' => $product->cost_price,
            'landing_cost' => $product->landing_cost,
            'product_code' => $product->product_code,
            'supplier_code' => $product->supplier_code,
            'international_code' => $product->international_code,
            'serial_no_status' => $product->serial_no_status,
            'non_inventory_type' => $product->non_inventory_type,
            'stock_validation_status' => $product->stock_validation_status,
            'item_type' => $product->item_type,
            'discount_print_status' => $product->discount_print_status,
            'item_created_on' => $product->item_created_on,
            'SKU' => $product->SKU,
            'hsn_code' => $product->hsn_code,
            'tax_rate' => $product->tax_rate,
            'item_location' => $product->item_location,
            'minimum_stock_qty' => $product->minimum_stock_qty ?? 0,
            'discount_type' => $product->discount_type,
            'sale_discount' => $product->sale_discount ?? 0,
            'inclusive_of_tax' => $product->inclusive_of_tax ? 1 : 0,
            'secondary_unit_id' => $product->secondary_unit_id,
            'conversion_rate' => $product->conversion_rate,
            'availablility' => 'out_stock',
            'gst_option' => $product->gst_option ?? 'without_gst',
            'product_gst' => is_array($product->product_gst)
                ? json_encode($product->product_gst)
                : $product->product_gst,
            'images' => is_array($product->images)
                ? json_encode($product->images)
                : $product->images,
            'is_rent_available' => $product->is_rent_available ?? 0,
            'rent_period' => $product->rent_period,
            'rent_price' => $product->rent_price,
            'create_by' => $user->id ?? null,
            'isDeleted' => 0,
        ];

        try {
            $clone = Product::create($payload);
        } catch (\Throwable $e) {
            $msg = strtolower($e->getMessage());
            if ($barcode !== '' && (str_contains($msg, 'barcode') || str_contains($msg, 'duplicate'))) {
                $payload['barcode'] = null;
                $clone = Product::create($payload);
            } else {
                throw $e;
            }
        }

        foreach ($product->variants as $variant) {
            try {
                $clone->variants()->create([
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'qty' => 0,
                    'price' => $variant->price,
                    'purchase_price' => $variant->purchase_price,
                    'barcode' => $variant->barcode,
                ]);
            } catch (\Throwable $e) {
                $clone->variants()->create([
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'qty' => 0,
                    'price' => $variant->price,
                    'purchase_price' => $variant->purchase_price,
                    'barcode' => null,
                ]);
            }
        }

        // Skip inventory rows during bulk sync for speed (qty already 0 on product).

        return 'created';
    }

    private function resolveCategoryForBranch($sourceCategoryId, int $targetBranchId, $sourceCategory = null): ?int
    {
        if (empty($sourceCategoryId) && ! $sourceCategory) {
            return null;
        }

        $source = $sourceCategory ?: Category::find($sourceCategoryId);
        if (! $source) {
            return null;
        }

        $name = trim((string) $source->name);
        if ($name === '') {
            return null;
        }

        $existing = Category::query()
            ->where('branch_id', $targetBranchId)
            ->where('isDeleted', 0)
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])
            ->first();

        if ($existing) {
            return (int) $existing->id;
        }

        $created = Category::create([
            'name' => $source->name,
            'image' => $source->image,
            'branch_id' => $targetBranchId,
            'isDeleted' => 0,
        ]);

        return (int) $created->id;
    }

    private function resolveBrandForBranch($sourceBrandId, int $targetBranchId, $sourceBrand = null): ?int
    {
        if (empty($sourceBrandId) && ! $sourceBrand) {
            return null;
        }

        $source = $sourceBrand ?: Brand::find($sourceBrandId);
        if (! $source) {
            return null;
        }

        $name = trim((string) $source->name);
        if ($name === '') {
            return null;
        }

        $existing = Brand::query()
            ->where('branch_id', $targetBranchId)
            ->where('isDeleted', 0)
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])
            ->first();

        if ($existing) {
            return (int) $existing->id;
        }

        $created = Brand::create([
            'name' => $source->name,
            'logo' => $source->logo,
            'email' => $source->email,
            'phone' => $source->phone,
            'branch_id' => $targetBranchId,
            'isDeleted' => 0,
        ]);

        return (int) $created->id;
    }
}
