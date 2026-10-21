<?php

if (! function_exists('canUseBranches')) {
    /**
     * Determine whether the authenticated user's plan allows multiple branches.
     * Returns true if branch_limit > 1 (or null, meaning unlimited).
     * Returns false if branch_limit is exactly 1 (Starter plan).
     */
    function canUseBranches(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $user = auth()->user();
        $plan = $user->plan;

        // No plan assigned → allow by default (don't lock out unassigned users)
        if (! $plan) {
            return true;
        }

        // null means unlimited; any value > 1 allows branches
        return $plan->branch_limit === null || $plan->branch_limit > 1;
    }
}

if (! function_exists('image_path_base')) {
    /**
     * Base URL for public assets (always ends with /).
     */
    function image_path_base(): string
    {
        static $cached = null;

        if ($cached !== null) {
            return $cached;
        }

        try {
            if (function_exists('app') && app()->bound('config')) {
                $configured = config('app.image_path');
                if (is_string($configured) && $configured !== '') {
                    return $cached = rtrim($configured, '/').'/';
                }

                $appUrl = config('app.url');
                if (is_string($appUrl) && $appUrl !== '') {
                    return $cached = rtrim($appUrl, '/').'/';
                }
            }
        } catch (\Throwable) {
            // Application not booted yet.
        }

        $raw = env('ImagePath') ?: env('IMAGE_PATH') ?: env('APP_URL', '');

        return $cached = ($raw !== '' ? rtrim((string) $raw, '/').'/' : '/');
    }
}

if (! function_exists('image_path')) {
    /**
     * Build a full URL to a file under public/ (e.g. admin/assets/... or storage/...).
     */
    function image_path(?string $path = ''): string
    {
        $base = image_path_base();

        if ($path === null || $path === '') {
            return $base;
        }

        return $base.ltrim($path, '/');
    }
}

