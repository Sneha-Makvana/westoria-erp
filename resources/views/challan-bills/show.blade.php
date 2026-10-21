@extends('layout.app')

@section('title', 'Challan Bill Details')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Challan Bill Preview</h4>
            <h6>Delivery Challan Details</h6>
        </div>
        <div class="page-btn d-flex">
            <a href="{{ route('challan-bills.pdf', $challanBill->id) }}" class="btn btn-primary me-2">
                <i class="fas fa-download"></i> Download PDF
            </a>
            <button onclick="window.print()" class="btn btn-secondary me-2">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="{{ route('challan-bills.index') }}" class="btn btn-added">Back</a>
        </div>
    </div>
    
    <div class="card" id="print-area">
        <div class="card-body p-5">
            <!-- Header section -->
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-sm-6">
                    @if(isset($setting) && $setting->logo)
                        <img src="{{ image_path('storage/' . $setting->logo) }}" alt="Company Logo" style="max-height: 80px;">
                    @else
                        <h3>{{ $setting->name ?? '' }}</h3>
                    @endif
                </div>
                <div class="col-sm-6 text-end">
                    <h2 class="mb-1">DELIVERY CHALLAN</h2>
                    <p class="mb-0"><strong>{{ $setting->name ?? '' }}</strong> </p>
                    <p class="mb-0"><strong></strong> {{ $setting->email ?? 'N/A' }}</p>
                    <p class="mb-0"><strong></strong> {{ $setting->phone ?? 'N/A' }}</p>
                    <p class="mb-0">{{ $setting->address ?? 'Company Address' }}</p>
                </div>
            </div>

            <!-- Customer & Delivery Details -->
            <div class="row mb-4">
                <div class="col-sm-4">
                    <h5 class="border-bottom pb-1 mb-2">Customer Details</h5>
                    <p class="mb-1"><strong>Name:</strong> {{ $challanBill->customer->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $challanBill->customer->phone ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $challanBill->customer->email ?? 'N/A' }}</p>
                    @if(isset($challanBill->customer->details->address))
                        <p class="mb-1"><strong>Address:</strong> {{ $challanBill->customer->details->address }}</p>
                    @endif
                </div>
                <div class="col-sm-4">
                    <h5 class="border-bottom pb-1 mb-2">Delivery Details</h5>
                    <p class="mb-1"><strong>Challan No:</strong> {{ $challanBill->challan_number }}</p>
                    <p class="mb-1"><strong>Date:</strong> {{ $challanBill->challan_date ? $challanBill->challan_date->format('d-m-Y') : 'N/A' }}</p>
                </div>
                <div class="col-sm-4">
                <!-- <div class="col-sm-12"> -->
                    <h5 class="border-bottom pb-1 mb-2">Vendor Details</h5>
                    <p class="mb-1"><strong>Name:</strong> {{ $challanBill->vendor->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $challanBill->vendor->phone ?? 'N/A' }}</p>
            </div>

            </div>

       
            <!-- Product Table -->
            <div class="row mt-4">
                <div class="col-12">
                    <table class="table table-bordered">
                        <thead class="table-light">
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
                                    <td colspan="2" class="text-center">No delivery data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Remarks & Footer -->
            <div class="row mt-4 pt-3 border-top">
                <div class="col-sm-7">
                    @if($challanBill->remarks)
                        <div class="mb-3">
                            <p class="mb-1"><strong>Remarks:</strong></p>
                            <p>{{ $challanBill->remarks }}</p>
                        </div>
                    @endif
                    <p class="mb-1"><strong>Prepared By:</strong> {{ $challanBill->prepared_by ?? 'N/A' }}</p>
                    <p class="fw-bold mt-4">Goods Delivered Successfully</p>
                </div>
                <div class="col-sm-5 text-end pt-5">
                    <p class="mb-5 border-bottom d-inline-block px-5"></p>
                    <p class="fw-bold">Authorized Signatory</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #print-area, #print-area * {
            visibility: visible;
        }
        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .page-header {
            display: none;
        }
        .card {
            border: none;
            box-shadow: none;
        }
    }
</style>
@endsection
