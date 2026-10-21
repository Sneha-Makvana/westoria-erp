@extends('layout.app')

@section('title', 'Return Sale')

@section('content')
    <style>
        .d-none { display: none !important; }

        .sr-create .page-header {
            margin-bottom: 16px;
        }

        .sr-create .card {
            border: 1px solid #edf1f5;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
        }

        .sr-create .card-body {
            padding: 18px 20px 22px;
        }

        .sr-create .sr-filters .form-group {
            margin-bottom: 0;
        }

        .sr-create .sr-filters label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .sr-create .sr-filters .form-control,
        .sr-create .sr-filters .select2-container .select2-selection--single {
            height: 40px !important;
            border-radius: 8px;
            border-color: #e5e7eb;
        }

        .sr-create .sr-filters .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }

        .sr-create .sr-filters .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }

        .sr-create .sr-table-wrap {
            margin-top: 18px;
            border: 1px solid #edf1f5;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
        }

        .sr-create .sr-table-wrap .table {
            margin: 0;
            width: 100%;
        }

        .sr-create .sr-table-wrap thead th {
            background: #f8fafc;
            color: #6b7280;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            white-space: nowrap;
            border-bottom: 1px solid #edf1f5;
            padding: 10px 10px;
            vertical-align: middle;
        }

        .sr-create .sr-table-wrap tbody td {
            vertical-align: middle;
            padding: 10px;
            border-color: #f1f5f9;
            font-size: 13px;
            color: #1b2850;
            white-space: nowrap;
        }

        .sr-create .sr-table-wrap tbody td:nth-child(2) {
            white-space: normal !important;
            min-width: 180px;
            max-width: 280px;
            word-break: break-word;
        }

        .sr-create .sr-table-wrap tbody td:nth-child(2) a {
            display: flex !important;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .sr-create .sr-table-wrap tbody td:nth-child(2) img {
            width: 40px !important;
            height: 40px !important;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
            margin-right: 0 !important;
        }

        .sr-create .sr-table-wrap tbody td:nth-child(2) span {
            font-weight: 600;
            line-height: 1.3;
        }

        .sr-create .sr-table-wrap .quantity-input {
            width: 88px !important;
            height: 36px;
            text-align: right;
            border-radius: 8px;
            font-weight: 600;
        }

        .sr-create .sr-table-wrap tbody td:last-child {
            display: none;
        }

        .sr-create .sr-table-wrap thead th:last-child {
            display: none;
        }

        .sr-create .total-order {
            background: #f8fafc;
            border: 1px solid #edf1f5;
            border-radius: 10px;
            padding: 8px 16px;
        }

        .sr-create .total-order ul {
            margin: 0;
        }

        .sr-create .total-order ul li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed #e8eaef;
        }

        .sr-create .total-order ul li:last-child {
            border-bottom: 0;
        }

        .sr-create .total-order h4,
        .sr-create .total-order h5 {
            margin: 0;
            font-size: 13px;
        }

        .sr-create .total-order .total h4,
        .sr-create .total-order .total h5 {
            font-size: 16px;
            font-weight: 700;
            color: #1b2850;
        }

            .sr-create .sr-actions {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
                margin-top: 8px;
            }

            .sr-refund-options {
                display: none;
                margin: 0 0 16px;
                padding: 12px 14px;
                border: 1px dashed #ff9f43;
                border-radius: 10px;
                background: #fff8f0;
            }

            .sr-refund-options.is-visible {
                display: block;
            }

            .sr-refund-options-title {
                font-size: 13px;
                font-weight: 700;
                color: #c2410c;
                margin-bottom: 8px;
            }

            .sr-refund-choices {
                display: flex;
                flex-wrap: wrap;
                gap: 14px;
                font-size: 13px;
                font-weight: 600;
                color: #1b2850;
            }

            .sr-refund-choices label {
                display: flex;
                align-items: center;
                gap: 6px;
                cursor: pointer;
                margin: 0;
            }

            .sr-refund-hint {
                display: none;
                margin-top: 6px;
                font-size: 11px;
                color: #6b7280;
            }

            .sr-balance-chip {
                display: none;
                margin-top: 6px;
                font-size: 12px;
                font-weight: 700;
                color: #0f766e;
            }

            .sr-balance-chip.is-visible {
                display: block;
            }

            .sr-balance-snapshot {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                flex-wrap: wrap;
                margin-top: 10px;
                padding-top: 8px;
                border-top: 1px dashed #ffd4a8;
                font-size: 12px;
            }

            .sr-balance-snapshot span {
                color: #6b7280;
                font-weight: 600;
            }

            .sr-balance-snapshot b {
                color: #1b2850;
                font-weight: 700;
            }

        /* Tablet: keep real table like other pages, just denser */
        @media screen and (min-width: 768px) and (max-width: 1024.98px) {
            .sr-create .card-body {
                padding: 14px;
            }

            .sr-create .sr-table-wrap thead th {
                font-size: 10px;
                padding: 8px 6px;
            }

            .sr-create .sr-table-wrap tbody td {
                font-size: 12px;
                padding: 8px 6px;
            }

            .sr-create .sr-table-wrap tbody td:nth-child(2) {
                min-width: 140px;
                max-width: 200px;
            }

            .sr-create .sr-table-wrap tbody td:nth-child(2) img {
                width: 32px !important;
                height: 32px !important;
            }

            .sr-create .sr-table-wrap .quantity-input {
                width: 72px !important;
                height: 32px;
                font-size: 12px;
            }
        }

        /* Phone only: stacked cards */
        @media screen and (max-width: 767.98px) {
            .sr-create .sr-filters > [class*="col-"] {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .sr-create .sr-filters .form-group {
                margin-bottom: 12px;
            }

            .sr-create .sr-table-wrap {
                border: 0;
                overflow: visible;
                background: transparent;
            }

            .sr-create .sr-table-wrap .table-responsive {
                overflow: visible;
            }

            .sr-create .sr-table-wrap table thead {
                display: none;
            }

            .sr-create .sr-table-wrap table,
            .sr-create .sr-table-wrap table tbody,
            .sr-create .sr-table-wrap table tr,
            .sr-create .sr-table-wrap table td {
                display: block;
                width: 100%;
            }

            #product-table-body {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            #product-table-body tr[data-product-id] {
                padding: 12px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                background: #fff;
                box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
            }

            #product-table-body tr[data-product-id] > td {
                border: 0;
                padding: 0;
                white-space: normal !important;
            }

            #product-table-body tr[data-product-id] > td:first-child,
            #product-table-body tr[data-product-id] > td:last-child {
                display: none;
            }

            #product-table-body tr[data-product-id] > td:nth-child(2) {
                margin-bottom: 8px;
            }

            #product-table-body tr[data-product-id] > td:nth-child(2) img {
                width: 48px !important;
                height: 48px !important;
            }

            #product-table-body tr[data-product-id] > td:not(:first-child):not(:nth-child(2)):not(:last-child) {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 7px 0;
                border-top: 1px dashed #f1f5f9;
            }

            #product-table-body tr[data-product-id] > td:not(:first-child):not(:nth-child(2)):not(:last-child)::before {
                content: attr(data-label);
                color: #6b7280;
                font-size: 12px;
                font-weight: 600;
            }

            #product-table-body tr[data-product-id] input.form-control {
                width: 110px !important;
                margin-left: auto;
            }

            .sr-create .total-order {
                max-width: none !important;
                margin: 0 0 12px !important;
            }

            .sr-create .sr-actions .btn {
                flex: 1 1 auto;
            }
        }
    </style>

    <div class="content sr-create">
        <div class="page-header">
            <div class="page-title">
                <h4>Create Return Sale</h4>
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(2, 'add') && app('hasPermission')(2, 'edit'))
                    <a href="{{ route('salesreturn.list') }}" class="btn btn-added"><i class="fas fa-arrow-left me-1"></i> Back</a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row g-3 sr-filters align-items-end">
                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                            <label>Order Number</label>
                            <select name="user_id" class="form-control select2-invoices">
                                <option value="">Select Order Number</option>
                                @foreach ($invoiceNumbers as $invoiceNumber)
                                    <option value="{{ $invoiceNumber }}" @if (!empty($preselectOrderNumber) && $preselectOrderNumber === $invoiceNumber) selected @endif>{{ $invoiceNumber }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                            <label>Customer Name</label>
                            <input type="text" id="customer_phone" class="form-control"
                                placeholder="Customer Name" name="customer_phone"
                                value="{{ $sales->user->name ?? ($sales->user->phone ?? '') }}" readonly>
                            <div id="srCustomerBalanceChip" class="sr-balance-chip">
                                Current balance: <span id="srCustomerBalanceText">{{ $currencySymbol }}0.00</span>
                            </div>
                            <span class="error_customerphone"></span>
                        </div>
                    </div>
                </div>

                <div class="sr-table-wrap mt-3 mb-3">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Sold Qty</th>
                                    <th>Already Returned</th>
                                    <th>Return Qty</th>
                                    <th>Price</th>
                                    <th>Discount Amt</th>
                                    <th class="gst-column" style="display: none;">GST</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="product-table-body"></tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="row justify-content-end">
                        <div class="col-lg-6">
                            <div class="total-order w-100 max-widthauto m-auto mb-4">
                                <ul>
                                    <li class="order-subtotal">
                                        <h4>Subtotal</h4>
                                        <h5>
                                            @if ($currencyPosition == 'left')
                                                {{ $currencySymbol }} <span id="subtotal">0.00</span>
                                            @else
                                                <span id="subtotal">0.00</span> {{ $currencySymbol }}
                                            @endif
                                        </h5>
                                    </li>

                                    <li class="discount" style="display:none;">
                                        <h4>Discount</h4>
                                        <h5 id="discount">0.00</h5>
                                    </li>

                                    {{-- Hidden anchor used by GST / shipping / round-off rows --}}
                                    <li class="after-discount" style="display:none !important;">
                                        <h4>After Discount</h4>
                                        <h5>
                                            @if ($currencyPosition == 'left')
                                                {{ $currencySymbol }} <span id="after-discount">0.00</span>
                                            @else
                                                <span id="after-discount">0.00</span> {{ $currencySymbol }}
                                            @endif
                                        </h5>
                                    </li>

                                    <li class="total">
                                        <h4>Return Total</h4>
                                        <h5>
                                            @if ($currencyPosition == 'left')
                                                {{ $currencySymbol }} <span id="grand-total">0.00</span>
                                            @else
                                                <span id="grand-total">0.00</span> {{ $currencySymbol }}
                                            @endif
                                        </h5>
                                    </li>
                                </ul>
                            </div>

                            <div id="srRefundOptions" class="sr-refund-options">
                                <div class="sr-refund-options-title">
                                    Settle return:
                                    <span id="srRefundAmountLabel">{{ $currencySymbol }}0.00</span>
                                </div>
                                <div class="sr-refund-choices">
                                    <label>
                                        <input type="radio" name="refund_action" value="return_cash">
                                        Return Cash
                                    </label>
                                    <label>
                                        <input type="radio" name="refund_action" value="deposit">
                                        Add to Customer Balance
                                    </label>
                                </div>
                                <div class="sr-balance-snapshot">
                                    <div>Current balance: <b id="srBalanceCurrent">{{ $currencySymbol }}0.00</b></div>
                                    <div>After this: <b id="srBalanceAfter">{{ $currencySymbol }}0.00</b></div>
                                </div>
                                <div id="srRefundDepositHint" class="sr-refund-hint">
                                    This amount stays on the customer and can be used on the next bill.
                                </div>
                                <input type="hidden" id="sr_refund_action" value="">
                                <input type="hidden" id="sr_customer_balance" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 sr-actions">
                        <button type="submit" class="btn btn-submit me-2">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true" id="submit-spinner"></span>
                            <span id="submit-text">Save Return</span>
                        </button>
                        <a href="{{ route('sales.list') }}" class="btn btn-cancel">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
$(document).ready(function () {

    // ─── Global Variables ────────────────────────────────────────────────────────
    const currencySymbol   = @json($currencySymbol);
    const currencyPosition = @json($currencyPosition);
    const authToken        = localStorage.getItem("authToken");

    let originalPaidAmount  = 0;
    let originalTotalAmount = 0;
    let shippingCharge      = 0;   // ← Set when invoice is loaded
    let currentCustomerId   = 0;
    let currentCustomerName = '';
    let currentCustomerBalance = 0;

    function updateSrBalanceSnapshot() {
        const current = Math.max(0, currentCustomerBalance);
        const refundTotal = parseFloat($('#grand-total').text().replace(/[^\d.]/g, '')) || 0;
        const action = ($('#sr_refund_action').val() || $('input[name="refund_action"]:checked').val() || '').trim();
        const after = action === 'deposit' ? current + refundTotal : current;
        $('#srCustomerBalanceText').text(formatCurrency(current));
        $('#srCustomerBalanceChip').toggleClass('is-visible', currentCustomerId > 0 && !/default customer/i.test(currentCustomerName || ''));
        $('#sr_customer_balance').val(current.toFixed(2));
        $('#srBalanceCurrent').text(formatCurrency(current));
        $('#srBalanceAfter').text(formatCurrency(after));
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────
    function formatCurrency(amount) {
        let num       = parseFloat(amount || 0);
        let formatted = num.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return currencyPosition === 'left'
            ? `${currencySymbol}${formatted}`
            : `${formatted}${currencySymbol}`;
    }

    function formatNumber(amount) {
        const num = parseFloat(amount || 0);

        return num.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function normalizeGstDetails(rawValue) {
        if (!rawValue) {
            return [];
        }

        let parsedValue = rawValue;

        if (typeof parsedValue === 'string') {
            try {
                parsedValue = JSON.parse(parsedValue);
            } catch (error) {
                return [];
            }
        }

        if (typeof parsedValue === 'string') {
            try {
                parsedValue = JSON.parse(parsedValue);
            } catch (error) {
                return [];
            }
        }

        if (Array.isArray(parsedValue)) {
            return parsedValue;
        }

        if (parsedValue && typeof parsedValue === 'object') {
            return Object.values(parsedValue);
        }

        return [];
    }

    /**
     * Returns true only when EVERY row's (returnQty + alreadyReturned) === originalQty.
     * This is the condition that unlocks shipping refund.
     */
    function areAllItemsFullyReturned() {
        let allFull = true;
        const epsilon = 0.0001;

        $('#product-table-body tr').each(function () {
            const $row          = $(this);
            const returnQty     = parseFloat($row.find('.quantity-input').val()) || 0;
            const originalQty   = parseFloat($row.data('original-qty'))          || 0;
            const availableQty  = parseFloat($row.data('available-qty'))         || 0;
            const alreadyReturned = originalQty - availableQty;

            if ((returnQty + alreadyReturned) < (originalQty - epsilon)) {
                allFull = false;
                return false; // break $.each
            }
        });

        return allFull;
    }

    // ─── Calculate Totals ────────────────────────────────────────────────────────
    function calculateTotals() {
        let subtotal            = 0;
        let totalDiscountAmount = 0;
        let totalGST            = 0;

        $('#product-table-body tr').each(function () {
            const $row                    = $(this);
            const quantity                = parseFloat($row.find('.quantity-input').val())                      || 0;
            const originalQty             = parseFloat($row.data('original-qty'))                               || 1;
            const price                   = parseFloat($row.find('.quantity-input').data('price'))              || 0;
            const originalDiscountAmount  = parseFloat($row.data('original-discount-amount'))                   || 0;
            const originalDiscountPct     = parseFloat($row.data('original-discount-percentage'))               || 0;
            const gstTotalPerItem         = parseFloat($row.find('.quantity-input').data('gst-total'))          || 0;

            // Pro-rate discount & GST
            const proRatedDiscount = (originalDiscountAmount / originalQty) * quantity;
            const proRatedGST      = (gstTotalPerItem         / originalQty) * quantity;

            const lineTotal = price * quantity;
            subtotal            += lineTotal;
            totalDiscountAmount += proRatedDiscount;
            totalGST            += proRatedGST;

            // ── Update Discount column (index 6) ──
            const $discountCol = $row.find('td:eq(6)');
            if (proRatedDiscount > 0) {
                $discountCol.html(
                    originalDiscountPct > 0
                        ? `${formatCurrency(proRatedDiscount)} (${originalDiscountPct.toFixed(2)}%)`
                        : formatCurrency(proRatedDiscount)
                );
            } else {
                $discountCol.html(formatCurrency('0.00'));
            }

            // ── Update GST column (index 7) ──
            const gstDetails = normalizeGstDetails($row.data('gst-details'));

            let gstHtml = '';
            if (gstDetails.length) {
                $.each(gstDetails, function (key, gst) {
                    const name          = gst.tax_name || key;
                    const rate          = gst.tax_rate  || 0;
                    const amount        = parseFloat(gst.tax_amount || 0);
                    const proRatedAmt   = (amount / originalQty) * quantity;
                    gstHtml += `<div><small>${name}(${rate}%): ${formatCurrency(proRatedAmt)}</small></div>`;
                });
            } else {
                gstHtml = proRatedGST > 0
                    ? formatCurrency(proRatedGST)
                    : formatCurrency('0.00');
            }
            $row.find('.item-gst').html(gstHtml);

            // ── Update Subtotal column (index 8) ──
            $row.find('td:eq(8)').text(formatCurrency(lineTotal));
        });

        // ── Summary panel ──────────────────────────────────────────────────────
        $('#subtotal').text(formatNumber(subtotal));

        // Discount display
        let discountText = '';
        if (totalDiscountAmount > 0) {
            discountText = formatCurrency(totalDiscountAmount);
        } else {
            discountText = formatCurrency('0.00');
        }
        $('#discount').text(discountText);
        $('.total-order ul .discount').toggle(totalDiscountAmount > 0);

        const afterDiscount = subtotal - totalDiscountAmount;
        $('#after-discount').text(formatNumber(afterDiscount));

        // Remove dynamic rows before re-inserting
        $('.total-order ul').find('li.tax-row, li.shipping-row, li.round-off-row').remove();

        // GST row (inserted after after-discount)
        if (totalGST > 0) {
            $('.total-order ul .after-discount').after(
                `<li class="tax-row"><h4>Tax</h4><h5>${formatCurrency(totalGST)}</h5></li>`
            );
        }

        // ── Shipping: only add when ALL items are fully returned ──────────────
        const shippingAmount = areAllItemsFullyReturned() ? shippingCharge : 0;

        if (shippingAmount > 0) {
            const shippingRow = `<li class="shipping-row"><h4>Shipping</h4><h5>${formatCurrency(shippingAmount)}</h5></li>`;
            // Insert after GST row if it exists, otherwise after after-discount
            if (totalGST > 0) {
                $('.total-order ul .tax-row').after(shippingRow);
            } else {
                $('.total-order ul .after-discount').after(shippingRow);
            }
        }

        // Round Off + Grand Total
        const rawGrandTotal = afterDiscount + totalGST + shippingAmount;
        const roundedGrandTotal = Math.round(rawGrandTotal);
        const roundOffAmount = roundedGrandTotal - rawGrandTotal;

        const roundOffRow = Math.abs(roundOffAmount) > 0.001
            ? `<li class="round-off-row"><h4>Round Off</h4><h5>${formatCurrency(roundOffAmount)}</h5></li>`
            : `<li class="round-off-row d-none"><h4>Round Off</h4><h5>${formatCurrency(roundOffAmount)}</h5></li>`;

        if (shippingAmount > 0) {
            $('.total-order ul .shipping-row').after(roundOffRow);
        } else if (totalGST > 0) {
            $('.total-order ul .tax-row').after(roundOffRow);
        } else {
            $('.total-order ul .after-discount').after(roundOffRow);
        }

        $('#grand-total').text(formatNumber(roundedGrandTotal));

        if (roundedGrandTotal > 0) {
            $('#srRefundOptions').addClass('is-visible');
            $('#srRefundAmountLabel').text(formatCurrency(roundedGrandTotal));
            if (!$('input[name="refund_action"]:checked').val()) {
                $('input[name="refund_action"][value="deposit"]').prop('checked', true);
                $('#sr_refund_action').val('deposit');
                $('#srRefundDepositHint').show();
            }
            updateSrBalanceSnapshot();
        } else {
            $('#srRefundOptions').removeClass('is-visible');
            $('input[name="refund_action"]').prop('checked', false);
            $('#sr_refund_action').val('');
            $('#srRefundDepositHint').hide();
            updateSrBalanceSnapshot();
        }
    }

    // ─── Select2 Init ────────────────────────────────────────────────────────────
    $('.select2-invoices').select2({
        placeholder: 'Select Order Number',
        allowClear:  true,
        width:       '100%'
    });

    // ─── Invoice Change → Load Products (SINGLE handler) ────────────────────────
    $('select[name="user_id"]').on('change', function () {
        const invoiceNumber     = $(this).val();
        const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

        if (!invoiceNumber) {
            // Reset everything when cleared
            $('#customer_phone').val('');
            $('#product-table-body').empty();
            shippingCharge      = 0;
            originalPaidAmount  = 0;
            originalTotalAmount = 0;
            currentCustomerId   = 0;
            currentCustomerName = '';
            currentCustomerBalance = 0;
            calculateTotals();
            return;
        }

        $.ajax({
            url:  `/api/getSaleDetails/${invoiceNumber}`,
            type: 'GET',
            headers: { "Authorization": "Bearer " + authToken },
            data: { selectedSubAdminId: selectedSubAdminId },

            success: function (data) {
                if (data.error) { alert(data.error); return; }

                // ── Store order-level values ──────────────────────────────────
                shippingCharge      = parseFloat(data.order.shipping)          || 0;
                originalPaidAmount  = parseFloat(data.order.paid_amount)       || 0;
                originalTotalAmount = parseFloat(data.order.total_amount)      || 0;

                $('#customer_phone').val(data.order.user_name);
                currentCustomerId   = parseInt(data.order.user_id, 10) || 0;
                currentCustomerName = data.order.user_name || '';
                currentCustomerBalance = parseFloat(data.order.deposit_balance) || 0;

                // ── Build product rows ────────────────────────────────────────
                const $tableBody = $('#product-table-body').empty();
                let hasGst = false;

                $.each(data.items, function (index, item) {
                    const price            = parseFloat(item.price)                  || 0;
                    const gstDetails       = normalizeGstDetails(item.product_gst_details);
                    const gstTotal         = parseFloat(item.product_gst_total)      || 0;
                    const soldQuantity     = parseFloat(item.sold_quantity)           || 0;
                    const availableQty     = parseFloat(item.quantity)               || 0;   // returnable
                    const returnedQuantity = parseFloat(item.returned_quantity)       || 0;

                    let discountAmount     = parseFloat(item.discount_amount)        || 0;
                    let discountPct        = parseFloat(item.discount_percentage)    || 0;

                    // Cross-calculate if one is missing
                    if (discountPct === 0 && discountAmount > 0 && price > 0) {
                        discountPct = (discountAmount / price) * 100;
                    }
                    if (discountAmount === 0 && discountPct > 0 && price > 0) {
                        discountAmount = (price * discountPct) / 100;
                    }

                    // Discount display for initial load (qty = 0, so show 0.00)
                    const discountDisplay = formatCurrency('0.00');

                    // GST column
                    if (gstTotal > 0 || gstDetails.length) {
                        hasGst = true;
                    }

                    let gstHtml = '';
                    if (gstDetails.length) {
                        $.each(gstDetails, function (key, gst) {
                            gstHtml += `<div><small>${gst.tax_name || key}(${gst.tax_rate || 0}%): ${formatCurrency('0.00')}</small></div>`;
                        });
                    } else {
                        gstHtml = formatCurrency('0.00');
                    }

                    const row = `
                        <tr data-product-id="${item.id}"
                            data-gst-details='${JSON.stringify(gstDetails)}'
                            data-gst-total="${gstTotal}"
                            data-original-qty="${soldQuantity}"
                            data-available-qty="${availableQty}"
                            data-original-discount-amount="${discountAmount}"
                            data-original-discount-percentage="${discountPct}">
                            <td>${index + 1}</td>
                            <td data-label="Product Name" style="width:100%;">
                                <a href="/product-view/${item.product_id}" style="display:flex;align-items:center;text-decoration:none;color:inherit;">
                                    <img src="${item.product_image
                                        ? '{{ env('ImagePath') }}/storage/' + item.product_image
                                        : '{{ env('ImagePath') }}/admin/assets/img/product/noimage.png'}"
                                        alt="${item.product_name}" width="40" height="40"
                                        style="object-fit:cover;border-radius:4px;margin-right:10px;flex-shrink:0;">
                                    <span style="flex:1;">${item.product_name || 'N/A'}</span>
                                </a>
                            </td>
                            <td data-label="Sold Qty">${parseFloat(soldQuantity).toFixed(2)}</td>
                            <td data-label="Already Returned">${parseFloat(returnedQuantity).toFixed(2)}</td>
                            <td data-label="Return Qty">
                                <input type="text" class="form-control quantity-input"
                                    value="0.00" step="0.01" min="0" max="${availableQty}"
                                    data-price="${price}"
                                    data-gst-total="${gstTotal}"
                                    data-original-discount-amount="${discountAmount}"
                                    data-original-discount-percentage="${discountPct}"
                                    data-original-qty="${soldQuantity}"
                                    style="width:80px;">
                            </td>
                            <td data-label="Price">${formatCurrency(price)}</td>
                            <td data-label="Discount Amt" class="discount-column">${discountDisplay}</td>
                            <td data-label="GST" class="item-gst gst-column">${gstHtml}</td>
                            <td data-label="Subtotal">${formatCurrency(0)}</td>
                            <td data-label="Action"></td>
                        </tr>`;
                    $tableBody.append(row);
                });

                // Show/hide GST column header + cells
                if (hasGst) {
                    $('.gst-column').show();
                } else {
                    $('.gst-column').hide();
                }

                calculateTotals();
            },

            error: function () {
                alert('Something went wrong while loading order details!');
            }
        });
    });

    // Pre-select bill when opened from Sales list (?order_number=…) — after change handler is bound
    (function preselectSalesReturnOrder() {
        var fromServer = @json($preselectOrderNumber ?? '');
        var fromQuery = '';
        try {
            fromQuery = new URLSearchParams(window.location.search).get('order_number') || '';
        } catch (e) {}
        var orderNumber = String(fromServer || fromQuery || '').trim();
        if (!orderNumber) return;
        var $sel = $('select[name="user_id"]');
        if (!$sel.find('option').filter(function() { return $(this).val() === orderNumber; }).length) {
            $sel.append($('<option></option>').val(orderNumber).text(orderNumber));
        }
        $sel.val(orderNumber).trigger('change');
    })();

    $(document).on('change', 'input[name="refund_action"]', function () {
        const action = $(this).val() || '';
        $('#sr_refund_action').val(action);
        $('#srRefundDepositHint').toggle(action === 'deposit');
        updateSrBalanceSnapshot();
        if (action === 'deposit') {
            const isDefault = /default customer/i.test(currentCustomerName || '');
            if (!currentCustomerId || isDefault) {
                Swal.fire({
                    icon: 'info',
                    title: 'Select customer',
                    text: 'This return is on Default Customer. Deposit needs a named customer.'
                });
            }
        }
    });

    // ─── Quantity Input Change ───────────────────────────────────────────────────
    $(document).on('input', '.quantity-input', function () {
        // Clamp value between 0 and max
        const max = parseFloat($(this).attr('max')) || 0;
        const rawValue = $(this).val().replace(/[^0-9.]/g, '');
        const parts = rawValue.split('.');
        const normalizedValue = parts.length > 2
            ? `${parts[0]}.${parts.slice(1).join('')}`
            : rawValue;

        let val = parseFloat(normalizedValue);

        if (normalizedValue !== $(this).val()) {
            $(this).val(normalizedValue);
        }

        if (isNaN(val)) {
            val = 0;
        }

        if (val < 0) {
            val = 0;
        }

        if (val > max) {
            val = max;
        }

        if (normalizedValue !== '' && !normalizedValue.endsWith('.')) {
            $(this).val(val.toString());
        }

        calculateTotals();
    });

    // ─── Submit Handler ──────────────────────────────────────────────────────────
    $(document).on('click', '.btn-submit', function (e) {
        e.preventDefault();

        const $btn     = $(this);
        const $spinner = $('#submit-spinner');
        const $text    = $('#submit-text');

        const allFullyReturned = areAllItemsFullyReturned();

        const refundAction = ($('#sr_refund_action').val() || $('input[name="refund_action"]:checked').val() || '').trim();
        const refundTotal  = parseFloat($('#grand-total').text().replace(/[^\d.]/g, '')) || 0;

        if (refundTotal > 0 && !refundAction) {
            Swal.fire({
                title: 'Refund option required',
                text:  'Choose Return Cash or Deposit to Customer.',
                icon:  'warning',
                confirmButtonColor: '#ffa957'
            });
            return;
        }

        if (refundAction === 'deposit') {
            const isDefault = /default customer/i.test(currentCustomerName || '');
            if (!currentCustomerId || isDefault) {
                Swal.fire({
                    title: 'Select customer',
                    text:  'Deposit needs a real customer on this bill, not Default Customer.',
                    icon:  'warning',
                    confirmButtonColor: '#ffa957'
                });
                return;
            }
        }

        const formData = {
            products:    [],
            discount:    0,   // No additional discount input in this form
            shipping:    allFullyReturned ? shippingCharge : 0,
            grand_total: $('#grand-total').text().replace(/[^\d.]/g, '').trim(),
            refund_action: refundAction || null,
            refund_amount: refundTotal
        };

        $('#product-table-body tr').each(function () {
            const $row                   = $(this);
            const productId              = $row.data('product-id');
            const quantity               = parseFloat($row.find('.quantity-input').val()) || 0;
            const originalQty            = parseFloat($row.data('original-qty'))           || 1;
            const price                  = parseFloat($row.find('.quantity-input').data('price')) || 0;
            const originalDiscountAmount = parseFloat($row.data('original-discount-amount'))  || 0;
            const discountPct            = parseFloat($row.data('original-discount-percentage')) || 0;
            const gstTotalPerItem        = parseFloat($row.find('.quantity-input').data('gst-total')) || 0;

            const proRatedDiscount = (originalDiscountAmount / originalQty) * quantity;
            const proRatedGST      = (gstTotalPerItem         / originalQty) * quantity;

            const gstDetails = normalizeGstDetails($row.data('gst-details'));

            let proRatedGstDetails = [];
            if (gstDetails.length) {
                $.each(gstDetails, function (key, gst) {
                    const amount      = parseFloat(gst.tax_amount || 0);
                    const proRatedAmt = (amount / originalQty) * quantity;
                    proRatedGstDetails.push({
                        tax_name:   gst.tax_name  || key,
                        tax_rate:   gst.tax_rate  || 0,
                        tax_amount: proRatedAmt.toFixed(2)
                    });
                });
            }

            if (productId && quantity > 0) {
                formData.products.push({
                    order_item_id:       productId,
                    quantity:            quantity,
                    price:               price,
                    subtotal:            (price * quantity).toFixed(2),
                    discount_amount:     proRatedDiscount.toFixed(2),
                    discount_percentage: discountPct,
                    product_gst_details: proRatedGstDetails,
                    product_gst_total:   proRatedGST.toFixed(2)
                });
            }
        });

        if (formData.products.length === 0) {
            Swal.fire({
                title: 'Error',
                text:  'Please enter a return quantity greater than 0 for at least one product.',
                icon:  'error',
                confirmButtonText:  'OK',
                confirmButtonColor: '#ffa957'
            });
            return;
        }

        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        $text.text('Saving...');

        $.ajax({
            url:  '/api/return_sale',
            type: 'POST',
            headers: { "Authorization": "Bearer " + authToken },
            data: formData,

            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success',
                        text:  response.message,
                        icon:  'success',
                        confirmButtonText:  'OK',
                        confirmButtonColor: '#ffa957'
                    }).then(result => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('salesreturn.list') }}";
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text:  response.message || 'Something went wrong.',
                        icon:  'error',
                        confirmButtonText:  'OK',
                        confirmButtonColor: '#ffa957'
                    });
                    $btn.prop('disabled', false);
                    $spinner.addClass('d-none');
                    $text.text('Save Return');
                }
            },

            error: function (xhr) {
                const serverMsg = xhr && xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error);
                Swal.fire({
                    title: 'Error',
                    text:  serverMsg || 'An error occurred while updating the order.',
                    icon:  'error',
                    confirmButtonText:  'OK',
                    confirmButtonColor: '#ffa957'
                });
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                $text.text('Save Return');
            }
        });
    });

}); // end document.ready
</script>
@endpush