if (! function_exists('resolve_local_storage_path')) {
    /**
     * Resolve a stored relative path to an absolute local file path for PDFs/exports.
     */
    function resolve_local_storage_path(?string $relativePath): ?string
    {
        $relativePath = trim((string) $relativePath);
        if ($relativePath === '') {
            return null;
        }

        $relativePath = preg_replace('#^https?://[^/]+/#', '', $relativePath);
        $relativePath = ltrim($relativePath, '/');
        $relativePath = preg_replace('#^storage/#', '', $relativePath);

        $candidates = [
            storage_path('app/public/'.$relativePath),
            public_path('storage/'.$relativePath),
            public_path($relativePath),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}

if (! function_exists('local_image_to_base64')) {
    /**
     * Convert a local image file to a base64 data URI for DomPDF rendering.
     */
    function local_image_to_base64(?string $relativePath, ?string $fallbackPath = null): ?string
    {
        $filePath = resolve_local_storage_path($relativePath);

        if (! $filePath && $fallbackPath) {
            $filePath = resolve_local_storage_path($fallbackPath);
        }

        if (! $filePath) {
            return null;
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeMap = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'svg'  => 'image/svg+xml',
        ];

        $mime = $mimeMap[$extension] ?? null;
        if (! $mime && function_exists('mime_content_type')) {
            $mime = @mime_content_type($filePath) ?: null;
        }
        $mime ??= 'image/jpeg';

        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($filePath));
    }
}


if (! function_exists('getEnabledPaymentMethods')) {
    /**
     * Get enabled payment methods.
     * Pay Later is always enabled.
     */
    function getEnabledPaymentMethods($branchId = null): array
    {
        try {
            // Always keep Pay Later enabled
            $enabledMethods = ['pay_later'];

            if (! auth()->check()) {
                return $enabledMethods;
            }

            $user = auth()->user();

            if (!$branchId) {
                $userRole   = $user->role ?? '';
                $userId     = $user->id ?? null;
                $userBranchId = $user->branch_id ?? null;
                $subAdminId = session('selectedSubAdminId');

                if ($userRole === 'sub-admin' && $userId) {
                    $branchId = $userId;
                } elseif ($userRole === 'admin' && ! empty($subAdminId)) {
                    $branchId = $subAdminId;
                } elseif ($userRole === 'staff' && $userBranchId) {
                    $branchId = $userBranchId;
                } else {
                    $branchId = $userId;
                }
            }

            $setting = \App\Models\Setting::where('branch_id', $branchId)->first();

            if ($setting && ! empty($setting->payment_methods)) {

                $methods = is_array($setting->payment_methods)
                    ? $setting->payment_methods
                    : [];

                $enabledMethods = array_merge($enabledMethods, $methods);
            }

            return array_unique(array_map('strtolower', $enabledMethods));

        } catch (\Throwable $e) {

            return ['pay_later'];
        }
    }
}

if (! function_exists('isPaymentMethodEnabled')) {
    /**
     * Check if a specific payment method is enabled for the authenticated user's branch.
     */
    function isPaymentMethodEnabled(string $method, $branchId = null): bool
    {
        $enabledMethods = getEnabledPaymentMethods($branchId);
        return in_array(strtolower($method), array_map('strtolower', $enabledMethods), true);
    }
}

if (! function_exists('thermal_invoice_paper')) {
    /**
     * DomPDF paper box for ~76mm thermal receipts (tight height for short bills).
     *
     * @param  int|iterable  $itemCountOrItems
     * @return array{0:float,1:float,2:float,3:float}
     */
    function thermal_invoice_paper($itemCountOrItems = 1): array
    {
        $itemCount = is_countable($itemCountOrItems)
            ? count($itemCountOrItems)
            : max(1, (int) $itemCountOrItems);

        $widthPt = 226.77; // 80mm (3 inch roll)
        // Keep height close to content so thermal printers don't feed blank paper.
        $heightMm = 48 + ($itemCount * 7.5) + 26;
        $heightMm = max(72, min(2800, $heightMm)) + 4;
        $heightPt = $heightMm * 2.83465;

        return [0, 0, $widthPt, $heightPt];
    }
}

if (! function_exists('make_thermal_invoice_pdf')) {
    /**
     * Build a thermal invoice PDF whose page height matches content (minimal blank tail).
     *
     * @param  int|iterable  $itemCountOrItems
     * @return \Barryvdh\DomPDF\PDF
     */
    function make_thermal_invoice_pdf(string $view, array $data, $itemCountOrItems = 1)
    {
        $inv = $data['inv'] ?? invoice_custom($data['setting'] ?? null);
        $widthPt = thermal_invoice_width_pt($inv['paper_size'] ?? '3inch');
        $options = [
            'isHtml5ParserEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ];

        // Pass 1: render on a tall canvas and measure the body frame height.
        $probe = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, $data)
            ->setPaper([0, 0, $widthPt, 4000], 'portrait')
            ->setOptions($options);

        $dompdf = $probe->getDomPDF();
        $measuredHeight = 0.0;
        $dompdf->setCallbacks([
            [
                'event' => 'end_frame',
                'f' => function ($frame, $canvas, $fontMetrics) use (&$measuredHeight) {
                    $node = $frame->get_node();
                    if ($node && strtolower((string) $node->nodeName) === 'body') {
                        $box = $frame->get_padding_box();
                        $measuredHeight = max($measuredHeight, (float) ($box['h'] ?? 0));
                    }
                },
            ],
        ]);
        $dompdf->render();

        if ($measuredHeight > 40) {
            // Small pad so the last line is not clipped; avoid large blank feed.
            $heightPt = min(8000, max(180, $measuredHeight + 10));
        } else {
            $paper = thermal_invoice_paper($itemCountOrItems);
            $heightPt = $paper[3];
        }

        return \Barryvdh\DomPDF\Facade\Pdf::loadView($view, $data)
            ->setPaper([0, 0, $widthPt, $heightPt], 'portrait')
            ->setOptions($options);
    }
}

if (! function_exists('default_invoice_customization')) {
    /**
     * BillingFast-style sales bill print options (applied on thermal templates 1–5).
     */
    function default_invoice_customization(): array
    {
        return [
            // Basic / header
            'show_logo' => true,
            'logo_size' => 1.0,
            'show_shop_gst' => true,
            'show_fssai' => false,
            'show_customer_gst' => false,
            'show_salesman' => false,
            'show_customer_name' => true,
            'show_customer_phone' => true,
            'show_customer_address' => true,
            'show_customer_message' => false,
            'enable_custom_heading' => false,
            'custom_heading_text' => '',

            // Template
            'preferred_template' => 1, // 1 = cash-sale bill, selected when print opens
            'language' => 'english',
            'font_size' => 8,
            'left_margin_mm' => 0,
            'print_copies' => 1,
            'paper_size' => '3inch', // 2inch | 3inch

            // Items
            'show_serial' => true,
            'show_item_barcode' => false,
            'show_item_qty' => true,
            'show_item_description' => false,
            'show_hsn' => false,
            'show_item_mrp' => true,
            'show_item_sp' => true,
            'sp_column_label' => 'SP',
            'sp_price_source' => 'selling_price', // selling_price | mrp
            'show_item_total' => true,

            // Footer
            'show_payment_mode' => true,
            'show_upi_qr' => true,
            'show_invoice_barcode' => false,
            'show_savings' => true,
            'savings_label' => 'You Saved',
            'enable_terms' => false,

            // Advanced
            'product_name_wrap' => true,
            'product_second_line' => false,
            'savings_in_line_item' => false,
            'show_total_item_count' => true,
            'merge_product_items' => false,
            'truncate_product_names' => false,
            'instant_billing_mode' => true,
            'enable_gst_billing' => false,
            'decimal_places' => 2,
            'round_off_quantity' => false,
            'show_customer_outstanding' => true,

            // Invoice Print page (thermal tab)
            'show_company_name' => true,
            'show_shop_phone' => true,
            'show_shop_address' => false,
            'show_shop_email' => false,
            'bill_of_supply' => false,
            'show_unit' => true,
            'show_received' => true,
            'show_balance' => true,
            'show_tax_details' => false,
            'show_amount_words' => false,
            'amount_grouping' => true,
            'amount_words_format' => 'indian', // indian | international
            'show_remarks' => false,
            'extra_end_lines' => 0,
            'text_styling' => true,
            'auto_cut' => false,
            'open_cash_drawer' => false,
            'default_printer' => '',
            'default_printer_label' => '',
            'thermal_theme' => 0, // 0 = not chosen, print dialog keeps T1
        ];
    }
}

if (! function_exists('default_invoice_print_settings')) {
    /**
     * Regular printer (A4 / A5) options from the Invoice Print settings page.
     * Defaults reproduce the A4 bill as it printed before these options existed.
     */
    function default_invoice_print_settings(): array
    {
        return [
            'theme_color' => '#ff9f43',
            'text_size' => 'medium', // small | medium | large
            'page_size' => 'a4', // a4 | a5
            'orientation' => 'portrait', // portrait | landscape
            'warn_unsaved' => true,

            'show_company_name' => true,
            'company_name_size' => 'large', // small | medium | large | xlarge
            'show_logo' => true,
            'show_watermark' => true,
            'show_address' => true,
            'show_email' => true,
            'show_phone' => true,
            'show_gstin' => true,
            'bill_of_supply' => false,
            'extra_top_space' => 0,
            'print_original_duplicate' => false,

            'expand_table' => false,
            'min_item_rows' => 0,
            'show_total_qty' => false,
            'amount_decimal' => true,
            'show_received' => true,
            'show_balance' => true,
            'show_party_balance' => false,
            'show_tax_details' => false,
            'amount_grouping' => true,
            'amount_words_format' => 'indian', // indian | international
            'show_amount_words' => true,
            'show_you_saved' => false,
            'show_bank_details' => true,
            'show_qr' => true,

            'show_description' => true,
            'show_terms' => false,
            'show_received_by' => false,
            'show_delivered_by' => false,
            'show_signature_text' => true,
            'signature_text' => 'Authorized Signatory',
            'show_payment_mode' => true,
            'show_acknowledgement' => false,
        ];
    }
}

if (! function_exists('invoice_print')) {
    /**
     * Merge saved regular printer options with defaults.
     *
     * @param  mixed  $setting
     */
    function invoice_print($setting = null): array
    {
        $defaults = default_invoice_print_settings();
        $saved = [];
        if ($setting && is_object($setting) && ! empty($setting->invoice_print_settings)) {
            $saved = is_array($setting->invoice_print_settings)
                ? $setting->invoice_print_settings
                : (json_decode((string) $setting->invoice_print_settings, true) ?: []);
        } elseif (is_array($setting)) {
            $saved = $setting;
        }

        return array_replace($defaults, array_intersect_key($saved, $defaults));
    }
}

if (! function_exists('invoice_regular_paper')) {
    /**
     * DomPDF paper for the regular (A4 / A5) bill.
     *
     * @return array{0:string,1:string}
     */
    function invoice_regular_paper($setting = null): array
    {
        $ip = invoice_print($setting);

        return [
            ($ip['page_size'] ?? 'a4') === 'a5' ? 'A5' : 'A4',
            ($ip['orientation'] ?? 'portrait') === 'landscape' ? 'landscape' : 'portrait',
        ];
    }
}

if (! function_exists('invoice_settings_branch_id')) {
    /**
     * Branch whose settings row the signed-in user edits (same rules as payment methods).
     */
    function invoice_settings_branch_id()
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }
        $subAdminId = session('selectedSubAdminId');
        if (($user->role ?? '') === 'sub-admin') {
            return $user->id;
        }
        if (($user->role ?? '') === 'admin' && ! empty($subAdminId)) {
            return $subAdminId;
        }
        if (($user->role ?? '') === 'staff' && ! empty($user->branch_id)) {
            return $user->branch_id;
        }

        return $user->id;
    }
}

