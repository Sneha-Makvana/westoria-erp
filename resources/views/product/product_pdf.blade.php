<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Products PDF</title>
    <style>
        @page {
            size: A4;
            margin: 1mm 1mm;
        }

        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            /* Set base font size here */
            margin: 0;
            padding: 0;
            background: white;
        }

        .pdf-wrapper {
            margin-top: 3mm;
            /* ensures top border is visible */
        }

        .card-body {
            width: 95%;
            min-height: 95%;
            padding: 3mm;
            margin: auto;
            box-sizing: border-box;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid black;
            font-size: 12px;
        }

        table,
        table td,
        table th {
            font-size: inherit;
            /* Make sure tables inherit the font size */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            /* margin-bottom: 20px; */
        }

        .header-table {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            margin-bottom: 30px;
        }

        .header-table td {
            padding: 10px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }

        .table-bordered {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
            padding: 4px 8px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-all; /* Ensures long continuous strings break */
        }

        .table-bordered thead tr {
            background-color: #e9ecf0ff;
            color: #333;
        }

        .table-bordered tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .table-bordered tfoot tr {
            font-weight: bold;
            background-color: #e9ecef;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        h3,
        h4 {
            margin: 0 0 10px 0;
            color: #343a40;
        }

        .invoice-title {
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 2px solid #e5e7ebff;
            display: inline-block;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .logo-container {
            position: relative;
            min-height: 50px;
            margin-bottom: 10px;
        }

        .logo-container .qr-code {
            height: 60px;
            position: absolute;
            /* top: 0;
            left: 0; */
        }

        .logo-container .company-logo {
            height: 50px;
            position: absolute;
            top: 0;
            left: 0;
        }

        .logo-container .company-details {
            text-align: center;
        }


        .signature-section img {
            height: 50px;
            margin-top: 5px;
        }

        .signature-section {
            margin-top: 50px;
            text-align: right;
        }

        .footer-section {
            width: 95%;
            position: fixed;
            bottom: 45px;
            left: 20px;
            right: 0;
        }
    </style>
</head>

<body>
    {{-- @foreach ($invoices as $invoice) --}}
    <div class="pdf-wrapper">
        <div class="card-body">

            <!-- Header with Logo + Company Info (only once) -->
            <table style="width:100%; margin-bottom: 10px; border-collapse: collapse;">
                <tr>
                    <!-- Logo -->
                    <td style="width: 150px; vertical-align: middle;">
                        @if (isset($settings->logo) && file_exists(storage_path('app/public/' . $settings->logo)))
                            @php
                                $logoPath = storage_path('app/public/' . $settings->logo);
                                $logoData = base64_encode(file_get_contents($logoPath));
                                $logoMime = mime_content_type($logoPath);
                            @endphp
                            <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Company Logo"
                                style="height: 60px; width: auto;">
                        @endif
                    </td>

                    <!-- Company Info -->
                    <td style="vertical-align: middle; text-align: right; padding-left: 15px;">
                        <h3 style="margin: 0; text-transform: uppercase; font-size: 16px; color: #000;">
                            {{ $settings->name ?? '' }}
                        </h3>
                        <small style="text-transform: uppercase; font-size: 14px;">
                            {{ $settings->address ?? '' }}<br>
                            Phone: {{ $settings->phone ?? '' }} |
                            Email: <span style="text-transform: none;">{{ $settings->email ?? '' }}</span>
                        </small>
                    </td>
                </tr>
            </table>

            <hr style="height: 2px; background-color: #d7cdcd; border: none; margin-top: 0; margin-bottom: 20px;">

            <!-- Purchase Details (Merged) -->
            <div class="text-center">
                <h4 style="text-transform: uppercase;">Product Details</h4>
            </div>
            <table class="table-bordered">
                <thead>
                    <tr>
                        <th style="width:4%; background-color:#ff9f43; color:#fff;">Sr No</th>
                        <th style="width:16%; background-color:#ff9f43; color:#fff;">Product Name</th>
                        <th style="width:8%; background-color:#ff9f43; color:#fff;">SKU</th>
                        <th style="width:10%; background-color:#ff9f43; color:#fff;">Barcode</th>
                        <th style="width:12%; background-color:#ff9f43; color:#fff;">Category</th>
                        <th style="width:10%; background-color:#ff9f43; color:#fff;">Brand</th>
                        <th style="width:5%; background-color:#ff9f43; color:#fff;">Qty</th>
                        <th style="width:5%; background-color:#ff9f43; color:#fff;">Unit</th>
                        <th style="width:10%; background-color:#ff9f43; color:#fff;">Price</th>
                        <th style="width:10%; background-color:#ff9f43; color:#fff;">MRP</th>
                        <th style="width:10%; background-color:#ff9f43; color:#fff;">Purchase Price</th>
                    </tr>
                </thead>
                <tbody>
                    @php $srNo = 1; @endphp
                    @foreach ($products as $product)
                        @if ($product->variants && $product->variants->isNotEmpty())
                            @foreach ($product->variants as $variant)
                                <tr>
                                    <td class="text-center">{{ $srNo++ }}</td>
                                    <td class="text-center">
                                        {{ !empty($product->name) ? ucfirst(strtolower($product->name)) : 'N/A' }}
                                        <br>
                                        <small class="text-muted">({{ $variant->size ?? '-' }} - {{ $variant->color ?? '-' }})</small>
                                    </td>
                                    <td class="text-center">{{ $product->SKU }}</td>
                                    <td class="text-center">{{ $variant->barcode ?? '-' }}</td>
                                    <td class="text-center">{{ $product->category ? ucfirst(strtolower($product->category->name)) : 'N/A' }}</td>
                                    <td class="text-center">{{ $product->brand ? ucfirst(strtolower($product->brand->name)) : 'N/A' }}</td>
                                    <td class="text-center">{{ $variant->qty ?? 0 }}</td>
                                    <td class="text-center">{{ $product->unit ? ucfirst(strtolower($product->unit->unit_name)) : 'N/A' }}</td>
                                    <td class="text-center">{{ number_format($variant->price ?? 0, 2) }}</td>
                                    <td class="text-center">{{ number_format($product->mrp ?? 0, 2) }}</td>
                                    <td class="text-center">{{ number_format($variant->purchase_price ?? $product->purchase_price ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center">{{ $srNo++ }}</td>
                                <td class="text-center">{{ !empty($product->name) ? ucfirst(strtolower($product->name)) : 'N/A' }}</td>
                                <td class="text-center">{{ $product->SKU }}</td>
                                <td class="text-center">{{ $product->barcode ?? '-' }}</td>
                                <td class="text-center">{{ $product->category ? ucfirst(strtolower($product->category->name)) : 'N/A' }}</td>
                                <td class="text-center">{{ $product->brand ? ucfirst(strtolower($product->brand->name)) : 'N/A' }}</td>
                                <td class="text-center">{{ $product->quantity ?? 0 }}</td>
                                <td class="text-center">{{ $product->unit ? ucfirst(strtolower($product->unit->unit_name)) : 'N/A' }}</td>
                                <td class="text-center">{{ number_format($product->price ?? 0, 2) }}</td>
                                <td class="text-center">{{ number_format($product->mrp ?? 0, 2) }}</td>
                                <td class="text-center">{{ number_format($product->purchase_price ?? 0, 2) }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
    {{-- @endforeach --}}


</body>

</html>
