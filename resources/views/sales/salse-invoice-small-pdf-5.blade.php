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

        * { box-sizing: border-box; }

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
            padding: 2mm 2mm 5mm 2mm;
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
        }

        .header p {
            margin: 0;
            font-size: 10px;
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
            text-align: left;
            font-weight: bold;
            border-bottom: 1px dashed #000;
            font-size: 9px;
        }

        table.item-table td {
            font-size: 9px;
        }

        /* Column Widths */
        .w-5 { width: 6%; }
        .w-45 { width: 42%; }
        .w-15 { width: 16%; text-align: center !important; }
        .w-15-price { width: 16%; text-align: center !important; }
        .w-20 { width: 20%; text-align: right !important; }

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
        $labourTotal = 0;

        foreach ($orderItems as $item) {
            $lineTotal = $item->price * $item->quantity;
            $subTotal += $lineTotal;
            $totalDiscount += (float) ($item->discount_amount ?? 0);
            $totalGst += (float) ($item->product_gst_total ?? 0);
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
    
    <!-- Customer & Invoice Info Block: Side-by-side layout -->
    <table class="info-table" style="margin-bottom: 4px; width: 100%;">
        <tr>
            <!-- Left side: Customer info -->
            <td style="width: 50%; text-align: left; vertical-align: top;">
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
            <!-- Right side: Invoice details -->
            <td style="width: 50%; text-align: right; vertical-align: top;">
                <div>Date: {{ date('d/m/Y', strtotime($sales->created_at)) }}</div>
                <div>Time: {{ date('h:i A', strtotime($sales->created_at)) }}</div>
                <div style="margin-top: 2px;">Invoice No.: {{ $sales->order_number }}</div>
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
                @if (!empty($inv['show_item_qty']))
                    <th class="w-15">Qty</th>
                @endif
                @if (!empty($inv['show_item_sp']))
                    <th class="w-15-price">{{ $spLabel }}</th>
                @endif
                @if (!empty($inv['show_item_total']))
                    <th class="w-20 text-right">Amount</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($orderItems as $index => $item)
                @php
                    $lineMrp = (float) ($item->mrp ?? $item->price);
                    $lineSp = $useMrpAsSp ? $lineMrp : (float) $item->price;
                    $amount = $lineSp * $item->quantity;
                    $discountAmt = $item->discount_amount ?? 0;
                    $finalAmt = $amount - $discountAmt;
                    $unit = $invShowUnit ? ($item->product->unit->short_name ?? 'Pcs') : '';
                    $pName = $productName($item->product ?? 'Product');
                @endphp
                <!-- Main item row -->
                <tr>
                    @if (!empty($inv['show_serial']))
                        <td class="w-5">{{ $index + 1 }}</td>
                    @endif
                    <td class="w-45" style="{{ $nameWrapStyle }}">
                        @if (!empty($printHindi))<span class="inv-hi-name">{{ $pName }}</span>@else{{ $pName }}@endif
                        @if (!empty($inv['show_item_mrp']))
                            <br><span style="font-size: 8px;">MRP: {{ $invMoney($lineMrp) }}</span>
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
                    @if (!empty($inv['show_item_qty']))
                        <td class="w-15 text-center" style="white-space: nowrap;">{{ (float)$item->quantity }} {{ $unit }}</td>
                    @endif
                    @if (!empty($inv['show_item_sp']))
                        <td class="w-15-price">{{ $invMoney($lineSp) }}</td>
                    @endif
                    @if (!empty($inv['show_item_total']))
                        <td class="w-20 text-right">{{ $invMoney($amount) }}</td>
                    @endif
                </tr>
                <!-- Discount sub-row (if discount exists) -->
                @if($discountAmt > 0)
                <tr style="font-size: 9px;">
                    @if (!empty($inv['show_serial']))
                        <td class="w-5"></td>
                    @endif
                    <td class="w-45" style="padding-left: 30px;">Disc.({{ (float)($item->discount_percentage ?? 0) }}%)</td>
                    @if (!empty($inv['show_item_qty']))
                        <td class="w-15"></td>
                    @endif
                    @if (!empty($inv['show_item_sp']))
                        <td class="w-15-price"></td>
                    @endif
                    @if (!empty($inv['show_item_total']))
                        <td class="w-20 text-right">-{{ $invMoney($discountAmt) }}</td>
                    @endif
                </tr>
                <!-- Final amount sub-row (if discount exists) -->
                <tr style="font-size: 9px;">
                    @if (!empty($inv['show_serial']))
                        <td class="w-5"></td>
                    @endif
                    <td class="w-45" style="padding-left: 30px;">Final amount</td>
                    @if (!empty($inv['show_item_qty']))
                        <td class="w-15"></td>
                    @endif
                    @if (!empty($inv['show_item_sp']))
                        <td class="w-15-price"></td>
                    @endif
                    @if (!empty($inv['show_item_total']))
                        <td class="w-20 text-right">{{ $invMoney($finalAmt) }}</td>
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
                        <td class="w-45">{{ $labour->labourItem->item_name }} (Labour)</td>
                        @if (!empty($inv['show_item_qty']))
                            <td class="w-15 text-center">{{ $labour->qty }}</td>
                        @endif
                        @if (!empty($inv['show_item_sp']))
                            <td class="w-15-price">{{ $invMoney($labour->price) }}</td>
                        @endif
                        @if (!empty($inv['show_item_total']))
                            <td class="w-20 text-right">{{ $invMoney($labourAmt) }}</td>
                        @endif
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    
    <div class="dashed-line"></div>
    
    <!-- Totals Summary Row -->
    <table class="info-table" style="margin: 4px 0; font-size: 9px;">
        <tr>
            <td style="width: 33%; text-align: left;">Total</td>
            <td style="width: 34%; text-align: center;">
                @if (!empty($inv['show_total_item_count']))
                    {{ $totalQtyFormatted }}
                @endif
            </td>
            <td style="width: 33%; text-align: right;">{{ $invMoney($baseSubtotal + $labourTotal) }}</td>
        </tr>
    </table>
    
    <!-- <div class="dashed-line"></div> -->
    
    <!-- Totals Breakdown Block (indented) -->
    <!-- <table style="width: 100%; margin-top: 5px; font-size: 9px;">
        @if ($totalGst > 0)
        <tr>
            <td style="width: 60%; text-align: left; padding-left: 15px;">Tax(GST)</td>
            <td style="width: 40%; text-align: right;">{{ $invMoney($totalGst) }}</td>
        </tr>
        @endif
        @if ($shippingCharge > 0)
        <tr>
            <td style="width: 60%; text-align: left; padding-left: 15px;">Shipping</td>
            <td style="width: 40%; text-align: right;">{{ $invMoney($shippingCharge) }}</td>
        </tr>
        @endif
        @if ($otherChargesTotal > 0)
        <tr>
            <td style="width: 60%; text-align: left; padding-left: 15px;">Other</td>
            <td style="width: 40%; text-align: right;">{{ $invMoney($otherChargesTotal) }}</td>
        </tr>
        @endif
        @if ($globalDiscountAmount > 0 || $globalDiscountPercent > 0)
        <tr>
            <td style="width: 60%; text-align: left; padding-left: 15px;">Disc.({{ (float)$globalDiscountPercent }}%)</td>
            <td style="width: 40%; text-align: right;">-{{ $invMoney($globalDiscountAmount) }}</td>
        </tr>
        @endif
        @if ($overallDiscount > 0)
        <tr>
            <td style="width: 60%; text-align: left; padding-left: 15px;">Total Disc.</td>
            <td style="width: 40%; text-align: right;">-{{ $invMoney($overallDiscount) }}</td>
        </tr>
        @endif
        @if(isset($sales->tds_percentage) && $sales->tds_percentage > 0)
        <tr>
            <td style="width: 60%; text-align: left; padding-left: 15px;">TDS({{ (float)$sales->tds_percentage }}%)</td>
            <td style="width: 40%; text-align: right;">-{{ $invMoney((float)($sales->tds_amount ?? 0)) }}</td>
        </tr>
        @endif
        <tr style="font-weight: bold;">
            <td style="width: 60%; text-align: left; padding-left: 15px;">Total</td>
            <td style="width: 40%; text-align: right;">{{ $invMoney($grandTotal) }}</td>
        </tr>
        <tr>
            <td style="width: 60%; text-align: left; padding-left: 15px;">Received</td>
            <td style="width: 40%; text-align: right;">{{ $invMoney($paidAmount) }}</td>
        </tr>
        <tr>
            <td style="width: 60%; text-align: left; padding-left: 15px;">Balance</td>
            <td style="width: 40%; text-align: right;">{{ $invMoney($pendingAmount) }}</td>
        </tr>
    </table>
     -->
    <table class="item-table" style="margin: 2px 0 0 0;">
    @if ($totalGst > 0)
    <tr>
        @if (!empty($inv['show_serial']))
            <td class="w-5"></td>
        @endif
        <td class="w-45" style="padding-left: 22px;">Tax(GST)</td>
        @if (!empty($inv['show_item_qty']))
            <td class="w-15"></td>
        @endif
        @if (!empty($inv['show_item_sp']))
            <td class="w-15-price"></td>
        @endif
        <td class="w-20 text-right">{{ $invMoney($totalGst) }}</td>
    </tr>
    @endif
    @if ($shippingCharge > 0)
    <tr>
        @if (!empty($inv['show_serial']))
            <td class="w-5"></td>
        @endif
        <td class="w-45" style="padding-left: 22px;">Shipping</td>
        @if (!empty($inv['show_item_qty']))
            <td class="w-15"></td>
        @endif
        @if (!empty($inv['show_item_sp']))
            <td class="w-15-price"></td>
        @endif
        <td class="w-20 text-right">{{ $invMoney($shippingCharge) }}</td>
    </tr>
    @endif
    @if ($otherChargesTotal > 0)
    <tr>
        @if (!empty($inv['show_serial']))
            <td class="w-5"></td>
        @endif
        <td class="w-45" style="padding-left: 22px;">Other</td>
        @if (!empty($inv['show_item_qty']))
            <td class="w-15"></td>
        @endif
        @if (!empty($inv['show_item_sp']))
            <td class="w-15-price"></td>
        @endif
        <td class="w-20 text-right">{{ $invMoney($otherChargesTotal) }}</td>
    </tr>
    @endif
    @if ($totalDiscount > 0)
    <tr>
        @if (!empty($inv['show_serial']))
            <td class="w-5"></td>
        @endif
        <td class="w-45" style="padding-left: 22px;">Item Disc.</td>
        @if (!empty($inv['show_item_qty']))
            <td class="w-15"></td>
        @endif
        @if (!empty($inv['show_item_sp']))
            <td class="w-15-price"></td>
        @endif
        <td class="w-20 text-right">-{{ $invMoney($totalDiscount) }}</td>
    </tr>
    @endif
    @if ($globalDiscountAmount > 0)
    <tr>
        @if (!empty($inv['show_serial']))
            <td class="w-5"></td>
        @endif
        <td class="w-45" style="padding-left: 22px;">Add. Disc.({{ rtrim(rtrim(number_format($globalDiscountPercent, 2), '0'), '.') }}%)</td>
        @if (!empty($inv['show_item_qty']))
            <td class="w-15"></td>
        @endif
        @if (!empty($inv['show_item_sp']))
            <td class="w-15-price"></td>
        @endif
        <td class="w-20 text-right">-{{ $invMoney($globalDiscountAmount) }}</td>
    </tr>
    @endif
    @if ($overallDiscount > 0)
    <tr>
        @if (!empty($inv['show_serial']))
            <td class="w-5"></td>
        @endif
        <td class="w-45" style="padding-left: 22px;">Total Disc.</td>
        @if (!empty($inv['show_item_qty']))
            <td class="w-15"></td>
        @endif
        @if (!empty($inv['show_item_sp']))
            <td class="w-15-price"></td>
        @endif
        <td class="w-20 text-right">-{{ $invMoney($overallDiscount) }}</td>
    </tr>
    @endif
    @if(isset($sales->tds_percentage) && $sales->tds_percentage > 0)
    <tr>
        @if (!empty($inv['show_serial']))
            <td class="w-5"></td>
        @endif
        <td class="w-45" style="padding-left: 22px;">TDS({{ (float)$sales->tds_percentage }}%)</td>
        @if (!empty($inv['show_item_qty']))
            <td class="w-15"></td>
        @endif
        @if (!empty($inv['show_item_sp']))
            <td class="w-15-price"></td>
        @endif
        <td class="w-20 text-right">-{{ $invMoney((float)($sales->tds_amount ?? 0)) }}</td>
    </tr>
    @endif
    <tr style="font-weight: bold;">
        @if (!empty($inv['show_serial']))
            <td class="w-5"></td>
        @endif
        <td class="w-45" style="padding-left: 22px;">Total</td>
        @if (!empty($inv['show_item_qty']))
            <td class="w-15"></td>
        @endif
        @if (!empty($inv['show_item_sp']))
            <td class="w-15-price"></td>
        @endif
        <td class="w-20 text-right">{{ $invMoney($grandTotal) }}</td>
    </tr>
</table>

    {{-- Payment split breakdown (BillingFast-style) --}}
    @if ($invShowReceived || $invShowBalance)
    <div style="border:1px solid #000;margin:6px 0;padding:3px 4px;">
        <div class="bold text-center" style="font-size:9px;margin-bottom:3px;border-bottom:1px dashed #000;padding-bottom:2px;">
            Payment Details
        </div>
        <table class="totals-table" style="margin:0;">
            @if (!$invShowReceived)
            @elseif (isset($allPayments) && count($allPayments) > 0)
                @foreach ($allPayments as $payment)
                    @php
                        $payLabel = trim((string) ($payment->payment_type ?? $payment->payment_method ?? $payment->payment_mode ?? ''));
                        $payLabel = $payLabel !== '' ? ucfirst($payLabel) : 'Paid';
                        $payAmt = (float) ($payment->payment_amount ?? 0);
                    @endphp
                    <tr>
                        <td class="text-left bold">{{ $payLabel }}</td>
                        <td class="text-right bold">{{ $invMoney($payAmt) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="text-left bold">Paid</td>
                    <td class="text-right bold">{{ $invMoney($paidAmount) }}</td>
                </tr>
            @endif
            @if ($invShowBalance)
            <tr>
                <td class="text-left">Balance</td>
                <td class="text-right">{{ $invMoney($pendingAmount) }}</td>
            </tr>
            @endif
        </table>
    </div>
    @endif

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