if (! function_exists('invoice_settings_row')) {
    /**
     * Settings row used for printing: the branch row, else the first row (same fallback as the invoice PDF).
     */
    function invoice_settings_row()
    {
        $branchId = invoice_settings_branch_id();
        $row = $branchId ? \App\Models\Setting::where('branch_id', $branchId)->first() : null;

        return $row ?: \App\Models\Setting::first();
    }
}

if (! function_exists('invoice_print_js_prefs')) {
    /**
     * Printer defaults handed to the print dialog script.
     */
    function invoice_print_js_prefs(): array
    {
        try {
            $setting = invoice_settings_row();
            $inv = invoice_custom($setting);

            return [
                'thermal' => ($setting->invoice_size ?? 'big') === 'small',
                'copies' => max(1, min(5, (int) ($inv['print_copies'] ?? 1))),
                'autoCut' => ! empty($inv['auto_cut']),
                'cashDrawer' => ! empty($inv['open_cash_drawer']),
                'extraLines' => max(0, min(10, (int) ($inv['extra_end_lines'] ?? 0))),
                'textStyling' => ! empty($inv['text_styling']),
                'defaultPrinter' => (string) ($inv['default_printer'] ?? ''),
                'defaultPrinterLabel' => (string) ($inv['default_printer_label'] ?? ''),
                'hindi' => ($inv['language'] ?? 'english') === 'hindi',
                'template' => max(1, min(5, (int) ($inv['thermal_theme'] ?? 0) ?: 1)),
            ];
        } catch (\Throwable $e) {
            return ['thermal' => false, 'copies' => 1, 'autoCut' => false, 'cashDrawer' => false, 'extraLines' => 0,
                'textStyling' => true, 'defaultPrinter' => '', 'defaultPrinterLabel' => '', 'hindi' => false, 'template' => 1];
        }
    }
}

