@php
    $ip = (isset($ip) && is_array($ip)) ? $ip : invoice_print($setting ?? null);
    $ipColor = $ip['theme_color'] ?? '#ff9f43';
    $ipDp = !empty($ip['amount_decimal']) ? 2 : 0;
    $ipPaper = ($ip['page_size'] ?? 'a4') === 'a5' ? 'A5' : 'A4';
    $ipLandscape = ($ip['orientation'] ?? 'portrait') === 'landscape';
    $ipMinHeightMm = ['A4' => $ipLandscape ? 196 : 283, 'A5' => $ipLandscape ? 134 : 196][$ipPaper];
    $ipFloatFooter = $ipPaper === 'A4' && !$ipLandscape;
    $ipScale = ['small' => 0.85, 'medium' => 1, 'large' => 1.15][$ip['text_size'] ?? 'medium'] ?? 1;
    $ipNameSize = ['small' => 11, 'medium' => 13, 'large' => 14, 'xlarge' => 18][$ip['company_name_size'] ?? 'large'] ?? 14;
    $ipMoney = function ($amount, $unused = null) use ($setting, $ip, $ipDp) {
        $text = invoice_money($amount, $ipDp, !empty($ip['amount_grouping']), $ip['amount_words_format'] ?? 'indian');
        return ($setting->currency_position ?? 'left') === 'right'
            ? $text . ($setting->currency_symbol ?? '')
            : ($setting->currency_symbol ?? '') . $text;
    };
    $ipCopies = !empty($ip['print_original_duplicate']) ? ['ORIGINAL FOR RECIPIENT', 'DUPLICATE COPY'] : [null];
