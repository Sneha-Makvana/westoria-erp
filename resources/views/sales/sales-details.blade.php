@extends('layout.app')

@section('title', $pageTitle ?? 'Sale Details')

@section('content')
    <style>
        .logo_img {
            max-width: 150px;
            height: auto;
        }

        .emi-summary-row {
            display: none;
            align-items: flex-start !important;
            border-top: 1px dashed #ddd;
            padding-top: 10px;
        }

        .emi-summary-row h4 {
            color: #1b2850;
        }

        .emi-summary-box {
            width: 100%;
            max-width: 320px;
            margin-left: auto;
            text-align: right;
            line-height: 1.45;
        }

        .emi-summary-box .emi-formula {
            display: inline-block;
            color: #1b2850;
            font-size: 15px;
            font-weight: 700;
        }

        .emi-summary-box .emi-meta {
            color: #555;
            font-size: 12px;
        }

        .emi-summary-box .emi-note {
            color: #6c757d;
            font-size: 11px;
        }

        /* Product Name Column - Allow Wrapping */
        .invoice-box table tbody tr.details td:first-child,
        .invoice-box table tbody tr.details td:nth-child(1) {
            white-space: normal !important;
            word-wrap: break-word;
            word-break: break-word;
            max-width: 300px;
            min-width: 200px;
        }

        .invoice-box table tbody tr.details td:first-child a,
        .invoice-box table tbody tr.details td:nth-child(1) a {
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
            max-width: 100%;
        }

        .invoice-box table tr.heading td:first-child {
            min-width: 200px;
            max-width: 300px;
        }

        .sd-mobile-item,
        .sd-billed-head-row {
            display: none !important;
        }

        @media (max-width: 767.98px) {
            .sd-page {
                padding-left: 10px !important;
                padding-right: 10px !important;
                padding-bottom: 96px !important;
                overflow-x: hidden;
            }

            .sd-page .page-header {
                flex-direction: column;
                align-items: stretch !important;
                gap: 10px;
                margin-bottom: 12px !important;
            }

            .sd-page .page-title {
                text-align: left !important;
            }

            .sd-page .page-title h4 {
                font-size: 18px;
                margin: 0;
            }

            .sd-page .page-btn {
                display: flex !important;
                flex-wrap: wrap !important;
                gap: 8px !important;
                width: 100% !important;
                margin: 0 !important;
            }

            .sd-page .page-btn a,
            .sd-page .page-btn button {
                flex: 1 1 calc(50% - 8px);
                margin: 0 !important;
                min-height: 44px;
                padding: 10px 8px !important;
                font-size: 13px !important;
                font-weight: 600;
                border-radius: 10px !important;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                gap: 6px;
                width: auto !important;
                max-width: none !important;
                white-space: nowrap !important;
                line-height: 1.2;
            }

            .sd-page .card {
                border-radius: 12px;
                overflow: hidden;
            }

            .sd-page .card-body {
                padding: 12px !important;
            }

            .sd-page .invoice-box,
            .sd-page .invoice-box.table-height {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 0 16px !important;
                overflow: visible !important;
            }

            .sd-page .invoice-box table,
            .sd-page .invoice-box td,
            .sd-page .invoice-box h4,
            .sd-page .invoice-box h5 {
                font-size: 13px;
                line-height: 1.35;
            }

            .sd-page .invoice-box table {
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
            }

            .sd-page .invoice-box > table,
            .sd-page .invoice-box table.product-list,
            .sd-page .invoice-box table.product-list tbody,
            .sd-page #product-details {
                display: block;
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
            }

            .sd-page #product-details > tr.top,
            .sd-page #product-details > tr.top > td {
                display: block;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
            }

            .sd-page .sd-invoice-title {
                text-align: left !important;
                margin-top: 8px !important;
            }

            .sd-page .invoice-box table tbody tr.details td:first-child,
            .sd-page .invoice-box table tbody tr.details td:nth-child(1),
            .sd-page .invoice-box table tr.heading td:first-child,
            .sd-page .invoice-box table tr.product-row td:first-child {
                min-width: 0 !important;
                max-width: none !important;
                white-space: normal !important;
            }

            .sd-page .invoice-box .row {
                display: flex !important;
                flex-direction: column !important;
                margin: 0 !important;
                gap: 4px;
            }

            .sd-page .invoice-box .col-6 {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
                padding: 0 !important;
                text-align: left !important;
            }

            .sd-page .invoice-box h1 {
                font-size: 22px !important;
                margin: 4px 0 0;
            }

            .sd-page .logo_img {
                max-width: 120px;
                height: auto;
            }

            .sd-page .invoice-box tr.sd-logo-row,
            .sd-page .invoice-box tr.sd-logo-row > td {
                display: block;
                width: 100% !important;
                padding: 0 !important;
            }

            .sd-page .invoice-box tr.sd-info-row {
                display: block;
                width: 100%;
            }

            .sd-page .invoice-box tr.sd-info-row > td {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 10px 0 14px !important;
                border-bottom: 1px solid #f0f1f4;
                word-break: break-word;
                overflow-wrap: anywhere;
            }

            .sd-page .invoice-box tr.sd-info-row > td:last-child {
                border-bottom: 0;
            }

            .sd-page .invoice-box tr.sd-info-row table {
                display: table;
                width: 100%;
                border-collapse: collapse;
            }

            .sd-page .invoice-box tr.sd-info-row table tr {
                display: table-row;
            }

            .sd-page .invoice-box tr.sd-info-row table td {
                display: table-cell !important;
                width: auto !important;
                max-width: none !important;
                padding: 3px 0 !important;
                border: none !important;
            }

            .sd-page #product-details > tr.heading {
                display: none !important;
            }

            .sd-page #product-details > tr.sd-billed-head-row,
            .sd-page #product-details > tr.sd-billed-head-row > td {
                display: block !important;
                width: 100% !important;
                padding: 0 !important;
                border: 0 !important;
            }

            .sd-page .sd-billed-head {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 4px 2px 12px;
                color: #1b2850;
                font-size: 16px;
                font-weight: 700;
            }

            .sd-page .sd-billed-head i {
                color: #3b82f6;
                font-size: 18px;
            }

            .sd-page #product-details > tr.product-row {
                display: block;
                width: 100%;
                border: 0;
                background: transparent;
                box-shadow: none;
                margin: 0;
                padding: 0;
            }

            .sd-page #product-details > tr.product-row > td {
                display: none !important;
            }

            .sd-page #product-details > tr.product-row > td.sd-mobile-item {
                display: block !important;
                width: 100% !important;
                min-width: 0 !important;
                max-width: none !important;
                padding: 0 0 10px !important;
                border: 0 !important;
            }

            .sd-page .sd-bill-card {
                background: #eef4fb;
                border-radius: 14px;
                padding: 12px 14px;
            }

            .sd-page .sd-bill-top {
                display: flex;
                align-items: flex-start;
                gap: 10px;
            }

            .sd-page .sd-bill-no {
                flex: 0 0 28px;
                width: 28px;
                height: 28px;
                border-radius: 50%;
                background: #3b82f6;
                color: #fff;
                font-size: 11px;
                font-weight: 700;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                line-height: 1;
            }

            .sd-page .sd-bill-name {
                flex: 1;
                min-width: 0;
                color: #1b3a6b;
                font-size: 14px;
                font-weight: 700;
                line-height: 1.35;
                word-break: break-word;
            }

            .sd-page .sd-bill-name a {
                color: inherit !important;
                text-decoration: none !important;
            }

            .sd-page .sd-bill-name small {
                display: block;
                margin-top: 2px;
                color: #8b9cb3;
                font-size: 11px;
                font-weight: 500;
            }

            .sd-page .sd-bill-amt {
                flex: 0 0 auto;
                color: #1b3a6b;
                font-size: 15px;
                font-weight: 700;
                white-space: nowrap;
                padding-top: 2px;
            }

            .sd-page .sd-bill-sub,
            .sd-page .sd-bill-disc,
            .sd-page .sd-bill-tax {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 10px;
                margin-top: 8px;
                padding-left: 38px;
                font-size: 12px;
                line-height: 1.35;
            }

            .sd-page .sd-bill-sub {
                color: #8b9cb3;
            }

            .sd-page .sd-bill-sub span:last-child,
            .sd-page .sd-bill-tax span:last-child {
                text-align: right;
            }

            .sd-page .sd-bill-disc {
                color: #f08a3a;
                font-weight: 600;
            }

            .sd-page .sd-bill-tax {
                color: #64748b;
            }

            .sd-page #return-history-details > tr.heading:not(:first-child),
            .sd-page #rental-return-history-details > tr.heading:not(:first-child) {
                display: none !important;
            }

            .sd-page #return-history-details > tr.heading:first-child,
            .sd-page #rental-return-history-details > tr.heading:first-child {
                display: block;
                text-align: center;
                margin-bottom: 8px;
                border-radius: 8px;
            }

            .sd-page #return-history-details > tr.details,
            .sd-page #rental-return-history-details > tr.details {
                display: block;
                width: 100%;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                background: #fff;
                margin: 0 0 12px;
                padding: 12px 14px;
            }

            .sd-page #return-history-details > tr.details td,
            .sd-page #rental-return-history-details > tr.details td {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                width: 100% !important;
                min-width: 0 !important;
                padding: 6px 0 !important;
                white-space: normal !important;
                word-break: break-word;
            }

            .sd-page #return-history-details > tr.details td[data-label]::before,
            .sd-page #rental-return-history-details > tr.details td[data-label]::before {
                content: attr(data-label);
                flex: 0 0 42%;
                color: #6b7280;
                font-size: 12px;
                font-weight: 600;
            }

            .sd-page .col-lg-6,
            .sd-page .col-lg-6.total-order {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .sd-page .total-order {
                max-width: 100% !important;
                margin: 0 !important;
            }

            .sd-page .total-order ul li {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                padding: 8px 0;
            }

            .sd-page .total-order ul li h4,
            .sd-page .total-order ul li h5 {
                margin: 0;
            }

            font {
                line-height: 1.4 !important;
                max-width: 100% !important;
            }
        }

        /* Dark Mode Overrides */
        :root[data-theme="dark"] .invoice-box {
            color: #d1d5db !important;
        }

        :root[data-theme="dark"] .invoice-box h1,
        :root[data-theme="dark"] .invoice-box h4,
        :root[data-theme="dark"] .invoice-box h5 {
            color: #fff !important;
        }

        :root[data-theme="dark"] .invoice-box table tr.heading {
            background: #0f172a !important;
        }

        :root[data-theme="dark"] .invoice-box table tr.heading th,
        :root[data-theme="dark"] .invoice-box table tr.heading td {
            color: #fff !important;
        }

        :root[data-theme="dark"] .invoice-box table tr.details {
            border-bottom-color: #323248 !important;
        }
        
        :root[data-theme="dark"] .invoice-box table tr.details td {
            color: #d1d5db !important;
        }
    </style>

    <div class="content sd-page">
        <div class="page-header">
            <div class="page-title">
                <h4>{{ $pageTitle ?? 'Sale Details' }}</h4>
            </div>

            <div class="page-btn d-flex gap-2">
                {{-- @if (app('hasPermission')(2, 'add'))
                <a href="{{ route('sales.add') }}" class="btn btn-added">
                    <i class="fa fa-plus me-1"></i> Add Sale
                </a>
                @endif --}}

                @if (app('hasPermission')(2, 'edit') && !$hasReturnStarted)
                    <a href="{{ route('sales.edit', $view_id) }}" class="btn btn-primary">
                        <i class="fa fa-edit me-1"></i> Edit
                    </a>
                @endif

                @if (app('hasPermission')(2, 'view'))
                <a href="{{ route('sales.invoice', $view_id) }}" target="_blank" class="btn btn-primary">
                    <i class="fa fa-file-invoice"></i> Invoice
                </a>
                @endif

                @if (app('hasPermission')(2, 'view'))
                <button type="button" class="btn btn-primary" id="saleDetailsPrintBtn" data-order-id="{{ $view_id }}">
                    <i class="fa fa-print me-1"></i> Print
                </button>
                @endif

                <!-- Delivery buttons (hidden by default) -->
                <a id="deliveryBtn" href="javascript:void(0);" class="btn btn-info d-none">
                    <i class="fa fa-truck me-1"></i> Delivery
                </a>

                <a id="deliveryChallanBtn" href="javascript:void(0);" class="btn btn-success d-none">
                    <i class="fa fa-file-alt me-1"></i> Delivery Challan
                </a>

                @if (app('hasPermission')(2, 'view'))
                <a href="{{ route('sales.list') }}" class="btn" style="background: #1b2850; color: #fff;">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <input type="hidden" name="selse_id" id="selse_id" value="{{ $view_id }}">

                <div class="download_pdf">
                    <div class="invoice-box table-height"
                        style="max-width: 1600px;width:100%;margin:15px auto;padding: 0;font-size: 14px;line-height: 24px;color: #555;">
                        <table cellpadding="0" cellspacing="0" style="width: 100%;line-height: inherit;text-align: left;">
                            <tbody id="product-details"></tbody>
                        </table>
                    </div>

                    <div id="quotation-items-section" class="invoice-box"
                        style="display:none; max-width: 1600px; width:100%; margin:30px auto; padding:0; font-size:14px; line-height:24px; color:#555;">
                        <table cellpadding="0" cellspacing="0"
                            style="width:100%; line-height: inherit; text-align:left; border-collapse: collapse;"
                            border="1">
                            <thead>
                                <tr style="background: #F3F2F7;">
                                    <th style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px;">Labour Name</th>
                                    <th style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px;">Qty</th>
                                    <th style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px;">Price</th>
                                </tr>
                            </thead>
                            <tbody id="quotation-items-details"></tbody>
                        </table>
                    </div>

                    <!-- Return History Section -->
                    <div id="return-history-section" class="invoice-box"
                        style="display:none; max-width: 1600px;width:100%;margin:30px auto;padding: 0;font-size: 14px;line-height: 24px;color: #555;">
                        <table cellpadding="0" cellspacing="0" style="width: 100%;line-height: inherit;text-align: left;">
                            <tbody id="return-history-details"></tbody>
                        </table>
                    </div>

                    <!-- Rental Return History Section -->
                    <div id="rental-return-history-section" class="invoice-box"
                        style="display:none; max-width: 1600px;width:100%;margin:30px auto;padding: 0;font-size: 14px;line-height: 24px;color: #555;">
                        <table cellpadding="0" cellspacing="0" style="width: 100%;line-height: inherit;text-align: left;">
                            <tbody id="rental-return-history-details"></tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="total-order w-100 max-widthauto m-auto mb-4">
                                </div>
                            </div>

                            @php
                                $subtotal = 0;
                                $totalDiscountAmount = 0;
                                $totalTaxAmount = 0;

                                foreach ($orderItems as $item) {
                                    $lineSubtotal = $item->price * $item->quantity;
                                    $subtotal += $lineSubtotal;
                                    $totalDiscountAmount += $item->discount_amount ?? 0;
                                    $totalTaxAmount += $item->product_gst_total ?? 0;
                                }

                                $productBaseSubtotal = $subtotal - $totalTaxAmount; // base excl. GST

                                $afterDiscount = $subtotal - $totalDiscountAmount;
                                // Grand Total = inclusive prices - discounts + labour + shipping
                                // (GST is already inside the price, do NOT add it again)
                                $subTotalAmount = $subtotal - $totalDiscountAmount;
                                $finalTotal = $subTotalAmount;

                                $totalTaxAmount = 0;
                                $taxDetails = [];
                                foreach ($orderItems as $item) {
                                    $totalTaxAmount += $item->product_gst_total ?? 0;
                                }

                                $labourSubTotal = 0;
                                foreach (($labour_items ?? []) as $labouritem) {
                                    $labourSubTotal += (float) ($labouritem->price ?? 0) * (float) ($labouritem->qty ?? 0);
                                }
                            @endphp

                            <div class="col-lg-6">
                                <div class="total-order w-100 max-widthauto m-auto mb-4">
                                    <ul>
                                        @php
                                            if (!function_exists('formatCurrency')) {
                                                function formatCurrency($amount, $symbol, $position = 'left')
                                                {
                                                    return $position === 'right'
                                                        ? number_format($amount, 2) . $symbol
                                                        : $symbol . number_format($amount, 2);
                                                }
                                            }
                                        @endphp

                                        <li>
                                            <h4>Total (Product)</h4>
                                            <h5 id="productTotalText">{{ formatCurrency($productBaseSubtotal, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}</h5>
                                        </li>
                                          @foreach ($taxDetails as $tax)
                                            <li>
                                                <h4>{{ $tax['name'] }} Tax</h4>
                                                <h5>{{ $tax['rate'] }}%
                                                    ({{ formatCurrency($tax['amount'], $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }})
                                                </h5>
                                            </li>
                                        @endforeach

                                         <li id="totalGstSummaryRow" @if ($totalTaxAmount <= 0) style="display:none;" @endif>
                                            <h4>Total GST Amount</h4>
                                            <h5>{{ formatCurrency($totalTaxAmount, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}</h5>
                                        </li>

                                        <li id="discountAmountSummaryRow" @if ($totalDiscountAmount <= 0) style="display:none;" @endif>
                                            <h4>Discount Amount</h4>
                                            <h5>{{ formatCurrency($totalDiscountAmount, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}</h5>
                                        </li>

                                        <li id="subTotalSummaryRow">
                                            <h4>SubTotal</h4>
                                            <h5 id="subTotalSummaryText" style="font-weight: 600;">{{ formatCurrency($productBaseSubtotal + $totalTaxAmount - $totalDiscountAmount, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}</h5>
                                        </li>




                                        @if (isset($sales) && (float) ($sales->shipping ?? 0) > 0)
                                            <li id="shippingChargeRow">
                                                <h4>Shipping Charge</h4>
                                                <h5>{{ formatCurrency($sales->shipping ?? 0, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}</h5>
                                            </li>
                                        @endif

                                        @php
                                            $other_charges = [];
                                            if (isset($sales->other_charges)) {
                                                $other_charges = is_string($sales->other_charges) ? json_decode($sales->other_charges, true) : $sales->other_charges;
                                            }
                                        @endphp
                                        @if (is_array($other_charges))
                                            @foreach ($other_charges as $charge)
                                                @if(isset($charge['amount']) && (float) $charge['amount'] > 0)
                                                    <li>
                                                        <h4>{{ $charge['name'] ?? 'Other Charge' }}</h4>
                                                        <h5>{{ formatCurrency((float) $charge['amount'], $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}</h5>
                                                    </li>
                                                @endif
                                            @endforeach
                                        @endif

                                        @if (isset($sales) && (float) ($sales->tds_amount ?? 0) > 0)
                                            <li id="tdsAmountRow">
                                                <h4>TDS ({{ (float)($sales->tds_percentage ?? 0) }}%)</h4>
                                                <h5 style="color: #ea5455;">- {{ formatCurrency($sales->tds_amount ?? 0, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}</h5>
                                            </li>
                                        @endif

                                        {{-- ✅ Return Amount: dynamically updated by JS --}}
                                        <li id="returnAmountRow" style="display:none;">
                                            <h4 style="color:#ea5455;">Return Amount</h4>
                                            <h5 id="returnamount" style="color:#ea5455; font-weight:600;">₹0.00</h5>
                                        </li>

                                        {{-- ✅ Return Status: dynamically updated by JS --}}
                                        <li id="returnStatusRow" style="display:none;">
                                            <h4>Return Status</h4>
                                            <h5 id="returnstatus">—</h5>
                                        </li>

                                        <li class="laborcharge" @if ($labourSubTotal <= 0) style="display:none;" @endif>
                                            <h4>Labour Charge</h4>
                                            <h5 id="labourChargeText">{{ formatCurrency($labourSubTotal, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}</h5>
                                        </li>

                                        <li class="total">
                                            <h4>Grand Total</h4>
                                            <h5>{{ formatCurrency($finalTotal, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}</h5>
                                        </li>

                                        <li id="depositAdvanceRow" style="display:none;">
                                            <h4>Deposit/Advance</h4>
                                            <h5 id="depositAdvanceText" style="color:#dc3545;font-weight:600;">
                                                ₹0.00
                                            </h5>
                                        </li>

                                        <li id="paidAmountRow" style="border-top:1px dashed #ddd; padding-top:10px;">
                                            <h4 style="color:#2E7D32;">Paid Amount</h4>
                                            <h5 id="paidAmountText" style="color:#2E7D32;font-weight:600;">₹0.00</h5>
                                        </li>

                                        <li class="total" id="pendingAmountRow">
                                            <h4>Pending Amount</h4>
                                            <h5 id="pendingAmountText" style="color:red;font-weight:600;">₹0.00</h5>
                                        </li>

                                        <li id="emiSummaryRow" class="emi-summary-row">
                                            <h4>EMI Plan</h4>
                                            <div id="emiSummaryText" class="emi-summary-box"></div>
                                        </li>

                                        <li id="extraPaidRow" style="display: none;">
                                            <h4 style="color:#dc3545;">Extra Paid</h4>
                                            <h5 id="extraPaidText" style="color:#dc3545;font-weight:600;">₹0.00</h5>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="text-align: center; margin-top: 100px;">
                        <h2 style="color: #7367F0; font-size: 24px; margin-bottom: 10px;">Thank You for Your Purchase!</h2>
                        <p style="font-size: 16px; color: #555;">We appreciate your business and hope to serve you again soon.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.sales-invoice-print-modal')

@endsection

@push('js')
    @include('partials.invoice-qz-print-script')
    @include('partials.sales-invoice-print-modal-script')
    <script>
        const userAddress = @json($userAddress);
        const userDeliveryAddress = @json($userDeliveryAddress ?? null);
        const defaultProductImage = @json(image_path('admin/assets/img/product/noimage.png'));
        const imageBasePath = @json(rtrim(image_path_base(), '/'));
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.querySelector('.download_pdf');
            const opt = {
                margin: 10,
                filename: 'invoice_SL0101.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }

        function formatCurrency(amount, symbol = '₹', position = 'left') {
            let formattedAmount = parseFloat(amount).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            return position === 'left' ? `${symbol}${formattedAmount}` : `${formattedAmount}${symbol}`;
        }

        function renderEmiSummary(sale, symbol = '₹', position = 'left') {
            const method = (sale.payment_method || '').toString().toLowerCase();

            if (method !== 'emi') {
                $("#emiSummaryRow").hide();
                $("#emiSummaryText").empty();
                return;
            }

            const monthlyAmount = parseFloat(sale.emi_monthly_amount || 0);
            const tenureRaw = (sale.emi_tenure || '').toString();
            const months = tenureRaw.toLowerCase() === 'custom' ? 0 : parseInt(tenureRaw, 10);
            const loanAmount = parseFloat(sale.emi_loan_amount || 0);
            const downPayment = parseFloat(sale.emi_down_payment || 0);
            const interestRate = parseFloat(sale.emi_interest_rate || 0);
            const totalInstallmentAmount = months > 0 ? monthlyAmount * months : 0;
            const tenureLabel = tenureRaw.toLowerCase() === 'custom' ? 'Custom' : `${months} months`;

            let summaryHtml = '';

            if (monthlyAmount > 0 && months > 0) {
                summaryHtml += `
                    <span class="emi-formula">
                        ${formatCurrency(monthlyAmount, symbol, position)} x ${months} months =
                        ${formatCurrency(totalInstallmentAmount, symbol, position)}
                    </span>`;
            } else if (monthlyAmount > 0) {
                summaryHtml += `<span class="emi-formula">${formatCurrency(monthlyAmount, symbol, position)} monthly EMI</span>`;
            } else {
                summaryHtml += `<span class="emi-formula">EMI details selected</span>`;
            }

            const metaParts = [];
            if (downPayment > 0) metaParts.push(`Down payment: ${formatCurrency(downPayment, symbol, position)}`);
            if (loanAmount > 0) metaParts.push(`Loan amount: ${formatCurrency(loanAmount, symbol, position)}`);
            if (interestRate > 0) metaParts.push(`Interest: ${interestRate.toFixed(2)}%`);
            if (tenureRaw) metaParts.push(`Tenure: ${tenureLabel}`);

            if (metaParts.length) {
                summaryHtml += `<div class="emi-meta">${metaParts.join(' | ')}</div>`;
            }

            if (monthlyAmount > 0 && months > 0 && loanAmount > 0) {
                summaryHtml += `<div class="emi-note">Installment total shown separately from down payment.</div>`;
            }

            $("#emiSummaryText").html(summaryHtml);
            $("#emiSummaryRow").css('display', 'flex');
        }

        $(document).ready(function () {
            var authToken        = localStorage.getItem("authToken");
            let salse_id         = $("#selse_id").val();
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

            if (salse_id) {
                fetchsalseDetails(salse_id);
            }

            function fetchsalseDetails(salse_id) {
                $.ajax({
                    url: `/api/getsalseById/${salse_id}`,
                    type: "GET",
                    data: { selectedSubAdminId: selectedSubAdminId },
                    dataType: "json",
                    headers: { "Authorization": "Bearer " + authToken },

                    success: function (response) {

                        // ── Page title ───────────────────────────────────────
                        if (response.sales.quotation_status === 'quotation') {
                            $(".page-title h4").text("Sale Quotation Details");
                        } else if (response.sales.quotation_status === 'advance_receipt' && {{ optional(\App\Models\Setting::first())->advance_receipt ? 'true' : 'false' }}) {
                            $(".page-title h4").text("Advance Receipt Details");
                        } else if ((response.sales.rental_status === 'rental' || response.sales.quotation_status === 'rental') && {{ optional(\App\Models\Setting::first())->rental ? 'true' : 'false' }}) {
                            $(".page-title h4").text("Rental Details");
                        } else {
                            $(".page-title h4").text("Sale Details");
                        }

                        const currSym = response.currency_symbol;
                        const currPos = response.currency_position;

                        // ── Labour charge ────────────────────────────────────
                        let labourSubTotal = 0;
                        (response.labour_items || []).forEach(item => {
                            labourSubTotal += parseFloat(item.price || 0) * parseFloat(item.qty || 0);
                        });
                        $("#labourChargeText").text(formatCurrency(labourSubTotal, currSym, currPos));
                        $(".laborcharge").toggle(labourSubTotal > 0);

                        // ── Grand total ──────────────────────────────────────
                        // Will be recalculated below once productGstTotal is known
                        let grandTotal = parseFloat(response.sales.total_amount || 0);
                        $(".total h5").text(formatCurrency(grandTotal, currSym, currPos));

                        // ── Paid / Pending ───────────────────────────────────
                        const sale = response.sales || {};
                        const paidAmount = parseFloat(sale.paid_amount ?? 0);
                        $("#paidAmountText").text(formatCurrency(paidAmount, currSym, currPos));
                        $("#pendingAmountText").text(formatCurrency(sale.pending_amount ?? 0, currSym, currPos));
                        renderEmiSummary(sale, currSym, currPos);

                        if (parseFloat(sale.extra_paid) > 0) {
                            $("#extraPaidText").text(formatCurrency(sale.extra_paid, currSym, currPos));
                            $("#extraPaidRow").show();
                        } else {
                            $("#extraPaidRow").hide();
                        }

                        // ── Return Amount & Return Status ────────────────────
                        //
                        // Logic mirrors the Purchase Invoice blade:
                        //   - totalReturnAmount = Σ (price × qty) - discount + gst  per return item
                        //   - isFullyReturned   = every sold item has been fully returned
                        //   - if fully returned → add shipping to return amount
                        //   - Status: No return | Partially Returned | Fully Returned
                        //
                        let totalReturnAmount = 0;
                        const returns = response.returns || [];

                        returns.forEach(ret => {
                            (ret.items || []).forEach(retItem => {
                                const lineTotal      = parseFloat(retItem.price      || 0) * parseFloat(retItem.quantity || 0);
                                const discountAmt    = parseFloat(retItem.discount_amount || 0);
                                const gstAmt         = parseFloat(retItem.product_gst_total || 0);
                                totalReturnAmount   += (lineTotal - discountAmt + gstAmt);
                            });
                        });

                        // Check if all sold items are fully returned
                        const items      = response.order_items || [];
                        const shippingCharge = parseFloat(response.order?.shipping || sale.shipping || 0);
                        const productSubtotal = items.reduce((sum, item) => {
                            let rentMultiplier = 1;
                            if ((sale.rental_status === 'rental' || sale.quotation_status === 'rental') && {{ optional(\App\Models\Setting::first())->rental ? 'true' : 'false' }}) {
                                rentMultiplier = parseInt(item.rent_value) || 1;
                            }
                            return sum + (parseFloat(item.price || 0) * parseFloat(item.quantity || 0) * rentMultiplier);
                        }, 0);
                        // Recalculate GST portion using EXCLUSIVE logic (price is base price)
                        const productGstTotal = items.reduce((sum, item) => {
                            let rentMultiplier = 1;
                            if ((sale.rental_status === 'rental' || sale.quotation_status === 'rental') && {{ optional(\App\Models\Setting::first())->rental ? 'true' : 'false' }}) {
                                rentMultiplier = parseInt(item.rent_value) || 1;
                            }
                            const baseTotal = parseFloat(item.price || 0) * parseFloat(item.quantity || 0) * rentMultiplier;
                            if (Array.isArray(item.product_tax) && item.product_tax.length > 0) {
                                const totalRate = item.product_tax.reduce((s, tax) => s + parseFloat(tax.tax_rate || 0), 0);
                                return sum + Math.round(baseTotal * (totalRate / 100) * 100) / 100;
                            }
                            return sum + parseFloat(item.product_gst_total || 0);
                        }, 0);
                        
                        // productSubtotal is the sum of exclusive base prices
                        const productDisplayTotal = productSubtotal;
                        $("#productTotalText").text(formatCurrency(productDisplayTotal, currSym, currPos));
                        // ✅ Update Total GST Amount row
                        $('#totalGstSummaryRow h5').text(formatCurrency(productGstTotal, currSym, currPos));

                        // ✅ Grand Total: prices are EXCLUSIVE, so ADD productGstTotal
                        // grandTotal = base prices + gst - discounts + labour + shipping + other charges
                        const shippingAmt  = parseFloat(response.sales?.shipping || sale?.shipping || 0);
                        const discountAmt  = items.reduce((s, i) => s + parseFloat(i.discount_amount || 0), 0);
                        let otherChargesTotal = 0;
                        let otherChargesArray = sale?.other_charges;
                        if (typeof otherChargesArray === 'string') {
                            try {
                                otherChargesArray = JSON.parse(otherChargesArray);
                            } catch (e) {
                                otherChargesArray = [];
                            }
                        }
                        if (Array.isArray(otherChargesArray)) {
                            otherChargesArray.forEach(charge => {
                                otherChargesTotal += parseFloat(charge.amount || 0);
                            });
                        }
                        
                        const calcSubTotal = productSubtotal + productGstTotal - discountAmt;
                        $("#subTotalSummaryText").text(formatCurrency(calcSubTotal, currSym, currPos));

                        const tdsAmt = parseFloat(sale?.tds_amount || 0);
                        grandTotal = Math.round(calcSubTotal + labourSubTotal + shippingAmt + otherChargesTotal - tdsAmt);
                        $(".total h5").text(formatCurrency(grandTotal, currSym, currPos));

                        const depositAmountNum = parseFloat(sale?.deposit_amount ?? 0);
                        if (depositAmountNum > 0) {
                            $("#depositAdvanceText").text(formatCurrency(depositAmountNum, currSym, currPos));
                            $("#depositAdvanceRow").show();
                            $("#paidAmountRow").show();
                        } else {
                            $("#depositAdvanceRow").hide();
                            $("#paidAmountRow").show();
                        }

                        // ✅ Recalculate Pending Amount = Grand Total − Paid
                        const computedPending = Math.max(0, grandTotal - paidAmount);
                        $("#pendingAmountText").text(formatCurrency(computedPending, currSym, currPos));

                        if (paidAmount > grandTotal) {
                            const extraPaidAmount = paidAmount - grandTotal;
                            $("#extraPaidText").text(formatCurrency(extraPaidAmount, currSym, currPos));
                            $("#extraPaidRow").show();
                        } else {
                            $("#extraPaidRow").hide();
                        }

                        let allItemsFullyReturned = true;
                        if (returns.length === 0) {
                            allItemsFullyReturned = false;
                        } else {
                            items.forEach(item => {
                                const soldQty = parseFloat(item.quantity || 0);
                                let returnedQty = 0;

                                returns.forEach(ret => {
                                    (ret.items || []).forEach(retItem => {
                                        // Match by order_item_id or product_id depending on your API
                                        if (
                                            (retItem.order_item_id && retItem.order_item_id == item.id) ||
                                            (retItem.product_id    && retItem.product_id    == item.product_id)
                                        ) {
                                            returnedQty += parseFloat(retItem.quantity || 0);
                                        }
                                    });
                                });

                                if (returnedQty < soldQty) {
                                    allItemsFullyReturned = false;
                                }
                            });
                        }

                        // Add shipping to return amount only when fully returned
                        const totalReturnWithShipping = allItemsFullyReturned
                            ? totalReturnAmount + shippingCharge
                            : totalReturnAmount;

                        // Update Return Amount row
                        if (totalReturnAmount > 0) {
                            let returnAmtHtml = formatCurrency(totalReturnWithShipping, currSym, currPos);
                            // if (allItemsFullyReturned && shippingCharge > 0) {
                            //     returnAmtHtml += ` <small style="font-size:10px;">(Incl. Shipping)</small>`;
                            // }
                            $("#returnamount").html(returnAmtHtml);
                            $("#returnAmountRow").show();
                        } else {
                            $("#returnAmountRow").hide();
                        }

                        // Update Return Status row
                        if (totalReturnAmount === 0) {
                            $("#returnStatusRow").hide();
                        } else if (allItemsFullyReturned) {
                            $("#returnstatus").html(`<span style="color:#ea5455; font-weight:600;">Fully Returned</span>`);
                            $("#returnStatusRow").show();
                        } else {
                            $("#returnstatus").html(`<span style="color:#ff9f43; font-weight:600;">Partially Returned</span>`);
                            $("#returnStatusRow").show();
                        }

                        // ── Quotation / Labour table ─────────────────────────
                        const quotationSection = document.getElementById('quotation-items-section');
                        const tbody            = document.getElementById('quotation-items-details');
                        tbody.innerHTML        = '';

                        const qoutation  = response.labour_items || [];
                        const isQuotation     = response.sales.quotation_status === 'quotation';
                        const isAdvanceReceipt = response.sales.quotation_status === 'advance_receipt' && {{ optional(\App\Models\Setting::first())->advance_receipt ? 'true' : 'false' }};
                        const isRentalOrder    = (response.sales.rental_status === 'rental' || response.sales.quotation_status === 'rental') && {{ optional(\App\Models\Setting::first())->rental ? 'true' : 'false' }};
                        const orderInfoLabel   = isQuotation ? 'Quotation Info'      : (isAdvanceReceipt ? 'Advance Receipt Info' : (isRentalOrder ? 'Rental Info'   : 'Order Info'));
                        const orderNumLabel    = isQuotation ? 'Quotation Number'    : (isAdvanceReceipt ? 'Advance Receipt No'   : (isRentalOrder ? 'Rental Number' : 'Order Number'));
                        const orderDateLabel   = isQuotation ? 'Quotation Date'      : (isAdvanceReceipt ? 'Receipt Date'         : (isRentalOrder ? 'Rental Date'   : 'Order Date'));

                        // Toggle Delivery / Delivery Challan buttons (only for actual sales)
                        try {
                            const saleId = response.sales.id || response.sales.order_id || salse_id;
                            const isDeliveryOrder = String(response.sales.order_type || '').toLowerCase() === 'delivery';
                            if (!isQuotation && isDeliveryOrder) {
                                $('#deliveryBtn').removeClass('d-none').attr('href', `/sales/delivery/${saleId}`);
                                // open the PDF view for delivery challan
                                $('#deliveryChallanBtn').removeClass('d-none').attr('href', `/sales/delivery/pdf/${saleId}`);
                            } else {
                                $('#deliveryBtn').addClass('d-none');
                                $('#deliveryChallanBtn').addClass('d-none');
                            }
                        } catch (e) {
                            console.error('Error toggling delivery buttons', e);
                        }

                        if (qoutation.length > 0) {
                            qoutation.forEach(item => {
                                const tr = document.createElement('tr');

                                const tdName = document.createElement('td');
                                tdName.textContent = item.labour_item_name ?? 'N/A';
                                tdName.style.padding = '8px';
                                tr.appendChild(tdName);

                                const tdQty = document.createElement('td');
                                tdQty.textContent = item.qty;
                                tdQty.style.padding = '8px';
                                tr.appendChild(tdQty);

                                const tdPrice = document.createElement('td');
                                tdPrice.textContent = formatCurrency(item.price, currSym, currPos);
                                tdPrice.style.padding = '8px';
                                tr.appendChild(tdPrice);

                                tbody.appendChild(tr);
                            });
                            quotationSection.style.display = 'block';
                        } else {
                            quotationSection.style.display = 'none';
                        }

                        if (response.error) return;

                        const company_info = response.company_info || {};

                        let hasGst = items.some(item =>
                            parseFloat(item.product_gst_total || 0) > 0 ||
                            (Array.isArray(item.product_tax) && item.product_tax.length > 0)
                        );
                        let hasDiscount = items.some(item => parseFloat(item.discount_amount || 0) > 0);
                        $("#discountAmountSummaryRow, #priceAfterDiscountSummaryRow").toggle(hasDiscount);
                        $("#totalGstSummaryRow").toggle(hasGst);

                        const taxHeaderColumns = hasGst
                            ? `<td class="tax-col" style="padding:10px; text-align:center; white-space:normal; vertical-align:middle; font-weight:600; color:#5E5873; font-size:14px;">Product Taxes</td>`
                            : '';
                        const discountHeaderColumn = hasDiscount
                            ? `<td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px;">Discount Amount</td>`
                            : '';

                        let productRows = `
                            <tr class="top col-12">
                                <td colspan="12" style="padding: 10px;vertical-align: top;">
                                    <table style="width: 100%;line-height: inherit;text-align: left;" class="product-list">
                                        <tbody>
                                            <tr class="sd-logo-row">
                                                <td colspan="3" style="padding: 0;">
                                                    <div class="row sd-logo-wrap">
                                                        <div class="col-6">
                                                            <img src="{{ $setting->logo ? image_path('storage/' . $setting->logo) : image_path('admin/assets/img/logo-image.jpg') }}"
                                                                alt="logo" class="logo_img">
                                                        </div>
                                                        <div class="col-6 mt-4 sd-invoice-title" style="text-align: end;">
                                                            <h1>Invoice</h1>
                                                            <h4 class="mt-3">#${sale.order_number}</h4>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </td>
                                            </tr>
                                            <tr class="sd-info-row">
                                                <td class="sd-info-cell" style="padding:5px;vertical-align:top;text-align:left;padding-bottom:20px; width: 25%;">
                                                    <font style="vertical-align: inherit; margin-bottom:25px;">
                                                        <font style="vertical-align: inherit; font-size:14px; color:#7367F0; font-weight:600; line-height:35px;">Customer Info</font>
                                                    </font><br>
                                                    <font style="vertical-align: inherit;">
                                                        <font style="vertical-align: inherit; font-size: 14px; font-weight: 400;" class="customer-name">${sale.user_name || "Default-customer"}</font>
                                                    </font><br>
                                                    ${sale.user_email ? `<font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 14px; font-weight: 400;" class="customer-email">${sale.user_email}</font></font><br>` : ''}
                                                    ${userAddress ? `<font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 14px; font-weight: 400;" class="customer-address">${userAddress}</font></font><br>` : ''}
                                                    ${userDeliveryAddress ? `<font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 14px; font-weight: 400;" class="customer-delivery-address">Delivery Address: ${userDeliveryAddress}</font></font><br>` : ''}
                                                    ${sale.user_phone ? `<font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 14px; font-weight: 400;" class="customer-phone">${sale.user_phone}</font></font><br>` : ''}
                                                    ${(sale.gst_option || 'without_gst') === 'with_gst' && sale.user_gst_number ? `<font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 14px; font-weight: 400;">GST: ${sale.user_gst_number}</font></font><br>` : ''}
                                                    ${sale.user_pan_number ? `<font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 14px; font-weight: 400;">PAN: ${sale.user_pan_number}</font></font><br>` : ''}
                                                </td>
                                                <td class="sd-info-cell" style="padding:5px; vertical-align:top; text-align:left; padding-bottom:20px; width: 30%;">
                                                    <font style="vertical-align: inherit; margin-bottom:25px;">
                                                        <font style="vertical-align: inherit; font-size:14px; color:#7367F0; font-weight:600; line-height: 35px;">Company Info</font>
                                                    </font><br>
                                                    ${company_info.name ? `<font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 14px; font-weight: 400;">${company_info.name}</font></font><br>` : ''}
                                                    ${company_info.email ? `<font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 14px; font-weight: 400;">${company_info.email}</font></font><br>` : ''}
                                                    ${company_info.phone ? `<font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 14px; font-weight: 400;">${company_info.phone}</font></font><br>` : ''}
                                                    ${company_info.address ? `<font style="vertical-align: inherit; display: block; max-width: 300px; word-wrap: break-word; white-space: normal;"><font style="vertical-align: inherit; font-size: 14px; font-weight: 400;">${company_info.address}</font></font>` : ''}
                                                    ${(sale.gst_option || 'without_gst') === 'with_gst' && company_info.gst_num ? `<font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 14px; font-weight: 400;">GST: ${company_info.gst_num}</font></font><br>` : ''}
                                                </td>
                                                <td class="sd-info-cell" style="padding:5px;vertical-align:top;text-align:left;padding-bottom:20px; width: 45%;" colspan="2">
                                                    <font style="vertical-align: inherit;margin-bottom:25px;">
                                                        <font style="vertical-align: inherit;font-size:14px;color:#7367F0;font-weight:600;line-height: 35px;">${orderInfoLabel}</font>
                                                    </font><br>
                                                    <table style="width: 100%; border-collapse: collapse;">
                                                        ${isQuotation ? `
                                                            <tr>
                                                                <td style="padding: 2px 0; font-size: 14px; font-weight: 400; border: none;">Order Status</td>
                                                                <td style="padding: 2px 0; font-size: 14px; font-weight: 400; text-align: right; border: none;">Quotation</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding: 2px 0; font-size: 14px; font-weight: 400; border: none;">Quotation Number</td>
                                                                <td style="padding: 2px 0; font-size: 14px; font-weight: 400; text-align: right; border: none;">#${sale.order_number}</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding: 2px 0; font-size: 14px; font-weight: 400; border: none;">Quotation Date</td>
                                                                <td style="padding: 2px 0; font-size: 14px; font-weight: 400; text-align: right; border: none;">${sale.created_at}</td>
                                                            </tr>
                                                        ` : `
                                                            <tr>
                                                                <td style="padding: 2px 0; font-size: 14px; font-weight: 400; border: none;">Order Status</td>
                                                                <td style="padding: 2px 0; font-size: 14px; font-weight: 400; text-align: right; border: none;">${isAdvanceReceipt ? 'Advance Receipt' : (isRentalOrder ? 'Rental' : 'Sales')}</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding: 2px 0; font-size: 14px; font-weight: 400; border: none;">${orderNumLabel}</td>
                                                                <td style="padding: 2px 0; font-size: 14px; font-weight: 400; text-align: right; border: none;" class="invoice-id">#${sale.order_number}</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="padding: 2px 0; font-size: 14px; font-weight: 400; border: none;">${orderDateLabel}</td>
                                                                <td style="padding: 2px 0; font-size: 14px; font-weight: 400; text-align: right; border: none;">${sale.created_at}</td>
                                                            </tr>

                                                            ${allItemsFullyReturned ? `
                                                                <tr>
                                                                    <td style="padding: 2px 0; font-size: 14px; color: #ea5455;">Order Status</td>
                                                                    <td style="padding: 2px 0; font-weight: bold; text-align: right; color: #ea5455;">Fully Returned</td>
                                                                </tr>
                                                            ` : ''}
                                                        `}
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr class="heading sd-product-head" style="background: #F3F2F7;">
                                <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px;">Product Name</td>
                                <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px;">Unit</td>
                                <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px;">Qty</td>
                                ${isRentalOrder ? `
                                <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px;">Rent Value</td>
                                <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px;">Rent Price</td>
                                <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px;">Start To End Date</td>
                                ` : ''}
                                <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px;">Price</td>
                                ${discountHeaderColumn}
                                ${taxHeaderColumns}
                                <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; text-align: right;">Total</td>
                            </tr>
                            <tr class="sd-billed-head-row">
                                <td colspan="12">
                                    <div class="sd-billed-head">
                                        <i class="fa fa-check-circle"></i>
                                        <span>Billed Items</span>
                                    </div>
                                </td>
                            </tr>`;

                        items.forEach((item, index) => {
                            const product               = item.product || {};
                            const discountAmount        = Number(item.discount_amount ?? 0);
                            const discountPercentageVal = parseFloat(item.discount_percentage ?? 0);
                            const showDiscountPct       = discountPercentageVal > 0;
                            // GST EXCLUSIVE pricing: price is base price without GST.
                            let rentMultiplier = 1;
                            if (isRentalOrder) {
                                rentMultiplier = parseInt(item.rent_value) || 1;
                                if (rentMultiplier <= 0) rentMultiplier = 1;
                            }
                            const baseLineTotal         = parseFloat(item.price || 0) * parseFloat(item.quantity || 0) * rentMultiplier;
                            let recalcItemGst           = 0;
                            let taxesHtml               = '';
                            if (Array.isArray(item.product_tax) && item.product_tax.length > 0) {
                                const totalRate = item.product_tax.reduce((s, t) => s + parseFloat(t.tax_rate || 0), 0);
                                const totalItemGst = baseLineTotal * (totalRate / 100);
                                
                                item.product_tax.forEach(tax => {
                                    const rate      = parseFloat(tax.tax_rate || 0);
                                    const recalcAmt = totalRate > 0 ? Math.round(totalItemGst * (rate / totalRate) * 100) / 100 : 0;
                                    recalcItemGst  += recalcAmt;
                                    taxesHtml += `<div>${tax.tax_name} (${rate}%): ${formatCurrency(recalcAmt, currSym, currPos)}</div>`;
                                });
                            } else {
                                recalcItemGst = parseFloat(item.product_gst_total || 0);
                            }
                            
                            // Final total the customer pays = base + GST - discount
                            const rowFinalTotal         = baseLineTotal + recalcItemGst - discountAmount;
                            const itemQty               = parseFloat(item.quantity || 0);
                            const displayUnitPrice      = parseFloat(item.price || 0);

                            let imagePath = defaultProductImage;

                            if (product.image_url) {
                                if (Array.isArray(product.image_url) && product.image_url.length > 0) {
                                    imagePath = product.image_url[0];
                                } else if (typeof product.image_url === 'string') {
                                    imagePath = product.image_url;
                                }
                            } else {
                                try {
                                    const images = JSON.parse(product.images || '[]');
                                    imagePath = (images && images.length > 0 && images[0])
                                        ? `${imageBasePath}/storage/${images[0]}`
                                        : defaultProductImage;
                                } catch (e) {
                                    imagePath = defaultProductImage;
                                }
                            }

                            const taxBodyColumns = hasGst
                                ? `<td class="tax-col" data-label="Taxes" style="padding:10px; white-space:normal; text-align:center; word-wrap:break-word;">${taxesHtml && taxesHtml.trim() !== '' ? taxesHtml : 'N/A'}</td>`
                                : '';
                            const discountBodyColumn = hasDiscount
                                ? `<td data-label="Discount" style="padding:10px; vertical-align:top;">
                                        ${formatCurrency(discountAmount, company_info.currency_symbol, company_info.currency_position)}
                                        ${showDiscountPct ? `<small>(${discountPercentageVal.toFixed(2)}%)</small>` : ''}
                                   </td>`
                                : '';

                            let rentalColumns = '';
                            if (isRentalOrder) {
                                let rentValue = item.rent_value ? `${item.rent_value} ${item.rent_period || 'Days'}` : '-';
                                let rentPrice = item.rent_price ? `${formatCurrency(item.rent_price, company_info.currency_symbol, company_info.currency_position)} /Per ${item.rent_period ? item.rent_period.replace(/s$/, '') : 'Day'}` : '-';
                                
                                let startDate = '-';
                                let endDate = '-';
                                if (item.rent_start_at && item.rent_end_at) {
                                    let sDate = new Date(item.rent_start_at);
                                    let eDate = new Date(item.rent_end_at);
                                    let opts = { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true };
                                    startDate = sDate.toLocaleDateString('en-GB', opts);
                                    endDate = eDate.toLocaleDateString('en-GB', opts);
                                }
                                let dateDisplay = startDate !== '-' ? `${startDate} To <br>${endDate}` : '-';

                                rentalColumns = `
                                    <td data-label="Rent Value" style="padding: 10px;vertical-align: top;">${rentValue}</td>
                                    <td data-label="Rent Price" style="padding: 10px;vertical-align: top;">${rentPrice}</td>
                                    <td data-label="Start To End Date" style="padding: 10px;vertical-align: top; white-space: nowrap;">${dateDisplay}</td>
                                `;
                            }

                            const unitName = item.product?.unit?.unit_name ?? 'Pcs';
                            const unitShort = /piece/i.test(unitName) ? 'Pcs' : unitName;
                            const qtyLabel = Number.isInteger(itemQty) ? String(itemQty) : parseFloat(itemQty.toFixed(2)).toString();
                            const variantText = item.variant
                                ? `${item.variant.size ? 'Size: ' + item.variant.size : ''}${item.variant.color ? (item.variant.size ? ' | ' : '') + 'Color: ' + item.variant.color : ''}`
                                : '';
                            const extraMeta = [variantText, item.imei_no ? `IMEI: ${item.imei_no}` : ''].filter(Boolean).join(' · ');
                            const formulaQty = isRentalOrder && rentMultiplier > 1
                                ? `${qtyLabel} ${unitShort} × ${formatCurrency(displayUnitPrice, company_info.currency_symbol, company_info.currency_position)} × ${rentMultiplier}`
                                : `${qtyLabel} ${unitShort} × ${formatCurrency(displayUnitPrice, company_info.currency_symbol, company_info.currency_position)}`;
                            const discountLine = hasDiscount
                                ? `<div class="sd-bill-disc">Discount (%): ${discountPercentageVal.toFixed(0)} ${discountAmount > 0 ? ' · ' + formatCurrency(discountAmount, company_info.currency_symbol, company_info.currency_position) : ''}</div>`
                                : '';
                            const taxLine = hasGst && recalcItemGst > 0
                                ? `<div class="sd-bill-tax"><span>Tax</span><span>${formatCurrency(recalcItemGst, company_info.currency_symbol, company_info.currency_position)}</span></div>`
                                : '';

                            productRows += `
                                <tr class="product-row">
                                    <td class="sd-mobile-item">
                                        <div class="sd-bill-card">
                                            <div class="sd-bill-top">
                                                <span class="sd-bill-no">#${index + 1}</span>
                                                <div class="sd-bill-name">
                                                    <a href="/product-view/${product.id}">${product.name || 'N/A'}</a>
                                                    ${extraMeta ? `<small>${extraMeta}</small>` : ''}
                                                </div>
                                                <div class="sd-bill-amt">${formatCurrency(rowFinalTotal, company_info.currency_symbol, company_info.currency_position)}</div>
                                            </div>
                                            <div class="sd-bill-sub">
                                                <span>Item Subtotal</span>
                                                <span>${formulaQty} = ${formatCurrency(baseLineTotal, company_info.currency_symbol, company_info.currency_position)}</span>
                                            </div>
                                            ${discountLine}
                                            ${taxLine}
                                        </div>
                                    </td>
                                    <td class="text-capitalize" style="padding: 10px; align:left; vertical-align: top;">
                                        <a href="/product-view/${product.id}" class="d-flex align-items-center text-decoration-none text-dark">
                                            <img src="${imagePath}" alt="img" class="me-2" style="width:40px; height:40px; object-fit:cover; border-radius:6px; flex-shrink:0;">
                                            <div>
                                                ${product.name || 'N/A'}
                                                ${item.variant ? `<br><small class="text-muted" style="font-size: 12px;">${item.variant.size ? 'Size: ' + item.variant.size : ''} ${item.variant.color ? (item.variant.size ? ' | ' : '') + 'Color: ' + item.variant.color : ''}</small>` : ''}
                                                ${item.imei_no ? `<br><small class="text-muted" style="font-size: 12px;">IMEI: ${item.imei_no}</small>` : ''}
                                            </div>
                                        </a>
                                    </td>
                                    <td class="text-capitalize" data-label="Unit" style="padding: 10px;vertical-align: top;">${item.product?.unit?.unit_name ?? 'N/A'}</td>
                                    <td data-label="Qty" style="padding: 10px;vertical-align: top;">${item.quantity}</td>
                                    ${rentalColumns}
                                    <td data-label="Price" style="padding: 10px;vertical-align: top;">${formatCurrency(displayUnitPrice, company_info.currency_symbol, company_info.currency_position)}</td>
                                    ${discountBodyColumn}
                                    ${taxBodyColumns}
                                    <td data-label="Total" style="padding: 10px;vertical-align: top; text-align: right;">${formatCurrency(rowFinalTotal, company_info.currency_symbol, company_info.currency_position)}</td>
                                </tr>`;
                        });

                        // ── Return History Table ──────────────────────────────
                        if (returns.length > 0) {
                            let hasReturnTax = false;
                            returns.forEach(ret => {
                                (ret.items || []).forEach(retItem => {
                                    if (parseFloat(retItem.product_gst_total || 0) > 0) hasReturnTax = true;
                                });
                            });

                            const colCount = hasReturnTax ? 8 : 7;

                            let returnRows = `
                                <tr class="heading" style="background: #F3F2F7;">
                                    <td colspan="${colCount}" style="padding: 10px; font-weight: 600; color: #7367F0; font-size: 16px; text-align: center;">Return History</td>
                                </tr>
                                <tr class="heading" style="background: #F8F9FA;">
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Return ID</td>
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Return Date</td>
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Product Name</td>
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Return Qty</td>
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Price</td>
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Discount Amt</td>
                                    ${hasReturnTax ? '<td style="padding: 10px; font-weight: 600; font-size: 14px;">Tax</td>' : ''}
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Total</td>
                                </tr>`;

                            returns.forEach(ret => {
                                const retDate         = new Date(ret.created_at);
                                const formattedRetDate = retDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
                                    + ' ' + retDate.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: true });

                                (ret.items || []).forEach(retItem => {
                                    const lineTotal     = retItem.price * retItem.quantity;
                                    const discountAmt   = parseFloat(retItem.discount_amount   || 0);
                                    const gstAmount     = parseFloat(retItem.product_gst_total || 0);
                                    const finalTotal    = lineTotal - discountAmt + gstAmount;

                                    returnRows += `
                                        <tr class="details" style="border-bottom: 1px solid #E9ECEF;">
                                            <td data-label="Return ID" style="padding: 10px; vertical-align: top;">#${ret.return_number}</td>
                                            <td data-label="Return Date" style="padding: 10px; vertical-align: top;">${formattedRetDate}</td>
                                            <td data-label="Product" style="padding: 10px; vertical-align: top;">
                                                ${retItem.product ? retItem.product.name : 'N/A'}
                                                ${retItem.imei_no ? `<br><small class="text-muted" style="font-size: 12px;">IMEI: ${retItem.imei_no}</small>` : ''}
                                            </td>
                                            <td data-label="Return Qty" style="padding: 10px; vertical-align: top;">${retItem.quantity}</td>
                                            <td data-label="Price" style="padding: 10px; vertical-align: top;">${formatCurrency(retItem.price, currSym, currPos)}</td>
                                            <td data-label="Discount" style="padding: 10px; vertical-align: top;">
                                                ${formatCurrency(discountAmt, currSym, currPos)}
                                                ${retItem.discount > 0 ? `<small>(${parseFloat(retItem.discount).toFixed(2)}%)</small>` : ''}
                                            </td>
                                            ${hasReturnTax ? `<td data-label="Tax" style="padding: 10px; vertical-align: top;">${formatCurrency(gstAmount, currSym, currPos)}</td>` : ''}
                                            <td data-label="Total" style="padding: 10px; vertical-align: top;">${formatCurrency(finalTotal, currSym, currPos)}</td>
                                        </tr>`;
                                });
                            });

                            $("#return-history-details").html(returnRows);
                            $("#return-history-section").show();
                        } else {
                            $("#return-history-section").hide();
                        }

                        // ── Rental Return History Table ──────────────────────────────
                        const rentalReturns = response.rental_returns || [];
                        if (rentalReturns.length > 0) {
                            let returnRows = `
                                <tr class="heading" style="background: #F3F2F7;">
                                    <td colspan="5" style="padding: 10px; font-weight: 600; color: #7367F0; font-size: 16px; text-align: center;">Rental Return History</td>
                                </tr>
                                <tr class="heading" style="background: #F8F9FA;">
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Return ID</td>
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Return Date</td>
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Product Name</td>
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Return Qty</td>
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Settlement Amount</td>
                                </tr>`;

                            rentalReturns.forEach(ret => {
                                const retDate         = new Date(ret.created_at);
                                const formattedRetDate = retDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
                                    + ' ' + retDate.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: true });

                                (ret.items || []).forEach(retItem => {
                                    returnRows += `
                                        <tr class="details" style="border-bottom: 1px solid #E9ECEF;">
                                            <td data-label="Return ID" style="padding: 10px; vertical-align: top;">#${ret.id}</td>
                                            <td data-label="Return Date" style="padding: 10px; vertical-align: top;">${formattedRetDate}</td>
                                            <td data-label="Product" style="padding: 10px; vertical-align: top;">
                                                ${retItem.product ? retItem.product.name : 'N/A'}
                                            </td>
                                            <td data-label="Return Qty" style="padding: 10px; vertical-align: top;">${retItem.qty_returned}</td>
                                            <td data-label="Settlement Amount" style="padding: 10px; vertical-align: top;">${formatCurrency(ret.final_settlement_amount || 0, currSym, currPos)}</td>
                                        </tr>`;
                                });
                            });

                            $("#rental-return-history-details").html(returnRows);
                            $("#rental-return-history-section").show();
                        } else {
                            $("#rental-return-history-section").hide();
                        }

                        $("#product-details").html(productRows);

                        // Update customer display fields
                        $(".customer-name").text(sale.user_name || "Default-customer");
                        $(".customer-phone").text(sale.user_phone || "");
                        $(".invoice-id").text(sale.order_number);
                        const paymentStatus = (sale.payment_status || 'pending').toString();
                        $(".payment-status").text(paymentStatus.charAt(0).toUpperCase() + paymentStatus.slice(1));
                        $(".order-status").text("Completed");
                    },

                    error: function (xhr, status, error) {
                        // console.error("Error fetching sales details:", error);
                    }
                });
            }
        });
    </script>
@endpush
