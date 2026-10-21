<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Print Barcode - {{ $product->name }}</title>
    @php
        $labelWidthMm = $labelWidthMm ?? 35;
        $labelHeightMm = $labelHeightMm ?? 25;
        $labelCols = max(1, (int) ($labelCols ?? 3));
        $pageWidthMm = $pageWidthMm ?? ($labelWidthMm * $labelCols);
        $pageHeightMm = $pageHeightMm ?? $labelHeightMm;
        $isTallLabel = $isTallLabel ?? false;
        $barcodes = array_values($barcodes ?? []);
        $rowImages = array_values($rowImages ?? []);
        $printer = strtolower((string) ($printer ?? 'tvs'));
        $labelFormat = strtolower((string) ($labelFormat ?? 'classic'));
        if ($labelFormat !== 'split') {
            $labelFormat = 'classic';
        }
        $isTsc = $printer === 'tsc';
        $useTscImagePrint = $isTsc && $labelFormat !== 'split' && count($rowImages) > 0;
        $autoPrint = $autoPrint ?? true;
        $isMobilePrint = $isMobilePrint ?? false;
    @endphp
    <style>
        @page {
            size: {{ $pageWidthMm }}mm {{ $pageHeightMm }}mm;
            margin: 0;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            border: 0 !important;
            outline: 0 !important;
            box-shadow: none !important;
            -webkit-font-smoothing: none;
            -moz-osx-font-smoothing: unset;
            font-smooth: never;
            text-rendering: geometricPrecision;
        }
        html, body {
            background: #fff;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            width: {{ $pageWidthMm }}mm;
            margin: 0;
            padding: 0;
        }
        .print-help {
            font-family: Arial, Helvetica, sans-serif;
            background: #fff3cd;
            padding: 10px 14px;
            font-size: 13px;
            line-height: 1.45;
            -webkit-font-smoothing: auto;
            width: auto;
        }
        .print-help button {
            background: #ff9f43;
            color: #fff;
            border: 0;
            border-radius: 4px;
            padding: 6px 12px;
            margin-top: 6px;
            cursor: pointer;
        }
        .mobile-print-toolbar {
            display: none;
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 99999;
            padding: 12px 14px calc(12px + env(safe-area-inset-bottom));
            background: #fff;
            border-top: 1px solid #dee2e6;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.08);
            gap: 8px;
        }
        body.mobile-print-mode {
            padding-bottom: 88px;
        }
        body.mobile-print-mode .mobile-print-toolbar {
            display: flex;
            flex-direction: column;
        }
        body.mobile-print-mode .print-help {
            background: #e8f4fd;
            color: #0c5460;
            font-size: 12px;
        }
        .mobile-print-toolbar button,
        .mobile-print-toolbar a {
            display: block;
            width: 100%;
            text-align: center;
            padding: 14px 12px;
            font-size: 16px;
            font-weight: 700;
            border-radius: 8px;
            text-decoration: none;
            border: 0;
        }
        .mobile-print-toolbar .btn-print-now {
            background: #ff9f43;
            color: #fff;
        }
        .mobile-print-toolbar .btn-open-pdf {
            background: #092C4C;
            color: #fff;
        }
        .tsc-label-page {
            width: {{ $pageWidthMm }}mm;
            height: {{ $pageHeightMm }}mm;
            overflow: hidden;
            page-break-after: always;
            break-after: page;
            page-break-inside: avoid;
            break-inside: avoid;
            margin: 0;
            padding: 0;
        }
        .tsc-label-page:last-child {
            page-break-after: auto;
            break-after: auto;
        }
        .tsc-label-page img {
            width: {{ $pageWidthMm }}mm;
            height: {{ $pageHeightMm }}mm;
            max-width: {{ $pageWidthMm }}mm;
            max-height: {{ $pageHeightMm }}mm;
            display: block;
            margin: 0;
            padding: 0;
            object-fit: fill;
        }
        .sheet {
            width: {{ $pageWidthMm }}mm;
            margin: 0;
            padding: 0;
        }
        table.row {
            border-collapse: collapse;
            border-spacing: 0;
            table-layout: fixed;
            width: {{ $pageWidthMm }}mm;
            height: {{ $pageHeightMm }}mm;
            page-break-after: always;
            page-break-inside: avoid;
            break-after: page;
            break-inside: avoid;
        }
        table.row:last-child {
            page-break-after: auto;
            break-after: auto;
        }
        table.row tr {
            height: {{ $pageHeightMm }}mm;
        }
        td.cell {
            width: {{ $labelWidthMm }}mm;
            height: {{ $labelHeightMm }}mm;
            vertical-align: middle;
            text-align: center;
            overflow: hidden;
            padding: 0;
            color: #000;
        }
        .inner {
            width: {{ $labelWidthMm }}mm;
            height: {{ $labelHeightMm }}mm;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            text-align: center;
            /* Top pad pushes content down so thermal print does not clip company title */
            padding: {{ $isTallLabel ? '2.0mm 1.6mm 1.0mm 1.6mm' : '2.0mm 1.8mm 0.8mm 1.8mm' }};
        }
        .inner.format-classic {
            justify-content: flex-start;
            gap: 0;
        }
        .inner.format-split {
            justify-content: flex-start;
            padding: {{ $isTallLabel ? '1.1mm 2.4mm 1.0mm 1.1mm' : '0.8mm 2.6mm 0.8mm 1.0mm' }};
        }
        .stack {
            width: 100%;
            text-align: center;
            font-weight: 800;
            letter-spacing: 0.03em;
            -webkit-text-stroke: 0.25px #000;
            text-shadow: 0.2px 0 0 #000, -0.2px 0 0 #000;
        }
        .company { font-weight: 800; font-size: {{ $isTallLabel ? '9pt' : '7.2pt' }}; line-height: 1; text-align: center; max-width: 100%; overflow: hidden; }
        .format-split .company {
            font-size: {{ $isTallLabel ? '11.5pt' : '10.5pt' }};
            line-height: 1;
            letter-spacing: 0.02em;
            margin: 0;
            overflow: hidden;
        }
        .address { font-weight: 800; font-size: {{ $isTallLabel ? '7.2pt' : '5.8pt' }}; line-height: 1; margin-top: 0.15mm; text-align: center; max-width: 100%; overflow: hidden; }
        .format-split .address {
            margin-top: 0.2mm;
        }
        .format-split .company,
        .format-split .address,
        .format-split .product,
        .format-split .code {
            text-transform: uppercase;
        }
        .product { font-weight: 800; font-size: {{ $isTallLabel ? '7.6pt' : '6pt' }}; text-transform: uppercase; line-height: 1; margin-top: 0.2mm; text-align: center; }
        .code { font-weight: 800; font-size: {{ $isTallLabel ? '7.6pt' : '6pt' }}; line-height: 1; margin-top: 0.08mm; text-align: center; }
        .code:empty { display: none; }
        .price { font-weight: 800; line-height: 1; white-space: nowrap; margin: 0.12mm 0 0.1mm 0; text-align: center; }
        .price .mrp { font-size: {{ $isTallLabel ? '7.4pt' : '6pt' }}; font-weight: 800; }
        .price .rate { font-size: {{ $isTallLabel ? '8pt' : '6.4pt' }}; font-weight: 800; }
        .bc-wrap {
            width: 94%;
            height: {{ $isTallLabel ? '11mm' : '5.4mm' }};
            margin: 0.1mm auto 0.08mm auto;
            overflow: hidden;
            background: #fff;
            flex-shrink: 0;
        }
        .format-split .bc-wrap {
            width: 100%;
            height: {{ $isTallLabel ? '12mm' : '8.2mm' }};
            margin: 0.15mm 0 0.1mm 0;
            padding: 0 0.6mm;
            background: #fff;
            overflow: hidden;
        }
        .bc-wrap img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: fill;
            shape-rendering: crispEdges;
            image-rendering: pixelated;
            image-rendering: crisp-edges;
            -ms-interpolation-mode: nearest-neighbor;
        }
        .meta {
            width: 100%;
            font-weight: 700;
            font-size: {{ $isTallLabel ? '6.2pt' : '5.2pt' }};
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            padding-bottom: 0.2mm;
        }
        .v2-bottom {
            width: 100%;
            display: table;
            table-layout: fixed;
            margin-top: 0.35mm;
        }
        .v2-bc,
        .v2-prices {
            display: table-cell;
            vertical-align: middle;
        }
        .v2-bc {
            width: 62%;
            text-align: center;
            padding-right: 0.6mm;
        }
        .v2-prices {
            width: 38%;
            text-align: left;
            padding-left: 0.5mm;
            padding-right: 0.2mm;
            overflow: hidden;
        }
        .v2-bc-num {
            font-weight: 700;
            font-size: {{ $isTallLabel ? '6.2pt' : '5.2pt' }};
            line-height: 1;
            white-space: nowrap;
            overflow: hidden;
        }
        .v2-mrp,
        .v2-rate {
            font-weight: 700;
            line-height: 1.08;
            white-space: nowrap;
            overflow: hidden;
            letter-spacing: -0.15pt;
        }
        .v2-mrp { font-size: {{ $isTallLabel ? '7.4pt' : '6.1pt' }}; }
        .v2-rate { font-size: {{ $isTallLabel ? '7.6pt' : '6.3pt' }}; }
        .v2-extra {
            font-weight: 700;
            font-size: {{ $isTallLabel ? '5.6pt' : '4.8pt' }};
            line-height: 1;
            margin-top: 0.35mm;
            white-space: nowrap;
            overflow: hidden;
        }
        @if($isTsc && !$useTscImagePrint)
        body.printer-tsc.format-split .inner.format-split {
            display: block !important;
        }
        body.printer-tsc.format-split .stack {
            display: block !important;
            width: 100%;
        }
        body.printer-tsc.format-split .v2-bottom {
            display: table !important;
            width: 100% !important;
            table-layout: fixed !important;
        }
        body.printer-tsc.format-split .v2-bc,
        body.printer-tsc.format-split .v2-prices {
            display: table-cell !important;
            vertical-align: middle !important;
        }
        @endif
        @media print {
            .print-help,
            .mobile-print-toolbar { display: none !important; }
            html, body, .sheet {
                width: {{ $pageWidthMm }}mm !important;
                height: {{ $pageHeightMm }}mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff;
            }
            @if($useTscImagePrint)
            html, body {
                width: {{ $pageWidthMm }}mm !important;
                height: auto !important;
            }
            .tsc-label-page {
                width: {{ $pageWidthMm }}mm !important;
                height: {{ $pageHeightMm }}mm !important;
            }
            .tsc-label-page img {
                width: {{ $pageWidthMm }}mm !important;
                height: {{ $pageHeightMm }}mm !important;
            }
            @endif
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color: #000 !important;
            }
        }
    </style>