@endphp
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice PDF</title>
    <style>
        @page {
            size: {{ $ipPaper }} {{ $ipLandscape ? 'landscape' : 'portrait' }};
            margin: 2mm;
        }
        @if ($ipScale != 1)
        .ip-items th, .ip-items td { font-size: {{ round(9 * $ipScale, 1) }}px !important; }
        .ip-info td, .ip-foot td { font-size: {{ round(10 * $ipScale, 1) }}px !important; }
        @endif
        .ip-copy-label { position: absolute; top: 1mm; right: 3mm; font-size: 8px; font-weight: bold; letter-spacing: .5px; color: #555; }
        .ip-sign-cell { border: 2px solid #dee2e6; padding: 8px 12px; vertical-align: top; }
        .ip-ack { margin-top: 6px; border-top: 1px dashed #777; padding-top: 4px; font-size: 9px; }

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
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            /* Set base font size here */
            margin: 0;
            padding: 0;
            background: white;
        }

        .pdf-wrapper {
            margin-top: 3mm;
            /* ensures top border is visible */
        }

        .card-body {
            width: 95%;
            /* min-height: auto; */
            min-height: {{ $ipMinHeightMm }}mm;
            padding: 3mm;
            margin: auto;
            box-sizing: border-box;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid black; /* thicker border */
            font-size: 12px;
            position: relative;
        }

        table,
        table td,
        table th {
            font-size: inherit;
            /* Make sure tables inherit the font size */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            /* margin-bottom: 20px; */
        }

        .header-table {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            margin-bottom: 30px;
        }

        .header-table td {
            padding: 10px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
            padding: 4px 8px;
        }

        .table-bordered thead tr {
            background-color: #e9ecf0ff;
            color: #333;
        }

        .table-bordered tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .table-bordered tfoot tr {
            /* font-weight: bold; */
            /* background-color: #e9ecef; */
        }

        .text-end {
            /* text-align: right; */
        }

        .text-center {
            text-align: center;
        }

        .mb-0 {
            /* margin-bottom: 0; */
        }

        h3,
        h4 {
            margin: 0 0 10px 0;
            color: #343a40;
        }

    .qr-details-box {
    text-align: center;
    vertical-align: middle;

}

.qr-details-box img {
    display: block;
    margin: auto 10px ; /* auto handles left & right center */
}

        .invoice-title {
            /* text-transform: uppercase; */
            /* letter-spacing: 2px; */
            /* border-bottom: 2px solid #e5e7ebff; */
            /* display: inline-block; */
            /* padding-bottom: 5px; */
            /* margin-bottom: 20px; */
        }

        .logo-container {
            /* position: relative; */
            /* min-height: 50px; */
            /* margin-bottom: 10px; */
        }

        /* .logo-container .qr-code {
            height: 60px;
            position: absolute;
            top: 0;
            left: 0;
        } */

        /* .logo-container .company-logo {
            height: 50px;
            position: absolute;
            top: 0;
            left: 0;
        } */

        /* .logo-container .company-details {
            text-align: center;
        } */


        /* .signature-section img {
            height: 50px;
            margin-top: 5px;
        } */

        /* .signature-section {
            margin-top: 50px;
            text-align: right;
        } */
         /* ===== PRODUCT NAME WRAP FIX ===== */

         


        @php
            $totalthing = count($orderItems);
            $footerBottom = ($totalthing > 5) ? 60 : 45;
            $isRentalOrder = ($sales->rental_status === 'rental' || $sales->quotation_status === 'rental') && optional(\App\Models\Setting::first())->rental;
        @endphp

        .footer-section {
            @if ($ipFloatFooter)
            position: absolute;
            bottom: 20px;
            left: 0;
            @endif
            width: 100%;
        }

        .invoice-label-nowrap {
            white-space: nowrap;
            word-break: keep-all;
            width: 58px;
        }

        .emi-pdf-box {
            margin-top: 8px;
            border: 1px solid #1b2850;
            background: #f7f9fc;
            page-break-inside: avoid;
        }

        .emi-pdf-blank-space {
            width: 100%;
            margin: 8px 0 8px 0;
        }

        .emi-pdf-title {
            padding: 5px 7px;
            background: #1b2850;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .emi-pdf-body {
            padding: 6px 7px;
            font-size: 9px;
            color: #222;
        }

        .emi-pdf-layout {
            width: 100%;
            border-collapse: collapse;
        }

        .emi-pdf-layout td {
            vertical-align: top;
            border: none;
            padding: 0;
        }

        .emi-pdf-summary-cell {
            width: 34%;
            padding-right: 8px !important;
        }

        .emi-pdf-schedule-cell {
            width: 66%;
        }

        .emi-pdf-formula {
            font-size: 11px;
            font-weight: bold;
            color: #1b2850;
            margin-bottom: 4px;
        }

        .emi-pdf-meta {
            font-size: 8.5px;
            color: #444;
            line-height: 1.45;
        }

        .emi-pdf-note {
            margin-top: 5px;
            padding: 4px 5px;
            background: #fff8e9;
            border: 1px solid #ffd89a;
            color: #5d4300;
            font-size: 8px;
            line-height: 1.35;
        }

        .emi-schedule-table {
            margin-top: 0;
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
        }

        .emi-schedule-table th {
            background: #e8edf6;
            color: #1b2850;
            border: 1px solid #cbd3df;
            padding: 3px;
            text-align: left;
        }

        .emi-schedule-table td {
            border: 1px solid #d7dde6;
            padding: 3px;
        }

        .emi-status-paid {
            color: #2E7D32;
            font-weight: bold;
        }

        .emi-status-pending {
            color: #C62828;
            font-weight: bold;
        }
    </style>
</head>

<body>
    @foreach ($ipCopies as $ipCopyIndex => $ipCopyLabel)
    @if ($ipCopyIndex > 0)
        <div style="page-break-before: always;"></div>
    @endif
    <div class="card-body">
        @if ($ipCopyLabel)
            <div class="ip-copy-label">{{ $ipCopyLabel }}</div>
        @endif
        @if ((int) ($ip['extra_top_space'] ?? 0) > 0)
            <div style="height: {{ (int) $ip['extra_top_space'] * 5 }}mm;"></div>
        @endif
        @php
            $logoBase64 = local_image_to_base64(
                $setting->logo ?? null,
                'admin/assets/img/logo-image.jpg'
            );
            $ipContactParts = [];
            if (!empty($ip['show_phone']) && !empty($setting->phone)) {
                $ipContactParts[] = 'Phone: ' . e($setting->phone);
            }
            if (!empty($ip['show_email']) && !empty($setting->email)) {
                $ipContactParts[] = 'Email: <span style="text-transform: none;">' . e($setting->email) . '</span>';
            }
        @endphp
        @if ($logoBase64 && !empty($ip['show_watermark']))
            <div style="position: absolute; top: 35%; left: 0; right: 0; text-align: center; z-index: -1; opacity: 0.1;">
                <img src="{{ $logoBase64 }}" style="width: 70%; max-width: 800px; height: auto; margin: 0 auto;">
            </div>
        @endif
        <table style="width:100%; margin-bottom: 10px; border-collapse: collapse;">
            <tr>
                <td style="width: 150px; vertical-align: top;">
                    @if ($logoBase64 && !empty($ip['show_logo']))
                        <img src="{{ $logoBase64 }}" alt="Company Logo"
                            style="height: 60px; width: auto;">
                    @endif
                </td>
                <td style="vertical-align: middle; padding-left: 15px; text-align: right; word-wrap: break-word; white-space: normal; max-width: 300px;">
                    @if (!empty($ip['show_company_name']))
                        <h3 style="margin: 0; text-transform: uppercase; font-size: {{ $ipNameSize }}px;">{{ $setting->name ?? '' }}</h3>
                    @endif
                    <small style="text-transform: uppercase; display: block;">
                        @if (!empty($ip['show_address']) && !empty($setting->address))
                            {{ $setting->address }}<br>
                        @endif
                        {!! implode(' | ', $ipContactParts) !!}
                    </small>
                </td>
            </tr>
        </table>

        <hr style="height: 2px; background-color: #d7cdcd; border: none; margin-top: 0; margin-bottom: 12px;">




        <table class="ip-info" style="width:100%; border-collapse: collapse; font-size: 10px; margin-bottom: 6px; table-layout: fixed;">
            <tr>
                <td
                    style="width:33%; position: relative; padding: 5px 8px; vertical-align: top; background-color: #eaedf0; border-right: 1px solid {{ $ipColor }};">
                    <strong style="text-transform: uppercase; display: block; margin-bottom: 6px;">Customer
                        Details:</strong>
                    <table style="width:100%; border-collapse: collapse; font-size: 10px; color: inherit;">
                        <tr>
                            <td style="padding: 0 0 4px 0;">Name :</td>
                            <td style="text-align: right; padding: 0 0 4px 0;">
                                {{ $customer['name'] ?? 'Default-customer' }}
                            </td>
                        </tr>
                        @if (!empty($customer['company_name']))
                            <tr>
                                <td style="padding: 0 0 4px 0;">Company Name :</td>
                                <td style="text-align: right; padding: 0 0 4px 0;">{{ $customer['company_name'] }}</td>
                            </tr>
                        @endif
                        @if (!empty($customer['phone']))
                            <tr>
                                <td style="padding: 0 0 4px 0;">Phone :</td>
                                <td style="text-align: right; padding: 0 0 4px 0;">{{ $customer['phone'] }}</td>
                            </tr>
                        @endif
                        @if (!empty($customer['email']))
                            <tr>
                                <td style="padding: 0 0 4px 0;">Email :</td>
                                <td style="text-align: right; padding: 0 0 4px 0;">{{ $customer['email'] }}</td>
                            </tr>
                        @endif
                        @if (!empty($customer['address']))
                            <tr>
                                <td class="invoice-label-nowrap" style="padding: 0 0 4px 0; vertical-align: top;">Address :</td>
                                <td style="text-align: right; padding: 0 0 4px 0; word-wrap: break-word; white-space: normal;">{{ $customer['address'] }}</td>
                            </tr>
                        @endif
                        @if (!empty($customer['delivery_address']))
                            <tr>
                                <td class="invoice-label-nowrap" style="padding: 0 0 4px 0; vertical-align: top;">Delivery Address :</td>
                                <td style="text-align: right; padding: 0 0 4px 0; word-wrap: break-word; white-space: normal;">{{ $customer['delivery_address'] }}</td>
                            </tr>
                        @endif
                        @if (($sales->gst_option ?? 'without_gst') === 'with_gst' && !empty($customer['gst_number']))
                            <tr>
                                <td style="padding: 0 0 4px 0;">GST :</td>
                                <td style="text-align: right; padding: 0 0 4px 0;">{{ $customer['gst_number'] }}</td>
                            </tr>
                        @endif
                        @if (!empty($customer['pan_number']))
                            <tr>
                                <td style="padding: 0 0 4px 0;">PAN :</td>
                                <td style="text-align: right; padding: 0 0 4px 0;">{{ $customer['pan_number'] }}</td>
                            </tr>
                        @endif
                    </table>

                </td>

                <!-- Vehicle Details -->
                <td
                    style="width:33%; position: relative; padding: 8px 12px; vertical-align: top; background-color: #eaedf0; border-right: 1px solid {{ $ipColor }};">
                    <strong style="text-transform: uppercase; display: block;margin-bottom: 2px">Company
                        Details:</strong>
                    <table style="width:100%; border-collapse: collapse; font-size: 10px; color: inherit;">
                        @if (!empty($ip['show_company_name']) && !empty($setting->name))
                            <tr>
                                <td style="padding: 0 0 2px 0;">Name :</td>
                                <td style="text-align: right; padding: 0 0 2px 0;">{{ $setting->name }}</td>
                            </tr>
                        @endif

                        @if (!empty($ip['show_email']) && !empty($setting->email))
                            <tr>
                                <td style="padding: 0 0 2px 0;">Email :</td>
                                <td style="text-align: right; padding: 0 0 2px 0;">{{ $setting->email }}</td>
                            </tr>
                        @endif

                        @if (!empty($ip['show_phone']) && !empty($setting->phone))
                            <tr>
                                <td style="padding: 0 0 2px 0;">Phone :</td>
                                <td style="text-align: right; padding: 0 0 2px 0;">{{ $setting->phone }}</td>
                            </tr>
                        @endif

                        @if (!empty($ip['show_address']) && !empty($setting->address))
                            <tr>
                                <td class="invoice-label-nowrap" style="padding: 0 0 2px 0; vertical-align: top;">Address :</td>
                                <td style="text-align: right; padding: 0 0 2px 0; word-wrap: break-word; white-space: normal;">{{ $setting->address }}</td>
                            </tr>
                        @endif

                        @if (!empty($ip['show_gstin']) && ($sales->gst_option ?? 'without_gst') === 'with_gst' && !empty($setting->gst_num))
                            <tr>
                                <td style="padding: 0 0 2px 0;">GST :</td>
                                <td style="text-align: right; padding: 0 0 2px 0;">{{ $setting->gst_num }}</td>
                            </tr>
                        @endif
                    </table>

                </td>
                @php
                    $isQuotation      = ($sales->quotation_status ?? '') === 'quotation';
                    $isAdvanceReceipt = ($sales->quotation_status ?? '') === 'advance_receipt' && optional(\App\Models\Setting::first())->advance_receipt;
                    $paymentMethodRaw = strtolower(trim((string) ($sales->payment_method ?? '')));
                    $paymentMethodLabels = [
                        'cash' => 'Cash',
                        'online' => 'Online',
                        'cash+online' => 'Cash+Online',
                        'cash_online' => 'Cash+Online',
                        'cash + online' => 'Cash+Online',
                        'cash+bank' => 'Cash+Online',
                        'cash_bank' => 'Cash+Online',
                        'cash + bank' => 'Cash+Online',
                        'emi' => 'EMI',
                    ];
                    $paymentMethodLabel = $paymentMethodLabels[$paymentMethodRaw]
                        ?? ($paymentMethodRaw !== '' ? ucwords(str_replace(['_', '-'], ' ', $paymentMethodRaw)) : '-');
                @endphp


                <!-- Invoice Details -->
                <td style="width:34%; border: 0px solid #dee2e6; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">

                    <strong style="text-transform: uppercase; display: block; margin-bottom: 2px;">
                        @if($isQuotation)
                            Quotation Info:
                        @elseif($isAdvanceReceipt)
                            Advance Receipt Info:
                        @elseif($isRentalOrder)
                            Rental Info:
                        @else
                            Order Details:
                        @endif
                    </strong>

                    <table style="width:100%; border-collapse: collapse; font-size: 10px; color: inherit;">

                        {{-- Order Status --}}
                        <tr>
                            <td style="padding: 0 0 2px 0;">Order Status :</td>
                            <td style="text-align: right; padding: 0 0 2px 0;">
                                @if($isQuotation)
                                    Quotation
                                @elseif($isAdvanceReceipt)
                                    Advance Receipt
                                @elseif($isRentalOrder)
                                    Rental
                                @else
                                    Sales
                                @endif
                            </td>
                        </tr>

                        {{-- Number --}}
                        <tr>
                            <td style="padding: 0 0 2px 0;">
                                @if($isQuotation)
                                    Quotation Number :
                                @elseif($isAdvanceReceipt)
                                    Advance Receipt No :
                                @elseif($isRentalOrder)
                                    Rental Number :
                                @else
                                    Order Number :
                                @endif
                            </td>
                            <td style="text-align: right; padding: 0 0 2px 0;">
                                {{ $sales->order_number ?? '-' }}
                            </td>
                        </tr>

                        {{-- Date --}}
                        <tr>
                            <td style="padding: 0 0 2px 0;">
                                @if($isQuotation)
                                    Quotation Date :
                                @elseif($isAdvanceReceipt)
                                    Receipt Date :
                                @elseif($isRentalOrder)
                                    Rental Date :
                                @else
                                    Order Date :
                                @endif
                            </td>
                            <td style="text-align: right; padding: 0 0 2px 0;">
                                {{ !empty($sales->created_at) ? date('d/m/Y h:i A', strtotime($sales->created_at)) : '-' }}
                            </td>
                        </tr>

                        {{-- Sales Person --}}
                        <tr>
                            <td style="padding: 0 0 2px 0;">Sales Person :</td>
                            <td style="text-align: right; padding: 0 0 2px 0;">
                                {{ $sales->staff->name ?? $sales->creator->name ?? '-' }}
                            </td>
                        </tr>

                        {{-- Payment Method --}}
                        @if (!empty($ip['show_payment_mode']))
                        <tr>
                            <td style="padding: 0 0 2px 0;">Payment Method :</td>
                            <td style="text-align: right; padding: 0 0 2px 0;">
                                {{ $paymentMethodLabel }}
                            </td>
                        </tr>
                        @endif

                    </table>
                </td>
            </tr>
        </table>

        @php
            $hasGst = false;
            $hasDiscount = false;

            foreach ($orderItems as $item) {
                if (
                    (!empty($item->product_gst_total) && $item->product_gst_total > 0) ||
                    (!empty($item->product_gst_details) && is_array($item->product_gst_details))
                ) {
                    $hasGst = true;
                }

                if ((float) ($item->discount_amount ?? 0) > 0) {
                    $hasDiscount = true;
                }
            }
            $ipBillOfSupply = !empty($ip['bill_of_supply']) && !$hasGst && ($sales->gst_option ?? 'without_gst') !== 'with_gst';
            $ipColCount = 6 + ($isRentalOrder ? 3 : 0) + ($hasDiscount ? 1 : 0) + ($hasGst ? 2 : 0);
            $ipExpandRows = !empty($ip['expand_table'])
                ? ($ipPaper === 'A4' ? ($ipLandscape ? 6 : 14) : ($ipLandscape ? 3 : 8))
                : 0;
            $ipTargetRows = max((int) ($ip['min_item_rows'] ?? 0), $ipExpandRows);
            $ipTaxSummary = [];
            $ipTotalQty = 0;
            $ipSaved = 0;
        @endphp
        <div class="text-center">
            <h4 style="text-transform: uppercase;">
                @if($isQuotation)
                    Quotation
                @elseif($isAdvanceReceipt)
                    Advance Receipt
                @elseif($isRentalOrder)
                    Rental Invoice
                @elseif($ipBillOfSupply)
                    Bill of Supply
                @else
                    Sale
                @endif
            </h4>
        </div>

        <table class="table-bordered ip-items"
            style="width: 100%; border-collapse: collapse; font-family: DejaVu Sans, sans-serif; font-size: 9px; margin: 5px 0 5px 0; table-layout: fixed;">
            <thead>
                <tr style="background-color:{{ $ipColor }}; color:#fff; font-size:9px; line-height:1.2;">
                    <th style="width:5%; padding: 2px 3px; text-align:center;">Sr No</th>
                    <th style="width:18%; padding: 2px 3px; text-align:left;">Product Name</th>
                    <th style="width:6%; padding: 2px 3px; text-align:center;">Unit</th>
                    <th style="width:5%; padding: 2px 3px; text-align:center;">Qty</th>
                    @if($isRentalOrder)
                        <th style="width:8%; padding: 2px 3px; text-align:center;">Rent Value</th>
                        <th style="width:10%; padding: 2px 3px; text-align:center;">Rent Price</th>
                        <th style="width:14%; padding: 2px 3px; text-align:center; white-space:nowrap;">Start - End Date</th>
                    @endif
                    <th style="width:10%; padding: 2px 3px; text-align:center;">Price</th>
                    @if($hasDiscount)
                        <th style="width:8%; padding: 2px 3px; text-align:center;">Disc Amt</th>
                    @endif
                    @if($hasGst)
                        <th style="width:14%; padding: 2px 3px; text-align:center;">Product Taxes</th>
                        <th style="width:8%; padding: 2px 3px; text-align:center;">Tax Amt</th>
                    @endif
                    <th style="width:10%; padding: 2px 3px; text-align:center;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orderItems as $item)
                    @php
                        $itemQty = (float) ($item->quantity ?? 0);
                        $productGstTotal = (float) ($item->product_gst_total ?? 0);
                        // price is now BASE price — display it directly (no GST subtraction)
                        $displayUnitPrice = (float) ($item->price ?? 0);
                        $ipTotalQty += $itemQty;
                        $ipItemMrp = (float) ($item->mrp ?? 0);
                        if ($ipItemMrp > $displayUnitPrice) {
                            $ipSaved += ($ipItemMrp - $displayUnitPrice) * $itemQty;
                        }
                        $ipSaved += (float) ($item->discount_amount ?? 0);
                    @endphp
                    <tr style="font-size:9px; line-height:1.3;">
                        <td style="text-align:center; padding:2px 3px;">{{ $loop->iteration }}</td>
                       <td style="padding:2px 3px; text-align:left;">
                        @php
                            $images = json_decode($item->product->images ?? '[]');
                            $firstImage = !empty($images) ? $images[0] : null;
                            $base64 = null;

                            if ($firstImage) {
                                $imagePath = storage_path('app/public/' . $firstImage);
                            } else {
                                $imagePath = public_path('/admin/assets/img/product/noimage.png');
                            }

                            if (file_exists($imagePath)) {
                                $type = pathinfo($imagePath, PATHINFO_EXTENSION);
                                $data = file_get_contents($imagePath);
                                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            }
                        @endphp

                        <table style="border-collapse: collapse; width:100%;">
                            <tr>
                                <td style="width: 25px; max-width: 25px; padding: 0; vertical-align: middle; border: none;">
                                    <img src="{{ $base64 }}" alt="img"
                                        style="width: 22px; height: 22px; object-fit: cover; border-radius: 3px; display: block;">
                                </td>
                                <td style="border: none; padding: 0 0 0 4px; vertical-align: middle; word-wrap: break-word; word-break: break-word; white-space: normal; overflow-wrap: break-word;">
                                    @if (!empty($printHindi))<span class="inv-hi-name">{{ invoice_line_product_name($item->product ?? null, true) }}</span>@else{{ invoice_line_product_name($item->product ?? null, false) }}@endif
                                    @if(!empty($item->product_variant_id) && $item->variant)
                                        <br><span style="font-size: 9px; color: #555;">
                                            {{ $item->variant->size ? 'Size: '.$item->variant->size : '' }}
                                            {{ $item->variant->color ? ($item->variant->size ? ' | ' : '') . 'Color: '.$item->variant->color : '' }}
                                        </span>
                                    @endif
                                    @if(!empty($item->imei_no))
                                        <br><span style="font-size: 7px; color: #555;">IMEI: {{ $item->imei_no }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                          <td style="padding:2px 3px; text-align:center;">
                            {{ ucfirst($item->product->unit->unit_name ?? 'N/A') }}
                        </td>
                         <td style="padding:2px 3px; text-align:center;">
                            {{ $item->quantity }}
                        </td>
                        @if($isRentalOrder)
                        <td style="padding:2px 3px; text-align:center;">
                            {{ $item->rent_value ? $item->rent_value . ' ' . ($item->rent_period ?? 'Days') : '-' }}
                        </td>
                        <td style="padding:2px 3px; text-align:center;">
                            {{ $item->rent_price ? $ipMoney($item->rent_price, $setting) . ' /Per ' . ($item->rent_period ? rtrim($item->rent_period, 's') : 'Day') : '-' }}
                        </td>
                        <td style="padding:2px 3px; text-align:center; font-size:8px;">
                            @if($item->rent_start_at && $item->rent_end_at)
                                {{ \Carbon\Carbon::parse($item->rent_start_at)->format('d-M-y h:i A') }}<br>To<br>
                                {{ \Carbon\Carbon::parse($item->rent_end_at)->format('d-M-y h:i A') }}
                            @else
                                -
                            @endif
                        </td>
                        @endif
                        <td style="padding:2px 3px; text-align:center;">
                            {{ $ipMoney($displayUnitPrice) }}
                        </td>
                    @php
                        $discountAmount = (float)($item->discount_amount ?? 0);
                        $discountPercentage = (float)($item->discount_percentage ?? 0);
                    @endphp

                    @if($hasDiscount)
                        <td style="padding:2px 3px; text-align:center;">
                            {{-- Discount Amount --}}
                            {{ $ipMoney($discountAmount) }}

                            {{-- Show percentage only if applied --}}
                            @if($discountPercentage > 0)
                                <small>({{ rtrim(rtrim(number_format($discountPercentage,2), '0'), '.') }}%)</small>
                            @endif
                        </td>
                    @endif
                    @php
                        // ✅ GST total per product (initialised from DB as fallback)
                        $productGstTotal = (float) ($productGstTotal ?? 0);

                        $rentMultiplier = 1;
                        if ($isRentalOrder) {
                            $rentMultiplier = max(1, (int)($item->rent_value ?? 1));
                        }

                        // ✅ price IS exclusive base price; lineTotal = baseUnitPrice × qty
                        $lineTotal = (float) ($item->price ?? 0) * (float) ($item->quantity ?? 0) * $rentMultiplier;

                        $rowGstDetails = $item->product_gst_details;
                        if (is_string($rowGstDetails)) {
                            $rowGstDetails = json_decode($rowGstDetails, true);
                            if (is_string($rowGstDetails)) {
                                $rowGstDetails = json_decode($rowGstDetails, true);
                            }
                        }
                        if (is_array($rowGstDetails) && isset($rowGstDetails['tax_name'])) {
                            $rowGstDetails = [$rowGstDetails];
                        }

                        // ✅ Recalculate GST using EXCLUSIVE calculation
                        // Step 1: Sum ALL tax rates (e.g. CGST 9% + SGST 9% = 18%)
                        // Step 2: Total GST = lineTotal * (totalRate / 100)
                        // Step 3: Split proportionally per sub-tax
                        $rowTotalGstRate = 0;
                        if (is_array($rowGstDetails) && !empty($rowGstDetails)) {
                            foreach ($rowGstDetails as $gstRow) {
                                $rowTotalGstRate += (float)($gstRow['tax_rate'] ?? 0);
                            }
                        }
                        if ($rowTotalGstRate > 0 && is_array($rowGstDetails) && !empty($rowGstDetails)) {
                            $rowLineGstTotal = round($lineTotal * ($rowTotalGstRate / 100), 2);
                            $productGstTotal = $rowLineGstTotal;
                            // Pre-compute per-sub-tax GST for display (proportional split)
                            $rowTaxPortions  = [];
                            foreach ($rowGstDetails as $gstRow) {
                                $rate = (float)($gstRow['tax_rate'] ?? 0);
                                $rowTaxPortions[] = [
                                    'tax_name' => $gstRow['tax_name'] ?? 'GST',
                                    'tax_rate' => $rate,
                                    'gst_amt'  => round(($rowLineGstTotal * $rate) / $rowTotalGstRate, 2),
                                ];
                            }
                        } else {
                            $rowLineGstTotal = 0;
                            $productGstTotal = 0;
                            $rowTaxPortions  = [];
                        }
                        // Row final total = lineTotal + GST - discountAmount
                        $rowFinalTotal = $lineTotal + $rowLineGstTotal - $discountAmount;
                        foreach ($rowTaxPortions as $taxPortion) {
                            $ipTaxKey = $taxPortion['tax_name'] . '|' . $taxPortion['tax_rate'];
                            if (!isset($ipTaxSummary[$ipTaxKey])) {
                                $ipTaxSummary[$ipTaxKey] = ['name' => $taxPortion['tax_name'], 'rate' => $taxPortion['tax_rate'], 'taxable' => 0, 'amount' => 0];
                            }
                            $ipTaxSummary[$ipTaxKey]['taxable'] += $lineTotal;
                            $ipTaxSummary[$ipTaxKey]['amount'] += $taxPortion['gst_amt'];
                        }
                    @endphp
                         @if($hasGst)
                        <!-- Product Taxes -->
                       <td style="padding:2px 3px; text-align:center; font-size:8px;">
                            @if(!empty($rowTaxPortions))
                                @foreach($rowTaxPortions as $taxPortion)
                                    <div>
                                        {{ $taxPortion['tax_name'] }} ({{ $taxPortion['tax_rate'] }}%)
                                        : {{ $ipMoney($taxPortion['gst_amt']) }}
                                    </div>
                                @endforeach
                            @else
                                N/A
                            @endif
                        </td>

                    <!-- ✅ TAX AMOUNT COLUMN -->
                    <td style="padding:2px 3px; text-align:right; font-weight:bold;">
                        {{ $ipMoney($productGstTotal) }}
                    </td>
                     @endif
                    <!-- ✅ TOTAL -->
                    <td style="padding:2px 3px; text-align:center; font-weight:bold;">
                        {{ $ipMoney($rowFinalTotal) }}
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="{{ $ipColCount }}" style="text-align:center; padding:8px;">No product data available</td>
                    </tr>
                @endforelse
                @for ($ipPad = count($orderItems); $ipPad < $ipTargetRows; $ipPad++)
                    <tr style="font-size:9px; line-height:1.3;">
                        @for ($ipCol = 0; $ipCol < $ipColCount; $ipCol++)
                            <td style="padding:2px 3px; height: 14px;">&nbsp;</td>
                        @endfor
                    </tr>
                @endfor
            </tbody>
            @if (!empty($ip['show_total_qty']) && count($orderItems) > 0)
                <tfoot>
                    <tr style="font-size:9px; font-weight:bold;">
                        <td colspan="3" style="padding:2px 3px; text-align:right;">Total Qty</td>
                        <td style="padding:2px 3px; text-align:center;">{{ fmod($ipTotalQty, 1.0) == 0.0 ? number_format($ipTotalQty, 0) : number_format($ipTotalQty, 2) }}</td>
                        <td colspan="{{ $ipColCount - 4 }}" style="padding:2px 3px;"></td>
                    </tr>
                </tfoot>
            @endif
        </table>

        @if (!empty($ip['show_tax_details']) && !empty($ipTaxSummary))
            <table class="table-bordered ip-items" style="width: 60%; border-collapse: collapse; font-size: 9px; margin: 4px 0 6px 0;">
                <thead>
                    <tr style="background-color:{{ $ipColor }}; color:#fff;">
                        <th style="padding: 2px 4px; text-align:left;">Tax</th>
                        <th style="padding: 2px 4px; text-align:center;">Rate</th>
                        <th style="padding: 2px 4px; text-align:right;">Taxable Amt</th>
                        <th style="padding: 2px 4px; text-align:right;">Tax Amt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ipTaxSummary as $ipTax)
                        <tr>
                            <td style="padding: 2px 4px;">{{ $ipTax['name'] }}</td>
                            <td style="padding: 2px 4px; text-align:center;">{{ rtrim(rtrim(number_format((float) $ipTax['rate'], 2), '0'), '.') }}%</td>
                            <td style="padding: 2px 4px; text-align:right;">{{ $ipMoney($ipTax['taxable']) }}</td>
                            <td style="padding: 2px 4px; text-align:right;">{{ $ipMoney($ipTax['amount']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif


        @php
            $isEmiSale = strtolower((string) ($sales->payment_method ?? '')) === 'emi';
            $emiTenureRaw = (string) ($sales->emi_tenure ?? '');
            $emiTenureMonths = (int) preg_replace('/[^0-9]/', '', $emiTenureRaw);
            $emiMonthlyAmount = (float) ($sales->emi_monthly_amount ?? 0);
            $emiLoanAmount = (float) ($sales->emi_loan_amount ?? 0);
            $emiDownPayment = (float) ($sales->emi_down_payment ?? 0);
            $emiInterestRate = (float) ($sales->emi_interest_rate ?? 0);
            $emiInstallmentTotal = $emiTenureMonths > 0 ? $emiMonthlyAmount * $emiTenureMonths : 0;
            $paidEmiPaymentsByMonth = [];

            foreach (($emiPayments ?? collect()) as $emiPayment) {
                $emiMonthNo = (int) ($emiPayment->emi_month ?? 0);
                if ($emiMonthNo > 0) {
                    $paidEmiPaymentsByMonth[$emiMonthNo] = $emiPayment;
                }
            }
        @endphp

        @if($isEmiSale && ($emiTenureMonths > 0 || $emiMonthlyAmount > 0))
            <div class="emi-pdf-blank-space">
                <div class="emi-pdf-box">
                    <div class="emi-pdf-title">EMI Distribution</div>
                    <div class="emi-pdf-body">
                        <table class="emi-pdf-layout">
                            <tr>
                                <td class="emi-pdf-summary-cell">
                                    <div class="emi-pdf-formula">
                                        @if($emiMonthlyAmount > 0 && $emiTenureMonths > 0)
                                            {{ $ipMoney($emiMonthlyAmount, $setting) }} x {{ $emiTenureMonths }} Months =
                                            {{ $ipMoney($emiInstallmentTotal, $setting) }}
                                        @elseif($emiMonthlyAmount > 0)
                                            Monthly EMI: {{ $ipMoney($emiMonthlyAmount, $setting) }}
                                        @else
                                            EMI plan selected
                                        @endif
                                    </div>

                                    <div class="emi-pdf-meta">
                                        @if($emiDownPayment > 0)
                                            Down Payment: {{ $ipMoney($emiDownPayment, $setting) }}<br>
                                        @endif
                                        @if($emiLoanAmount > 0)
                                            Loan Amount: {{ $ipMoney($emiLoanAmount, $setting) }}<br>
                                        @endif
                                        @if($emiInterestRate > 0)
                                            Interest: {{ number_format($emiInterestRate, 2) }}%<br>
                                        @endif
                                        @if(!empty($emiTenureRaw))
                                            Tenure: {{ strtolower($emiTenureRaw) === 'custom' ? 'Custom' : $emiTenureMonths . ' Months' }}
                                        @endif
                                    </div>

                                    <div class="emi-pdf-note">
                                        Note: EMI distribution is shown month-wise. Paid months are marked as Paid and remaining months are pending as per the selected EMI tenure.
                                    </div>
                                </td>
                                <td class="emi-pdf-schedule-cell">
                                    @if($emiTenureMonths > 0)
                                        <table class="emi-schedule-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 26%;">Month</th>
                                                    <th style="width: 28%;">Amount</th>
                                                    <th style="width: 22%;">Status</th>
                                                    <th style="width: 24%;">Paid Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for($month = 1; $month <= $emiTenureMonths; $month++)
                                                    @php
                                                        $paidPayment = $paidEmiPaymentsByMonth[$month] ?? null;
                                                        $isPaidEmiMonth = !empty($paidPayment);
                                                        $monthLabel = $month === 1 ? '1st Month' : ($month === 2 ? '2nd Month' : ($month === 3 ? '3rd Month' : $month . 'th Month'));
                                                        $emiPaidDate = '-';
                                                        if ($isPaidEmiMonth) {
                                                            $rawEmiPaidDate = $paidPayment->payment_date ?? $paidPayment->created_at ?? null;
                                                            $emiPaidDate = $rawEmiPaidDate ? \Carbon\Carbon::parse($rawEmiPaidDate)->format('d-m-Y') : '-';
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $monthLabel }}</td>
                                                        <td style="text-align: right;">{{ $ipMoney($emiMonthlyAmount, $setting) }}</td>
                                                        <td class="{{ $isPaidEmiMonth ? 'emi-status-paid' : 'emi-status-pending' }}">
                                                            {{ $isPaidEmiMonth ? 'Paid' : 'Pending' }}
                                                        </td>
                                                        <td>{{ $emiPaidDate ?: '-' }}</td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        @endif

                {{-- ✅ Show only when quotation AND labour items exist --}}
                @if( isset($labourItems) && $labourItems->isNotEmpty())

                <div class="text-center">
                    <h4 style="text-transform: uppercase;">Labour Items</h4>
                </div>

                <table class="table-bordered"
                    style="width: 100%; border-collapse: collapse; font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 10px 0 7px 0;">

                    <thead>
                        <tr style="background-color:{{ $ipColor }}; color:#fff;font-size:10px;">
                            <th style="width:10%; padding:3px; text-align:center;">Sr No</th>
                            <th style="padding:3px; text-align:left;">Labour Name</th>
                            <th style="width:8%; padding:3px; text-align:center;">Qty</th>
                            <th style="padding:3px; text-align:center;">Price</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($labourItems as $labour)
                        <tr style="font-size:10px;">
                            <td style="text-align:center;">{{ $loop->iteration }}</td>

                            <td>
                                {{ $labour->labourItem->item_name ?? 'Labour' }}
                            </td>

                            <td style="text-align:center;">
                                {{ $labour->qty ?? 0 }}
                            </td>

                            <td style="text-align:center;">
                                {{ $ipMoney($labour->price ?? 0) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>

                @endif


            {{-- GST Details --}}
            {{-- @if ($sales->gst_option == 'with_gst' && !empty($taxDetails1))
                @foreach ($taxDetails1 as $tax)
                    <tr>
                        <td
                            style="padding: 6px 12px; text-align: left; background-color: {{ $ipColor }}; color:#fff; font-weight:bold; border: 1px solid #e0e0e0;">
                            {{ $tax['name'] }} ({{ $tax['rate'] }}%)
                        </td>
                        <td style="padding: 6px 12px; text-align: right; border: 1px solid #e0e0e0;">
                            {{ $tax['formatted_amount'] }}
                        </td>
                    </tr>
                @endforeach
            @endif --}}


        


         @php
            $totalthing = count($orderItems);
        @endphp

        @if($ipFloatFooter && $totalthing > 5 && $totalthing <= 15)
            <div style="page-break-before: always;"></div>
        @endif
        @if($ipFloatFooter && $totalthing > 15)
            <div style="page-break-before: auto;"></div>
        @endif


        <div class="footer-section">
            <!-- Bank Details + Totals: table layout -->
            <table class="ip-foot" style="width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 12px; color: #000;">
                <tr>
                    <!-- Bank Details -->
                    <td
                        style="width:40%; vertical-align: top; {{ !empty($ip['show_bank_details']) ? 'border: 1px solid ' . $ipColor . '; padding: 5px 8px; background-color: #eaedf0;' : 'border: none; padding: 0;' }}">
                        @if (!empty($ip['show_bank_details']))
                        <strong style="display: block; margin-bottom: 6px; text-transform: uppercase;">Bank
                            Details:</strong>
                        <table style="width:100%; border-collapse: collapse; font-size: 10px; color: inherit;">
                            <tr>
                                <td style="padding: 0 0 3px 0;">Bank Name :</td>
                                <td style="text-align: right; padding: 0 0 2px 0;">
                                    {{ $setting->bank_name ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0 0 3px 0;">Branch :</td>
                                <td style="text-align: right; padding: 0 0 3px 0;">{{ $setting->branch ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0 0 3px 0;">A/C No :</td>
                                <td style="text-align: right; padding: 0 0 3px 0;">{{ $setting->ac_no ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0;">IFSC Code :</td>
                                <td style="text-align: right; padding: 0;">
                                    {{ $setting->ifsc_code ?? 'N/A' }}
                                </td>
                            </tr>
                        </table>
                        @endif
                          @php
                            $subTotal = 0;
                            $totalDiscount = 0;
                            $totalGstAmount = 0;

                            foreach ($orderItems as $item) {
                                $rentMultiplier = 1;
                                if ($isRentalOrder) {
                                    $rentMultiplier = max(1, (int)($item->rent_value ?? 1));
                                }
                                $lineTotal = $item->price * $item->quantity * $rentMultiplier;
                                $subTotal += $lineTotal;
                                $totalDiscount += (float)($item->discount_amount ?? 0);

                                // Calculate GST using EXCLUSIVE calculation
                                $gstDetails = $item->product_gst_details;
                                if (is_string($gstDetails)) {
                                    $gstDetails = json_decode($gstDetails, true);
                                    if (is_string($gstDetails)) $gstDetails = json_decode($gstDetails, true);
                                }
                                if (is_array($gstDetails) && isset($gstDetails['tax_name'])) {
                                    $gstDetails = [$gstDetails];
                                }
                                if (is_array($gstDetails) && !empty($gstDetails)) {
                                    $itemTotalGstRate = 0;
                                    foreach ($gstDetails as $gRow) {
                                        $itemTotalGstRate += (float)($gRow['tax_rate'] ?? 0);
                                    }
                                    if ($itemTotalGstRate > 0) {
                                        $totalGstAmount += round($lineTotal * ($itemTotalGstRate / 100), 2);
                                    }
                                } else {
                                    $totalGstAmount += (float)($item->product_gst_total ?? 0);
                                }
                            }

                            // base subtotal is just subTotal in exclusive mode
                            $baseSubtotal = $subTotal;

                            // Labour total
                            $labourTotal = 0;
                            if(!empty($labourItems)){
                                foreach ($labourItems as $labour) {
                                    $labourTotal += ($labour->price * $labour->qty);
                                }
                            }

                            $shippingCharge = (float)($sales->shipping ?? 0);

                            $otherChargesTotal = 0;
                            $other_charges = [];
                            if (isset($sales->other_charges)) {
                                $other_charges = is_string($sales->other_charges) ? json_decode($sales->other_charges, true) : $sales->other_charges;
                            }
                            if (is_array($other_charges)) {
                                foreach ($other_charges as $charge) {
                                    $otherChargesTotal += (float)($charge['amount'] ?? 0);
                                }
                            }
                            $sales->other_charges = $other_charges;

                            // Item discounts + bill-level additional discount
                            $itemDiscountTotal = $totalDiscount;
                            $globalDiscountPercent = (float) ($sales->discount ?? 0);
                            $afterItemDiscountBase = max(0, $baseSubtotal - $itemDiscountTotal);
                            $globalDiscountAmount = ($afterItemDiscountBase * $globalDiscountPercent) / 100;
                            $overallDiscount = $itemDiscountTotal + $globalDiscountAmount;
                            $afterDiscount = $subTotal - $overallDiscount;

                            $tdsAmount = (float)($sales->tds_amount ?? 0);

                            $exchangeAmount = (float)($sales->exchange_amount ?? 0);

                            // ✅ Always recalculate Grand Total from components (prices are exclusive, so ADD GST)
                            $grandTotal = max(0, $subTotal + $totalGstAmount - $overallDiscount + $labourTotal + $shippingCharge + $otherChargesTotal - $exchangeAmount - $tdsAmount);

                            // Calculate RETURN AMOUNT
                            $totalReturnAmount = 0;
                            $allItemsFullyReturned = false;

                            if (isset($returns) && $returns->isNotEmpty()) {
                                foreach ($returns as $ret) {
                                    if (!empty($ret->items)) {
                                        foreach ($ret->items as $retItem) {
                                            $lineRetTotal = (float)($retItem->price ?? 0) * (float)($retItem->quantity ?? 0);
                                            $discountRetAmt = (float)($retItem->discount_amount ?? 0);
                                            $gstRetAmt = (float)($retItem->product_gst_total ?? 0);
                                            $totalReturnAmount += ($lineRetTotal - $discountRetAmt + $gstRetAmt);
                                        }
                                    } else {
                                        $totalReturnAmount += (float)($ret->total_amount ?? 0);
                                    }
                                }

                                // Check if all items are fully returned
                                $orderItemsQuantities = [];
                                foreach ($orderItems as $item) {
                                    $orderItemsQuantities[$item->id] = $item->quantity;
                                }

                                $returnedQuantities = [];
                                foreach ($returns as $ret) {
                                    foreach ($ret->items as $retItem) {
                                        if (!isset($returnedQuantities[$retItem->order_item_id])) {
                                            $returnedQuantities[$retItem->order_item_id] = 0;
                                        }
                                        $returnedQuantities[$retItem->order_item_id] += $retItem->quantity;
                                    }
                                }

                                $allItemsFullyReturned = true;
                                foreach ($orderItemsQuantities as $orderItemId => $originalQty) {
                                    $returnedQty = $returnedQuantities[$orderItemId] ?? 0;
                                    if ($returnedQty < $originalQty) {
                                        $allItemsFullyReturned = false;
                                        break;
                                    }
                                }
                            }

                            // Keep return amount aligned with invoice screen:
                            // Add shipping to return amount only when fully returned
                            $totalReturnWithShipping = $allItemsFullyReturned
                                ? $totalReturnAmount + $shippingCharge
                                : $totalReturnAmount;

                            // Calculate PAID AMOUNT
                            $paidAmount = (float)($paidAmount ?? 0);
                            $depositAmountNum = (float)($sales->deposit_amount ?? 0);

                            // Calculate PENDING AMOUNT = Grand Total - Return Amount - Paid Amount
                            $pendingAmount = max(0, $grandTotal - $totalReturnWithShipping - $paidAmount);

                            // Calculate EXTRA PAID (if any)
                            $extraPaid = max(0, $paidAmount - ($grandTotal - $totalReturnWithShipping));

                            // Amount in words
                            $amountInWords = invoice_amount_words(round($grandTotal), $ip['amount_words_format'] ?? 'indian');
                            $ipPartyBalance = !empty($ip['show_party_balance']) ? customer_outstanding_balance($sales->user_id ?? null) : 0;

                            @endphp

                    <!-- QR Code -->
                    <td class="qr-details-box"
                        style="width: 25%; vertical-align: middle; {{ !empty($ip['show_qr']) ? 'border: 1px solid ' . $ipColor . '; padding: 8px 12px; background-color: #eaedf0;' : 'border: none; padding: 0;' }}">
                        <table style="width: 100%; font-size: 12px; border-collapse: collapse; color: inherit;">
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">
                                    @php
                                        $qrCodePath = null;
                                        if (empty($ip['show_qr'])) {
                                            // hidden by Invoice Print settings
                                        } elseif (!empty($sales->custom_qr_code) && file_exists(storage_path('app/public/qr_codes/' . $sales->custom_qr_code))) {
                                            $qrCodePath = storage_path('app/public/qr_codes/' . $sales->custom_qr_code);
                                        } elseif (!empty($setting->qr_code) && file_exists(storage_path('app/public/' . $setting->qr_code))) {
                                            $qrCodePath = storage_path('app/public/' . $setting->qr_code);
                                        }
                                    @endphp

                                    @if ($qrCodePath)
                                        @php
                                            $imageData = base64_encode(file_get_contents($qrCodePath));
                                            $mimeType = mime_content_type($qrCodePath);
                                        @endphp
                                        <img src="data:{{ $mimeType }};base64,{{ $imageData }}"
                                            style="width:80px; height:80px;">
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>

                    <!-- Totals -->
                <td style="width: 40%; border: 1px solid #22b428; padding: 8px 12px; vertical-align: top; background-color: #eaedf0;">
                    <strong style="display: block; margin-bottom: 2px; text-transform: uppercase;">Totals:</strong>
                    <table style="width: 100%; font-size: 10px; border-collapse: collapse; color: inherit;">
                        <tr>
                            <td style="padding: 0 0 3px 0;">Total (Product):</td>
                            <td style="text-align: right; padding: 0 0 3px 0;">
                                {{ $ipMoney($baseSubtotal, $setting) }}
                            </td>
                        </tr>

                        @if($hasGst || $totalGstAmount > 0)
                        <tr>
                            <td style="padding: 0 0 3px 0;">Total GST :</td>
                            <td style="text-align: right; padding: 0 0 3px 0;">
                                {{ $ipMoney($totalGstAmount, $setting) }}
                            </td>
                        </tr>
                        @endif

                        @if($itemDiscountTotal > 0)
                            <tr>
                                <td style="padding: 0 0 3px 0;">Item Discount:</td>
                                <td style="text-align: right; padding: 0 0 3px 0;">
                                    -{{ $ipMoney($itemDiscountTotal, $setting) }}
                                </td>
                            </tr>
                        @endif

                        @if($globalDiscountAmount > 0)
                            <tr>
                                <td style="padding: 0 0 3px 0;">Additional Discount ({{ rtrim(rtrim(number_format($globalDiscountPercent, 2), '0'), '.') }}%):</td>
                                <td style="text-align: right; padding: 0 0 3px 0;">
                                    -{{ $ipMoney($globalDiscountAmount, $setting) }}
                                </td>
                            </tr>
                        @endif

                        @if($overallDiscount > 0)
                            <tr>
                                <td style="padding: 0 0 3px 0;">Total Discount:</td>
                                <td style="text-align: right; padding: 0 0 3px 0;">
                                    -{{ $ipMoney($overallDiscount, $setting) }}
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td style="padding: 0 0 3px 0; font-weight: bold;">SubTotal:</td>
                            <td style="text-align: right; padding: 0 0 3px 0; font-weight: bold;">
                                {{ $ipMoney($baseSubtotal + $totalGstAmount - $overallDiscount, $setting) }}
                            </td>
                        </tr>


                        @if($shippingCharge > 0)
                            <tr>
                                <td style="padding: 0 0 3px 0;">Shipping Charge :</td>
                                <td style="text-align: right; padding: 0 0 3px 0;">
                                    {{ $ipMoney($shippingCharge, $setting) }}
                                </td>
                            </tr>
                        @endif

                        @if(isset($sales->other_charges) && is_array($sales->other_charges))
                            @foreach($sales->other_charges as $charge)
                                @if((float)($charge['amount'] ?? 0) > 0)
                                    <tr>
                                        <td style="padding: 0 0 3px 0;">{{ $charge['name'] ?? 'Other Charge' }} :</td>
                                        <td style="text-align: right; padding: 0 0 3px 0;">
                                            {{ $ipMoney((float)($charge['amount'] ?? 0), $setting) }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif

                        @if($exchangeAmount > 0)
                            @php
                                $exDetails = $sales->exchange_details;
                                if (is_string($exDetails)) {
                                    $exDetails = json_decode($exDetails, true);
                                }
                                $exMatName = '';
                                $exTypeName = '';
                                $exWeight = $sales->exchange_weight ?? null;
                                if (is_array($exDetails)) {
                                    if (!empty($exDetails['items']) && is_array($exDetails['items'])) {
                                        $parts = [];
                                        foreach ($exDetails['items'] as $exItem) {
                                            $label = $exItem['material_name'] ?? '';
                                            if (!empty($exItem['type_name'])) {
                                                $label .= ' - ' . $exItem['type_name'];
                                            }
                                            if (!empty($exItem['weight'])) {
                                                $label .= ' (' . $exItem['weight'] . ')';
                                            }
                                            if ($label !== '') {
                                                $parts[] = $label;
                                            }
                                        }
                                        $exMatName = implode(', ', $parts);
                                        $exWeight = null;
                                    } else {
                                        $exMatName = $exDetails['material_name'] ?? ($sales->exchangeMaterial->name ?? '');
                                        $exTypeName = $exDetails['type_name'] ?? ($sales->exchangeType->type_name ?? '');
                                        $exWeight = $sales->exchange_weight ?? ($exDetails['weight'] ?? null);
                                    }
                                } else {
                                    $exMatName = $sales->exchangeMaterial->name ?? '';
                                    $exTypeName = $sales->exchangeType->type_name ?? '';
                                }
                            @endphp
                            <tr>
                                <td style="padding: 0 0 3px 0;">Exchange Deduction
                                    @if($exMatName)
                                        <span style="font-size: 10px; color: #555;">({{ $exMatName }}{{ $exTypeName ? ' - ' . $exTypeName : '' }}{{ $exWeight ? ' | ' . $exWeight . ' kg' : '' }})</span>
                                    @endif
                                    :
                                </td>
                                <td style="text-align: right; padding: 0 0 3px 0; color: #ea5455;">
                                    -{{ $ipMoney($exchangeAmount, $setting) }}
                                </td>
                            </tr>
                        @endif

                        @if($labourTotal > 0)
                            <tr>
                                <td style="padding: 0 0 3px 0;">Labour Charge :</td>
                                <td style="text-align: right; padding: 0 0 3px 0;">
                                    {{ $ipMoney($labourTotal, $setting) }}
                                </td>
                            </tr>
                        @endif

                        @if(isset($sales) && (float) ($sales->tds_amount ?? 0) > 0)
                            <tr>
                                <td style="padding: 0 0 3px 0;">TDS ({{ (float)($sales->tds_percentage ?? 0) }}%) :</td>
                                <td style="text-align: right; padding: 0 0 3px 0; color: #ea5455;">
                                    -{{ $ipMoney((float)($sales->tds_amount ?? 0), $setting) }}
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td style="padding: 0 0 3px 0; font-weight:bold;">Grand Total :</td>
                            <td style="text-align: right; padding: 0 0 3px 0; font-weight:bold;">
                                {{ $ipMoney(round($grandTotal), $setting) }}
                            </td>
                        </tr>

                        @if($depositAmountNum > 0)
                        <tr>
                            <td style="padding: 0 0 3px 0; color:#dc3545; font-weight:bold;">Deposit/Advance :</td>
                            <td style="text-align: right; padding: 0 0 3px 0; color:#dc3545; font-weight:bold;">
                                {{ $ipMoney($depositAmountNum, $setting) }}
                            </td>
                        </tr>
                        @endif

                        @if($totalReturnAmount > 0)
                        <tr>
                            <td style="padding: 0 0 3px 0; color:#ea5455; font-weight:bold;">
                                Return Amount :
                            </td>
                            <td style="text-align: right; padding: 0 0 3px 0; color:#ea5455; font-weight:bold;">
                                {{ $ipMoney($totalReturnWithShipping, $setting) }}
                            </td>
                        </tr>
                        @endif

                        @if (!empty($ip['show_received']))
                        <tr>
                            <td style="padding: 0px 0 3px 0; color:#2E7D32; font-weight:bold;">
                                Paid Amount :
                            </td>
                            <td style="text-align: right; padding: 0px 0 3px 0; color:#2E7D32; font-weight:bold;">
                                {{ $ipMoney($paidAmount, $setting) }}
                            </td>
                        </tr>
                        @endif

                        @if (!empty($ip['show_balance']))
                        <tr>
                            <td style="padding: 0 0 3px 0; color:#C62828; font-weight:bold;">
                                Pending Amount :
                            </td>
                            <td style="text-align: right; padding: 0 0 3px 0; color:#C62828; font-weight:bold;">
                                {{ $ipMoney($pendingAmount, $setting) }}
                            </td>
                        </tr>
                        @endif

                        @if (!empty($ip['show_party_balance']) && !empty($sales->user_id))
                        <tr>
                            <td style="padding: 0 0 3px 0; font-weight:bold;">Current Balance of Party :</td>
                            <td style="text-align: right; padding: 0 0 3px 0; font-weight:bold;">
                                {{ $ipMoney($ipPartyBalance) }}
                            </td>
                        </tr>
                        @endif

                        @if (!empty($ip['show_you_saved']) && $ipSaved > 0)
                        <tr>
                            <td style="padding: 0 0 3px 0; color:#2E7D32;">You Saved :</td>
                            <td style="text-align: right; padding: 0 0 3px 0; color:#2E7D32;">
                                {{ $ipMoney($ipSaved) }}
                            </td>
                        </tr>
                        @endif

                        @if(!empty($extraPaid) && $extraPaid > 0)
                        <tr>
                            <td style="padding: 0 0 3px 0; color:#d81414; font-weight:bold;">
                                Extra Paid :
                            </td>
                            <td style="text-align: right; padding: 0 0 3px 0; color:#d81414; font-weight:bold;">
                                {{ $ipMoney($extraPaid, $setting) }}
                            </td>
                        </tr>
                        @endif
                    </table>
                </td>

                </tr>
            </table>

                <table class="ip-foot" style="width: 100%; border-collapse: collapse; font-size: 12px; color: #000;">
                    <tr>
                        @if (!empty($ip['show_amount_words']))
                        <td style="width: 60%; border: 2px solid #dee2e6; padding: 8px 12px; vertical-align: top; font-size: 11px;">
                            <strong>{{ $amountInWords }}</strong>
                        </td>
                        @endif
                        <td style="width: {{ !empty($ip['show_amount_words']) ? '40%' : '100%' }}; border: 2px solid #dee2e6; padding: 8px 12px; text-align: right; vertical-align: top; background-color:{{ $ipColor }}; color:#fff;">
                            <strong>Grand Total : {{ $ipMoney(round($grandTotal), $setting) }}</strong>
                        </td>
                    </tr>
                </table>

            @php
                $ipTermsText = trim((string) ($setting->terms_condition_eng ?? ''));
                $ipNoteCells = [];
                if (!empty($ip['show_description'])) {
                    $ipNoteCells[] = 'remarks';
                }
                if (!empty($ip['show_terms']) && $ipTermsText !== '') {
                    $ipNoteCells[] = 'terms';
                }
                $ipSignCells = [];
                if (!empty($ip['show_received_by'])) {
                    $ipSignCells[] = 'received';
                }
                if (!empty($ip['show_delivered_by'])) {
                    $ipSignCells[] = 'delivered';
                }
                if (!empty($ip['show_signature_text'])) {
                    $ipSignCells[] = 'signature';
                }
                $ipFooterRows = count($ipNoteCells) + count($ipSignCells) <= 2
                    ? [array_merge($ipNoteCells, $ipSignCells)]
                    : [$ipNoteCells, $ipSignCells];
            @endphp
            @foreach ($ipFooterRows as $ipCells)
                @if (count($ipCells))
                <table class="ip-foot" style="width: 100%; border-collapse: collapse; font-size: 12px; color: #000; table-layout: fixed;">
                    <tr>
                        @foreach ($ipCells as $ipCell)
                            @if ($ipCell === 'remarks')
                                <td class="ip-sign-cell">
                                    <strong>Remarks :</strong><br>
                                    <span style="white-space: pre-line;">{{ !empty($sales->remarks) ? $sales->remarks : 'N/A' }}</span>
                                </td>
                            @elseif ($ipCell === 'terms')
                                <td class="ip-sign-cell" style="font-size: 10px;">
                                    <strong>Terms &amp; Conditions :</strong><br>
                                    <span style="white-space: pre-line;">{{ $ipTermsText }}</span>
                                </td>
                            @elseif ($ipCell === 'received')
                                <td class="ip-sign-cell">
                                    <p style="margin: 0;"><strong>Received By</strong></p>
                                    <p style="margin: 4px 0 0; font-size: 10px;">Name :</p>
                                    <div style="height: 26px;"></div>
                                    <p style="margin: 0; font-size: 10px;">Signature :</p>
                                </td>
                            @elseif ($ipCell === 'delivered')
                                <td class="ip-sign-cell">
                                    <p style="margin: 0;"><strong>Delivered By</strong></p>
                                    <p style="margin: 4px 0 0; font-size: 10px;">Name :</p>
                                    <div style="height: 26px;"></div>
                                    <p style="margin: 0; font-size: 10px;">Signature :</p>
                                </td>
                            @else
                                <td class="ip-sign-cell" style="text-align: right;">
                                    <p style="margin: 0;">For, {{ $setting->name ?? ' Auto Care' }}</p>
                                    <div style="height: 42px;"></div>
                                    <p style="margin: 0;">({{ $ip['signature_text'] ?? 'Authorized Signatory' }})</p>
                                </td>
                            @endif
                        @endforeach
                    </tr>
                </table>
                @endif
            @endforeach

            @if (!empty($ip['show_acknowledgement']))
                <div class="ip-ack">
                    <table class="ip-foot" style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 2px 4px; vertical-align: top;">
                                <strong>ACKNOWLEDGEMENT</strong><br>
                                {{ $setting->name ?? '' }}<br>
                                Invoice No: {{ $sales->order_number ?? '-' }} &nbsp; | &nbsp;
                                Date: {{ !empty($sales->created_at) ? date('d/m/Y', strtotime($sales->created_at)) : '-' }}<br>
                                Customer: {{ $customer['name'] ?? '-' }} &nbsp; | &nbsp;
                                Amount: {{ $ipMoney(round($grandTotal)) }}
                            </td>
                            <td style="width: 35%; padding: 2px 4px; text-align: right; vertical-align: bottom;">
                                <div style="height: 24px;"></div>
                                Receiver's Seal &amp; Signature
                            </td>
                        </tr>
                    </table>
                </div>
            @endif
        </div>
    </div>
    @endforeach

</body>

</html>
