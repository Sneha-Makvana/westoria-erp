<?php
namespace App\Models;

        use Carbon\Carbon;
        use Illuminate\Database\Eloquent\Factories\HasFactory;
        use Illuminate\Database\Eloquent\Model;
        use Illuminate\Support\Facades\DB;

        class Product extends Model
        {
            use HasFactory;

            public static bool $skipBarcodeHistory = false;

            protected $fillable = [
                'vendor_id',
                'category_id',
                'brand_id',
                'branch_id',
                'name',
                'name_hindi',
                'SKU',
                'hsn_code',
                'barcode',
                'barcode_details',
                'selling_prices',
                'description',
                'price',
                'purchase_price',
                'cost_price',
                'landing_cost',
                'product_code',
                'supplier_code',
                'international_code',
                'serial_no_status',
                'non_inventory_type',
                'stock_validation_status',
                'item_type',
                'discount_print_status',
                'item_created_on',
                'images',
                'quantity',
                'specific_qty',
                'imei_no',
                'unit_id',
                'isDeleted',
                'mrp',
                'batch_no',
                'label_code',
                'label_reference',
                'label_size',
                'availablility',
                'status',
                'gst_option',
                'product_gst',
                'is_rent_available',
                'rent_period',
                'rent_price',
                'created_at',
                'updated_at',
                'create_by',
                'tax_rate',
                'item_location',
                'minimum_stock_qty',
                'discount_type',
                'sale_discount',
                'inclusive_of_tax',
                'secondary_unit_id',
                'conversion_rate',
            ];

            protected $appends = ['image_url', 'selling_price_options', 'wholesale_price'];

            protected $casts = [
                'quantity' => 'decimal:2',
                'mrp' => 'decimal:2',
                'barcode_details' => 'array',
                'selling_prices' => 'array',
                'minimum_stock_qty' => 'decimal:2',
                'sale_discount' => 'decimal:2',
                'inclusive_of_tax' => 'boolean',
                'conversion_rate' => 'decimal:4',
            ];

            // Relationship with Category
            public function category()
            {
                return $this->belongsTo(Category::class, 'category_id');
            }

            public function unit()
            {
                return $this->belongsTo(Unit::class, 'unit_id');
            }

            public function secondaryUnit()
            {
                return $this->belongsTo(Unit::class, 'secondary_unit_id');
            }

            public function variants()
            {
                return $this->hasMany(ProductVariant::class);
            }

            public function getSellingPriceOptionsAttribute(): array
            {
                return $this->sellingPriceOptions();
            }

            public function sellingPriceOptions(): array
            {
                $options = [];
                $saved = $this->selling_prices;
                if (is_string($saved)) {
                    $saved = json_decode($saved, true);
                }
                if (is_array($saved)) {
                    foreach ($saved as $row) {
                        if (!is_array($row) || !isset($row['price']) || $row['price'] === '' || $row['price'] === null) {
                            continue;
                        }
                        $name = trim((string) ($row['name'] ?? ''));
                        $mrp = self::nullableMoney($row['mrp'] ?? null);
                        $option = [
                            'name' => $name !== '' ? $name : ('Rate ' . (count($options) + 1)),
                            'price' => (float) $row['price'],
                            'mrp' => $mrp,
                        ];
                        if (array_key_exists('qty', $row) && $row['qty'] !== '' && $row['qty'] !== null) {
                            $option['qty'] = (float) $row['qty'];
                        }
                        $wholesale = self::nullableMoney($row['wholesale'] ?? null);
                        if ($wholesale !== null) {
                            $option['wholesale'] = $wholesale;
                        }
                        $options[] = $option;
                    }
                }

                $main = (float) ($this->price ?? 0);
                $mainMrp = self::nullableMoney($this->mrp);
                $hasMain = false;
                foreach ($options as $i => $option) {
                    if (abs((float) $option['price'] - $main) < 0.0001) {
                        $hasMain = true;
                        if (($option['mrp'] ?? null) === null && $mainMrp !== null) {
                            $options[$i]['mrp'] = $mainMrp;
                        }
                        break;
                    }
                }
                if (!$hasMain) {
                    array_unshift($options, [
                        'name' => 'Rate 1',
                        'price' => $main,
                        'mrp' => $mainMrp,
                    ]);
                }

                $details = $this->barcode_details ?? [];
                if (is_string($details)) {
                    $details = json_decode($details, true) ?: [];
                }
                if (is_array($details)) {
                    foreach ($details as $detail) {
                        if (!is_array($detail) || !isset($detail['price']) || $detail['price'] === '' || $detail['price'] === null) {
                            continue;
                        }
                        $price = (float) $detail['price'];
                        $exists = false;
                        foreach ($options as $option) {
                            if (abs((float) $option['price'] - $price) < 0.0001) {
                                $exists = true;
                                break;
                            }
                        }
                        if (!$exists) {
                            $options[] = [
                                'name' => 'Rate ' . (count($options) + 1),
                                'price' => $price,
                                'mrp' => self::nullableMoney($detail['mrp'] ?? null),
                            ];
                        }
                    }
                }

                return $options;
            }

            public static function resolveWholesaleRates($sellingPrices = null, $retailPrice = null): array
            {
                $rates = $sellingPrices;
                if (is_string($rates)) {
                    $rates = json_decode($rates, true);
                }
                if (!is_array($rates) || $rates === []) {
                    return [];
                }

                $retail = $retailPrice !== null && $retailPrice !== '' ? (float) $retailPrice : null;
                $options = [];
                $seen = [];

                $pushOption = function (string $name, float $price, $mrp = null) use (&$options, &$seen) {
                    $key = number_format($price, 4, '.', '');
                    if (isset($seen[$key])) {
                        return;
                    }
                    $seen[$key] = true;
                    $options[] = [
                        'name' => $name !== '' ? $name : ('Wholesale ' . (count($options) + 1)),
                        'price' => $price,
                        'mrp' => self::nullableMoney($mrp),
                    ];
                };

                foreach ($rates as $row) {
                    if (!is_array($row) || !isset($row['price']) || $row['price'] === '' || $row['price'] === null) {
                        continue;
                    }
                    $price = (float) $row['price'];
                    $name = trim((string) ($row['name'] ?? ''));
                    if ($name !== '' && stripos($name, 'wholesale') !== false) {
                        $pushOption($name, $price, $row['mrp'] ?? null);
                    }
                }

                foreach ($rates as $row) {
                    if (!is_array($row) || !isset($row['price']) || $row['price'] === '' || $row['price'] === null) {
                        continue;
                    }
                    $price = (float) $row['price'];
                    $name = trim((string) ($row['name'] ?? ''));
                    $isRetail = $retail !== null && abs($price - $retail) < 0.0001;
                    $isRate1 = preg_match('/^rate\s*1$/i', $name);
                    $isWholesaleNamed = $name !== '' && stripos($name, 'wholesale') !== false;

                    if ($isWholesaleNamed || ($isRate1 && $isRetail) || ($isRetail && !$isWholesaleNamed)) {
                        continue;
                    }

                    $pushOption($name, $price, $row['mrp'] ?? null);
                }

                return $options;
            }

            public static function resolveWholesalePrice($sellingPrices = null, $retailPrice = null): ?float
            {
                $options = self::resolveWholesaleRates($sellingPrices, $retailPrice);

                return isset($options[0]['price']) ? (float) $options[0]['price'] : null;
            }

            public static function normalizeSellingPrices($mainPrice, $extraRates = [], $mainMrp = null, $mainQty = null): array
            {
                $rates = [];
                if ($mainPrice !== null && $mainPrice !== '') {
                    $rateOne = [
                        'name' => 'Rate 1',
                        'price' => (float) $mainPrice,
                        'mrp' => self::nullableMoney($mainMrp),
                    ];
                    if ($mainQty !== null && $mainQty !== '') {
                        $rateOne['qty'] = (float) $mainQty;
                    }
                    $rates[] = $rateOne;
                }
                if (!is_array($extraRates)) {
                    return $rates;
                }
                foreach ($extraRates as $row) {
                    if (!is_array($row) || !isset($row['price']) || $row['price'] === '' || $row['price'] === null) {
                        continue;
                    }
                    $name = trim((string) ($row['name'] ?? ''));
                    $price = (float) $row['price'];
                    $mrp = self::nullableMoney($row['mrp'] ?? null);
                    if ($name === '') {
                        $name = 'Rate ' . (count($rates) + 1);
                    }
                    $duplicate = false;
                    foreach ($rates as $rate) {
                        $samePrice = abs((float) $rate['price'] - $price) < 0.0001;
                        $sameName = strcasecmp((string) $rate['name'], $name) === 0;
                        $sameMrp = abs((float) ($rate['mrp'] ?? 0) - (float) ($mrp ?? 0)) < 0.0001
                            && (($rate['mrp'] === null) === ($mrp === null));
                        if ($samePrice && $sameName && $sameMrp) {
                            $duplicate = true;
                            break;
                        }
                    }
                    if (!$duplicate) {
                        $entry = [
                            'name' => $name,
                            'price' => $price,
                            'mrp' => $mrp,
                        ];
                        if (array_key_exists('qty', $row) && $row['qty'] !== '' && $row['qty'] !== null) {
                            $entry['qty'] = (float) $row['qty'];
                        }
                        $wholesale = self::nullableMoney($row['wholesale'] ?? null);
                        if ($wholesale !== null) {
                            $entry['wholesale'] = $wholesale;
                        }
                        $rates[] = $entry;
                    }
                }

                return $rates;
            }

            public function getWholesalePriceAttribute(): ?float
            {
                return self::resolveWholesalePrice($this->selling_prices, $this->price);
            }

            public static function buildSellingPrices($mainPrice, $extraRates = [], $mainMrp = null, $wholesalePrice = null, $mainQty = null): array
            {
                $extra = is_array($extraRates) ? $extraRates : [];
                $extra = array_values(array_filter($extra, function ($row) {
                    if (!is_array($row)) {
                        return false;
                    }
                    $name = trim((string) ($row['name'] ?? ''));

                    return $name === '' || stripos($name, 'wholesale') === false;
                }));

                $rates = self::normalizeSellingPrices($mainPrice, $extra, $mainMrp, $mainQty);
                $wholesale = self::nullableMoney($wholesalePrice);

                $rates = array_values(array_filter($rates, function ($row) {
                    $name = trim((string) ($row['name'] ?? ''));

                    return stripos($name, 'wholesale') === false;
                }));

                if ($wholesale === null) {
                    return $rates;
                }

                $main = (float) ($mainPrice ?? 0);
                if (abs($wholesale - $main) < 0.0001) {
                    return $rates;
                }

                $rates[] = [
                    'name' => 'Wholesale',
                    'price' => (float) $wholesale,
                    'mrp' => null,
                ];

                return $rates;
            }

            public static function skuExistsForBranch(?int $branchId, string $sku, ?int $ignoreProductId = null): bool
            {
                $sku = trim($sku);
                if ($sku === '') {
                    return false;
                }

                $query = static::query()
                    ->where('branch_id', $branchId)
                    ->where('isDeleted', 0)
                    ->where('SKU', $sku);

                if ($ignoreProductId) {
                    $query->where('id', '!=', $ignoreProductId);
                }

                return $query->exists();
            }

            /**
             * Same product in other branches: same SKU (or barcode when SKU is empty) and same name,
             * so a re-numbered SKU in another branch never receives a different product's images.
             */
            public function branchCopiesQuery()
            {
                $sku = trim((string) ($this->SKU ?? ''));
                $barcode = trim((string) ($this->barcode ?? ''));
                $name = mb_strtolower(trim((string) ($this->name ?? '')));

                if (($sku === '' && $barcode === '') || $name === '') {
                    return null;
                }

                return static::query()
                    ->where('branch_id', '!=', $this->branch_id)
                    ->where('isDeleted', 0)
                    ->when($sku !== '', fn ($q) => $q->where('SKU', $sku), fn ($q) => $q->where('barcode', $barcode))
                    ->whereRaw('LOWER(TRIM(name)) = ?', [$name]);
            }

            public static function normalizeImagesJson($images): string
            {
                if (is_string($images)) {
                    $decoded = json_decode($images, true);
                    $images = is_array($decoded) ? $decoded : (trim($images) !== '' ? [$images] : []);
                }

                return json_encode(array_values(array_filter((array) $images, fn ($p) => trim((string) $p) !== '')));
            }

            public function syncImagesToBranchCopies(): int
            {
                $query = $this->branchCopiesQuery();
                if (! $query) {
                    return 0;
                }

                $ids = $query->pluck('id')->all();
                if ($ids === []) {
                    return 0;
                }

                return DB::table('products')->whereIn('id', $ids)->update([
                    'images' => static::normalizeImagesJson($this->images),
                    'updated_at' => now(),
                ]);
            }

            /**
             * One-time fix: give branch copies that have no images the images of their branch twin.
             */
            public static function backfillBranchCopyImages(): int
            {
                $filled = 0;

                $sources = static::query()
                    ->where('isDeleted', 0)
                    ->whereNotNull('images')
                    ->whereNotIn('images', ['', '[]', 'null'])
                    ->orderByDesc('updated_at')
                    ->cursor();

                foreach ($sources as $source) {
                    $json = static::normalizeImagesJson($source->images);
                    if ($json === '[]') {
                        continue;
                    }
                    $query = $source->branchCopiesQuery();
                    if (! $query) {
                        continue;
                    }
                    $ids = $query->where(function ($q) {
                        $q->whereNull('images')->orWhereIn('images', ['', '[]', 'null']);
                    })->pluck('id')->all();

                    if ($ids !== []) {
                        $filled += DB::table('products')->whereIn('id', $ids)->update([
                            'images' => $json,
                            'updated_at' => now(),
                        ]);
                    }
                }

                return $filled;
            }

            public static function isSequentialSku(string $sku): bool
            {
                $sku = trim($sku);

                return $sku !== '' && ctype_digit($sku) && strlen($sku) <= 8;
            }

            public static function getMaxSequentialSku(?int $branchId): int
            {
                $max = 0;

                static::query()
                    ->where('branch_id', $branchId)
                    ->where('isDeleted', 0)
                    ->whereNotNull('SKU')
                    ->pluck('SKU')
                    ->each(function ($sku) use (&$max) {
                        $sku = trim((string) $sku);
                        if (static::isSequentialSku($sku)) {
                            $max = max($max, (int) $sku);
                        }
                    });

                return $max;
            }

            public static function getNextAvailableSku(?int $branchId, $startFrom = null): string
            {
                $candidate = null;

                if ($startFrom !== null && $startFrom !== '' && static::isSequentialSku((string) $startFrom)) {
                    $candidate = max(1, (int) $startFrom);
                }

                if ($candidate === null) {
                    $candidate = static::getMaxSequentialSku($branchId) + 1;
                }

                while (static::skuExistsForBranch($branchId, (string) $candidate)) {
                    $candidate++;
                }

                return (string) $candidate;
            }

            /**
             * Reduce stock for the sold price. Rate 1 uses products.quantity.
             * Other rates use the qty stored on that selling price.
             *
             * @param  array<int, array{price: float|int|string, qty: float|int|string}>  $sales
             */
            public function applyRateSales(array $sales, ?float $mainQuantity = null): void
            {
                $rates = $this->selling_prices;
                if (is_string($rates)) {
                    $rates = json_decode($rates, true);
                }
                if (!is_array($rates)) {
                    $rates = [];
                }

                $mainPrice = (float) ($this->price ?? 0);
                if ($mainQuantity !== null) {
                    // Allow negative stock (oversell / negative buying).
                    $this->quantity = $mainQuantity;
                }

                foreach ($sales as $sale) {
                    $price = (float) ($sale['price'] ?? 0);
                    $qty = (float) ($sale['qty'] ?? 0);
                    if ($qty <= 0 || abs($price - $mainPrice) < 0.0001) {
                        continue;
                    }
                    foreach ($rates as $index => $rate) {
                        if (!is_array($rate) || abs((float) ($rate['price'] ?? 0) - $price) >= 0.0001) {
                            continue;
                        }
                        $current = array_key_exists('qty', $rate) ? (float) $rate['qty'] : 0;
                        $rates[$index]['qty'] = $current - $qty;
                    }
                }

                foreach ($rates as $index => $rate) {
                    if (!is_array($rate)) {
                        continue;
                    }
                    if (abs((float) ($rate['price'] ?? 0) - $mainPrice) < 0.0001) {
                        $rates[$index]['qty'] = (float) $this->quantity;
                    }
                }

                $this->selling_prices = $rates;
            }

            /**
             * Add or remove stock for one selling rate.
             * Empty rate, or a rate that matches products.price, uses products.quantity.
             * Any other rate uses the qty stored on that selling price.
             */
            public function adjustRateStock(float $delta, $ratePrice = null): void
            {
                $rates = $this->selling_prices;
                if (is_string($rates)) {
                    $rates = json_decode($rates, true);
                }
                $hadRates = is_array($rates);
                if (!is_array($rates)) {
                    $rates = [];
                }

                $mainPrice = (float) ($this->price ?? 0);
                $target = ($ratePrice === null || $ratePrice === '') ? $mainPrice : (float) $ratePrice;
                $isMain = abs($target - $mainPrice) < 0.0001;

                if (!$isMain) {
                    $matched = false;
                    foreach ($rates as $index => $rate) {
                        if (!is_array($rate) || abs((float) ($rate['price'] ?? 0) - $target) >= 0.0001) {
                            continue;
                        }
                        if (abs((float) ($rate['price'] ?? 0) - $mainPrice) < 0.0001) {
                            $isMain = true;
                            break;
                        }
                        $current = array_key_exists('qty', $rate) ? (float) $rate['qty'] : 0;
                        // Allow negative rate stock when overselling.
                        $rates[$index]['qty'] = $current + $delta;
                        $matched = true;
                        break;
                    }
                    if (!$matched) {
                        $isMain = true;
                    }
                }

                if ($isMain) {
                    $this->quantity = (float) $this->quantity + $delta;
                    foreach ($rates as $index => $rate) {
                        if (!is_array($rate)) {
                            continue;
                        }
                        if (abs((float) ($rate['price'] ?? 0) - $mainPrice) < 0.0001) {
                            $rates[$index]['qty'] = (float) $this->quantity;
                        }
                    }
                }

                if ($hadRates || $rates !== []) {
                    $this->selling_prices = $rates;
                }
            }

            /**
             * Stock currently held for a selling rate.
             */
            public function rateStockValue($ratePrice = null): float
            {
                $mainPrice = (float) ($this->price ?? 0);
                $target = ($ratePrice === null || $ratePrice === '') ? $mainPrice : (float) $ratePrice;
                if (abs($target - $mainPrice) < 0.0001) {
                    return (float) $this->quantity;
                }

                $rates = $this->selling_prices;
                if (is_string($rates)) {
                    $rates = json_decode($rates, true);
                }
                if (!is_array($rates)) {
                    return (float) $this->quantity;
                }

                foreach ($rates as $rate) {
                    if (!is_array($rate) || abs((float) ($rate['price'] ?? 0) - $target) >= 0.0001) {
                        continue;
                    }
                    if (abs((float) ($rate['price'] ?? 0) - $mainPrice) < 0.0001) {
                        return (float) $this->quantity;
                    }

                    return array_key_exists('qty', $rate) ? (float) $rate['qty'] : 0;
                }

                return (float) $this->quantity;
            }

            /**
             * Assign SKU 1, 2, 3… by product id (oldest first) for one branch.
             *
             * @return array{updated:int,total:int,from:int,to:int,next_sku:string}
             */
            public static function resequenceBranchSkus(int $branchId, int $startFrom = 1): array
            {
                $products = static::query()
                    ->where('branch_id', $branchId)
                    ->where('isDeleted', 0)
                    ->orderBy('id')
                    ->get(['id', 'SKU']);

                $total   = $products->count();
                $updated = 0;

                if ($total === 0) {
                    return [
                        'updated'  => 0,
                        'total'    => 0,
                        'from'     => 0,
                        'to'       => 0,
                        'next_sku' => (string) $startFrom,
                    ];
                }

                DB::transaction(function () use ($products, $startFrom, &$updated) {
                    foreach ($products as $product) {
                        static::query()
                            ->where('id', $product->id)
                            ->update(['SKU' => '__reseq_' . $product->id]);
                    }

                    $sequence = $startFrom;
                    foreach ($products as $product) {
                        $newSku = (string) $sequence++;
                        if ((string) $product->SKU !== $newSku) {
                            $updated++;
                        }
                        static::query()
                            ->where('id', $product->id)
                            ->update(['SKU' => $newSku]);
                    }
                });

                $from = $startFrom;
                $to   = $startFrom + $total - 1;

                return [
                    'updated'  => $updated,
                    'total'    => $total,
                    'from'     => $from,
                    'to'       => $to,
                    'next_sku' => (string) ($to + 1),
                ];
            }

            public static function formatLabelAmount($value): string
            {
                if ($value === null || $value === '') {
                    return '';
                }
                $n = round((float) $value, 2);
                if (abs($n - round($n)) < 0.001) {
                    return (string) (int) round($n);
                }
                return number_format($n, 2, '.', '');
            }

            /**
             * Amount string used inside barcode wholesale footer (no 11 prefix).
             */
            public static function wholesaleAmountString(float $value): string
            {
                $amount = self::formatLabelAmount($value);
                if (str_contains($amount, '.')) {
                    $amount = rtrim(rtrim($amount, '0'), '.');
                }

                return $amount;
            }

            /**
             * Parse a wholesale amount. The typed price is kept as-is.
             * 111115 stays 111115 (it is not treated as an 11-prefix code).
             */
            public static function parseBarcodeWholesaleValue(?string $labelReference): ?float
            {
                $raw = trim((string) $labelReference);
                if ($raw === '') {
                    return null;
                }

                $numeric = (float) preg_replace('/[^\d.]/', '', $raw);

                return $numeric > 0 ? $numeric : null;
            }

            /**
             * Barcode footer wholesale display.
             * The printed code is always 11 plus the wholesale amount: 117 → 11117, 1105 → 111105.
             */
            public static function formatBarcodeWholesaleRef(?string $labelReference, bool $showOnBarcode = true): string
            {
                if (! $showOnBarcode) {
                    return '';
                }

                $value = self::parseBarcodeWholesaleValue($labelReference);
                if ($value === null || $value <= 0) {
                    return '';
                }

                return '11'.self::wholesaleAmountString($value);
            }

            public static function nullableMoney($value): ?float
            {
                if ($value === null || $value === '') {
                    return null;
                }
                return (float) $value;
            }

            public function scopeMatchingSearch($query, ?string $search, bool $includeCategory = true)
            {
                $search = trim((string) $search);
                // Empty term must not match everything (LIKE '%%' / unfiltered catalog)
                if ($search === '') {
                    return $query->whereRaw('1 = 0');
                }

                $words = preg_split('/\s+/u', $search, -1, PREG_SPLIT_NO_EMPTY);
                if (! is_array($words) || $words === []) {
                    return $query->whereRaw('1 = 0');
                }

                foreach ($words as $word) {
                    $word = trim((string) $word);
                    if ($word === '') {
                        continue;
                    }
                    $query->where(function ($subQuery) use ($word, $includeCategory) {
                        static::applySingleProductSearchTerm($subQuery, $word, $includeCategory);
                    });
                }

                return $query;
            }

            protected static function applySingleProductSearchTerm($query, string $term, bool $includeCategory = true): void
            {
                $normalizedTerm = preg_replace('/\s+/', '', $term) ?: $term;
                $likeTerm       = '%' . addcslashes($term, '%_\\') . '%';
                $likeNormalized = '%' . addcslashes($normalizedTerm, '%_\\') . '%';

                $query->where(function ($subQuery) use ($term, $normalizedTerm, $likeTerm, $likeNormalized, $includeCategory) {
                    $subQuery->where('name', 'LIKE', $likeTerm)
                        // "moti kada" / "kada" also matches names like MOTIKADA (no spaces)
                        ->orWhereRaw("REPLACE(IFNULL(name, ''), ' ', '') LIKE ?", [$likeNormalized])
                        ->orWhere('SKU', 'LIKE', $likeTerm)
                        ->orWhere('SKU', 'LIKE', $likeNormalized)
                        ->orWhere('product_code', 'LIKE', $likeTerm)
                        ->orWhere('product_code', 'LIKE', $likeNormalized)
                        ->orWhere('barcode', 'LIKE', $likeTerm)
                        ->orWhere('barcode', 'LIKE', $likeNormalized)
                        ->orWhere('barcode', $term)
                        ->orWhere('barcode', $normalizedTerm)
                        ->orWhereRaw("FIND_IN_SET(?, REPLACE(IFNULL(barcode, ''), ' ', '')) > 0", [$normalizedTerm])
                        ->orWhereHas('variants', function ($variantQuery) use ($likeTerm, $likeNormalized, $normalizedTerm) {
                            $variantQuery->where('barcode', 'LIKE', $likeTerm)
                                ->orWhere('barcode', 'LIKE', $likeNormalized)
                                ->orWhereRaw("FIND_IN_SET(?, REPLACE(IFNULL(barcode, ''), ' ', '')) > 0", [$normalizedTerm]);
                        });

                    if ($includeCategory) {
                        $subQuery->orWhereHas('category', function ($categoryQuery) use ($likeTerm, $likeNormalized) {
                            $categoryQuery->where('name', 'LIKE', $likeTerm)
                                ->orWhereRaw("REPLACE(IFNULL(name, ''), ' ', '') LIKE ?", [$likeNormalized]);
                        });
                    }
                });
            }

            // Relationship with Brand
            public function brand()
            {
                return $this->belongsTo(Brand::class, 'brand_id');
            }

            // protected static function booted()
            // {
            //     static::creating(function ($product) {
            //         do {
            //             $barcode = 'PRD' . mt_rand(1000000000, 9999999999); // 13-char barcode
            //         } while (Product::where('barcode', $barcode)->exists());

            //         $product->barcode = $barcode;
            //     });
            // }
            protected static function booted()
            {
                static::creating(function ($product) {
                    if (empty($product->barcode)) {
                        do {
                            $barcode = (string) mt_rand(10000, 99999);
                        } while (Product::where('barcode', $barcode)->exists());

                        $product->barcode = $barcode;
                    }
                });
            }
            public function getImageUrlAttribute()
            {
                $images = $this->images;

                if ($images) {
                    $decoded = json_decode($images, true);

                    if (is_array($decoded)) {
                        return array_map(function ($img) {
                            return image_path('storage/' . ltrim($img, '/'));
                        }, array_filter($decoded));
                    }

                    return [image_path('storage/' . ltrim($images, '/'))];
                }

                return [image_path('admin/assets/img/product/noimage.png')];
            }

            public function product_inventory()
            {
                return $this->hasOne(ProductInventory::class, 'product_id');
            }

            public function activeRentals()
            {
                $now = \Carbon\Carbon::now();
                return $this->hasMany(OrderItem::class, 'product_id')
                    ->where('is_rental', 1)
                    ->where('rent_start_at', '<=', $now)
                    ->where('rent_end_at', '>=', $now)
                    ->whereHas('order', function ($q) {
                        $q->where('isDeleted', 0);
                    })
                    ->with(['order.user']);
            }

            protected static function boot()
            {
                parent::boot();

                static::creating(function ($model) {
                    $model->created_at = Carbon::now('Asia/Kolkata');
                    $model->updated_at = Carbon::now('Asia/Kolkata');
                });

            static::updating(function ($model) {
                $model->updated_at = Carbon::now('Asia/Kolkata');
            });

            static::updating(function ($product) {
                $trackedFields = ['barcode', 'price', 'mrp', 'batch_no', 'label_code', 'label_reference', 'quantity', 'unit_id', 'barcode_details', 'tax_rate', 'sale_discount', 'discount_type', 'minimum_stock_qty'];
                $changed = false;

                foreach ($trackedFields as $field) {
                    if ($product->isDirty($field)) {
                        $changed = true;
                        break;
                    }
                }

                if ($changed && !static::$skipBarcodeHistory) {
                    $historyData = ['product_id' => $product->id, 'changed_by' => auth()->id()];
                    
                    $directMapping = [
                        'barcode' => 'barcode',
                        'price' => 'price',
                        'mrp' => 'mrp',
                        'batch_no' => 'batch_no',
                        'label_code' => 'label_code',
                        'label_reference' => 'label_reference',
                        'barcode_details' => 'barcode_details',
                        'tax_rate' => 'tax_rate',
                        'sale_discount' => 'sale_discount',
                        'discount_type' => 'discount_type',
                        'minimum_stock_qty' => 'minimum_stock_qty'
                    ];

                    foreach ($directMapping as $modelField => $dbField) {
                        $historyData["old_{$dbField}"] = $product->getOriginal($modelField);
                        $historyData["new_{$dbField}"] = $product->{$modelField};
                    }

                    $historyData['old_qty'] = $product->getOriginal('quantity');
                    $historyData['new_qty'] = $product->quantity;

                    $oldUnit = \App\Models\Unit::find($product->getOriginal('unit_id'));
                    $newUnit = \App\Models\Unit::find($product->unit_id);
                    $historyData['old_unit'] = $oldUnit ? $oldUnit->unit_name : null;
                    $historyData['new_unit'] = $newUnit ? $newUnit->unit_name : null;

                    ProductBarcodeHistory::create($historyData);
                }
            });
        }

        public function barcodeHistories()
        {
            return $this->hasMany(ProductBarcodeHistory::class)->latest();
        }
    }
