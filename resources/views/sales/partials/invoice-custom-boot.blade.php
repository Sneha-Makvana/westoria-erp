{{-- Shared invoice customization bootstrap for thermal templates 1–5 --}}
@php
    if (! isset($paperWmm)) {
        extract(invoice_thermal_view_data(get_defined_vars()));
    }
    $inv = isset($inv) && is_array($inv) ? $inv : invoice_custom($setting ?? null);
    $dp = isset($dp) ? (int) $dp : max(0, min(4, (int) ($inv['decimal_places'] ?? 2)));
    $is2inch = in_array(strtolower((string) ($inv['paper_size'] ?? '3inch')), ['2inch', '2'], true);
    $paperWmm = $is2inch ? 58 : 80;
    $wrapWmm = $is2inch ? 54 : 76;
    $fontPx = max(6, min(14, (int) ($inv['font_size'] ?? 8)));
    // USB / QZ Tray only: slightly larger text on the printed paper (preview + Bluetooth stay normal).
    $usbPrint = request()->boolean('usb_print');
    if ($usbPrint) {
        $fontPx = min(14, $fontPx + 2);
    }
    $leftMarginMm = max(0, min(10, (float) ($inv['left_margin_mm'] ?? 0)));
    $spLabel = trim((string) ($inv['sp_column_label'] ?? 'SP')) ?: 'SP';
    $useMrpAsSp = (($inv['sp_price_source'] ?? 'selling_price') === 'mrp');
    $nameWrapStyle = !empty($inv['product_name_wrap'])
        ? 'word-wrap:break-word;overflow-wrap:break-word;word-break:break-word;'
        : 'white-space:nowrap;overflow:hidden;text-overflow:ellipsis;';

    $logoBase64 = null;
    if (!empty($inv['show_logo']) && !empty($setting)) {
        $logoBase64 = local_image_to_base64($setting->logo ?? null);
    }
    $logoHeightPx = (int) max(16, min(80, round(28 * (float) ($inv['logo_size'] ?? 1))));

    $qrBase64 = null;
    if (!empty($inv['show_upi_qr']) && !empty($setting)) {
        $qrPath = null;
        if (!empty($sales->custom_qr_code ?? null) && file_exists(storage_path('app/public/qr_codes/' . $sales->custom_qr_code))) {
            $qrPath = storage_path('app/public/qr_codes/' . $sales->custom_qr_code);
        } elseif (!empty($setting->qr_code) && file_exists(storage_path('app/public/' . $setting->qr_code))) {
            $qrPath = storage_path('app/public/' . $setting->qr_code);
        }
        if ($qrPath) {
            $qrMime = function_exists('mime_content_type') ? (@mime_content_type($qrPath) ?: 'image/png') : 'image/png';
            $qrBase64 = 'data:' . $qrMime . ';base64,' . base64_encode(file_get_contents($qrPath));
        }
    }

    $invSavingsAmount = 0.0;
    $invSavingsBase = 0.0;
    if (!empty($orderItems)) {
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
    if (!empty($inv['show_payment_mode'])) {
        $modes = [];
        if (!empty($allPayments) && count($allPayments) > 0) {
            foreach ($allPayments as $__p) {
                $m = trim((string) ($__p->payment_type ?? $__p->payment_mode ?? $__p->payment_method ?? ''));
                if ($m !== '') {
                    $modes[] = ucfirst($m);
                }
            }
        }
        if (empty($modes) && !empty($sales->payment_method)) {
            $modes[] = ucfirst((string) $sales->payment_method);
        }
        $paymentModeLabel = implode(' + ', array_unique($modes));
    }

    $salesmanName = '';
    if (!empty($inv['show_salesman'])) {
        if (!empty($user) && is_array($user)) {
            $salesmanName = $user['name'] ?? '';
        } elseif (!empty($sales->creator) && is_object($sales->creator)) {
            $salesmanName = $sales->creator->name ?? '';
        } elseif (!empty($sales->createdBy) && is_object($sales->createdBy)) {
            $salesmanName = $sales->createdBy->name ?? '';
        }
    }

    $invoiceBarcodeHtml = '';
    if (!empty($inv['show_invoice_barcode']) && !empty($sales->order_number)) {
        try {
            if (class_exists('\Milon\Barcode\DNS1D')) {
                $invoiceBarcodeHtml = (new \Milon\Barcode\DNS1D())->getBarcodeHTML((string) $sales->order_number, 'C128', 1.2, 28);
            } elseif (class_exists('\Picqer\Barcode\BarcodeGeneratorPNG')) {
                $gen = new \Picqer\Barcode\BarcodeGeneratorPNG();
                $png = base64_encode($gen->getBarcode((string) $sales->order_number, $gen::TYPE_CODE_128, 1, 28));
                $invoiceBarcodeHtml = '<img src="data:image/png;base64,' . $png . '" style="height:28px;width:auto;max-width:95%;">';
            }
        } catch (\Throwable $e) {
            $invoiceBarcodeHtml = '';
        }
    }

    $productName = function ($raw) use ($inv) {
        $name = (string) ($raw ?? 'Product');
        if (!empty($inv['truncate_product_names']) && mb_strlen($name) > 28) {
            return mb_substr($name, 0, 27) . '…';
        }
        return $name;
    };
@endphp
<style>
    @if (!empty($printHindi))
    @font-face {
        font-family: 'Nirmala';
        src: url("file://{{ storage_path('fonts/Nirmala.ttf') }}") format('truetype');
        font-weight: normal;
        font-style: normal;
    }
    .inv-hi-name {
        font-family: 'Nirmala', 'DejaVu Sans', sans-serif;
        font-weight: normal;
    }
    @endif
    html, body {
        width: {{ $paperWmm }}mm !important;
        max-width: {{ $paperWmm }}mm !important;
    }
    body, .invoice-wrapper {
        font-size: {{ $fontPx }}px !important;
    }
    .invoice-wrapper, body > .invoice-wrapper {
        width: {{ $wrapWmm }}mm !important;
        max-width: {{ $wrapWmm }}mm !important;
        margin-left: {{ $leftMarginMm }}mm !important;
        box-sizing: border-box;
    }
    /* templates 2–5 use body as wrapper */
    body.inv-thermal-body {
        width: {{ $wrapWmm }}mm !important;
        max-width: {{ $wrapWmm }}mm !important;
        padding-left: {{ max(2, 2 + $leftMarginMm) }}mm !important;
        font-size: {{ $fontPx }}px !important;
    }
    .inv-logo { display: block; margin: 0 auto 3px auto; height: {{ $logoHeightPx }}px; width: auto; max-width: 90%; }
    .inv-savings {
        margin: 6px 0;
        padding: 4px 3px;
        background: #e8e8e8;
        text-align: center;
        font-weight: bold;
        font-size: {{ max(7, $fontPx) }}px;
    }
    .inv-qr { text-align: center; margin-top: 6px; }
    /* Larger QR so UPI apps can scan on narrow thermal paper (~42mm / ~32mm). */
    .inv-qr img { width: {{ $is2inch ? 32 : 42 }}mm; height: {{ $is2inch ? 32 : 42 }}mm; image-rendering: pixelated; }
    .inv-barcode { text-align: center; margin-top: 4px; }
    .inv-terms { margin-top: 6px; font-size: {{ max(6, $fontPx - 1) }}px; text-align: left; }
    .inv-shop-extra { margin: 1px 0 0 0; font-size: {{ max(7, $fontPx) }}px; }
    .inv-words { margin: 4px 0; font-size: {{ max(7, $fontPx) }}px; text-align: left; }
    .inv-tax-table { width: 100%; border-collapse: collapse; margin: 4px 0; }
    .inv-tax-table th, .inv-tax-table td { font-size: {{ max(6, $fontPx - 1) }}px; padding: 1px; text-align: right; }
    .inv-tax-table th:first-child, .inv-tax-table td:first-child { text-align: left; }
    .inv-tax-table th { border-bottom: 1px solid #000; }
    @if (!$invTextStyling)
    body, body *, .bold, b, strong, th, h1, h2, h3, h4 { font-weight: normal !important; }
    @endif
    @if (!empty($usbPrint))
    /* USB print only — bump fixed template sizes a little */
    body { font-size: {{ max(10, $fontPx) }}px !important; line-height: 1.3 !important; }
    .shop-name { font-size: 14px !important; }
    .shop-phone, .inv-shop-extra { font-size: 12px !important; }
    .inv-title { font-size: 13px !important; }
    .item-table th, .item-table td, .item-table .item-name { font-size: 10px !important; }
    .item-extra { font-size: 9px !important; }
    .sum-table td { font-size: 11px !important; }
    .terms, .inv-terms, .inv-words { font-size: 10px !important; }
    .inv-savings { font-size: 10px !important; }
    .inv-tax-table th, .inv-tax-table td { font-size: 9px !important; }
    .inv-qr img { width: {{ $is2inch ? 36 : 46 }}mm !important; height: {{ $is2inch ? 36 : 46 }}mm !important; }
    @endif
</style>
