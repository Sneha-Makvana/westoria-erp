@php
    $hideMrpRate = !empty($bc['hide_mrp_rate']);
    if ($hideMrpRate) {
        $mrpVal = '';
        $rateVal = '';
    } else {
        $mrpVal = (isset($bc['mrp']) && $bc['mrp'] !== '') ? \App\Models\Product::formatLabelAmount($bc['mrp']) : ($product->mrp ? \App\Models\Product::formatLabelAmount($product->mrp) : '');
        $rateVal = (isset($bc['price']) && $bc['price'] !== '') ? \App\Models\Product::formatLabelAmount($bc['price']) : ($product->price ? \App\Models\Product::formatLabelAmount($product->price) : '');
    }
    // Always show MRP-/RATE- text; empty values leave write-in blanks.
    $blankPad = '______';
    $mrpLine = 'MRP-' . ($mrpVal !== '' ? $mrpVal : $blankPad);
    $rateLine = 'RATE-' . ($rateVal !== '' ? $rateVal : $blankPad);
    $batchNo = trim((string) ($bc['batch_no'] ?? $product->batch_no ?? ''));
    $showWholesaleOnBarcode = (bool) ($bc['show_wholesale_on_barcode'] ?? ($showWholesaleOnBarcode ?? false));
    $labelRef = \App\Models\Product::formatBarcodeWholesaleRef(
        (string) ($bc['label_reference'] ?? $product->label_reference ?? ''),
        $showWholesaleOnBarcode
    );
    $qty = trim((string) ($bc['specific_qty'] ?? ''));
    $companyName = trim((string) ($settings->name ?? 'POOJA SAMAN.IN'));
    $address = trim((string) ($settings->address ?? 'DURG,RISALI,RAIPUR'));
    $address = preg_replace('/\s*,\s*/', ', ', preg_replace('/\s+/', ' ', $address));
    $productName = trim((string) ($bc['name'] ?? $product->name));
    $labelCode = trim((string) ($bc['label_code'] ?? $product->label_code));
    $codeVal = (string) ($bc['barcode_display'] ?? $bc['barcode'] ?? '');
    $sideBits = array_values(array_filter([$qty, $batchNo, $labelRef], fn ($v) => $v !== ''));
    $splitFooter = $codeVal . (count($sideBits) ? '  ' . implode('  ', $sideBits) : '');
    $barcodeSrc = '';
    if (!empty($bc['svg']) && is_string($bc['svg'])) {
        $barcodeSrc = $bc['svg'];
    } elseif (!empty($bc['img']) && is_string($bc['img'])) {
        $barcodeSrc = $bc['img'];
    }
    $isSplit = ($labelFormat ?? 'classic') === 'split';
    $includeCompanyTitle = isset($includeCompanyTitle) ? (bool) $includeCompanyTitle : true;
@endphp
<td class="cell">
    @if($isSplit)
    <div class="inner format-split">
        <div class="stack">
            @if($includeCompanyTitle)
            <div class="company"><span>{{ $companyName }}</span></div>
            <div class="address"><span>{{ $address }}</span></div>
            @endif
            <div class="product">{{ $productName }}</div>
            @if($labelCode !== '')
            <div class="code">{{ $labelCode }}</div>
            @endif
        </div>
        <div class="v2-bottom">
            <div class="v2-bc">
                <div class="bc-wrap">
                    @if($barcodeSrc !== '')
                        <img src="{{ $barcodeSrc }}" alt="{{ $codeVal }}">
                    @endif
                </div>
                <div class="v2-bc-num">{{ $splitFooter }}</div>
            </div>
            <div class="v2-prices">
                <div class="v2-mrp">{{ $mrpLine }}</div>
                <div class="v2-rate">{{ $rateLine }}</div>
            </div>
        </div>
    </div>
    @else
    <div class="inner format-classic">
        <div class="stack">
            @if($includeCompanyTitle)
            <div class="company"><span>{{ $companyName }}</span></div>
            <div class="address"><span>{{ $address }}</span></div>
            @endif
            <div class="product">{{ $productName }}</div>
            @if($labelCode !== '')
            <div class="code">{{ $labelCode }}</div>
            @endif
            <div class="price"><span class="mrp">{{ $mrpLine }}</span>&nbsp;&nbsp;<span class="rate">{{ $rateLine }}</span></div>
            <div class="bc-wrap">
                @if($barcodeSrc !== '')
                    <img src="{{ $barcodeSrc }}" alt="{{ $codeVal }}">
                @endif
            </div>
            <div class="meta">{{ $codeVal }}@if(count($sideBits)){{ '  ' . implode('  ', $sideBits) }}@endif</div>
        </div>
    </div>
    @endif
</td>