if (! function_exists('invoice_money')) {
    /**
     * Amount text for printed bills: decimals, digit grouping and Indian (1,00,000) or international (100,000) style.
     */
    function invoice_money($amount, int $decimals = 2, bool $grouping = true, string $format = 'indian'): string
    {
        $num = (float) $amount;
        $negative = $num < 0;
        $parts = explode('.', number_format(abs($num), max(0, $decimals), '.', ''));
        $whole = $parts[0];
        if ($grouping && strlen($whole) > 3) {
            if ($format === 'international') {
                $whole = number_format((float) $whole, 0, '.', ',');
            } else {
                $lastThree = substr($whole, -3);
                $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($whole, 0, -3));
                $whole = $rest.','.$lastThree;
            }
        }
        $text = $whole.(isset($parts[1]) ? '.'.$parts[1] : '');

        return ($negative ? '-' : '').$text;
    }
}

if (! function_exists('invoice_number_words')) {
    function invoice_number_words(int $number, string $format = 'indian'): string
    {
        $ones = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
            'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
        $tens = [2 => 'twenty', 3 => 'thirty', 4 => 'forty', 5 => 'fifty', 6 => 'sixty', 7 => 'seventy', 8 => 'eighty', 9 => 'ninety'];

        if ($number < 0) {
            return 'minus '.invoice_number_words(abs($number), $format);
        }
        if ($number < 20) {
            return $ones[$number];
        }
        if ($number < 100) {
            return $tens[intdiv($number, 10)].($number % 10 ? '-'.$ones[$number % 10] : '');
        }
        if ($number < 1000) {
            return $ones[intdiv($number, 100)].' hundred'.($number % 100 ? ' and '.invoice_number_words($number % 100, $format) : '');
        }
        $scales = $format === 'international'
            ? [1000000000 => 'billion', 1000000 => 'million', 1000 => 'thousand']
            : [10000000 => 'crore', 100000 => 'lakh', 1000 => 'thousand'];
        foreach ($scales as $divisor => $label) {
            if ($number >= $divisor) {
                $rest = $number % $divisor;

                return invoice_number_words(intdiv($number, $divisor), $format).' '.$label
                    .($rest ? ($rest < 100 ? ' and ' : ' ').invoice_number_words($rest, $format) : '');
            }
        }

        return (string) $number;
    }
}

