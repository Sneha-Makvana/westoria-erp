<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rental Return PDF</title>
    <style>
        @page {
            size: A4;
            margin: 1mm 1mm;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background: white;
        }

        .card-body {
            width: 95%;
            min-height: 283mm;
            padding: 3mm;
            margin: auto;
            box-sizing: border-box;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-size: 12px;
            border: 1px solid black;
            position: relative;
        }

        table,
        table td,
        table th {
            font-size: inherit;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
            padding: 1px 2px;
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
            overflow-wrap: break-word;
        }

        .table-bordered thead tr {
            background-color: #e9ecf0ff;
            color: #333;
        }

        .table-bordered tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .text-center { text-align: center; }

        h3, h4 {
            margin: 0 0 5px 0;
            color: #343a40;
        }

        .footer-section {
            position: absolute;
            bottom: 20px;
            left: 0;
            width: 100%;
        }

        .footer-summary-table,
        .footer-notes-table,
        .footer-signature-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            color: #000;
            page-break-inside: avoid;
        }

        .footer-summary-table {
            margin-top: 8px;
            table-layout: fixed;
        }

        .qr-details-box,
        .totals-box {
            vertical-align: top;
            background-color: #eaedf0;
            padding: 2px 5px;
        }

        .qr-details-box {
            width: 50%;
            border: 1px solid #ff9f43;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .totals-box {
            width: 50%;
            border: 1px solid #22b428;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .footer-box-title {
            display: block;
            margin-bottom: 4px;
            text-transform: uppercase;
            font-size: 12px;
        }

        .totals-details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            color: inherit;
            table-layout: fixed;
        }

        .totals-details-table td {
            padding: 0 0 1px 0;
            vertical-align: top;
        }

        .totals-details-table td:last-child {
            text-align: right;
        }

        .footer-notes-table td,
        .footer-signature-table td {
            border: 2px solid #dee2e6;
            padding: 4px 10px;
            vertical-align: top;
        }

        .signature-box {
            text-align: right;
        }

        .invoice-label-nowrap {
            white-space: nowrap;
            word-break: keep-all;
            width: 58px;
        }
    </style>
</head>