</head>
<body class="printer-{{ $printer }} format-{{ $labelFormat }}{{ $isMobilePrint ? ' mobile-print-mode' : '' }}">
    <div class="print-help">
        @if($isMobilePrint)
            <strong>Print from phone</strong><br>
            1. Tap the orange <strong>Print / Select Printer</strong> button at the bottom.<br>
            2. <strong>Android:</strong> choose your Bluetooth printer app (RawBT, PrinterShare, MPT, or system Print).<br>
            3. <strong>iPhone:</strong> use AirPrint or Share → printer app.<br>
            4. If no printer appears, tap <strong>Open PDF</strong> then use ⋮ → Print or Share to your printer app.<br>
            Paper: <strong>{{ $pageWidthMm }} x {{ $pageHeightMm }} mm</strong>
        @elseif($isTsc)
            <strong>TSC TE244</strong> — Label roll: <strong>{{ $labelWidthMm }} x {{ $labelHeightMm }} mm, {{ $labelCols }} columns, gap 0</strong><br>
            Paper size: <strong>{{ $pageWidthMm }} x {{ $pageHeightMm }} mm</strong> &nbsp;|&nbsp; Orientation: <strong>Landscape</strong><br>
            Scale: <strong>100%</strong> &nbsp;|&nbsp; Margins: <strong>None</strong> &nbsp;|&nbsp; Headers &amp; footers: <strong>Off</strong> (removes URL / about:blank)
        @else
            <strong>Select your label printer</strong> (not Save as PDF / Microsoft Print to PDF).<br>
            If it is missing, click <strong>Print using system dialog (Ctrl+Shift+P)</strong>.<br>
            Paper: <strong>{{ $pageWidthMm }} x {{ $pageHeightMm }} mm</strong> &nbsp;|&nbsp;
            Layout: <strong>{{ $labelWidthMm }} x {{ $labelHeightMm }} mm, {{ $labelCols }} columns, gap 0</strong> &nbsp;|&nbsp;
            Orientation: <strong>{{ $pageWidthMm > $pageHeightMm ? 'Landscape' : 'Portrait' }}</strong> &nbsp;|&nbsp;
            Scale: <strong>100% / Actual size</strong> &nbsp;|&nbsp;
            Auto-rotate: <strong>Off</strong>
        @endif
        <br>
        <button type="button" onclick="startPrint()">Print now</button>
    </div>
    @if($useTscImagePrint)
        @foreach($rowImages as $rowImage)
        <div class="tsc-label-page">
            <img src="{{ $rowImage }}" alt="barcode label row">
        </div>
        @endforeach
    @else
    <div class="sheet">
        @forelse(array_chunk($barcodes, $labelCols) as $row)
        <table class="row">
            <tr>
                @foreach($row as $bc)
                @include('product.partials.barcode_print_cell')
                @endforeach
                @for($i = count($row); $i < $labelCols; $i++)
                <td class="cell"></td>
                @endfor
            </tr>
        </table>
        @empty
        <p style="padding: 16px; font-family: Arial;">No barcode assigned.</p>
        @endforelse
    </div>
    @endif
    @if($isMobilePrint)
    <div class="mobile-print-toolbar">
        <button type="button" class="btn-print-now" onclick="startPrint()">Print / Select Printer</button>
        @if(!empty($pdfDownloadUrl))
        <a class="btn-open-pdf" href="{{ $pdfDownloadUrl }}" target="_blank" rel="noopener">Open PDF</a>
        @endif
    </div>
    @endif
    <script>
        (function () {
            var closed = false;
            function finishBarcodePrint() {
                if (closed) return;
                closed = true;
                try {
                    if (window.parent && window.parent !== window) {
                        window.parent.postMessage({ type: 'barcodePrintDone' }, '*');
                        return;
                    }
                } catch (e) {}
                try { window.close(); } catch (e) {}
            }
            function startPrint() {
                var runPrint = function () {
                    try {
                        window.focus();
                        window.print();
                    } catch (e) {}
                    setTimeout(function () {
                        if (document.hasFocus()) {
                            finishBarcodePrint();
                        }
                    }, 600);
                };
                @if($useTscImagePrint)
                var images = document.querySelectorAll('.tsc-label-page img');
                if (!images.length) {
                    setTimeout(runPrint, 300);
                    return;
                }
                var pending = images.length;
                var done = function () {
                    pending--;
                    if (pending <= 0) {
                        setTimeout(runPrint, 250);
                    }
                };
                images.forEach(function (img) {
                    if (img.complete && img.naturalWidth > 0) {
                        done();
                    } else {
                        img.addEventListener('load', done);
                        img.addEventListener('error', done);
                    }
                });
                setTimeout(runPrint, 4000);
                @else
                setTimeout(runPrint, 300);
                @endif
            }
            window.addEventListener('afterprint', finishBarcodePrint);
            try { window.onafterprint = finishBarcodePrint; } catch (e) {}
            @if($autoPrint)
            window.addEventListener('load', startPrint);
            if (document.readyState === 'complete') {
                startPrint();
            }
            @endif
            window.startPrint = startPrint;
        })();
    </script>
</body>
</html>
