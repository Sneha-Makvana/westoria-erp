@extends('layout.app')

@section('title', 'View Credit Note')

@section('content')
    <style>
        .invoice-box {
            max-width: 100%;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
            background: #fff;
            overflow: hidden;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:last-child {
            border-top: 2px solid #eee;
            font-weight: bold;
            text-align: right;
        }

        .credit-note-title {
            color: #ff9f43;
            font-size: 32px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1;
        }

        .note-meta-row {
            display: flex;
            justify-content: space-between;
            gap: 24px;
        }

        .note-meta-left {
            width: 50%;
        }

        .note-meta-right {
            width: 50%;
            text-align: right;
        }

        .note-meta-right table {
            width: auto;
            margin-left: auto;
            margin-top: 8px;
        }

        .note-meta-right table td {
            text-align: right;
            white-space: nowrap;
            padding: 3px 0 3px 12px;
        }

        .note-meta-right table td:first-child {
            padding-left: 0;
        }

        @media only screen and (max-width: 768px) {
            .invoice-box {
                padding: 16px;
                font-size: 14px;
                line-height: 1.5;
            }

            .credit-note-title {
                font-size: 38px;
                margin-bottom: 12px;
            }

            .note-meta-row {
                flex-direction: column;
                gap: 12px;
            }

            .note-meta-left,
            .note-meta-right {
                width: 100%;
            }

            .note-meta-right,
            .note-meta-right table,
            .note-meta-right table td {
                text-align: left !important;
                margin-left: 0;
            }

            .invoice-box > table > tbody > tr.heading {
                display: none;
            }

            .invoice-box > table > tbody > tr.item {
                display: block;
                border: 1px solid #eee;
                border-radius: 8px;
                margin-top: 12px;
                padding: 8px 10px;
            }

            .invoice-box > table > tbody > tr.item td {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 10px;
                width: 100%;
                text-align: left !important;
                border-bottom: 1px solid #f1f1f1;
                padding: 6px 0;
            }

            .invoice-box > table > tbody > tr.item td:last-child {
                border-bottom: none;
            }

            .invoice-box > table > tbody > tr.item td::before {
                content: attr(data-label);
                font-weight: 700;
                color: #333;
                white-space: nowrap;
            }

            .invoice-box > table > tbody > tr.total td:first-child {
                display: none;
            }

            .invoice-box > table > tbody > tr.total td:last-child {
                display: block;
                width: 100%;
                text-align: left !important;
                font-size: 24px !important;
                padding-top: 12px;
            }
        }

        .rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .rtl table {
            text-align: right;
        }

        .rtl .invoice-box table tr.total td:last-child {
            text-align: left;
        }

        /* Dark Mode Overrides for Invoice Box */
        :root[data-theme="dark"] .invoice-box {
            background: transparent !important;
            border-color: #334155 !important;
            color: #d1d5db !important;
            box-shadow: none !important;
        }

        :root[data-theme="dark"] .invoice-box table tr.heading td {
            background: #0f172a !important;
            border-color: #334155 !important;
            color: #fff !important;
        }

        :root[data-theme="dark"] .invoice-box table tr.item td,
        :root[data-theme="dark"] .invoice-box table tr.total td:last-child {
            border-color: #334155 !important;
        }

        @media only screen and (max-width: 768px) {
            :root[data-theme="dark"] .invoice-box > table > tbody > tr.item td::before {
                color: #d1d5db !important;
            }
        }
    </style>

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>View Credit Note</h4>
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(27, 'view'))
                <a href="{{ route('credit-notes-items.index') }}" class="btn btn-added">Back to List</a>
                @endif
            </div>
        </div>

        <div class="invoice-box">
            <table cellpadding="0" cellspacing="0">

                <tr>
                    <td colspan="6" style="padding-bottom: 20px;">
                        <table style="width: 100%;">
                            <tr>
                                {{-- Left: Logo + Customer info --}}
                                <td style="width:50%; vertical-align: top;">
                                    <img src="{{ $compenyinfo->logo ? env('ImagePath') . 'storage/' . $compenyinfo->logo : env('ImagePath') . '/admin/assets/img/logo.png' }}"
                                        style="max-width:150px; display:block; margin-bottom: 16px;">
                                    <strong id="customerName"></strong><br>
                                    <span style="font-weight: bold;">Address: </span><span id="customerAddress"></span><br>
                                    <span style="font-weight: bold;">Phone: </span><span id="customerPhone"></span>
                                </td>

                                {{-- Right: Credit Note title + Company info + Date/Order --}}
                                <td style="width:50%; vertical-align: top; text-align: right;">
                                    <div class="credit-note-title" style="margin-bottom: 8px;">Credit Note</div>
                                    <strong>{{ $compenyinfo->name ?? '' }}</strong><br>
                                    {!! nl2br(e($compenyinfo->address ?? '')) !!}
                                    <br>
                                    <table style="width: auto; margin-left: auto; margin-top: 8px;">
                                        <tr>
                                            <td style="font-weight: bold; text-align: right; white-space: nowrap; padding: 3px 0 3px 12px;">Date:</td>
                                            <td style="text-align: right; white-space: nowrap; padding: 3px 0 3px 12px;" id="creditNoteDate"></td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; text-align: right; white-space: nowrap; padding: 3px 0 3px 12px;" id="orderLabel">Order Number:</td>
                                            <td style="text-align: right; white-space: nowrap; padding: 3px 0 3px 12px;" id="orderNumber"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="heading">
                    <td> Reason</td>
                    <td style="text-align: center;">Type</td>
                    <td style="text-align: center;">Total Amount</td>
                    <td style="text-align: center;">Settlement Amount</td>
                    <td style="text-align: center;">Final Total</td>
                </tr>

                <tr class="item">
                    <td id="reason" data-label="Reason"></td>
                    <td style="text-align: center;" id="typeName" data-label="Type"></td>
                    <td style="text-align: center;" id="totalAmt" data-label="Total Amount"></td>
                    <td style="text-align: center;" id="settlementAmt" data-label="Settlement Amount"></td>
                    <td style="text-align: center;" id="finalTotal" data-label="Final Total"></td>
                </tr>

                {{-- <tr class="total">
                    <td colspan="3"></td>
                    <td colspan="2">
                        Subtotal:
                        <span id="subtotalAmount"></span>
                    </td>
                </tr> --}}

                <tr class="total">
                    <td colspan="3"></td>
                    <td colspan="2" style="font-size: 20px;">
                        TOTAL:
                        <span id="totalAmount"></span>
                    </td>
                </tr>

            </table>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {

            let id = "{{ $id }}";
            let currencySymbol = "{{ $currencySymbol }}";
            let currencyPosition = "{{ $currencyPosition }}";
            let selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            function formatCurrency(amount) {
                let formatted = parseFloat(amount).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                return currencyPosition === 'left' ?
                    currencySymbol + formatted :
                    formatted + currencySymbol;
            }

            $.ajax({
                url: `/api/credit-note-items-api/${id}`,
                type: "GET",
                headers: {
                    "Authorization": "Bearer " + localStorage.getItem("authToken")
                },
                data: {
                    selectedSubAdminId: selectedSubAdminId
                },
                success: function(res) {

                    if (!res.status) return;

                    let d = res.data;

                    // ✅ Customer/Vendor info
                    if (d.transaction_type === 'payment') {
                        // Purchase / Vendor credit note
                        $('#customerName').text(d.purchase_invoice?.vendor?.name ?? 'N/A');
                        $('#customerPhone').text(d.purchase_invoice?.vendor?.phone ?? 'N/A');
                        $('#orderLabel').text('Bill Number:');
                        $('#orderNumber').text(d.purchase_invoice?.bill_no ?? 'N/A');

                        if (d.purchase_invoice?.vendor?.user_detail ?? d.purchase_invoice?.vendor?.userDetail) {
                            const addr = (d.purchase_invoice?.vendor?.user_detail ?? d.purchase_invoice?.vendor?.userDetail)?.address ?? 'N/A';
                            $('#customerAddress').html(addr.replace(/\n/g, '<br>'));
                        } else {
                            $('#customerAddress').text('N/A');
                        }
                    } else {
                        // Sale / Customer credit note (transaction_type === 'receipt' or legacy)
                        $('#customerName').text(d.order?.user?.name ?? 'N/A');
                        $('#customerPhone').text(d.order?.user?.phone ?? 'N/A');
                        $('#orderLabel').text('Order Number:');
                        $('#orderNumber').text(d.order?.order_number ?? 'N/A');

                        if (d.order?.user?.user_detail ?? d.order?.user?.userDetail) {
                            const addr = (d.order?.user?.user_detail ?? d.order?.user?.userDetail)?.address ?? 'N/A';
                            $('#customerAddress').html(addr.replace(/\n/g, '<br>'));
                        } else {
                            $('#customerAddress').text('N/A');
                        }
                    }

                    // ✅ Date
                    let dateObj = new Date(d.created_at);
                    let formattedDate = dateObj.toLocaleDateString('en-IN', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    $('#creditNoteDate').text(formattedDate);

                    // ✅ Credit note row
                    $('#reason').text(d.reason);
                    $('#typeName').text(d.credit_note?.type_name ?? 'N/A');
                    $('#totalAmt').text(formatCurrency(d.total_amt));
                    $('#settlementAmt').text(formatCurrency(d.settlement_amount));
                    $('#finalTotal').text(formatCurrency(d.total));

                    // ✅ Totals
                    $('#subtotalAmount').text(formatCurrency(d.total));
                    $('#totalAmount').text(formatCurrency(d.total));
                },
                error: function(err) {
                    console.error(err);
                    alert('Unable to load credit note');
                }
            });
        });
    </script>
@endpush
