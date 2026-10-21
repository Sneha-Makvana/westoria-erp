@extends('layout.app')

@section('title', $pageTitle ?? 'Sale Invoice')

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

        /* Ensure Product Name header column has proper width */
        .invoice-box table tr.heading td:first-child {
            min-width: 200px;
            max-width: 300px;
        }

        /* Adjust columns for mobile, keep same layout */
        @media (max-width: 767px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .page-btn {
                display: grid !important;
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 5px !important;
                width: 100% !important;
                margin-top: 10px !important;
            }

            .page-btn a,
            .page-btn button {
                margin: 0 !important;
                padding: 8px 2px !important;
                font-size: 11px !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                text-align: center;
                width: 100% !important;
                white-space: normal !important;
                line-height: 1.1;
            }

            .invoice-box {
                overflow-x: auto;
            }

            .card-sales-split {
                flex-direction: row;
            }

            .row {
                display: flex;
                flex-wrap: wrap;
            }

            .col-lg-6.table_view {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .col-lg-6.total-order {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .invoice-box,
            .invoice-box table,
            .invoice-box td,
            .invoice-box h4,
            .invoice-box h5 {
                font-size: 12px;
                line-height: 1.2;
            }

            /* Ensure Product Name column has enough space on mobile */
            .invoice-box table tr.heading td:first-child,
            .invoice-box table tr.details td:first-child {
                min-width: 200px !important;
            }

            .logo_img {
                max-width: 130px;
                height: auto;
            }

            font {
                line-height: 22px !important;
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
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>{{ $pageTitle ?? 'Sale Invoice' }}</h4>
            </div>

            <div class="page-btn d-flex gap-2">
                <!-- ✅ Add Sale -->
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
                <a href="{{ route('sales.details', $view_id) }}" target="_blank" class="btn btn-primary">
                    <i class="fa fa-eye"></i> Details
                </a>
                @endif

                <!-- ✅ Print Invoice -->
                @if (app('hasPermission')(2, 'view'))
                <a href="{{ route('sales.invoice.pdf', $view_id) }}" target="_blank" class="btn btn-primary">
                    <i class="fa fa-print me-1"></i> Print
                </a>
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
                            <tbody id="product-details">

                            </tbody>
                        </table>
                    </div>
                    <div id="quotation-items-section" class="invoice-box"
                        style="display:none; max-width: 1600px; width:100%; margin:30px auto; padding:0; font-size:14px; line-height:24px; color:#555;">
                        <table cellpadding="0" cellspacing="0"
                            style="width:100%; line-height: inherit; text-align:left; border-collapse: collapse;"
                            border="1">
                            <thead>
                                <tr style="background: #F3F2F7;">
                                    <th
                                        style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; ">
                                        Labour Name</th>
                                    <th
                                        style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; ">
                                        Qty</th>
                                    <th
                                        style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; ">
                                        Price</th>
                                </tr>
                            </thead>
                            <tbody id="quotation-items-details"></tbody>
                        </table>
                    </div>

                    <!-- ✅ Return History Section -->
                    <div id="return-history-section" class="invoice-box"
                        style="display:none; max-width: 1600px;width:100%;margin:30px auto;padding: 0;font-size: 14px;line-height: 24px;color: #555;">
                        <table cellpadding="0" cellspacing="0" style="width: 100%;line-height: inherit;text-align: left;">
                            <tbody id="return-history-details">
                            </tbody>
                        </table>
                    </div>

                    <!-- ✅ Rental Return History Section -->
                    <div id="rental-return-history-section" class="invoice-box"
                        style="display:none; max-width: 1600px;width:100%;margin:30px auto;padding: 0;font-size: 14px;line-height: 24px;color: #555;">
                        <table cellpadding="0" cellspacing="0" style="width: 100%;line-height: inherit;text-align: left;">
                            <tbody id="rental-return-history-details">
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="total-order w-100 max-widthauto m-auto mb-4">
                                </div>
                            </div>

                            @php
                                $subtotal = $orderItems->sum(function ($item) {
                                    return $item->price * $item->quantity;
                                });
                                // $discountPercentage = $sales->discount; // e.g., 10 for 10%
                                // $discountAmount = ($subtotal * $discountPercentage) / 100;

                                $subtotal = 0;
                                $totalDiscountAmount = 0;
                                $totalTaxAmount = 0;
                                $finalTotal = 0;
                                foreach ($orderItems as $item) {
                                    $lineSubtotal = $item->price * $item->quantity;
                                    $subtotal += $lineSubtotal;

                                    // ✅ Sum all product discount
                                    $totalDiscountAmount += $item->discount_amount ?? 0;

                                    // ✅ Sum GST
                                    $totalTaxAmount += $item->product_gst_total ?? 0;

                                    // ✅ Sum final total (excluding GST if that is how stored)
                                    // $finalTotal += $item->total_amount ?? 0;
                                }

                                $itemDiscountTotal = $totalDiscountAmount;
                                $globalDiscountPercent = (float) ($sales->discount ?? 0);
                                $afterItemDiscountBase = max(0, $subtotal - $itemDiscountTotal);
                                $globalDiscountAmount = ($afterItemDiscountBase * $globalDiscountPercent) / 100;
                                $overallDiscount = $itemDiscountTotal + $globalDiscountAmount;
                                $productBaseSubtotal = $subtotal; // price IS base price; no subtraction needed

                                $afterDiscount = $subtotal - $overallDiscount;
                                $subTotalAmount = $subtotal + $totalTaxAmount - $overallDiscount;
                                $grandTotal = $subTotalAmount;

                                $finalTotal = $subTotalAmount;

                                $totalTaxAmount = 0;
                                $taxDetails = [];

                                foreach ($orderItems as $item) {
                                    // Add the product GST total to the totalTaxAmount
                                    $totalTaxAmount += $item->product_gst_total ?? 0;

                                    // Optional: for tax breakdown per item
                                    if ($item->product_gst_details) {
                                        $details = $item->product_gst_details;
                                        if (is_string($details)) {
                                            $details = json_decode($details, true);
                                        }
                                    }
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
                                            <h5 id="productTotalText">{{ formatCurrency($productBaseSubtotal, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}
                                            </h5>
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
                                            <h5>{{ formatCurrency($totalTaxAmount, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}
                                            </h5>
                                        </li>

                                        <li id="itemDiscountSummaryRow" @if ($itemDiscountTotal <= 0) style="display:none;" @endif>
                                            <h4>Item Discount</h4>
                                            <h5>
                                                {{ formatCurrency($itemDiscountTotal, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}
                                            </h5>
                                        </li>
                                        <li id="additionalDiscountSummaryRow" @if ($globalDiscountAmount <= 0) style="display:none;" @endif>
                                            <h4>Additional Discount ({{ rtrim(rtrim(number_format($globalDiscountPercent, 2), '0'), '.') }}%)</h4>
                                            <h5>
                                                {{ formatCurrency($globalDiscountAmount, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}
                                            </h5>
                                        </li>
                                        <li id="totalDiscountSummaryRow" @if ($overallDiscount <= 0) style="display:none;" @endif>
                                            <h4>Total Discount</h4>
                                            <h5>
                                                {{ formatCurrency($overallDiscount, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}
                                            </h5>
                                        </li>

                                        <li id="subTotalSummaryRow">
                                            <h4>SubTotal</h4>
                                            <h5 id="subTotalSummaryText" style="font-weight: 600;">
                                                {{ formatCurrency($productBaseSubtotal + $totalTaxAmount - $overallDiscount, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}
                                            </h5>
                                        </li>
                                        @if (isset($sales) && (float) ($sales->shipping ?? 0) > 0)
                                            <li id="shippingChargeRow">
                                                <h4>Shipping Charge</h4>
                                                <h5>
                                                    {{ formatCurrency($sales->shipping ?? 0, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}
                                                </h5>
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

                                        @if (isset($sales) && (float) ($sales->exchange_amount ?? 0) > 0)
                                            <li id="exchangeChargesRow">
                                                <h4>Exchange Deduction
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
                                                    @if($exMatName)
                                                        <span style="font-size: 11px; font-weight: normal; color: #7367f0;">
                                                            ({{ $exMatName }}{{ $exTypeName ? ' - ' . $exTypeName : '' }}{{ $exWeight ? ' | ' . $exWeight . ' kg' : '' }})
                                                        </span>
                                                    @endif
                                                </h4>
                                                <h5 style="color: #ea5455;">- {{ formatCurrency($sales->exchange_amount ?? 0, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}</h5>
                                            </li>
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
                                            <h5 id="labourChargeText">
                                                {{ formatCurrency($labourSubTotal, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}
                                            </h5>
                                        </li>

                                        <li class="total">
                                            <h4>Grand Total</h4>
                                            <h5>{{ formatCurrency($finalTotal, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}
                                            </h5>
                                        </li>

                                        <li id="depositAdvanceRow" style="display:none;">
                                            <h4>Deposit/Advance</h4>
                                            <h5 id="depositAdvanceText" style="color:#dc3545;font-weight:600;">
                                                ₹0.00
                                            </h5>
                                        </li>

                                        <li id="paidAmountRow" style="border-top:1px dashed #ddd; padding-top:10px;">
                                            <h4 style="color:#2E7D32;">Paid Amount</h4>
                                            <h5 id="paidAmountText" style="color:#2E7D32;font-weight:600;">
                                                ₹0.00
                                            </h5>
                                        </li>

                                        <li id="pendingAmountRow">
                                            <h4 style="color:#C62828;">Pending Amount</h4>
                                            <h5 id="pendingAmountText" style="color:#C62828;font-weight:600;">
                                                ₹0.00
                                            </h5>
                                        </li>

                                        <li id="emiSummaryRow" class="emi-summary-row">
                                            <h4>EMI Plan</h4>
                                            <div id="emiSummaryText" class="emi-summary-box"></div>
                                        </li>

                                        <li id="extraPaidRow" style="display: none;">
                                            <h4 style="color:#dc3545;">Extra Paid</h4>
                                            <h5 id="extraPaidText" style="color:#dc3545;font-weight:600;">
                                                ₹0.00
                                            </h5>
                                        </li>

                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div style="text-align: center; margin-top: 100px;">
                        <h2 style="color: #7367F0; font-size: 24px; margin-bottom: 10px;">Thank You for Your Purchase!
                        </h2>
                        <p style="font-size: 16px; color: #555;">We appreciate your business and hope to serve you again
                            soon.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>


@endsection
@push('js')
    <script>
        const userAddress = @json($userAddress);
        const userDeliveryAddress = @json($userDeliveryAddress ?? null);
        const defaultProductImage = @json(image_path('admin/assets/img/product/noimage.png'));
        const imageBasePath = @json(rtrim(image_path_base(), '/'));
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            // Get the element you want to convert to PDF
            const element = document.querySelector('.download_pdf');

            // Options for the PDF
            const opt = {
                margin: 10,
                filename: 'invoice_SL0101.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };

            // Generate the PDF
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

        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            let salse_id = $("#selse_id").val();
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

            if (salse_id) {
                fetchsalseDetails(salse_id);
            }

            function fetchsalseDetails(salse_id) {
                $.ajax({
                    url: `/api/getsalseById/${salse_id}`,
                    type: "GET",
                    data: {
                        selectedSubAdminId: selectedSubAdminId // ✅ fixed object syntax
                    },
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        const sale = response.sales || {};
                        const quotationStatus = sale.quotation_status || 'sales';
                        const isAdvanceReceipt = quotationStatus === 'advance_receipt' && {{ optional(\App\Models\Setting::first())->advance_receipt ? 'true' : 'false' }};
                        const isQuotationSale  = quotationStatus === 'quotation';

                        // ── Page title ──────────────────────────────────────
                        if (isQuotationSale) {
                            $(".page-title h4").text("Sale Quotation Invoice");
                        } else if (isAdvanceReceipt) {
                            $(".page-title h4").text("Advance Receipt Invoice");
                        } else if ((sale.rental_status === 'rental' || sale.quotation_status === 'rental') && {{ optional(\App\Models\Setting::first())->rental ? 'true' : 'false' }}) {
                            $(".page-title h4").text("Rental Invoice");
                        } else {
                            $(".page-title h4").text("Sale Invoice");
                        }

                        let labourSubTotal = 0;

                        const labourItems = response.labour_items || [];

                        labourItems.forEach(item => {
                            const price = parseFloat(item.price || 0);
                            const qty = parseFloat(item.qty || 0);

                            labourSubTotal += price * qty;
                        });
                        $("#labourChargeText").text(
                            formatCurrency(
                                labourSubTotal,
                                response.currency_symbol,
                                response.currency_position
                            )
                        );
                        $(".laborcharge").toggle(labourSubTotal > 0);

                        // Will be recalculated below once productGstTotal is known
                        let grandTotal =
                            parseFloat(response.sales.total_amount || 0);
                        $(".total h5").text(
                            formatCurrency(
                                grandTotal,
                                response.currency_symbol,
                                response.currency_position
                            )
                        );
                        const paidAmountNum = parseFloat(sale?.paid_amount ?? 0);

                        const dateStr = response.order_items && response.order_items.length > 0 ? response.order_items[0].date : null;
                        const dateObj = dateStr ? new Date(dateStr) : new Date();

                        // console.log('ffhgfgh',response.labour_items);

                        const quotationSection = document.getElementById('quotation-items-section');
                        const tbody = document.getElementById('quotation-items-details');

                        // clear old data
                        tbody.innerHTML = '';

                        const qoutation = response.labour_items || [];

                        // ✅ SHOW ONLY FOR QUOTATION
                        if (qoutation.length > 0) {

                            qoutation.forEach(item => {
                                const tr = document.createElement('tr');

                                // Name
                                const tdName = document.createElement('td');
                                tdName.textContent = item.labour_item_name ?? 'N/A';
                                tdName.style.padding = '8px';
                                tr.appendChild(tdName);

                                // Quantity
                                const tdQty = document.createElement('td');
                                tdQty.textContent = item.qty;
                                tdQty.style.padding = '8px';
                                tr.appendChild(tdQty);

                                // Price
                                const tdPrice = document.createElement('td');
                                tdPrice.textContent = formatCurrency(
                                    item.price,
                                    response.currency_symbol,
                                    response.currency_position
                                );
                                tdPrice.style.padding = '8px';
                                tr.appendChild(tdPrice);

                                tbody.appendChild(tr);
                            });

                            // ✅ show section (thead + tbody)
                            quotationSection.style.display = 'block';

                        } else {
                            // ✅ hide COMPLETE table including thead
                            quotationSection.style.display = 'none';
                        }
                        // Options for the formatter
                        const options = {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        };

                        // Using Intl.DateTimeFormat
                        // const formattedDate = new Intl.DateTimeFormat('en-GB', options).format(dateObj);



                        if (response.error) {
                            // console.error(response.error);
                            return;
                        }
                        // console.log(response.company_info);

                        const company_info = response.company_info || {};

                        const currSym = response.currency_symbol;
                        const currPos = response.currency_position;

                        // Update customer and invoice info
                        $(".customer-name").text(sale.user_name || "Walk-in Customer");
                        $(".customer-phone").text(sale.user_phone || "");
                        $(".invoice-id").text(sale.order_number);
                        const paymentStatus = (sale.payment_status || 'pending').toString();
                        $(".payment-status").text(paymentStatus.charAt(0).toUpperCase() + paymentStatus.slice(1));
                        $(".order-status").text("Completed"); // or dynamically from status if available

                        const paidAmount = formatCurrency(
                            paidAmountNum,
                            currSym,
                            currPos
                        );

                        $("#paidAmountText").text(paidAmount);
                        $("#pendingAmountText").text(formatCurrency(sale.pending_amount ?? 0, currSym, currPos));
                        renderEmiSummary(sale, currSym, currPos);

                        if (parseFloat(sale.extra_paid) > 0) {
                            const extraPaid = formatCurrency(
                                sale.extra_paid,
                                currSym,
                                currPos
                            );
                            $("#extraPaidText").text(extraPaid);
                            $("#extraPaidRow").show();
                        } else {
                            $("#extraPaidRow").hide();
                        }

                        // ── Return Amount & Return Status ────────────────────
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
                        const exchangeAmt = parseFloat(response.sales?.exchange_amount || sale?.exchange_amount || 0);
                        grandTotal = Math.round(calcSubTotal + labourSubTotal + shippingAmt + otherChargesTotal - exchangeAmt - tdsAmt);
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
                        const computedPending = Math.max(0, grandTotal - paidAmountNum);
                        $("#pendingAmountText").text(formatCurrency(computedPending, currSym, currPos));

                        if (paidAmountNum > grandTotal) {
                            const extraPaidAmount = paidAmountNum - grandTotal;
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

                        const isFullyReturned = allItemsFullyReturned;

                        // Update Return Amount row
                        if (totalReturnAmount > 0) {
                            let returnAmtHtml = formatCurrency(totalReturnWithShipping, currSym, currPos);
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
                        const isQuotation     = isQuotationSale; // already declared at top of callback
                        // isAdvanceReceipt already declared at top of callback
                        const isRentalOrder   = (sale.rental_status === 'rental' || sale.quotation_status === 'rental') && {{ optional(\App\Models\Setting::first())->rental ? 'true' : 'false' }};
                        const orderStatusLabel = isQuotation ? 'Quotation' : (isAdvanceReceipt ? 'Advance Receipt' : (isRentalOrder ? 'Rental' : 'Sales'));
                        const orderInfoLabel  = isQuotation ? 'Quotation Info'      : (isAdvanceReceipt ? 'Advance Receipt Info' : (isRentalOrder ? 'Rental Info'   : 'Order Info'));
                        const orderNumLabel   = isQuotation ? 'Quotation Number'    : (isAdvanceReceipt ? 'Advance Receipt No'   : (isRentalOrder ? 'Rental Number' : 'Order Number'));
                        const orderDateLabel  = isQuotation ? 'Quotation Date'      : (isAdvanceReceipt ? 'Receipt Date'         : (isRentalOrder ? 'Rental Date'   : 'Order Date'));


                        //  const quotationItems = response.quotation_items || [];
                        // console.log('dsvfdg',quotationItems);

                        let hasGst = items.some(item => {
                            return parseFloat(item.product_gst_total || 0) > 0 ||
                                (Array.isArray(item.product_tax) && item.product_tax.length > 0);
                        });
                        let hasDiscount = items.some(item => parseFloat(item.discount_amount || 0) > 0);
                        const billDiscountPct = parseFloat(sale.discount || 0);
                        const hasAdditionalDiscount = billDiscountPct > 0;
                        $("#itemDiscountSummaryRow").toggle(hasDiscount);
                        $("#additionalDiscountSummaryRow").toggle(hasAdditionalDiscount);
                        $("#totalDiscountSummaryRow").toggle(hasDiscount || hasAdditionalDiscount);
                        $("#priceAfterDiscountSummaryRow").toggle(hasDiscount || hasAdditionalDiscount);
                        $("#totalGstSummaryRow").toggle(hasGst);



                        const taxHeaderColumns = hasGst ?
                            `<td class="tax-col" style="padding:10px; text-align:center; white-space:normal; vertical-align:middle; font-weight:600; color:#5E5873; font-size:14px;">
                                Product Taxes
                            </td>` : '';
                        const discountHeaderColumn = hasDiscount ?
                            `<td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; ">
                                                        Discount Amount
                                                    </td>` : '';
                        let productRows = `
                                                <tr class="top col-12">
                                                    <td colspan="12" style="padding: 10px;vertical-align: top;">
                                                        <table style="width: 100%;line-height: inherit;cellpadding:0; cellspacing:0 text-align: left;" class="product-list">
                                                        <div class="row">
                                                                <div class="col-6">
                                                                    <img
                                                                    src="{{ $setting->logo ? image_path('storage/' . $setting->logo) : image_path('admin/assets/img/logo-image.jpg') }}"
                                                                    alt="logo"
                                                                    class="logo_img">
                                                                </div>
                                                                <div class="col-6 mt-4" style=" text-align: end; ">
                                                                    <h1>Invoice</h1>
                                                                    <h4 class="mt-3">#${sale.order_number}</h4>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                                <tbody>
                                                                <tr>
                                                                   <td style="padding:5px;vertical-align:top;text-align:left;padding-bottom:20px; width: 25%;">
                                                                    <font style="vertical-align: inherit; margin-bottom:25px;">
                                                                        <font style="vertical-align: inherit; font-size:14px; color:#7367F0; font-weight:600; line-height:35px;">
                                                                            Customer Info
                                                                        </font>
                                                                    </font><br>

                                                                    <!-- Name (with fallback) -->
                                                                    <font style="vertical-align: inherit;">
                                                                        <font style="vertical-align: inherit; font-size: 14px; font-weight: 400;" class="customer-name">
                                                                            ${sale.user_name || "Default-customer"}
                                                                        </font>
                                                                    </font><br>

                                                                    <!-- Email (show only if exists) -->
                                                                    ${sale.user_email ? `
                                                                                                                                                                        <font style="vertical-align: inherit;">
                                                                                                                                                                            <font style="vertical-align: inherit; font-size: 14px; font-weight: 400;" class="customer-email">
                                                                                                                                                                                ${sale.user_email}
                                                                                                                                                                            </font>
                                                                                                                                                                        </font><br>` : ''}

                                                                    <!-- Address (show only if exists) -->
                                                                    ${userAddress ? `
                                                                                                                                                                        <font style="vertical-align: inherit;">
                                                                                                                                                                            <font style="vertical-align: inherit; font-size: 14px; font-weight: 400;" class="customer-address">
                                                                                                                                                                                ${userAddress}
                                                                                                                                                                            </font>
                                                                                                                                                                        </font><br>` : ''
                                                                    }
                                                                    ${userDeliveryAddress ? `
                                                                                                                                                                        <font style="vertical-align: inherit;">
                                                                                                                                                                            <font style="vertical-align: inherit; font-size: 14px; font-weight: 400;" class="customer-delivery-address">
                                                                                                                                                                                Delivery Address: ${userDeliveryAddress}
                                                                                                                                                                            </font>
                                                                                                                                                                        </font><br>` : ''
                                                                    }

                                                                    <!-- Phone (show only if exists) -->
                                                                    ${sale.user_phone ? `
                                                                                                                                                                        <font style="vertical-align: inherit;">
                                                                                                                                                                            <font style="vertical-align: inherit; font-size: 14px; font-weight: 400;" class="customer-phone">
                                                                                                                                                                                ${sale.user_phone}
                                                                                                                                                                            </font>
                                                                                                                                                                        </font><br>` : ''}
                                                                    ${(sale.gst_option || 'without_gst') === 'with_gst' && sale.user_gst_number ? `<font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 14px; font-weight: 400;">GST: ${sale.user_gst_number}</font></font><br>` : ''}
                                                                    ${sale.user_pan_number ? `<font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 14px; font-weight: 400;">PAN: ${sale.user_pan_number}</font></font><br>` : ''}

                                                                </td>

                                                                   <td style="padding:5px; vertical-align:top; text-align:left; padding-bottom:20px; width: 30%;">
                                                                    <font style="vertical-align: inherit; margin-bottom:25px;">
                                                                        <font style="vertical-align: inherit; font-size:14px; color:#7367F0; font-weight:600; line-height: 35px;">
                                                                            Company Info
                                                                        </font>
                                                                    </font><br>

                                                                    ${company_info.name ? `
                                                                                                                                                                        <font style="vertical-align: inherit;">
                                                                                                                                                                            <font style="vertical-align: inherit; font-size: 14px; font-weight: 400;">
                                                                                                                                                                                ${company_info.name}
                                                                                                                                                                            </font>
                                                                                                                                                                        </font><br>` : ''}

                                                                    <!-- Email (only if exists) -->
                                                                    ${company_info.email ? `
                                                                                                                                                                        <font style="vertical-align: inherit;">
                                                                                                                                                                            <font style="vertical-align: inherit; font-size: 14px; font-weight: 400;">
                                                                                                                                                                                ${company_info.email}
                                                                                                                                                                            </font>
                                                                                                                                                                        </font><br>` : ''}

                                                                    <!-- Phone (only if exists) -->
                                                                    ${company_info.phone ? `
                                                                                                                                                                        <font style="vertical-align: inherit;">
                                                                                                                                                                            <font style="vertical-align: inherit; font-size: 14px; font-weight: 400;">
                                                                                                                                                                                ${company_info.phone}
                                                                                                                                                                            </font>
                                                                                                                                                                        </font><br>` : ''}

                                                                    <!-- Address (only if exists) -->
                                                                    ${company_info.address ? `
                                                                                                                                                                    <font style="vertical-align: inherit; display: block; max-width: 300px; word-wrap: break-word; white-space: normal;">
                                                                                                                                                                        <font style="vertical-align: inherit; font-size: 14px; font-weight: 400;">
                                                                                                                                                                            ${company_info.address}
                                                                                                                                                                        </font>
                                                                                                                                                                    </font>` : ''}

                                                                        ${(sale.gst_option || 'without_gst') === 'with_gst' && company_info.gst_num ? `
                                                                                                                                                                        <font style="vertical-align: inherit;">
                                                                                                                                                                            <font style="vertical-align: inherit; font-size: 14px; font-weight: 400;">
                                                                                                                                                                               GST: ${company_info.gst_num}
                                                                                                                                                                            </font>
                                                                                                                                                                        </font><br>` : ''}
                                                                </td>


                                                                   <td style="padding:5px;vertical-align:top;text-align:left;padding-bottom:20px; width: 45%;" colspan="2">
                                                                        <font style="vertical-align: inherit;margin-bottom:25px;">
                                                                            <font style="vertical-align: inherit;font-size:14px;color:#7367F0;font-weight:600;line-height: 35px; ">${orderInfoLabel}</font>
                                                                        </font><br>
                                                                     <table style="width: 100%; border-collapse: collapse;">

                                                            ${isQuotation ? `
                                                                                                                                    <tr>
                                                                                                                                        <td style="padding: 2px 0; font-size: 14px; font-weight: 400; border: none;">Order Status</td>
                                                                                                                                        <td style="padding: 2px 0; font-size: 14px; font-weight: 400; text-align: right; border: none;">${orderStatusLabel}</td>
                                                                                                                                    </tr>
                                                                                                                                    <tr>
                                                                                                                                        <td style="padding: 2px 0; font-size: 14px; font-weight: 400; border: none;">
                                                                                                                                            Quotation Number
                                                                                                                                        </td>
                                                                                                                                        <td style="padding: 2px 0; font-size: 14px; font-weight: 400; text-align: right; border: none;">
                                                                                                                                            #${sale.order_number}
                                                                                                                                        </td>
                                                                                                                                    </tr>
                                                                                                                                    <tr>
                                                                                                                                        <td style="padding: 2px 0; font-size: 14px; font-weight: 400; border: none;">
                                                                                                                                            Quotation Date
                                                                                                                                        </td>
                                                                                                                                        <td style="padding: 2px 0; font-size: 14px; font-weight: 400; text-align: right; border: none;">
                                                                                                                                            ${sale.created_at}
                                                                                                                                        </td>
                                                                                                                                    </tr>
                                                                                                                                ` : `
                                                                                                                                    <tr>
                                                                                                                                        <td style="padding: 2px 0; font-size: 14px; font-weight: 400; border: none;">Order Status</td>
                                                                                                                                        <td style="padding: 2px 0; font-size: 14px; font-weight: 400; text-align: right; border: none;">${orderStatusLabel}</td>
                                                                                                                                    </tr>
                                                                                                                                    <tr>
                                                                                                                                        <td style="padding: 2px 0; font-size: 14px; font-weight: 400; border: none;">
                                                                                                                                            ${orderNumLabel}
                                                                                                                                        </td>
                                                                                                                                        <td style="padding: 2px 0; font-size: 14px; font-weight: 400; text-align: right; border: none;" class="invoice-id">
                                                                                                                                            ${sale.order_number}
                                                                                                                                        </td>
                                                                                                                                    </tr>
                                                                                                                                    <tr>
                                                                                                                                        <td style="padding: 2px 0; font-size: 14px; font-weight: 400; border: none;">
                                                                                                                                            ${orderDateLabel}
                                                                                                                                        </td>
                                                                                                                                        <td style="padding: 2px 0; font-size: 14px; font-weight: 400; text-align: right; border: none;">
                                                                                                                                            ${sale.created_at}
                                                                                                                                        </td>
                                                                                                                                    </tr>

                                                                                                                                    ${isFullyReturned ? `
                                                                    <tr>
                                                                        <td style="padding: 2px 0; font-size: 14px; color: red;">
                                                                            Order Status
                                                                        </td>
                                                                        <td style="padding: 2px 0; font-weight: bold; text-align: right; color: red;">
                                                                            Fully Returned
                                                                        </td>
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
                                                <tr class="heading " style="background: #F3F2F7;">
                                                    <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; ">
                                                        Product Name
                                                    </td>
                                                      <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; ">
                                                        Unit
                                                    </td>
                                                    <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; ">
                                                        Qty
                                                    </td>
                                                    ${isRentalOrder ? `
                                                    <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; ">Rent Value</td>
                                                    <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; ">Rent Price</td>
                                                    <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; ">Start To End Date</td>
                                                    ` : ''}
                                                    <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; ">
                                                        Price
                                                    </td>
                                                    ${discountHeaderColumn}
                                                    ${taxHeaderColumns}
                                                    <td style="padding: 5px;vertical-align: middle;font-weight: 600;color: #5E5873;font-size: 14px;padding: 10px; text-align: right; ">
                                                        Total
                                                    </td>
                                                </tr>`;

                        items.forEach((item) => {
                            const product = item.product || {};
                            const taxAmount =
                                0; // You can calculate tax if required from tax_ids
                            const discount = sale.discount || 0;
                            // let taxesHtml = '';
                            // let baseAmount = item.price * item.quantity;

                            // try {
                            //     const gstData = JSON.parse(product.product_gst);

                            //     gstData.forEach(tax => {
                            //         const rate = parseFloat(tax.tax_rate);
                            //         const taxAmount = (baseAmount * rate) / 100;

                            //         taxesHtml += `
                        //             <div>
                        //                 ${tax.tax_name} (${rate.toFixed(2)}%) :
                        //                 ${formatCurrency(taxAmount, company_info.currency_symbol, company_info.currency_position)}
                        //             </div>
                        //         `;
                            //     });
                            // } catch (e) {
                            //     console.error('Invalid GST JSON', e);
                            // }
                            let taxesHtml = '';

                            let rentMultiplier = 1;
                            if (isRentalOrder) {
                                rentMultiplier = parseInt(item.rent_value) || 1;
                                if (rentMultiplier <= 0) rentMultiplier = 1;
                            }

                            let calculatedItemGst = 0;
                            if (Array.isArray(item.product_tax) && item.product_tax.length > 0) {
                                const baseAmountForTax = parseFloat(item.price || 0) * parseFloat(item.quantity || 0) * rentMultiplier;
                                const totalRate = item.product_tax.reduce((s, t) => s + parseFloat(t.tax_rate || 0), 0);
                                const totalItemGst = baseAmountForTax * (totalRate / 100);

                                item.product_tax.forEach(tax => {
                                    const taxRate = parseFloat(tax.tax_rate || 0);
                                    const calculatedTaxAmount = totalRate > 0 ? Math.round(totalItemGst * (taxRate / totalRate) * 100) / 100 : 0;
                                    calculatedItemGst += calculatedTaxAmount;
                                    taxesHtml += `
                                    <div>
                                        ${tax.tax_name} (${tax.tax_rate}%):
                                        ${formatCurrency(
                                            calculatedTaxAmount,
                                            response.currency_symbol,
                                            response.currency_position
                                        )}
                                    </div>
                                `;
                                });
                            } else {
                                calculatedItemGst = parseFloat(item.product_gst_total || 0);
                            }
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

                                    if (images && images.length > 0 && images[0]) {
                                        imagePath = `${imageBasePath}/storage/${images[0]}`;
                                    }
                                } catch (e) {
                                    imagePath = defaultProductImage;
                                }
                            }
                            const itemQty = parseFloat(item.quantity || 0);
                            const itemGstTotal = parseFloat(item.product_gst_total || 0);
                            // price is now BASE price — display it directly (no GST subtraction)
                            const displayUnitPrice = parseFloat(item.price || 0);
                            const unitPrice = formatCurrency(displayUnitPrice, company_info
                                .currency_symbol, company_info.currency_position);
                            const discountAmount = Number(item.discount_amount ?? 0);
                            const discountPercentageValue = parseFloat(item
                                .discount_percentage ?? 0);

                            // show only when > 0
                            const showDiscountPercentage = discountPercentageValue > 0;
                            // Total column: baseLineTotal + calculatedItemGst - discountAmount
                            const baseLineTotal = parseFloat(item.price || 0) * parseFloat(item.quantity || 0) * rentMultiplier;
                            const rowFinalTotal = baseLineTotal + calculatedItemGst - discountAmount;
                            const totalAmount = formatCurrency(rowFinalTotal, company_info
                                .currency_symbol, company_info.currency_position);

                            // let orderSubtotal = 0;
                            // <tr class="details" style="border-bottom:1px solid #E9ECEF;">
                            const taxBodyColumns = hasGst ?
                                `<td class="tax-col" style="padding:10px; white-space:normal; text-align:center; word-wrap:break-word;">
                                    ${taxesHtml && taxesHtml.trim() !== '' ? taxesHtml : 'N/A'}
                                </td>` : '';
                            const discountBodyColumn = hasDiscount ?
                                `<td style="padding:10px; vertical-align:top;">
                                                        ${formatCurrency(
                                                            discountAmount,
                                                            company_info.currency_symbol,
                                                            company_info.currency_position
                                                        )}
                                                        ${showDiscountPercentage
                                                            ? `<small>(${discountPercentageValue.toFixed(2)}%)</small>`
                                                            : ''
                                                        }
                                                </td>` : '';

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
                                    <td style="padding: 10px;vertical-align: top;">${rentValue}</td>
                                    <td style="padding: 10px;vertical-align: top;">${rentPrice}</td>
                                    <td style="padding: 10px;vertical-align: top; white-space: nowrap;">${dateDisplay}</td>
                                `;
                            }

                            productRows += `
                                                <tr class="product-row">
                                               <td class="text-capitalize" style="padding: 10px; align:left; vertical-align: top;">
                                                <a href="/product-view/${product.id}" class="d-flex align-items-center text-decoration-none text-dark">
                                                    <img src="${imagePath}" alt="img" class="me-2" style="width:40px; height:40px;" onerror="this.onerror=null;this.src=defaultProductImage;">
                                                    <div>
                                                        ${product.name || 'N/A'}
                                                        ${item.variant ? `<br><small class="text-muted" style="font-size: 12px;">${item.variant.size ? 'Size: ' + item.variant.size : ''} ${item.variant.color ? (item.variant.size ? ' | ' : '') + 'Color: ' + item.variant.color : ''}</small>` : ''}
                                                        ${item.imei_no ? `<br><small class="text-muted" style="font-size: 12px;">IMEI: ${item.imei_no}</small>` : ''}
                                                    </div>
                                                </a>
                                                </td>
                                                <td class="text-capitalize" style="padding: 10px;vertical-align: top;">  ${item.product?.unit?.unit_name ?? 'N/A'}</td>
                                                <td style="padding: 10px;vertical-align: top;">${itemQty}</td>
                                                ${rentalColumns}
                                                    <td style="padding: 10px;vertical-align: top;">${unitPrice}</td>
                                               ${discountBodyColumn}

                                                    ${taxBodyColumns}
                                                    <td style="padding: 10px;vertical-align: top; text-align: right;">${totalAmount}</td>
                                            </tr>`;
                        });

                        // ✅ Return History logic
                        if (response.returns && response.returns.length > 0) {
                            let hasReturnTax = false;
                            response.returns.forEach(ret => {
                                ret.items.forEach(retItem => {
                                    if (parseFloat(retItem.product_gst_total || 0) >
                                        0) {
                                        hasReturnTax = true;
                                    }
                                });
                            });

                            const colCount = hasReturnTax ? 7 : 6;

                            let returnRows = `
                                <tr class="heading" style="background: #F3F2F7;">
                                    <td colspan="${colCount}" style="padding: 10px; font-weight: 600; color: #7367F0; font-size: 16px; text-align: center;">
                                        Return History
                                    </td>
                                </tr>
                                <tr class="heading" style="background: #F8F9FA;">
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Return ID</td>
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Return Date</td>
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Product Name</td>
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Return Qty</td>
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Price</td>
                                    ${hasReturnTax ? '<td style="padding: 10px; font-weight: 600; font-size: 14px;">Tax</td>' : ''}
                                    <td style="padding: 10px; font-weight: 600; font-size: 14px;">Total</td>
                                </tr>`;

                            response.returns.forEach(ret => {
                                // Format Return Date
                                const retDate = new Date(ret.created_at);
                                const formattedRetDate = retDate.toLocaleDateString('en-GB', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric'
                                }) + ' ' + retDate.toLocaleTimeString('en-GB', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true
                                });

                                ret.items.forEach(retItem => {
                                    const retProduct = retItem.product;
                                    const retUnitPrice = formatCurrency(retItem.price,
                                        response.currency_symbol, response
                                        .currency_position);
                                    const retTax = formatCurrency(retItem
                                        .product_gst_total, response
                                        .currency_symbol, response.currency_position
                                    );
                                    const retTotal = formatCurrency(retItem.subtotal,
                                        response.currency_symbol, response
                                        .currency_position);

                                    returnRows += `
                                        <tr class="details" style="border-bottom: 1px solid #E9ECEF;">
                                            <td style="padding: 10px; vertical-align: top;">#${ret.return_number}</td>
                                            <td style="padding: 10px; vertical-align: top;">${formattedRetDate}</td>
                                            <td style="padding: 10px; vertical-align: top;">${retProduct ? retProduct.name : 'N/A'}</td>
                                            <td style="padding: 10px; vertical-align: top;">${retItem.quantity}</td>
                                            <td style="padding: 10px; vertical-align: top;">${retUnitPrice}</td>
                                            ${hasReturnTax ? `<td style="padding: 10px; vertical-align: top;">${retTax}</td>` : ''}
                                            <td style="padding: 10px; vertical-align: top;">${retTotal}</td>
                                        </tr>`;
                                });
                            });
                            $("#return-history-details").html(returnRows);
                            $("#return-history-section").show();
                        } else {
                            $("#return-history-section").hide();
                        }

                        // ✅ Rental Return History logic
                        const rentalReturns = response.rental_returns || [];
                        if (rentalReturns.length > 0) {
                            let rentalReturnRows = `
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
                                const retDate = new Date(ret.created_at);
                                const formattedRetDate = retDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
                                    + ' ' + retDate.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: true });

                                (ret.items || []).forEach(retItem => {
                                    rentalReturnRows += `
                                        <tr class="details" style="border-bottom: 1px solid #E9ECEF;">
                                            <td style="padding: 10px; vertical-align: top;">#${ret.id}</td>
                                            <td style="padding: 10px; vertical-align: top;">${formattedRetDate}</td>
                                            <td style="padding: 10px; vertical-align: top;">
                                                ${retItem.product ? retItem.product.name : 'N/A'}
                                            </td>
                                            <td style="padding: 10px; vertical-align: top;">${retItem.qty_returned}</td>
                                            <td style="padding: 10px; vertical-align: top;">${formatCurrency(ret.final_settlement_amount || 0, currSym, currPos)}</td>
                                        </tr>`;
                                });
                            });

                            $("#rental-return-history-details").html(rentalReturnRows);
                            $("#rental-return-history-section").show();
                        } else {
                            $("#rental-return-history-section").hide();
                        }

                        $("#product-details").html(productRows);

                    },
                    error: function(xhr, status, error) {
                        // console.error("Error fetching sales details:", error);
                    }
                });

            }



        });
    </script>
@endpush
