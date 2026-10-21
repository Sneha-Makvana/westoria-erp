{{-- Footer extras driven by invoice customization --}}
@php
    $invFooterTotal = (float) ($grandTotal ?? ($sales->total_amount ?? 0));
    $invTaxRows = [];
    if (!empty($inv['show_tax_details']) && !empty($orderItems)) {
        foreach ($orderItems as $__it) {
            $__gst = $__it->product_gst_details ?? null;
            if (is_string($__gst)) {
                $__gst = json_decode($__gst, true);
                if (is_string($__gst)) {
                    $__gst = json_decode($__gst, true);
                }
            }
            if (is_array($__gst) && isset($__gst['tax_name'])) {
                $__gst = [$__gst];
            }
            if (!is_array($__gst) || empty($__gst)) {
                continue;
            }
            $__line = (float) ($__it->price ?? 0) * (float) ($__it->quantity ?? 0);
            foreach ($__gst as $__g) {
                $__rate = (float) ($__g['tax_rate'] ?? 0);
                if ($__rate <= 0) {
                    continue;
                }
                $__key = ($__g['tax_name'] ?? 'GST') . '|' . $__rate;
                if (!isset($invTaxRows[$__key])) {
                    $invTaxRows[$__key] = ['name' => $__g['tax_name'] ?? 'GST', 'rate' => $__rate, 'taxable' => 0, 'amount' => 0];
                }
                $invTaxRows[$__key]['taxable'] += $__line;
                $invTaxRows[$__key]['amount'] += round($__line * $__rate / 100, 2);
            }
        }
    }
@endphp

@if (!empty($invTaxRows))
    <table class="inv-tax-table">
        <thead>
            <tr><th>Tax</th><th>Taxable</th><th>Amt</th></tr>
        </thead>
        <tbody>
            @foreach ($invTaxRows as $__t)
                <tr>
                    <td>{{ $__t['name'] }} {{ rtrim(rtrim(number_format($__t['rate'], 2), '0'), '.') }}%</td>
                    <td>{{ $invMoney($__t['taxable']) }}</td>
                    <td>{{ $invMoney($__t['amount']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if (!empty($inv['show_amount_words']) && $invFooterTotal > 0)
    <div class="inv-words">
        <span class="bold">Amount in words:</span>
        {{ invoice_amount_words(round($invFooterTotal), $invWordsFormat) }}
    </div>
@endif

@if (!empty($inv['show_remarks']) && trim((string) ($sales->remarks ?? '')) !== '')
    <div class="inv-words">
        <span class="bold">Remarks:</span>
        <span style="white-space: pre-line;">{{ $sales->remarks }}</span>
    </div>
@endif
@if (!empty($inv['show_savings']) && $invSavingsAmount > 0)
    <div class="inv-savings">
        {{ $inv['savings_label'] ?? 'You Saved' }}
        Rs {{ $invMoney($invSavingsAmount) }}
        @if ($invSavingsPct > 0)
            ({{ rtrim(rtrim(number_format($invSavingsPct, 1), '0'), '.') }}%)
        @endif
        on this purchase
    </div>
@endif

@if (!empty($inv['show_payment_mode']) && $paymentModeLabel !== '')
    <div class="text-center bold" style="margin: 4px 0; font-size: {{ max(7, $fontPx) }}px;">
        Payment Mode: {{ $paymentModeLabel }}
        @if (!empty($paidAmount))
            : Rs {{ $invMoney($paidAmount) }}
        @endif
    </div>
@endif

@if (!empty($inv['show_customer_outstanding']) && isset($pendingAmount) && (float) $pendingAmount > 0)
    <div class="text-center bold" style="margin: 3px 0;">
        Outstanding: Rs {{ $invMoney($pendingAmount) }}
    </div>
@endif

@if (!empty($inv['show_upi_qr']) && !empty($qrBase64))
    <div class="inv-qr">
        <div style="margin-bottom: 2px;">Scan QR to Pay Bill:</div>
        <img src="{{ $qrBase64 }}" alt="UPI QR">
    </div>
@endif

@if (!empty($invoiceBarcodeHtml))
    <div class="inv-barcode">
        {!! $invoiceBarcodeHtml !!}
        <div style="font-size: 7px;">{{ $sales->order_number ?? '' }}</div>
    </div>
@endif

@if (!empty($inv['enable_terms']))
    @php
        $termsText = trim((string) ($setting->terms_condition_eng ?? ''));
    @endphp
    @if ($termsText !== '')
        <div class="inv-terms">
            <div class="bold">Terms &amp; Conditions:</div>
            <div>{{ $termsText }}</div>
        </div>
    @endif
@endif
