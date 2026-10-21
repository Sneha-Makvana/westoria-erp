<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Challan - {{ $challanBill->challan_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .header td {
            vertical-align: top;
        }
        .logo {
            max-height: 80px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        .challan-title {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
            margin: 0 0 10px 0;
        }
        .text-right {
            text-align: right;
        }
        .details-section {
            width: 100%;
            margin-bottom: 20px;
        }
        .details-section td {
            width: 50%;
            vertical-align: top;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .details-box {
            padding-right: 20px;
        }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.items-table th, table.items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table.items-table th {
            background-color: #f5f5f5;
        }
        .footer {
            width: 100%;
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }
        .footer td {
            vertical-align: bottom;
            width: 50%;
        }
        .sign-box {
            text-align: right;
            padding-top: 50px;
        }
        .sign-line {
            display: inline-block;
            width: 200px;
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    @if(isset($setting) && $setting->logo)
                        @php
                            $logoPath = public_path('storage/' . $setting->logo);
                            if(file_exists($logoPath)) {
                                $logoData = base64_encode(file_get_contents($logoPath));
                                echo '<img src="data:image/png;base64,'.$logoData.'" class="logo">';
                            }
                        @endphp
                    @endif
                </td>
                <td class="text-right">
                    <h1 class="challan-title">DELIVERY CHALLAN</h1>
                    <div style="font-size: 16px; font-weight: bold; margin-bottom: 3px;">{{ $setting->name ?? 'Company Name' }}</div>
                    <div><strong>Email:</strong> {{ $setting->email ?? 'N/A' }}</div>
                    <div><strong>Phone:</strong> {{ $setting->phone ?? 'N/A' }}</div>
                    <div>{{ $setting->address ?? 'Company Address' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="details-section">
        <tr>
            <td class="details-box" style="width: 33%;">
                <div class="section-title">Customer Details</div>
                <div><strong>Name:</strong> {{ $challanBill->customer->name ?? 'N/A' }}</div>
                <div><strong>Phone:</strong> {{ $challanBill->customer->phone ?? 'N/A' }}</div>
                <div><strong>Email:</strong> {{ $challanBill->customer->email ?? 'N/A' }}</div>
                @if(isset($challanBill->customer->details->address))
                    <div><strong>Address:</strong> {{ $challanBill->customer->details->address }}</div>
                @endif
            </td>
            <td class="details-box text-center" style="width: 33%; padding: 0 10px;">
                <div class="section-title text-center">Delivery Details</div>
                <div><strong>Challan No:</strong> {{ $challanBill->challan_number }}</div>
                <div><strong>Date:</strong> {{ $challanBill->challan_date ? $challanBill->challan_date->format('d-m-Y') : 'N/A' }}</div>
            </td>
            <td class="details-box text-right" style="width: 33%;">
                <div class="section-title text-right">Vendor Details</div>
                <div><strong>Name:</strong> {{ $challanBill->vendor->name ?? 'N/A' }}</div>
                <div><strong>Phone:</strong> {{ $challanBill->vendor->phone ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Sr No</th>
                <th>Product Name</th>
            </tr>
        </thead>
        <tbody>
            @php
                $products = is_array($challanBill->products) ? $challanBill->products : [];
            @endphp
            
            @forelse($products as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['product_name'] ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center;">No delivery data available</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer">
        <tr>
            <td>
                @if($challanBill->remarks)
                    <div style="margin-bottom: 10px;">
                        <strong>Remarks:</strong><br>
                        {{ $challanBill->remarks }}
                    </div>
                @endif
                <div><strong>Prepared By:</strong> {{ $challanBill->prepared_by ?? 'N/A' }}</div>
                <div style="font-weight: bold; margin-top: 20px;">Goods Delivered Successfully</div>
            </td>
            <td class="sign-box">
                <span class="sign-line"></span><br>
                <strong>Authorized Signatory</strong>
            </td>
        </tr>
    </table>

</body>
</html>
