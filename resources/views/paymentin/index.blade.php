@extends('layout.app')

@section('title', 'Payment-In')

@section('content')
    <style>
        .pi-page .card {
            border: 1px solid #edf1f5;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
        }
        .pi-page label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
        }
        .pi-balance-box {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 16px;
        }
        .pi-balance-box .item {
            flex: 1 1 140px;
            background: #f8fafc;
            border: 1px solid #edf1f5;
            border-radius: 10px;
            padding: 10px 12px;
        }
        .pi-balance-box .item small {
            display: block;
            font-size: 11px;
            color: #6b7280;
            font-weight: 600;
        }
        .pi-balance-box .item b {
            font-size: 16px;
            color: #1b2850;
        }
        .pi-settle {
            border: 1px dashed #ff9f43;
            border-radius: 10px;
            background: #fff8f0;
            padding: 12px 14px;
            margin-bottom: 16px;
        }
        .pi-settle .choices {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            font-size: 13px;
            font-weight: 600;
            color: #1b2850;
        }
        .pi-settle .choices label {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            cursor: pointer;
            color: #1b2850;
            font-size: 13px;
        }
        .pi-page .form-group { margin-bottom: 0; }
        .pi-page .form-control,
        .pi-page .select2-container .select2-selection--single {
            height: 40px !important;
            border-radius: 8px;
            border-color: #e5e7eb;
        }
        .pi-page .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }
        .pi-page .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }
        .pi-bills-wrap {
            display: none;
            border: 1px solid #edf1f5;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 16px;
        }
        .pi-bills-wrap.is-visible { display: block; }
        .pi-bills-wrap table { margin: 0; width: 100%; }
        .pi-bills-wrap thead th {
            background: #f8fafc;
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 700;
        }
        .pi-hint { font-size: 12px; color: #6b7280; margin-top: 6px; }
        @media (max-width: 767.98px) {
            .pi-bills-wrap { overflow-x: auto; }
        }
    </style>

    <div class="content pi-page">
        <div class="page-header">
            <div class="page-title">
                <h4>Payment-In</h4>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="card mb-3">
            <div class="card-body">
                <form method="POST" action="{{ route('paymentin.store') }}" id="paymentInForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label>Customer</label>
                                <select name="customer_id" id="pi_customer_id" class="form-control" required>
                                    <option value="">Select Customer</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->company_name ?: $customer->name }}{{ $customer->phone ? ' - '.$customer->phone : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="payment_date" class="form-control" value="{{ now('Asia/Kolkata')->toDateString() }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label>Received Amount</label>
                                <input type="number" name="amount" id="pi_amount" class="form-control" min="0.01" step="0.01" required placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label>Payment Mode</label>
                                <select name="payment_method" id="pi_method" class="form-control" required>
                                    <option value="cash">Cash</option>
                                    <option value="online">Online</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6" id="piBankWrap" style="display:none;">
                            <div class="form-group">
                                <label>Bank</label>
                                <select name="bank_id" id="pi_bank_id" class="form-control">
                                    <option value="">Select Bank</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->bank_name }}{{ $bank->account_number ? ' ('.$bank->account_number.')' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6">
                            <div class="form-group">
                                <label>Remarks</label>
                                <input type="text" name="remarks" class="form-control" maxlength="500" placeholder="Optional">
                            </div>
                        </div>
                    </div>

                    <div class="pi-balance-box mt-3">
                        <div class="item">
                            <small>Current balance</small>
                            <b id="piCurrentBalance">{{ $currencySymbol }}0.00</b>
                        </div>
                        <div class="item">
                            <small>Old pending bills</small>
                            <b id="piPendingTotal">{{ $currencySymbol }}0.00</b>
                        </div>
                        <div class="item">
                            <small>Will settle bills</small>
                            <b id="piSettleAmt">{{ $currencySymbol }}0.00</b>
                        </div>
                        <div class="item">
                            <small>Will add to balance</small>
                            <b id="piDepositAmt">{{ $currencySymbol }}0.00</b>
                        </div>
                        <div class="item">
                            <small>After this</small>
                            <b id="piAfterBalance">{{ $currencySymbol }}0.00</b>
                        </div>
                    </div>

                    <div class="pi-settle">
                        <div class="mb-2" style="font-size:13px;font-weight:700;color:#c2410c;">How should this money be used?</div>
                        <div class="choices">
                            <label>
                                <input type="radio" name="settle_old_bills" value="1" checked>
                                Settle old bills first
                            </label>
                            <label>
                                <input type="radio" name="settle_old_bills" value="0">
                                Do not settle bills — add to customer balance
                            </label>
                        </div>
                        <div class="pi-hint" id="piSettleHint">Oldest pending bills are paid first. Extra amount is added to customer balance.</div>
                    </div>

                    <div class="pi-bills-wrap" id="piBillsWrap">
                        <div class="px-3 py-2" style="background:#f8fafc;font-size:13px;font-weight:700;color:#1b2850;border-bottom:1px solid #edf1f5;">Pending bills</div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Bill No</th>
                                        <th>Date</th>
                                        <th>Bill Total</th>
                                        <th>Pending</th>
                                    </tr>
                                </thead>
                                <tbody id="piBillsBody">
                                    <tr><td colspan="4" class="text-center text-muted">Select a customer</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-submit">Save Payment-In</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Recent Payment-In</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Receipt</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Mode</th>
                                <th>Amount</th>
                                <th>Settled</th>
                                <th>Added to balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($receipts as $row)
                                <tr>
                                    <td>{{ $row->receipt_number }}</td>
                                    <td>{{ optional($row->payment_date)->format('d-M-Y') }}</td>
                                    <td>{{ optional($row->customer)->name }}</td>
                                    <td>{{ strtoupper($row->payment_method) }}</td>
                                    <td>{{ $currencyPosition === 'left' ? $currencySymbol : '' }}{{ number_format((float)$row->amount, 2) }}{{ $currencyPosition === 'right' ? $currencySymbol : '' }}</td>
                                    <td>{{ $currencyPosition === 'left' ? $currencySymbol : '' }}{{ number_format((float)$row->settled_amount, 2) }}{{ $currencyPosition === 'right' ? $currencySymbol : '' }}</td>
                                    <td>{{ $currencyPosition === 'left' ? $currencySymbol : '' }}{{ number_format((float)$row->deposit_amount, 2) }}{{ $currencyPosition === 'right' ? $currencySymbol : '' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted">No Payment-In yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
$(document).ready(function () {
    const currencySymbol = @json($currencySymbol);
    const currencyPosition = @json($currencyPosition);
    let currentBalance = 0;
    let pendingTotal = 0;
    let pendingOrders = [];

    function money(n) {
        const v = Number(n || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return currencyPosition === 'left' ? (currencySymbol + v) : (v + currencySymbol);
    }

    function refreshSummary() {
        const amount = Math.max(0, parseFloat($('#pi_amount').val()) || 0);
        const settle = $('input[name="settle_old_bills"]:checked').val() === '1';
        let settleAmt = 0;
        let depositAmt = amount;
        if (settle) {
            settleAmt = Math.min(amount, pendingTotal);
            depositAmt = Math.max(0, amount - settleAmt);
        }
        $('#piCurrentBalance').text(money(currentBalance));
        $('#piPendingTotal').text(money(pendingTotal));
        $('#piSettleAmt').text(money(settleAmt));
        $('#piDepositAmt').text(money(depositAmt));
        $('#piAfterBalance').text(money(currentBalance + depositAmt));
        $('#piSettleHint').text(settle
            ? 'Oldest pending bills are paid first. Extra amount is added to customer balance.'
            : 'Full amount is added to customer balance. Old bills stay pending.');
        $('#piBillsWrap').toggleClass('is-visible', pendingOrders.length > 0);
    }

    function renderBills() {
        const $body = $('#piBillsBody').empty();
        if (!pendingOrders.length) {
            $body.append('<tr><td colspan="4" class="text-center text-muted">No pending bills</td></tr>');
            return;
        }
        pendingOrders.forEach(function (row) {
            $body.append(
                '<tr>' +
                    '<td>' + (row.order_number || '-') + '</td>' +
                    '<td>' + (row.order_date || '-') + '</td>' +
                    '<td>' + money(row.total_amount) + '</td>' +
                    '<td>' + money(row.remaining_amount) + '</td>' +
                '</tr>'
            );
        });
    }

    function loadCustomer(id) {
        currentBalance = 0;
        pendingTotal = 0;
        pendingOrders = [];
        if (!id) {
            renderBills();
            refreshSummary();
            return;
        }
        $.get(@json(url('/payment-in/customer')) + '/' + encodeURIComponent(id), function (res) {
            if (!res || !res.status) return;
            currentBalance = parseFloat(res.balance) || 0;
            pendingTotal = parseFloat(res.pending_total) || 0;
            pendingOrders = res.orders || [];
            renderBills();
            refreshSummary();
        });
    }

    $('#pi_customer_id').on('change select2:select', function () {
        loadCustomer($(this).val());
    });

    $('#pi_amount').on('input change', refreshSummary);
    $('input[name="settle_old_bills"]').on('change', refreshSummary);
    $('#pi_method').on('change', function () {
        $('#piBankWrap').toggle($(this).val() === 'online');
    });

    if ($.fn.select2) {
        $('#pi_customer_id').select2({
            placeholder: 'Select Customer',
            width: '100%',
            allowClear: true
        });
    }
    refreshSummary();
});
</script>
@endpush
