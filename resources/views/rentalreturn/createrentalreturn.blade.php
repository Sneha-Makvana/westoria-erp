@extends('layout.app')

@section('title', 'Return Rental')

@section('content')
    <style>
        .d-none { display: none !important; }
        .table-responsive table td:nth-child(2) {
            word-wrap: break-word; word-break: break-word;
            white-space: normal !important; min-width: 150px;
        }
        .submit-bar {
            position: sticky; bottom: 0; background: #FF9F43;
            color: white; padding: 15px; border-radius: 5px;
            display: flex; justify-content: space-between;
            align-items: center; z-index: 1000;
        }
        .submit-bar h4 { color: white; margin: 0; }
        .submit-bar .btn {
            background: transparent; border: 1px solid white; color: white;
        }
        .submit-bar .btn:hover { background: white; color: #FF9F43; }

        .summary-box {
            background: #f8f9fa; padding: 20px; border-radius: 8px;
            border: 1px solid #e9ecef;
            height: 100%;
        }
        .summary-box h5 { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .summary-box h5 span.value { font-weight: bold; }

        /* ── Full-return badge shown in summary ── */
        .full-return-badge {
            display: none;
            background: #d4edda; color: #155724;
            border: 1px solid #c3e6cb; border-radius: 6px;
            padding: 8px 12px; font-size: 13px; font-weight: 600;
            margin-top: 10px; align-items: center; gap: 6px;
        }
        .full-return-badge.show { display: flex; }

        /* ── Payment method tiles ── */
        .setvaluecash ul {
            margin: 0; padding: 0;
            display: flex; gap: 8px;
            list-style: none; flex-wrap: nowrap; overflow-x: auto;
        }
        .setvaluecash ul::-webkit-scrollbar { display: none; }
        .setvaluecash ul li { flex: 1 1 0; min-width: 60px; }
        .setvaluecash ul li a {
            border: 1px solid #e9ecef; color: #000;
            font-size: 12px; font-weight: 600; min-height: 59px;
            border-radius: 5px; padding: 8px 7px;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            text-align: center; text-decoration: none;
            width: 100%; box-sizing: border-box;
        }
        .setvaluecash ul li a i { font-size: 16px; display: block; margin-bottom: 4px; }
        .setvaluecash ul li a:hover { background-color: #1b2850; color: white !important; }
        .paymentmethod.active { background-color: #1b2850 !important; color: white !important; }
        .paymentmethod.active i { color: white !important; }

        /* ── Sub-boxes ── */
        #posPaidTypeBox, #cashOnlineBox, #bankSelectionBox, #emiBox {
            width: 100%; box-sizing: border-box;
        }
        #posPaidTypeBox .form-group label,
        #cashOnlineBox .form-group label,
        #bankSelectionBox .form-group label,
        #emiBox .form-group label {
            font-size: 12px; font-weight: 600; margin-bottom: 4px; color: #444;
        }
        #posPendingAmount { background: #f8f9fa !important; color: #555; }

        /* Full-return locked state */
        #posPaidAmount.locked, #posPaidType.locked {
            background: #e9ecef !important;
            cursor: not-allowed; pointer-events: none;
            color: #495057;
        }
        .full-return-notice {
            display: none; background: #fff3cd; border: 1px solid #ffc107;
            border-radius: 5px; padding: 8px 12px; font-size: 12px;
            color: #856404; margin-top: 6px; font-weight: 600;
        }
        .full-return-notice.show { display: block; }

        #emiBox {
            max-height: 250px; overflow-y: auto; overflow-x: hidden;
            padding: 8px 6px 0 0;
        }
        #emiBox::-webkit-scrollbar { width: 5px; }
        #emiBox::-webkit-scrollbar-thumb { background: #d7dde8; border-radius: 10px; }

        .bank-label-row {
            display: flex; align-items: center;
            justify-content: space-between; gap: 10px; margin-bottom: 4px;
        }

        /* Validation error for amount */
        .amount-error {
            display: none; color: #dc3545;
            font-size: 12px; margin-top: 4px; font-weight: 600;
        }
        .amount-error.show { display: block; }
    </style>

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Create Return Rental</h4>
            </div>
            <div class="page-btn">
                <a href="/rental-return-list" class="btn btn-added">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Order ID</label>
                            <select name="order_id" id="order_id" class="form-control select2-invoices">
                                <option value="">Select Order ID</option>
                                @foreach ($invoiceNumbers as $invoiceNumber)
                                    <option value="{{ $invoiceNumber }}">{{ $invoiceNumber }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Customer Name</label>
                            <input type="text" id="customer_name" class="form-control"
                                placeholder="Customer Name" readonly style="background-color: #e9ecef;">
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="table-responsive mb-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>Total Qty</th>
                                        <th>Received Qty</th>
                                        <th>Remaining Qty</th>
                                        <th>Rent Period</th>
                                        <th>Price</th>
                                        <th>Discount Amt</th>
                                        <th class="gst-column">GST</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="product-table-body">
                                    <tr>
                                        <td colspan="8" class="text-center">Select an order to view products</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group mt-4">
                            <label>Reviews</label>
                            <textarea name="reviews" id="reviews" class="form-control" rows="3"
                                placeholder="Enter reviews"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Summary + Payment Section side by side -->
                <div class="row mt-4 align-items-stretch">

                    {{-- LEFT: Summary box --}}
                    <div class="col-lg-6 col-12 mb-3 mb-lg-0" id="summaryCol">
                        <div class="setvalue summary-box h-100" id="summaryBoxContainer" style="background:#fff; border-radius:4px; padding:20px; border:1px solid #ddd;">
                            <ul style="list-style:none; padding:0; margin:0;">
                                <li style="display:flex; justify-content:space-between; margin-bottom:12px;">
                                    <h5 style="margin:0; font-size:14px; color:#555;">Total (Product)</h5>
                                    <h6 id="products_total_display" style="margin:0; font-size:14px; font-weight:600; color:#2E7D32;">0.00</h6>
                                </li>
                                <li class="order-charge-row" style="display:none; justify-content:space-between; margin-bottom:12px;" id="tax_row">
                                    <h5 style="margin:0; font-size:14px; color:#555;">Total GST Amount</h5>
                                    <h6 id="tax_display" style="margin:0; font-size:14px; font-weight:600;">0.00</h6>
                                </li>
                                <li class="order-charge-row" style="display:none; justify-content:space-between; margin-bottom:12px;" id="discount_row">
                                    <h5 style="margin:0; font-size:14px; color:#555;">Discount Amount</h5>
                                    <h6 id="discount_display" style="margin:0; font-size:14px; font-weight:600;">0.00</h6>
                                </li>
                                <li class="order-charge-row" style="display:none; justify-content:space-between; margin-bottom:12px;" id="subtotal_row">
                                    <h5 style="margin:0; font-size:14px; color:#555;">SubTotal</h5>
                                    <h6 id="subtotal_display" style="margin:0; font-size:14px; font-weight:600;">0.00</h6>
                                </li>
                                <li class="order-charge-row" style="display:none; justify-content:space-between; margin-bottom:12px;" id="shipping_row">
                                    <h5 style="margin:0; font-size:14px; color:#555;">Shipping Cost</h5>
                                    <h6 id="shipping_display" style="margin:0; font-size:14px; font-weight:600;">0.00</h6>
                                </li>
                                <li class="order-charge-row" style="display:none; justify-content:space-between; margin-bottom:12px;" id="tds_row">
                                    <h5 style="margin:0; font-size:14px; color:#555;">TDS</h5>
                                    <h6 id="tds_display" style="margin:0; font-size:14px; font-weight:600; color:#dc3545;">- 0.00</h6>
                                </li>
                                <li class="order-charge-row" style="display:none; justify-content:space-between; margin-bottom:12px;" id="other_charges_row">
                                    <h5 style="margin:0; font-size:14px; color:#555;">Other Charges</h5>
                                    <h6 id="other_charges_display" style="margin:0; font-size:14px; font-weight:600;">0.00</h6>
                                </li>
                                <li class="total-value" style="display:flex; justify-content:space-between; margin-bottom:12px; border-top:1px dashed #ddd; padding-top:12px;">
                                    <h5 style="margin:0; font-size:16px; font-weight:700; color:#333;">Total</h5>
                                    <h6 id="total_amount_display" style="margin:0; font-size:16px; font-weight:700; color:#333;">0.00</h6>
                                </li>
                                <li style="display:flex; justify-content:space-between; margin-bottom:12px; border-top:1px dashed #ddd; padding-top:12px;">
                                    <h5 style="margin:0; font-size:14px; color:#dc3545;">Deposit/Advance</h5>
                                    <h6 id="total_deposit_display" style="margin:0; font-size:14px; font-weight:600; color:#dc3545;">- 0.00</h6>
                                </li>
                                <li style="display:flex; justify-content:space-between; margin-bottom:12px;">
                                    <h5 style="margin:0; font-size:14px; font-weight:700; color:#1f2937;">Balance Due</h5>
                                    <h6 id="total_remaining_display" style="margin:0; font-size:14px; font-weight:700; color:#1f2937;">0.00</h6>
                                </li>
                                <li style="display:flex; justify-content:space-between; margin-bottom:0; border-top:1px dashed #ddd; padding-top:12px;">
                                    <h5 style="margin:0; font-size:14px; color:#2E7D32;">Total Paid Amount</h5>
                                    <h6 id="total_paid_display" style="margin:0; font-size:14px; font-weight:600; color:#2E7D32;">0.00</h6>
                                </li>
                            </ul>
                            
                            <div class="full-return-badge mt-3" id="fullReturnBadge">
                                <i class="fas fa-check-circle"></i>
                                Full Return — Deposit will be settled on submit
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Payment section --}}
                    <div class="col-lg-6 col-12">
                        <div class="card bg-light h-100 mb-0">
                            <div class="card-body">
                                <div class="form-group mb-2">
                                    <label style="font-weight:600;">Rental Payment Method</label>
                                </div>

                                <div class="setvaluecash" id="paymentSection">
                                    <ul>
                                        <li>
                                            <a href="javascript:void(0);" class="paymentmethod active">
                                                <input type="radio" name="payment_method" value="cash" checked hidden>
                                                <i class="fas fa-money-bill-wave"></i>Cash
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <input type="radio" name="payment_method" value="debit card" hidden>
                                                <i class="fas fa-credit-card"></i>Debit
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <input type="radio" name="payment_method" value="cash+online" hidden>
                                                <i class="fas fa-money-check-alt"></i>Cash+Online
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <input type="radio" name="payment_method" value="scan" hidden>
                                                <i class="fas fa-qrcode"></i>Scan
                                            </a>
                                        </li>
                                        {{-- EMI option hidden for rental return --}}
                                        {{-- <li id="emiPaymentOption">
                                            <a href="javascript:void(0);" class="paymentmethod">
                                                <input type="radio" name="payment_method" value="emi" hidden>
                                                <i class="fas fa-calendar-alt"></i>EMI
                                            </a>
                                        </li> --}}
                                    </ul>
                                </div>

                                {{-- Paid Type Box (Cash / Debit / Scan / Cash+Online) --}}
                                <div id="posPaidTypeBox" style="display:block; margin-top:12px;">
                                    <div class="form-group mb-2" id="posPaidTypeDropdownGroup">
                                        <label for="posPaidType">Paid Type</label>
                                        <select id="posPaidType" class="form-control">
                                            <option value="fully" selected>Fully</option>
                                            <option value="partially">Partially</option>
                                        </select>
                                        <span class="error_paidtype text-danger"></span>
                                    </div>
                                    <div id="posPaidAmountFields" style="display:none;">
                                        <div class="row g-2">
                                            <div class="col-md-6 col-6">
                                                <div class="form-group mb-2">
                                                    <label for="posPaidAmount">Enter Amount</label>
                                                    <input type="number" class="form-control" id="posPaidAmount"
                                                        placeholder="0.00" min="0" step="0.01">
                                                    <span class="error_paidamount text-danger"></span>
                                                    <div class="amount-error" id="fullReturnAmountError"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="form-group">
                                                    <label for="posPendingAmount">Pending Amount</label>
                                                    <input type="number" class="form-control" id="posPendingAmount"
                                                        placeholder="0.00" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="full-return-notice" id="fullReturnNotice">
                                        <i class="fas fa-lock"></i>
                                        Full return detected — Enter Amount is locked to the full outstanding balance.
                                        Deposit of <span id="noticeDepositAmt"></span> will be auto-settled.
                                    </div>
                                </div>

                                {{-- Cash + Online split --}}
                                <div id="cashOnlineBox" style="display:none; margin-top:10px;">
                                    <div class="row g-2">
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>Cash Amount</label>
                                                <input type="number" class="form-control"
                                                    id="posCashOnlineCashAmount" placeholder="Enter Cash Amount">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>Online Amount</label>
                                                <input type="number" class="form-control"
                                                    id="posCashOnlineOnlineAmount" placeholder="Enter Online Amount"
                                                    value="0" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Bank selection --}}
                                <div id="bankSelectionBox" style="display:none; margin-top:10px;">
                                    <div class="row g-2">
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <div class="bank-label-row"><label>Select Bank</label></div>
                                                <select name="bank_id" id="bank_id" class="form-control">
                                                    <option value="">Select Bank</option>
                                                    @foreach ($banks as $bank)
                                                        <option value="{{ $bank->id }}">
                                                            {{ $bank->bank_name }} ({{ $bank->account_number }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="error_bank text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label for="posPaymentRemark">Remark</label>
                                                <input type="text" class="form-control" id="posPaymentRemark"
                                                    placeholder="Enter remark">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- EMI Box --}}
                                <div id="emiBox" style="display:none; margin-top:10px;">
                                    <div class="row g-2">
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>Down Payment (Optional)</label>
                                                <input type="number" class="form-control" id="emiDownPayment"
                                                    placeholder="₹ Amount" min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>Loan Amount</label>
                                                <input type="number" class="form-control" id="emiLoanAmount"
                                                    placeholder="Auto Calculate" min="0" step="0.01" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>EMI Tenure</label>
                                                <select class="form-control" id="emiTenure">
                                                    <option value="">Select Tenure</option>
                                                    <option value="3">3 Months</option>
                                                    <option value="6">6 Months</option>
                                                    <option value="9">9 Months</option>
                                                    <option value="12">12 Months</option>
                                                    <option value="custom">Custom</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6 d-none" id="emiCustomTenureCol">
                                            <div class="form-group">
                                                <label>Custom Tenure (Months)</label>
                                                <input type="number" class="form-control" id="emiCustomTenure"
                                                    min="1" max="120" step="1" placeholder="Enter months">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>Interest Rate (%) <small class="text-muted">Optional</small></label>
                                                <input type="number" class="form-control" id="emiInterestRate"
                                                    value="0" min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>Monthly EMI</label>
                                                <input type="number" class="form-control" id="emiMonthlyAmount"
                                                    placeholder="Auto Calculate" min="0" step="0.01" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>Aadhar Number</label>
                                                <input type="text" class="form-control" id="emiAadharNumber"
                                                    placeholder="Customer Aadhar Number">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>DO ID</label>
                                                <input type="text" class="form-control" id="emiDoId" placeholder="DO ID">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>PAN Number <small class="text-muted">Optional</small></label>
                                                <input type="text" class="form-control" id="emiPanNumber" placeholder="PAN Number">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label>Guarantor Name <small class="text-muted">Optional</small></label>
                                                <input type="text" class="form-control" id="emiGuarantorName" placeholder="Guarantor Name">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <div class="bank-label-row">
                                                    <label class="mb-0">Select Bank <small class="text-muted">Optional</small></label>
                                                </div>
                                                <select class="form-control" id="emiBankId">
                                                    <option value="">Select Bank</option>
                                                    @foreach ($banks as $bank)
                                                        <option value="{{ $bank->id }}">
                                                            {{ $bank->bank_name }} ({{ $bank->account_number }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>{{-- end card-body --}}
                        </div>{{-- end card --}}
                    </div>{{-- end col-lg-6 payment --}}
                </div>{{-- end row summary+payment --}}

                <div class="submit-bar mt-4">
                    <h4>Total Amount : <span id="submit_bar_total">0.00</span></h4>
                    <button type="button" class="btn" id="btn-submit">
                        <span class="spinner-border spinner-border-sm d-none" role="status"
                            aria-hidden="true" id="submit-spinner"></span>
                        <span id="submit-text">Submit Return &rarr;</span>
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
$(document).ready(function () {
    const authToken = localStorage.getItem("authToken");
    let globalCurrencySymbol   = '₹';
    let globalCurrencyPosition = 'left';

    let originalTotalAmount      = 0;
    let originalPaidAmount       = 0;
    let originalDepositAmount    = 0;
    let currentTotalReturnAmount = 0;
    let originalOrderId          = null;
    let hasGst                   = false;

    let orderShipping = 0;
    let orderDiscount = 0;
    let orderTds = 0;
    let orderOtherCharges = 0;

    // Per-item data: keyed by order_item_id → { soldQty, returnedQty }
    let itemMetaMap = {};

    /* ─────────────────────────────────────────────
       Currency formatter
    ───────────────────────────────────────────── */
    function formatCurrency(amount) {
        let num = parseFloat(amount || 0);
        let formatted = num.toLocaleString('en-IN', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        });
        return globalCurrencyPosition === 'left'
            ? `${globalCurrencySymbol}${formatted}`
            : `${formatted}${globalCurrencySymbol}`;
    }

    function normalizeGstDetails(rawValue) {
        if (!rawValue) return [];
        let parsedValue = rawValue;
        if (typeof parsedValue === 'string') {
            try { parsedValue = JSON.parse(parsedValue); } catch (e) { return []; }
        }
        if (typeof parsedValue === 'string') {
            try { parsedValue = JSON.parse(parsedValue); } catch (e) { return []; }
        }
        if (Array.isArray(parsedValue)) return parsedValue;
        if (parsedValue && typeof parsedValue === 'object') return Object.values(parsedValue);
        return [];
    }

    /* ─────────────────────────────────────────────
       Select2 init
    ───────────────────────────────────────────── */
    $('.select2-invoices').select2({
        placeholder: 'Select Order ID', allowClear: true, width: '100%'
    });

    /* ─────────────────────────────────────────────
       Load rental order details
    ───────────────────────────────────────────── */
    $('#order_id').on('change', function () {
        const invoiceNumber = $(this).val();
        if (!invoiceNumber) { resetForm(); return; }

        $.ajax({
            url: `/api/getRentalDetails/${invoiceNumber}`,
            type: 'GET',
            headers: { "Authorization": "Bearer " + authToken },
            success: function (data) {
                if (data.error) {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.error, confirmButtonColor: '#ff9f43' });
                    return;
                }

                globalCurrencySymbol   = data.currency_symbol;
                globalCurrencyPosition = data.currency_position;

                $('#customer_name').val(data.order.user_name);
                originalPaidAmount    = parseFloat(data.order.paid_amount)    || 0;
                originalDepositAmount = parseFloat(data.order.deposit_amount) || 0;
                originalTotalAmount   = parseFloat(data.order.total_amount)   || 0;
                originalOrderId       = data.order.id;
                
                orderShipping = parseFloat(data.order.shipping) || 0;
                orderDiscount = parseFloat(data.order.discount) || 0;
                orderTds = parseFloat(data.order.tds_amount) || 0;
                orderOtherCharges = parseFloat(data.order.other_charges) || 0;

                $('#total_paid_display').text(formatCurrency(originalPaidAmount));
                $('#total_deposit_display').text(formatCurrency(originalDepositAmount));

                // Build item meta map
                itemMetaMap = {};
                const $tbody = $('#product-table-body').empty();

                if (data.items.length === 0) {
                    $tbody.append('<tr><td colspan="8" class="text-center">No returnable products found</td></tr>');
                }

                $.each(data.items, function (index, item) {
                    const price            = parseFloat(item.price)             || 0;
                    const soldQuantity     = parseFloat(item.sold_quantity)     || 0;
                    const availableQty     = parseFloat(item.quantity)          || 0; // remaining unreturned
                    const returnedQuantity = parseFloat(item.returned_quantity) || 0;
                    const rentValue        = parseFloat(item.rent_value)        || 1;
                    const basePrice        = price; // price is already 1-unit, 1-duration base
                    const gstTotal         = parseFloat(item.product_gst_total) || 0;
                    const baseGst          = (gstTotal / rentValue) || gstTotal;
                    
                    const discountAmount   = parseFloat(item.discount_amount) || 0;
                    const discountPct      = parseFloat(item.discount_percentage) || 0;
                    const baseDiscount     = (discountAmount / rentValue) || discountAmount;
                    const gstDetails       = normalizeGstDetails(item.product_gst_details);
                    
                    if (gstTotal > 0 || gstDetails.length > 0) hasGst = true;

                    itemMetaMap[item.id] = {
                        soldQty:     soldQuantity,
                        returnedQty: returnedQuantity,
                        availableQty: availableQty
                    };

                    let dateRangeStr = '';
                    if (item.rent_start_at && item.rent_end_at) {
                        dateRangeStr = moment(item.rent_start_at).format('DD/MM hh:mm A') + ' - ' + moment(item.rent_end_at).format('DD/MM hh:mm A');
                    }

                    const row = `
                        <tr data-product-id="${item.id}" data-item-id="${item.id}" data-rent-period="${item.raw_rent_period}">
                            <td>${index + 1}</td>
                            <td>${item.product_name}</td>
                            <td>${soldQuantity}</td>
                            <td>${returnedQuantity}</td>
                            <td>
                                <input type="number" class="form-control quantity-input"
                                    value="${availableQty}" min="0" max="${availableQty}" step="1"
                                    data-base-price="${basePrice}" data-price="${basePrice * rentValue}" 
                                    data-base-gst-total="${baseGst}" data-gst-total="${gstTotal}"
                                    data-base-discount="${baseDiscount}" data-discount="${discountAmount}"
                                    data-discount-pct="${discountPct}"
                                    data-original-qty="${soldQuantity}"
                                    data-gst-details='${JSON.stringify(gstDetails)}'
                                    data-original-rent="${rentValue}" data-current-rent="${rentValue}"
                                    data-item-id="${item.id}"
                                    data-available="${availableQty}" style="width:100px;">
                            </td>
                            <td>
                                <div class="rental-datetime">
                                    <input type="text" class="form-control form-control-sm return-daterange" name="return_daterange[${item.id}]" value="${dateRangeStr}" placeholder="Select return date range" readonly style="min-width: 210px;">
                                    <small class="text-muted">${item.raw_rent_period}s : <span class="return-duration" data-item-id="${item.id}">${rentValue}</span></small>
                                </div>
                                <input type="hidden" class="return-duration-input" name="return_duration[${item.id}]" value="${rentValue}">
                                <input type="hidden" class="return-end-date" name="return_end_date[${item.id}]" value="${item.rent_end_at}">
                            </td>
                            <td class="unit-price-col">${formatCurrency(basePrice * rentValue)}</td>
                            <td class="discount-col">${formatCurrency('0.00')}</td>
                            <td class="gst-col item-gst">${formatCurrency('0.00')}</td>
                            <td class="subtotal-col">${formatCurrency(basePrice * rentValue * availableQty)}</td>
                        </tr>`;
                    $tbody.append(row);
                });
                
                if (hasGst) $('.gst-column, .gst-col').show();
                else $('.gst-column, .gst-col').hide();

                initReturnDaterangepicker();
                calculateTotals();
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Failed', text: 'Error loading rental details.', confirmButtonColor: '#ff9f43' });
            }
        });
    });

    function initReturnDaterangepicker() {
        if ($('.return-daterange').length === 0) return;
        
        $('.return-daterange').each(function() {
            if (!$(this).data('daterangepicker')) {
                let options = {
                    timePicker: true,
                    timePickerIncrement: 15,
                    locale: {
                        format: 'DD/MM hh:mm A'
                    },
                    autoUpdateInput: false,
                    drops: 'auto'
                };
                
                const existingVal = $(this).val();
                if (existingVal) {
                    const dates = existingVal.split(' - ');
                    if (dates.length === 2) {
                        options.startDate = moment(dates[0], 'DD/MM hh:mm A');
                        options.endDate = moment(dates[1], 'DD/MM hh:mm A');
                    }
                }

                $(this).daterangepicker(options);
                
                $(this).on('apply.daterangepicker', function(ev, picker) {
                    const dateText = picker.startDate.format('DD/MM hh:mm A') + ' - ' + picker.endDate.format('DD/MM hh:mm A');
                    $(this).val(dateText);
                    
                    const $row = $(this).closest('tr');
                    const periodType = $row.data('rent-period') || 'day';
                    const $durationSpan = $row.find('.return-duration');
                    const $durationInput = $row.find('.return-duration-input');
                    const $endDateInput = $row.find('.return-end-date');
                    const $qtyInput = $row.find('.quantity-input');
                    const basePrice = parseFloat($qtyInput.data('base-price')) || 0;
                    const baseGst = parseFloat($qtyInput.data('base-gst-total')) || 0;
                    const baseDiscount = parseFloat($qtyInput.data('base-discount')) || 0;
                    
                    let duration = 1;
                    const msDiff = picker.endDate.toDate() - picker.startDate.toDate();
                    const hoursDiff = msDiff / (1000 * 60 * 60);
                    
                    if (periodType === 'hour') {
                        duration = Math.max(1, Math.ceil(hoursDiff));
                    } else if (periodType === 'day') {
                        duration = Math.max(1, Math.ceil(hoursDiff / 24));
                    } else if (periodType === 'week') {
                        duration = Math.max(1, Math.floor(hoursDiff / (24 * 7)));
                    } else if (periodType === 'month') {
                        duration = Math.max(1, Math.ceil(hoursDiff / (24 * 30)));
                    } else if (periodType === 'year') {
                        duration = Math.max(1, Math.ceil(hoursDiff / (24 * 365)));
                    }
                    
                    $durationSpan.text(duration);
                    $durationInput.val(duration);
                    $endDateInput.val(picker.endDate.format('YYYY-MM-DD HH:mm:00'));
                    
                    // Update price dynamically
                    const newPrice = basePrice * duration;
                    const newGst = baseGst * duration;
                    const newDiscount = baseDiscount * duration;
                    $qtyInput.data('price', newPrice);
                    $qtyInput.data('gst-total', newGst);
                    $qtyInput.data('discount', newDiscount);
                    $qtyInput.data('current-rent', duration);
                    $row.find('.unit-price-col').text(formatCurrency(newPrice));
                    
                    const qty = parseFloat($qtyInput.val()) || 0;
                    $row.find('.subtotal-col').text(formatCurrency(newPrice * qty));
                    
                    calculateTotals();
                });
            }
        });
    }

    /* ─────────────────────────────────────────────
       Detect whether ALL items are being fully returned
    ───────────────────────────────────────────── */
    function detectFullReturn() {
        let isFullReturn = true;
        let hasAnyItem   = false;

        $('.quantity-input').each(function () {
            hasAnyItem = true;
            const entering   = parseFloat($(this).val())            || 0;
            const available  = parseFloat($(this).data('available')) || 0;
            if (entering < available) {
                isFullReturn = false;
                return false; // break
            }
        });

        return hasAnyItem && isFullReturn;
    }

    /* ─────────────────────────────────────────────
       Apply full-return UI lock / unlock
    ───────────────────────────────────────────── */
    function applyFullReturnUI(isFullReturn) {
        const method = $('input[name="payment_method"]:checked').val();

        if (isFullReturn && method !== 'emi') {
            // Force Paid Type = Fully and lock it
            $('#posPaidType').val('fully').prop('disabled', true).addClass('locked');

            // Always show the amount fields on full return so cashier sees what to collect
            $('#posPaidAmountFields').show();

            // Lock Enter Amount to the exact required value
            $('#posPaidAmount').prop('readonly', true).addClass('locked');

            $('#fullReturnNotice').addClass('show');
            $('#noticeDepositAmt').text(formatCurrency(originalDepositAmount));
            $('#fullReturnBadge').addClass('show');
            $('#fullReturnAmountError').removeClass('show');
        } else {
            $('#posPaidType').prop('disabled', false).removeClass('locked');
            $('#fullReturnNotice').removeClass('show');
            $('#fullReturnBadge').removeClass('show');
            $('#posPaidAmount').prop('readonly', false).removeClass('locked');

            // Restore normal Paid Type visibility logic
            if ($('#posPaidType').val() === 'partially') {
                $('#posPaidAmountFields').show();
            } else {
                $('#posPaidAmountFields').hide();
            }
        }
    }

    /* ─────────────────────────────────────────────
       Payment method click — single handler
    ───────────────────────────────────────────── */
    $('.paymentmethod').on('click', function () {
        $('.paymentmethod').removeClass('active');
        $(this).addClass('active');
        $(this).find('input[name="payment_method"]').prop('checked', true);

        const method = $(this).find('input[name="payment_method"]').val();

        $('#posPaidTypeBox, #cashOnlineBox, #bankSelectionBox, #emiBox').hide();

        if (method === 'cash') {
            $('#posPaidTypeBox').show();
        } else if (method === 'debit card' || method === 'scan') {
            $('#posPaidTypeBox').show();
            $('#bankSelectionBox').show();
        } else if (method === 'cash+online') {
            $('#posPaidTypeBox').show();
            $('#bankSelectionBox').show();
            $('#cashOnlineBox').show();
        } else if (method === 'emi') {
            $('#emiBox').show();
        }

        // Re-apply full-return lock for new method
        applyFullReturnUI(detectFullReturn());
        $('#posPaidType').val('fully').trigger('change');
    });

    /* ─────────────────────────────────────────────
       Paid type change
    ───────────────────────────────────────────── */
    $('#posPaidType').on('change', function () {
        if ($(this).val() === 'partially') {
            $('#posPaidAmountFields').show();
            $('#posPaidAmount').val('');
        } else {
            $('#posPaidAmountFields').hide();
        }
        calculatePendingAmount();
    });

    /* ─────────────────────────────────────────────
       Quantity input
    ───────────────────────────────────────────── */
    $(document).on('input', '.quantity-input', function () {
        let val = parseFloat($(this).val()) || 0;
        const max = parseFloat($(this).attr('max')) || 0;
        if (val < 0) val = 0;
        if (val > max) val = max;
        if (val.toString() !== $(this).val()) $(this).val(val);

        const price = parseFloat($(this).data('price'));
        $(this).closest('tr').find('.subtotal-col').text(formatCurrency(price * val));

        calculateTotals();
    });

    $('#posPaidAmount, #posCashOnlineCashAmount').on('input', function () {
        calculatePendingAmount();
    });

    /* ─────────────────────────────────────────────
       Calculate totals
    ───────────────────────────────────────────── */
    function calculateTotals() {
        let isFullReturn = detectFullReturn();
        let productsSubtotal = 0;
        let totalGST = 0;
        let totalItemDiscount = 0;

        $('.quantity-input').each(function () {
            const qty = parseFloat($(this).val()) || 0;
            const price = parseFloat($(this).data('price')) || 0;
            const originalQty = parseFloat($(this).data('original-qty')) || 1;
            const gstTotal = parseFloat($(this).data('gst-total')) || 0;
            const discountAmt = parseFloat($(this).data('discount')) || 0;
            const discountPct = parseFloat($(this).data('discount-pct')) || 0;
            
            const currentRent = parseFloat($(this).data('current-rent')) || 1;
            const originalRent = parseFloat($(this).data('original-rent')) || 1;
            
            let gstDetails = $(this).data('gst-details');
            if (typeof gstDetails === 'string') {
                try { gstDetails = JSON.parse(gstDetails); } catch(e) { gstDetails = []; }
            }

            const proRatedGST = (gstTotal / originalQty) * qty;
            const proRatedDiscount = (discountAmt / originalQty) * qty;
            
            const $row = $(this).closest('tr');
            
            // Discount Display
            const $discountCol = $row.find('.discount-col');
            if (proRatedDiscount > 0) {
                $discountCol.html(discountPct > 0 ? `${formatCurrency(proRatedDiscount)} <br><small>(${discountPct}%)</small>` : formatCurrency(proRatedDiscount));
            } else {
                $discountCol.html(formatCurrency('0.00'));
            }
            
            // GST Display
            let gstHtml = '';
            if (gstDetails && gstDetails.length) {
                $.each(gstDetails, function (key, gst) {
                    const name = gst.tax_name || key;
                    const rate = gst.tax_rate || 0;
                    const amount = parseFloat(gst.tax_amount || 0);
                    const scaledGst = (amount / originalQty) * qty * (currentRent / originalRent);
                    gstHtml += `<div><small>${name}(${rate}%): ${formatCurrency(scaledGst)}</small></div>`;
                });
            } else {
                gstHtml = proRatedGST > 0 ? formatCurrency(proRatedGST) : formatCurrency('0.00');
            }
            $row.find('.item-gst').html(gstHtml);
            
            productsSubtotal += (qty * price);
            totalGST += proRatedGST;
            totalItemDiscount += proRatedDiscount;
        });

        applyFullReturnUI(isFullReturn);
        
        $('#products_total_display').text(formatCurrency(productsSubtotal));

        let grandTotal = productsSubtotal;

        if (totalGST > 0) {
            $('#tax_row').css('display', 'flex');
            $('#tax_display').text(formatCurrency(totalGST));
            grandTotal += totalGST;
        } else {
            $('#tax_row').hide();
        }

        let sumDiscount = totalItemDiscount;
        if (isFullReturn && orderDiscount > 0) {
            sumDiscount += orderDiscount;
        }

        if (sumDiscount > 0) {
            $('#discount_row').css('display', 'flex');
            $('#discount_display').text('- ' + formatCurrency(sumDiscount));
            grandTotal -= sumDiscount;
        } else {
            $('#discount_row').hide();
        }

        $('#subtotal_row').css('display', 'flex');
        $('#subtotal_display').text(formatCurrency(grandTotal));

        if (isFullReturn) {
            if (orderShipping > 0) {
                $('#shipping_row').css('display', 'flex');
                $('#shipping_display').text(formatCurrency(orderShipping));
                grandTotal += orderShipping;
            } else {
                $('#shipping_row').hide();
            }
            
            if (orderTds > 0) {
                $('#tds_row').css('display', 'flex');
                $('#tds_display').text('- ' + formatCurrency(orderTds));
                grandTotal -= orderTds;
            } else {
                $('#tds_row').hide();
            }
            
            if (orderOtherCharges > 0) {
                $('#other_charges_row').css('display', 'flex');
                $('#other_charges_display').text(formatCurrency(orderOtherCharges));
                grandTotal += orderOtherCharges;
            } else {
                $('#other_charges_row').hide();
            }
        } else {
            $('#shipping_row, #tds_row, #other_charges_row').hide();
            if (sumDiscount === 0 && totalGST === 0) {
                $('#subtotal_row').hide();
            }
        }

        grandTotal = Math.round(grandTotal);
        currentTotalReturnAmount = grandTotal;
        $('#total_amount_display').text(formatCurrency(currentTotalReturnAmount));

        // Remaining = GrandTotal - PaidAmount
        let remainingToPay = currentTotalReturnAmount - originalPaidAmount;
        
        if (remainingToPay < 0) remainingToPay = 0;

        $('#total_remaining_display').text(formatCurrency(remainingToPay));
        $('#submit_bar_total').text(formatCurrency(remainingToPay));

        calculatePendingAmount();
    }

    /* ─────────────────────────────────────────────
       Calculate pending amount
    ───────────────────────────────────────────── */
    function calculatePendingAmount() {
        const isFullReturn = detectFullReturn();
        const method       = $('input[name="payment_method"]:checked').val();
        const paidType     = $('#posPaidType').val();

        // Effective remaining after deposit on full return
        let remaining = currentTotalReturnAmount - originalPaidAmount;
        if (remaining < 0) remaining = 0;

        let entered = 0;

        if (isFullReturn && method !== 'emi') {
            // Locked to full remaining
            entered = remaining;
            $('#posPaidAmount').val(remaining.toFixed(2));
        } else if (paidType === 'fully') {
            entered = remaining;
            $('#posPaidAmount').val(remaining.toFixed(2));
        } else {
            entered = parseFloat($('#posPaidAmount').val()) || 0;
        }

        if (method === 'cash+online') {
            let cashAmt   = parseFloat($('#posCashOnlineCashAmount').val()) || 0;
            let onlineAmt = entered - cashAmt;
            if (onlineAmt < 0) onlineAmt = 0;
            $('#posCashOnlineOnlineAmount').val(onlineAmt.toFixed(2));
        }

        let pending = remaining - entered;
        if (pending < 0) pending = 0;
        $('#posPendingAmount').val(pending.toFixed(2));
    }

    /* ─────────────────────────────────────────────
       Reset form
    ───────────────────────────────────────────── */
    function resetForm() {
        $('#customer_name').val('');
        $('#product-table-body').html('<tr><td colspan="10" class="text-center">Select an order to view products</td></tr>');
        $('#products_total_display, #total_amount_display, #total_paid_display, #total_deposit_display, #total_remaining_display, #submit_bar_total').text('0.00');
        $('.order-charge-row').hide();
        $('.gst-column').hide();
        originalOrderId          = null;
        hasGst                   = false;
        originalTotalAmount      = 0;
        originalPaidAmount       = 0;
        originalDepositAmount    = 0;
        currentTotalReturnAmount = 0;
        orderShipping = 0;
        orderDiscount = 0;
        orderTds = 0;
        orderOtherCharges = 0;
        itemMetaMap              = {};
        $('#fullReturnBadge').removeClass('show');
        $('#fullReturnNotice').removeClass('show');
        $('#fullReturnAmountError').removeClass('show');
        $('#posPaidType').prop('disabled', false).removeClass('locked').val('fully');
        $('#posPaidAmount').prop('readonly', false).removeClass('locked');
    }

    /* ─────────────────────────────────────────────
       Submit
    ───────────────────────────────────────────── */
    $('#btn-submit').on('click', function () {
        if (!originalOrderId) {
            Swal.fire({ icon: 'warning', title: 'No Order Selected', text: 'Please select an order.', confirmButtonColor: '#ff9f43' });
            return;
        }

        const paymentMethod = $('input[name="payment_method"]:checked').val();
        if (!paymentMethod) {
            Swal.fire({ icon: 'warning', title: 'Payment Method Required', text: 'Please select a payment method.', confirmButtonColor: '#ff9f43' });
            return;
        }

        const isFullReturn = detectFullReturn();

        let hasQuantity  = false;
        const productsData = [];
        $('.quantity-input').each(function () {
            const $row = $(this).closest('tr');
            const qty   = parseFloat($(this).val()) || 0;
            const price = parseFloat($(this).data('base-price')) || 0; // Send basePrice to backend
            const currentPrice = parseFloat($(this).data('price')) || 0;
            const rentDuration = parseFloat($row.find('.return-duration-input').val()) || 1;
            const rentEndDate = $row.find('.return-end-date').val() || '';
            
            if (qty > 0) hasQuantity = true;
            productsData.push({
                order_item_id: $(this).data('item-id'),
                quantity:  qty,
                price:     price,
                subtotal:  qty * currentPrice,
                rent_value: rentDuration,
                rent_end_at: rentEndDate
            });
        });

        if (!hasQuantity) {
            Swal.fire({ icon: 'warning', title: 'No Quantity', text: 'Please enter a return quantity for at least one item.', confirmButtonColor: '#ff9f43' });
            return;
        }

        // Compute required amount
        let remaining = originalTotalAmount - originalPaidAmount;
        if (remaining < 0) remaining = 0;

        const paidType = $('#posPaidType').val();
        let enteredAmount = 0;

        if (isFullReturn && paymentMethod !== 'emi') {
            enteredAmount = remaining;
        } else if (paidType === 'fully') {
            enteredAmount = remaining;
        } else {
            enteredAmount = parseFloat($('#posPaidAmount').val()) || 0;
        }
        
        // Validate: full return must have full payment
        if (isFullReturn && paymentMethod !== 'emi') {
            if (enteredAmount < remaining - 0.01) {
                $('#fullReturnAmountError')
                    .text('Full return requires full payment of ' + formatCurrency(remaining))
                    .addClass('show');
                Swal.fire({
                    icon: 'error',
                    title: 'Full Payment Required',
                    text: 'Full return requires full payment of ' + formatCurrency(remaining) + '. Please collect the complete amount.',
                    confirmButtonColor: '#ff9f43'
                });
                return;
            }
            $('#fullReturnAmountError').removeClass('show');
        }

        const formData = {
            order_id:        originalOrderId,
            payment_method:  paymentMethod,
            paid_type:       paidType,
            grand_total:     currentTotalReturnAmount,
            entered_amount:  enteredAmount,
            is_full_return:  isFullReturn ? 1 : 0,
            reviews:         $('#reviews').val(),
            products:        productsData,
            bank_id:         $('#bank_id').val(),
            remark:          $('#posPaymentRemark').val(),
            cash_amount:     $('#posCashOnlineCashAmount').val(),
            online_amount:   $('#posCashOnlineOnlineAmount').val(),
            // EMI fields
            emi_down_payment:   $('#emiDownPayment').val(),
            emi_loan_amount:    $('#emiLoanAmount').val(),
            emi_tenure:         $('#emiTenure').val(),
            emi_custom_tenure:  $('#emiCustomTenure').val(),
            emi_interest_rate:  $('#emiInterestRate').val(),
            emi_monthly_amount: $('#emiMonthlyAmount').val(),
            emi_aadhar_number:  $('#emiAadharNumber').val(),
            emi_do_id:          $('#emiDoId').val(),
            emi_pan_number:     $('#emiPanNumber').val(),
            emi_guarantor_name: $('#emiGuarantorName').val(),
            emi_bank_id:        $('#emiBankId').val(),
        };

        $('#btn-submit').prop('disabled', true);
        $('#submit-spinner').removeClass('d-none');
        $('#submit-text').text('Processing...');

        $.ajax({
            url: '/api/return_rental',
            type: 'POST',
            headers: { 'Authorization': 'Bearer ' + (localStorage.getItem('token') || authToken) },
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function (response) {
                if (response.success) {
                    const msg = response.is_full_return
                        ? 'Rental fully returned and deposit settled successfully!'
                        : 'Rental partially returned successfully!';
                    Swal.fire({
                        icon: 'success',
                        title: response.is_full_return ? 'Fully Returned' : 'Partially Returned',
                        text: msg,
                        confirmButtonColor: '#FF9F43',
                        timer: 2500,
                        showConfirmButton: true
                    }).then(function () {
                        window.location.href = "{{ route('rentalreturn.list') }}";
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message, confirmButtonColor: '#ff9f43' });
                    resetSubmitBtn();
                }
            },
            error: function (xhr) {
                let errorMsg = 'An error occurred while submitting the return.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({ icon: 'error', title: 'Submission Failed', text: errorMsg, confirmButtonColor: '#ff9f43' });
                resetSubmitBtn();
            }
        });
    });

    function resetSubmitBtn() {
        $('#btn-submit').prop('disabled', false);
        $('#submit-spinner').addClass('d-none');
        $('#submit-text').text('Submit Return →');
    }

});
</script>
@endpush