if (! function_exists('invoice_amount_words')) {
    function invoice_amount_words($amount, string $format = 'indian'): string
    {
        $amount = round((float) $amount, 2);
        $rupees = (int) floor(abs($amount));
        $paise = (int) round((abs($amount) - $rupees) * 100);
        $text = ucwords(invoice_number_words($rupees, $format)).' Rupees';
        if ($paise > 0) {
            $text .= ' and '.ucwords(invoice_number_words($paise, $format)).' Paise';
        }

        return $text.' Only';
    }
}

if (! function_exists('customer_outstanding_balance')) {
    /**
     * Unpaid amount across all of a customer's sales (after returns), as shown on the sales list.
     */
    function customer_outstanding_balance($userId): float
    {
        if (empty($userId)) {
            return 0.0;
        }
        try {
            $orders = \App\Models\Order::where('user_id', $userId)
                ->where('isDeleted', 0)
                ->where(function ($q) {
                    $q->whereNull('quotation_status')->orWhere('quotation_status', '!=', 'quotation');
                })
                ->withSum(['payments as total_paid' => function ($q) {
                    $q->where('isDeleted', 0);
                }], 'payment_amount')
                ->withSum('returns as total_return', 'total_amount')
                ->get(['id', 'total_amount']);

            $due = 0.0;
            foreach ($orders as $order) {
                $due += max(0, (float) $order->total_amount - (float) $order->total_return - (float) $order->total_paid);
            }

            return round($due, 2);
        } catch (\Throwable $e) {
            return 0.0;
        }
    }
}

if (! function_exists('invoice_custom')) {
    /**
     * Merge saved invoice customization with defaults.
     *
     * @param  mixed  $setting
     */
    function invoice_custom($setting = null): array
    {
        $defaults = default_invoice_customization();
        $saved = [];
        if ($setting && is_object($setting) && ! empty($setting->invoice_customization)) {
            $saved = is_array($setting->invoice_customization)
                ? $setting->invoice_customization
                : (json_decode((string) $setting->invoice_customization, true) ?: []);
        } elseif (is_array($setting)) {
            $saved = $setting;
        }

        return array_replace($defaults, array_intersect_key($saved, $defaults));
    }
}

