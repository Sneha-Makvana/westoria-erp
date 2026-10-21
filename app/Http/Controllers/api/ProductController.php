<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\ProductVariant;
use App\Models\RowMaterial;
use App\Models\Purchases;
use App\Models\TaxRate;
use App\Models\Unit;
use App\Models\User;
use App\Services\ProductImportPreviewStore;
use App\Services\ProductImportRowMapper;
use App\Services\ProductImportStreamParser;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Services\BarcodeLabelRenderer;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    private static array $importCategoryCache = [];

    private static array $importBrandCache = [];

    private static array $importUnitCache = [];

    private static array $importProductCodeCache = [];

    private static array $importProductNameCache = [];

    private static ?array $importJobLookupMaps = null;

    private static array $importProductModelCache = [];

    private static ?string $currentImportJobKey = null;

    private function resetImportRuntimeCaches(): void
    {
        self::$importCategoryCache = [];
        self::$importBrandCache = [];
        self::$importUnitCache = [];
        self::$importProductCodeCache = [];
        self::$importProductNameCache = [];
        self::$importJobLookupMaps = null;
        self::$importProductModelCache = [];
        self::$currentImportJobKey = null;
    }

    private function importLookupCacheKey(string $jobKey): string
    {
        return $jobKey . '_lookups';
    }

    private function buildImportProductLookupMaps(int $branchId): array
    {
        $byCode = [];
        $byName = [];

        Product::where('branch_id', $branchId)
            ->where('isDeleted', 0)
            ->select(['id', 'barcode', 'SKU', 'product_code', 'name'])
            ->chunkById(2000, function ($products) use (&$byCode, &$byName) {
                foreach ($products as $product) {
                    $id = (int) $product->id;

                    $normalizedName = strtolower(trim((string) $product->name));
                    if ($normalizedName !== '') {
                        $byName[$normalizedName] = $id;
                    }

                    foreach (explode(',', (string) $product->barcode) as $part) {
                        $code = strtoupper($this->sanitizeImportBarcode(trim($part)));
                        if ($code !== '') {
                            $byCode[$code] = $id;
                        }
                    }

                    foreach ([$product->SKU, $product->product_code] as $codeValue) {
                        $code = strtoupper($this->sanitizeImportBarcode((string) $codeValue));
                        if ($code !== '') {
                            $byCode[$code] = $id;
                        }
                    }
                }
            }, 'id');

        return [
            'by_code' => $byCode,
            'by_name' => $byName,
        ];
    }

    private function importLookupMapsHaveProductIds(array $maps): bool
    {
        foreach (['by_code', 'by_name'] as $key) {
            if (empty($maps[$key])) {
                continue;
            }

            $sample = reset($maps[$key]);
            if (!is_numeric($sample) || (int) $sample <= 0) {
                return false;
            }
        }

        return true;
    }

    private function ensureImportJobLookupMaps(string $jobKey, int $branchId, ?string $previewToken = null): array
    {
        $cacheKey = $this->importLookupCacheKey($jobKey);
        $maps     = Cache::get($cacheKey);

        if (!is_array($maps) || !isset($maps['by_code'], $maps['by_name']) || !$this->importLookupMapsHaveProductIds($maps)) {
            $maps = null;

            if ($previewToken !== null) {
                $preview = Cache::get($this->importPreviewCacheKey($previewToken));
                $previewLookups = is_array($preview['lookups'] ?? null) ? $preview['lookups'] : null;
                if ($previewLookups !== null
                    && isset($previewLookups['by_code'], $previewLookups['by_name'])
                    && $this->importLookupMapsHaveProductIds($previewLookups)) {
                    $maps = [
                        'by_code' => $previewLookups['by_code'],
                        'by_name' => $previewLookups['by_name'],
                    ];
                }
            }

            if (!is_array($maps)) {
                $maps = $this->buildImportProductLookupMaps($branchId);
            }

            Cache::put($cacheKey, $maps, now()->addHours(3));
        }

        return $maps;
    }

    private function registerImportLookupProduct(string $jobKey, Product $product, string $itemCode): void
    {
        $cacheKey = $this->importLookupCacheKey($jobKey);
        $maps     = Cache::get($cacheKey);
        if (!is_array($maps)) {
            return;
        }

        $id = (int) $product->id;

        $nameKey = strtolower(trim((string) $product->name));
        if ($nameKey !== '') {
            $maps['by_name'][$nameKey] = $id;
        }

        $code = strtoupper($this->sanitizeImportBarcode($itemCode));
        if ($code !== '') {
            $maps['by_code'][$code] = $id;
        }

        foreach (explode(',', (string) $product->barcode) as $part) {
            $partCode = strtoupper($this->sanitizeImportBarcode(trim($part)));
            if ($partCode !== '') {
                $maps['by_code'][$partCode] = $id;
            }
        }

        foreach ([$product->SKU, $product->product_code] as $codeValue) {
            $storedCode = strtoupper($this->sanitizeImportBarcode((string) $codeValue));
            if ($storedCode !== '') {
                $maps['by_code'][$storedCode] = $id;
            }
        }

        Cache::put($cacheKey, $maps, now()->addHours(3));

        if (self::$importJobLookupMaps !== null) {
            self::$importJobLookupMaps = $maps;
        }
    }

    private function warmImportReferenceCaches(int $branchId): void
    {
        if (self::$importCategoryCache !== []) {
            return;
        }

        foreach (Category::where('branch_id', $branchId)->where('isDeleted', 0)->get(['id', 'name']) as $category) {
            $key = (int) $branchId . '|' . strtolower(trim((string) $category->name));
            self::$importCategoryCache[$key] = $category;
        }

        foreach (Brand::where('branch_id', $branchId)->where('isDeleted', 0)->get(['id', 'name']) as $brand) {
            $key = (int) $branchId . '|' . strtolower(trim((string) $brand->name));
            self::$importBrandCache[$key] = $brand;
        }

        foreach (Unit::where('is_delete', 0)->get(['id', 'unit_name']) as $unit) {
            self::$importUnitCache[strtolower(trim((string) $unit->unit_name))] = $unit;
        }
    }

    private function hydrateImportJobContext(string $jobKey, int $branchId, ?string $previewToken = null): void
    {
        self::$currentImportJobKey = $jobKey;
        self::$importJobLookupMaps = $this->ensureImportJobLookupMaps($jobKey, $branchId, $previewToken);
        $this->warmImportReferenceCaches($branchId);
    }

    private function importItemCodeExists(int $branchId, string $normalizedCode): bool
    {
        if ($normalizedCode === '') {
            return false;
        }

        if (self::$importJobLookupMaps !== null) {
            return isset(self::$importJobLookupMaps['by_code'][$normalizedCode]);
        }

        return Product::where('branch_id', $branchId)
            ->where(function ($q) use ($normalizedCode) {
                $q->where('barcode', $normalizedCode)
                    ->orWhere('SKU', $normalizedCode)
                    ->orWhere('product_code', $normalizedCode);
            })
            ->exists();
    }

    private function generateUniqueImportItemCode(int $branchId): string
    {
        do {
            $itemCode = 'ITEM' . mt_rand(10000000, 99999999);
        } while ($this->importItemCodeExists($branchId, strtoupper($this->sanitizeImportBarcode($itemCode))));

        return $itemCode;
    }

    private function findImportProductModelById(int $productId): ?Product
    {
        if ($productId <= 0) {
            return null;
        }

        if (!array_key_exists($productId, self::$importProductModelCache)) {
            self::$importProductModelCache[$productId] = Product::find($productId);
        }

        return self::$importProductModelCache[$productId];
    }

    private function importJobCacheKey(string $token): string
    {
        return 'product_import_job_' . $token;
    }

    private function resolveBarcodeSettings($branchId): object
    {
        $branchId = is_numeric($branchId) ? (int) $branchId : null;
        
        if ($branchId) {
            return DB::table('settings')->where('branch_id', $branchId)->first()
                ?? DB::table('settings')->first()
                ?? (object) [
                    'barcode_setting_type' => 'simple',
                    'barcode_detail_option' => 'long',
                ];
        }

        return DB::table('settings')->first()
            ?? (object) [
                'barcode_setting_type' => 'simple',
                'barcode_detail_option' => 'long',
            ];
    }

    private function resolveBarcodeLayout(?object $settings): array
    {
        $type = $settings->barcode_setting_type ?? 'simple';
        $detailOption = $settings->barcode_detail_option ?? 'long';

        if ($type !== 'detailed') {
            $type = 'simple';
            $detailOption = null;
        }

        return [$type, $detailOption];
    }

    private function formatMoney(?float $amount, ?object $settings = null): string
    {
        if ($amount === null) {
            return '-';
        }

        $symbol = trim((string) ($settings->currency_symbol ?? '₹'));
        $position = $settings->currency_position ?? 'left';
        $value = number_format($amount, 2);

        return $position === 'left' ? $symbol . $value : $value . $symbol;
    }

    private function buildBarcodeLabelData(Product $product, ?object $variant, ?object $settings, string $layout): array
    {
        $size = trim((string) ($variant->size ?? ''));
        $color = trim((string) ($variant->color ?? ''));
        $variantText = trim(implode(' ', array_filter([$size, $color])));
        $displayName = $variantText !== '' ? $product->name . ' (' . $variantText . ')' : $product->name;

        $price = $variant->price ?? $product->price ?? null;
        $purchasePrice = $variant->purchase_price ?? $product->purchase_price ?? null;
        $barcode = $variant->barcode ?? $product->barcode ?? '';

        return [
            'id' => $variant?->id ? 'variant_' . $variant->id : 'product_' . $product->id,
            'type' => $variant ? 'variant' : 'product',
            'layout' => $layout,
            'name' => $product->name ?? '',
            'display_name' => $displayName,
            'sku' => $product->SKU ?? '-',
            'category' => $product->category->name ?? '-',
            'brand' => $product->brand->name ?? '-',
            'unit' => $product->unit->unit_name ?? '-',
            'size' => $size !== '' ? $size : '-',
            'color' => $color !== '' ? $color : '-',
            'price' => $price,
            'mrp' => $product->mrp ?? null,
            'purchase_price' => $purchasePrice,
            'price_text' => $this->formatMoney($price !== null ? (float) $price : null, $settings),
            'purchase_price_text' => $this->formatMoney($purchasePrice !== null ? (float) $purchasePrice : null, $settings),
            'barcode' => $barcode,
            'barcode_image' => null,
            'batch_no' => $product->batch_no ?? '',
            'label_code' => $product->label_code ?? '',
            'label_reference' => $product->label_reference ?? '',
            'quantity' => $product->quantity ?? null,
            'unit_name' => $product->unit->unit_name ?? '-',
        ];
    }

    private function generateBarcodeImage(DNS1D $generator, string $value, string $layout, ?string $detailOption = null, ?string $barcodeType = 'barcode', ?int $labelWidthMm = null, ?int $labelHeightMm = null): string
    {
        // If barcode type is QR code, generate QR code instead
        if ($barcodeType === 'qrcode') {
            $dns2d = new DNS2D();
            return 'data:image/svg+xml;base64,' . base64_encode($dns2d->getBarcodeSVG($value, 'QRCODE', 5, 5));
        }

        // Detailed layouts keep SVG (rotated/positioned in those templates).
        if ($layout === 'detailed' && $detailOption === 'short') {
            return 'data:image/svg+xml;base64,' . base64_encode($generator->getBarcodeSVG($value, 'C128', 1.35, 34, 'black', false));
        }

        if ($layout === 'detailed' && $detailOption === 'long') {
            return 'data:image/svg+xml;base64,' . base64_encode($generator->getBarcodeSVG($value, 'C128', 1.8, 48, 'black', false));
        }

        // Simple sticker labels: PNG avoids DomPDF slicing SVG barcode bars.
        $barWidth = ($labelWidthMm !== null && $labelWidthMm <= 28) ? 1.2 : 2;
        $barHeight = ($labelHeightMm !== null && $labelHeightMm >= 45) ? 70 : 50;

        try {
            return 'data:image/png;base64,' . $generator->getBarcodePNG($value, 'C128', $barWidth, $barHeight, [0, 0, 0], false);
        } catch (\Throwable $e) {
            return 'data:image/svg+xml;base64,' . base64_encode($generator->getBarcodeSVG($value, 'C128', $barWidth, $barHeight, 'black', false));
        }
    }

    /**
     * Client roll sizes: 35x25x1 (TSC 1-up), 35x25x3, 38x25x2, 25x50x2.
     */
    private function parseLabelSize(?string $labelSize): array
    {
        $key = strtolower(preg_replace('/[^0-9x]/', '', (string) $labelSize));

        $sizes = [
            '35x25x1' => [
                'key' => '35x25x3',
                'width' => 35,
                'height' => 25,
                'cols' => 3,
                'font' => 6.2,
                'barcode' => 5.0,
                'pad' => 1.1,
                'tall' => false,
            ],
            '35x25x3' => [
                'key' => '35x25x3',
                'width' => 35,
                'height' => 25,
                'cols' => 3,
                'font' => 6.2,
                'barcode' => 5.0,
                'pad' => 1.1,
                'tall' => false,
            ],
            '38x25x2' => [
                'key' => '38x25x2',
                'width' => 38,
                'height' => 25,
                'cols' => 2,
                'font' => 6.4,
                'barcode' => 5.0,
                'pad' => 1.2,
                'tall' => false,
            ],
            '25x50x2' => [
                'key' => '25x50x2',
                'width' => 25,
                'height' => 50,
                'cols' => 2,
                'font' => 6.2,
                'barcode' => 11.0,
                'pad' => 1.4,
                'tall' => true,
            ],
        ];

        return $sizes[$key] ?? $sizes['35x25x3'];
    }

    private function mmToPoints(float $mm): float
    {
        return $mm * 72 / 25.4;
    }

    private function isMobileCategoryId($categoryId): bool
    {
        if (empty($categoryId)) {
            return false;
        }

        $categoryName = strtolower(trim((string) Category::where('id', $categoryId)->value('name')));
        return str_contains($categoryName, 'mobile');
    }

    private function isWarrantyCategoryId($categoryId): bool
    {
        if (empty($categoryId)) {
            return false;
        }

        return strtolower(trim((string) Category::where('id', $categoryId)->value('name'))) === 'warranty';
    }

    private function validateUniqueProductName($validator, $name, $branchId, int $ignoreProductId = 0): void
    {
        $normalized = mb_strtolower(trim((string) $name));
        if ($normalized === '' || empty($branchId)) {
            return;
        }

        $query = Product::query()
            ->where('branch_id', $branchId)
            ->where('isDeleted', 0)
            ->whereRaw('LOWER(TRIM(name)) = ?', [$normalized]);

        if ($ignoreProductId > 0) {
            $query->where('id', '!=', $ignoreProductId);
        }

        if ($query->exists()) {
            $validator->errors()->add('name', 'A product with this name already exists.');
        }
    }


    public function getAllProduct(Request $request)
    {
        $user = Auth::guard('api')->user();

        $branchId = match (strtolower($user->role)) {
            'admin'     => $request->sub_branch_id ?: $user->id,
            'sub-admin' => $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);
        $search = trim((string) $request->input('search', ''));
        $shouldPaginate = $request->has('page') || $request->has('per_page') || $request->filled('search');
        $deletedRaw = $request->input('deleted', $request->input('status'));
        $showDeleted = in_array($deletedRaw, [1, '1', true, 'true', 'yes', 'deleted'], true);

        $settings = $this->resolveBarcodeSettings($branchId);
        $enableVariants = $settings ? ($settings->enable_variant_product ?? 1) : 1;

        $query = Product::query()
            ->select([
                'id',
                'name',
                'name_hindi',
                'SKU',
                'price',
                'purchase_price',
                'quantity',
                'unit_id',
                'category_id',
                'brand_id',
                'branch_id',
                'images',
                'barcode',
                'barcode_details',
                'product_code',
                'item_type',
                'selling_prices',
                'mrp',
                'batch_no',
                'label_code',
                'label_reference',
                'label_size',
                'is_rent_available',
                'rent_period',
                'rent_price',
                'specific_qty',
                'gst_option',
                'product_gst',
                'isDeleted',
            ])
            ->with([
                'category:id,name',
                'brand:id,name',
                'unit:id,unit_name',
                'variants:id,product_id,barcode,size,color,qty,price,purchase_price',
            ])
            // Always scope to active OR deleted — never mix both lists
            ->where('isDeleted', $showDeleted ? 1 : 0)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->unit_id, fn($q) => $q->where('unit_id', $request->unit_id))
            ->when($request->brand_id, fn($q) => $q->where('brand_id', $request->brand_id))
            ->when($search !== '', function ($q) use ($search, $showDeleted) {
                // Deleted list: match name / SKU / barcode only (not category name),
                // so searching "default" does not pull every item in Default Category.
                $q->matchingSearch($search, ! $showDeleted);
            })
            ->when(!$enableVariants, function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('item_type')
                        ->orWhere('item_type', '')
                        ->orWhereRaw("LOWER(item_type) <> 'variant'");
                });
            });

        $this->applyProductDateFilter($query, $request);

        if ($request->boolean('quick_update')) {
            $query->addSelect([
                'hsn_code',
                'item_location',
                'minimum_stock_qty',
                'cost_price',
                'landing_cost',
                'description',
                'created_at',
                'updated_at',
            ]);
        }

        $pagination = null;

        if ($shouldPaginate) {
            $paginatedProducts = $query
                ->latest('id')
                ->paginate($perPage, ['*'], 'page', $page);

            $products = $paginatedProducts->items();

            $pagination = [
                'current_page' => $paginatedProducts->currentPage(),
                'last_page' => $paginatedProducts->lastPage(),
                'per_page' => $paginatedProducts->perPage(),
                'total' => $paginatedProducts->total(),
                'next_page_url' => $paginatedProducts->nextPageUrl(),
                'prev_page_url' => $paginatedProducts->previousPageUrl(),
            ];
        } else {
            $products = $query
                ->latest('id')
                ->get();
        }

        $settingsQuery = DB::table('settings')
            ->select('low_stock', 'currency_symbol', 'currency_position', 'global_batch_no');

        if ($branchId) {
            $settingsQuery->where('branch_id', $branchId);
        }

        $settings = $settingsQuery->first();

        if (! $settings) {
            $settings = DB::table('settings')
                ->select('low_stock', 'currency_symbol', 'currency_position', 'global_batch_no')
                ->first();
        }

        return response()->json([
            'status'            => true,
            'data'              => $products,
            'pagination'        => $pagination,
            'currencySymbol'    => $settings->currency_symbol ?? '₹',
            'currencyPosition'  => $settings->currency_position ?? 'left',
            'lowStockThreshold' => $settings->low_stock ?? 0,
            'enableVariants'    => (int) $enableVariants,
            'globalBatchNo'     => $settings->global_batch_no ?? '',
        ]);
    }

    /**
     * date_field: created | updated | any (added or updated).
     */
    private function applyProductDateFilter($query, Request $request)
    {
        [$dateFrom, $dateTo] = $this->resolveProductDateFilter($request);
        if (! $dateFrom || ! $dateTo) {
            return $query;
        }

        $dateField = (string) $request->input('date_field', 'any');
        if (! in_array($dateField, ['created', 'updated', 'any'], true)) {
            $dateField = 'any';
        }

        return $query->where(function ($q) use ($dateField, $dateFrom, $dateTo) {
            if ($dateField !== 'updated') {
                $q->orWhereBetween('created_at', [$dateFrom, $dateTo]);
            }
            if ($dateField !== 'created') {
                $q->orWhereBetween('updated_at', [$dateFrom, $dateTo]);
            }
        });
    }

    /**
     * date_filter: today | yesterday | last5 | custom (with date_from / date_to as Y-m-d).
     */
    private function resolveProductDateFilter(Request $request): array
    {
        $preset = (string) $request->input('date_filter', '');
        $today = now();

        try {
            return match ($preset) {
                'today' => [$today->copy()->startOfDay(), $today->copy()->endOfDay()],
                'yesterday' => [$today->copy()->subDay()->startOfDay(), $today->copy()->subDay()->endOfDay()],
                'last5' => [$today->copy()->subDays(4)->startOfDay(), $today->copy()->endOfDay()],
                'custom' => $request->filled('date_from') || $request->filled('date_to')
                    ? (function () use ($request) {
                        $a = \Carbon\Carbon::parse($request->input('date_from') ?: $request->input('date_to'));
                        $b = \Carbon\Carbon::parse($request->input('date_to') ?: $request->input('date_from'));
                        if ($a->gt($b)) {
                            [$a, $b] = [$b, $a];
                        }
                        return [$a->startOfDay(), $b->endOfDay()];
                    })()
                    : [null, null],
                default => [null, null],
            };
        } catch (\Throwable $e) {
            return [null, null];
        }
    }

    public function export_product(Request $request)
    {
        $user = Auth::guard('api')->user();

        /* -------------------------------------------------
     | 1ï¸âƒ£ Resolve Branch ID (single, clean logic)
     -------------------------------------------------*/
        $branchId = match ($user->role) {
            'staff' => $user->branch_id,
            'admin' => $request->selectedSubAdminId ?: $user->id,
            default => $user->id,
        };

        /* -------------------------------------------------
     | 2ï¸âƒ£ Fetch Products
     -------------------------------------------------*/
        $products = \App\Models\Product::with(['category', 'brand', 'unit', 'variants'])
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->brand_id, fn($q) => $q->where('brand_id', $request->brand_id))
            ->when($request->unit_id, fn($q) => $q->where('unit_id', $request->unit_id))
            ->tap(fn($q) => $this->applyProductDateFilter($q, $request))
            ->latest('id')
            ->get();

        /* -------------------------------------------------
     | 3ï¸âƒ£ Currency Settings
     -------------------------------------------------*/
        $settings = DB::table('settings')
            ->where('branch_id', $branchId)
            ->first();

        $currencySymbol = trim(
            html_entity_decode($settings->currency_symbol ?? 'â‚¹', ENT_QUOTES | ENT_HTML5, 'UTF-8')
        );
        $currencyPosition = $settings->currency_position ?? 'left';

            // Helper function for Indian number formatting
    $formatIndianCurrency = function($amount) {
        if ($amount === null || $amount === '-') return '-';

        $amount = (float)$amount;
        $amount = number_format($amount, 2, '.', '');

        // Split into whole and decimal parts
        $parts = explode('.', $amount);
        $whole = $parts[0];
        $decimal = $parts[1];

        // Indian numbering system formatting
        $lastThree = substr($whole, -3);
        $otherNumbers = substr($whole, 0, -3);

        if ($otherNumbers != '') {
            $otherNumbers = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $otherNumbers);
            $formattedWhole = $otherNumbers . ',' . $lastThree;
        } else {
            $formattedWhole = $lastThree;
        }

        return $formattedWhole . '.' . $decimal;
    };


        /* -------------------------------------------------
     | 4ï¸âƒ£ Create Excel
     -------------------------------------------------*/
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            'A1' => 'Product Name',
            'B1' => 'SKU',
            'C1' => 'Size',
            'D1' => 'Color',
            'E1' => 'Barcode',
            'F1' => 'Category',
            'G1' => 'Brand',
            'H1' => 'Quantity',
            'I1' => 'Unit',
            'J1' => 'Price',
            'K1' => 'MRP',
            'L1' => 'Purchase Price',
            'M1' => 'Description',
            'N1' => 'Created At',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }
        $sheet->getStyle('A1:N1')->getFont()->setBold(true);

          // Set column widths for better readability
    $sheet->getColumnDimension('A')->setWidth(30); // Product Name
    $sheet->getColumnDimension('B')->setWidth(15); // SKU
    $sheet->getColumnDimension('C')->setWidth(15); // Size
    $sheet->getColumnDimension('D')->setWidth(15); // Color
    $sheet->getColumnDimension('E')->setWidth(15); // Barcode
    $sheet->getColumnDimension('F')->setWidth(15); // Category
    $sheet->getColumnDimension('G')->setWidth(15); // Brand
    $sheet->getColumnDimension('H')->setWidth(12); // Quantity
    $sheet->getColumnDimension('I')->setWidth(12); // Unit
    $sheet->getColumnDimension('J')->setWidth(18); // Price
    $sheet->getColumnDimension('K')->setWidth(18); // MRP
    $sheet->getColumnDimension('L')->setWidth(18); // Purchase Price
    $sheet->getColumnDimension('M')->setWidth(30); // Description
    $sheet->getColumnDimension('N')->setWidth(15); // Created At

        // Data
        $row = 2;
        foreach ($products as $product) {
            if ($product->variants && $product->variants->isNotEmpty()) {
                foreach ($product->variants as $variant) {
                    $formattedPrice = $formatIndianCurrency($variant->price);
                    $price = $currencyPosition === 'left' ? "{$currencySymbol}{$formattedPrice}" : "{$formattedPrice}{$currencySymbol}";
                    
                    $formattedMRP = $formatIndianCurrency($product->mrp);
                    $mrp = $currencyPosition === 'left' ? "{$currencySymbol}{$formattedMRP}" : "{$formattedMRP}{$currencySymbol}";

                    $formattedPurchase = $formatIndianCurrency($variant->purchase_price ?? $product->purchase_price);
                    $purchasePrice = $currencyPosition === 'left' ? "{$currencySymbol}{$formattedPurchase}" : "{$formattedPurchase}{$currencySymbol}";

                    $sheet->fromArray([
                        $product->name,
                        $product->SKU,
                        $variant->size ?? '-',
                        $variant->color ?? '-',
                        $variant->barcode ?? '-',
                        $product->category ? $product->category->name : '-',
                        $product->brand ? $product->brand->name : '-',
                        $variant->qty ?? '-',
                        $product->unit ? $product->unit->unit_name : '-',
                        $price,
                        $mrp,
                        $purchasePrice,
                        $product->description ?? '-',
                        \Carbon\Carbon::parse($product->created_at)->format('d-m-Y'),
                    ], null, "A{$row}");
                    
                    $sheet->getStyle('J' . $row)->getNumberFormat()
                          ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $row++;
                }
            } else {
                $formattedPrice = $formatIndianCurrency($product->price);
                $price = $currencyPosition === 'left' ? "{$currencySymbol}{$formattedPrice}" : "{$formattedPrice}{$currencySymbol}";
                
                $formattedMRP = $formatIndianCurrency($product->mrp);
                $mrp = $currencyPosition === 'left' ? "{$currencySymbol}{$formattedMRP}" : "{$formattedMRP}{$currencySymbol}";

                $formattedPurchase = $formatIndianCurrency($product->purchase_price);
                $purchasePrice = $currencyPosition === 'left' ? "{$currencySymbol}{$formattedPurchase}" : "{$formattedPurchase}{$currencySymbol}";
                
                $sheet->fromArray([
                    $product->name,
                    $product->SKU,
                    '-',
                    '-',
                    $product->barcode ?? '-',
                    $product->category ? $product->category->name : '-',
                    $product->brand ? $product->brand->name : '-',
                    $product->quantity ?? '-',
                    $product->unit ? $product->unit->unit_name : '-',
                    $price,
                    $mrp,
                    $purchasePrice,
                    $product->description ?? '-',
                    \Carbon\Carbon::parse($product->created_at)->format('d-m-Y'),
                ], null, "A{$row}");
                
                $sheet->getStyle('J' . $row)->getNumberFormat()
                      ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $row++;
            }
        }

        /* -------------------------------------------------
     | 5ï¸âƒ£ Save File
     -------------------------------------------------*/
        $fileName     = 'Products_' . now()->format('Ymd_His') . '.xlsx';
        $folder       = 'product-exports';
        $relativePath = "{$folder}/{$fileName}";

        Storage::disk('public')->makeDirectory($folder);

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path("app/public/{$relativePath}"));

        $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

        /* -------------------------------------------------
     | 6ï¸âƒ£ Response
     -------------------------------------------------*/
        return response()->json([
            'status'    => true,
            'message'   => 'Product Excel exported successfully.',
            'file_url'  => $fileUrl,
            'file_name' => $fileName,
        ]);
    }

    public function export_product_pdf(Request $request)
    {
        $user = Auth::guard('api')->user();

        /* -------------------------------------------------
     | 1ï¸âƒ£ Resolve Branch ID (single source of truth)
     -------------------------------------------------*/
        $branchId = match ($user->role) {
            'staff' => $user->branch_id,
            'admin' => $request->selectedSubAdminId ?: $user->id,
            default => $user->id,
        };

        /* -------------------------------------------------
     | 2ï¸âƒ£ Build Product Query
     -------------------------------------------------*/
        $products = \App\Models\Product::with(['category', 'brand', 'unit', 'variants'])
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->brand_id, fn($q) => $q->where('brand_id', $request->brand_id))
            ->tap(fn($q) => $this->applyProductDateFilter($q, $request))
            ->latest('id')
            ->get();
    // dd($products);
        /* -------------------------------------------------
     | 3ï¸âƒ£ Settings (currency, GST, branding)
     -------------------------------------------------*/
        $settings = DB::table('settings')
            ->where('branch_id', $branchId)
            ->first();

        $currencySymbol = trim(
            html_entity_decode($settings->currency_symbol ?? 'â‚¹', ENT_QUOTES | ENT_HTML5, 'UTF-8')
        );

        /* -------------------------------------------------
     | 4ï¸âƒ£ PDF Generation
     -------------------------------------------------*/
        $pdf = Pdf::loadView('product.product_pdf', [
            'products'         => $products,
            'currencySymbol'   => $currencySymbol,
            'currencyPosition' => $settings->currency_position ?? 'left',
            'settings'         => $settings,
            'userName'         => $user->name ?? 'N/A',
            'gstNum'           => $user->gst_number ?? 'N/A',
        ])->setPaper('A4', 'portrait');

        /* -------------------------------------------------
     | 5ï¸âƒ£ Store PDF
     -------------------------------------------------*/
        $fileName     = 'Products_' . now()->format('Ymd_His') . '.pdf';
        $folder       = 'all-products';
        $relativePath = "{$folder}/{$fileName}";

        Storage::disk('public')->makeDirectory($folder);
        Storage::disk('public')->put($relativePath, $pdf->output());

        $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

        /* -------------------------------------------------
     | 6ï¸âƒ£ Response
     -------------------------------------------------*/
        return response()->json([
            'status'    => true,
            'message'   => 'Product PDF generated successfully.',
            'file_url'  => $fileUrl,
            'file_name' => $fileName,
        ]);
    }

    public function getProductById($id)
    {
        $product = Product::with(['category', 'brand', 'variants'])->find($id);
        if ($product) {
            return response()->json(['status' => true, 'product' => $product], 200);
        } else {
            return response()->json(['status' => false, 'error' => 'Product not found'], 404);
        }
    }

    public function generateUniqueBarcodes(Request $request)
    {
        $count = (int) $request->input('count', 3);
        $count = max(1, min($count, 30));

        $exclude = $request->input('exclude', []);
        if (!is_array($exclude)) {
            $exclude = $exclude === null || $exclude === '' ? [] : explode(',', (string) $exclude);
        }
        $exclude = array_values(array_filter(array_map(function ($code) {
            return trim((string) $code);
        }, $exclude), function ($code) {
            return $code !== '';
        }));

        $used = $this->collectExistingBarcodeValues();
        foreach ($exclude as $code) {
            $used[$code] = true;
        }

        $generated = [];
        $maxAttempts = max($count * 80, 200);
        $attempts = 0;

        while (count($generated) < $count && $attempts < $maxAttempts) {
            $attempts++;
            $code = (string) mt_rand(10000, 99999);
            if (isset($used[$code])) {
                continue;
            }
            $used[$code] = true;
            $generated[] = $code;
        }

        return response()->json([
            'success' => count($generated) > 0,
            'barcodes' => $generated,
            'message' => count($generated) === $count
                ? 'Generated ' . count($generated) . ' unique barcodes.'
                : 'Generated ' . count($generated) . ' unique barcodes. Some numbers were already in use.',
        ]);
    }

    private function collectExistingBarcodeValues(): array
    {
        $used = [];

        $addCodes = function ($barcodeList) use (&$used) {
            foreach ($barcodeList as $barcode) {
                foreach (preg_split('/\s*,\s*/', (string) $barcode) as $code) {
                    $code = trim($code);
                    if ($code !== '') {
                        $used[$code] = true;
                    }
                }
            }
        };

        $productQuery = Product::query()->whereNotNull('barcode')->where('barcode', '!=', '');
        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'isDeleted')) {
            $productQuery->where('isDeleted', 0);
        }
        $addCodes($productQuery->pluck('barcode'));

        if (\Illuminate\Support\Facades\Schema::hasTable('product_variants')) {
            $addCodes(
                ProductVariant::query()
                    ->whereNotNull('barcode')
                    ->where('barcode', '!=', '')
                    ->pluck('barcode')
            );
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('row_material')) {
            $addCodes(
                RowMaterial::query()
                    ->whereNotNull('barcode')
                    ->where('barcode', '!=', '')
                    ->pluck('barcode')
            );
        }

        return $used;
    }

    public function generateImage(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|min:3',
        ]);

        try {
            $productName = trim($request->product_name);
            $translatedName = $productName;

            // 1. Local dictionary mapping for common romanized Hindi/Gujarati words
            $dictionary = [
                'diya' => 'oil lamp',
                'deepak' => 'oil lamp',
                'divo' => 'oil lamp',
                'pital' => 'brass',
                'tamba' => 'copper',
                'chandi' => 'silver',
                'kansa' => 'bronze',
                'thali' => 'plate',
                'matka' => 'clay pot',
                'lota' => 'water pot',
                'kalash' => 'ceremonial pot',
                'chimta' => 'tongs',
                'tawa' => 'griddle pan',
                'kadhai' => 'wok',
                'agarbatti' => 'incense sticks',
                'dhoop' => 'incense cone',
                'kapoor' => 'camphor',
                'kapur' => 'camphor',
                'kumkum' => 'vermilion powder',
                'kanku' => 'vermilion powder',
                'haldi' => 'turmeric powder',
                'harad' => 'turmeric powder',
                'chandan' => 'sandalwood paste',
                'sukhad' => 'sandalwood paste',
                'akshat' => 'rice grains',
                'chokha' => 'rice grains',
                'moli' => 'sacred red thread',
                'kalawa' => 'sacred red thread',
                'nadachadi' => 'sacred red thread',
                'janeu' => 'sacred white thread',
                'supari' => 'betel nut',
                'sopari' => 'betel nut',
                'laung' => 'clove',
                'laving' => 'clove',
                'elaichi' => 'cardamom',
                'elachi' => 'cardamom',
                'nariyal' => 'coconut',
                'shriphal' => 'coconut',
                'panchaamrit' => 'holy mixture of milk curd honey sugar ghee',
                'panchamrut' => 'holy mixture of milk curd honey sugar ghee',
                'gangajal' => 'holy water',
                'ghanti' => 'brass hand bell',
                'aarti' => 'prayer oil lamp',
                'arti' => 'prayer oil lamp',
                'shankh' => 'conch shell',
                'mukut' => 'deity crown',
                'vastra' => 'deity clothes',
                'mala' => 'prayer beads',
                'rudraksha' => 'rudraksha beads',
                'tulsi' => 'holy basil',
                'murti' => 'deity idol',
                'pratima' => 'deity idol',
                'singhasan' => 'deity throne',
                'chhatra' => 'deity umbrella',
                'panchpatra' => 'brass holy water vessel',
                'achmani' => 'brass holy spoon',
                'toran' => 'door hanging decoration',
                'rangoli' => 'floor decoration colors',
                'bindi' => 'forehead dot',
                'sindoor' => 'vermilion',
                'havan' => 'fire ritual',
                'kund' => 'copper pot',
                'samagri' => 'ritual mixture',
                'ghee' => 'clarified butter',
                'prasad' => 'religious offering food',
                'pata' => 'wooden prayer stool',
                'bajot' => 'wooden prayer stool',
                'katora' => 'small brass bowl',
                'vatki' => 'small brass bowl',
                'chunri' => 'sacred red cloth',
                'chunari' => 'sacred red cloth',
                'mata' => 'goddess',
                'bhagwan' => 'god',
                'phool' => 'flowers',
                'aggarbatti' => 'incense sticks',
                'machish' => 'matchbox',
                'machis' => 'matchbox',
                'bati' => 'cotton wicks',
                'baati' => 'cotton wicks',
                'diwat' => 'cotton wicks',
                'loban' => 'frankincense resin',
                'gugal' => 'bdellium incense',
                'lobaan' => 'frankincense resin',
                'guggal' => 'bdellium incense',
                'kankavati' => 'traditional vermilion container',
                'dhoop daan' => 'brass incense burner',
                'dhupia' => 'brass incense burner',
                'aarti thali' => 'decorated brass prayer plate',
                'pooja thali' => 'traditional brass prayer plate',
                'chowki' => 'wooden prayer stool',
                'chandan powder' => 'sandalwood powder',
                'haar' => 'marigold flower garland',
                'yantra' => 'sacred geometric copper plate',
                'kapur aarti' => 'brass camphor burner lamp',
                'gomutra' => 'holy water bottle',
                'havan kund' => 'copper fire pit for rituals',
                'samidha' => 'sacred wood sticks for fire',
                'asan' => 'velvet prayer mat'
            ];

            $words = explode(' ', strtolower($productName));
            $dictionaryMatched = false;
            foreach ($words as &$word) {
                if (isset($dictionary[$word])) {
                    $word = $dictionary[$word];
                    $dictionaryMatched = true;
                }
            }
            unset($word);

            if ($dictionaryMatched) {
                // Capitalize first letters for better prompt appearance
                $translatedName = ucwords(implode(' ', $words));
            } else {
                // 2. Generic robust translation logic for any language/script
                try {
                    $hasNativeScript = preg_match('/[\x{0900}-\x{097F}\x{0A80}-\x{0AFF}]/u', $productName);
                    
                    if ($hasNativeScript) {
                        // Native Devanagari/Gujarati script
                        $translateUrl = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=en&dt=t&q=' . rawurlencode($productName);
                        $translateResponse = Http::timeout(5)->get($translateUrl);
                        if ($translateResponse->successful()) {
                            $json = $translateResponse->json();
                            if (isset($json[0][0][0])) {
                                $translatedName = $json[0][0][0];
                            }
                        }
                    } else {
                        // Romanized/Latin script - Try Hindi first
                        $hiTranslation = $productName;
                        $translateUrlHi = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=hi&tl=en&dt=t&q=' . rawurlencode($productName);
                        $responseHi = Http::timeout(5)->get($translateUrlHi);
                        if ($responseHi->successful()) {
                            $json = $responseHi->json();
                            if (isset($json[0][0][0])) $hiTranslation = $json[0][0][0];
                        }

                        if (strtolower($hiTranslation) !== strtolower($productName)) {
                            $translatedName = $hiTranslation;
                        } else {
                            // Try Gujarati next
                            $guTranslation = $productName;
                            $translateUrlGu = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=gu&tl=en&dt=t&q=' . rawurlencode($productName);
                            $responseGu = Http::timeout(5)->get($translateUrlGu);
                            if ($responseGu->successful()) {
                                $json = $responseGu->json();
                                if (isset($json[0][0][0])) $guTranslation = $json[0][0][0];
                            }

                            if (strtolower($guTranslation) !== strtolower($productName)) {
                                $translatedName = $guTranslation;
                            } else {
                                // Both failed - Fallback to Google Input Tools Transliteration to Hindi
                                $translitUrl = 'https://inputtools.google.com/request?text=' . rawurlencode($productName) . '&itc=hi-t-i0-und&num=1';
                                $translitResponse = Http::timeout(5)->get($translitUrl);
                                if ($translitResponse->successful()) {
                                    $json = $translitResponse->json();
                                    if (isset($json[1][0][1][0])) {
                                        $transliteratedText = $json[1][0][1][0];
                                        
                                        // Translate the transliterated native script
                                        $translateUrlFinal = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=hi&tl=en&dt=t&q=' . rawurlencode($transliteratedText);
                                        $responseFinal = Http::timeout(5)->get($translateUrlFinal);
                                        if ($responseFinal->successful()) {
                                            $jsonFinal = $responseFinal->json();
                                            if (isset($jsonFinal[0][0][0])) {
                                                $translatedName = $jsonFinal[0][0][0];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Ignore API errors, gracefully fallback to the original/dictionary name
                }
            }

            $prefix = $dictionaryMatched ? "traditional Indian Hindu prayer item: " : "";
            $prompt = "Professional e-commerce product photography of a single product: {$prefix}{$translatedName}, plain white background, studio lighting, highly detailed 8k, photorealistic, centered, no text, no watermark, no people, product only";

            $apiKey = env('GEMINI_API_KEY');
            $geminiSuccess = false;
            
            if ($apiKey) {
                // Try Google Gemini (Imagen 3) API
                // Using imagen-3.0-generate-001 as 002 might not be available in all regions yet
                $url = "https://generativelanguage.googleapis.com/v1beta/models/imagen-3.0-generate-001:predict";
                try {
                    $response = Http::withHeaders([
                        'x-goog-api-key' => $apiKey,
                        'Content-Type' => 'application/json'
                    ])->timeout(60)->post($url, [
                        'instances' => [
                            ['prompt' => $prompt]
                        ],
                        'parameters' => [
                            'sampleCount' => 1
                        ]
                    ]);

                    if ($response->successful()) {
                        $json = $response->json();
                        if (isset($json['predictions'][0]['bytesBase64'])) {
                            $imageBytes = base64_decode($json['predictions'][0]['bytesBase64']);
                            $filename = 'ai_gen_' . time() . '_' . uniqid() . '.jpg';
                            $path = 'img/product/' . $filename;
                            
                            Storage::disk('public')->put($path, $imageBytes);

                            return response()->json([
                                'success' => true,
                                'image_url' => Storage::disk('public')->url($path),
                                'image_path' => $path
                            ]);
                        }
                    } else {
                        \Log::error("Gemini API Error: " . $response->body());
                    }
                } catch (\Exception $e) {
                    \Log::error("Gemini API Exception: " . $e->getMessage());
                }
            }

            // Fallback to Free Pollinations API (runs if no key OR if Gemini failed)
            $encodedPrompt = rawurlencode($prompt);
                $seed = random_int(1, 999999);

                $url = "https://image.pollinations.ai/prompt/{$encodedPrompt}"
                     . "?width=1280&height=1280&nologo=true&model=flux&seed={$seed}";

                \Log::info("Pollinations Prompt: " . $prompt);
                \Log::info("Pollinations URL: " . $url);

                $response = Http::timeout(60)->get($url);

                if ($response->successful()) {
                    $imageBytes = $response->body();
                    $filename = 'ai_gen_' . time() . '_' . uniqid() . '.jpg';
                    $path = 'img/product/' . $filename;
                    
                    Storage::disk('public')->put($path, $imageBytes);

                    return response()->json([
                        'success' => true,
                        'image_url' => Storage::disk('public')->url($path),
                        'image_path' => $path
                    ]);
                }

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate image from AI service.'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while generating the image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove background from an AI-generated or uploaded product image.
     * Accepts either an uploaded file (image_file) or a stored path (image_path).
     *
     * Free by default (local GD). Optional paid remove.bg / self-hosted URL are tried first;
     * on failure we always fall back to free GD so Remove BG keeps working.
     */
    public function removeBackground(Request $request)
    {
        $request->validate([
            'image_file' => 'nullable|file|image|max:51200',
            'image_path' => 'nullable|string',
        ]);

        try {
            if ($request->hasFile('image_file')) {
                $uploadedFile = $request->file('image_file');
                $tmpName      = 'tmp_' . time() . '_' . uniqid() . '.' . $uploadedFile->getClientOriginalExtension();
                $tmpPath      = 'img/product/' . $tmpName;
                Storage::disk('public')->put($tmpPath, file_get_contents($uploadedFile->getRealPath()));
                $fullPath = Storage::disk('public')->path($tmpPath);
            } elseif ($request->filled('image_path')) {
                $relativePath = ltrim($request->image_path, '/');
                $fullPath     = Storage::disk('public')->path($relativePath);
            } else {
                return response()->json(['success' => false, 'message' => 'No image provided.'], 422);
            }

            if (!file_exists($fullPath)) {
                return response()->json(['success' => false, 'message' => 'Image file not found.'], 404);
            }

            $pngBytes = null;
            $method   = 'local';

            // Optional free/self-hosted remover (e.g. rembg / withoutBG Docker on your server)
            $localApiUrl = trim((string) config('services.bg_removal.url', env('BG_REMOVAL_URL', '')));
            if ($localApiUrl !== '' && $pngBytes === null) {
                $pngBytes = $this->removeBackgroundViaHttpApi($fullPath, $localApiUrl, null);
                if ($pngBytes !== null) {
                    $method = 'self_hosted';
                }
            }

            // Optional paid remove.bg — if it fails (quota/key), fall through to free local
            $removeBgKey = trim((string) config('services.removebg.key', env('REMOVEBG_API_KEY', '')));
            if ($removeBgKey !== '' && $pngBytes === null) {
                $pngBytes = $this->removeBackgroundViaRemoveBg($fullPath, $removeBgKey);
                if ($pngBytes !== null) {
                    $method = 'remove_bg';
                }
            }

            // Free built-in GD remover (studio / near-uniform backgrounds)
            if ($pngBytes === null) {
                $pngBytes = $this->removeBackgroundWithGd($fullPath);
                $method   = 'local';
            }

            if ($pngBytes === null || $pngBytes === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Background removal failed. Free local remover works best on plain/studio backgrounds.',
                ], 500);
            }

                    $filename   = 'nobg_' . time() . '_' . uniqid() . '.png';
                    $outputPath = 'img/product/' . $filename;
            Storage::disk('public')->put($outputPath, $pngBytes);

                    return response()->json([
                        'success'    => true,
                        'image_path' => $outputPath,
                        'image_url'  => Storage::disk('public')->url($outputPath),
                'method'     => $method,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Background removal failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Paid remove.bg API. Returns PNG bytes or null on failure.
     */
    protected function removeBackgroundViaRemoveBg(string $fullPath, string $apiKey): ?string
    {
        try {
            $response = Http::timeout(60)
                ->withHeaders(['X-Api-Key' => $apiKey])
                ->attach('image_file', file_get_contents($fullPath), basename($fullPath))
                ->post('https://api.remove.bg/v1.0/removebg', [
                    'size' => 'full',
                ]);

            if ($response->successful() && $response->body() !== '') {
                return $response->body();
            }

            \Log::warning('remove.bg API failed; falling back to free local remover', [
                'status' => $response->status(),
                'body'   => substr($response->body(), 0, 500),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('remove.bg API error; falling back to free local remover', [
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Optional free self-hosted API (rembg / withoutBG style).
     * POST multipart field "image_file" (also sends "file" / "image" for compatibility).
     */
    protected function removeBackgroundViaHttpApi(string $fullPath, string $url, ?string $apiKey = null): ?string
    {
        try {
            $bytes = file_get_contents($fullPath);
            $name  = basename($fullPath);
            $req   = Http::timeout(120);
            if ($apiKey) {
                $req = $req->withHeaders(['X-Api-Key' => $apiKey]);
            }

            $response = $req
                ->attach('image_file', $bytes, $name)
                ->attach('file', $bytes, $name)
                ->attach('image', $bytes, $name)
                ->post($url);

            if ($response->successful() && $response->body() !== '') {
                $ctype = strtolower((string) $response->header('Content-Type'));
                if (str_contains($ctype, 'json')) {
                    return null;
                }

                return $response->body();
            }

            \Log::warning('Self-hosted BG removal API failed', [
                'url'    => $url,
                'status' => $response->status(),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Self-hosted BG removal API error', [
                'url'   => $url,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Free GD-based background removal (corner flood-fill for studio / plain backgrounds).
     */
    protected function removeBackgroundWithGd(string $fullPath): ?string
    {
            $info = @getimagesize($fullPath);
            if (!$info) {
            return null;
            }

            $mime = $info['mime'];
            $src  = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($fullPath),
            'image/png'               => @imagecreatefrompng($fullPath),
            'image/webp'              => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($fullPath) : null,
                default                   => null,
            };

            if (!$src) {
            return null;
            }

            $w = imagesx($src);
            $h = imagesy($src);

            $out = imagecreatetruecolor($w, $h);
            imagealphablending($out, false);
            imagesavealpha($out, true);
            $transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
            imagefill($out, 0, 0, $transparent);
            imagecopy($out, $src, 0, 0, 0, 0, $w, $h);

        // Slightly higher tolerance helps common studio / off-white product shots
        $tolerance = 28;
            $corners   = [[0, 0], [$w - 1, 0], [0, $h - 1], [$w - 1, $h - 1]];
            $bgColors  = [];

            foreach ($corners as [$cx, $cy]) {
                $cornerRgb = imagecolorat($out, $cx, $cy);
                $ca = ($cornerRgb >> 24) & 0x7F;
            if ($ca > 0) {
                continue;
            }

            $bgColors[] = [
                'r' => ($cornerRgb >> 16) & 0xFF,
                'g' => ($cornerRgb >> 8) & 0xFF,
                'b' => $cornerRgb & 0xFF,
            ];
            }

            if (!empty($bgColors)) {
                $mask = imagecreatetruecolor($w, $h);
            $colorObject = imagecolorallocate($mask, 0, 0, 0);
            $colorBgCandidate = imagecolorallocate($mask, 255, 255, 255);
            $colorConfirmedBg = imagecolorallocate($mask, 255, 0, 0);
                imagefill($mask, 0, 0, $colorObject);

                for ($x = 0; $x < $w; $x++) {
                    for ($y = 0; $y < $h; $y++) {
                        $rgb = imagecolorat($out, $x, $y);
                        $a   = ($rgb >> 24) & 0x7F;
                    if ($a > 0) {
                        continue;
                    }

                        $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                        $b = $rgb & 0xFF;

                        foreach ($bgColors as $bgColor) {
                        if (
                            abs($r - $bgColor['r']) <= $tolerance
                            && abs($g - $bgColor['g']) <= $tolerance
                            && abs($b - $bgColor['b']) <= $tolerance
                        ) {
                            imagesetpixel($mask, $x, $y, $colorBgCandidate);
                                break;
                            }
                        }
                    }
                }

                foreach ($corners as [$cx, $cy]) {
                    if (imagecolorat($mask, $cx, $cy) === $colorBgCandidate) {
                        imagefill($mask, $cx, $cy, $colorConfirmedBg);
                    }
                }

                for ($x = 0; $x < $w; $x++) {
                    for ($y = 0; $y < $h; $y++) {
                        if (imagecolorat($mask, $x, $y) === $colorConfirmedBg) {
                            imagesetpixel($out, $x, $y, $transparent);
                        }
                    }
                }

                imagedestroy($mask);
            }

            imagedestroy($src);

        $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'nobg_' . uniqid('', true) . '.png';
            imagepng($out, $tmpFile);
            imagedestroy($out);

        $pngBytes = @file_get_contents($tmpFile);
            @unlink($tmpFile);

        return ($pngBytes !== false && $pngBytes !== '') ? $pngBytes : null;
    }

    public function createProduct(Request $request)
    {
        /* -------------------------------------------------
     | 1ï¸âƒ£ Validation
     -------------------------------------------------*/

        $user = Auth::guard('api')->user();
        $role = strtolower($user->role ?? '');

        if ($role === 'admin') {
            $branchId = $request->branch_id ?? $user->id;
        } else {
            // Ignore any branch_id or sub_admin_id value sent from the frontend
            $request->request->remove('branch_id');
            $request->request->remove('sub_admin_id');
            $branchId = match ($role) {
                'sub-admin' => $user->id,
                'staff'     => $user->branch_id,
                default     => $user->id,
            };
        }

        $isWarrantyCategory = $this->isWarrantyCategoryId($request->category_id);

        $request->merge([
            'quantity' => $request->filled('quantity') ? $request->quantity : 0,
        ]);

        if ($isWarrantyCategory) {
            $request->merge([
                'price' => $request->filled('price') ? $request->price : 0,
            ]);
        }

        if ($request->item_type === 'normal') {
            $request->request->remove('variants');
        }

        $request->merge([
            'SKU' => Product::getNextAvailableSku($branchId),
        ]);

        $rules = [
            'vendor_id'     => 'nullable|numeric',
            'category_id'   => 'required|numeric',
            'brand_id'      => 'nullable|numeric',
            'name'          => 'required|string|max:255',
            'name_hindi'    => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'item_type'     => 'required|in:normal,variant',
            'price'         => $isWarrantyCategory ? 'nullable|numeric|gte:0' : 'required_if:item_type,normal|nullable|numeric|gte:0',
            'SKU' => [
                'nullable',
                'string',

                Rule::unique('products', 'SKU')
                    ->where(function ($q) use ($branchId) {
                        return $q->where('branch_id', $branchId)
                                ->where('isDeleted', 0); // 👉 IMPORTANT
                    }),
            ],
            // 'hsn_code' => ['required', 'string', 'max:255', Rule::unique('products', 'hsn_code')->where(fn($q) => $q->where('branch_id', $branchId)),],
            'hsn_code' => 'nullable',
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'barcode')
                    ->where(function ($q) use ($branchId) {
                        return $q->where('branch_id', $branchId)
                                ->where('isDeleted', 0);
                    }),
            ],
            'mrp' => 'nullable|numeric|gte:0',
            'batch_no' => 'nullable|string|max:255',
            'label_code' => 'nullable|string|max:255',
            'label_reference' => 'nullable|string|max:255',
            'wholesale_price' => 'nullable|numeric|gte:0',
            'specific_qty'    => 'nullable|string|max:255',
            'quantity'      => 'nullable|numeric|gte:0',
            'unit_id' => 'required|numeric',
            'status'        => 'nullable|in:active,inactive',
            'availablility' => 'nullable|in:in_stock,out_stock',
            'gst_option'    => 'nullable|in:with_gst,without_gst',
            'product_gst'   => 'nullable|array',
            'product_gst.*' => 'nullable|numeric|exists:taxes,id',
            'images'        => 'nullable|array',
            'images.*'      => 'image|mimes:jpeg,png,jpg,gif,webp|max:51200',
            'branch_id' => $role === 'admin'
                ? [
                    'required',
                    'numeric',
                    Rule::exists('users', 'id')->where(function ($q) {
                        $q->whereIn('role', ['admin', 'sub-admin'])->where('isDeleted', 0);
                    })
                  ]
                : 'nullable|numeric',
            'sub_admin_id'  => 'nullable|numeric',
            'imei_no'       => 'nullable|string',
            'product_code'  => 'nullable|string|max:255',
            'tax_rate' => 'nullable|numeric|min:0',
            'item_location' => 'nullable|string|max:255',
            'minimum_stock_qty' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,amount',
            'sale_discount' => 'nullable|numeric|min:0',
            'inclusive_of_tax' => 'nullable',
            'secondary_unit_id' => 'nullable|exists:units,id',
            'conversion_rate' => 'nullable|numeric|min:0',
            'supplier_code' => 'nullable|string|max:255',
            'international_code' => 'nullable|string|max:255',
            'serial_no_status' => 'nullable|string|max:255',
            'non_inventory_type' => 'nullable|string|max:255',
            'stock_validation_status' => 'nullable|string|max:255',
            'goods_type' => 'nullable|string|max:255',
            'discount_print_status' => 'nullable|string|max:255',
            'item_created_on' => 'nullable|date',
            'is_rent_available' => 'nullable|in:0,1',
            'purchase_price' => 'nullable|numeric|gte:0',
            'cost_price'    => 'nullable|numeric|gte:0',
            'landing_cost'  => 'nullable|numeric|gte:0',
            'rent_period'   => 'required_if:is_rent_available,1|nullable|string',
            'rent_price'    => 'required_if:is_rent_available,1|nullable|numeric|gte:0',
            'variants'         => 'required_if:item_type,variant|nullable|array',
            'variants.*.size'  => 'required_if:item_type,variant|string',
            'variants.*.color' => 'required_if:item_type,variant|string',
            'variants.*.qty'   => 'required_if:item_type,variant|numeric|min:0',
            'variants.*.price' => 'required_if:item_type,variant|numeric|min:0',
            'variants.*.purchase_price' => 'nullable|numeric|min:0',
            'variants.*.barcode' => 'nullable|string|distinct|unique:product_variants,barcode',
            'selling_rates' => 'nullable|array',
            'selling_rates.*.name' => 'nullable|string|max:50',
            'selling_rates.*.price' => 'nullable|numeric|gte:0',
            'selling_rates.*.mrp' => 'nullable|numeric|gte:0',
            'selling_rates.*.qty' => 'nullable|numeric|gte:0',
            'selling_rates.*.wholesale' => 'nullable|numeric|gte:0',
        ];

        $validator = Validator::make($request->all(), $rules, [
            'price.required_if' => 'The price field is required.',
        ], [
            'category_id'   => 'category',
            'brand_id'      => 'brand',
            'SKU'           => 'SKU',
            'hsn_code'      => 'HSN Code',
            'availablility' => 'availability',
            'gst_option'    => 'GST option',
            'product_gst'   => 'GST rates',
            'unit_id'          => 'unit',
        ]);

        $validator->after(function ($validator) use ($request, $branchId) {
            $this->validateUniqueProductName($validator, $request->name, $branchId);

            // GST validation removed as per request
            // if ($request->gst_option === 'with_gst') {
            //     $productGst = $request->product_gst;
            //     if (empty($productGst) || !is_array($productGst) || count(array_filter($productGst)) === 0) {
            //         $validator->errors()->add('product_gst', 'Please select at least one GST rate when "With GST" is selected.');
            //     }
            // }

            if ($this->isMobileCategoryId($request->category_id)) {
                $qty = (float) ($request->quantity ?? 0);
                $imeiNoJson = $request->imei_no;
                if ($qty > 0) {
                    if (empty($imeiNoJson) || $imeiNoJson === '[]') {
                        $validator->errors()->add('imei_no', 'Please enter IMEI/Serial numbers for the selected mobile quantity.');
                    } else {
                        $imeiArray = is_string($imeiNoJson) ? json_decode($imeiNoJson, true) : $imeiNoJson;
                        if (!is_array($imeiArray)) {
                            $validator->errors()->add('imei_no', 'Invalid IMEI numbers format.');
                        } else {
                            $nonEmptyImeis = array_filter(array_map('trim', $imeiArray));
                            if (count($nonEmptyImeis) != $qty) {
                                $validator->errors()->add('imei_no', 'The number of IMEI numbers must match the quantity (' . $qty . ').');
                            }

                            // Duplicate checking within form
                            $allImeis = [];
                            foreach ($nonEmptyImeis as $imei) {
                                if (in_array($imei, $allImeis)) {
                                    $validator->errors()->add('imei_no', 'IMEI ' . $imei . ' is duplicated in this form.');
                                }
                                $allImeis[] = $imei;

                                // Duplicate checking in database (products)
                                $existsInProducts = DB::table('products')
                                    ->where('isDeleted', 0)
                                    ->where('imei_no', 'LIKE', '%"' . $imei . '"%')
                                    ->exists();
                                if ($existsInProducts) {
                                    $validator->errors()->add('imei_no', 'IMEI ' . $imei . ' already exists in products.');
                                }
                            }
                        }
                    }
                }
            }
        });

        $validated = $validator->validate();

        /* -------------------------------------------------
     | 2ï¸âƒ£ Upload Images
     -------------------------------------------------*/
        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $this->storeProductImageOriginal($image);
            }
        }

        $generatedPaths = $this->parseGeneratedImagePaths($request);
        if (!empty($generatedPaths)) {
            $imagePaths = array_merge($imagePaths, $generatedPaths);
        }

        /* -------------------------------------------------
     | 3ï¸âƒ£ Resolve Branch ID (clean & safe)
     -------------------------------------------------*/
        $user = Auth::guard('api')->user();
        $role = strtolower($user->role ?? '');

        if ($role === 'admin') {
            $branchId = $validated['branch_id'] ?? $user->id;
        } else {
            $branchId = match ($role) {
                'sub-admin' => $user->id,
                'staff'     => $user->branch_id,
                default     => $user->id,
            };
        }

        /* -------------------------------------------------
     | 4ï¸âƒ£ Availability Logic
     -------------------------------------------------*/
        $availability = ($validated['quantity'] ?? 0) == 0
            ? 'out_stock'
            : ($validated['availablility'] ?? 'in_stock');

        /* -------------------------------------------------
     | 5ï¸âƒ£ Create Product
     -------------------------------------------------*/
        $gstData = [];
        if (!empty($validated['product_gst']) && is_array($validated['product_gst'])) {
            foreach ($validated['product_gst'] as $taxId) {
                $tax = TaxRate::find($taxId);
                if ($tax) {
                    $gstData[] = [
                        'tax_id'   => $taxId,
                        'tax_name' => $tax->tax_name,
                        'tax_rate' => $tax->tax_rate,
                    ];
                }
            }
        }

        $product = Product::create([
            'vendor_id'              => $validated['vendor_id'] ?? 1,
            'category_id'            => $validated['category_id'],
            'brand_id'               => $validated['brand_id'],
            'branch_id'              => $branchId,
            'name'                   => $validated['name'],
            'name_hindi'             => trim((string) ($validated['name_hindi'] ?? '')) ?: null,
            'description'            => $validated['description'] ?? null,
            'price'                  => $validated['price'],
            'selling_prices'         => Product::buildSellingPrices($validated['price'] ?? 0, $request->input('selling_rates', []), $validated['mrp'] ?? null, $request->input('wholesale_price'), $validated['quantity'] ?? null),
            'mrp'                    => $validated['mrp'] ?? null,
            'barcode'                => $validated['barcode'] ?? null,
            'quantity'               => $validated['quantity'] ?? 0,
            'imei_no'                => $this->isMobileCategoryId($validated['category_id']) ? ($request->imei_no ?? '[]') : null,
            'unit_id'                => $validated['unit_id'],
            'status'                 => $validated['status'] ?? 'active',
            'batch_no'               => $validated['batch_no'] ?? null,
            'label_code'             => $validated['label_code'] ?? null,
            'label_reference'        => $validated['label_reference'] ?? null,
            'specific_qty'           => $validated['specific_qty'] ?? null,
            'purchase_price'         => $validated['purchase_price'] ?? null,
            'cost_price'             => $validated['cost_price'] ?? null,
            'landing_cost'           => $validated['landing_cost'] ?? null,
            'product_code'           => $validated['product_code'] ?? null,
            'supplier_code'          => $validated['supplier_code'] ?? null,
            'international_code'     => $validated['international_code'] ?? null,
            'serial_no_status'       => $validated['serial_no_status'] ?? null,
            'non_inventory_type'     => $validated['non_inventory_type'] ?? null,
            'stock_validation_status' => $validated['stock_validation_status'] ?? null,
            'item_type'              => $validated['item_type'] ?? null,
            'discount_print_status'  => $validated['discount_print_status'] ?? null,
            'item_created_on'        => $validated['item_created_on'] ?? null,
            'SKU'                    => $validated['SKU'],
            'hsn_code'               => $validated['hsn_code'],
            'tax_rate'               => $validated['tax_rate'] ?? null,
            'item_location'          => $validated['item_location'] ?? null,
            'minimum_stock_qty'      => $validated['minimum_stock_qty'] ?? 0,
            'discount_type'          => $validated['discount_type'] ?? null,
            'sale_discount'          => $validated['sale_discount'] ?? 0,
            'inclusive_of_tax'       => $request->boolean('inclusive_of_tax') ? 1 : 0,
            'secondary_unit_id'      => $validated['secondary_unit_id'] ?? null,
            'conversion_rate'        => $validated['conversion_rate'] ?? null,
            'barcode'                => $validated['barcode'] ?? null,
            'quantity'               => $validated['quantity'] ?? 0,
            'imei_no'                => $this->isMobileCategoryId($validated['category_id']) ? ($request->imei_no ?? '[]') : null,
            'unit_id'                => $validated['unit_id'],
            'status'                 => $validated['status'] ?? 'active',
            'availablility'          => $availability,
            'gst_option'             => $validated['gst_option'] ?? 'without_gst',
            'product_gst'            => !empty($gstData) ? json_encode($gstData) : null,
            'images'                 => json_encode($imagePaths),
            'is_rent_available'      => $validated['is_rent_available'] ?? 0,
            'rent_period'            => ($validated['is_rent_available'] ?? 0) == 1 ? ($validated['rent_period'] ?? null) : null,
            'rent_price'             => ($validated['is_rent_available'] ?? 0) == 1 ? ($validated['rent_price'] ?? null) : null,
        ]);

        if (isset($validated['variants']) && is_array($validated['variants']) && count($validated['variants']) > 0) {
            foreach ($validated['variants'] as $variant) {
                $variantBarcode = !empty($variant['barcode']) ? $variant['barcode'] : time() . rand(1000, 9999);

                $createdVariant = $product->variants()->create([
                    'size'  => $variant['size'],
                    'color' => $variant['color'],
                    'qty'   => $variant['qty'],
                    'price' => $variant['price'],
                    'purchase_price' => $variant['purchase_price'] ?? null,
                    'barcode' => $variantBarcode,
                ]);

                if (! $isWarrantyCategory) {
                    ProductInventory::create([
                        'product_id'         => $product->id,
                        'product_variant_id' => $createdVariant->id,
                        'initial_stock'      => $variant['qty'],
                        'current_stock'      => $variant['qty'],
                        'branch_id'          => $branchId,
                        'create_by'          => $user->id,
                        'type'               => 'Create',
                        'date'               => now(),
                    ]);
                }
            }
        } else {
            /* -------------------------------------------------
             | 6️⃣ Inventory Log (non-variant fallback)
             -------------------------------------------------*/
            if (! $isWarrantyCategory) {
                ProductInventory::create([
                    'product_id'         => $product->id,
                    'product_variant_id' => null,
                    'initial_stock'      => $validated['quantity'] ?? 0,
                    'current_stock'      => $validated['quantity'] ?? 0,
                    'branch_id'          => $branchId,
                    'create_by'          => $user->id,
                    'type'               => 'Create',
                    'date'               => now(),
                ]);
            }
        }

        /* -------------------------------------------------
     | 7ï¸âƒ£ Response
     -------------------------------------------------*/

        $typedBatch = $this->normalizeBatchNo($validated['batch_no'] ?? null);
        if ($typedBatch !== null) {
            $this->syncGlobalBatchNo($branchId, $typedBatch);
        } else {
            $globalBatch = $this->currentGlobalBatchNo($branchId);
            if ($globalBatch !== null && $product->batch_no !== $globalBatch) {
                $product->batch_no = $globalBatch;
                $product->save();
            }
        }

        try {
            $this->replicateProductToOtherBranches($product->fresh(['variants', 'category', 'brand']), $user);
        } catch (\Throwable $e) {
            \Log::warning('Cross-branch product replicate failed: ' . $e->getMessage(), [
                'product_id' => $product->id,
            ]);
        }

        $actor = \App\Services\NotificationService::actorName($user->id ?? null);
        $this->notifyProductEvent(
            $product,
            'product_created',
            'New Product Added',
            $actor . " added product '{$product->name}'."
        );

        return response()->json([
            'status'  => true,
            'message' => 'Product created successfully',
            'product' => $product->fresh(['variants']),
        ], 200);
    }

    public function getNextProductSku(Request $request)
    {
        $user = Auth::guard('api')->user();
        $role = strtolower($user->role ?? '');

        if ($role === 'admin') {
            $branchId = $request->query('sub_branch_id')
                ?? $request->query('branch_id')
                ?? $user->id;
        } else {
            $branchId = match ($role) {
                'sub-admin' => $user->id,
                'staff'     => $user->branch_id,
                default     => $user->id,
            };
        }

        $startFrom = $request->query('start_from');

        return response()->json([
            'status' => true,
            'sku'    => Product::getNextAvailableSku((int) $branchId, $startFrom),
        ]);
    }

    public function getLastAddedProduct(Request $request)
    {
        $user = Auth::guard('api')->user();
        $role = strtolower($user->role ?? '');

        if ($role === 'admin') {
            $branchId = $request->query('sub_branch_id')
                ?? $request->query('branch_id')
                ?? $user->id;
        } else {
            $branchId = match ($role) {
                'sub-admin' => $user->id,
                'staff'     => $user->branch_id,
                default     => $user->id,
            };
        }

        $product = Product::with(['variants'])
            ->where('branch_id', (int) $branchId)
            ->where('isDeleted', 0)
            ->latest('id')
            ->first();

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'No previous product found',
                'product' => null,
            ]);
        }

        return response()->json([
            'status'  => true,
            'product' => $product,
        ]);
    }

    public function resequenceProductSkus(Request $request)
    {
        $user = Auth::guard('api')->user();
        $role = strtolower($user->role ?? '');

        if ($role === 'admin') {
            $branchId = $request->input('sub_branch_id')
                ?? $request->input('branch_id')
                ?? $user->id;
        } else {
            $branchId = match ($role) {
                'sub-admin' => $user->id,
                'staff'     => $user->branch_id,
                default     => $user->id,
            };
        }

        $startFrom = max(1, (int) $request->input('start_from', 1));
        $result    = Product::resequenceBranchSkus((int) $branchId, $startFrom);

        return response()->json([
            'status'  => true,
            'message' => $result['total'] > 0
                ? 'Product SKUs renumbered ' . $result['from'] . ' to ' . $result['to'] . ' (' . $result['updated'] . ' updated). Next SKU: ' . $result['next_sku'] . '.'
                : 'No products found to resequence.',
            'data'    => $result,
        ]);
    }

    /**
     * Start background full catalog copy to all branches (one click).
     */
    public function startBackgroundCatalogSync(Request $request)
    {
        $user = Auth::guard('api')->user();
        $role = strtolower($user->role ?? '');
        if (! in_array($role, ['admin', 'sub-admin'], true)) {
            return response()->json([
                'status' => false,
                'message' => 'Only admin/sub-admin can sync products to branches.',
            ], 403);
        }

        $sourceBranchId = (int) $user->id;
        $reset = $request->boolean('reset', false);

        $sync = app(\App\Services\BranchCatalogSyncService::class);
        $status = $sync->status();
        if (! empty($status['running'])) {
            return response()->json([
                'status' => true,
                'message' => 'Sync is already running. Please wait...',
                'data' => $status,
            ]);
        }

        $sync->writeStatus([
            'running' => true,
            'done' => false,
            'message' => 'Starting sync... copying all products to all branches.',
            'source_branch_id' => $sourceBranchId,
            'processed' => 0,
            'created' => 0,
            'skipped' => 0,
            'failed' => 0,
            'remaining' => 0,
            'started_at' => now()->toDateTimeString(),
            'finished_at' => null,
            'error' => null,
        ]);

        // Run after HTTP response so browser is free to poll status.
        // This copies remaining products to EVERY other branch until finished.
        dispatch(function () use ($sync, $sourceBranchId, $reset) {
            try {
                $sync->run($sourceBranchId, 100, $reset);
            } catch (\Throwable $e) {
                $sync->writeStatus([
                    'running' => false,
                    'done' => false,
                    'error' => $e->getMessage(),
                    'message' => 'Sync failed: '.$e->getMessage(),
                    'finished_at' => now()->toDateTimeString(),
                ]);
            }
        })->afterResponse();

        return response()->json([
            'status' => true,
            'message' => 'Sync started. Keep this page open — all Main Branch products will be copied to every other branch.',
            'data' => $sync->status(),
        ]);
    }

    public function catalogSyncStatus()
    {
        $sync = app(\App\Services\BranchCatalogSyncService::class);
        $data = $sync->status();

        // Enrich with live branch product counts for UI.
        $branches = \App\Models\User::query()
            ->whereIn('role', ['admin', 'sub-admin'])
            ->where('isDeleted', 0)
            ->get(['id', 'name', 'role']);
        $branchStats = [];
        foreach ($branches as $b) {
            $branchStats[] = [
                'id' => (int) $b->id,
                'name' => $b->name,
                'role' => $b->role,
                'count' => Product::query()->where('branch_id', $b->id)->where('isDeleted', 0)->count(),
            ];
        }
        $data['branch_stats'] = $branchStats;

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    /**
     * Backfill catalog products from source branch to other branches (qty 0).
     * Chunked for large catalogs (~30k): pass after_id from previous response.
     */
    public function syncCatalogToOtherBranches(Request $request)
    {
        @set_time_limit(180);
        $user = Auth::guard('api')->user();
        $role = strtolower($user->role ?? '');

        if (! in_array($role, ['admin', 'sub-admin'], true)) {
            return response()->json([
                'status' => false,
                'message' => 'Only admin/sub-admin can sync products to branches.',
            ], 403);
        }

        // Empty string must not become branch_id 0 (Main Branch often sends blank sub_branch_id).
        $requested = $request->input('source_branch_id')
            ?: $request->input('sub_branch_id')
            ?: $request->input('branch_id')
            ?: null;

        if ($role === 'admin') {
            // Default: sync FROM main admin catalog to all other branches.
            $fromMain = $request->boolean('from_main', true);
            if ($fromMain || empty($requested)) {
                $sourceBranchId = (int) $user->id;
            } else {
                $sourceBranchId = (int) $requested;
            }
        } else {
            $sourceBranchId = (int) $user->id;
        }

        if ($sourceBranchId <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid source branch.',
            ], 422);
        }

        $afterId = max(0, (int) $request->input('after_id', 0));
        $limit = max(10, min(150, (int) $request->input('limit', 80)));
        $reset = $request->boolean('reset', false);
        $progressKey = 'product_branch_sync_after_' . (int) $user->id . '_' . $sourceBranchId;

        if ($reset) {
            Cache::forget($progressKey);
            $afterId = 0;
        } elseif ($afterId <= 0) {
            // Resume from server-saved cursor if browser progress was lost.
            $afterId = max(0, (int) Cache::get($progressKey, 0));
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

        if ($targetBranchIds === []) {
            return response()->json([
                'status' => true,
                'message' => 'No other branches found to sync.',
                'done' => true,
                'after_id' => $afterId,
                'created' => 0,
                'skipped' => 0,
                'failed' => 0,
                'processed' => 0,
                'total_remaining' => 0,
                'source_branch_id' => $sourceBranchId,
                'source_count' => $sourceCount,
            ]);
        }

        // Resume at FIRST missing product (MAX synced id was wrong — it skipped gaps).
        if ($sourceCount > 0 && $targetBranchIds !== []) {
            try {
                $firstMissing = null;
                foreach ($targetBranchIds as $tid) {
                    $id = DB::table('products as s')
                        ->leftJoin('products as t', function ($join) use ($tid) {
                            $join->on('s.SKU', '=', 't.SKU')
                                ->where('t.branch_id', $tid)
                                ->where('t.isDeleted', 0);
                        })
                        ->where('s.branch_id', $sourceBranchId)
                        ->where('s.isDeleted', 0)
                        ->whereNotNull('s.SKU')
                        ->where('s.SKU', '!=', '')
                        ->whereNull('t.id')
                        ->orderBy('s.id')
                        ->value('s.id');
                    if ($id) {
                        $id = (int) $id;
                        $firstMissing = $firstMissing === null ? $id : min($firstMissing, $id);
                    }
                }
                if ($firstMissing !== null && $afterId >= $firstMissing) {
                    $afterId = max(0, $firstMissing - 1);
                    Cache::put($progressKey, $afterId, now()->addDays(14));
                }
            } catch (\Throwable $e) {
                \Log::warning('Smart sync resume failed: ' . $e->getMessage());
            }
        }

        if ($sourceCount === 0) {
            return response()->json([
                'status' => false,
                'message' => 'No products found in source branch (id ' . $sourceBranchId . '). Open Main Branch and try again.',
                'done' => true,
                'after_id' => $afterId,
                'created' => 0,
                'skipped' => 0,
                'failed' => 0,
                'processed' => 0,
                'source_branch_id' => $sourceBranchId,
                'source_count' => 0,
            ], 422);
        }

        $baseQuery = Product::query()
            ->where('branch_id', $sourceBranchId)
            ->where('isDeleted', 0);

        $totalRemaining = (clone $baseQuery)->where('id', '>', $afterId)->count();

        $products = (clone $baseQuery)
            ->with(['variants', 'category', 'brand'])
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->limit($limit)
            ->get();

        $created = 0;
        $skipped = 0;
        $failed = 0;
        $lastId = $afterId;
        $lastError = null;

        $prevSkip = Product::$skipBarcodeHistory;
        Product::$skipBarcodeHistory = true;

        try {
            foreach ($products as $product) {
                $lastId = (int) $product->id;
                foreach ($targetBranchIds as $targetBranchId) {
                    try {
                        $result = $this->cloneProductToBranch($product, $targetBranchId, $user);
                        if ($result === 'created') {
                            $created++;
                        } elseif ($result === 'skipped') {
                            $skipped++;
                        } else {
                            $failed++;
                        }
                    } catch (\Throwable $e) {
                        $failed++;
                        $lastError = $e->getMessage();
                        \Log::warning('cloneProductToBranch failed', [
                            'product_id' => $product->id,
                            'target_branch_id' => $targetBranchId,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        } finally {
            Product::$skipBarcodeHistory = $prevSkip;
        }

        $done = $products->count() < $limit;

        if ($done) {
            Cache::forget($progressKey);
        } else {
            Cache::put($progressKey, $lastId, now()->addDays(14));
        }

        $targetCounts = [];
        foreach ($targetBranchIds as $tid) {
            $targetCounts[(string) $tid] = Product::query()
                ->where('branch_id', $tid)
                ->where('isDeleted', 0)
                ->count();
        }

        return response()->json([
            'status' => true,
            'message' => $done
                ? 'Product catalog sync completed.'
                : 'Syncing products to other branches...',
            'done' => $done,
            'after_id' => $lastId,
            'created' => $created,
            'skipped' => $skipped,
            'failed' => $failed,
            'processed' => $products->count(),
            'total_remaining' => max(0, $totalRemaining - $products->count()),
            'source_branch_id' => $sourceBranchId,
            'source_count' => $sourceCount,
            'target_branches' => count($targetBranchIds),
            'target_counts' => $targetCounts,
            'last_error' => $lastError,
        ]);
    }

    /**
     * Lightweight product update for the Quick Update modal (name, prices, SKU, barcode, qty).
     */
    public function quickUpdateProduct(Request $request)
    {
        $user = Auth::guard('api')->user();

        foreach ([
            'category_id', 'brand_id', 'mrp', 'wholesale_price', 'purchase_price', 'quantity',
            'minimum_stock_qty', 'cost_price', 'landing_cost', 'rate2_price', 'rate2_mrp',
        ] as $nullableField) {
            if ($request->exists($nullableField) && $request->input($nullableField) === '') {
                $request->merge([$nullableField => null]);
            }
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'name_hindi' => 'nullable|string|max:255',
            'SKU' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'price' => 'required|numeric|gte:0',
            'mrp' => 'nullable|numeric|gte:0',
            'wholesale_price' => 'nullable|numeric|gte:0',
            'purchase_price' => 'nullable|numeric|gte:0',
            'quantity' => 'nullable|numeric|gte:0',
            'description' => 'nullable|string',
            'hsn_code' => 'nullable|string|max:255',
            'product_code' => 'nullable|string|max:255',
            'batch_no' => 'nullable|string|max:255',
            'specific_qty' => 'nullable|string|max:255',
            'label_code' => 'nullable|string|max:255',
            'label_reference' => 'nullable|string|max:255',
            'item_location' => 'nullable|string|max:255',
            'minimum_stock_qty' => 'nullable|numeric|gte:0',
            'cost_price' => 'nullable|numeric|gte:0',
            'landing_cost' => 'nullable|numeric|gte:0',
            'category_id' => 'nullable|numeric|exists:categories,id',
            'brand_id' => 'nullable|numeric|exists:brands,id',
            'rate2_price' => 'nullable|numeric|gte:0',
            'rate2_mrp' => 'nullable|numeric|gte:0',
            'rate2_name' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $product = Product::where('id', $validated['product_id'])
            ->where('isDeleted', 0)
            ->firstOrFail();
        $previousBatchNo = $this->normalizeBatchNo($product->batch_no);

        $branchId = match (strtolower((string) $user->role)) {
            'sub-admin' => $user->id,
            'admin' => $request->branch_id ?? $product->branch_id,
            'staff' => $user->branch_id ?: $product->branch_id,
            default => $product->branch_id,
        };

        // Branch safety: staff/sub-admin can only edit their branch products
        if (in_array(strtolower((string) $user->role), ['sub-admin', 'staff'], true)
            && (int) $product->branch_id !== (int) $branchId) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot update products from another branch.',
            ], 403);
        }

        $nameValidator = Validator::make(['name' => $validated['name']], ['name' => 'required']);
        $this->validateUniqueProductName($nameValidator, $validated['name'], $branchId, (int) $product->id);
        if ($nameValidator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $nameValidator->errors()->first() ?: 'Product name already exists.',
                'errors' => $nameValidator->errors(),
            ], 422);
        }

        $sku = trim((string) ($validated['SKU'] ?? ''));
        if ($sku !== '' && Product::skuExistsForBranch((int) $branchId, $sku, (int) $product->id)) {
            return response()->json([
                'status' => false,
                'message' => 'SKU already exists for this branch.',
            ], 422);
        }

        $barcode = trim((string) ($validated['barcode'] ?? ''));
        if ($barcode !== '') {
            $barcodeTaken = Product::query()
                ->where('branch_id', $branchId)
                ->where('isDeleted', 0)
                ->where('barcode', $barcode)
                ->where('id', '!=', $product->id)
                ->exists();
            if ($barcodeTaken) {
                return response()->json([
                    'status' => false,
                    'message' => 'Barcode already exists for this branch.',
                ], 422);
            }
        }

        $oldPrice = $product->price;
        $oldMrp = $product->mrp;
        $oldPurchasePrice = $product->purchase_price;

        $newPrice = (float) $validated['price'];
        $newMrp = array_key_exists('mrp', $validated) && $validated['mrp'] !== null && $validated['mrp'] !== ''
            ? (float) $validated['mrp']
            : $product->mrp;
        $newPurchase = array_key_exists('purchase_price', $validated) && $validated['purchase_price'] !== null && $validated['purchase_price'] !== ''
            ? (float) $validated['purchase_price']
            : $product->purchase_price;

        $wholesaleInput = null;
        if ($request->exists('wholesale_price')) {
            $rawWholesale = $request->input('wholesale_price');
            $wholesaleInput = ($rawWholesale === '' || $rawWholesale === null)
                ? null
                : (float) $rawWholesale;
        } else {
            $existingWholesale = Product::resolveWholesaleRates($product->selling_prices, $product->price);
            $wholesaleInput = $existingWholesale[0]['price'] ?? null;
        }

        // Keep Rate 2+ (non-wholesale) extras when rebuilding selling_prices
        $extraRates = [];
        $rawRates = $product->selling_prices;
        if (is_string($rawRates)) {
            $rawRates = json_decode($rawRates, true);
        }
        if (is_array($rawRates)) {
            foreach ($rawRates as $rate) {
                if (!is_array($rate)) {
                    continue;
                }
                $name = trim((string) ($rate['name'] ?? ''));
                if ($name !== '' && stripos($name, 'wholesale') !== false) {
                    continue;
                }
                if ($name === '' || preg_match('/^rate\s*1$/i', $name)) {
                    continue;
                }
                $extraRates[] = [
                    'name' => $name,
                    'price' => $rate['price'] ?? null,
                    'mrp' => $rate['mrp'] ?? null,
                    'qty' => $rate['qty'] ?? null,
                ];
            }
        }

        // Optional Rate 2 from More Details popup (replace existing Rate 2 / first extra)
        if ($request->exists('rate2_price')) {
            $rate2Price = $request->input('rate2_price');
            $extraRates = array_values(array_filter($extraRates, function ($rate) {
                $name = trim((string) ($rate['name'] ?? ''));
                return !preg_match('/^rate\s*2$/i', $name);
            }));
            if ($rate2Price !== '' && $rate2Price !== null && is_numeric($rate2Price)) {
                $keptRate2Qty = null;
                if (is_array($rawRates)) {
                    foreach ($rawRates as $existingRate) {
                        if (!is_array($existingRate)) {
                            continue;
                        }
                        if (preg_match('/^rate\s*2$/i', trim((string) ($existingRate['name'] ?? '')))) {
                            $keptRate2Qty = $existingRate['qty'] ?? null;
                            break;
                        }
                    }
                }
                array_unshift($extraRates, [
                    'name' => trim((string) ($request->input('rate2_name') ?: 'Rate 2')) ?: 'Rate 2',
                    'price' => (float) $rate2Price,
                    'mrp' => ($request->filled('rate2_mrp') ? (float) $request->input('rate2_mrp') : null),
                    'qty' => $keptRate2Qty,
                ]);
            }
        }

        $qty = array_key_exists('quantity', $validated) && $validated['quantity'] !== null && $validated['quantity'] !== ''
            ? (float) $validated['quantity']
            : (float) $product->quantity;

        $product->name = trim($validated['name']);
        if (array_key_exists('SKU', $validated)) {
            $product->SKU = $sku !== '' ? $sku : null;
        }
        if (array_key_exists('barcode', $validated)) {
            $product->barcode = $barcode !== '' ? $barcode : null;
        }
        $product->price = $newPrice;
        $product->mrp = $newMrp;
        $product->purchase_price = $newPurchase;
        $product->quantity = $qty;
        $product->availablility = $qty <= 0 ? 'out_stock' : ($product->availablility ?: 'in_stock');
        $product->selling_prices = Product::buildSellingPrices(
            $newPrice,
            $extraRates,
            $newMrp,
            $wholesaleInput,
            $qty
        );

        $detailFields = [
            'name_hindi',
            'description',
            'hsn_code',
            'product_code',
            'batch_no',
            'specific_qty',
            'label_code',
            'label_reference',
            'item_location',
            'minimum_stock_qty',
            'cost_price',
            'landing_cost',
            'category_id',
            'brand_id',
        ];
        foreach ($detailFields as $field) {
            if (!$request->exists($field)) {
                continue;
            }
            $value = $request->input($field);
            if (in_array($field, ['minimum_stock_qty', 'cost_price', 'landing_cost', 'category_id', 'brand_id'], true)) {
                $product->{$field} = ($value === '' || $value === null) ? null : $value;
            } else {
                $product->{$field} = is_string($value) ? trim($value) : $value;
            }
        }

        $product->save();

        if ($request->exists('batch_no') && $this->normalizeBatchNo($product->batch_no) !== $previousBatchNo) {
            $this->syncGlobalBatchNo($product->branch_id, $product->batch_no);
        }

        $actor = \App\Services\NotificationService::actorName($user->id ?? null);
        $this->notifyProductEvent(
            $product,
            'product_updated',
            'Product Updated',
            $actor . " updated '{$product->name}'. " . $this->describeProductChanges($product)
        );

        try {
            $this->dispatchCrossBranchPriceUpdates(
                $product,
                $oldPrice,
                $newPrice,
                $oldMrp,
                $newMrp,
                $oldPurchasePrice,
                $newPurchase
            );
        } catch (\Throwable $e) {
            // Non-fatal for quick update
        }

        $wholesaleRates = Product::resolveWholesaleRates($product->selling_prices, $product->price);

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully.',
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'SKU' => $product->SKU,
                'barcode' => $product->barcode,
                'price' => $product->price,
                'mrp' => $product->mrp,
                'purchase_price' => $product->purchase_price,
                'quantity' => $product->quantity,
                'wholesale_price' => $wholesaleRates[0]['price'] ?? null,
                'selling_prices' => $product->selling_prices,
            ],
        ]);
    }

    public function updateProduct(Request $request)
    {
        /* -------------------------------------------------
     | 1ï¸âƒ£ Validation
     -------------------------------------------------*/

        $user = Auth::guard('api')->user();

        $product = Product::findOrFail($request->product_id);
        $previousBatchNo = $this->normalizeBatchNo($product->batch_no);

        $branchId = match (strtolower($user->role)) {
            'sub-admin' => $user->id,
            'admin'     => $request->branch_id ?? $product->branch_id,
            'staff'     => $product->branch_id,
            default     => $product->branch_id,
        };

        $isWarrantyCategory = $this->isWarrantyCategoryId($request->category_id);

        $request->merge([
            'quantity' => $request->filled('quantity') ? $request->quantity : 0,
        ]);

        if ($isWarrantyCategory) {
            $request->merge([
                'price' => $request->filled('price') ? $request->price : 0,
            ]);
        }

        if ($request->item_type === 'normal') {
            $request->request->remove('variants');
        }

        $rules = [
            'product_id'    => 'required|exists:products,id',
            'vendor_id'     => 'nullable|numeric',
            'category_id'   => 'required|numeric',
            'brand_id'      => 'nullable|numeric',
            'name'          => 'required|string|max:255',
            'name_hindi'    => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'item_type'     => 'required|in:normal,variant',
            'price'         => $isWarrantyCategory ? 'nullable|numeric|gte:0' : 'required_if:item_type,normal|nullable|numeric|gte:0',
            'SKU' => [
                'nullable',
                'max:255',
                Rule::unique('products', 'SKU')
                    ->where(function ($q) use ($branchId) {
                        return $q->where('branch_id', $branchId)
                                ->where('isDeleted', 0); // 👉 ADD THIS
                    })
                    ->ignore($request->product_id),
            ],
            // 'hsn_code' => ['required', 'string', 'max:255', Rule::unique('products', 'hsn_code')->where(fn($q) => $q->where('branch_id', $branchId))->ignore($request->product_id),],
            'hsn_code' => 'nullable',
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'barcode')
                    ->where(function ($q) use ($branchId) {
                        return $q->where('branch_id', $branchId)
                                ->where('isDeleted', 0); // 👉 ADD THIS
                    })
                    ->ignore($request->product_id),
            ],
            'mrp' => 'nullable|numeric|gte:0',
            'batch_no' => 'nullable|string|max:255',
            'label_code' => 'nullable|string|max:255',
            'label_reference' => 'nullable|string|max:255',
            'wholesale_price' => 'nullable|numeric|gte:0',
            'specific_qty'    => 'nullable|string|max:255',
            'quantity'      => 'nullable|numeric|gte:0',
            'unit_id' => 'required|numeric',
            'status'        => 'nullable|in:active,inactive',
            'availablility' => 'nullable|in:in_stock,out_stock',
            'gst_option'    => 'nullable|in:with_gst,without_gst',
            'product_gst'   => 'nullable|array',
            'product_gst.*' => 'nullable|numeric|exists:taxes,id',
            'images'        => 'nullable|array',
            'images.*'      => 'image|mimes:jpeg,png,jpg,gif,webp|max:51200',
            'capacity'      => 'nullable|string',
            'voltage'       => 'nullable|string',
            'warranty'      => 'nullable|string',
            'expiry_date'   => 'nullable|date',
            'imei_no'       => 'nullable|string',
            'is_rent_available' => 'nullable|in:0,1',
            'product_code'  => 'nullable|string|max:255',
            'tax_rate' => 'nullable|numeric|min:0',
            'item_location' => 'nullable|string|max:255',
            'minimum_stock_qty' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,amount',
            'sale_discount' => 'nullable|numeric|min:0',
            'inclusive_of_tax' => 'nullable',
            'secondary_unit_id' => 'nullable|exists:units,id',
            'conversion_rate' => 'nullable|numeric|min:0',
            'supplier_code' => 'nullable|string|max:255',
            'international_code' => 'nullable|string|max:255',
            'serial_no_status' => 'nullable|string|max:255',
            'non_inventory_type' => 'nullable|string|max:255',
            'stock_validation_status' => 'nullable|string|max:255',
            'goods_type' => 'nullable|string|max:255',
            'discount_print_status' => 'nullable|string|max:255',
            'item_created_on' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|gte:0',
            'cost_price'    => 'nullable|numeric|gte:0',
            'landing_cost'  => 'nullable|numeric|gte:0',
            'rent_period'   => 'required_if:is_rent_available,1|nullable|string',
            'rent_price'    => 'required_if:is_rent_available,1|nullable|numeric|gte:0',
            'variants'         => 'required_if:item_type,variant|nullable|array',
            'variants.*.size'  => 'required_if:item_type,variant|string',
            'variants.*.color' => 'required_if:item_type,variant|string',
            'variants.*.qty'   => 'required_if:item_type,variant|numeric|min:0',
            'variants.*.price' => 'required_if:item_type,variant|numeric|min:0',
            'variants.*.purchase_price' => 'nullable|numeric|min:0',
            'variants.*.barcode' => [
                'nullable',
                'string',
                'distinct',
                Rule::unique('product_variants', 'barcode')->where(function ($query) use ($request) {
                    return $query->where('product_id', '!=', $request->product_id);
                })
            ],
            'selling_rates' => 'nullable|array',
            'selling_rates.*.name' => 'nullable|string|max:50',
            'selling_rates.*.price' => 'nullable|numeric|gte:0',
            'selling_rates.*.mrp' => 'nullable|numeric|gte:0',
            'selling_rates.*.qty' => 'nullable|numeric|gte:0',
            'selling_rates.*.wholesale' => 'nullable|numeric|gte:0',
        ];

        $validator = Validator::make($request->all(), $rules, [
            'price.required_if' => 'The price field is required.',
        ], [
            'category_id'   => 'category',
            'brand_id'      => 'brand',
            'SKU'           => 'SKU',
            'hsn_code'      => 'HSN Code',
            'availablility' => 'availability',
            'gst_option'    => 'GST option',
            'product_gst'   => 'GST rates',
            'unit_id'       => 'unit',
        ]);

        $validator->after(function ($validator) use ($request, $branchId) {
            $this->validateUniqueProductName(
                $validator,
                $request->name,
                $branchId,
                (int) ($request->product_id ?? 0)
            );

            // GST validation removed as per request
            // if ($request->gst_option === 'with_gst') {
            //     $productGst = $request->product_gst;
            //     if (empty($productGst) || !is_array($productGst) || count(array_filter($productGst)) === 0) {
            //         $validator->errors()->add('product_gst', 'Please select at least one GST rate when "With GST" is selected.');
            //     }
            // }

            if ($this->isMobileCategoryId($request->category_id)) {
                $qty = (float) ($request->quantity ?? 0);
                $imeiNoJson = $request->imei_no;
                if ($qty > 0) {
                    if (empty($imeiNoJson) || $imeiNoJson === '[]') {
                        $validator->errors()->add('imei_no', 'Please enter IMEI/Serial numbers for the selected mobile quantity.');
                    } else {
                        $imeiArray = is_string($imeiNoJson) ? json_decode($imeiNoJson, true) : $imeiNoJson;
                        if (!is_array($imeiArray)) {
                            $validator->errors()->add('imei_no', 'Invalid IMEI numbers format.');
                        } else {
                            $nonEmptyImeis = array_filter(array_map('trim', $imeiArray));
                            if (count($nonEmptyImeis) != $qty) {
                                $validator->errors()->add('imei_no', 'The number of IMEI numbers must match the quantity (' . $qty . ').');
                            }

                            // Duplicate checking within form
                            $allImeis = [];
                            foreach ($nonEmptyImeis as $imei) {
                                if (in_array($imei, $allImeis)) {
                                    $validator->errors()->add('imei_no', 'IMEI ' . $imei . ' is duplicated in this form.');
                                }
                                $allImeis[] = $imei;

                                // Duplicate checking in database (products)
                                $existsInProducts = DB::table('products')
                                    ->where('id', '!=', $request->product_id ?? 0)
                                    ->where('isDeleted', 0)
                                    ->where('imei_no', 'LIKE', '%"' . $imei . '"%')
                                    ->exists();
                                if ($existsInProducts) {
                                    $validator->errors()->add('imei_no', 'IMEI ' . $imei . ' already exists in products.');
                                }
                            }
                        }
                    }
                }
            }
        });

        $validated = $validator->validate();

        /* -------------------------------------------------
     | 2ï¸âƒ£ Fetch Product
     -------------------------------------------------*/
        // $product = Product::findOrFail($validated['product_id']);

        /* -------------------------------------------------
     | 3ï¸âƒ£ Handle Images
     -------------------------------------------------*/
        $existingImages = json_decode($product->images, true) ?? [];
        $imagesBefore = Product::normalizeImagesJson($existingImages);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $existingImages[] = $this->storeProductImageOriginal($image);
            }
        }

        $generatedPaths = $this->parseGeneratedImagePaths($request);
        if (!empty($generatedPaths)) {
            $existingImages = array_merge($existingImages, $generatedPaths);
        }

        /* -------------------------------------------------
     | 4ï¸âƒ£ Resolve Branch ID
     -------------------------------------------------*/
        $user = Auth::guard('api')->user();

        $branchId = match ($user->role) {
            'sub-admin' => $user->id,
            'admin'     => $request->branch_id ?? $product->branch_id,
            default     => $product->branch_id,
        };

        /* -------------------------------------------------
     | 5ï¸âƒ£ Availability Auto-Logic
     -------------------------------------------------*/
        $availability = ($validated['quantity'] ?? 0) == 0
            ? 'out_stock'
            : ($validated['availablility'] ?? 'in_stock');

        /* -------------------------------------------------
     | 6ï¸âƒ£ Prepare GST Data
     -------------------------------------------------*/
        $gstData = [];
        if (!empty($validated['product_gst']) && is_array($validated['product_gst'])) {
            foreach ($validated['product_gst'] as $taxId) {
                $tax = TaxRate::find($taxId);
                if ($tax) {
                    $gstData[] = [
                        'tax_id'   => $taxId,
                        'tax_name' => $tax->tax_name,
                        'tax_rate' => $tax->tax_rate,
                    ];
                }
            }
        }

        /* -------------------------------------------------
     | 7️⃣ Update Product
     -------------------------------------------------*/
        $oldQuantity = (float) $product->quantity;
        $oldPrice = $product->price;
        $oldPurchasePrice = $product->purchase_price;
        $oldMrp = $product->mrp;

        $updateData = [
            'vendor_id'              => $validated['vendor_id'] ?? 1,
            'category_id'            => $validated['category_id'],
            'brand_id'               => $validated['brand_id'],
            'branch_id'              => $branchId,
            'name'                   => $validated['name'],
            'name_hindi'             => trim((string) ($validated['name_hindi'] ?? '')) ?: null,
            'description'            => $validated['description'] ?? null,
            'price'                  => $validated['price'],
            'selling_prices'         => Product::buildSellingPrices($validated['price'] ?? 0, $request->input('selling_rates', []), $validated['mrp'] ?? null, $request->input('wholesale_price'), $validated['quantity'] ?? null),
            'purchase_price'         => $validated['purchase_price'] ?? null,
            'cost_price'             => $validated['cost_price'] ?? null,
            'landing_cost'           => $validated['landing_cost'] ?? null,
            'product_code'           => $validated['product_code'] ?? null,
            'supplier_code'          => $validated['supplier_code'] ?? null,
            'international_code'     => $validated['international_code'] ?? null,
            'serial_no_status'       => $validated['serial_no_status'] ?? null,
            'non_inventory_type'     => $validated['non_inventory_type'] ?? null,
            'stock_validation_status' => $validated['stock_validation_status'] ?? null,
            'item_type'              => $validated['item_type'] ?? null,
            'discount_print_status'  => $validated['discount_print_status'] ?? null,
            'item_created_on'        => $validated['item_created_on'] ?? null,
            'SKU'                    => $validated['SKU'],
            'hsn_code'               => $validated['hsn_code'],
            'tax_rate'               => $validated['tax_rate'] ?? null,
            'item_location'          => $validated['item_location'] ?? null,
            'minimum_stock_qty'      => $validated['minimum_stock_qty'] ?? 0,
            'discount_type'          => $validated['discount_type'] ?? null,
            'sale_discount'          => $validated['sale_discount'] ?? 0,
            'inclusive_of_tax'       => $request->boolean('inclusive_of_tax') ? 1 : 0,
            'secondary_unit_id'      => $validated['secondary_unit_id'] ?? null,
            'conversion_rate'        => $validated['conversion_rate'] ?? null,
            'barcode'                => $validated['barcode'] ?? null,
            'quantity'               => $validated['quantity'] ?? 0,
            'mrp'                    => $validated['mrp'] ?? null,
            'batch_no'               => $validated['batch_no'] ?? null,
            'label_code'             => $validated['label_code'] ?? null,
            'label_reference'        => array_key_exists('label_reference', $validated) ? $validated['label_reference'] : $product->label_reference,
            'specific_qty'           => $validated['specific_qty'] ?? null,
            'imei_no'                => $this->isMobileCategoryId($validated['category_id']) ? ($request->imei_no ?? '[]') : null,
            'unit_id'                => $validated['unit_id'],
            'status'                 => $validated['status'] ?? $product->status,
            'availablility'          => $availability,
            'gst_option'             => $validated['gst_option'] ?? 'without_gst',
            'product_gst'            => !empty($gstData) ? json_encode($gstData) : null,
            'images'                 => json_encode($existingImages),
            'capacity'               => $validated['capacity'] ?? null,
            'voltage'                => $validated['voltage'] ?? null,
            'warranty'               => $validated['warranty'] ?? null,
            'expiry_date'            => $validated['expiry_date'] ?? null,
            'is_rent_available'      => $validated['is_rent_available'] ?? 0,
            'rent_period'            => ($validated['is_rent_available'] ?? 0) == 1 ? ($validated['rent_period'] ?? null) : null,
            'rent_price'             => ($validated['is_rent_available'] ?? 0) == 1 ? ($validated['rent_price'] ?? null) : null,
        ];

        // Sync barcode_details prices if the main price/mrp changed
        $details = is_string($product->barcode_details) ? json_decode($product->barcode_details, true) : ($product->barcode_details ?? []);
        if (is_array($details) && !empty($details)) {
            $changed = false;
            foreach ($details as $bCode => $bData) {
                if ($oldPrice != $validated['price']) {
                    $details[$bCode]['price'] = $validated['price'];
                    $changed = true;
                }
                if (array_key_exists('mrp', $validated) && $oldMrp != $validated['mrp']) {
                    $details[$bCode]['mrp'] = $validated['mrp'];
                    $changed = true;
                }
            }
            if ($changed) {
                $updateData['barcode_details'] = $details;
            }
        }

        $product->update($updateData);

        if (Product::normalizeImagesJson($existingImages) !== $imagesBefore) {
            $product->syncImagesToBranchCopies();
        }

        if (array_key_exists('batch_no', $validated) && $this->normalizeBatchNo($validated['batch_no'] ?? null) !== $previousBatchNo) {
            $this->syncGlobalBatchNo($product->branch_id, $validated['batch_no'] ?? null);
        }

        if (isset($validated['variants']) && is_array($validated['variants'])) {
            $product->variants()->delete();
            foreach ($validated['variants'] as $variant) {
                $variantBarcode = !empty($variant['barcode']) ? $variant['barcode'] : time() . rand(1000, 9999);
                $product->variants()->create([
                    'size'  => $variant['size'],
                    'color' => $variant['color'],
                    'qty'   => $variant['qty'],
                    'price' => $variant['price'],
                    'purchase_price' => $variant['purchase_price'] ?? null,
                    'barcode' => $variantBarcode,
                ]);
            }
        }

        /* -------------------------------------------------
     | 8️⃣ Inventory Log
     -------------------------------------------------*/
        $newMrp = $validated['mrp'] ?? null;
        $newPurchasePrice = $validated['purchase_price'] ?? null;
        
        $actor = \App\Services\NotificationService::actorName($user->id ?? null);
        $changeSummary = $this->describeProductChanges($product);
        if (isset($validated['variants']) && is_array($validated['variants'])) {
            $changeSummary = trim($changeSummary . ' Variants were updated.');
        }
        $this->notifyProductEvent(
            $product,
            'product_updated',
            'Product Updated',
            $actor . " updated '{$product->name}'. " . $changeSummary
        );

        if ($oldPrice != $validated['price'] || $oldMrp != $newMrp || $oldPurchasePrice != $newPurchasePrice) {
            $this->dispatchCrossBranchPriceUpdates($product, $oldPrice, $validated['price'], $oldMrp, $newMrp, $oldPurchasePrice, $newPurchasePrice);
        }

        if (! $isWarrantyCategory) {
            ProductInventory::create([
                'product_id'    => $product->id,
                'initial_stock' => $oldQuantity,
                'current_stock' => $validated['quantity'] ?? 0,
                'branch_id'     => $branchId,
                'create_by'     => $user->id,
                'type'          => 'Edit',
                'date'          => now(),
            ]);
        }

        /* -------------------------------------------------
     | 9ï¸âƒ£ Response
     -------------------------------------------------*/
        return response()->json([
            'status'  => true,
            'message' => 'Product updated successfully',
            'product' => $product->fresh(['variants']),
        ], 200);
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);

        if (! $product) {
            return response()->json(['status' => false, 'error' => 'Product not found'], 404);
        }

        $orderItemExists = OrderItem::where('product_id', $id)->where('isDeleted', 0)->exists();
        $purchaseExists  = Purchases::where('item', $id)->where('isDeleted', 0)->exists();

        if ($orderItemExists) {
            return response()->json([
                'status' => false,
                'error'  => 'Product is associated with existing orders and cannot be deleted.',
            ], 409);
        }

        if ($purchaseExists) {
            return response()->json([
                'status' => false,
                'error'  => 'Product is associated with existing purchases and cannot be deleted.',
            ], 409);
        }

        // Soft delete: mark isDeleted as 1
        $product->isDeleted = 1;
        $product->save();

        $actor = \App\Services\NotificationService::actorName(Auth::guard('api')->id());
        $this->notifyProductEvent(
            $product,
            'product_deleted',
            'Product Deleted',
            $actor . " moved '{$product->name}' to Deleted Products."
        );

        return response()->json(['status' => true, 'message' => 'Product moved to Deleted Products'], 200);
    }

    public function restoreProduct($id)
    {
        $product = Product::where('id', $id)->where('isDeleted', 1)->first();

        if (! $product) {
            return response()->json(['status' => false, 'error' => 'Deleted product not found'], 404);
        }

        $product->isDeleted = 0;
        $product->save();

        $actor = \App\Services\NotificationService::actorName(Auth::guard('api')->id());
        $this->notifyProductEvent(
            $product,
            'product_restored',
            'Product Restored',
            $actor . " restored product '{$product->name}'."
        );

        return response()->json(['status' => true, 'message' => 'Product restored successfully'], 200);
    }

    /**
     * Store a product image at original resolution / bytes — no resize or re-encode.
     */
    private function storeProductImageOriginal(\Illuminate\Http\UploadedFile $image): string
    {
        $extension = strtolower((string) ($image->getClientOriginalExtension() ?: $image->extension() ?: 'jpg'));
        $extension = preg_replace('/[^a-z0-9]/', '', $extension) ?: 'jpg';
        $filename = 'prod_' . time() . '_' . uniqid('', true) . '.' . $extension;
        $relativePath = 'img/product/' . $filename;

        // Raw file bytes only — do not pass through GD / Intervention.
        Storage::disk('public')->put($relativePath, file_get_contents($image->getRealPath()));

        return $relativePath;
    }

    private function parseGeneratedImagePaths(Request $request): array
    {
        $paths = [];

        if ($request->filled('generated_image_paths')) {
            $decoded = json_decode((string) $request->generated_image_paths, true);
            if (is_array($decoded)) {
                foreach ($decoded as $path) {
                    $path = trim((string) $path);
                    if ($path !== '') {
                        $paths[] = $path;
                    }
                }
            }
        } elseif ($request->filled('generated_image_path')) {
            $path = trim((string) $request->generated_image_path);
            if ($path !== '') {
                $paths[] = $path;
            }
        }

        return array_values(array_unique($paths));
    }

    public function removeProductImage(Request $request)
    {
        $product = Product::find($request->product_id);

        if (! $product) {
            return response()->json(["success" => false, "message" => "Product not found"]);
        }

        $images = json_decode($product->images, true);

        // Check if image exists
        if (($key = array_search($request->image, $images)) !== false) {
            // Remove image from storage
            Storage::delete("storage/img/product" . $request->image);

            // Remove image from array and update database
            unset($images[$key]);
            $product->images = json_encode(array_values($images)); // Re-index array
            $product->save();
            $product->syncImagesToBranchCopies();

            return response()->json(["success" => true, "message" => "Image deleted"]);
        }

        return response()->json(["success" => false, "message" => "Image not found"]);
    }

    private function normalizeImportHeader(?string $header): string
    {
        $header = strtolower(trim((string) $header));
        // Excel wraps long titles onto new lines inside one cell ("Sale\nprice", "Default\nMRP")
        $header = str_replace(["\r\n", "\n", "\r", "\t", '_', "\u{00A0}"], ' ', $header);
        $header = preg_replace('/[\x00-\x1F\x7F]/', '', $header) ?? $header;
        $header = str_replace(['*', ':', '(', ')', '.'], ' ', $header);
        $header = preg_replace('/\s+/', ' ', $header) ?? $header;

        return trim($header);
    }

    private function compactImportHeader(string $header): string
    {
        return preg_replace('/[^a-z0-9]/', '', $this->normalizeImportHeader($header)) ?? '';
    }

    private function importColumnValue(array $rowCells, array $headerMap, array $aliases, $default = null, bool $allowPrefix = true)
    {
        $aliases = array_values(array_filter(array_map(fn ($alias) => $this->normalizeImportHeader($alias), $aliases)));
        $colIdx = null;

        foreach ($aliases as $alias) {
            if (array_key_exists($alias, $headerMap)) {
                $colIdx = $headerMap[$alias];
                break;
            }
        }

        if ($colIdx === null) {
            foreach ($aliases as $alias) {
                $aliasCompact = $this->compactImportHeader($alias);
                if (strlen($aliasCompact) < 3) {
                    continue;
                }
                foreach ($headerMap as $header => $idx) {
                    if ($this->compactImportHeader((string) $header) === $aliasCompact) {
                        $colIdx = $idx;
                        break 2;
                    }
                }
            }
        }

        if ($colIdx === null && $allowPrefix) {
            foreach ($headerMap as $header => $idx) {
                if ($header === '' || strlen((string) $header) < 8) {
                    continue;
                }
                foreach ($aliases as $alias) {
                    if (strlen($alias) < 8) {
                        continue;
                    }
                    if (str_starts_with($alias, $header) || str_starts_with($header, $alias)) {
                        $colIdx = $idx;
                        break 2;
                    }
                }
            }
        }

        if ($colIdx === null) {
            return $default;
        }

        $val = trim((string) ($rowCells[$colIdx] ?? ''));
        if ($val === '' || $val === '-') {
            return $default;
        }

        return $val;
    }

    private function parseImportMoney($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $clean = preg_replace('/[^0-9.\-]/', '', (string) $value);
        if ($clean === '' || $clean === '-' || $clean === '.' || $clean === '-.') {
            return null;
        }

        return (float) $clean;
    }

    private function parseImportDiscountType($value): ?string
    {
        $raw = strtolower(trim((string) $value));
        if ($raw === '') {
            return null;
        }
        if (str_contains($raw, '%') || str_contains($raw, 'percent')) {
            return 'percentage';
        }
        if (str_contains($raw, 'amount') || preg_match('/discount\s*a/', $raw) || $raw === 'a') {
            return 'amount';
        }

        return in_array($raw, ['percentage', 'amount'], true) ? $raw : null;
    }

    private function parseImportDescriptionWholesale(?string $description): array
    {
        $raw = trim((string) $description);
        if ($raw === '' || $raw === '-' || $raw === '...') {
            return [
                'description' => null,
                'wholesale_price' => null,
            ];
        }

        $numeric = preg_replace('/[^0-9.]/', '', $raw) ?? '';
        if ($numeric === '' || !preg_match('/^11\d+(\.\d+)?$/', $numeric)) {
            return [
                'description' => $raw,
                'wholesale_price' => null,
            ];
        }

        $wholesalePart = substr($numeric, 2);
        if ($wholesalePart === '' || $wholesalePart === '.') {
            return [
                'description' => null,
                'wholesale_price' => null,
            ];
        }

        $wholesalePrice = $this->parseImportMoney($wholesalePart);
        if ($wholesalePrice === null) {
            return [
                'description' => null,
                'wholesale_price' => null,
            ];
        }

        return [
            'description' => null,
            'wholesale_price' => $wholesalePrice,
        ];
    }

    private function sanitizeImportBarcode(?string $code): string
    {
        return preg_replace('/[^A-Za-z0-9\-_.\/+]/', '', trim((string) $code)) ?? '';
    }

    private function parseImportDate($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return \Carbon\Carbon::createFromFormat('m/d/Y', (string) $value)->format('Y-m-d');
                        } catch (\Exception $e) {
                            try {
                return \Carbon\Carbon::parse((string) $value)->format('Y-m-d');
                            } catch (\Exception $e2) {
                return null;
            }
        }
    }

    private function findOrCreateImportCategory(string $name, $branchId): Category
    {
        $cacheKey = (int) $branchId . '|' . strtolower($name);
        if (isset(self::$importCategoryCache[$cacheKey])) {
            return self::$importCategoryCache[$cacheKey];
        }

                    $category = Category::where('branch_id', $branchId)
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                        ->first();

                    if (!$category) {
                        $category = Category::create([
                'name'      => $name,
                            'branch_id' => $branchId,
                'isDeleted' => 0,
            ]);
        }

        self::$importCategoryCache[$cacheKey] = $category;

        return $category;
    }

    private function findOrCreateImportBrand(?string $name, $branchId): ?Brand
    {
        $name = trim((string) $name);
        if ($name === '') {
            return null;
        }

        $cacheKey = (int) $branchId . '|' . strtolower($name);
        if (isset(self::$importBrandCache[$cacheKey])) {
            return self::$importBrandCache[$cacheKey];
        }

        $brand = Brand::where('branch_id', $branchId)
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->first();

        if (!$brand) {
            $brand = Brand::create([
                'name'      => $name,
                'branch_id' => $branchId,
                'isDeleted' => 0,
                'status'    => 1,
            ]);
        }

        self::$importBrandCache[$cacheKey] = $brand;

        return $brand;
    }

    private function findOrCreateImportUnit(?string $name, $branchId): ?Unit
    {
        $name = trim((string) $name);
        if ($name === '') {
            return null;
        }

        $cacheKey = strtolower($name);
        if (isset(self::$importUnitCache[$cacheKey])) {
            return self::$importUnitCache[$cacheKey];
        }

        $unit = Unit::whereRaw('LOWER(unit_name) = ?', [strtolower($name)])->first();
        if (!$unit) {
            $unit = Unit::create([
                'unit_name'  => $name,
                'created_by' => $branchId,
                'is_delete'  => 0,
            ]);
        }

        self::$importUnitCache[$cacheKey] = $unit;

        return $unit;
    }

    private function mergeImportBarcode(?string $existing, string $itemCode): string
    {
        $itemCode = $this->sanitizeImportBarcode($itemCode);
        if ($itemCode === '') {
            return trim((string) $existing);
        }

        $parts = array_values(array_filter(array_map('trim', explode(',', (string) $existing))));
        foreach ($parts as $part) {
            if (strcasecmp($this->sanitizeImportBarcode($part), $itemCode) === 0) {
                return implode(',', $parts);
            }
        }
        $parts[] = $itemCode;

        return implode(',', $parts);
    }

    private function rememberImportProductInCache(int $branchId, string $itemCode, ?Product $product): void
    {
        $itemCode = strtoupper($this->sanitizeImportBarcode($itemCode));
        if ($itemCode !== '') {
            self::$importProductCodeCache[(int) $branchId . '|' . $itemCode] = $product;
        }

                        if ($product) {
            $nameKey = strtolower(trim((string) $product->name));
            if ($nameKey !== '') {
                self::$importProductNameCache[(int) $branchId . '|n|' . $nameKey] = $product;
            }

            self::$importProductModelCache[(int) $product->id] = $product;

            if (self::$currentImportJobKey !== null) {
                $this->registerImportLookupProduct(self::$currentImportJobKey, $product, $itemCode);
            }
        }
    }

    private function findExistingImportProductByName(string $name, $branchId): ?Product
    {
        $nameKey = strtolower(trim($name));
        if ($nameKey === '') {
            return null;
        }

        $cacheKey = (int) $branchId . '|n|' . $nameKey;
        if (array_key_exists($cacheKey, self::$importProductNameCache)) {
            return self::$importProductNameCache[$cacheKey];
        }

        $product = Product::where('branch_id', $branchId)
            ->where('isDeleted', 0)
            ->whereRaw('LOWER(TRIM(name)) = ?', [$nameKey])
            ->first();

        self::$importProductNameCache[$cacheKey] = $product;

        return $product;
    }

    private function findExistingImportProduct(string $itemCode, string $name, $categoryId, $branchId): ?Product
    {
        if (self::$importJobLookupMaps !== null) {
            $productId = null;

            if ($itemCode !== '') {
                $normalizedCode = strtoupper($this->sanitizeImportBarcode($itemCode));
                $productId      = self::$importJobLookupMaps['by_code'][$normalizedCode] ?? null;
            }

            if (!$productId && $name !== '') {
                $productId = self::$importJobLookupMaps['by_name'][strtolower(trim($name))] ?? null;
            }

            if ($productId) {
                return $this->findImportProductModelById((int) $productId);
            }

            return null;
        }

        $base = Product::where('branch_id', $branchId)->where('isDeleted', 0);

        if ($itemCode !== '') {
            $normalizedCode = strtoupper($this->sanitizeImportBarcode($itemCode));
            $cacheKey         = (int) $branchId . '|' . $normalizedCode;
            if (array_key_exists($cacheKey, self::$importProductCodeCache)) {
                $product = self::$importProductCodeCache[$cacheKey];
                if ($product) {
                    return $product;
                }
                        } else {
                $product = (clone $base)->where(function ($q) use ($itemCode, $normalizedCode) {
                    $q->where('barcode', $itemCode)
                        ->orWhere('SKU', $itemCode)
                        ->orWhere('product_code', $itemCode)
                        ->orWhere('barcode', $normalizedCode)
                        ->orWhere('SKU', $normalizedCode)
                        ->orWhere('product_code', $normalizedCode);
                })->first();

                self::$importProductCodeCache[$cacheKey] = $product;

                if ($product) {
                    return $product;
                }
            }
        }

        $byName = $this->findExistingImportProductByName($name, $branchId);
        if ($byName) {
            return $byName;
        }

        if ($name !== '' && $categoryId) {
            return (clone $base)->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($name))])
                ->where('category_id', $categoryId)
                ->first();
        }

        return null;
    }

    private function mapImportRow(callable $col): array
    {
        $itemCode = $this->sanitizeImportBarcode((string) $col([
            'item code', 'item/model code', 'item/model code*', 'item_code', 'sku', 'sku code', 'product code', 'barcode',
        ], '', false));

        $name = trim((string) $col([
            'items', 'item name*', 'item name', 'name', 'product name', 'item/model desc', 'item/model desc*',
            'item/model', 'item/model*', 'item/model description',
        ], '', false));

        $categoryName = trim((string) $col(['category', 'category name'], '', false));
        if ($categoryName === '') {
            $categoryName = trim((string) $col(['product', 'product*'], '', false));
        }

        return [
            'name' => $name,
            'category_name' => $categoryName,
            'brand_name' => $col(['company', 'company name', 'brand', 'brand name', 'brand*'], null, false),
            'item_code' => $itemCode,
            'description' => $col(['description'], null),
            'price' => $col(['sale price', 'selling price', 'selling price*', 'price', 'product price', 'sales price'], null),
            'purchase_price' => $col(['purchase price', 'purchase rate', 'wholesale purchase rate', 'purchase p', 'purchase', 'cost price', 'cost price*', 'cost_price'], null),
            'wholesale_price' => $col(['wholesale price', 'wholesale rate', 'wholesale'], null, false),
            'landing_cost' => $col(['landing cost', 'landing cost*', 'landing_cost'], null),
            'mrp' => $col(['default mrp', 'default mr', 'mrp'], null),
            'online_price' => $col(['online store price', 'online sto', 'online store', 'online price'], null),
            'hsn_code' => $col(['hsn', 'hsn/sac code', 'hsn/sac code*', 'hsn/sac', 'hsn code', 'hsn_code'], null),
            'unit_name' => $col(['base unit (x)', 'base unit', 'unit quantity code', 'unit quantity code*', 'uqc', 'unit', 'unit_name'], null),
            'status' => strtolower((string) $col(['status', 'product status'], 'active')),
            'qty' => $col(['opening stock quantity', 'opening stock', 'opening qty', 'opening stock qty', 'qty', 'qty*', 'quantity', 'quantity*', 'qt'], null),
            'supplier_code' => $col(['supplier i/m code', 'supplier i/m code*', 'supplier_code'], null),
            'intl_code' => $col(['international i/m code', 'international_code'], null),
            'serial_no_status' => $col(['serial no status', 'serial_no_status'], null),
            'non_inventory_type' => $col(['non inventory type', 'non_inventory_type'], null),
            'stock_val_status' => $col(['stock validation status', 'stock_validation_status'], null),
            'item_type' => $col(['item/type', 'item type', 'item_type'], null, false),
            'discount_print' => $col(['discount print status', 'discount_print_status'], null),
            'item_created_on' => $col(['item created on', 'item_created_on'], null),
            'tax_rate' => $col(['tax rate', 'tax rate (%)', 'tax %', 'gst rate', 'gst %', 'tax_rate'], null),
            'item_location' => $col(['item location', 'item locat', 'location', 'item_location'], null),
            'min_stock_qty' => $col(['minimum stock quantity', 'minimum stock qty', 'min stock quantity', 'min stock qty', 'min stock', 'minimum', 'minimum_stock_qty'], null),
            'discount_type' => $col(['discount type', 'discount t', 'discount_type'], null),
            'sale_discount' => $col(['sale discount', 'sale_discount'], null),
            'inclusive_of_tax' => $col(['inclusive of tax', 'inclusive c', 'inclusive', 'inclusive_of_tax'], null),
            'secondary_unit_name' => $col(['secondary unit (y)', 'secondary unit', 'secondary', 'secondary_unit'], null),
            'conversion_rate' => $col(['conversion rate (n) (x = ny)', 'conversion rate (n)', 'conversion rate', 'conversion_rate'], null),
            'maintain_imei' => strtolower(trim((string) $col(['maintain imei no:(y/n)', 'maintain imei no', 'maintain imei', 'maintain_imei'], ''))),
            'auto_imei' => trim((string) $col(['auto imei no:(y/n)', 'auto imei no', 'auto imei', 'imei', 'imei no', 'imei_no', 'serial no', 'serial_no'], '')),
            'availability' => strtolower((string) $col(['availability', 'product availability'], '')),
        ];
    }

    private function upsertImportedProduct(array $row, $branchId, $userId): string
    {
        $name = trim((string) ($row['name'] ?? ''));
        $categoryName = trim((string) ($row['category_name'] ?? ''));
        $itemCode = $this->sanitizeImportBarcode((string) ($row['item_code'] ?? ''));

        if ($name === '') {
            return 'skipped';
        }
        if ($categoryName === '') {
            $categoryName = 'General';
        }

        $price = $this->parseImportMoney($row['price'] ?? null) ?? 0;
        $purchasePrice = $this->parseImportMoney($row['purchase_price'] ?? null);
        $landingCost = $this->parseImportMoney($row['landing_cost'] ?? null);
        $mrp = $this->parseImportMoney($row['mrp'] ?? null);
        $onlinePrice = $this->parseImportMoney($row['online_price'] ?? null);
        $qty = array_key_exists('qty', $row) && $row['qty'] !== null && $row['qty'] !== ''
            ? (float) ($this->parseImportMoney($row['qty']) ?? 0)
            : null;
        $taxRate = $this->parseImportMoney($row['tax_rate'] ?? null);
        $minStockQty = $this->parseImportMoney($row['min_stock_qty'] ?? null);
        $saleDiscount = $this->parseImportMoney($row['sale_discount'] ?? null);
        $conversionRate = $this->parseImportMoney($row['conversion_rate'] ?? null);
        $status = in_array(strtolower((string) ($row['status'] ?? 'active')), ['active', 'inactive'], true)
            ? strtolower((string) $row['status'])
            : 'active';
        $parsedDiscountType = $this->parseImportDiscountType($row['discount_type'] ?? null);
        $inclusiveRaw = $row['inclusive_of_tax'] ?? null;
        $inclusiveOfTaxFlag = in_array(strtolower(trim((string) $inclusiveRaw)), ['yes', 'y', '1', 'true', 'inclusive'], true) ? 1 : 0;
        $itemCreatedOn = $this->parseImportDate($row['item_created_on'] ?? null);

        $category = $this->findOrCreateImportCategory($categoryName, $branchId);
        $brand = $this->findOrCreateImportBrand($row['brand_name'] ?? null, $branchId);
        $unit = $this->findOrCreateImportUnit($row['unit_name'] ?? null, $branchId);
        $secondaryUnit = $this->findOrCreateImportUnit($row['secondary_unit_name'] ?? null, $branchId);

        $product = $this->findExistingImportProduct($itemCode, $name, $category->id, $branchId);

        if ($itemCode === '') {
            if ($product) {
                $itemCode = $this->sanitizeImportBarcode((string) ($product->barcode ?: $product->SKU ?: $product->product_code));
            }
            if ($itemCode === '') {
                $itemCode = $this->generateUniqueImportItemCode((int) $branchId);
            }
        }

        $maintainImei = strtolower(trim((string) ($row['maintain_imei'] ?? '')));
        $autoImei = trim((string) ($row['auto_imei'] ?? ''));
                    $isImeiManaged = ($maintainImei === 'y') || $this->isMobileCategoryId($category->id);
                    $imeisList = [];
        if ($isImeiManaged && $autoImei !== '' && strtolower($autoImei) !== 'n' && $autoImei !== '-') {
            foreach (preg_split('/[\n\r,;\/]+/', $autoImei) as $splitImei) {
                            $cleanImei = trim($splitImei);
                            if ($cleanImei !== '' && strtolower($cleanImei) !== 'n') {
                                $imeisList[] = $cleanImei;
                            }
                        }
                    }

        $extraRates = [];
        if ($onlinePrice !== null && abs($onlinePrice - $price) >= 0.0001) {
            $extraRates[] = [
                'name' => 'Online Store',
                'price' => $onlinePrice,
                'mrp' => $mrp,
            ];
        }

        $descriptionParsed = $this->parseImportDescriptionWholesale($row['description'] ?? null);
        $importDescription = $descriptionParsed['description'];
        $wholesalePrice = $descriptionParsed['wholesale_price'];
        $explicitWholesale = $this->parseImportMoney($row['wholesale_price'] ?? null);
        if ($explicitWholesale !== null) {
            $wholesalePrice = $explicitWholesale;
        }
        $sellingPrices = Product::buildSellingPrices($price, $extraRates, $mrp, $wholesalePrice);

                    if ($product) {
                        $oldQty = (float) $product->quantity;
            $product->name = $name;
            $product->category_id = $category->id;
            if ($brand) {
                            $product->brand_id = $brand->id;
                        }
            $product->barcode = $this->mergeImportBarcode($product->barcode, $itemCode);
            $product->product_code = $itemCode;
            $product->price = $price;
            $product->mrp = $mrp ?? $product->mrp;
            $product->purchase_price = $purchasePrice ?? $product->purchase_price;
            $product->cost_price = $purchasePrice ?? $product->cost_price;
            $product->landing_cost = $landingCost ?? $product->landing_cost;
            $product->hsn_code = ($row['hsn_code'] ?? null) !== null ? $row['hsn_code'] : $product->hsn_code;
            $product->status = $status;
            if ($unit) {
                $product->unit_id = $unit->id;
            }
            if (array_key_exists('description', $row)) {
                $product->description = $importDescription;
            }
            $product->selling_prices = $sellingPrices;
            $product->tax_rate = $taxRate ?? $product->tax_rate;
            $product->item_location = ($row['item_location'] ?? null) !== null ? $row['item_location'] : $product->item_location;
            $product->minimum_stock_qty = $minStockQty ?? $product->minimum_stock_qty;
            $product->discount_type = $parsedDiscountType ?? $product->discount_type;
            $product->sale_discount = $saleDiscount ?? $product->sale_discount;
            $product->inclusive_of_tax = $inclusiveRaw !== null ? $inclusiveOfTaxFlag : $product->inclusive_of_tax;
                        if ($secondaryUnit) {
                            $product->secondary_unit_id = $secondaryUnit->id;
                        }
            $product->conversion_rate = $conversionRate ?? $product->conversion_rate;
            $excelItemType = strtolower(trim((string) ($row['item_type'] ?? '')));
            $product->item_type = $excelItemType === 'variant' ? 'variant' : 'normal';
            if (($row['supplier_code'] ?? null) !== null) {
                $product->supplier_code = $row['supplier_code'] ?: null;
            }
            if ($qty !== null) {
                $product->quantity = $oldQty + $qty;
            }
            if ($isImeiManaged && !empty($imeisList)) {
                            $existingImeis = json_decode($product->imei_no ?? '[]', true);
                            if (!is_array($existingImeis)) {
                                $existingImeis = [];
                            }
                            foreach ($imeisList as $imei) {
                    if (!in_array($imei, $existingImeis, true)) {
                                    $existingImeis[] = $imei;
                                }
                            }
                            $product->imei_no = json_encode($existingImeis);
                        }
            $product->availablility = ((float) $product->quantity) > 0 ? 'in_stock' : ($product->availablility ?: 'out_stock');
                        $product->save();
            $this->rememberImportProductInCache((int) $branchId, $itemCode, $product);

            if ($qty !== null && (float) $qty !== 0.0) {
                        ProductInventory::create([
                            'product_id'    => $product->id,
                            'initial_stock' => $oldQty,
                            'current_stock' => $product->quantity,
                            'branch_id'     => $branchId,
                    'create_by'     => $userId,
                            'type'          => 'Edit',
                            'date'          => now(),
                        ]);
            }

            return 'updated';
        }

        $createQty = $qty ?? 0;
        $availability = str_replace(' ', '_', (string) ($row['availability'] ?? ''));
        if (!in_array($availability, ['in_stock', 'out_stock'], true)) {
            $availability = $createQty > 0 ? 'in_stock' : 'out_stock';
        }

                        $product = Product::create([
            'name'                    => $name,
            'category_id'             => $category->id,
            'brand_id'                => $brand?->id,
            'product_code'            => $itemCode,
            'SKU'                     => Product::getNextAvailableSku((int) $branchId),
            'barcode'                 => $itemCode,
            'quantity'                => $createQty,
            'unit_id'                 => $unit?->id,
            'price'                   => $price,
            'mrp'                     => $mrp,
            'purchase_price'          => $purchasePrice,
            'cost_price'              => $purchasePrice,
            'landing_cost'            => $landingCost,
            'selling_prices'          => $sellingPrices,
            'hsn_code'                => $row['hsn_code'] ?? null,
            'status'                  => $status,
            'availablility'           => $availability,
            'description'             => $importDescription,
            'branch_id'               => $branchId,
            'isDeleted'               => 0,
            'supplier_code'           => $row['supplier_code'] ?: null,
            'international_code'      => $row['intl_code'] ?: null,
            'serial_no_status'        => $row['serial_no_status'] ?: null,
            'non_inventory_type'      => $row['non_inventory_type'] ?: null,
            'stock_validation_status' => $row['stock_val_status'] ?: null,
            'item_type'               => (strtolower(trim((string) ($row['item_type'] ?? ''))) === 'variant') ? 'variant' : 'normal',
            'discount_print_status'   => $row['discount_print'] ?: null,
            'item_created_on'         => $itemCreatedOn,
            'tax_rate'                => $taxRate,
            'item_location'           => $row['item_location'] ?? null,
            'minimum_stock_qty'       => $minStockQty ?? 0,
            'discount_type'           => $parsedDiscountType,
            'sale_discount'           => $saleDiscount ?? 0,
            'inclusive_of_tax'        => $inclusiveRaw !== null ? $inclusiveOfTaxFlag : 0,
            'secondary_unit_id'       => $secondaryUnit?->id,
            'conversion_rate'         => $conversionRate,
            'imei_no'                 => json_encode($imeisList),
            'create_by'               => $userId,
                        ]);

                        ProductInventory::create([
                            'product_id'    => $product->id,
            'initial_stock' => $createQty,
            'current_stock' => $createQty,
                            'branch_id'     => $branchId,
            'create_by'     => $userId,
                            'type'          => 'Create',
                            'date'          => now(),
                        ]);

        $this->rememberImportProductInCache((int) $branchId, $itemCode, $product);

        return 'created';
    }

    private function resolveImportBranchId(Request $request, $user)
    {
        return match (strtolower($user->role)) {
            'admin'     => $request->sub_branch_id ?: $user->id,
            'sub-admin' => $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };
    }

    private function importFileValidationRules(): array
    {
        return [
            'csv_file' => 'required|file|max:51200',
        ];
    }

    private function importFileValidationMessages(): array
    {
        return [
            'csv_file.max'   => 'File is too large. Maximum allowed size is 50 MB.',
            'csv_file.mimes' => 'Please upload a valid CSV or Excel file (.csv, .xlsx, .xls).',
        ];
    }

    private function prepareImportRuntimeLimits(): void
    {
        @set_time_limit(600);
        @ini_set('memory_limit', '1024M');
    }

    private function importPreviewCacheKey(string $token): string
    {
        return 'product_import_preview_' . $token;
    }

    private function importRowsDiskPath(string $token): string
    {
        return 'temp/product-imports/' . $token . '.jsonl';
    }

    private function storeImportPreviewSession(string $token, $userId, $branchId, string $fileName, string $rowsFile, int $totalCount, int $validCount, array $lookups): void
    {
        $this->patchImportPreviewCache($token, [
            'user_id'     => (int) $userId,
            'branch_id'   => (int) $branchId,
            'file_name'   => $fileName,
            'rows_file'   => $rowsFile,
            'total_count' => $totalCount,
            'valid_count' => $validCount,
            'lookups'     => $lookups,
            'status'      => 'complete',
            'created_at'  => now()->toDateTimeString(),
        ]);
    }

    private function createPendingImportPreviewSession(
        string $token,
        $userId,
        $branchId,
        string $fileName,
        string $uploadFile,
        string $rowsFile
    ): void {
        $this->patchImportPreviewCache($token, [
            'user_id'        => (int) $userId,
            'branch_id'      => (int) $branchId,
            'file_name'      => $fileName,
            'upload_file'    => $uploadFile,
            'rows_file'      => $rowsFile,
            'status'         => 'pending',
            'total_count'    => 0,
            'valid_count'    => 0,
            'processed_rows' => 0,
            'lookups'        => null,
            'error'          => null,
            'created_at'     => now()->toDateTimeString(),
        ]);
    }

    private function patchImportPreviewCache(string $token, array $patch): void
    {
        $key    = $this->importPreviewCacheKey($token);
        $cached = Cache::get($key, []);

        Cache::put($key, array_merge(is_array($cached) ? $cached : [], $patch), now()->addHours(3));
    }

    private function getImportPreviewSession(string $token, $userId): ?array
    {
        $cached = Cache::get($this->importPreviewCacheKey($token));
        if (!is_array($cached) || (int) ($cached['user_id'] ?? 0) !== (int) $userId) {
            return null;
        }

        return $cached;
    }

    private function clearImportPreviewSession(string $token): void
    {
        $cached = Cache::get($this->importPreviewCacheKey($token));
        if (is_array($cached)) {
            if (!empty($cached['rows_file'])) {
                Storage::delete((string) $cached['rows_file']);
            }
            if (!empty($cached['upload_file'])) {
                Storage::delete((string) $cached['upload_file']);
            }
        }

        Cache::forget($this->importPreviewCacheKey($token));
    }

    private function writeImportRowsToDisk(string $token, array $rows): string
    {
        $relativePath = $this->importRowsDiskPath($token);
        Storage::makeDirectory('temp/product-imports');

        $handle = fopen(Storage::path($relativePath), 'wb');
        foreach ($rows as $row) {
            fwrite($handle, json_encode($row, JSON_UNESCAPED_UNICODE) . "\n");
        }
        fclose($handle);

        return $relativePath;
    }

    private function readImportRowsPage(string $relativePath, int $page, int $perPage): array
    {
        $path = Storage::path($relativePath);
        if (!is_file($path)) {
            return [];
        }

        $offset = max(0, ($page - 1) * $perPage);
        $rows   = [];
        $index  = 0;

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return [];
        }

        while (($line = fgets($handle)) !== false) {
            if ($index >= $offset && count($rows) < $perPage) {
                $decoded = json_decode(trim($line), true);
                if (is_array($decoded)) {
                    $rows[] = $decoded;
                }
            }
            $index++;
            if ($index >= $offset + $perPage) {
                break;
            }
        }

        fclose($handle);

        return $rows;
    }

    private function importRowMatchesSearch(array $mapped, int $rowNumber, string $search): bool
    {
        if ($search === '') {
            return true;
        }

        if ($rowNumber > 0 && str_contains((string) $rowNumber, $search)) {
            return true;
        }

        $haystack = strtolower(implode(' ', array_filter([
            (string) ($mapped['name'] ?? ''),
            (string) ($mapped['item_code'] ?? ''),
            (string) ($mapped['category_name'] ?? ''),
            (string) ($mapped['brand_name'] ?? ''),
            (string) ($mapped['description'] ?? ''),
        ], fn ($value) => trim($value) !== '')));

        return str_contains($haystack, $search);
    }

    /**
     * @return array{rows: array, total_matches: int}
     */
    private function searchImportRowsPage(string $relativePath, string $search, int $page, int $perPage): array
    {
        $path = Storage::path($relativePath);
        if (!is_file($path)) {
            return ['rows' => [], 'total_matches' => 0];
        }

        $search = strtolower(trim($search));
        if ($search === '') {
            return ['rows' => $this->readImportRowsPage($relativePath, $page, $perPage), 'total_matches' => 0];
        }

        $offset       = max(0, ($page - 1) * $perPage);
        $rows         = [];
        $totalMatches = 0;

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return ['rows' => [], 'total_matches' => 0];
        }

        while (($line = fgets($handle)) !== false) {
            $decoded = json_decode(trim($line), true);
            if (!is_array($decoded)) {
                continue;
            }

            $mapped     = $decoded['mapped'] ?? [];
            $rowNumber  = (int) ($decoded['row_number'] ?? 0);
            if (!$this->importRowMatchesSearch($mapped, $rowNumber, $search)) {
                continue;
            }

            if ($totalMatches >= $offset && count($rows) < $perPage) {
                $rows[] = $decoded;
            }

            $totalMatches++;
        }

        fclose($handle);

        return ['rows' => $rows, 'total_matches' => $totalMatches];
    }

    /**
     * @return array{rows: array, total_matches: int}
     */
    private function filterImportRowsPage(string $relativePath, string $search, string $action, int $page, int $perPage, array $lookups): array
    {
        $search = strtolower(trim($search));
        $offset = max(0, ($page - 1) * $perPage);
        $rows = [];
        $totalMatches = 0;

        foreach ($this->iterateImportRowsFromDisk($relativePath) as $decoded) {
            $mapped = $decoded['mapped'] ?? [];
            $rowNumber = (int) ($decoded['row_number'] ?? 0);
            if ($this->resolveImportPreviewAction($mapped, $lookups) !== $action) {
                continue;
            }
            if ($search !== '' && !$this->importRowMatchesSearch($mapped, $rowNumber, $search)) {
                continue;
            }
            if ($totalMatches >= $offset && count($rows) < $perPage) {
                $rows[] = $decoded;
            }
            $totalMatches++;
        }

        return ['rows' => $rows, 'total_matches' => $totalMatches];
    }

    private function iterateImportRowsFromDisk(string $relativePath): \Generator
    {
        $path = Storage::path($relativePath);
        if (!is_file($path)) {
            return;
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return;
        }

        while (($line = fgets($handle)) !== false) {
            $decoded = json_decode(trim($line), true);
            if (is_array($decoded)) {
                yield $decoded;
            }
        }

        fclose($handle);
    }

    private function countImportValidRows(array $rows): int
    {
        $validCount = 0;
        foreach ($rows as $row) {
            $name = trim((string) ($row['mapped']['name'] ?? ''));
            if ($name !== '') {
                $validCount++;
            }
        }

        return $validCount;
    }

    private function shouldImportPreviewRow(
        array $row,
        bool $selectAllValid,
        array $selectedRows,
        array $deselectedMap,
        bool $skipUpdates = false,
        ?int $branchId = null,
        ?array $tabSelection = null
    ): bool {
        $rowNumber = (int) ($row['row_number'] ?? 0);

        if (is_array($tabSelection)) {
            $action = $this->previewRowImportAction($row, $branchId);
            if ($action === 'update') {
                if (!empty($tabSelection['select_all_updates'])) {
                    return !isset($tabSelection['deselected_updates'][$rowNumber]);
                }

                return isset($tabSelection['selected_updates'][$rowNumber]);
            }
            if ($action !== 'create') {
                return false;
            }
            if (!empty($tabSelection['select_all_creates'])) {
                return !isset($tabSelection['deselected_creates'][$rowNumber]);
            }

            return isset($tabSelection['selected_creates'][$rowNumber]);
        }

        $isValid   = trim((string) ($row['mapped']['name'] ?? '')) !== '';

        $selected = $selectAllValid
            ? ($isValid && !isset($deselectedMap[$rowNumber]))
            : in_array($rowNumber, $selectedRows, true);

        if (!$selected) {
            return false;
        }

        if ($skipUpdates && $branchId) {
            return !$this->isImportPreviewUpdateRow($row['mapped'] ?? [], $branchId);
        }

        return true;
    }

    private function isImportPreviewUpdateRow(array $mapped, int $branchId): bool
    {
        $name = trim((string) ($mapped['name'] ?? ''));
        if ($name === '') {
            return false;
        }

        $itemCode     = $this->sanitizeImportBarcode((string) ($mapped['item_code'] ?? ''));
        $categoryName = trim((string) ($mapped['category_name'] ?? '')) ?: 'General';
        $categoryId   = null;
        $cacheKey     = (int) $branchId . '|' . strtolower($categoryName);

        if (isset(self::$importCategoryCache[$cacheKey])) {
            $categoryId = self::$importCategoryCache[$cacheKey]->id;
        } else {
            $category = Category::where('branch_id', $branchId)
                ->whereRaw('LOWER(name) = ?', [strtolower($categoryName)])
                ->first();
            $categoryId = $category?->id;
        }

        return $this->findExistingImportProduct($itemCode, $name, $categoryId, $branchId) !== null;
    }

    private function previewRowImportAction(array $row, ?int $branchId): string
    {
        $mapped = $row['mapped'] ?? [];
        $name = trim((string) ($mapped['name'] ?? ''));
        if ($name === '') {
            return 'skip';
        }

        if (is_array(self::$importJobLookupMaps) && isset(self::$importJobLookupMaps['by_code'], self::$importJobLookupMaps['by_name'])) {
            return $this->resolveImportPreviewAction($mapped, self::$importJobLookupMaps);
        }

        if ($branchId && $this->isImportPreviewUpdateRow($mapped, $branchId)) {
            return 'update';
        }

        return 'create';
    }

    private function importIdSetFromRequest(Request $request, string $key): array
    {
        $value = $request->input($key);
        if (is_string($value)) {
            $value = json_decode($value, true);
        }
        $ids = is_array($value) ? array_values(array_unique(array_map('intval', $value))) : [];

        return array_fill_keys($ids, true);
    }

    private function importTabSelectionFromRequest(Request $request): ?array
    {
        if (!$request->has('select_all_creates')) {
            return null;
        }

        return [
            'select_all_creates' => filter_var($request->input('select_all_creates', true), FILTER_VALIDATE_BOOLEAN),
            'deselected_creates' => $this->importIdSetFromRequest($request, 'deselected_create_rows'),
            'selected_creates'   => $this->importIdSetFromRequest($request, 'selected_create_rows'),
            'select_all_updates' => filter_var($request->input('select_all_updates', false), FILTER_VALIDATE_BOOLEAN),
            'selected_updates'   => $this->importIdSetFromRequest($request, 'selected_update_rows'),
            'deselected_updates' => $this->importIdSetFromRequest($request, 'deselected_update_rows'),
        ];
    }

    private function countImportPreviewActions(string $rowsFile, int $branchId): array
    {
        $lookups     = $this->preloadImportPreviewLookups($branchId);
        $createCount = 0;
        $updateCount = 0;

        foreach ($this->iterateImportRowsFromDisk($rowsFile) as $row) {
            $mapped = $row['mapped'] ?? [];
            if (trim((string) ($mapped['name'] ?? '')) === '') {
                continue;
            }

            if ($this->resolveImportPreviewAction($mapped, $lookups) === 'update') {
                $updateCount++;
            } else {
                $createCount++;
            }
        }

        return [
            'create_count' => $createCount,
            'update_count' => $updateCount,
        ];
    }

    private function ensureImportPreviewActionCounts(string $token, array $cached): array
    {
        if (isset($cached['create_count'], $cached['update_count'])
            && is_array($cached['lookups'] ?? null)
            && isset($cached['lookups']['by_name'])
            && $this->importLookupMapsHaveProductIds($cached['lookups'])) {
            return $cached;
        }

        $branchId = (int) ($cached['branch_id'] ?? 0);
        if ($branchId <= 0 || empty($cached['rows_file'])) {
            return $cached;
        }

        $counts = $this->countImportPreviewActions((string) $cached['rows_file'], $branchId);
        $this->patchImportPreviewCache($token, $counts);

        return array_merge($cached, $counts);
    }

    private function preloadImportPreviewLookups($branchId): array
    {
        $maps = $this->buildImportProductLookupMaps($branchId);

        $categories = [];
        foreach (Category::where('branch_id', $branchId)->get(['id', 'name']) as $category) {
            $categories[strtolower(trim((string) $category->name))] = $category->id;
        }

        return [
            'by_code'    => $maps['by_code'],
            'by_name'    => $maps['by_name'],
            'categories' => $categories,
        ];
    }

    private function resolveImportPreviewAction(array $mapped, array $lookups): string
    {
        $name = trim((string) ($mapped['name'] ?? ''));
        if ($name === '') {
            return 'skip';
        }

        $itemCode = strtoupper($this->sanitizeImportBarcode((string) ($mapped['item_code'] ?? '')));
        if ($itemCode !== '' && isset($lookups['by_code'][$itemCode])) {
            return 'update';
        }

        $normalizedName = strtolower($name);
        if (isset($lookups['by_name'][$normalizedName])) {
            return 'update';
        }

        return 'create';
    }

    private function extractImportRowsFromFile($file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $rows      = [];

        if (in_array($extension, ['xlsx', 'xls'])) {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet       = $spreadsheet->getActiveSheet();
            $sheetData   = $sheet->toArray(null, true, true, false);
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            if (empty($sheetData)) {
                return ['error' => 'Excel file is empty!', 'rows' => []];
            }

            $maxCol       = 0;
            $headerRowNum = null;
            $headerMap    = [];

            foreach ($sheetData as $rowIndex => $rowValues) {
                $maxCol = max($maxCol, count($rowValues));
            }
            $maxCol = max($maxCol, 20);

            for ($r = 0; $r < min(count($sheetData), 20); $r++) {
                $cells = [];
                for ($c = 0; $c < $maxCol; $c++) {
                    $cells[$c + 1] = $this->normalizeImportHeader((string) ($sheetData[$r][$c] ?? ''));
                }

                $foundHeader = false;
                foreach ($cells as $cellVal) {
                    if (
                        str_contains($cellVal, 'item code') ||
                        str_contains($cellVal, 'item name') ||
                        str_contains($cellVal, 'item/model') ||
                        str_contains($cellVal, 'item model') ||
                        $cellVal === 'items' ||
                        str_contains($cellVal, 'sale price') ||
                        str_contains($cellVal, 'selling price') ||
                        str_contains($cellVal, 'default mrp') ||
                        str_contains($cellVal, 'opening stock') ||
                        str_contains($cellVal, 'product*') ||
                        str_contains($cellVal, 's.no')
                    ) {
                        $foundHeader = true;
                        break;
                    }
                }

                if ($foundHeader) {
                    $headerRowNum = $r + 1;
                    foreach ($cells as $colIdx => $colName) {
                        if ($colName !== '' && !isset($headerMap[$colName])) {
                            $headerMap[$colName] = $colIdx;
                        }
                    }
                    break;
                }
            }

            if (!$headerRowNum) {
                return ['error' => 'Could not find the header row in the Excel file.', 'rows' => []];
            }

            for ($r = $headerRowNum; $r < count($sheetData); $r++) {
                $rowValues = $sheetData[$r] ?? [];
                $rowCells  = [];
                for ($c = 0; $c < $maxCol; $c++) {
                    $rowCells[$c + 1] = (string) ($rowValues[$c] ?? '');
                }

                $nonEmpty = array_filter($rowCells, fn ($v) => trim((string) $v) !== '' && trim((string) $v) !== '-');
                if (empty($nonEmpty)) {
                    continue;
                }

                $mapped = $this->mapImportRow(fn (array $aliases, $default = null, bool $allowPrefix = true) => $this->importColumnValue($rowCells, $headerMap, $aliases, $default, $allowPrefix));
                $rows[] = [
                    'row_number' => $r + 1,
                    'mapped'     => $mapped,
                ];
            }

            unset($sheetData);

            return ['error' => null, 'rows' => $rows];
        }

            $path  = $file->getRealPath();
            $lines = file($path);
            if (empty($lines)) {
            return ['error' => 'CSV file is empty!', 'rows' => []];
            }

            $rawHeader = array_map('str_getcsv', [array_shift($lines)])[0];
            $headerMap = [];
            foreach ($rawHeader as $index => $col) {
            $normalised = $this->normalizeImportHeader($col);
            if ($normalised !== '' && !isset($headerMap[$normalised])) {
                $headerMap[$normalised] = $index;
            }
        }

        $data = array_map('str_getcsv', $lines);
        foreach ($data as $rowIndex => $row) {
            if (empty(array_filter($row, fn ($v) => trim((string) $v) !== ''))) {
                continue;
            }

            $mapped = $this->mapImportRow(fn (array $aliases, $default = null, bool $allowPrefix = true) => $this->importColumnValue($row, $headerMap, $aliases, $default, $allowPrefix));
            $rows[] = [
                'row_number' => $rowIndex + 2,
                'mapped'     => $mapped,
            ];
        }

        return ['error' => null, 'rows' => $rows];
    }

    private function buildImportPreviewItem(array $mapped, int $rowNumber, array $lookups): array
    {
        $name         = trim((string) ($mapped['name'] ?? ''));
        $categoryName = trim((string) ($mapped['category_name'] ?? ''));
        $itemCode     = $this->sanitizeImportBarcode((string) ($mapped['item_code'] ?? ''));
        $valid        = $name !== '';

        if ($categoryName === '') {
            $categoryName = 'General';
        }

        $descriptionParsed = $this->parseImportDescriptionWholesale($mapped['description'] ?? null);
        $importAction      = $valid ? $this->resolveImportPreviewAction($mapped, $lookups) : 'skip';

        return [
            'row_number'      => $rowNumber,
            'valid'           => $valid,
            'import_action'   => $importAction,
            'name'            => $name,
            'item_code'       => $itemCode,
            'category_name'   => $categoryName,
            'brand_name'      => trim((string) ($mapped['brand_name'] ?? '')),
            'description'     => $descriptionParsed['description'],
            'description_raw' => trim((string) ($mapped['description'] ?? '')),
            'wholesale_price' => $descriptionParsed['wholesale_price'],
            'price'           => $this->parseImportMoney($mapped['price'] ?? null),
            'mrp'             => $this->parseImportMoney($mapped['mrp'] ?? null),
            'purchase_price'  => $this->parseImportMoney($mapped['purchase_price'] ?? null),
        ];
    }

    public function previewImportProducts(Request $request)
    {
        $user     = Auth::guard('api')->user();
        $branchId = $this->resolveImportBranchId($request, $user);

        $request->validate(
            $this->importFileValidationRules(),
            $this->importFileValidationMessages()
        );

        $file = $request->file('csv_file');
        $token = ProductImportPreviewStore::createPendingFromUpload(
            $file,
            (int) $user->id,
            (int) $branchId
        );

        return response()->json([
            'status'       => true,
            'token'        => $token,
            'redirect_url' => url('/import-product/preview/' . $token),
        ]);
    }

    public function getImportPreviewStatus(Request $request, string $token)
    {
        $user   = Auth::guard('api')->user();
        $cached = $this->getImportPreviewSession($token, $user->id);
        if (!$cached) {
            return response()->json([
                'status'  => false,
                'message' => 'Import preview expired. Please upload the file again.',
            ], 404);
        }

        return response()->json([
            'status'         => true,
            'process_status' => (string) ($cached['status'] ?? 'pending'),
            'total_count'    => (int) ($cached['total_count'] ?? 0),
            'valid_count'    => (int) ($cached['valid_count'] ?? 0),
            'processed_rows' => (int) ($cached['processed_rows'] ?? 0),
            'file_name'      => (string) ($cached['file_name'] ?? ''),
            'error'          => $cached['error'] ?? null,
        ]);
    }

    public function processImportPreview(Request $request, string $token)
    {
        $this->prepareImportRuntimeLimits();

        $user   = Auth::guard('api')->user();
        $cached = $this->getImportPreviewSession($token, $user->id);
        if (!$cached) {
            return response()->json([
                'status'  => false,
                'message' => 'Import preview expired. Please upload the file again.',
            ], 404);
        }

        $status = (string) ($cached['status'] ?? 'pending');
        if ($status === 'complete') {
            $cached = $this->ensureImportPreviewActionCounts($token, $cached);

            return response()->json([
                'status'         => true,
                'process_status' => 'complete',
                'total_count'    => (int) ($cached['total_count'] ?? 0),
                'valid_count'    => (int) ($cached['valid_count'] ?? 0),
                'create_count'   => (int) ($cached['create_count'] ?? 0),
                'update_count'   => (int) ($cached['update_count'] ?? 0),
            ]);
        }

        if ($status === 'failed') {
            $retryFile = (string) ($cached['upload_file'] ?? '');
            if ($retryFile !== '' && Storage::exists($retryFile)) {
                $this->patchImportPreviewCache($token, [
                    'status'         => 'pending',
                    'error'          => null,
                    'processed_rows' => 0,
                    'total_count'    => 0,
                    'valid_count'    => 0,
                ]);
                $cached['status'] = 'pending';
                $status = 'pending';
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => (string) ($cached['error'] ?? 'Unable to read import file.'),
                ]);
            }
        }

        if ($status === 'processing') {
            return response()->json([
                'status'         => true,
                'process_status' => 'processing',
                'total_count'    => (int) ($cached['total_count'] ?? 0),
                'valid_count'    => (int) ($cached['valid_count'] ?? 0),
                'processed_rows' => (int) ($cached['processed_rows'] ?? 0),
            ]);
        }

        $uploadFile = (string) ($cached['upload_file'] ?? '');
        if ($uploadFile === '' || !Storage::exists($uploadFile)) {
            return response()->json([
                'status'  => false,
                'message' => 'Uploaded import file not found. Please upload again.',
            ]);
        }

        $lockKey = 'product_import_preview_lock_' . $token;
        if (!Cache::add($lockKey, 1, now()->addMinutes(30))) {
            return response()->json([
                'status'         => true,
                'process_status' => 'processing',
                'total_count'    => (int) ($cached['total_count'] ?? 0),
                'valid_count'    => (int) ($cached['valid_count'] ?? 0),
                'processed_rows' => (int) ($cached['processed_rows'] ?? 0),
            ]);
        }

        $this->patchImportPreviewCache($token, ['status' => 'processing', 'error' => null]);

        dispatch(function () use ($token, $lockKey) {
            $controller = app(self::class);
            try {
                $controller->runImportPreviewProcessing($token);
            } catch (\Throwable $e) {
                report($e);
                $latest = Cache::get($controller->importPreviewCacheKey($token));
                $processed = is_array($latest) ? (int) ($latest['processed_rows'] ?? 0) : 0;
                if ($processed > 0) {
                    $controller->patchImportPreviewCache($token, [
                        'status'         => 'complete',
                        'total_count'    => $processed,
                        'processed_rows' => $processed,
                        'valid_count'    => (int) ($latest['valid_count'] ?? 0),
                        'error'          => null,
                    ]);
                        } else {
                    $controller->patchImportPreviewCache($token, [
                        'status' => 'failed',
                        'error'  => 'Unable to read import file. Please try again.',
                    ]);
                }
            } finally {
                Cache::forget($lockKey);
            }
        })->afterResponse();

        return response()->json([
            'status'         => true,
            'process_status' => 'processing',
            'total_count'    => 0,
            'valid_count'    => 0,
            'processed_rows' => 0,
        ]);
    }

    public function runImportPreviewProcessing(string $token): void
    {
        $this->prepareImportRuntimeLimits();

        $cached = Cache::get($this->importPreviewCacheKey($token));
        if (!is_array($cached)) {
            return;
        }

        $uploadFile = (string) ($cached['upload_file'] ?? '');
        if ($uploadFile === '' || !Storage::exists($uploadFile)) {
            $this->patchImportPreviewCache($token, [
                'status' => 'failed',
                'error'  => 'Uploaded import file not found. Please upload again.',
            ]);

            return;
        }

        $extension  = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        if (in_array($extension, ['xlsx', 'csv'], true) && !class_exists(\OpenSpout\Reader\XLSX\Reader::class)) {
            $this->patchImportPreviewCache($token, [
                'status' => 'failed',
                'error'  => 'Server is missing the OpenSpout library. Run composer install --no-dev on the server, then try again.',
            ]);

            return;
        }

        $sourcePath = Storage::path($uploadFile);
        $jsonlPath  = Storage::path((string) $cached['rows_file']);
        $parser     = new ProductImportStreamParser(new ProductImportRowMapper());

        $result = $parser->streamToJsonl(
            $sourcePath,
            $extension,
            $jsonlPath,
            fn (array $rowCells, array $headerMap) => $this->mapImportRow(
                fn (array $aliases, $default = null, bool $allowPrefix = true) => $this->importColumnValue(
                    $rowCells,
                    $headerMap,
                    $aliases,
                    $default,
                    $allowPrefix
                )
            ),
            fn (array $mapped) => trim((string) ($mapped['name'] ?? '')) !== '',
            function (int $processed, int $valid) use ($token) {
                if ($processed % 1000 !== 0 && $processed > 0) {
                    return;
                }
                $this->patchImportPreviewCache($token, [
                    'processed_rows' => $processed,
                    'total_count'    => $processed,
                    'valid_count'    => $valid,
                ]);
            }
        );

        if ($result['error']) {
            $this->patchImportPreviewCache($token, [
                'status' => 'failed',
                'error'  => $result['error'],
            ]);

            return;
        }

        if ($result['total_count'] === 0) {
            $this->patchImportPreviewCache($token, [
                'status' => 'failed',
                'error'  => 'File is empty or contains no valid product data.',
            ]);

            return;
        }

        $this->patchImportPreviewCache($token, [
            'status'         => 'complete',
            'total_count'    => $result['total_count'],
            'valid_count'    => $result['valid_count'],
            'processed_rows' => $result['total_count'],
            'lookups'        => null,
            'error'          => null,
        ]);

        Storage::delete($uploadFile);
    }

    public function getImportPreviewRows(Request $request, string $token)
    {
        $user = Auth::guard('api')->user();
        $cached = $this->getImportPreviewSession($token, $user->id);
        if (!$cached) {
            return response()->json([
                'status'  => false,
                'message' => 'Import preview expired. Please upload the file again.',
            ], 404);
        }

        if (($cached['status'] ?? '') !== 'complete') {
            return response()->json([
                'status'  => false,
                'message' => 'Import file is still being processed. Please wait.',
                'process_status' => (string) ($cached['status'] ?? 'pending'),
            ], 409);
        }

        $page    = max(1, (int) $request->query('page', 1));
        $perPage = min(200, max(25, (int) $request->query('per_page', 100)));
        $search  = trim((string) $request->query('search', ''));
        $actionFilter = strtolower(trim((string) $request->query('action', '')));
        if (!in_array($actionFilter, ['create', 'update'], true)) {
            $actionFilter = '';
        }

        $lookups = is_array($cached['lookups'] ?? null) ? $cached['lookups'] : null;
        if ($lookups === null || !isset($lookups['by_name']) || !$this->importLookupMapsHaveProductIds($lookups)) {
            $branchId = (int) ($cached['branch_id'] ?? 0);
            $lookups  = $this->preloadImportPreviewLookups($branchId);
            $this->patchImportPreviewCache($token, [
                'lookups'      => $lookups,
                'create_count' => null,
                'update_count' => null,
            ]);
            unset($cached['create_count'], $cached['update_count']);
        }

        $cached = $this->ensureImportPreviewActionCounts($token, $cached);

        if ($actionFilter !== '') {
            $filtered    = $this->filterImportRowsPage((string) $cached['rows_file'], $search, $actionFilter, $page, $perPage, $lookups);
            $rows        = $filtered['rows'];
            $resultTotal = (int) $filtered['total_matches'];
        } elseif ($search !== '') {
            $searchResult = $this->searchImportRowsPage((string) $cached['rows_file'], $search, $page, $perPage);
            $rows         = $searchResult['rows'];
            $resultTotal  = (int) $searchResult['total_matches'];
                    } else {
            $rows        = $this->readImportRowsPage((string) $cached['rows_file'], $page, $perPage);
            $resultTotal = (int) ($cached['total_count'] ?? 0);
        }

        $items = [];
        foreach ($rows as $row) {
            $items[] = $this->buildImportPreviewItem($row['mapped'] ?? [], (int) ($row['row_number'] ?? 0), $lookups);
        }

        $totalCount = (int) ($cached['total_count'] ?? 0);
        $lastPage   = max(1, (int) ceil($resultTotal / $perPage));

        return response()->json([
            'status'         => true,
            'items'          => $items,
            'page'           => $page,
            'per_page'       => $perPage,
            'total_count'    => $totalCount,
            'filtered_count' => $resultTotal,
            'valid_count'    => (int) ($cached['valid_count'] ?? 0),
            'create_count'   => (int) ($cached['create_count'] ?? 0),
            'update_count'   => (int) ($cached['update_count'] ?? 0),
            'last_page'      => $lastPage,
            'search'         => $search,
            'file_name'      => (string) ($cached['file_name'] ?? ''),
        ]);
    }

    public function importProducts(Request $request)
    {
        $this->prepareImportRuntimeLimits();

        $user = Auth::guard('api')->user();

        $selectAllValid = filter_var($request->input('select_all_valid', false), FILTER_VALIDATE_BOOLEAN);
        $deselectedRows = $request->input('deselected_rows');
        if (is_string($deselectedRows)) {
            $deselectedRows = json_decode($deselectedRows, true);
        }
        $deselectedRows = is_array($deselectedRows)
            ? array_values(array_unique(array_map('intval', $deselectedRows)))
            : [];

        $selectedRows = $request->input('selected_rows');
        if (is_string($selectedRows)) {
            $selectedRows = json_decode($selectedRows, true);
        }
        $selectedRows = is_array($selectedRows)
            ? array_values(array_unique(array_map('intval', $selectedRows)))
            : [];

        $importToken = trim((string) $request->input('import_token', ''));
        $skipUpdates = filter_var($request->input('skip_updates', false), FILTER_VALIDATE_BOOLEAN);
        $tabSelection = $this->importTabSelectionFromRequest($request);
        if ($importToken !== '') {
            $cached = $this->getImportPreviewSession($importToken, $user->id);
            if (is_array($tabSelection)) {
                $cached = is_array($cached) ? $this->ensureImportPreviewActionCounts($importToken, $cached) : $cached;
                $createSelected = !empty($tabSelection['select_all_creates'])
                    ? max(0, (int) ($cached['create_count'] ?? 0) - count($tabSelection['deselected_creates']))
                    : count($tabSelection['selected_creates']);
                $updateSelected = !empty($tabSelection['select_all_updates'])
                    ? max(0, (int) ($cached['update_count'] ?? 0) - count($tabSelection['deselected_updates']))
                    : count($tabSelection['selected_updates']);
                if (($createSelected + $updateSelected) <= 0) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Please select at least one row to import.',
                    ]);
                }

                return $this->importProductsFromPreviewToken($request, $user, $importToken, $selectedRows, $selectAllValid, $deselectedRows, $skipUpdates, $tabSelection);
            }
            if ($selectAllValid) {
                if ($skipUpdates) {
                    $cached = is_array($cached) ? $this->ensureImportPreviewActionCounts($importToken, $cached) : $cached;
                    $importableCount = (int) ($cached['create_count'] ?? 0);
                } else {
                    $importableCount = (int) ($cached['valid_count'] ?? 0);
                }
                if (($importableCount - count($deselectedRows)) <= 0) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Please select at least one row to import.',
                    ]);
                }
            } elseif ($selectedRows === []) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Please select at least one row to import.',
                ]);
            }

            return $this->importProductsFromPreviewToken($request, $user, $importToken, $selectedRows, $selectAllValid, $deselectedRows, $skipUpdates);
        }

        if ($selectedRows === []) {
            return response()->json([
                'status'  => false,
                'message' => 'Please select at least one row to import.',
            ]);
        }

        $branchId = $this->resolveImportBranchId($request, $user);

        $request->validate(
            $this->importFileValidationRules(),
            $this->importFileValidationMessages()
        );

        $parsed = $this->extractImportRowsFromFile($request->file('csv_file'));
        if ($parsed['error']) {
            return response()->json(['status' => false, 'message' => $parsed['error']]);
        }

        if (empty($parsed['rows'])) {
            return response()->json([
                'status'  => false,
                'message' => 'File is empty or contains no valid product data. Use Item code as barcode and ITEMS as product name.',
            ]);
        }

        return $this->runImportRows($parsed['rows'], $branchId, $user->id, $selectedRows, false, []);
    }

    private function importProductsFromPreviewToken(Request $request, $user, string $token, array $selectedRows, bool $selectAllValid, array $deselectedRows, bool $skipUpdates = false, ?array $tabSelection = null)
    {
        $cached = $this->getImportPreviewSession($token, $user->id);
        if (!$cached || empty($cached['rows_file'])) {
            return response()->json([
                'status'  => false,
                'message' => 'Import preview expired. Please upload the file again and open preview.',
            ]);
        }

        $branchId = (int) ($cached['branch_id'] ?? 0);
        if ($branchId <= 0) {
            $branchId = $this->resolveImportBranchId($request, $user);
        }

        $batchSize  = min(50, max(10, (int) $request->input('batch_size', 25)));
        $fileOffset = max(0, (int) $request->input('file_offset', 0));
        $jobKey     = $this->importJobCacheKey($token);
        $deselectedMap = array_fill_keys($deselectedRows, true);

        if ($fileOffset === 0 && !Cache::has($jobKey)) {
            Cache::put($jobKey, [
                'created'     => 0,
                'updated'     => 0,
                'skipped'     => [],
                'next_offset' => 0,
            ], now()->addHours(3));
            $this->resetImportRuntimeCaches();
        }

        $this->hydrateImportJobContext($jobKey, $branchId, $token);

        $job = Cache::get($jobKey, [
            'created'     => 0,
            'updated'     => 0,
            'skipped'     => [],
            'next_offset' => 0,
        ]);

        if ($fileOffset === 0 && (int) ($job['next_offset'] ?? 0) > 0) {
            $fileOffset = (int) $job['next_offset'];
        }

        Product::$skipBarcodeHistory = true;
        try {
            $batchResult = $this->runImportBatchFromDisk(
                (string) $cached['rows_file'],
                $fileOffset,
                $batchSize,
                $branchId,
                $user->id,
                $selectedRows,
                $selectAllValid,
                $deselectedMap,
                $skipUpdates,
                $tabSelection
            );
        } catch (\Exception $e) {
            Product::$skipBarcodeHistory = false;
            $this->resetImportRuntimeCaches();

            return response()->json([
                'status'  => false,
                'message' => 'Import failed: ' . $e->getMessage(),
            ]);
        }

        $job['created'] += (int) ($batchResult['inserted'] ?? 0);
        $job['updated'] += count($batchResult['updated'] ?? []);
        $job['skipped'] = array_merge($job['skipped'], $batchResult['skipped'] ?? []);
        $job['next_offset'] = (int) ($batchResult['next_offset'] ?? $fileOffset);

        if (!empty($batchResult['done'])) {
            Product::$skipBarcodeHistory = false;
            $this->resetImportRuntimeCaches();
            Cache::forget($jobKey);
            Cache::forget($this->importLookupCacheKey($jobKey));
            $this->clearImportPreviewSession($token);

            if ($job['created'] > 0 || $job['updated'] > 0) {
                $parts = [];
                if ($job['created'] > 0) {
                    $parts[] = $job['created'] . ' new product(s) created';
                }
                if ($job['updated'] > 0) {
                    $parts[] = $job['updated'] . ' existing product(s) updated';
                }

                return response()->json([
                    'status'        => true,
                    'done'          => true,
                    'message'       => implode(', ', $parts) . '.',
                    'created_count' => $job['created'],
                    'updated_count' => $job['updated'],
                    'skipped_rows'  => $job['skipped'],
                ]);
            }

            return response()->json([
                'status'  => false,
                'done'    => true,
                'message' => 'No products were imported. Please select at least one valid row.',
            ]);
        }

        Cache::put($jobKey, $job, now()->addHours(3));

        return response()->json([
            'status'         => true,
            'done'           => false,
            'next_offset'    => (int) ($batchResult['next_offset'] ?? $fileOffset),
            'batch_imported' => (int) ($batchResult['batch_imported'] ?? 0),
            'created_count'  => (int) $job['created'],
            'updated_count'  => (int) $job['updated'],
            'message'        => 'Imported ' . ($job['created'] + $job['updated']) . ' product(s) so far...',
        ]);
    }

    private function runImportBatchFromDisk(
        string $rowsFile,
        int $startByte,
        int $batchSize,
        $branchId,
        $userId,
        array $selectedRows,
        bool $selectAllValid,
        array $deselectedMap,
        bool $skipUpdates = false,
        ?array $tabSelection = null
    ): array {
        $insertedCount     = 0;
        $updatedSKUs       = [];
        $skippedRows       = [];
        $importedThisBatch = 0;
        $done              = true;
        $nextByte          = $startByte;

        $path = Storage::path($rowsFile);
        if (!is_file($path)) {
            return [
                'inserted'       => 0,
                'updated'        => [],
                'skipped'        => [],
                'next_offset'    => 0,
                'done'           => true,
                'batch_imported' => 0,
            ];
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new \RuntimeException('Unable to read import rows file.');
        }

        if ($startByte > 0) {
            fseek($handle, $startByte);
        }

        DB::beginTransaction();
        try {
            while (($line = fgets($handle)) !== false) {
                $decoded = json_decode(trim($line), true);
                if (!is_array($decoded)) {
                    continue;
                }

                if (!$this->shouldImportPreviewRow($decoded, $selectAllValid, $selectedRows, $deselectedMap, $skipUpdates, (int) $branchId, $tabSelection)) {
                    continue;
                }

                $mapped = $decoded['mapped'] ?? [];
                $result = $this->upsertImportedProduct($mapped, $branchId, $userId);
                if ($result === 'created') {
                        $insertedCount++;
                } elseif ($result === 'updated') {
                    $updatedSKUs[] = $mapped['item_code'] ?: $mapped['name'];
                } else {
                    $skippedRows[] = (int) ($decoded['row_number'] ?? 0);
                    }

                $importedThisBatch++;
                if ($importedThisBatch >= $batchSize) {
                    break;
                }
            }

            $nextByte = (int) ftell($handle);
            $done     = feof($handle);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            fclose($handle);

            throw $e;
        }

        fclose($handle);

        return [
            'inserted'       => $insertedCount,
            'updated'        => $updatedSKUs,
            'skipped'        => $skippedRows,
            'next_offset'    => $nextByte,
            'done'           => $done,
            'batch_imported' => $importedThisBatch,
        ];
    }

    private function processImportChunk(array $rows, $branchId, $userId, array $selectedRows, bool $selectAllValid, array $deselectedMap, bool $skipUpdates = false): array
    {
        $insertedCount = 0;
        $updatedSKUs   = [];
        $skippedRows   = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                if (!$this->shouldImportPreviewRow($row, $selectAllValid, $selectedRows, $deselectedMap, $skipUpdates, (int) $branchId)) {
                    continue;
                }

                $mapped = $row['mapped'] ?? [];
                $result = $this->upsertImportedProduct($mapped, $branchId, $userId);
                if ($result === 'created') {
                    $insertedCount++;
                } elseif ($result === 'updated') {
                    $updatedSKUs[] = $mapped['item_code'] ?: $mapped['name'];
                } else {
                    $skippedRows[] = (int) ($row['row_number'] ?? 0);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

        return [
            'inserted' => $insertedCount,
            'updated'  => $updatedSKUs,
            'skipped'  => $skippedRows,
        ];
    }

    private function runImportRowsFromDisk(string $rowsFile, $branchId, $userId, array $selectedRows, bool $selectAllValid, array $deselectedMap)
    {
        $insertedCount = 0;
        $updatedSKUs   = [];
        $skippedRows   = [];
        $chunk         = [];
        $chunkSize     = 150;

        Product::$skipBarcodeHistory = true;
        try {
            foreach ($this->iterateImportRowsFromDisk($rowsFile) as $row) {
                $chunk[] = $row;
                if (count($chunk) < $chunkSize) {
                    continue;
                }

                $result = $this->processImportChunk($chunk, $branchId, $userId, $selectedRows, $selectAllValid, $deselectedMap);
                $insertedCount += $result['inserted'];
                $updatedSKUs = array_merge($updatedSKUs, $result['updated']);
                $skippedRows = array_merge($skippedRows, $result['skipped']);
                $chunk = [];
            }

            if ($chunk !== []) {
                $result = $this->processImportChunk($chunk, $branchId, $userId, $selectedRows, $selectAllValid, $deselectedMap);
                $insertedCount += $result['inserted'];
                $updatedSKUs = array_merge($updatedSKUs, $result['updated']);
                $skippedRows = array_merge($skippedRows, $result['skipped']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Import failed: ' . $e->getMessage()]);
        } finally {
            Product::$skipBarcodeHistory = false;
        }

        if ($insertedCount > 0 || count($updatedSKUs) > 0) {
            $parts = [];
            if ($insertedCount > 0) {
                $parts[] = "{$insertedCount} new product(s) created";
            }
            if (count($updatedSKUs) > 0) {
                $parts[] = count($updatedSKUs) . ' existing product(s) updated';
            }

            return response()->json([
                'status'        => true,
                'message'       => implode(', ', $parts) . '.',
                'created_count' => $insertedCount,
                'updated_skus'  => $updatedSKUs,
                'skipped_rows'  => $skippedRows,
            ]);
        }

            return response()->json([
            'status'  => false,
            'message' => 'No products were imported. Please select at least one valid row.',
            ]);
        }

    private function runImportRows(array $rows, $branchId, $userId, ?array $selectedRows, bool $selectAllValid = false, array $deselectedMap = [])
    {
        if (empty($rows)) {
        return response()->json([
            'status'  => false,
                'message' => 'File is empty or contains no valid product data. Use Item code as barcode and ITEMS as product name.',
            ]);
        }

        $insertedCount = 0;
        $updatedSKUs   = [];
        $skippedRows   = [];
        $chunk         = [];
        $chunkSize     = 150;

        Product::$skipBarcodeHistory = true;
        try {
            foreach ($rows as $row) {
                $chunk[] = $row;
                if (count($chunk) < $chunkSize) {
                    continue;
                }

                $result = $this->processImportChunk($chunk, $branchId, $userId, $selectedRows ?? [], $selectAllValid, $deselectedMap);
                $insertedCount += $result['inserted'];
                $updatedSKUs = array_merge($updatedSKUs, $result['updated']);
                $skippedRows = array_merge($skippedRows, $result['skipped']);
                $chunk = [];
            }

            if ($chunk !== []) {
                $result = $this->processImportChunk($chunk, $branchId, $userId, $selectedRows ?? [], $selectAllValid, $deselectedMap);
                $insertedCount += $result['inserted'];
                $updatedSKUs = array_merge($updatedSKUs, $result['updated']);
                $skippedRows = array_merge($skippedRows, $result['skipped']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Import failed: ' . $e->getMessage()]);
        } finally {
            Product::$skipBarcodeHistory = false;
        }

        if ($insertedCount > 0 || count($updatedSKUs) > 0) {
            $parts = [];
            if ($insertedCount > 0) {
                $parts[] = "{$insertedCount} new product(s) created";
            }
            if (count($updatedSKUs) > 0) {
                $parts[] = count($updatedSKUs) . ' existing product(s) updated';
            }

            return response()->json([
                'status'        => true,
                'message'       => implode(', ', $parts) . '.',
                'created_count' => $insertedCount,
                'updated_skus'  => $updatedSKUs,
                'skipped_rows'  => $skippedRows,
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'No products were imported. Please select at least one valid row.',
        ]);
    }

    public function getProductDetails($id)
    {
        $product = Product::with(['category', 'brand', 'unit', 'variants'])->findOrFail($id);

        $basePath = rtrim((string) (env('IMAGE_PATH') ?? env('ImagePath') ?? env('APP_URL', url('/'))), '/');
        $rawImages = json_decode($product->images ?? '[]', true);
        $rawImages = is_array($rawImages) ? $rawImages : [];
        $imageUrls = [];

        foreach ($rawImages as $image) {
            if (!is_string($image) || trim($image) === '') {
                continue;
            }

            $normalizedImage = ltrim($image, '/');

            if (str_starts_with($normalizedImage, 'http://') || str_starts_with($normalizedImage, 'https://')) {
                $imageUrls[] = $normalizedImage;
            } elseif (str_starts_with($normalizedImage, 'admin/assets/')) {
                $imageUrls[] = $basePath . '/' . $normalizedImage;
            } else {
                $imageUrls[] = $basePath . '/storage/' . $normalizedImage;
            }
        }

        if (empty($imageUrls)) {
            $imageUrls[] = $basePath . '/admin/assets/img/product/noimage.png';
        }

        // Fetch currency settings
        $settings         = DB::table('settings')->first();
        $currencySymbol   = $settings->currency_symbol ?? 'â‚¹';
        $currencyPosition = $settings->currency_position ?? 'left';

        $barcodeGenerator = new DNS1D();
        $barcodeHtml      = '';

        if (! empty($product->barcode)) {
            $barcodes = array_map('trim', explode(',', $product->barcode));
            $details = is_string($product->barcode_details) ? json_decode($product->barcode_details, true) : ($product->barcode_details ?? []);
            foreach ($barcodes as $code) {
                if (!empty($code)) {
                    $qty = '';
                    if (isset($details[$code]) && isset($details[$code]['quantity']) && $details[$code]['quantity'] !== '') {
                        $qty = ' (' . $details[$code]['quantity'] . ')';
                    }
                    $barcodeHtml .= '<div class="single-barcode-history" data-barcode="'.htmlspecialchars($code).'" style="text-align: center; cursor: pointer;" title="Click to view Barcode History">' . 
                                    $barcodeGenerator->getBarcodeSVG($code, 'C128', 2, 60, 'black', false) . 
                                    '<div style="font-size: 13px; margin-top: 3px; font-weight: 500; letter-spacing: 1px;">' . htmlspecialchars($code) . '</div>' .
                                    '</div>';
                }
            }
        }

        return response()->json([
            'product'          => $product,
            'image_urls'       => $imageUrls,
            'currencySymbol'   => $currencySymbol,
            'currencyPosition' => $currencyPosition,
            'barcode_html'     => $barcodeHtml,
        ]);
    }

    public function exportAllBarcodesPdf(Request $request)
    {
        $user = Auth::guard('api')->user();

        $branchId = match (strtolower($user->role)) {
            'admin'     => $request->selectedSubAdminId ?: $user->id,
            'sub-admin' => $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        $products = Product::with(['category', 'brand', 'unit', 'variants'])
            ->select(['id', 'name', 'SKU', 'barcode', 'barcode_details', 'price', 'category_id', 'brand_id', 'unit_id', 'mrp', 'batch_no', 'label_code', 'label_reference', 'quantity', 'specific_qty'])
            ->where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->brand_id,    fn($q) => $q->where('brand_id',    $request->brand_id))
            ->tap(fn($q) => $this->applyProductDateFilter($q, $request))
            ->latest('id')
            ->get();

        $settings = $this->resolveBarcodeSettings($branchId);
        [$barcodeLayout, $barcodeDetailOption] = $this->resolveBarcodeLayout($settings);
        $barcodeType = $settings->barcode_type ?? 'barcode';

        $generator     = new DNS1D();
        $barcodeImages = [];
        $barcodeItems  = collect();

        foreach ($products as $product) {
            if ($product->variants && $product->variants->isNotEmpty()) {
                foreach ($product->variants as $variant) {
                    if (!empty($variant->barcode)) {
                        $variantBarcodes = array_filter(array_map('trim', explode(',', $variant->barcode)));
                        foreach ($variantBarcodes as $singleBarcode) {
                            $item = $this->buildBarcodeLabelData($product, $variant, $settings, $barcodeLayout);
                            $item['barcode'] = $singleBarcode;
                            $item['barcode_image'] = $this->generateBarcodeImage($generator, $item['barcode'], $barcodeLayout, $barcodeDetailOption, $barcodeType);
                            $item['name'] = $item['display_name'];
                            $item['value'] = $item['barcode'];
                            $item['img'] = $item['barcode_image'];
                            
                            $qty = '';
                            $details = is_string($product->barcode_details) ? json_decode($product->barcode_details, true) : ($product->barcode_details ?? []);
                            if (isset($details[$singleBarcode])) {
                                if (isset($details[$singleBarcode]['quantity']) && $details[$singleBarcode]['quantity'] !== '') $qty = $details[$singleBarcode]['quantity'];
                                if (isset($details[$singleBarcode]['batch_no']) && $details[$singleBarcode]['batch_no'] !== '') $item['batch_no'] = $details[$singleBarcode]['batch_no'];
                                if (isset($details[$singleBarcode]['label_code']) && $details[$singleBarcode]['label_code'] !== '') $item['label_code'] = $details[$singleBarcode]['label_code'];
                                if (isset($details[$singleBarcode]['label_reference']) && $details[$singleBarcode]['label_reference'] !== '') $item['label_reference'] = $details[$singleBarcode]['label_reference'];
                                if (isset($details[$singleBarcode]['price']) && $details[$singleBarcode]['price'] !== '') $item['price'] = $details[$singleBarcode]['price'];
                                if (isset($details[$singleBarcode]['mrp']) && $details[$singleBarcode]['mrp'] !== '') $item['mrp'] = $details[$singleBarcode]['mrp'];
                            }
                            $item['barcode_display'] = $singleBarcode;
                            $item['specific_qty'] = $qty;
                            
                            $barcodeItems->push((object) $item);
                            $barcodeImages[$item['id']] = $item['barcode_image'];
                        }
                    }
                }
            } else {
                if (!empty($product->barcode)) {
                    $mainBarcodes = array_filter(array_map('trim', explode(',', $product->barcode)));
                    foreach ($mainBarcodes as $singleBarcode) {
                        $item = $this->buildBarcodeLabelData($product, null, $settings, $barcodeLayout);
                        $item['barcode'] = $singleBarcode;
                        $item['barcode_image'] = $this->generateBarcodeImage($generator, $item['barcode'], $barcodeLayout, $barcodeDetailOption, $barcodeType);
                        $item['name'] = $item['display_name'];
                        $item['value'] = $item['barcode'];
                        $item['img'] = $item['barcode_image'];
                        
                        $qty = '';
                        $details = is_string($product->barcode_details) ? json_decode($product->barcode_details, true) : ($product->barcode_details ?? []);
                        if (isset($details[$singleBarcode])) {
                            if (isset($details[$singleBarcode]['quantity']) && $details[$singleBarcode]['quantity'] !== '') $qty = $details[$singleBarcode]['quantity'];
                            if (isset($details[$singleBarcode]['batch_no']) && $details[$singleBarcode]['batch_no'] !== '') $item['batch_no'] = $details[$singleBarcode]['batch_no'];
                            if (isset($details[$singleBarcode]['label_code']) && $details[$singleBarcode]['label_code'] !== '') $item['label_code'] = $details[$singleBarcode]['label_code'];
                            if (isset($details[$singleBarcode]['label_reference']) && $details[$singleBarcode]['label_reference'] !== '') $item['label_reference'] = $details[$singleBarcode]['label_reference'];
                            if (isset($details[$singleBarcode]['price']) && $details[$singleBarcode]['price'] !== '') $item['price'] = $details[$singleBarcode]['price'];
                            if (isset($details[$singleBarcode]['mrp']) && $details[$singleBarcode]['mrp'] !== '') $item['mrp'] = $details[$singleBarcode]['mrp'];
                        }
                        $item['barcode_display'] = $singleBarcode;
                        $item['specific_qty'] = $qty;

                        $barcodeItems->push((object) $item);
                        $barcodeImages[$item['id']] = $item['barcode_image'];
                    }
                }
            }
        }

        $view = 'product.all_barcodes_pdf';

        $pdf = Pdf::loadView($view, [
            'products'      => $barcodeItems,
            'barcodeImages' => $barcodeImages,
            'settings'      => $settings,
            'barcodeLayout' => $barcodeLayout,
            'barcodeDetailOption' => $barcodeDetailOption,
            'barcodeType'   => $barcodeType,
        ])->setPaper('A4', 'portrait');

        $fileName     = 'All_Barcodes_' . now()->format('Ymd_His') . '.pdf';
        $folder       = 'product-barcodes';
        $relativePath = "{$folder}/{$fileName}";

        Storage::disk('public')->makeDirectory($folder);
        Storage::disk('public')->put($relativePath, $pdf->output());

        $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

        return response()->json([
            'status'    => true,
            'message'   => 'All barcodes PDF generated successfully.',
            'file_url'  => $fileUrl,
            'file_name' => $fileName,
        ]);
    }

    public function downloadBarcodePdf($id)
    {
        $user    = Auth::guard('api')->user();
        $product = Product::with(['category', 'brand', 'unit', 'variants'])->findOrFail($id);

        $branchId = match (strtolower($user->role)) {
            'admin'     => $user->id,
            'sub-admin' => $user->id,
            'staff'     => $user->branch_id,
            default     => $user->id,
        };

        $settings = $this->resolveBarcodeSettings($branchId);
        [$barcodeLayout, $barcodeDetailOption] = $this->resolveBarcodeLayout($settings);
        $barcodeType = $settings->barcode_type ?? 'barcode';

        $currencySymbol = trim(
            html_entity_decode($settings->currency_symbol ?? '₹', ENT_QUOTES | ENT_HTML5, 'UTF-8')
        );
        $currencyPosition = $settings->currency_position ?? 'left';

        $generator  = new DNS1D();
        $barcodes   = [];
        $labelSize  = $this->parseLabelSize(request('label_size', $product->label_size));
        if ((int) $labelSize['width'] === 35 && (int) $labelSize['height'] === 25) {
            $labelSize['cols'] = 3;
            $labelSize['key'] = '35x25x3';
        }

        if ($product->variants && $product->variants->isNotEmpty()) {
            foreach ($product->variants as $variant) {
                if (!empty($variant->barcode)) {
                    $variantBarcodes = array_filter(array_map('trim', explode(',', $variant->barcode)));
                    foreach ($variantBarcodes as $singleBarcode) {
                        $item = $this->buildBarcodeLabelData($product, $variant, $settings, $barcodeLayout);
                        $item['barcode'] = $singleBarcode;
                        $item['barcode_image'] = $this->generateBarcodeImage($generator, $item['barcode'], $barcodeLayout, $barcodeDetailOption, $barcodeType, $labelSize['width'], $labelSize['height']);
                        $item['name'] = $item['display_name'];
                        $item['value'] = $item['barcode'];
                        $item['img'] = $item['barcode_image'];
                        
                        $qty = '';
                        $details = is_string($product->barcode_details) ? json_decode($product->barcode_details, true) : ($product->barcode_details ?? []);
                        if (isset($details[$singleBarcode])) {
                            if (isset($details[$singleBarcode]['specific_qty']) && $details[$singleBarcode]['specific_qty'] !== '') $qty = $details[$singleBarcode]['specific_qty'];
                            if (isset($details[$singleBarcode]['batch_no']) && $details[$singleBarcode]['batch_no'] !== '') $item['batch_no'] = $details[$singleBarcode]['batch_no'];
                            if (isset($details[$singleBarcode]['label_code']) && $details[$singleBarcode]['label_code'] !== '') $item['label_code'] = $details[$singleBarcode]['label_code'];
                            if (isset($details[$singleBarcode]['label_reference']) && $details[$singleBarcode]['label_reference'] !== '') $item['label_reference'] = $details[$singleBarcode]['label_reference'];
                            if (isset($details[$singleBarcode]['price']) && $details[$singleBarcode]['price'] !== '') $item['price'] = $details[$singleBarcode]['price'];
                            if (isset($details[$singleBarcode]['mrp']) && $details[$singleBarcode]['mrp'] !== '') $item['mrp'] = $details[$singleBarcode]['mrp'];
                        }
                        $item['barcode_display'] = $singleBarcode;
                        $item['specific_qty'] = $qty;

                        $barcodes[] = $item;
                    }
                }
            }
        } else {
            if (!empty($product->barcode)) {
                $mainBarcodes = array_filter(array_map('trim', explode(',', $product->barcode)));
                foreach ($mainBarcodes as $singleBarcode) {
                    $item = $this->buildBarcodeLabelData($product, null, $settings, $barcodeLayout);
                    $item['barcode'] = $singleBarcode;
                    $item['barcode_image'] = $this->generateBarcodeImage($generator, $item['barcode'], $barcodeLayout, $barcodeDetailOption, $barcodeType, $labelSize['width'], $labelSize['height']);
                    $item['name'] = $item['display_name'];
                    $item['value'] = $item['barcode'];
                    $item['img'] = $item['barcode_image'];
                    
                    $qty = '';
                    $details = is_string($product->barcode_details) ? json_decode($product->barcode_details, true) : ($product->barcode_details ?? []);
                    if (isset($details[$singleBarcode])) {
                        if (isset($details[$singleBarcode]['specific_qty']) && $details[$singleBarcode]['specific_qty'] !== '') $qty = $details[$singleBarcode]['specific_qty'];
                        if (isset($details[$singleBarcode]['batch_no']) && $details[$singleBarcode]['batch_no'] !== '') $item['batch_no'] = $details[$singleBarcode]['batch_no'];
                        if (isset($details[$singleBarcode]['label_code']) && $details[$singleBarcode]['label_code'] !== '') $item['label_code'] = $details[$singleBarcode]['label_code'];
                        if (isset($details[$singleBarcode]['label_reference']) && $details[$singleBarcode]['label_reference'] !== '') $item['label_reference'] = $details[$singleBarcode]['label_reference'];
                        if (isset($details[$singleBarcode]['price']) && $details[$singleBarcode]['price'] !== '') $item['price'] = $details[$singleBarcode]['price'];
                        if (isset($details[$singleBarcode]['mrp']) && $details[$singleBarcode]['mrp'] !== '') $item['mrp'] = $details[$singleBarcode]['mrp'];
                    }
                    $item['barcode_display'] = $singleBarcode;
                    $item['specific_qty'] = $qty;

                    $barcodes[] = $item;
                }
            }
        }

        $printBarcode = trim((string) request('barcode', ''));
        if ($printBarcode !== '') {
            $wanted = array_values(array_filter(array_map('trim', explode(',', $printBarcode))));
            $barcodes = array_values(array_filter($barcodes, function ($item) use ($wanted) {
                return in_array((string) ($item['barcode'] ?? ''), $wanted, true);
            }));
        }

        $printPrice = request('price');
        $printMrp = request('mrp');
        $omitMrpRate = filter_var(request('omit_mrp_rate', false), FILTER_VALIDATE_BOOLEAN);
        if ($omitMrpRate) {
            foreach ($barcodes as &$item) {
                $item['price'] = '';
                $item['mrp'] = '';
                $item['hide_mrp_rate'] = true;
            }
            unset($item);
        } else {
            if ($printPrice !== null && $printPrice !== '') {
                foreach ($barcodes as &$item) {
                    $item['price'] = $printPrice;
                }
                unset($item);
            }
            if ($printMrp !== null && $printMrp !== '') {
                foreach ($barcodes as &$item) {
                    $item['mrp'] = $printMrp;
                }
                unset($item);
            }
        }

        $copies = (int) request('copies', 1);
        $copies = max(1, min(200, $copies));
        $printer = strtolower(trim((string) request('printer', 'tvs')));
        if (!in_array($printer, ['tvs', 'tsc'], true)) {
            $printer = 'tvs';
        }
        $labelFormat = strtolower(trim((string) request('label_format', 'classic')));
        if ($labelFormat !== 'split') {
            $labelFormat = 'classic';
        }

        $includeCompanyTitle = !in_array(strtolower((string) request('include_title', '1')), ['0', 'false', 'no', 'off'], true);
        $isMobilePrint = filter_var(request('mobile', false), FILTER_VALIDATE_BOOLEAN);
        $showWholesaleOnBarcode = filter_var(request('print_wholesale_label', false), FILTER_VALIDATE_BOOLEAN);
        $showBatchOnBarcode = !in_array(strtolower((string) request('print_batch_label', '1')), ['0', 'false', 'no', 'off'], true);
        $printName = trim((string) request('print_name', ''));
        if ($printName !== '') {
            $printName = mb_substr($printName, 0, 300);
            foreach ($barcodes as &$item) {
                $item['name'] = $printName;
                $item['display_name'] = $printName;
            }
            unset($item);
        }

        foreach ($barcodes as &$item) {
            if (!$showBatchOnBarcode) {
                $item['batch_no'] = '';
            }
            $itemWholesale = \App\Models\Product::parseBarcodeWholesaleValue((string) ($item['label_reference'] ?? ''));
            $item['show_wholesale_on_barcode'] = $showWholesaleOnBarcode || ($itemWholesale !== null && $itemWholesale > 0);
            if (empty($item['barcode'])) {
                continue;
            }
            try {
                // Thicker bars + taller PNG so thermal printers stay scannable on 35mm labels.
                $barModule = $labelFormat === 'split' ? 3 : 3;
                $barHeight = $labelFormat === 'split' ? 90 : 72;
                $item['svg'] = 'data:image/png;base64,' . $generator->getBarcodePNG(
                    (string) $item['barcode'],
                    'C128',
                    $barModule,
                    $barHeight,
                    [0, 0, 0],
                    false
                );
            } catch (\Throwable $e) {
                $item['svg'] = $item['img'] ?? '';
            }
        }
        unset($item);

        $labelSize['include_title'] = $includeCompanyTitle;
        if ($copies > 1 && !empty($barcodes)) {
            $repeated = [];
            for ($i = 0; $i < $copies; $i++) {
                foreach ($barcodes as $item) {
                    $repeated[] = $item;
                }
            }
            $barcodes = $repeated;
        }

        $labelCols = max(1, (int) ($labelSize['cols'] ?? 3));
        $pageWidthMm = $labelSize['width'] * $labelCols;
        $pageHeightMm = $labelSize['height'];

        if ($barcodeLayout === 'detailed') {
            $view = $barcodeDetailOption === 'short'
                ? 'product.barcode_pdf_detailed_short'
                : 'product.barcode_pdf_detailed_long';
        $pdf = Pdf::loadView($view, [
            'product'          => $product,
            'barcodes'         => $barcodes,
            'settings'         => $settings,
            'currencySymbol'   => $currencySymbol,
            'currencyPosition' => $currencyPosition,
            'barcodeLayout' => $barcodeLayout,
            'barcodeDetailOption' => $barcodeDetailOption,
            'barcodeType'      => $barcodeType,
            ])->setPaper('A4', 'portrait');
        } else {
            $rowImages = [];
            $renderer = new BarcodeLabelRenderer();
            foreach (array_chunk(array_values($barcodes), $labelCols) as $row) {
                if ($labelFormat === 'split') {
                    $rowImages[] = $renderer->renderSplitRow($row, $labelSize, $settings);
                } else {
                    $rowImages[] = $renderer->renderRow($row, $labelSize, $settings);
                }
            }

            $pdf = Pdf::loadView('product.barcode_pdf', [
                'product'      => $product,
                'rowImages'    => $rowImages,
                'pageWidthMm'  => $pageWidthMm,
                'pageHeightMm' => $pageHeightMm,
            ])->setPaper([
                0,
                0,
                $this->mmToPoints($pageWidthMm),
                $this->mmToPoints($pageHeightMm),
            ], 'portrait');

        $fileName     = 'Barcode_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $product->name) . '_' . now()->format('Ymd_His') . '.pdf';
        $folder       = 'product-barcodes';
        $relativePath = "{$folder}/{$fileName}";

        Storage::disk('public')->makeDirectory($folder);
        Storage::disk('public')->put($relativePath, $pdf->output());

        $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);

            $printHtml = view('product.barcode_print', [
                'product'       => $product,
                'barcodes'      => $barcodes,
                'rowImages'     => $rowImages,
                'settings'      => $settings,
                'labelWidthMm'  => $labelSize['width'],
                'labelHeightMm' => $labelSize['height'],
                'labelCols'     => $labelCols,
                'pageWidthMm'   => $pageWidthMm,
                'pageHeightMm'  => $pageHeightMm,
                'isTallLabel'   => $labelSize['tall'],
                'printer'       => $printer,
                'labelFormat'   => $labelFormat,
                'includeCompanyTitle' => $includeCompanyTitle,
                'autoPrint'     => ! $isMobilePrint,
                'isMobilePrint' => $isMobilePrint,
                'pdfDownloadUrl' => $isMobilePrint ? $fileUrl : null,
                'showWholesaleOnBarcode' => $showWholesaleOnBarcode,
            ])->render();
        }

        if (! isset($fileUrl)) {
            $fileName     = 'Barcode_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $product->name) . '_' . now()->format('Ymd_His') . '.pdf';
            $folder       = 'product-barcodes';
            $relativePath = "{$folder}/{$fileName}";

            Storage::disk('public')->makeDirectory($folder);
            Storage::disk('public')->put($relativePath, $pdf->output());

            $fileUrl = asset(env('ImagePath') . 'storage/' . $relativePath);
        }

        return response()->json([
            'status'     => true,
            'message'    => 'Barcode PDF generated successfully.',
            'file_url'   => $fileUrl,
            'file_name'  => $fileName,
            'print_html' => $printHtml ?? null,
            'row_images' => filter_var(request('bt', false), FILTER_VALIDATE_BOOLEAN) ? array_values($rowImages ?? []) : [],
            'page_width_mm' => $pageWidthMm,
            'page_height_mm' => $pageHeightMm,
        ]);
    }

    public function getQuantityHistory($productId)
    {
        $product = Product::find($productId);
        if (! $product) {
            return response()->json(['status' => false, 'message' => 'Product not found.']);
        }

        $history = [];

        // Initial stock (e.g., when product added)
        $history[] = [
            'type'     => 'Added',
            'quantity' => $product->quantity,
            'note'     => 'Initial stock on product creation',
            'date'     => $product->created_at->format('d-M-Y h:i A'),
        ];

        // Sales data (reduce quantity)
        $orderItems = OrderItem::with('order')->where('product_id', $productId)->get();
        foreach ($orderItems as $item) {
            $orderNumber = $item->order ? $item->order->order_number : 'N/A';

            $history[] = [
                'type'     => 'Sold',
                'quantity' => $item->quantity,
                'note'     => 'Sold in order #' . $orderNumber,
                'date'     => $item->created_at->format('d-M-Y h:i A'),
            ];
        }

        return response()->json([
            'status'  => true,
            'product' => $product->name,
            'history' => $history,
        ]);
    }

    public function getCategory(Request $request)
    {
        $user = Auth::guard('api')->user();

        // Agar request me sub_branch_id aaya hai to use karo, warna logged-in user ka branch_id lo
        $branchId = $request->sub_branch_id ?? $user->id;
        if ($user->role == 'staff') {
            $branchId = $user->branch_id;
        }
        $categories = Category::where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->get();
        // dd($categories);

        return response()->json([
            'status' => true,
            'data'   => $categories,
        ], 200);
    }

    public function getBrand(Request $request)
    {
        $user = Auth::guard('api')->user();

        // Branch ID decide karna (agar sub_branch_id aaya hai to use karo, otherwise user ka branch_id)
        $branchId = $request->sub_branch_id ?? $user->id;

        // Categories fetch
        if ($user->role == 'staff') {
            $branchId = $user->branch_id;
        }
        $Brands = Brand::where('isDeleted', 0)
            ->where('branch_id', $branchId)
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $Brands,
        ], 200);
    }

    public function getTaxRates(Request $request)
    {
        $user = Auth::guard('api')->user();

        $branchId = $request->sub_branch_id ?? $user->id;

        if ($user->role == 'staff') {
            $branchId = $user->branch_id;
        }

        $taxRates = TaxRate::where('isDeleted', 0)
            ->where('status', 'active')
            ->where('branch_id', $branchId)
            ->get(['id', 'tax_name', 'tax_rate']);

        return response()->json([
            'status' => true,
            'data'   => $taxRates,
        ], 200);
    }

    public function getUnits(Request $request)
    {
        $user = Auth::guard('api')->user();

        $branchId = $request->sub_branch_id ?? $user->id;

        if ($user->role == 'staff') {
            $branchId = $user->branch_id;
        }

        $units = Unit::where('is_delete', 0)
            ->where('created_by', $branchId)
            ->get(['id', 'unit_name']);

        return response()->json([
            'status' => true,
            'data'   => $units,
        ], 200);
    }

    public function edit_product(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        // dd($product);
        // $units = Unit::find($product->unit_id);

        // dd($units);
        return response()->json([
            'status'  => true,
            'product' => $product,
            // 'units'    => $units,
        ]);
    }
    private function resolveProfitLossDateRange($timePeriod)
    {
        return match ($timePeriod) {
            'this_week' => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
            'this_month' => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
            'last_6_months' => [now()->subMonths(5)->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
            'this_year' => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
            'previous_year' => [now()->subYear()->startOfYear()->toDateString(), now()->subYear()->endOfYear()->toDateString()],
            default => [null, null],
        };
    }

    private function applyDateFilters($query, $column, $startDate = null, $endDate = null, $year = null, $month = null)
    {
        if ($startDate) {
            $query->whereDate($column, '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate($column, '<=', $endDate);
        }

        if ($year) {
            $query->whereYear($column, $year);
        }

        if ($month) {
            $query->whereMonth($column, $month);
        }

        return $query;
    }

    private function getProfitLossReportData($branchId, $productId = null, $vendorId = null, $customerId = null, $startDate = null, $endDate = null, $year = null, $month = null, $timePeriod = 'all_time', ?User $user = null)
    {
        $user = $user ?? Auth::user();
        $allowedProductIds = null;

        if ($productId) {
            $allowedProductIds = [(int) $productId];
        }

        if ($vendorId) {
            $vendorProductIds = DB::table('purchases')
                ->join('purchase_invoice', 'purchases.invoice_id', '=', 'purchase_invoice.id')
                ->where('purchases.isDeleted', 0)
                ->where('purchase_invoice.isDeleted', 0)
                ->where('purchase_invoice.branch_id', $branchId)
                ->where('purchase_invoice.vendor_id', $vendorId);

            if ($user) {
                StaffDepartmentScope::applyRestrictedPurchaseJoinScope($vendorProductIds, $user);
            }

            $this->applyDateFilters(
                $vendorProductIds,
                DB::raw('COALESCE(purchase_invoice.purchase_date, purchase_invoice.created_at)'),
                $startDate,
                $endDate,
                $year,
                $month
            );

            $vendorProductIds = $vendorProductIds->distinct()->pluck('purchases.item')->map(fn($id) => (int) $id)->all();
            $allowedProductIds = is_array($allowedProductIds)
                ? array_values(array_intersect($allowedProductIds, $vendorProductIds))
                : $vendorProductIds;
        }

        if ($customerId) {
            $customerProductIds = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('orders.isDeleted', 0)
                ->where('products.isDeleted', 0)
                ->where('orders.payment_status', 'completed')
                ->where('products.branch_id', $branchId)
                ->where('orders.user_id', $customerId);

            if ($user) {
                StaffDepartmentScope::applyRestrictedOrderJoinScope($customerProductIds, $user);
            }

            $this->applyDateFilters($customerProductIds, 'orders.created_at', $startDate, $endDate, $year, $month);

            $customerProductIds = $customerProductIds->distinct()->pluck('products.id')->map(fn($id) => (int) $id)->all();
            $allowedProductIds = is_array($allowedProductIds)
                ? array_values(array_intersect($allowedProductIds, $customerProductIds))
                : $customerProductIds;
        }

        $salesQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.isDeleted', 0)
            ->where('products.isDeleted', 0)
            ->where('orders.payment_status', 'completed')
            ->where('products.branch_id', $branchId)
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'order_items.quantity',
                'order_items.total_amount as sales_amount',
                'orders.created_at as transaction_date'
            );

        if ($user) {
            StaffDepartmentScope::applyRestrictedOrderJoinScope($salesQuery, $user);
        }

        if ($customerId) {
            $salesQuery->where('orders.user_id', $customerId);
        }

        if (is_array($allowedProductIds)) {
            if (empty($allowedProductIds)) {
                $salesQuery->whereRaw('1 = 0');
            } else {
                $salesQuery->whereIn('products.id', $allowedProductIds);
            }
        }

        $this->applyDateFilters($salesQuery, 'orders.created_at', $startDate, $endDate, $year, $month);
        $salesRows = $salesQuery->get();

        $purchaseQuery = DB::table('purchases')
            ->join('purchase_invoice', 'purchases.invoice_id', '=', 'purchase_invoice.id')
            ->join('products', 'purchases.item', '=', 'products.id')
            ->where('purchases.isDeleted', 0)
            ->where('purchase_invoice.isDeleted', 0)
            ->where('products.isDeleted', 0)
            ->where('purchase_invoice.branch_id', $branchId)
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'purchases.quantity',
                'purchases.price as purchase_rate',
                DB::raw('COALESCE(NULLIF(purchases.amount_total, 0), purchases.price * purchases.quantity) as purchase_amount'),
                DB::raw('COALESCE(purchase_invoice.purchase_date, purchase_invoice.created_at) as transaction_date')
            );

        if ($user) {
            StaffDepartmentScope::applyRestrictedPurchaseJoinScope($purchaseQuery, $user);
        }

        if ($vendorId) {
            $purchaseQuery->where('purchase_invoice.vendor_id', $vendorId);
        }

        if (is_array($allowedProductIds)) {
            if (empty($allowedProductIds)) {
                $purchaseQuery->whereRaw('1 = 0');
            } else {
                $purchaseQuery->whereIn('products.id', $allowedProductIds);
            }
        }

        $this->applyDateFilters(
            $purchaseQuery,
            DB::raw('COALESCE(purchase_invoice.purchase_date, purchase_invoice.created_at)'),
            $startDate,
            $endDate,
            $year,
            $month
        );

        $purchaseRows = $purchaseQuery->get();

        $productBreakdown = [];
        $periodProfit = [];
        $rowMap = [];
        $dayMode = (! empty($year) && ! empty($month)) || in_array($timePeriod, ['this_week', 'this_month'], true);

        foreach ($salesRows as $row) {
            $productKey = (string) $row->product_id;
            $salesAmount = (float) $row->sales_amount;
            $quantity = (float) $row->quantity;

            if (! isset($productBreakdown[$productKey])) {
                $productBreakdown[$productKey] = [
                    'product_id' => (int) $row->product_id,
                    'name' => $row->product_name,
                    'sales' => 0.0,
                    'purchase' => 0.0,
                    'profit' => 0.0,
                    'sales_qty' => 0.0,
                    'purchase_qty' => 0.0,
                    'latest_date' => $row->transaction_date,
                ];
            }

            $productBreakdown[$productKey]['sales'] += $salesAmount;
            $productBreakdown[$productKey]['sales_qty'] += $quantity;
            $productBreakdown[$productKey]['latest_date'] = max($productBreakdown[$productKey]['latest_date'], $row->transaction_date);

            $dt = \Carbon\Carbon::parse($row->transaction_date);
            $periodKey = $dayMode ? $dt->format('Y-m-d') : $dt->format('Y-m');
            $periodLabel = $dayMode ? $dt->format('d M Y') : $dt->format('M Y');

            if (! isset($periodProfit[$periodKey])) {
                $periodProfit[$periodKey] = ['label' => $periodLabel, 'profit' => 0.0];
            }
            $periodProfit[$periodKey]['profit'] += $salesAmount;

            $rowMap[$productKey] = true;
        }

        foreach ($purchaseRows as $row) {
            $productKey = (string) $row->product_id;
            $purchaseAmount = (float) $row->purchase_amount;
            $purchaseRate = (float) $row->purchase_rate;
            $quantity = (float) $row->quantity;

            if (! isset($productBreakdown[$productKey])) {
                $productBreakdown[$productKey] = [
                    'product_id' => (int) $row->product_id,
                    'name' => $row->product_name,
                    'sales' => 0.0,
                    'purchase' => 0.0,
                    'profit' => 0.0,
                    'sales_qty' => 0.0,
                    'purchase_qty' => 0.0,
                    'latest_date' => $row->transaction_date,
                ];
            }

            $productBreakdown[$productKey]['purchase'] += $purchaseAmount;
            $productBreakdown[$productKey]['purchase_qty'] += $quantity;
            $productBreakdown[$productKey]['latest_date'] = max($productBreakdown[$productKey]['latest_date'], $row->transaction_date);

            $dt = \Carbon\Carbon::parse($row->transaction_date);
            $periodKey = $dayMode ? $dt->format('Y-m-d') : $dt->format('Y-m');
            $periodLabel = $dayMode ? $dt->format('d M Y') : $dt->format('M Y');

            if (! isset($periodProfit[$periodKey])) {
                $periodProfit[$periodKey] = ['label' => $periodLabel, 'profit' => 0.0];
            }
            $periodProfit[$periodKey]['profit'] -= $purchaseAmount;

            if (! isset($rowMap[$productKey])) {
                $rowMap[$productKey] = true;
            }
        }

        foreach ($productBreakdown as &$item) {
            $item['profit'] = $item['sales'] - $item['purchase'];
            $item['purchase_rate'] = $item['purchase_qty'] > 0
                ? $item['purchase'] / $item['purchase_qty']
                : 0.0;
            unset($item['sales_qty'], $item['purchase_qty']);
        }
        unset($item);

        usort($productBreakdown, fn($a, $b) => strcmp($a['name'], $b['name']));

        ksort($periodProfit);
        $labels = array_values(array_map(fn($item) => $item['label'], $periodProfit));
        $profitValues = array_values(array_map(fn($item) => round($item['profit'], 2), $periodProfit));

        $totalSales = round(array_sum(array_column($productBreakdown, 'sales')), 2);
        $totalPurchase = round(array_sum(array_column($productBreakdown, 'purchase')), 2);
        $totalProfitAmount = round(array_sum(array_map(fn($item) => $item['profit'] > 0 ? $item['profit'] : 0, $productBreakdown)), 2);
        $totalLossAmount = round(array_sum(array_map(fn($item) => $item['profit'] < 0 ? abs($item['profit']) : 0, $productBreakdown)), 2);

        $pdfItems = collect($productBreakdown)->map(function ($item, $index) {
            return (object) [
                'created_at' => $item['latest_date'],
                'product_name' => $item['name'],
                'quantity' => 1,
                'purchase_rate' => $item['purchase'],
                'sales_amount' => $item['sales'],
                'profit_amount' => $item['profit'],
            ];
        })->values();

        return [
            'total_sales' => $totalSales,
            'total_purchase' => $totalPurchase,
            'total_profit' => round($totalSales - $totalPurchase, 2),
            'total_profit_amount' => $totalProfitAmount,
            'total_loss_amount' => $totalLossAmount,
            'has_data' => ! empty($productBreakdown),
            'product_breakdown' => $productBreakdown,
            'chart' => [
                'labels' => ! empty($labels) ? $labels : [now()->format($dayMode ? 'd M Y' : 'M Y')],
                'datasets' => [
                    [
                        'label' => 'Total Profit / Loss',
                        'data' => ! empty($profitValues) ? $profitValues : [0],
                        'borderColor' => '#007bff',
                        'backgroundColor' => 'rgba(0, 123, 255, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                        'pointRadius' => 6,
                        'pointBackgroundColor' => '#007bff',
                        'cubicInterpolationMode' => 'monotone',
                    ],
                ],
            ],
            'pdf_items' => $pdfItems,
        ];
    }

    private function resolveBranchId()
    {
        $user       = auth()->user();
        $role       = strtolower($user->role ?? '');
        $subAdminId = session('selectedSubAdminId');

        return match ($role) {
            'sub-admin' => $user->id,
            'staff'     => $user->branch_id,
            'admin'     => $subAdminId ?: $user->id,
            default     => $user->id,
        };
    }


    public function getProfitLossData(Request $request)
    {
        $user = Auth::guard('api')->user() ?? Auth::user();
        $branchId = $this->resolveBranchId();

        $productId = $request->get('product_id');
        $vendorId = $request->get('vendor_id');
        $customerId = $request->get('customer_id');
        $timePeriod = $request->get('time_period', 'all_time');
        $year = $request->get('year');
        $month = $request->get('month');

        [$startDate, $endDate] = $this->resolveProfitLossDateRange($timePeriod);

        return response()->json(
            $this->getProfitLossReportData($branchId, $productId, $vendorId, $customerId, $startDate, $endDate, $year, $month, $timePeriod, $user)
        );
    }

    public function profitLossPdf(Request $request)
    {
        $user = Auth::guard('api')->user() ?? Auth::user();
        $branchId = $this->resolveBranchId();

        $productId = $request->get('product_id');
        $vendorId = $request->get('vendor_id');
        $customerId = $request->get('customer_id');
        $timePeriod = $request->get('time_period', 'all_time');
        $year = $request->get('year');
        $month = $request->get('month');

        [$startDate, $endDate] = $this->resolveProfitLossDateRange($timePeriod);
        $reportData = $this->getProfitLossReportData($branchId, $productId, $vendorId, $customerId, $startDate, $endDate, $year, $month, $timePeriod, $user);
        $items = $reportData['pdf_items'];

        $settings = DB::table('settings')->where('branch_id', $branchId)->first();
        $selectedProduct = $productId ? Product::find($productId) : null;
        $selectedVendor = $vendorId ? User::find($vendorId) : null;
        $selectedCustomer = $customerId ? User::find($customerId) : null;

        $pdf = Pdf::loadView('reports.profit-loss-report-pdf', [
            'items' => $items,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'year' => $year,
            'month' => $month,
            'settings' => $settings,
            'selectedProduct' => $selectedProduct,
            'selectedVendor' => $selectedVendor,
            'selectedCustomer' => $selectedCustomer,
        ]);

        return $pdf->download('Profit_Loss_Report.pdf');
    }

    public function getVariants($id)
    {
        $product = Product::with('variants')->find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $product->variants
        ]);
    }

    public function getAllPriceHistory()
    {
        $histories = \App\Models\ProductBarcodeHistory::with('product')
            ->whereNotNull('new_price')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'status' => true,
            'data' => $histories
        ]);
    }

    public function getBarcodeHistory($id)
    {
        $histories = \App\Models\ProductBarcodeHistory::where('product_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'status' => true,
            'data' => $histories
        ]);
    }

    public function saveProductHindiName(Request $request)
    {
        $product = Product::find($request->input('product_id'));
        $hindi = trim((string) $request->input('hindi', ''));
        if (! $product || $hindi === '' || mb_strlen($hindi) > 255) {
            return response()->json([
                'status' => false,
                'message' => 'Enter a Hindi name to save.',
            ], 422);
        }

        $product->name_hindi = $hindi;
        $product->save();

        return response()->json([
            'status' => true,
            'hindi' => $hindi,
        ]);
    }

    public function translateProductName(Request $request)
    {
        $text = trim((string) $request->input('text', ''));
        if ($text === '' || mb_strlen($text) > 300) {
            return response()->json([
                'status' => false,
                'message' => 'Enter a product name to translate.',
            ], 422);
        }

        $product = null;
        $productId = (int) $request->input('product_id', 0);
        if ($productId > 0) {
            $product = Product::find($productId);
            if ($product && $this->containsDevanagari($product->name_hindi)) {
                return response()->json([
                    'status' => true,
                    'english' => $text,
                    'hindi' => $product->name_hindi,
                    'stored' => true,
                ]);
            }
        }

        $cacheKey = 'product_name_hi_v2_'.md5($text);
        $hindi = Cache::get($cacheKey);
        if (! is_string($hindi) || ! $this->containsDevanagari($hindi)) {
            $hindi = $this->translateEnglishToHindi($text);
            if ($this->containsDevanagari($hindi)) {
                Cache::put($cacheKey, $hindi, now()->addDays(30));
            }
        }

        if (! $this->containsDevanagari($hindi)) {
            return response()->json([
                'status' => false,
                'message' => 'Could not translate this product name.',
            ], 422);
        }

        if ($product) {
            $product->name_hindi = $hindi;
            $product->save();
        }

        return response()->json([
            'status' => true,
            'english' => $text,
            'hindi' => $hindi,
            'stored' => $product !== null,
        ]);
    }

    private function translateEnglishToHindi(string $text): ?string
    {
        foreach ([$this->translateViaEdge($text), $this->translateViaMyMemory($text), $this->translateViaGoogle($text)] as $hindi) {
            if ($this->containsDevanagari($hindi)) {
                return $hindi;
            }
        }

        return null;
    }

    private function containsDevanagari(?string $text): bool
    {
        return is_string($text) && $text !== '' && preg_match('/\p{Devanagari}/u', $text) === 1;
    }

    private function translateViaEdge(string $text): ?string
    {
        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36 Edg/128.0.0.0',
                ])
                ->withBody(json_encode([$text], JSON_UNESCAPED_UNICODE), 'application/json')
                ->post('https://edge.microsoft.com/translate/translatetext?from=en&to=hi');
            if (! $response->ok()) {
                return null;
            }
            $translated = data_get($response->json(), '0.translations.0.text');

            return is_string($translated) ? trim($translated) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function translateViaMyMemory(string $text): ?string
    {
        try {
            $response = Http::timeout(8)->get('https://api.mymemory.translated.net/get', [
                'q' => $text,
                'langpair' => 'en|hi',
            ]);
            if (! $response->ok()) {
                return null;
            }
            $translated = html_entity_decode(trim((string) data_get($response->json(), 'responseData.translatedText', '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($translated === '' || stripos($translated, 'MYMEMORY WARNING') !== false) {
                return null;
            }

            return $translated;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function translateViaGoogle(string $text): ?string
    {
        try {
            $response = Http::timeout(4)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                ->get('https://translate.googleapis.com/translate_a/single', [
                    'client' => 'gtx',
                    'sl' => 'en',
                    'tl' => 'hi',
                    'dt' => 't',
                    'q' => $text,
                ]);
            if (! $response->ok()) {
                return null;
            }
            $json = $response->json();
            if (! is_array($json) || ! isset($json[0]) || ! is_array($json[0])) {
                return null;
            }
            $parts = [];
            foreach ($json[0] as $chunk) {
                if (is_array($chunk) && isset($chunk[0]) && is_string($chunk[0])) {
                    $parts[] = $chunk[0];
                }
            }
            $joined = trim(implode('', $parts));

            return $joined !== '' ? $joined : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function updateBarcodeInfo(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'current_edit_barcode' => 'nullable|string',
            'price' => 'nullable|numeric|gte:0',
            'mrp' => 'nullable|numeric|gte:0',
            'batch_no' => 'nullable|string|max:255',
            'label_code' => 'nullable|string|max:255',
            'label_reference' => 'nullable|string|max:255',
            'specific_qty' => 'nullable|string|max:255',
            'barcode' => 'nullable|string',
            'label_size' => 'nullable|in:35x25x1,35x25x3,38x25x2,25x50x2',
        ]);

        $product = Product::findOrFail($request->product_id);
        $oldPrice = $product->price;
        $oldMrp = $product->mrp;
        $oldPurchasePrice = $product->purchase_price;

        $updateData = [];
        if ($request->has('barcode')) $updateData['barcode'] = $request->barcode;
        if ($request->has('label_size')) $updateData['label_size'] = $request->label_size;
        
        $currentEditBarcode = $request->current_edit_barcode;
        
        if ($request->has('all_barcode_details')) {
            $details = json_decode($request->all_barcode_details, true) ?? [];
            
            // Clean up: only keep details for barcodes that are still present
            $barcodesList = $request->barcode ? array_filter(array_map('trim', explode(',', $request->barcode))) : [];
            $filteredDetails = [];
            foreach ($barcodesList as $b) {
                if (isset($details[$b])) {
                    $filteredDetails[$b] = $details[$b];
                }
            }
            $updateData['barcode_details'] = $filteredDetails;
            
            // Keep batch/label fields on the product for the active barcode; price/mrp stay on barcode_details so extra rates do not overwrite Rate 1.
            if (!empty($barcodesList) && $barcodesList[0] == $currentEditBarcode) {
                if ($request->has('batch_no')) $updateData['batch_no'] = $request->batch_no;
                if ($request->has('label_code')) $updateData['label_code'] = $request->label_code;
                if ($request->has('label_reference')) $updateData['label_reference'] = $request->label_reference;
                if ($request->has('specific_qty')) $updateData['specific_qty'] = $request->specific_qty;
            }
        } elseif ($currentEditBarcode) {
            // Save specific barcode details into JSON column (legacy fallback)
            $details = is_string($product->barcode_details) ? json_decode($product->barcode_details, true) : ($product->barcode_details ?? []);
            if (!is_array($details)) $details = [];
            
            $details[$currentEditBarcode] = [
                'price' => $request->price,
                'mrp' => $request->mrp,
                'batch_no' => $request->batch_no,
                'label_code' => $request->label_code,
                'label_reference' => $request->label_reference,
                'specific_qty' => $request->specific_qty,
            ];
            $updateData['barcode_details'] = $details;
            
            $barcodesList = $request->barcode ? array_filter(array_map('trim', explode(',', $request->barcode))) : [];
            if (empty($barcodesList) || $barcodesList[0] == $currentEditBarcode) {
                if ($request->has('batch_no')) $updateData['batch_no'] = $request->batch_no;
                if ($request->has('label_code')) $updateData['label_code'] = $request->label_code;
                if ($request->has('label_reference')) $updateData['label_reference'] = $request->label_reference;
                if ($request->has('specific_qty')) $updateData['specific_qty'] = $request->specific_qty;
            }
        } else {
            // Fallback for generic updates if no specific barcode selected
            // Empty price/mrp means "omit on label only" — do not wipe product catalog prices.
            if ($request->filled('price')) $updateData['price'] = $request->price;
            if ($request->filled('mrp')) $updateData['mrp'] = $request->mrp;
            if ($request->has('batch_no')) $updateData['batch_no'] = $request->batch_no;
            if ($request->has('label_code')) $updateData['label_code'] = $request->label_code;
            if ($request->has('label_reference')) $updateData['label_reference'] = $request->label_reference;
            if ($request->has('specific_qty')) $updateData['specific_qty'] = $request->specific_qty;
        }

        $product->update($updateData);

        // Blank MRP/Rate on print = hide on barcode only; never treat as a catalog price change.
        $newPrice = $request->filled('price') ? $request->price : null;
        $newMrp = $request->filled('mrp') ? $request->mrp : null;
        $newPurchasePrice = $request->filled('purchase_price') ? $request->purchase_price : null;
        $priceChanged = $newPrice !== null && (string) $oldPrice !== (string) $newPrice;
        $mrpChanged = $newMrp !== null && (string) $oldMrp !== (string) $newMrp;
        $purchaseChanged = $newPurchasePrice !== null && (string) $oldPurchasePrice !== (string) $newPurchasePrice;

        // Printing labels saves batch/label fields every time; only notify on a real catalog price change.
        if ($priceChanged || $mrpChanged || $purchaseChanged) {
            $actor = \App\Services\NotificationService::actorName(Auth::guard('api')->id());
            $this->notifyProductEvent(
                $product,
                'product_updated',
                'Product Updated',
                $actor . " updated '{$product->name}'. " . $this->describeProductChanges($product)
            );
        }

        if ($priceChanged || $mrpChanged || $purchaseChanged) {
            $this->dispatchCrossBranchPriceUpdates(
                $product, 
                $oldPrice, 
                $priceChanged ? $newPrice : $oldPrice,
                $oldMrp,
                $mrpChanged ? $newMrp : $oldMrp,
                $oldPurchasePrice,
                $purchaseChanged ? $newPurchasePrice : $oldPurchasePrice
            );
        }

        if ($request->has('batch_no')) {
            $this->syncGlobalBatchNo($product->branch_id, $request->input('batch_no'));
        }

        return response()->json([
            'status' => true,
            'message' => 'Barcode information updated successfully',
            'product' => $product
        ]);
    }

    public function applyGlobalBatchNo(Request $request)
    {
        $request->validate([
            'batch_no' => 'nullable|string|max:255',
        ]);

        $user = Auth::guard('api')->user();
        $branchId = match (strtolower((string) ($user->role ?? ''))) {
            'admin' => $request->input('sub_branch_id') ?: $user->id,
            'sub-admin' => $user->id,
            'staff' => $user->branch_id,
            default => $user->id,
        };

        $batchNo = $this->syncGlobalBatchNo($branchId, $request->input('batch_no'));

        return response()->json([
            'status' => true,
            'batch_no' => $batchNo ?? '',
            'message' => 'Batch number updated for all products',
        ]);
    }

    private function normalizeBatchNo($batchNo): ?string
    {
        $batchNo = trim((string) $batchNo);
        if ($batchNo === '') {
            return null;
        }

        return mb_substr($batchNo, 0, 255);
    }

    private function currentGlobalBatchNo($branchId): ?string
    {
        if (!$branchId) {
            return null;
        }

        return $this->normalizeBatchNo(
            DB::table('settings')->where('branch_id', $branchId)->value('global_batch_no')
        );
    }

    private function syncGlobalBatchNo($branchId, $batchNo): ?string
    {
        $batchNo = $this->normalizeBatchNo($batchNo);
        if (!$branchId) {
            return $batchNo;
        }

        if ($this->currentGlobalBatchNo($branchId) === $batchNo) {
            return $batchNo;
        }

        DB::table('settings')->where('branch_id', $branchId)->update([
            'global_batch_no' => $batchNo,
        ]);

        DB::table('products')->where('branch_id', $branchId)->update([
            'batch_no' => $batchNo,
        ]);

        Product::$skipBarcodeHistory = true;
        try {
            Product::query()
                ->where('branch_id', $branchId)
                ->whereNotNull('barcode_details')
                ->select(['id', 'barcode_details'])
                ->orderBy('id')
                ->chunkById(150, function ($products) use ($batchNo) {
                    foreach ($products as $product) {
                        $details = $product->barcode_details;
                        if (!is_array($details) || $details === []) {
                            continue;
                        }
                        $changed = false;
                        foreach ($details as $code => $row) {
                            if (!is_array($row)) {
                                continue;
                            }
                            if (($row['batch_no'] ?? null) !== $batchNo) {
                                $details[$code]['batch_no'] = $batchNo;
                                $changed = true;
                            }
                        }
                        if ($changed) {
                            $product->barcode_details = $details;
                            $product->save();
                        }
                    }
                });
        } finally {
            Product::$skipBarcodeHistory = false;
        }

        return $batchNo;
    }


    private function notifyProductEvent(Product $product, string $type, string $title, string $message): void
    {
        try {
            $branchId = (int) $product->branch_id;
            $link = '/product-view/' . $product->id;
            $users = User::query()
                ->where('isDeleted', 0)
                ->whereIn('role', ['admin', 'sub-admin', 'staff'])
                ->where(function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId)
                        ->orWhere('id', $branchId);
                })
                ->get(['id']);

            foreach ($users as $targetUser) {
                \App\Services\NotificationService::notify(
                    (int) $targetUser->id,
                    $branchId,
                    $type,
                    $title,
                    $message,
                    $link
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Product notification failed: ' . $e->getMessage());
        }
    }

    private function describeProductChanges(Product $product): string
    {
        $skip = ['updated_at', 'created_at', 'barcode_details', 'selling_prices', 'images', 'product_gst', 'imei_no'];
        $labels = [
            'name' => 'Name',
            'name_hindi' => 'Hindi name',
            'price' => 'Price',
            'mrp' => 'MRP',
            'purchase_price' => 'Purchase price',
            'quantity' => 'Quantity',
            'SKU' => 'SKU',
            'barcode' => 'Barcode',
            'hsn_code' => 'HSN',
            'status' => 'Status',
            'category_id' => 'Category',
            'brand_id' => 'Brand',
            'unit_id' => 'Unit',
            'description' => 'Description',
            'batch_no' => 'Batch',
            'tax_rate' => 'Tax',
            'cost_price' => 'Cost price',
            'landing_cost' => 'Landing cost',
            'minimum_stock_qty' => 'Min stock',
            'item_location' => 'Location',
        ];

        $parts = [];
        foreach ($product->getChanges() as $field => $newValue) {
            if (in_array($field, $skip, true)) {
                continue;
            }
            $label = $labels[$field] ?? ucfirst(str_replace('_', ' ', (string) $field));
            $parts[] = $label . ': ' . $this->shortNotifyValue($product->getOriginal($field)) . ' → ' . $this->shortNotifyValue($newValue);
            if (count($parts) >= 5) {
                break;
            }
        }

        return $parts ? implode('; ', $parts) : 'Details were updated.';
    }

    private function shortNotifyValue($value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }
        if (is_array($value) || is_object($value)) {
            return 'updated';
        }
        $text = trim((string) $value);
        if (strlen($text) > 40) {
            return substr($text, 0, 37) . '...';
        }

        return $text;
    }

    /**
     * Copy a newly created product catalog row to every other branch with qty 0.
     * Stock stays local; shared SKU/barcode enables existing price approve/reject.
     */
    protected function replicateProductToOtherBranches(Product $product, $user): void
    {
        $sourceBranchId = (int) ($product->branch_id ?? 0);
        if ($sourceBranchId <= 0) {
            return;
        }

        $targetBranches = User::query()
            ->whereIn('role', ['admin', 'sub-admin'])
            ->where('isDeleted', 0)
            ->where('id', '!=', $sourceBranchId)
            ->pluck('id');

        if ($targetBranches->isEmpty()) {
            return;
        }

        $product->loadMissing(['variants', 'category', 'brand']);

        foreach ($targetBranches as $targetBranchId) {
            $this->cloneProductToBranch($product, (int) $targetBranchId, $user);
        }
    }

    /**
     * Clone one catalog product into a target branch (qty 0). Returns created|skipped|failed.
     */
    protected function cloneProductToBranch(Product $product, int $targetBranchId, $user): string
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
            \Log::warning('Skip cross-branch product clone: category unresolved', [
                'product_id' => $product->id,
                'target_branch_id' => $targetBranchId,
            ]);

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
            // Live DB may still have global unique barcode — keep SKU, drop barcode.
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

        try {
            if (! $this->isWarrantyCategoryId($clone->category_id)) {
                ProductInventory::create([
                    'product_id' => $clone->id,
                    'product_variant_id' => null,
                    'initial_stock' => 0,
                    'current_stock' => 0,
                    'branch_id' => $targetBranchId,
                    'create_by' => $user->id ?? null,
                    'type' => 'Create',
                    'date' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // Inventory log is optional for catalog share.
        }

        return 'created';
    }

    protected function resolveCategoryForBranch($sourceCategoryId, int $targetBranchId, $sourceCategory = null): ?int
    {
        if (empty($sourceCategoryId) && !$sourceCategory) {
            return null;
        }

        $source = $sourceCategory ?: Category::find($sourceCategoryId);
        if (!$source) {
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

    protected function resolveBrandForBranch($sourceBrandId, int $targetBranchId, $sourceBrand = null): ?int
    {
        if (empty($sourceBrandId) && !$sourceBrand) {
            return null;
        }

        $source = $sourceBrand ?: Brand::find($sourceBrandId);
        if (!$source) {
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


    /**
     * Dispatch price updates to other branches if the same product exists there.
     */
    protected function dispatchCrossBranchPriceUpdates($product, $oldPrice, $newPrice, $oldMrp = null, $newMrp = null, $oldPurchasePrice = null, $newPurchasePrice = null)
    {
        // new_price is NOT NULL on product_price_updates — never queue an empty selling price.
        if ($newPrice === null || $newPrice === '') {
            return;
        }

        if ($oldPrice == $newPrice && $oldMrp == $newMrp && $oldPurchasePrice == $newPurchasePrice) {
            return;
        }
        
        // Find products with the same SKU (or barcode if SKU is missing) in other branches
        $query = Product::where('branch_id', '!=', $product->branch_id)
            ->where('isDeleted', 0);
            
        if (!empty($product->SKU)) {
            $query->where('SKU', $product->SKU);
        } elseif (!empty($product->barcode)) {
            $query->where('barcode', $product->barcode);
        } else {
            return; // Cannot reliably match products without SKU or Barcode
        }
        
        $otherProducts = $query->get();
        
        foreach ($otherProducts as $otherProduct) {
            // Check if there is already a pending request for this target product
            $existingPending = \App\Models\ProductPriceUpdate::where('target_product_id', $otherProduct->id)
                ->where('status', 'pending')
                ->first();
                
            if ($existingPending) {
                // Update the existing pending request
                $existingPending->update([
                    'new_price' => $newPrice,
                    'new_mrp' => $newMrp,
                    'new_purchase_price' => $newPurchasePrice,
                    'source_product_id' => $product->id,
                ]);
            } else {
                // Create a new pending request
                \App\Models\ProductPriceUpdate::create([
                    'source_product_id' => $product->id,
                    'target_product_id' => $otherProduct->id,
                    'branch_id' => $otherProduct->branch_id,
                    'old_price' => $otherProduct->price,
                    'new_price' => $newPrice,
                    'old_mrp' => $otherProduct->mrp,
                    'new_mrp' => $newMrp,
                    'old_purchase_price' => $otherProduct->purchase_price,
                    'new_purchase_price' => $newPurchasePrice,
                    'status' => 'pending',
                ]);
            }
            
            // Notify the target branch admins/staff (including the sub-admin whose ID is the branch_id)
            $targetUsers = \App\Models\User::where(function ($query) use ($otherProduct) {
                    $query->where('branch_id', $otherProduct->branch_id)
                          ->orWhere('id', $otherProduct->branch_id);
                })
                ->where('isDeleted', 0)
                ->whereIn('role', ['admin', 'sub-admin', 'staff'])
                ->get();
                
            foreach ($targetUsers as $targetUser) {
                \App\Services\NotificationService::notify(
                    $targetUser->id,
                    $otherProduct->branch_id,
                    'price_update_request',
                    'Pending Price Update',
                    "A new price ({$newPrice}) was proposed for '{$otherProduct->name}' by another branch. Please review and confirm.",
                    '/pricing-updates'
                );
            }
        }
    }
}
