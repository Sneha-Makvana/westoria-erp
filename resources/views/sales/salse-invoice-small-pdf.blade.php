<!DOCTYPE html>
<html>
<head>
    <title>Invoice PDF</title>
    <meta charset="utf-8">
    @php extract(invoice_thermal_view_data(get_defined_vars())); @endphp
    @include('sales.partials.invoice-custom-boot')
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            width: {{ $paperWmm }}mm;
            max-width: {{ $paperWmm }}mm;
        }
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: {{ max(8, $fontPx) }}px;
            font-weight: bold;
            color: #000;
            margin: 0 auto;
            padding: 2mm 2mm 4mm 2mm;
            width: {{ $wrapWmm }}mm;
            max-width: {{ $wrapWmm }}mm;
            line-height: 1.25;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .shop-name { margin: 0; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .shop-phone { margin: 1px 0 0 0; font-size: 10px; }
        .inv-title { margin: 4px 0; font-size: 11px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td, th { vertical-align: top; word-wrap: break-word; }
        .item-table { margin-top: 4px; }
        .item-table th {
            font-size: 8px;
            font-weight: bold;
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 2px 1px;
        }
        .item-table td { font-size: 8px; padding: 1px 1px; }
        .name-row td { padding-top: 3px; padding-bottom: 0; }
        .c-no { width: 6%; }
        .c-name { width: 40%; }
        .c-mrp, .c-price { width: 18%; text-align: right; }
        .c-amt { width: 18%; text-align: right; }
        .item-name { font-weight: bold; }
        .item-extra { font-size: 7px; font-weight: normal; }
        .disc-line td { padding-top: 0; padding-bottom: 2px; }
        .sum-table { margin-top: 4px; border-top: 1px solid #000; }
        .sum-table td { font-size: 9px; padding: 2px 0; font-weight: bold; }
        .terms { margin-top: 8px; font-size: 8px; font-weight: bold; }
    </style>
</head>
<body class="inv-thermal-body">
@php
    $subTotal = 0;
    $totalQty = 0;
    foreach ($orderItems as $item) {
        $subTotal += ((float) $item->price) * ((float) $item->quantity);
        $totalQty += (float) $item->quantity;
    }
    $labourTotal = 0;
    if (isset($labourItems)) {
        foreach ($labourItems as $labour) {
            $labourTotal += ((float) $labour->price) * ((float) $labour->qty);
            $totalQty += (float) $labour->qty;
        }
    }
    $grandTotal = isset($sales->total_amount) ? (float) $sales->total_amount : ($subTotal + $labourTotal);
    $paidAmount = (float) ($paidAmount ?? 0);
    $totalQtyFormatted = fmod($totalQty, 1.0) == 0.0 ? number_format($totalQty, 0) : number_format($totalQty, 2);
    $payRaw = trim((string) ($paymentModeLabel ?: ($sales->payment_method ?? 'Cash')));
    $saleLabel = ($payRaw === '' || strcasecmp($payRaw, 'cash') === 0) ? 'Cash Sale' : $payRaw;
    $thanks = trim((string) ($setting->footer_text ?? ''));
    if ($thanks === '') {
        $thanks = 'Thanks for business with us!';
    }
    $pendingAmount = $grandTotal - $paidAmount;
    $showSerial = !empty($inv['show_serial']);
    $showQty = !empty($inv['show_item_qty']);
    $showMrp = !empty($inv['show_item_mrp']);
    $showSp = !empty($inv['show_item_sp']);
    $showTotal = !empty($inv['show_item_total']);
    $nameSpan = 1 + ($showMrp ? 1 : 0) + ($showSp ? 1 : 0) + ($showTotal ? 1 : 0);
    $priceColLabel = trim((string) ($spLabel ?? ''));
    if ($priceColLabel === '' || strcasecmp($priceColLabel, 'SP') === 0) {
        $priceColLabel = 'Price';
    }
    $cust = is_array($customer ?? null) ? $customer : [];
    // Prefer company name, then customer name (empty string must not block the fallback)
    $custName = trim((string) (($cust['company_name'] ?? '') ?: ($cust['name'] ?? '')));
    $custPhone = trim((string) ($cust['phone'] ?? ''));
    $custAddress = trim((string) ($cust['address'] ?? ''));
    $custGst = trim((string) ($cust['gst_number'] ?? ($cust['gst'] ?? '')));
@endphp

    <div class="text-center">
        @if (!empty($inv['show_logo']) && !empty($logoBase64))
            <img src="{{ $logoBase64 }}" class="inv-logo" alt="Logo">
        @endif
        @if ($invShowCompanyName)
            <h3 class="shop-name">{{ $setting->name ?? '' }}</h3>
        @endif
        @if ($invShowShopAddress && $invShopAddress !== '')
            <p class="shop-phone">{{ $invShopAddress }}</p>
        @endif
        @if ($invShowShopPhone && !empty($setting->phone))
            <p class="shop-phone">Ph.No.: {{ $setting->phone }}</p>
        @endif
        @if ($invShowShopEmail && $invShopEmail !== '')
            <p class="shop-phone">Email: {{ $invShopEmail }}</p>
        @endif
        @if (!empty($inv['show_shop_gst']) && !empty($setting->gst_num))
            <p class="shop-phone">GST: {{ $setting->gst_num }}</p>
        @endif
        @if (!empty($inv['show_fssai']) && !empty($setting->fssai_number ?? $setting->fssai ?? null))
            <p class="shop-phone">FSSAI: {{ $setting->fssai_number ?? $setting->fssai }}</p>
        @endif
        <div class="inv-title">
            @if (!empty($inv['enable_custom_heading']) && trim((string) ($inv['custom_heading_text'] ?? '')) !== '')
                {{ $inv['custom_heading_text'] }}
            @else
                {{ $invBillTitle ?? 'Invoice' }}
            @endif
        </div>
    </div>

    <table>
        <tr>
            <td class="text-left" style="width:48%;">{{ $saleLabel }}</td>
            <td class="text-right" style="width:52%;">
                Date: {{ date('d/m/Y', strtotime($sales->created_at)) }}<br>
                Time: {{ date('h:i A', strtotime($sales->created_at)) }}
            </td>
        </tr>
    </table>

    @if (!empty($inv['show_customer_name']) || !empty($inv['show_customer_phone']) || !empty($inv['show_customer_address']) || !empty($inv['show_customer_gst']) || !empty($inv['show_salesman']))
        <div style="margin: 3px 0; font-size: 8px;">
            @if (!empty($inv['show_customer_name']) && $custName !== '')
                <div>Customer: {{ $custName }}</div>
            @endif
            @if (!empty($inv['show_customer_phone']) && $custPhone !== '')
                <div>Ph. No.: {{ $custPhone }}</div>
            @endif
            @if (!empty($inv['show_customer_address']) && $custAddress !== '')
                <div>{{ $custAddress }}</div>
            @endif
            @if (!empty($inv['show_customer_gst']) && $custGst !== '')
                <div>Cust. GST: {{ $custGst }}</div>
            @endif
            @if (!empty($inv['show_salesman']) && $salesmanName !== '')
                <div>Salesman: {{ $salesmanName }}</div>
            @endif
        </div>
    @endif

    <table class="item-table">
        <thead>
            <tr>
                @if ($showSerial)
                    <th class="c-no">#</th>
                @endif
                <th class="c-name">Item Name
                    @if ($showQty)
                        <br>Quantity
                    @endif
                </th>
                @if ($showMrp)
                    <th class="c-mrp">MRP</th>
                @endif
                @if ($showSp)
                    <th class="c-price">{{ $priceColLabel }}</th>
                @endif
                @if ($showTotal)
                    <th class="c-amt">Amount</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($orderItems as $index => $item)
                @php
                    $lineMrp = (float) ($item->mrp ?? $item->price);
                    $lineSp = !empty($useMrpAsSp) ? $lineMrp : (float) $item->price;
                    $qty = (float) $item->quantity;
                    $qtyLabel = fmod($qty, 1.0) == 0.0 ? number_format($qty, 0) : number_format($qty, 2);
                    $unit = $invShowUnit ? ($item->product->unit->short_name ?? 'Pcs') : '';
                    $pName = $productName($item->product ?? 'Product');
                    $amount = $lineSp * $qty;
                    $discountAmt = (float) ($item->discount_amount ?? 0);
                    $discountPct = (float) ($item->discount_percentage ?? 0);
                @endphp
                <tr class="name-row">
                    @if ($showSerial)
                        <td class="c-no">{{ $index + 1 }}</td>
                    @endif
                    <td class="item-name" colspan="{{ $nameSpan }}" style="{{ $nameWrapStyle }}">
                        @if (!empty($printHindi))<span class="inv-hi-name">{{ $pName }}</span>@else{{ $pName }}@endif
                        @if (!empty($inv['show_item_barcode']) && !empty($item->product->barcode))
                            <br><span class="item-extra">{{ $item->product->barcode }}</span>
                        @endif
                        @if (!empty($inv['show_hsn']) && !empty($item->product->hsn_code ?? $item->product->hsn ?? null))
                            <br><span class="item-extra">HSN: {{ $item->product->hsn_code ?? $item->product->hsn }}</span>
                        @endif
                        @if (!empty($inv['show_item_description']) && !empty($item->product->description))
                            <br><span class="item-extra">{{ \Illuminate\Support\Str::limit(strip_tags($item->product->description), 40) }}</span>
                        @endif
                        @if (!empty($inv['product_second_line']) && !empty($item->product->short_name ?? $item->variant->name ?? null))
                            <br><span class="item-extra">{{ $item->product->short_name ?? ($item->variant->name ?? '') }}</span>
                        @endif
                        @if (!empty($inv['savings_in_line_item']) && $lineMrp > $lineSp)
                            <br><span class="item-extra">Saved {{ $invMoney(($lineMrp - $lineSp) * $qty) }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    @if ($showSerial)
                        <td></td>
                    @endif
                    <td class="c-name">@if ($showQty){{ $qtyLabel }}{{ $unit }}@endif</td>
                    @if ($showMrp)
                        <td class="c-mrp">{{ $invMoney($lineMrp) }}</td>
                    @endif
                    @if ($showSp)
                        <td class="c-price">{{ $invMoney($lineSp) }}</td>
                    @endif
                    @if ($showTotal)
                        <td class="c-amt">{{ $invMoney($amount) }}</td>
                    @endif
                </tr>
                @if ($discountAmt > 0)
                <tr class="disc-line">
                    @if ($showSerial)
                        <td></td>
                    @endif
                    <td colspan="{{ max(1, $nameSpan - ($showTotal ? 1 : 0)) }}">Disc.({{ rtrim(rtrim(number_format($discountPct, 2), '0'), '.') }}%)</td>
                    @if ($showTotal)
                        <td class="c-amt">-{{ $invMoney($discountAmt) }}</td>
                    @endif
                </tr>
                @endif
            @endforeach
            @if (isset($labourItems) && count($labourItems) > 0)
                @foreach ($labourItems as $lIndex => $labour)
                    @php
                        $lQty = (float) $labour->qty;
                        $lQtyLabel = fmod($lQty, 1.0) == 0.0 ? number_format($lQty, 0) : number_format($lQty, 2);
                        $lAmt = ((float) $labour->price) * $lQty;
                    @endphp
                    <tr class="name-row">
                        @if ($showSerial)
                            <td class="c-no">{{ count($orderItems) + $lIndex + 1 }}</td>
                        @endif
                        <td class="item-name" colspan="{{ $nameSpan }}" style="{{ $nameWrapStyle }}">{{ $labour->labourItem->item_name ?? 'Labour' }}</td>
                    </tr>
                    <tr>
                        @if ($showSerial)
                            <td></td>
                        @endif
                        <td class="c-name">@if ($showQty){{ $lQtyLabel }}@endif</td>
                        @if ($showMrp)
                            <td class="c-mrp"></td>
                        @endif
                        @if ($showSp)
                            <td class="c-price">{{ $invMoney((float) $labour->price) }}</td>
                        @endif
                        @if ($showTotal)
                            <td class="c-amt">{{ $invMoney($lAmt) }}</td>
                        @endif
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <table class="sum-table">
        <tr>
            <td class="text-left">@if (!empty($inv['show_total_item_count']))Total: {{ $totalQtyFormatted }}@else Total @endif</td>
            <td class="text-right">{{ $invMoney($subTotal + $labourTotal) }}</td>
        </tr>
        <tr>
            <td class="text-left">Total</td>
            <td class="text-right">: {{ $invMoney($grandTotal) }}</td>
        </tr>
        @if ($invShowReceived)
        <tr>
            <td class="text-left">Received</td>
            <td class="text-right">: {{ $invMoney($paidAmount) }}</td>
        </tr>
        @endif
        @if ($invShowBalance && $pendingAmount > 0)
        <tr>
            <td class="text-left">Balance</td>
            <td class="text-right">: {{ $invMoney($pendingAmount) }}</td>
        </tr>
        @endif
    </table>

    @include('sales.partials.invoice-custom-footer')

    <div class="terms">
        <div>{{ $thanks }}</div>
    </div>
    @include('sales.partials.invoice-custom-end')
</body>
</html>