if (! function_exists('text_has_devanagari')) {
    function text_has_devanagari(?string $text): bool
    {
        return is_string($text) && $text !== '' && preg_match('/\p{Devanagari}/u', $text) === 1;
    }
}

if (! function_exists('invoice_line_product_name')) {
    /**
     * Product label for a bill line. Hindi uses the saved name, same as barcode print.
     *
     * @param  mixed  $product
     */
    function invoice_line_product_name($product, bool $printHindi): string
    {
        $english = 'Product';
        $hindi = '';
        if (is_object($product)) {
            $english = trim((string) ($product->name ?? '')) ?: 'Product';
            $hindi = trim((string) ($product->name_hindi ?? ''));
        } elseif (is_string($product) && trim($product) !== '') {
            $english = trim($product);
        }
        if ($printHindi && text_has_devanagari($hindi)) {
            return $hindi;
        }

        return $english;
    }
}

if (! function_exists('ensure_product_hindi_name')) {
    /**
     * Fill products.name_hindi when Print in Hindi is used and the name is not saved yet.
     *
     * @param  mixed  $product
     */
    function ensure_product_hindi_name($product): void
    {
        if (! is_object($product) || empty($product->id)) {
            return;
        }

        $stored = \Illuminate\Support\Facades\DB::table('products')
            ->where('id', $product->id)
            ->value('name_hindi');
        if (text_has_devanagari(is_string($stored) ? $stored : null)) {
            $product->name_hindi = $stored;

            return;
        }

        $english = trim((string) ($product->name ?? ''));
        if ($english === '' || mb_strlen($english) > 300) {
            return;
        }
        $cacheKey = 'product_name_hi_v2_'.md5($english);
        $hindi = \Illuminate\Support\Facades\Cache::get($cacheKey);
        if (! text_has_devanagari(is_string($hindi) ? $hindi : null)) {
            $hindi = translate_english_to_hindi($english);
            if (text_has_devanagari($hindi)) {
                \Illuminate\Support\Facades\Cache::put($cacheKey, $hindi, now()->addDays(30));
            }
        }
        if (! text_has_devanagari(is_string($hindi) ? $hindi : null)) {
            return;
        }
        $product->name_hindi = $hindi;
        \Illuminate\Support\Facades\DB::table('products')
            ->where('id', $product->id)
            ->update([
                'name_hindi' => $hindi,
                'updated_at' => now(),
            ]);
    }
}

if (! function_exists('translate_english_to_hindi')) {
    function translate_english_to_hindi(string $text): ?string
    {
        foreach (['translate_via_edge', 'translate_via_mymemory', 'translate_via_google'] as $method) {
            $hindi = $method($text);
            if (text_has_devanagari($hindi)) {
                return $hindi;
            }
        }

        return null;
    }
}

if (! function_exists('translate_via_edge')) {
    function translate_via_edge(string $text): ?string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(6)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'Mozilla/5.0',
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
}

