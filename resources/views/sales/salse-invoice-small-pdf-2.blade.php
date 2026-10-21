<!DOCTYPE html>
<html>
<head>
    <title>Invoice PDF</title>
    <meta charset="utf-8">
    @php extract(invoice_thermal_view_data(get_defined_vars())); @endphp
    @include('sales.partials.invoice-custom-boot')
    <style>
        @page {
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: {{ $paperWmm }}mm;
            max-width: {{ $paperWmm }}mm;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: {{ $fontPx }}px;
            font-weight: bold;
            color: #000;
            margin: 0 auto;
            padding: 2mm 2mm 3mm 2mm;
            width: {{ $wrapWmm }}mm;
            max-width: {{ $wrapWmm }}mm;
            line-height: 1.25;
            -webkit-print-color-adjust: exact;
            page-break-inside: avoid;
            overflow: hidden;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .dashed-line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .header h3 {
            margin: 0 0 2px 0;
            font-size: 14px;
            font-weight: bold;
            word-wrap: break-word;
        }

        .header p {
            margin: 0;
            font-size: 10px;
            font-weight: bold;
            word-wrap: break-word;
        }

        table {
            width: 100% !important;
            max-width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.info-table td {
            padding: 1px 0;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        table.item-table {
            margin: 5px 0;
        }

        table.item-table th, table.item-table td {
            padding: 2px 1px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        table.item-table th {
            font-weight: bold;
            border-bottom: 1px dashed #000;
            font-size: 9px;
        }

        table.item-table td {
            font-size: 9px;
        }

        /* Column Widths — Name | MRP | Qty | Amount (tax-detail layout) */
        .w-5 { width: 6%; text-align: left; }
        .w-45 { width: 36%; text-align: left; }
        .w-mrp { width: 16%; text-align: right; }
        .w-15 { width: 18%; text-align: right; }
        .w-15-price { width: 14%; text-align: right; }
        .w-20 { width: 14%; text-align: center; }
        .item-tax-line {
            font-size: 7px;
            font-weight: normal;
            margin-top: 1px;
        }
        .item-table thead th.w-20 {
            padding-left: 0;
        }

        .totals-table {
            width: 100%;
            margin-left: 0;
            margin-top: 5px;
        }
        .totals-table td {
            padding: 1px 0;
            font-size: 9px;
            word-wrap: break-word;
        }
        .totals-table td:first-child {
            text-align: left;
            padding-left: 0;
            width: 60%;
        }
        .totals-table td:last-child {
            text-align: right;
            width: 40%;
        }
        .footer-note {
            margin-top: 5px;
            text-align: center;
            font-size: 9px;
            word-wrap: break-word;
        }
    </style>
</head>
<body class="inv-thermal-body">

    @php
        $subTotal = 0;
        $totalDiscount = 0;
        $totalGst = 0;
        $totalCgst = 0;
        $totalSgst = 0;
        $labourTotal = 0;

        foreach ($orderItems as $item) {
            $lineTotal = $item->price * $item->quantity;
            $subTotal += $lineTotal;
            $totalDiscount += (float) ($item->discount_amount ?? 0);
            $totalGst += (float) ($item->product_gst_total ?? 0);

            $rowCgst = (float) ($item->cgst_amount ?? $item->product_cgst ?? 0);
            $rowSgst = (float) ($item->sgst_amount ?? $item->product_sgst ?? 0);
            if ($rowCgst <= 0 && $rowSgst <= 0) {
                $gstHalf = (float) ($item->product_gst_total ?? 0) / 2;
                $rowCgst = $gstHalf;
                $rowSgst = $gstHalf;
            }
            $totalCgst += $rowCgst;
            $totalSgst += $rowSgst;
        }

        if (isset($labourItems)) {
            foreach ($labourItems as $labour) {
                $labourTotal += $labour->price * $labour->qty;
            }
        }

        $shippingCharge = (float) ($sales->shipping ?? 0);

        $otherChargesTotal = 0;
        if (isset($sales->other_charges) && is_array($sales->other_charges)) {
            foreach ($sales->other_charges as $charge) {
                $otherChargesTotal += (float)($charge['amount'] ?? 0);
            }
        }
        
        $baseSubtotal = $subTotal;
        $afterDiscount = $baseSubtotal - $totalDiscount;
        $globalDiscountPercent = (float) ($sales->discount ?? ($discountPercent ?? 0));
        $afterItemDiscountBase = max(0, $baseSubtotal - $totalDiscount);
        $globalDiscountAmount = ($afterItemDiscountBase * $globalDiscountPercent) / 100;
        $overallDiscount = $totalDiscount + $globalDiscountAmount;
        $afterDiscount = $baseSubtotal - $overallDiscount;
        
        $calculatedGrandTotal = $afterDiscount + $totalGst + $labourTotal + $shippingCharge + $otherChargesTotal;
        $grandTotal = isset($sales->total_amount) ? (float) $sales->total_amount : $calculatedGrandTotal;
        $paidAmount = (float) ($paidAmount ?? 0);
        $pendingAmount = $grandTotal - $paidAmount;

        $totalQty = 0;
        foreach ($orderItems as $item) {
            $totalQty += (float) ($item->quantity ?? 0);
        }
        if (isset($labourItems)) {
            foreach ($labourItems as $labour) {
                $totalQty += (float) ($labour->qty ?? 0);
            }
        }
        $totalQtyFormatted = fmod($totalQty, 1.0) == 0.0
            ? number_format($totalQty, 0)
            : number_format($totalQty, 2);
    @endphp

    <div class="header text-center">
        @if (!empty($inv['show_logo']) && !empty($logoBase64))
            <img src="{{ $logoBase64 }}" class="inv-logo" alt="Logo">
        @endif
        @if ($invShowCompanyName)
            <h3>{{ $setting->name ?? '' }}</h3>
        @endif
        @if ($invShowShopAddress && $invShopAddress !== '')
            <p>{{ $invShopAddress }}</p>
        @endif
        @if ($invShowShopPhone && !empty($setting->phone))
            <p>Ph.No.: {{ $setting->phone }}</p>
        @endif
        @if ($invShowShopEmail && $invShopEmail !== '')
            <p>Email: {{ $invShopEmail }}</p>
        @endif
        @if (!empty($inv['show_shop_gst']) && !empty($setting->gst_num))
            <p>GST: {{ $setting->gst_num }}</p>
        @endif
        @if (!empty($inv['show_fssai']) && !empty($setting->fssai_number ?? $setting->fssai ?? null))
            <p>FSSAI: {{ $setting->fssai_number ?? $setting->fssai }}</p>
        @endif
    </div>

    <div class="dashed-line"></div>
    <div class="text-center bold" style="margin: 4px 0;">
        @if (!empty($inv['enable_custom_heading']) && trim((string) ($inv['custom_heading_text'] ?? '')) !== '')
            {{ $inv['custom_heading_text'] }}
        @else
            {{ $invBillTitle ?? 'Tax Invoice' }}
        @endif
    </div>
    <!-- <div class="dashed-line"></div> -->
    
    <table class="info-table" style="margin-bottom: 4px; width: 100%;">
        <tr>
            <td style="width: 60%; text-align: left;">
                @if (!empty($inv['show_customer_name']))
                <div class="bold">{{ $customer['company_name'] ?: ($customer['name'] ?? 'Walk-in') }}</div>
                @endif
                @if (!empty($inv['show_customer_phone']) && !empty($customer['phone']))
                <div>Ph. No.: {{ $customer['phone'] }}</div>
                @endif
                @if (!empty($inv['show_customer_address']))
                <div style="margin-top: 2px;" class="bold">Bill To:</div>
                <div>{{ $customer['address'] ?? '' }}</div>
                @endif
                @if (!empty($inv['show_customer_gst']) && !empty($customer['gst_number'] ?? $customer['gst'] ?? null))
                <div>Cust. GST: {{ $customer['gst_number'] ?? $customer['gst'] }}</div>
                @endif
                @if (!empty($inv['show_salesman']) && $salesmanName !== '')
                <div>Salesman: {{ $salesmanName }}</div>
                @endif
            </td>
            <td style="width: 40%; text-align: right;">
                Date: {{ date('d/m/Y', strtotime($sales->created_at)) }}<br>
                Time: {{ date('h:i A', strtotime($sales->created_at)) }}<br>
                Invoice No.: {{ $sales->order_number }}
            </td>
        </tr>
    </table>
    
    <div class="dashed-line"></div>
    
    <table class="item-table">
        <thead>
            <tr>
                @if (!empty($inv['show_serial']))
                    <th class="w-5">#</th>
                @endif
                <th class="w-45">Name</th>
                @if (!empty($inv['show_item_mrp']))
                    <th class="w-mrp">MRP</th>
                @endif
                @if (!empty($inv['show_item_qty']))
                    <th class="w-20 text-center">Qty</th>
                @endif
                @if (!empty($inv['show_item_sp']))
                    <th class="w-15-price">{{ $spLabel }}</th>
                @endif
                @if (!empty($inv['show_item_total']))
                    <th class="w-15 text-right">Amount</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($orderItems as $index => $item)
                @php
                    $lineMrp = (float) ($item->mrp ?? $item->price);
                    $lineSp = $useMrpAsSp ? $lineMrp : (float) $item->price;
                    $amount = $lineSp * $item->quantity;
                    $discountAmt = (float) ($item->discount_amount ?? 0);
                    $discountPct = (float) ($item->discount_percentage ?? 0);
                    $unit = $invShowUnit ? ($item->product->unit->short_name ?? 'Pcs') : '';
                    $pName = $productName($item->product ?? 'Product');
                    $itemCgst = (float) ($item->cgst_amount ?? $item->product_cgst ?? 0);
                    $itemSgst = (float) ($item->sgst_amount ?? $item->product_sgst ?? 0);
                    if ($itemCgst <= 0 && $itemSgst <= 0) {
                        $gstHalf = (float) ($item->product_gst_total ?? 0) / 2;
                        $itemCgst = $gstHalf;
                        $itemSgst = $gstHalf;
                    }
                @endphp
                <tr>
                    @if (!empty($inv['show_serial']))
                        <td class="w-5">{{ $index + 1 }}</td>
                    @endif
                    <td class="w-45" style="{{ $nameWrapStyle }}">
                        @if (!empty($printHindi))<span class="inv-hi-name">{{ $pName }}</span>@else{{ $pName }}@endif
                        @if ($itemCgst > 0 || $itemSgst > 0)
                            <div class="item-tax-line">CGST: {{ $invMoney($itemCgst) }} | SGST: {{ $invMoney($itemSgst) }}</div>
                        @endif
                        @if (!empty($inv['show_item_barcode']) && !empty($item->product->barcode))
                            <br><span style="font-size:7px;">{{ $item->product->barcode }}</span>
                        @endif
                        @if (!empty($inv['show_hsn']) && !empty($item->product->hsn_code ?? $item->product->hsn ?? null))
                            <br><span style="font-size:7px;">HSN: {{ $item->product->hsn_code ?? $item->product->hsn }}</span>
                        @endif
                        @if (!empty($inv['show_item_description']) && !empty($item->product->description))
                            <br><span style="font-size:7px;">{{ \Illuminate\Support\Str::limit(strip_tags($item->product->description), 40) }}</span>
                        @endif
                        @if (!empty($inv['product_second_line']) && !empty($item->product->short_name ?? $item->variant->name ?? null))
                            <br><span style="font-size:7px;">{{ $item->product->short_name ?? ($item->variant->name ?? '') }}</span>
                        @endif
                    </td>
                    @if (!empty($inv['show_item_mrp']))
                        <td class="w-mrp">{{ $invMoney($lineMrp) }}</td>
                    @endif
                    @if (!empty($inv['show_item_qty']))
                        <td class="w-20 text-center">{{ $item->quantity }} {{ $unit }}</td>
                    @endif
                    @if (!empty($inv['show_item_sp']))
                        <td class="w-15-price">{{ $invMoney($lineSp) }}</td>
                    @endif
                    @if (!empty($inv['show_item_total']))
                        <td class="w-15 text-right">{{ $invMoney($amount) }}</td>
                    @endif
                </tr>
                @if ($discountAmt > 0)
                <tr>
                    @if (!empty($inv['show_serial']))
                        <td class="w-5"></td>
                    @endif
                    <td class="w-45">Disc.({{ rtrim(rtrim(number_format($discountPct, 2), '0'), '.') }}%)</td>
                    @if (!empty($inv['show_item_mrp']))
                        <td></td>
                    @endif
                    @if (!empty($inv['show_item_qty']))
                        <td></td>
                    @endif
                    @if (!empty($inv['show_item_sp']))
                        <td></td>
                    @endif
                    @if (!empty($inv['show_item_total']))
                        <td class="w-15 text-right">-{{ $invMoney($discountAmt) }}</td>
                    @endif
                </tr>
                @endif
            @endforeach
            @if (isset($labourItems) && count($labourItems) > 0)
                @foreach ($labourItems as $l_index => $labour)
                    @php 
                        $labourAmt = $labour->price * $labour->qty; 
                    @endphp
                    <tr>
                        @if (!empty($inv['show_serial']))
                            <td class="w-5">{{ count($orderItems) + $l_index + 1 }}</td>
                        @endif
                        <td class="w-45">
                            {{ $labour->labourItem->item_name }} (Labour)
                        </td>
                        @if (!empty($inv['show_item_mrp']))
                            <td class="w-mrp"></td>
                        @endif
                        @if (!empty($inv['show_item_qty']))
                            <td class="w-20 text-center">{{ $labour->qty }}</td>
                        @endif
                        @if (!empty($inv['show_item_sp']))
                            <td class="w-15-price">{{ $invMoney($labour->price) }}</td>
                        @endif
                        @if (!empty($inv['show_item_total']))
                            <td class="w-15 text-right">{{ $invMoney($labourAmt) }}</td>
                        @endif
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    
    <div class="dashed-line"></div>
    
    <table class="info-table bold" style="margin: 4px 0; font-size: 9px;">
        <tr>
            <td style="width: 35%; text-align: left;">Total:</td>
            <td style="width: 30%; text-align: center;">
                @if (!empty($inv['show_total_item_count']))
                    {{ $totalQtyFormatted }}
                @endif
            </td>
            <td style="width: 35%; text-align: right;">{{ $invMoney($baseSubtotal + $labourTotal) }}</td>
        </tr>
    </table>
    
    <!-- <div class="dashed-line"></div> -->
    
    <table class="totals-table">
        @if ($totalCgst > 0)
        <tr>
            <td class="text-left">Total CGST</td>
            <td class="text-right">{{ $invMoney($totalCgst) }}</td>
        </tr>
        @endif
        @if ($totalSgst > 0)
        <tr>
            <td class="text-left">Total SGST</td>
            <td class="text-right">{{ $invMoney($totalSgst) }}</td>
        </tr>
        @endif
        @if (($totalCgst + $totalSgst) > 0)
        <tr>
            <td class="text-left">Total Tax</td>
            <td class="text-right">{{ $invMoney($totalCgst + $totalSgst) }}</td>
        </tr>
        @elseif ($totalGst > 0)
        <tr>
            <td class="text-left">Tax(GST)</td>
            <td class="text-right">{{ $invMoney($totalGst) }}</td>
        </tr>
        @endif
        @if ($shippingCharge > 0)
        <tr>
            <td class="text-left">Shipping</td>
            <td class="text-right">{{ $invMoney($shippingCharge) }}</td>
        </tr>
        @endif
        @if ($otherChargesTotal > 0)
        <tr>
            <td class="text-left">Other</td>
            <td class="text-right">{{ $invMoney($otherChargesTotal) }}</td>
        </tr>
        @endif
        @if ($totalDiscount > 0)
        <tr>
            <td class="text-left">Item Disc.</td>
            <td class="text-right">-{{ $invMoney($totalDiscount) }}</td>
        </tr>
        @endif
        @if ($globalDiscountAmount > 0)
        <tr>
            <td class="text-left">Add. Disc.({{ rtrim(rtrim(number_format($globalDiscountPercent, 2), '0'), '.') }}%)</td>
            <td class="text-right">-{{ $invMoney($globalDiscountAmount) }}</td>
        </tr>
        @endif
        @if ($overallDiscount > 0)
        <tr>
            <td class="text-left">Total Disc.</td>
            <td class="text-right">-{{ $invMoney($overallDiscount) }}</td>
        </tr>
        @endif
        @if(isset($sales->tds_percentage) && $sales->tds_percentage > 0)
        <tr>
            <td class="text-left">TDS({{ (float)$sales->tds_percentage }}%)</td>
            <td class="text-right">-{{ $invMoney((float)($sales->tds_amount ?? 0)) }}</td>
        </tr>
        @endif
        <tr>
            <td class="text-left bold">Total</td>
            <td class="text-right bold">{{ $invMoney($grandTotal) }}</td>
        </tr>
        @if ($invShowReceived)
        <tr>
            <td class="text-left">Received</td>
            <td class="text-right">{{ $invMoney($paidAmount) }}</td>
        </tr>
        @endif
        @if ($invShowBalance)
        <tr>
            <td class="text-left">Balance</td>
            <td class="text-right">{{ $invMoney($pendingAmount) }}</td>
        </tr>
        @endif
    </table>
    
    <div class="dashed-line"></div>

    @include('sales.partials.invoice-custom-footer')
    
    @if ($pendingAmount > 0)
    <div class="footer-note">
        Balance to be paid in {{ $sales->due_days ?? 3 }} days
    </div>
    @else
    <div class="footer-note">
        {{ $setting->footer_text ?? 'Thank You! Visit Again..' }}
    </div>
    @endif

    @include('sales.partials.invoice-custom-end')
</body>
</html>
