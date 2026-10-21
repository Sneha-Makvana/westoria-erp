<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Product Barcodes</title>
    <style>
        @page {
            size: A4;
            margin: 10mm 10mm 10mm 10mm;
        }

        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: white;
            width: 190mm;
            box-sizing: border-box;
            color: #000;
        }

        /* ── Company header section ── */
        .page-header-block {
            width: 100%;
            box-sizing: border-box;
            border: none;
            padding: 4mm;
            margin-bottom: 4mm;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
            padding: 2px 4px;
        }

        hr.divider {
            height: 2px;
            background: #ccc;
            border: none;
            margin: 5px 0 8px 0;
        }

        .section-title {
            text-align: center;
            text-transform: uppercase;
            font-size: 13px;
            font-weight: bold;
            color: #222;
        }

        /* ── 3-column barcode grid ── */
        .barcode-grid {
            width: calc(100% - 2px);
            border-collapse: collapse;
            table-layout: fixed;
        }

        .barcode-grid td {
            width: 25%;
            vertical-align: top;
            padding: 3mm;
            box-sizing: border-box;
        }

        .bc-empty {
            border: 1px solid transparent;
        }

        /* ── The Sticker Label Format ── */
        .label-box {
            padding: 2mm 1mm;
            border-radius: 10px;
            border: 1px solid #000;
            box-sizing: border-box;
            background: #fff;
            margin: 0 auto;
            overflow: hidden;
            height: 30mm;
            text-align: center;
            width: 100%;
        }
        
        .company-name { font-size: 10px; font-weight: bold; letter-spacing: 0.5px; }
        .address { font-size: 8px; margin-top: 1px; }
        .product-name { font-size: 9px; font-weight: bold; margin-top: 2px; text-transform: uppercase; }
        .label-code { font-size: 9px; font-weight: bold; margin-top: 2px; }
        .price-line { font-size: 10px; font-weight: bold; margin-top: 2px; }
        
        .label-box table { width: 100%; border-collapse: collapse; margin-top: 2px; border: none; }
        .label-box td { vertical-align: middle; padding: 0; border: none; }
        
        .barcode-container { text-align: center; padding: 0 1mm; }
        .barcode-img { max-width: 100%; width: auto; height: auto; max-height: 35px; }
        .unit-text { font-size: 14px; font-weight: bold; text-align: center; line-height: 1; }
        
        .batch-text { font-size: 9px; font-weight: bold; text-align: center; margin-top: 3px; word-wrap: break-word; }
        .ref-text { font-size: 9px; font-weight: bold; text-align: center; line-height: 1.1; margin-top: 3px; word-wrap: break-word; }
    </style>
</head>
<body>

    <!-- ── Company Header ── -->
    <!-- <div class="page-header-block">
        <table class="header-table">
            <tr>
                <td style="width: 120px;">
                    @if(!empty($settings->logo) && file_exists(storage_path('app/public/' . $settings->logo)))
                        @php
                            $logoPath = storage_path('app/public/' . $settings->logo);
                            $logoData = base64_encode(file_get_contents($logoPath));
                            $logoMime = mime_content_type($logoPath);
                        @endphp
                        <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Logo"
                             style="height:48px; width:auto;">
                    @endif
                </td>
                <td style="text-align:right; padding-left:10px;">
                    <div style="font-size:15px; font-weight:bold; text-transform:uppercase; color:#000;">
                        {{ $settings->name ?? 'POOJA SAMAN.IN' }}
                    </div>
                    <div style="font-size:10px; color:#555; margin-top:3px; text-transform:uppercase;">
                        {{ $settings->address ?? 'DURG,RISALI,RAIPUR' }}<br>
                        @if(!empty($settings->phone))PHONE: {{ $settings->phone }}@endif
                        @if(!empty($settings->email)) | EMAIL: {{ $settings->email }}@endif
                    </div>
                </td>
            </tr>
        </table>

        <hr class="divider">
        <div class="section-title">ALL PRODUCT BARCODES</div>
    </div> -->

    <!-- ── Barcode grid: 3 per row ── -->
    <table class="barcode-grid">
        @foreach($products->chunk(4) as $row)
        <tr>
            @foreach($row as $prod)
            <td>
                <div class="label-box">
                    <div class="company-name">{{ $settings->name ?? 'POOJA SAMAN.IN' }}</div>
                    <div class="address">{{ $settings->address ?? 'DURG,RISALI,RAIPUR' }}</div>
                    <div class="product-name">{{ $prod->name }}</div>
                    <div class="label-code">{{ $prod->label_code ?? '' }}</div>
                    <div class="price-line">
                        MRP-{{ isset($prod->mrp) && $prod->mrp ? number_format($prod->mrp, 0) : '' }} 
                        RATE-{{ isset($prod->price) && $prod->price ? number_format($prod->price, 0) : '' }}
                    </div>
                    
                    <table>
                        <tr>
                            <td style="width: 70%;" class="barcode-container">
                                @if(isset($prod->img))
                                    <img src="{{ $prod->img }}" alt="barcode" class="barcode-img">
                                @elseif(isset($barcodeImages) && isset($prod->id) && isset($barcodeImages[$prod->id]))
                                    <img src="{{ $barcodeImages[$prod->id] }}" alt="barcode" class="barcode-img">
                                @endif
                            </td>
                            <td style="width: 30%;">
                                <div class="unit-text">
                                    {{ $prod->specific_qty ?? '' }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="barcode-container">
                                <div class="batch-text">{{ $prod->barcode_display ?? $prod->barcode ?? '' }}</div>
                            </td>
                            <td>
                                <div class="ref-text">
                                    <div>{{ $prod->batch_no ?? '' }}</div>
                                    <div>{{ $prod->label_reference ?? '' }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            @endforeach
            
            {{-- Fill empty cells if row has fewer than 4 --}}
            @for($i = $row->count(); $i < 4; $i++)
            <td class="bc-empty"></td>
            @endfor
        </tr>
        @endforeach
    </table>

</body>
</html>