if (! function_exists('translate_via_mymemory')) {
    function translate_via_mymemory(string $text): ?string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(6)->get('https://api.mymemory.translated.net/get', [
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
}

if (! function_exists('translate_via_google')) {
    function translate_via_google(string $text): ?string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(4)
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
            $translated = trim(implode('', $parts));

            return $translated !== '' ? $translated : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}

if (! function_exists('invoice_thermal_view_data')) {
    /**
     * Layout values for thermal invoice templates.
     * Must be extracted in the parent view. An @include keeps its own scope,
     * so variables set only inside the partial are missing on the invoice page.
     *
     * @param  array<string, mixed>  $view
     * @return array<string, mixed>
     */
    function invoice_thermal_view_data(array $view): array
    {
        $setting = $view['setting'] ?? null;
        $sales = $view['sales'] ?? null;
        $orderItems = $view['orderItems'] ?? [];
        $user = $view['user'] ?? null;
        $allPayments = $view['allPayments'] ?? null;
        $inv = (isset($view['inv']) && is_array($view['inv'])) ? $view['inv'] : invoice_custom($setting);
        $dp = isset($view['dp']) ? (int) $view['dp'] : max(0, min(4, (int) ($inv['decimal_places'] ?? 2)));
        $is2inch = in_array(strtolower((string) ($inv['paper_size'] ?? '3inch')), ['2inch', '2'], true);
        $paperWmm = $is2inch ? 58 : 80;
        $wrapWmm = $is2inch ? 54 : 76;
        $fontPx = max(6, min(14, (int) ($inv['font_size'] ?? 8)));
        $leftMarginMm = max(0, min(10, (float) ($inv['left_margin_mm'] ?? 0)));
        $spLabel = trim((string) ($inv['sp_column_label'] ?? 'SP')) ?: 'SP';
        $useMrpAsSp = (($inv['sp_price_source'] ?? 'selling_price') === 'mrp');
        $nameWrapStyle = ! empty($inv['product_name_wrap'])
            ? 'word-wrap:break-word;overflow-wrap:break-word;word-break:break-word;'
            : 'white-space:nowrap;overflow:hidden;text-overflow:ellipsis;';

        $logoBase64 = null;
        if (! empty($inv['show_logo']) && ! empty($setting)) {
            $logoBase64 = local_image_to_base64($setting->logo ?? null);
        }
        $logoHeightPx = (int) max(16, min(80, round(28 * (float) ($inv['logo_size'] ?? 1))));

        $qrBase64 = null;
        if (! empty($inv['show_upi_qr']) && ! empty($setting)) {
            $qrPath = null;
            if (! empty($sales->custom_qr_code ?? null) && file_exists(storage_path('app/public/qr_codes/'.$sales->custom_qr_code))) {
                $qrPath = storage_path('app/public/qr_codes/'.$sales->custom_qr_code);
            } elseif (! empty($setting->qr_code) && file_exists(storage_path('app/public/'.$setting->qr_code))) {
                $qrPath = storage_path('app/public/'.$setting->qr_code);
            }
            if ($qrPath) {
                $qrMime = function_exists('mime_content_type') ? (@mime_content_type($qrPath) ?: 'image/png') : 'image/png';
                $qrBase64 = 'data:'.$qrMime.';base64,'.base64_encode(file_get_contents($qrPath));
            }
        }

        $invSavingsAmount = 0.0;
        $invSavingsBase = 0.0;
        if (! empty($orderItems)) {
            foreach ($orderItems as $__it) {
                $__mrp = (float) ($__it->mrp ?? $__it->price ?? 0);
                $__price = (float) ($__it->price ?? 0);
                $__qty = (float) ($__it->quantity ?? 0);
                $invSavingsBase += max($__mrp, $__price) * $__qty;
                if ($__mrp > $__price) {
                    $invSavingsAmount += ($__mrp - $__price) * $__qty;
                }
                $invSavingsAmount += (float) ($__it->discount_amount ?? 0);
            }
        }
        $invSavingsPct = $invSavingsBase > 0 ? round(($invSavingsAmount / $invSavingsBase) * 100, 1) : 0;

        $paymentModeLabel = '';
        if (! empty($inv['show_payment_mode'])) {
            $modes = [];
            if (! empty($allPayments) && count($allPayments) > 0) {
                foreach ($allPayments as $__p) {
                    $m = trim((string) ($__p->payment_type ?? $__p->payment_mode ?? $__p->payment_method ?? ''));
                    if ($m !== '') {
                        $modes[] = ucfirst($m);
                    }
                }
            }
            if (empty($modes) && ! empty($sales->payment_method)) {
                $modes[] = ucfirst((string) $sales->payment_method);
            }
            $paymentModeLabel = implode(' + ', array_unique($modes));
        }

        $salesmanName = '';
        if (! empty($inv['show_salesman'])) {
            if (! empty($user) && is_array($user)) {
                $salesmanName = $user['name'] ?? '';
            } elseif (! empty($sales->creator) && is_object($sales->creator)) {
                $salesmanName = $sales->creator->name ?? '';
            } elseif (! empty($sales->createdBy) && is_object($sales->createdBy)) {
                $salesmanName = $sales->createdBy->name ?? '';
            }
        }

        $invoiceBarcodeHtml = '';
        if (! empty($inv['show_invoice_barcode']) && ! empty($sales->order_number)) {
            try {
                if (class_exists('\Milon\Barcode\DNS1D')) {
                    $invoiceBarcodeHtml = (new \Milon\Barcode\DNS1D())->getBarcodeHTML((string) $sales->order_number, 'C128', 1.2, 28);
                } elseif (class_exists('\Picqer\Barcode\BarcodeGeneratorPNG')) {
                    $gen = new \Picqer\Barcode\BarcodeGeneratorPNG();
                    $png = base64_encode($gen->getBarcode((string) $sales->order_number, $gen::TYPE_CODE_128, 1, 28));
                    $invoiceBarcodeHtml = '<img src="data:image/png;base64,'.$png.'" style="height:28px;width:auto;max-width:95%;">';
                }
            } catch (\Throwable $e) {
                $invoiceBarcodeHtml = '';
            }
        }

        $printHindi = ! empty($view['printHindi']);
        $productName = function ($raw) use ($inv, $printHindi) {
            $name = invoice_line_product_name($raw, $printHindi);
            if (! empty($inv['truncate_product_names']) && mb_strlen($name) > 28) {
                return mb_substr($name, 0, 27).'…';
            }

            return $name;
        };

        $invOn = function (string $key) use ($inv) {
            return ! array_key_exists($key, $inv) || ! empty($inv[$key]);
        };
        $invGrouping = $invOn('amount_grouping');
        $invWordsFormat = ($inv['amount_words_format'] ?? 'indian') === 'international' ? 'international' : 'indian';
        $invMoney = function ($amount) use ($dp, $invGrouping, $invWordsFormat) {
            return invoice_money((float) $amount, $dp, $invGrouping, $invWordsFormat);
        };
        $invShowCompanyName = $invOn('show_company_name');
        $invShowShopPhone = $invOn('show_shop_phone');
        $invShowShopAddress = ! empty($inv['show_shop_address']);
        $invShowShopEmail = ! empty($inv['show_shop_email']);
        $invShowUnit = $invOn('show_unit');
        $invShowReceived = $invOn('show_received');
        $invShowBalance = $invOn('show_balance');
        $invBillTitle = ! empty($inv['bill_of_supply']) ? 'Bill of Supply' : null;
        $invTextStyling = $invOn('text_styling');
        $invExtraEndLines = max(0, min(10, (int) ($inv['extra_end_lines'] ?? 0)));
        $invShopAddress = trim((string) ($setting->address ?? ''));
        $invShopEmail = trim((string) ($setting->email ?? ''));

        return compact(
            'invGrouping',
            'invWordsFormat',
            'invMoney',
            'invShowCompanyName',
            'invShowShopPhone',
            'invShowShopAddress',
            'invShowShopEmail',
            'invShowUnit',
            'invShowReceived',
            'invShowBalance',
            'invBillTitle',
            'invTextStyling',
            'invExtraEndLines',
            'invShopAddress',
            'invShopEmail',
            'inv',
            'dp',
            'paperWmm',
            'wrapWmm',
            'fontPx',
            'leftMarginMm',
            'spLabel',
            'useMrpAsSp',
            'nameWrapStyle',
            'logoBase64',
            'logoHeightPx',
            'qrBase64',
            'invSavingsAmount',
            'invSavingsBase',
            'invSavingsPct',
            'paymentModeLabel',
            'salesmanName',
            'invoiceBarcodeHtml',
            'productName',
            'printHindi'
        );
    }
}

if (! function_exists('thermal_invoice_width_pt')) {
    function thermal_invoice_width_pt(string $paperSize = '3inch'): float
    {
        $paperSize = strtolower(trim($paperSize));
        if ($paperSize === '2inch' || $paperSize === '2') {
            return 164.41; // 58mm
        }

        return 226.77; // 80mm / 3 inch roll
    }
}