<body>
    @php
        $setting = collect($setting)->isEmpty() ? (object)[
            'name' => 'Fablead Developer & Technolab',
            'email' => 'info@gmail.com',
            'phone' => '1234567890',
            'address' => 'Adajan Surat',
            'logo' => 'admin/assets/img/logo-image.jpg',
            'currency_symbol' => '₹',
            'currency_position' => 'left',
        ] : $setting;

        $sym = $setting->currency_symbol ?? '₹';

        $logoPath = null;
        if (!empty($setting->logo)) {
            $normalizedLogo = ltrim($setting->logo, '/\\');
            $candidates = [
                public_path($normalizedLogo),
                storage_path('app/public/' . $normalizedLogo),
            ];
            foreach ($candidates as $c) {
                if (file_exists($c)) { $logoPath = $c; break; }
            }
        }
    @endphp

    <div class="card-body">
        @if ($logoPath)
            @php
                $logoData   = base64_encode(file_get_contents($logoPath));
                $logoMime   = mime_content_type($logoPath) ?: 'image/png';
                $logoBase64 = "data:{$logoMime};base64,{$logoData}";
            @endphp
            <div style="position:absolute;top:35%;left:0;right:0;text-align:center;z-index:-1;opacity:0.1;">
                <img src="{{ $logoBase64 }}" style="width:70%;max-width:800px;height:auto;margin:0 auto;">
            </div>
        @endif

        {{-- ===== HEADER ===== --}}
        <table style="width:100%;margin-bottom:5px;border-collapse:collapse;">
            <tr>
                <td style="width:150px;vertical-align:top;">
                    @if($logoPath)
                        <img src="{{ $logoBase64 }}" alt="Logo" style="height:60px;width:auto;">
                    @endif
                </td>
                <td style="vertical-align:middle;padding-left:10px;text-align:right;">
                    <h3 style="margin:0;text-transform:uppercase;">{{ $setting->name ?? '' }}</h3>
                    <small style="text-transform:uppercase;">
                        {{ $setting->address ?? '' }}<br>
                        Phone: {{ $setting->phone ?? '' }} |
                        Email: <span style="text-transform:none;">{{ $setting->email ?? '' }}</span>
                    </small>
                </td>
            </tr>
        </table>

        <hr style="height:2px;background-color:#d7cdcd;border:none;margin-top:0;margin-bottom:10px;">

        {{-- ===== INFO BOXES ===== --}}
        <table style="width:100%;border-collapse:collapse;font-size:10px;margin-bottom:5px;">
            <tr>
                {{-- Customer --}}
                <td style="width:33%;position:relative;padding:5px 8px;vertical-align:top;background-color:#eaedf0;border-right:1px solid #ff9f43;">
                    <strong style="text-transform:uppercase;display:block;margin-bottom:0.5rem;">Customer Details:</strong>
                    <table style="width:100%;border-collapse:collapse;font-size:10px;color:inherit;">
                        <tr>
                            <td style="padding:0 0 5px 0;">Name :</td>
                            <td style="text-align:right;padding:0 0 5px 0;">{{ $customer['name'] ?? 'walk-in-customer' }}</td>
                        </tr>
                        @if(!empty($customer['phone']))
                        <tr>
                            <td style="padding:0 0 5px 0;">Phone :</td>
                            <td style="text-align:right;padding:0 0 5px 0;">{{ $customer['phone'] }}</td>
                        </tr>
                        @endif
                        @if(!empty($customer['email']))
                        <tr>
                            <td style="padding:0 0 5px 0;">Email :</td>
                            <td style="text-align:right;padding:0 0 5px 0;">{{ $customer['email'] }}</td>
                        </tr>
                        @endif
                    </table>
                </td>

                {{-- Company --}}
                <td style="width:33%;position:relative;padding:5px 8px;vertical-align:top;background-color:#eaedf0;border-right:1px solid #ff9f43;">
                    <strong style="text-transform:uppercase;display:block;margin-bottom:0.5rem;">Company Details:</strong>
                    <table style="width:100%;border-collapse:collapse;font-size:10px;color:inherit;">
                        @if(!empty($setting->name))
                        <tr>
                            <td style="padding:0 0 5px 0;">Name :</td>
                            <td style="text-align:right;padding:0 0 5px 0;">{{ $setting->name }}</td>
                        </tr>
                        @endif
                        @if(!empty($setting->email))
                        <tr>
                            <td style="padding:0 0 5px 0;">Email :</td>
                            <td style="text-align:right;padding:0 0 5px 0;">{{ $setting->email }}</td>
                        </tr>
                        @endif
                        @if(!empty($setting->phone))
                        <tr>
                            <td style="padding:0 0 5px 0;">Phone :</td>
                            <td style="text-align:right;padding:0 0 5px 0;">{{ $setting->phone }}</td>
                        </tr>
                        @endif
                        @if(!empty($setting->address))
                        <tr>
                            <td class="invoice-label-nowrap" style="padding:0 0 5px 0;vertical-align:top;">Address :</td>
                            <td style="text-align:right;padding:0 0 5px 0;">{{ $setting->address }}</td>
                        </tr>
                        @endif
                    </table>
                </td>

                {{-- Return Info --}}
                <td style="width:34%;padding:5px 8px;vertical-align:top;background-color:#eaedf0;">
                    <strong style="text-transform:uppercase;display:block;margin-bottom:0.5rem;">Return Details:</strong>
                    <table style="width:100%;border-collapse:collapse;font-size:10px;color:inherit;">
                        <tr>
                            <td style="padding:0 0 5px 0;">Order No :</td>
                            <td style="text-align:right;padding:0 0 5px 0;">{{ $return->order->order_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:0 0 5px 0;">Return Date :</td>
                            <td style="text-align:right;padding:0 0 5px 0;">
                                {{ $return->return_date ? \Carbon\Carbon::parse($return->return_date)->format('d/m/Y') : ($return->created_at ? $return->created_at->format('d/m/Y') : '-') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:0 0 5px 0;">Return Type :</td>
                            <td style="text-align:right;padding:0 0 5px 0;">
                                {{ $return->is_full_return ? 'Full Return' : 'Partial Return' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:0 0 5px 0;">Payment :</td>
                            <td style="text-align:right;padding:0 0 5px 0;">{{ $return->payment_method ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="text-center">
            <h4 style="text-transform:uppercase;">Rental Return</h4>
        </div>

        {{-- ===== ITEMS TABLE ===== --}}
        <table class="table-bordered"
            style="width:100%;border-collapse:collapse;font-family:DejaVu Sans,sans-serif;font-size:12px;margin:5px 0;">
            <thead>
                <tr style="background-color:#ff9f43;color:#fff;font-size:10px;white-space:nowrap;">
                    <th style="width:8%;padding:3px;text-align:center;">Sr No</th>
                    <th style="padding:3px;width:28%;text-align:left;">Product Name</th>
                    <th style="width:10%;padding:3px;text-align:center;">Qty Returned</th>
                    <th style="width:12%;padding:3px;text-align:center;">Condition</th>
                    <th style="width:14%;padding:3px;text-align:right;">Late Fee</th>
                    <th style="width:14%;padding:3px;text-align:right;">Damage Charge</th>
                    <th style="width:14%;padding:3px;text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($return->items as $item)
                @php
                    $lateFee      = (float)($item->late_fee ?? 0);
                    $damageCharge = (float)($item->damage_charge ?? 0);
                    $itemPrice    = (float)($item->orderItem->price ?? ($item->product->price ?? 0));
                    $itemSubtotal = $itemPrice * (float)($item->qty_returned ?? 0);
                @endphp
                <tr style="font-size:10px;">
                    <td style="text-align:center;padding:3px;">{{ $loop->iteration }}</td>
                    <td style="padding:3px;text-align:left;">
                        {{ $item->product->name ?? ($item->orderItem->product->name ?? 'N/A') }}
                    </td>
                    <td style="padding:3px;text-align:center;">{{ number_format($item->qty_returned, 2) }}</td>
                    <td style="padding:3px;text-align:center;">{{ ucfirst($item->condition ?? 'good') }}</td>
                    <td style="padding:3px;text-align:right;">{{ $sym }}{{ number_format($lateFee, 2) }}</td>
                    <td style="padding:3px;text-align:right;">{{ $sym }}{{ number_format($damageCharge, 2) }}</td>
                    <td style="padding:3px;text-align:right;">{{ $sym }}{{ number_format($itemSubtotal, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:4px;">No items found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @php
            function rentalConvertNumberToWords($number) {
                $hyphen = '-'; $conjunction = ' and '; $negative = 'negative ';
                $dictionary = [
                    0=>'zero',1=>'one',2=>'two',3=>'three',4=>'four',5=>'five',6=>'six',
                    7=>'seven',8=>'eight',9=>'nine',10=>'ten',11=>'eleven',12=>'twelve',
                    13=>'thirteen',14=>'fourteen',15=>'fifteen',16=>'sixteen',17=>'seventeen',
                    18=>'eighteen',19=>'nineteen',20=>'twenty',30=>'thirty',40=>'forty',
                    50=>'fifty',60=>'sixty',70=>'seventy',80=>'eighty',90=>'ninety',
                ];
                if (!is_numeric($number)) return false;
                if ($number < 0) return $negative . rentalConvertNumberToWords(abs($number));
                $string = '';
                if ($number < 21) { $string = $dictionary[$number]; }
                elseif ($number < 100) {
                    $tens = ((int)($number / 10)) * 10; $units = $number % 10;
                    $string = $dictionary[$tens]; if ($units) $string .= $hyphen . $dictionary[$units];
                } elseif ($number < 1000) {
                    $hundreds = (int)($number / 100); $remainder = $number % 100;
                    $string = $dictionary[$hundreds] . ' hundred';
                    if ($remainder) $string .= $conjunction . rentalConvertNumberToWords($remainder);
                } else {
                    $baseUnits = [10000000=>'crore',100000=>'lakh',1000=>'thousand'];
                    foreach ($baseUnits as $divisor => $label) {
                        if ($number >= $divisor) {
                            $units = (int)($number / $divisor); $remainder = $number % $divisor;
                            $string = rentalConvertNumberToWords($units) . ' ' . $label;
                            if ($remainder) $string .= $conjunction . rentalConvertNumberToWords($remainder);
                            break;
                        }
                    }
                }
                return $string;
            }

            $totalLate       = (float)($return->total_late_fee ?? 0);
            $totalDamage     = (float)($return->total_damage_charge ?? 0);
            $settlementAmt   = (float)($return->final_settlement_amount ?? 0);
            $depositApplied  = (float)($return->deposit_amount_applied ?? 0);
            $refundAmount    = (float)($return->refund_amount ?? 0);
            $amountInWords   = ucwords(rentalConvertNumberToWords((int)$settlementAmt)) . ' Rupees';
        @endphp

        {{-- ===== FOOTER ===== --}}
        <div class="footer-section">
            <table class="footer-summary-table">
                <tr>
                    <td class="qr-details-box" style="width:50%;">
                        <strong class="footer-box-title">Remarks:</strong>
                        <span style="white-space:pre-line;font-size:11px;">{{ $return->remarks ?? 'N/A' }}</span>
                    </td>

                    <td class="totals-box" style="width:50%;">
                        <strong class="footer-box-title">Totals:</strong>
                        <table class="totals-details-table">
                            <tr>
                                <td>Total Late Fee :</td>
                                <td>{{ $sym }}{{ number_format($totalLate, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Total Damage Charge :</td>
                                <td>{{ $sym }}{{ number_format($totalDamage, 2) }}</td>
                            </tr>
                            @if($depositApplied > 0)
                            <tr>
                                <td>Deposit Applied :</td>
                                <td>- {{ $sym }}{{ number_format($depositApplied, 2) }}</td>
                            </tr>
                            @endif
                            @if($refundAmount > 0)
                            <tr>
                                <td>Refund Amount :</td>
                                <td>{{ $sym }}{{ number_format($refundAmount, 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td style="font-weight:bold;">Final Settlement :</td>
                                <td style="font-weight:bold;">{{ $sym }}{{ number_format($settlementAmt, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table class="footer-notes-table">
                <tr>
                    <td style="width:60%;font-size:11px;">
                        <strong>{{ $amountInWords }} Only</strong>
                    </td>
                    <td style="width:40%;text-align:right;background-color:#ff9f43;color:#fff;">
                        <strong>Grand Total : {{ $sym }}{{ number_format($settlementAmt, 2) }}</strong>
                    </td>
                </tr>
            </table>

            <table class="footer-signature-table">
                <tr>
                    <td class="signature-box" style="width:100%;">
                        <p style="margin:0;">For, {{ $setting->name ?? 'Auto Care' }}</p>
                        <br><br><br>
                        <p style="margin:0;">(Authorized Signatory)</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
