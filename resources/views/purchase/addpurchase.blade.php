@extends('layout.app')

@php
    $purchaseType = $purchaseType ?? 'product';
    $editPurchaseId = $editPurchaseId ?? null;
    $isEditPurchase = !empty($editPurchaseId);
    $isRowMaterialPurchase = $purchaseType === 'row-material';
    $pageTitle = $isRowMaterialPurchase
        ? ($isEditPurchase ? 'Edit Row Material Purchase' : 'Add Row Material Purchase')
        : 'Add Purchase';
    $itemLabel = $isRowMaterialPurchase ? 'Row Material Name' : 'Product Name';
    $itemPriceLabel = $isRowMaterialPurchase ? 'Row Material Price' : 'Product Price';
    $totalItemLabel = $isRowMaterialPurchase ? 'Total Row Material Amount' : 'Total Product Amount';
    $purchaseSubmitUrl = $isRowMaterialPurchase ? '/api/row-material-purchase_order' : '/api/purchase_order';
    $purchaseRedirectUrl = $isRowMaterialPurchase ? route('purchase.row_material.lists') : '/print-purchase/';
    $itemOptions = $isRowMaterialPurchase ? ($rowMaterialsArray ?? []) : ($productsArray ?? []);
@endphp

@section('title', $pageTitle)

@section('content')

    <style>
        .gst-header {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            padding: 8px 0 0;
        }

        .custom-radio-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            color: #333;
            user-select: none;
            position: relative;
            padding-left: 24px;
            margin: 0;
            white-space: nowrap;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: color 0.2s ease;
        }

        .custom-radio-label input[type="radio"] {
            appearance: none;
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            border: 2px solid #888;
            border-radius: 50%;
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .custom-radio-label input[type="radio"]:checked {
            border-color: #0056b3;
            background-color: #0056b3;
        }

        .custom-radio-label input[type="radio"]:checked::after {
            content: "";
            display: block;
            width: 7px;
            height: 7px;
            background: white;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .custom-radio-label:hover input[type="radio"] {
            border-color: #5a50cc;
        }

        .custom-radio-label:hover {
            color: #0056b3;
        }

        .manage_btn {
            color: #fff;
            background: #FF9F43;
        }

        #addVendorBtn .vendor-add-icon {
            display: none;
        }

        .manage_btn:hover {
            color: #e6e4e4ff;
        }

        .form-row {
            border: 1px solid #fdc794;
            padding: 20px 10px 10px 10px;
            margin-bottom: 20px;
            border-radius: 10px;
            background: #fdfdfd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .add-row-btn {
            margin-top: 28px;
        }

        .row-material-hint {
            margin-top: 8px;
            font-size: 12px;
            color: #5b6670;
            line-height: 1.5;
        }

        .row-material-chip {
            display: inline-block;
            margin: 4px 6px 0 0;
            padding: 4px 10px;
            border-radius: 999px;
            background: #fff3e6;
            color: #c66a00;
            font-weight: 600;
            font-size: 11px;
        }

        .purchase-tabs {
            display: flex;
            gap: 12px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .purchase-title-row {
            display: flex;
            /* align-items: center; */
            /* justify-content: center; */
            gap: 16px;
            flex-wrap: wrap;
        }

        .purchase-type-radios {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .purchase-tab {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            border-radius: 10px;
            border: 1px solid #d9dce3;
            background: #f7f8fa;
            color: #212b36;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .purchase-tab:hover {
            color: #ff9f43;
            border-color: #ff9f43;
        }

        .purchase-tab.active {
            background: #ff9f43;
            border-color: #ff9f43;
            color: #fff;
        }

        @media screen and (max-width: 768px) {
            #addVendorBtn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 24px;
                height: 24px;
                margin-left: 6px !important;
                padding: 0 !important;
                font-size: 12px !important;
                line-height: 1;
            }

            #addVendorBtn .vendor-add-icon {
                display: inline-block;
            }

            #addVendorBtn .vendor-add-text {
                display: none;
            }

            .purchase-header {
                display: flex;
                align-items: flex-start;
                justify-content: flex-start;
                flex-wrap: wrap;
                gap: 6px;
            }

            .purchase-header .page-title h4 {
                font-size: 16px;
                margin-bottom: 0;
            }

            .purchase-title-row {
                gap: 10px;
            }

            .purchase-type-radios {
                gap: 10px;
            }

            .purchase-header .page-title {
                width: 100%;
                flex: 1 1 100%;
                min-width: 0;
                text-align: center;
            }

            .purchase-tabs {
                display: flex;
                flex-wrap: nowrap;
                gap: 6px;
                margin-top: 8px;
                width: 100%;
                max-width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                justify-content: flex-start;
            }

            .purchase-tabs::-webkit-scrollbar {
                display: none;
            }

            .purchase-tab {
                flex: 0 0 auto;
                white-space: nowrap;
                padding: 8px 6px;
                font-size: 11px;
                border-radius: 8px;
                text-align: center;
            }

            .gst-header {
                display: flex;
                align-items: center;
                width: 100%;
                padding: 0;
                gap: 8px;
                flex-wrap: nowrap;
                margin-left: 0;
                margin-top: 2px;
                justify-content: flex-end;
                flex: 0 0 auto;
            }

            .custom-radio-label {
                font-size: 12px;
                font-weight: 500;
                padding-left: 20px;
                gap: 4px;
            }

            .custom-radio-label input[type="radio"] {
                width: 14px;
                height: 14px;
            }

            .custom-radio-label input[type="radio"]:checked::after {
                width: 5px;
                height: 5px;
            }

            .add-row-btn {
                margin-top: 0;
                margin-bottom: 1rem;
            }

            .add-row {
                width: 100%;
                text-align: center;
            }

            .remove-row {
                width: 100%;
                text-align: center;
            }

            .form-group {
                margin-bottom: 10px !important;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 1024px) {
            .content {
                max-width: 768px;
                margin: 0 auto;
                padding: 20px 10px 80px;
            }

            .purchase-header {
                align-items: flex-start;
                margin-bottom: 8px;
            }

            .purchase-header .page-title {
                text-align: left;
            }

            .purchase-header .page-title h4 {
                font-size: 16px;
            }

            .purchase-tabs {
                gap: 6px;
                margin-top: 8px;
            }

            .purchase-tab {
                padding: 8px 10px;
                font-size: 11px;
                border-radius: 8px;
            }

            .gst-header {
                justify-content: flex-end;
                margin-top: 0;
            }

            .card {
                margin-bottom: 0;
            }

            .card .card-body {
                padding: 12px 10px 14px;
            }

            .form-row {
                padding: 14px 10px 10px;
                margin: 0 0 12px;
                border-radius: 8px;
                display: flex;
                flex-wrap: wrap;
            }

            .form-row .form-group {
                margin-bottom: 8px !important;
            }

            /* Row 1: Category Name + Product Name — 50% each */
            .form-row > .col-lg-2.col-sm-12.col-6:nth-child(1),
            .form-row > .col-lg-2.col-sm-12.col-6:nth-child(2) {
                flex: 0 0 50%;
                max-width: 50%;
                padding-left: 6px;
                padding-right: 6px;
            }

            /* Row 2: Product Price + Qty — 50% each */
            .form-row > .col-lg-2.col-sm-12.col-6:nth-child(3),
            .form-row > .col-lg-1.col-sm-12.col-6:nth-child(4) {
                flex: 0 0 50%;
                max-width: 50%;
                padding-left: 6px;
                padding-right: 6px;
            }

            /* Row 3: Disc% + Disc-Amt — 50% each */
            .form-row > .col-lg-1.col-sm-12.col-6:nth-child(5),
            .form-row > .col-lg-1.col-sm-12.col-6:nth-child(6) {
                flex: 0 0 50%;
                max-width: 50%;
                padding-left: 6px;
                padding-right: 6px;
            }

            /* Row 4: Total Amount — fills remaining space */
            .form-row > .col-lg-2.col-sm-12.col-12:nth-child(7) {
                flex: 1 1 auto;
                max-width: calc(100% - 80px);
                padding-left: 6px;
                padding-right: 6px;
            }

            /* +/- button — compact, auto width */
            .form-row > .col-lg-1.add-row-btn {
                flex: 0 0 auto;
                width: 80px;
                max-width: 80px;
                margin-top: 28px;
                padding-left: 6px;
                padding-right: 6px;
            }

            .add-row,
            .remove-row {
                width: 100%;
                height: 36px;
                line-height: 1;
                border-radius: 4px;
            }

            .form-row .add-row-btn {
                margin-bottom: 0;
            }

            .form-group label {
                font-size: 12px;
                margin-bottom: 4px;
            }

            .form-control,
            .select2-container--default .select2-selection--single {
                min-height: 36px;
                font-size: 12px;
            }

            .select2-container {
                width: 100% !important;
            }

            .total-order {
                margin-top: 4px;
            }
        }
        .vendor-modal-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px 16px;
        }

        .vendor-modal-grid .vendor-full-width {
            grid-column: 1 / -1;
        }

        .vendor-modal-grid .form-group {
            margin-bottom: 0 !important;
        }

        .vendor-modal-grid label {
            display: block;
            margin-bottom: 6px;
        }

        @media screen and (max-width: 767px) {
            .vendor-modal-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .vendor-modal-grid .vendor-full-width {
                grid-column: 1 / -1;
            }
        }
        .purchase-remark-block .form-group {
            margin-bottom: 0;
        }

        .purchase-remark-textarea {
            min-height: 150px;
            resize: vertical;
        }
    </style>

    <div class="content">
        <div class="page-header purchase-header">
            <div class="page-title">
                <div class="purchase-title-row">
                    <h4>{{ $pageTitle }}</h4>
                    <div class="purchase-type-radios" style="display: none;">
                        <label class="custom-radio-label">
                            <input type="radio" name="purchase_type_option" value="product"
                                data-url="{{ route('purchase.add') }}" {{ $isRowMaterialPurchase ? '' : 'checked' }} />
                            Product Purchase
                        </label>
                        <label class="custom-radio-label">
                            <input type="radio" name="purchase_type_option" value="row-material"
                                data-url="{{ route('purchase.row_material.add') }}" {{ $isRowMaterialPurchase ? 'checked' : '' }} />
                            Row Material Purchase
                        </label>
                    </div>
                </div>
                <div class="purchase-tabs">
                    <a href="{{ route('purchase.add') }}" class="purchase-tab {{ $isRowMaterialPurchase ? '' : 'active' }}">Product Purchase</a>
                    <a href="{{ route('purchase.row_material.add') }}" class="purchase-tab {{ $isRowMaterialPurchase ? 'active' : '' }}">Row Material Purchase</a>
                </div>
            </div>
            <div class="gst-header">

                <label class="custom-radio-label">
                    <input type="radio" name="gst_option" id="without_gst" value="without" checked />
                    Without GST
                </label>

                <label class="custom-radio-label">
                    <input type="radio" name="gst_option" id="with_gst" value="with" />
                    With GST
                </label>

            </div>
        </div>

        <div class="card">

            <div class="card-body">
                <div class="">
            <!-- <div class="row d-flex ">

                <div class="col-lg-6 col-sm-6 col-6">
                    <div class="form-group">
                        <label>Vendor Name
                            <button type="button" class="btn btn-sm manage_btn ms-2" id="addVendorBtn" style="padding: 3px 8px; font-size: 12px;">Add Vendor</button>
                        </label>
                        <select id="vendor_name" name="vendor_id"
                            class="form-control select2 vendor-select w-100">
                            <option value="">Select Vendor</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}" data-phone="{{ $vendor->phone }}">
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="error text-danger"></span>
                    </div>
                </div>

                <div class="col-lg-6 col-sm-6 col-6">
                    <div class="form-group">
                        <label>Bill No.</label>
                        <input type="text" name="bill_no" id="bill_no"
                            class="form-control" placeholder="Bill No">
                        <span class="error text-danger"></span>
                    </div>
                </div>

            </div> -->
            <div class="row d-flex ">
    <div class="col-lg-4 col-sm-6 col-6">
        <div class="form-group">
            <label>Vendor Name
        <button type="button" class="btn btn-sm manage_btn ms-2" id="addVendorBtn" aria-label="Add Vendor" title="Add Vendor" style="padding: 3px 8px; font-size: 12px;">
            <i class="fas fa-plus vendor-add-icon" aria-hidden="true"></i><span class="vendor-add-text">Add Vendor</span>
        </button>
    </label>
            <select id="vendor_name" name="vendor_id"
                class="form-control select2 vendor-select w-100">
                <option value="">Select Vendor</option>
                @foreach ($vendors as $vendor)
                    <option value="{{ $vendor->id }}" data-phone="{{ $vendor->phone }}">
                        {{ $vendor->name }}
                    </option>
                @endforeach
            </select>
            <span class="error text-danger"></span>
        </div>
    </div>

    <div class="col-lg-4 col-sm-6 col-6">
        <div class="form-group">
            <label>Bill No.</label>
            <input type="text" name="bill_no" id="bill_no"
                class="form-control" placeholder="Bill No">
            <span class="error text-danger"></span>
        </div>
    </div>

    <div class="col-lg-4 col-sm-6 col-6">
        <div class="form-group">
            <label>Purchase Date</label>
            <input type="datetime-local" name="purchase_date" id="purchase_date" class="form-control"
                value="{{ now()->format('Y-m-d\TH:i') }}">
            <span class="error text-danger" id="purchase_date_error"></span>
            <!-- <div class="addonset">
                <img src="{{ env('ImagePath').'admin/assets/img/icons/calendars.svg' }}" alt="img">
            </div> -->
        </div>
    </div>
</div>

                    <div class="col-lg-6 col-sm-12 d-none">
                        <div class="form-group">
                            <label>Vendor Phone</label>
                            <input type="number" id="vendor_phone" name="phone" class="form-control"
                                placeholder="Enter Phone">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                </div>
                {{-- <div class="mt-2"> --}}

                <div class="row mb-3">
                    <div class="col-md-12 position-relative">
                        <div class="d-flex flex-wrap align-items-center" style="gap: 10px; border: 1px solid #ff9f43; padding: 10px; border-radius: 5px; background-color: #fff;">
                            <div style="flex: 1 1 250px;">
                                <input type="text" id="search_product" class="form-control w-100" placeholder="Search product by name..." style="border: 1px solid #dce0e4;" autocomplete="off">
                            </div>
                            <div class="d-flex flex-wrap" style="gap: 10px; flex: 1 1 auto; width: 100%;">
                                <button type="button" id="openAddProductModalBtn" class="btn text-white flex-fill" style="flex: 1 1 140px; background-color: #ff9f43; white-space: normal; padding: 8px 10px; border-radius: 5px; font-size: 13px;">
                                    {{ $isRowMaterialPurchase ? 'Add Row Material Product' : 'Add Product' }}
                                </button>
                                <button type="button" id="openScanBarcodeModalBtn" class="btn text-white flex-fill" style="flex: 1 1 140px; background-color: #ff9f43; white-space: normal; padding: 8px 10px; border-radius: 5px; font-size: 13px;">
                                    <i class="fa-solid fa-barcode"></i> Scan Barcode
                                </button>
                            </div>
                        </div>

                        <!-- Search Results Dropdown -->
                        <div id="search_results_container" class="dropdown-menu w-100" style="display: none; position: absolute; top: 100%; left: 0; z-index: 1000; padding: 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 4px; border: 1px solid #ff9f43; margin-top: 5px;">
                            <div class="d-flex justify-content-between align-items-center p-2 border-bottom" style="background-color: #f8f9fa;">
                                <strong style="font-size: 14px; margin-left: 10px;">Products</strong>
                                <i class="fa fa-times text-muted" id="close_search_results" style="cursor: pointer; margin-right: 10px;"></i>
                            </div>
                            <div id="search_results_list" style="max-height: 300px; overflow-y: auto;">
                                <!-- Results populated by JS -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row form-row">

                    <div class="col-lg-2 col-sm-12 col-6">
                        <div class="form-group">
                            <label>Category Name</label>
                            <select id="category_name" name="category_name[]" class="form-control select2 category-select">
                                <option value="">Category Name</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" data-price="{{ $category->price }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-12 col-6">
                        <div class="form-group">
                            <label>{{ $itemLabel }}</label>
                            <select id="product_name" name="product_name[]" class="form-control select2 product-select"
                                disabled>
                                <option value="">{{ $itemLabel }}</option>
                                @foreach ($itemOptions as $product)
                                    <option value="{{ $product['id'] }}" data-price="{{ $product['price'] }}"
                                        data-category="{{ $product['category_id'] }}"
                                        data-gst-option="{{ $product['gst_option'] ?? 'without_gst' }}" data-gst='@json($product['product_gst'] ?? null)'>
                                        {{ $product['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="product-gst-info mt-1"></div>

                            <span class="error text-danger product-error"></span>
                            @unless ($isRowMaterialPurchase)
                                <div class="row-material-hint"></div>
                                <div class="purchase-rate-wrap mt-1" style="display:none;">
                                    <label class="mb-0" style="font-size:12px;">Add stock to</label>
                                    <select class="form-control purchase-rate-select" style="font-size:12px;"></select>
                                </div>
                            @endunless
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-12 col-6">
                        <div class="form-group">
                            <label>{{ $itemPriceLabel }}</label>
                            <input type="text" name="price[]" class="form-control price-input" placeholder="Enter Price"
                                min="0" oninput="this.value = this.value < 0 ? 0 : this.value" inputmode="decimal"
                                step="0.01">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-12 col-6">
                        <div class="form-group">
                            <label>Qty</label>
                            <input type="number" name="quantity[]" class="form-control quantity-input" placeholder="Qty"
                                value="1" min="0" oninput="this.value = this.value < 0 ? 0 : this.value">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-12 col-6">
                        <div class="form-group">
                            <label>Disc%</label>
                            <input type="text" inputmode="decimal" name="product_discount[]"
                                class="form-control product-discount-input" placeholder="0.00" value="0">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-12 col-6">
                        <div class="form-group">
                            <label>Disc-Amt</label>
                            <input type="text" name="product_discount_amount[]"
                                class="form-control product-discount-amount-input" placeholder="0.00" value="0"
                                min="0">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-12 col-12">
                        <div class="form-group">
                            <label>Total Amount</label>
                            <input type="text" name="total[]" class="form-control total-input" placeholder="0"
                                readonly>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-12 add-row-btn">
                        <button type="button" class="btn btn-success add-row">+</button>
                    </div>
                </div>
                <div id="form-container"></div>
                {{-- </div> --}}

                <div class="row">

                    <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Shipping</label>
                            <input type="text" name="shipping" id="shipping" class="form-control"
                                placeholder="0.00" min="0">
                            <span class="error text-danger"></span>
                        </div>
                    </div>
                    <!-- <div class="col-lg-3 col-sm-6 col-6">
                        <div class="form-group">
                            <label>Purchase Status</label>
                            <select name="status" class="form-control purchase-status-select">
                                <option value="">Choose Status</option>
                                <option value="pending">Pending</option>
                                <option value="partially">Partially</option>
                                <option value="completed">Completed</option>
                            </select>
                            <span class="error text-danger"></span>
                        </div>
                    </div> -->
                    <div class="col-sm-6 col-lg-2 col-6">
                        <div class="form-group">
                            <label>Payment Mode</label>
                            <select name="payment_mode" id="payment_mode" class="form-control payment-mode-select">
                                <option value="">Select Payment Mode</option>
                                <option value="pending">Pending</option>
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                                <option value="cashonline">Cash + Online</option>
                            </select>
                            <div class="text-danger error-payment_mode"></div>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-12 col-lg-2 col-6 d-none" id="paid_type_container">
                        <div class="form-group">
                            <label>Paid Type</label>
                            <select id="paid_type" name="paid_type" class="form-control paid-type-container">
                                <option value="">Select Paid Type</option>
                                <option value="full">Fully Paid</option>
                                <option value="partial">Partially Paid</option>
                            </select>
                            <div class="text-danger error-paid_type"></div>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-12 col-lg-2 col-6 d-none" id="cash_amount_input_container">
                        <div class="form-group">
                            <label>Cash Amount</label>
                            <input type="text" id="cash_amount_input" name="cash_amount" class="form-control"
                                placeholder="Enter Cash Amount">
                            <span class="text-danger error-cash-amount" id="cash_amount_error"></span>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12 col-lg-2 col-6 d-none" id="amount_input_container">
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="text" id="amount_input" name="amount" class="form-control"
                                placeholder="Enter Amount">
                            <span class="text-danger error-cash-amount" id="amount_error"></span>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12 col-lg-2 col-6 d-none" id="upi_amount_input_container">
                        <div class="form-group">
                            <label>Online Amount</label>
                            <input type="text" id="upi_amount_input" name="upi_amount" class="form-control"
                                placeholder="Enter Online Amount">
                            <span class="text-danger error-upi-amount" id="upi_amount_error"></span>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12 col-lg-2 col-6 d-none" id="pending_amount_container">
                        <div class="form-group">
                            <label>Pending Amount</label>
                            <input type="text" id="pending_amount" name="pending_amount" class="form-control"
                                readonly>
                            <span id="pending_error" style="color:red; font-size:12px;"></span>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-12 col-lg-2 col-6 d-none" id="bank_container">
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="mb-0">Select Bank</label>
                                <button type="button" id="openAddBankModal" class="btn btn-sm" style="border: 1px solid #ff9f43; color: #ff9f43; padding: 2px 8px; font-size: 12px;">Add Bank</button>
                            </div>
                            <select name="bank_id" id="bank_id" class="form-control select2">
                                <option value="">Select Bank</option>

                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                                @endforeach
                            </select>
                            <div class="text-danger error-bank_id"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                     <div class="col-lg-6 col-sm-6 col-12 purchase-remark-block">
                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remark" id="remark" class="form-control purchase-remark-textarea" placeholder="Enter any remarks"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6 float-md-right">
                        <div class="total-order">
                            <ul>
                                <li>
                                    <h4>{{ $totalItemLabel }}</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="total-product-amount">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="total-product-amount">0.00</span>
                                        @endif
                                    </h5>
                                </li>


                                <li>
                                    <h4>Discount Amount</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="total-discount-amount">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="total-discount-amount">0.00</span>
                                        @endif
                                    </h5>
                                </li>

                                <li>
                                    <h4>Price after Discount</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="price-after-discount">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="price-after-discount">0.00</span>
                                        @endif
                                    </h5>
                                </li>

                                <li id="gst-section" style="display:none;">
                                    <h4>Total GST</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="total-gst-amount">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="total-gst-amount">0.00</span>
                                        @endif
                                    </h5>
                                </li>

                                <li>
                                    <h4>Shipping</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="shipping-amount">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="shipping-amount">0.00</span>
                                        @endif
                                    </h5>
                                </li>

                                <li class="total">
                                    <h4>Grand Total</h4>
                                    <h5 style="color: green;">
                                        @if ($currencyPosition === 'right')
                                            <span id="grand-total">0.00</span>{{ $currencySymbol }}
                                        @else
                                            {{ $currencySymbol }}<span id="grand-total">0.00</span>
                                        @endif
                                    </h5>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <a href="javascript:void(0);" class="btn btn-submit me-2">{{ $isEditPurchase ? 'Update' : 'Submit' }}</a>
                    <a href="{{ $isRowMaterialPurchase ? route('purchase.row_material.lists') : route('purchase.lists') }}" class="btn btn-cancel">Cancel</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bank Modal -->
    <div class="modal fade" id="addBankModal" tabindex="-1" aria-labelledby="addBankModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addBankForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addBankModalLabel">Add Bank</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="add_bank_name" name="bank_name">
                                <div class="text-danger small" id="addBankNameError"></div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Account Number</label>
                                <input type="text" class="form-control" id="add_account_number" name="account_number">
                                <div class="text-danger small" id="addAccountNumberError"></div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">IFSC Code</label>
                                <input type="text" class="form-control" id="add_ifsc_code" name="ifsc_code">
                                <div class="text-danger small" id="addIfscCodeError"></div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Branch Name</label>
                                <input type="text" class="form-control" id="add_branch_name" name="branch_name">
                                <div class="text-danger small" id="addBranchNameError"></div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Opening Balance</label>
                                <input type="number" class="form-control" id="add_opening_balance" name="opening_balance" min="0" step="0.01" value="0">
                                <div class="text-danger small" id="addOpeningBalanceError"></div>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="add_bank_status" name="status">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="text-danger small" id="addBankStatusError"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-submit text-white" id="saveBankBtn" style="background-color: #ff9f43;">Save Bank</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Vendor Modal -->
<div class="modal fade" id="addVendorModal" tabindex="-1" aria-labelledby="addVendorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVendorModalLabel">Add New Vendor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <form id="addVendorForm" enctype="multipart/form-data">
                    <div class="vendor-modal-grid">
                        <div class="form-group">
                            <label>Vendor Name <span class="text-danger">*</span></label>
                            <input type="text" id="vendor_name_input" name="name" class="form-control" placeholder="Enter Vendor Name">
                            <span class="text-danger" id="vendor_name_error"></span>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="vendor_email_input" name="email" class="form-control" placeholder="Enter Email">
                            <span class="text-danger" id="vendor_email_error"></span>
                        </div>
                        <div class="form-group">
                            <label>Phone <span class="text-danger">*</span></label>
                            <input type="text" id="vendor_phone_input" name="phone" maxlength="10" class="form-control" placeholder="Enter Phone Number">
                            <span class="text-danger" id="vendor_phone_error"></span>
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" id="country_input" name="country" class="form-control" placeholder="Enter Country">
                            <span class="text-danger" id="country_error"></span>
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" id="city_input" name="city" class="form-control" placeholder="Enter City">
                            <span class="text-danger" id="city_error"></span>
                        </div>
                        <div class="form-group">
                            <label>State Code</label>
                            <input type="text" id="state_code_input" name="state_code" class="form-control" placeholder="Enter State Code">
                            <span class="text-danger" id="state_code_error"></span>
                        </div>
                        <div class="form-group">
                            <label>PAN Number</label>
                            <input type="text" id="pan_number_input" name="pan_number" class="form-control" placeholder="Enter Pan Number">
                            <span class="text-danger" id="pan_number_error"></span>
                        </div>
                        <div class="form-group">
                            <label>GST Number</label>
                            <input type="text" id="gst_number_input" name="gst_number" class="form-control" placeholder="Enter GST Number">
                            <span class="text-danger" id="gst_number_error"></span>
                        </div>
                        <div class="form-group vendor-full-width">
                            <label>Address</label>
                            <textarea id="vendor_address_input" name="address" class="form-control" rows="3" placeholder="Enter Address"></textarea>
                            <span class="text-danger" id="vendor_address_error"></span>
                        </div>
                        <div class="form-group vendor-full-width">
                            <label>Photo</label>
                            <input type="file" id="photo_input" name="photo" class="form-control" placeholder="Upload Photo" accept="image">
                            <span class="text-danger" id="photo_error"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <!-- <button type="button" class="btn btn-submit" id="saveVendorBtn">Save Vendor</button> -->
                 <button type="button" class="btn text-white" id="saveVendorBtn" style="background-color:#ff9f43;">Save Vendor</button>
            </div>
        </div>
    </div>
</div>
<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">{{ $isRowMaterialPurchase ? 'Add New Row Material' : 'Add New Product' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form id="addProductForm" novalidate>
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-12">
                            <div class="form-group mb-3">
                                <label>Product Type <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="item_type" id="modal_item_type_normal" value="normal" checked>
                                        <label class="form-check-label" for="modal_item_type_normal">Normal Product</label>
                                    </div>
                                    @php
                                        $userBranch = auth()->user()->branch_id ?? null;
                                        $setting = $userBranch ? \App\Models\Setting::where('branch_id', $userBranch)->first() : \App\Models\Setting::first();
                                        $enableVariant = $setting ? $setting->enable_variant_product : 1;
                                    @endphp
                                    @if ($enableVariant)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="item_type" id="modal_item_type_variant" value="variant">
                                            <label class="form-check-label" for="modal_item_type_variant">Variant Product</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>{{ $isRowMaterialPurchase ? 'Row Material Name' : 'Product Name' }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control modal-required-field" id="modal_product_name" required>
                                <small class="text-danger modal-field-error" id="error_modal_product_name" style="display:none;">{{ $isRowMaterialPurchase ? 'Row Material Name' : 'Product Name' }} is required.</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Category <span class="text-danger">*</span></label>
                                <select class="form-control modal-required-field" name="category_id" id="modal_category_id" required>
                                    <option value="">Choose Category</option>
                                </select>
                                <small class="text-danger modal-field-error" id="error_modal_category_id" style="display:none;">Category is required.</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Brand</label>
                                <select class="form-control" name="brand_id" id="modal_brand_id">
                                    <option value="">Choose Brand</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Unit <span class="text-danger">*</span></label>
                                <select class="form-control modal-required-field" name="unit_id" id="modal_unit_id" required>
                                    <option value="">Choose Unit</option>
                                </select>
                                <small class="text-danger modal-field-error" id="error_modal_unit_id" style="display:none;">Unit is required.</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6 normal-product-field-modal">
                            <div class="form-group">
                                <label>SKU</label>
                                <input type="text" name="SKU" class="form-control" id="modal_sku">
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>HSN Code</label>
                                <input type="text" name="hsn_code" class="form-control" id="modal_hsn_code">
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6 normal-product-field-modal">
                            <div class="form-group">
                                <label>Price <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" id="modal_price" step="0.01" min="0">
                                <small class="text-danger modal-field-error" id="error_modal_price" style="display:none;">Price is required.</small>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6 normal-product-field-modal">
                            <div class="form-group">
                                <label>Purchase Price</label>
                                <input type="number" name="purchase_price" class="form-control" id="modal_purchase_price" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6 normal-product-field-modal">
                            <div class="form-group">
                                <label>Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control" id="modal_quantity"  min="0" value="0">
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control" id="modal_status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>Stock</label>
                                <select name="availablility" class="form-control" id="modal_availablility" disabled>
                                    <option value="in_stock">In Stock</option>
                                    <option value="out_stock" selected>Out of Stock</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6">
                            <div class="form-group">
                                <label>GST Option</label>
                                <select name="gst_option" class="form-control" id="modal_gst_option">
                                    <option value="without_gst">Without GST</option>
                                    <option value="with_gst">With GST</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6" id="modal_gst_container" style="display:none;">
                            <div class="form-group">
                                <label>GST Rates</label>
                                <select name="product_gst[]" class="form-control" id="modal_product_gst" multiple>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6 normal-product-field-modal">
                            <div class="form-group">
                                <label>Barcode</label>
                                <input type="text" name="barcode" class="form-control" id="modal_barcode">
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6 normal-product-field-modal">
                            <div class="form-group">
                                <label>MRP</label>
                                <input type="number" name="mrp" class="form-control" id="modal_mrp" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6 normal-product-field-modal">
                            <div class="form-group">
                                <label>Batch No</label>
                                <input type="text" name="batch_no" class="form-control" id="modal_batch_no">
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6 normal-product-field-modal">
                            <div class="form-group">
                                <label>Label Code</label>
                                <input type="text" name="label_code" class="form-control" id="modal_label_code">
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6 normal-product-field-modal">
                            <div class="form-group">
                                <label>Wholesale</label>
                                <input type="text" name="label_reference" class="form-control" id="modal_label_ref">
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-6 normal-product-field-modal">
                            <div class="form-group">
                                <label>Other</label>
                                <input type="text" name="specific_qty" class="form-control" id="modal_specific_qty">
                            </div>
                        </div>

                        <!-- Rental Fields -->
                        <div class="col-lg-3 col-sm-6 col-6 mt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_rent_available" id="modal_is_rent_available" value="1">
                                <label class="form-check-label fw-bold" for="modal_is_rent_available" style="cursor: pointer;">Available for Rent</label>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-sm-6 col-6 modal-rental-fields" style="display: none;">
                            <div class="form-group">
                                <label class="form-label-icon">Rent Type</label>
                                <select class="form-control" name="rent_period" id="modal_rent_period">
                                    <option value="">Select</option>
                                    <option value="hour">Hour</option>
                                    <option value="day">Day</option>
                                    <option value="week">Week</option>
                                    <option value="month">Month</option>
                                    <option value="year">Year</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-sm-6 col-6 modal-rental-fields" style="display: none;">
                            <div class="form-group">
                                <label class="form-label-icon"> Rent Price</label>
                                <input type="number" name="rent_price" id="modal_rent_price" class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                        
                        <!-- Product Variants Section for Modal -->
                        <div class="col-lg-12 mt-3 variant-product-field-modal" style="display: none;">
                            <div class="card">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Product Variants</h5>
                                    <button type="button" class="btn btn-primary btn-sm" id="addVariantBtnModal2">+ Add More</button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered border" id="variantsTableModal2">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Size</th>
                                                    <th>Color</th>
                                                    <th>Qty</th>
                                                    <th>Purchase Price</th>
                                                    <th>Price <span class="text-danger">*</span></th>
                                                    <th>Barcode</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="variantsBodyModal2">
                                                <tr>
                                                    <td><input type="text" name="variants[0][size]" class="form-control" required placeholder="e.g. M"></td>
                                                    <td><input type="text" name="variants[0][color]" class="form-control" required placeholder="e.g. Red"></td>
                                                    <td><input type="number" name="variants[0][qty]" class="form-control" min="0" required placeholder="0"></td>
                                                    <td><input type="number" name="variants[0][purchase_price]" class="form-control" min="0" step="0.01" required placeholder="0.00"></td>
                                                    <td><input type="number" name="variants[0][price]" class="form-control" min="0" step="0.01" required placeholder="0.00"></td>
                                                    <td>
                                                        <input type="text" name="variants[0][barcode]" class="form-control" placeholder="Auto-generate">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-danger btn-sm remove-variant-modal2" disabled><i class="fa fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Variants Section -->
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" id="modal_description" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="form-group">
                                <label>{{ $isRowMaterialPurchase ? 'Row Material Image' : 'Product Image' }}</label>
                                <input type="file" name="images[]" class="form-control" id="modal_product_image" accept="image/*">
                            </div>
                        </div>
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const rentCheckbox = document.getElementById('modal_is_rent_available');
                            const rentFields = document.querySelectorAll('.modal-rental-fields');
                            if(rentCheckbox) {
                                rentCheckbox.addEventListener('change', function() {
                                    rentFields.forEach(field => {
                                        field.style.display = this.checked ? 'block' : 'none';
                                    });
                                });
                            }
                            
                            const itemTypeRadios = document.querySelectorAll("input[name='item_type']");
                            itemTypeRadios.forEach(radio => {
                                radio.addEventListener('change', function() {
                                    if (this.value === 'variant') {
                                        document.querySelectorAll('.normal-product-field-modal').forEach(el => el.style.display = 'none');
                                        document.querySelectorAll('.variant-product-field-modal').forEach(el => el.style.display = 'block');
                                        document.querySelectorAll('.modal-rental-fields').forEach(el => el.style.display = 'none');
                                    } else {
                                        document.querySelectorAll('.variant-product-field-modal').forEach(el => el.style.display = 'none');
                                        document.querySelectorAll('.normal-product-field-modal').forEach(el => el.style.display = 'block');
                                        if (document.getElementById('modal_is_rent_available').checked) {
                                            document.querySelectorAll('.modal-rental-fields').forEach(el => el.style.display = 'block');
                                        }
                                    }
                                });
                            });
                            
                            document.getElementById('addVariantBtnModal2')?.addEventListener('click', function() {
                                let tbody = document.getElementById('variantsBodyModal2');
                                let rowCount = tbody.querySelectorAll('tr').length;
                                let newRow = document.createElement('tr');
                                newRow.innerHTML = `
                                    <td><input type="text" name="variants[${rowCount}][size]" class="form-control" required placeholder="e.g. M"></td>
                                    <td><input type="text" name="variants[${rowCount}][color]" class="form-control" required placeholder="e.g. Red"></td>
                                    <td><input type="number" name="variants[${rowCount}][qty]" class="form-control" min="0" required placeholder="0"></td>
                                    <td><input type="number" name="variants[${rowCount}][purchase_price]" class="form-control" min="0" step="0.01" required placeholder="0.00"></td>
                                    <td><input type="number" name="variants[${rowCount}][price]" class="form-control" min="0" step="0.01" required placeholder="0.00"></td>
                                    <td>
                                        <input type="text" name="variants[${rowCount}][barcode]" class="form-control" placeholder="Auto-generate">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-variant-modal2"><i class="fa fa-trash"></i></button>
                                    </td>
                                `;
                                tbody.appendChild(newRow);
                                updateModalRemoveButtons2();
                            });
                            
                            document.getElementById('variantsBodyModal2')?.addEventListener('click', function(e) {
                                if(e.target.closest('.remove-variant-modal2')) {
                                    if (document.querySelectorAll('#variantsBodyModal2 tr').length > 1) {
                                        e.target.closest('tr').remove();
                                        updateModalRemoveButtons2();
                                    }
                                }
                            });
                            
                            function updateModalRemoveButtons2() {
                                let rows = document.querySelectorAll('#variantsBodyModal2 tr');
                                if (rows.length === 1) {
                                    rows[0].querySelector('.remove-variant-modal2').disabled = true;
                                } else {
                                    rows.forEach(row => {
                                        let btn = row.querySelector('.remove-variant-modal2');
                                        if(btn) btn.disabled = false;
                                    });
                                }
                            }
                        });
                    </script>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" id="saveProductBtn" style="background-color: #ff9f43; border-color: #ff9f43;">Save Product</button>
            </div>
        </div>
    </div>
</div>

<!-- Scan Barcode Modal -->
<div class="modal fade" id="scanBarcodeModal" tabindex="-1" aria-labelledby="scanBarcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scanBarcodeModalLabel">Scan Barcode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body text-center" style="padding: 30px;">
                <!-- Camera Viewport -->
                <div id="barcode-reader" style="width: 100%; max-width: 400px; margin: 0 auto 20px auto; border-radius: 8px; overflow: hidden; display: none;"></div>
                <div id="camera_status_message" class="text-muted mb-3" style="display: none;">Starting camera...</div>
                
                <p class="text-muted mb-4" id="manual_entry_text">No camera found on this device.<br>Enter barcode manually below:</p>
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <input type="text" id="barcode_input_field" class="form-control" placeholder="Enter barcode / product code" style="max-width: 250px; margin-right: 10px;" autocomplete="off" autofocus>
                    <button type="button" id="searchBarcodeBtn" class="btn text-white" style="background-color: #ff9f43; white-space: nowrap;">Search</button>
                </div>
                <div id="barcode_scan_message" class="alert alert-success mt-3" style="display: none; padding: 10px;">
                    <!-- Message goes here -->
                </div>
                <div id="barcode_error_message" class="alert alert-danger mt-3" style="display: none; padding: 10px;">
                    <!-- Error message goes here -->
                </div>
            </div>
            <div class="modal-footer" style="justify-content: flex-start;">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        const products = @json($itemOptions);
        const rowMaterials = @json($rowMaterialsArray ?? []);
        const isRowMaterialPurchase = @json($isRowMaterialPurchase);
        const isEditPurchase = @json($isEditPurchase);
        const editPurchaseId = @json($editPurchaseId);
        const purchaseSubmitUrl = @json($purchaseSubmitUrl);
        const purchaseRedirectUrl = @json($purchaseRedirectUrl);
    </script>
    <script>
        function purchaseRetailRates(product) {
            if (!product) return [];
            let rates = product.selling_prices;
            if (typeof rates === 'string') {
                try { rates = JSON.parse(rates); } catch (e) { rates = []; }
            }
            if (!Array.isArray(rates)) rates = [];
            const mainPrice = parseFloat(product.price) || 0;
            const mainQty = parseFloat(product.quantity) || 0;
            const options = [];
            const seen = {};
            const pushRate = function(name, price, qty) {
                const key = Number(price).toFixed(4);
                if (seen[key]) return;
                seen[key] = true;
                options.push({
                    name: name,
                    price: parseFloat(price) || 0,
                    qty: parseFloat(qty) || 0
                });
            };
            pushRate('Rate 1', mainPrice, mainQty);
            rates.forEach(function(rate) {
                if (!rate || rate.price === undefined || rate.price === null || rate.price === '') return;
                const name = String(rate.name || '');
                if (/wholesale/i.test(name)) return;
                const price = parseFloat(rate.price);
                if (Math.abs(price - mainPrice) < 0.0001) return;
                pushRate(name || ('Rate ' + (options.length + 1)), price, rate.qty);
            });
            return options;
        }

        function fillPurchaseRateSelect($row, selectedPrice) {
            const $wrap = $row.find('.purchase-rate-wrap');
            const $select = $row.find('.purchase-rate-select');
            if (!$wrap.length || (typeof isRowMaterialPurchase !== 'undefined' && isRowMaterialPurchase)) {
                $wrap.hide();
                return;
            }
            const productId = $row.find('.product-select').val();
            const product = (typeof products !== 'undefined' ? products : []).find(function(p) {
                return String(p.id) === String(productId);
            });
            const rates = purchaseRetailRates(product);
            if (rates.length < 2) {
                $wrap.hide();
                $select.empty();
                return;
            }
            $select.empty();
            rates.forEach(function(rate) {
                const label = rate.name + ' · ₹' + rate.price + ' · Stock ' + rate.qty;
                $select.append($('<option>', { value: rate.price, text: label }));
            });
            if (selectedPrice !== undefined && selectedPrice !== null && selectedPrice !== '') {
                const match = rates.find(function(rate) {
                    return Math.abs(parseFloat(rate.price) - parseFloat(selectedPrice)) < 0.0001;
                });
                if (match) $select.val(String(match.price));
            }
            $wrap.show();
        }

        $(document).ready(function() {
            let authToken = localStorage.getItem("authToken");
            // Product Search Logic
            $('#search_product').on('input focus', function() {
                let query = $(this).val().toLowerCase();
                let $resultsList = $('#search_results_list');
                $resultsList.empty();

                if (!products || products.length === 0) return;

                let filtered = products.filter(p => p.name && p.name.toLowerCase().includes(query));

                if (filtered.length > 0) {
                    filtered.forEach(p => {
                        let price = parseFloat(p.price) || 0;
                        let gstLabel = p.gst_option === 'with_gst' ? '<small class="text-success">With GST</small>' : '<small class="text-muted">Without GST</small>';
                        
                        let imageSrc = p.image ? ('/images/' + p.image) : '{{ env("ImagePath") ?? "" }}/admin/assets/img/product/product1.jpg'; 
                        
                        let itemHtml = `
                        <div class="d-flex justify-content-between align-items-center p-2 border-bottom search-item bg-white">
                            <div class="d-flex align-items-center" style="margin-left: 10px;">
                                <div style="width: 40px; height: 40px; background-color: #eee; border-radius: 4px; margin-right: 15px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <img src="${imageSrc}" onerror="this.src='{{ env('ImagePath', '') }}/admin/assets/img/product/product1.jpg'; this.onerror=null;" style="max-width: 100%; max-height: 100%;">
                                </div>
                                <div>
                                    <strong style="font-size: 13px;">${p.name}</strong><br>
                                    <small>Price: {{ $currencySymbol ?? '₹' }}${price.toFixed(2)}</small><br>
                                    ${gstLabel}
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-warning btn-add-search-product" data-id="${p.id}" style="border-color: #ff9f43; color: #ff9f43 !important; margin-right: 10px;">+ Add</button>
                        </div>`;
                        $resultsList.append(itemHtml);
                    });
                    $('#search_results_container').show();
                } else {
                    $resultsList.append('<div class="p-3 text-center text-muted bg-white">No products found</div>');
                    $('#search_results_container').show();
                }
            });

            $('#close_search_results').on('click', function() {
                $('#search_results_container').hide();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#search_results_container, #search_product').length) {
                    $('#search_results_container').hide();
                }
            });

            $(document).on('click', '.btn-add-search-product', function() {
                let productId = $(this).data('id');
                let product = products.find(p => p.id == productId);
                
                if (product) {
                    let lastRow = $('.form-row').last();
                    let lastRowProduct = lastRow.find('.product-select').val();
                    
                    if (lastRowProduct) {
                        $('.add-row').first().trigger('click');
                        lastRow = $('.form-row').last();
                    }
                    
                    // Set category first, then product
                    if(product.category_id) {
                        lastRow.find('.category-select').val(product.category_id).trigger('change');
                    }
                    
                    setTimeout(() => {
                        lastRow.find('.product-select').val(product.id).trigger('change');
                        
                        Swal.fire({
                            title: '<h3 style="margin: 0; padding: 0;">Product Added</h3>',
                            html: `<p style="margin-top: 10px;">${product.name} added to purchase</p>`,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500,
                            customClass: {
                                popup: 'rounded-4'
                            }
                        });
                        
                        $('#search_results_container').hide();
                        $('#search_product').val('');
                    }, 100);
                }
            });

            // Clear inline errors on field input/change
            $(document).on('input change', '.modal-required-field', function() {
                let fieldId = $(this).attr('id');
                if ($(this).val() && $(this).val().toString().trim() !== '') {
                    $('#error_' + fieldId).hide();
                    $(this).removeClass('is-invalid');
                }
            });

            let html5QrCode = null;

            // Scan Barcode Modal
            $('#openScanBarcodeModalBtn').on('click', function() {
                $('#scanBarcodeModal').modal('show');
                
                // Initialize camera if available
                if (typeof Html5Qrcode !== 'undefined') {
                    Html5Qrcode.getCameras().then(devices => {
                        if (devices && devices.length) {
                            $('#barcode-reader').show();
                            $('#camera_status_message').show().text("Starting camera...");
                            $('#manual_entry_text').html('Or enter barcode manually below:');
                            
                            html5QrCode = new Html5Qrcode("barcode-reader");
                            html5QrCode.start(
                                { facingMode: "environment" }, 
                                {
                                    fps: 10,
                                    qrbox: { width: 250, height: 150 },
                                    aspectRatio: 1.777778
                                },
                                (decodedText, decodedResult) => {
                                    if (html5QrCode.getState() === 2) {
                                        html5QrCode.pause();
                                    }
                                    
                                    $('#barcode_input_field').val(decodedText);
                                    processBarcodeSearch();
                                    
                                    setTimeout(() => {
                                        if (html5QrCode && html5QrCode.getState() === 3) {
                                            html5QrCode.resume();
                                        }
                                    }, 2000);
                                },
                                (errorMessage) => {
                                    // Parse error, ignore
                                })
                            .then(() => {
                                $('#camera_status_message').hide();
                            })
                            .catch((err) => {
                                $('#camera_status_message').text("Unable to start camera.");
                                console.error("Camera start error", err);
                            });
                        } else {
                            $('#barcode-reader').hide();
                            $('#camera_status_message').hide();
                            $('#manual_entry_text').html('No camera found on this device.<br>Enter barcode manually below:');
                        }
                    }).catch(err => {
                        $('#barcode-reader').hide();
                        $('#camera_status_message').hide();
                        $('#manual_entry_text').html('Camera access denied or unsupported.<br>Enter barcode manually below:');
                    });
                }
                
                setTimeout(() => {
                    $('#barcode_input_field').focus();
                }, 500);
            });

            $('#scanBarcodeModal').on('hidden.bs.modal', function () {
                $('#barcode_input_field').val('');
                $('#barcode_scan_message').hide();
                $('#barcode_error_message').hide();
                
                if (html5QrCode) {
                    html5QrCode.stop().then(() => {
                        html5QrCode.clear();
                        html5QrCode = null;
                    }).catch(err => {
                        console.error("Failed to stop html5QrCode", err);
                    });
                }
            });

            function processBarcodeSearch() {
                let barcode = $('#barcode_input_field').val().trim();
                if (!barcode) return;

                let product = products.find(p => {
                    if (p.barcode) {
                        let barcodes = p.barcode.toString().split(',').map(b => b.trim());
                        if (barcodes.includes(barcode)) return true;
                    }
                    if (p.name && p.name.toLowerCase() == barcode.toLowerCase()) return true;
                    return false;
                });
                
                if (product) {
                    let lastRow = $('.form-row').last();
                    let lastRowProduct = lastRow.find('.product-select').val();
                    
                    if (lastRowProduct) {
                        $('.add-row').first().trigger('click');
                        lastRow = $('.form-row').last();
                    }
                    
                    if(product.category_id) {
                        lastRow.find('.category-select').val(product.category_id).trigger('change');
                    }
                    
                    setTimeout(() => {
                        lastRow.find('.product-select').val(product.id).trigger('change');
                        
                        // $('#barcode_scan_message').html(`✓ ${product.name} added — enter next barcode`).show();
                        $('#barcode_error_message').hide();
                        $('#barcode_input_field').val('');
                        
                        // Close the modal automatically
                        $('#scanBarcodeModal').modal('hide');
                        
                        Swal.fire({
                            title: 'Success!',
                            text: product.name + ' added to purchase',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }, 100);
                } else {
                    // $('#barcode_error_message').html(`Product not found for barcode: ${barcode}`).show();
                    // $('#barcode_scan_message').hide();
                    $('#barcode_input_field').val('').focus();
                    
                    Swal.fire({
                        title: 'Error!',
                        text: 'Product not found for barcode: ' + barcode,
                        icon: 'error',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }

            $('#searchBarcodeBtn').on('click', function() {
                processBarcodeSearch();
            });

            $('#barcode_input_field').on('keypress', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    processBarcodeSearch();
                }
            });

            // Add Product Modal Logic
            $('#openAddProductModalBtn').on('click', function() {
                $('#addProductForm')[0].reset();
                $('.modal-field-error').hide();
                $('.modal-required-field').removeClass('is-invalid');
                $('#modal_gst_container').hide();
                $('#modal_availablility').val('out_stock');
                
                // Initialize Select2 if not already
                if (!$('#modal_category_id').hasClass('select2-hidden-accessible')) {
                    $('#modal_category_id, #modal_brand_id, #modal_unit_id, #modal_product_gst').select2({
                        dropdownParent: $('#addProductModal'),
                        width: '100%'
                    });
                }
                
                fetchModalDropdowns();
                $('#addProductModal').modal('show');
            });

            $('#modal_quantity').on('input change', function() {
                let qty = parseFloat($(this).val());
                if (qty > 0) {
                    $('#modal_availablility').val('in_stock');
                } else {
                    $('#modal_availablility').val('out_stock');
                }
            });

            $('#modal_gst_option').on('change', function() {
                if ($(this).val() === 'with_gst') {
                    $('#modal_gst_container').show();
                } else {
                    $('#modal_gst_container').hide();
                    $('#modal_product_gst').val(null).trigger('change');
                }
            });

            function fetchModalDropdowns() {
                // Fetch Categories
                $.ajax({
                    url: '/api/get-category',
                    type: 'GET',
                    headers: { "Authorization": "Bearer " + authToken },
                    success: function(res) {
                        if(res.status) {
                            let sel = $('#modal_category_id');
                            sel.empty().append('<option value="">Choose Category</option>');
                            res.data.forEach(item => sel.append(`<option value="${item.id}">${item.name}</option>`));
                        }
                    }
                });

                // Fetch Brands
                $.ajax({
                    url: '/api/get-brand',
                    type: 'GET',
                    headers: { "Authorization": "Bearer " + authToken },
                    success: function(res) {
                        if(res.status) {
                            let sel = $('#modal_brand_id');
                            sel.empty().append('<option value="">Choose Brand</option>');
                            res.data.forEach(item => sel.append(`<option value="${item.id}">${item.name}</option>`));
                        }
                    }
                });

                // Fetch Units
                $.ajax({
                    url: '/api/get-units',
                    type: 'GET',
                    headers: { "Authorization": "Bearer " + authToken },
                    success: function(res) {
                        if(res.status) {
                            let sel = $('#modal_unit_id');
                            sel.empty().append('<option value="">Choose Unit</option>');
                            res.data.forEach(item => sel.append(`<option value="${item.id}">${item.unit_name}</option>`));
                        }
                    }
                });

                // Fetch GST Rates
                $.ajax({
                    url: '/api/get-tax-rates',
                    type: 'GET',
                    headers: { "Authorization": "Bearer " + authToken },
                    success: function(res) {
                        if(res.status) {
                            let sel = $('#modal_product_gst');
                            sel.empty();
                            res.data.forEach(item => sel.append(`<option value="${item.id}">${item.tax_name} (${item.tax_rate}%)</option>`));
                        }
                    }
                });
            }

            $('#saveProductBtn').on('click', function() {
                // Clear all previous inline errors
                $('.modal-field-error').hide();
                $('.is-invalid').removeClass('is-invalid');

                // Custom inline validation
                let isValid = true;
                let itemType = $("input[name='item_type']:checked").val();
                
                let requiredFields = [
                    { id: 'modal_product_name', errId: 'error_modal_product_name', label: 'Product Name is required.' },
                    { id: 'modal_category_id',  errId: 'error_modal_category_id',  label: 'Category is required.' },
                    { id: 'modal_unit_id',       errId: 'error_modal_unit_id',       label: 'Unit is required.' }
                ];
                
                if (itemType !== 'variant') {
                    requiredFields.push({ id: 'modal_price', errId: 'error_modal_price', label: 'Price is required.' });
                }

                requiredFields.forEach(function(f) {
                    let val = $('#' + f.id).val();
                    if (!val || val.toString().trim() === '') {
                        $('#' + f.errId).text(f.label).show();
                        $('#' + f.id).addClass('is-invalid');
                        isValid = false;
                    }
                });
                if (!isValid) {
                    $('#addProductForm .is-invalid').on('input change', function() {
                        $(this).removeClass('is-invalid');
                        $(this).siblings('.modal-field-error').hide();
                    });
                    return;
                }

                let form = $('#addProductForm')[0];

                let formData = new FormData(form);
                
                let selectedAdmin = localStorage.getItem('selectedSubAdminId');
                if (selectedAdmin) {
                    formData.append('branch_id', selectedAdmin);
                }

                let btn = $(this);
                btn.text('Saving...').prop('disabled', true);

                let submitUrl = isRowMaterialPurchase ? '/api/create-row-material' : '/api/createProduct';

                $.ajax({
                    url: submitUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        btn.text('Save Product').prop('disabled', false);
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#addProductModal').modal('hide');
                            
                            let newProduct = isRowMaterialPurchase ? response.row_material : response.product;
                            
                            // Map row_materialname to name for UI compatibility
                            if(isRowMaterialPurchase && newProduct.row_materialname) {
                                newProduct.name = newProduct.row_materialname;
                            }
                            
                            // Add to global products array
                            if(typeof products !== 'undefined') {
                                products.push(newProduct);
                            }
                            
                            // Trigger Add Row functionality to add it
                            let lastRow = $('.form-row').last();
                            let lastRowProduct = lastRow.find('.product-select').val();
                            
                            if (lastRowProduct) {
                                $('.add-row').first().trigger('click');
                                lastRow = $('.form-row').last();
                            }
                            
                            // Set category first, then product
                            if(newProduct.category_id) {
                                lastRow.find('.category-select').val(newProduct.category_id).trigger('change');
                            }
                            
                            // Wait for product dropdown to populate based on category
                            setTimeout(() => {
                                // If the new product isn't in the product dropdown yet, add it manually
                                let productSelect = lastRow.find('.product-select');
                                if(productSelect.find(`option[value='${newProduct.id}']`).length === 0) {
                                    productSelect.append(`<option value="${newProduct.id}">${newProduct.name}</option>`);
                                }
                                productSelect.val(newProduct.id).trigger('change');
                            }, 500);

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to create product'
                            });
                        }
                    },
                    error: function(xhr) {
                        btn.text('Save Product').prop('disabled', false);
                        let errMsg = 'Something went wrong';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errMsg = Object.values(xhr.responseJSON.errors).map(err => err.join(', ')).join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errMsg
                        });
                    }
                });
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            if ($('.purchase-datepicker').length > 0 && typeof $.fn.datetimepicker === 'function') {
                $('.purchase-datepicker').datetimepicker({
                    format: 'DD/MM/YYYY',
                    icons: {
                        up: "fas fa-angle-up",
                        down: "fas fa-angle-down",
                        next: 'fas fa-angle-right',
                        previous: 'fas fa-angle-left'
                    }
                });
            }
            // Add Vendor Modal Functionality
            $('#addVendorBtn').on('click', function() {
                $('#addVendorModal').modal('show');
                $('#addVendorForm')[0].reset();
                $('#addVendorForm .text-danger').text('');
            });

            // Save Vendor
            $('#saveVendorBtn').on('click', function() {
                let $btn = $(this);
                let originalText = $btn.text();

                $('#addVendorForm .text-danger').text('');

                let vendorData = {
                    name: $('#vendor_name_input').val().trim(),
                    email: $('#vendor_email_input').val().trim(),
                    phone: $('#vendor_phone_input').val().trim(),
                    address: $('#vendor_address_input').val().trim(),
                    country: $('#country_input').val().trim(),
                    city: $('#city_input').val().trim(),
                    state_code: $('#state_code_input').val().trim(),
                    pan_number: $('#pan_number_input').val().trim(),
                    gst_number: $('#gst_number_input').val().trim(),
                    photo: $('#photo_input')[0].files[0] || null,
                    selectedSubAdminId: localStorage.getItem('selectedSubAdminId') || null
                };

                let hasError = false;

                if (!vendorData.name) {
                    $('#vendor_name_error').text('Vendor name is required');
                    hasError = true;
                }

                if (!vendorData.phone) {
                    $('#vendor_phone_error').text('Phone number is required');
                    hasError = true;
                } else if (!/^[0-9]{10}$/.test(vendorData.phone)) {
                    $('#vendor_phone_error').text('Please enter a valid 10 digit phone number');
                    hasError = true;
                }

                if (vendorData.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(vendorData.email)) {
                    $('#vendor_email_error').text('Please enter a valid email');
                    hasError = true;
                }

                if (hasError) {
                    return;
                }

                $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...').prop('disabled', true);

                $.ajax({
                    url: '/api/createSupplier',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Authorization': 'Bearer ' + authToken,
                    },
                    contentType: 'application/json',
                    data: JSON.stringify(vendorData),
                    success: function(response) {
                        $btn.text(originalText).prop('disabled', false);

                        if (response.success || response.status) {
                            let newVendor = response.vendor;

                            if (!newVendor || !newVendor.id || !newVendor.name) {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Vendor was created, but vendor details were not returned. Please refresh the page.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                                return;
                            }

                            let newOption = new Option(newVendor.name, newVendor.id, true, true);
                            $(newOption).attr('data-phone', newVendor.phone || '');
                            $('#vendor_name').append(newOption);
                            $('#vendor_name').val(newVendor.id).trigger('change');
                            $('#vendor_phone').val(newVendor.phone || '');
                            $('#addVendorModal').modal('hide');

                            Swal.fire({
                                title: 'Success',
                                text: response.message || 'Vendor added successfully',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#ff9f43'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message || 'Failed to add vendor',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        $btn.text(originalText).prop('disabled', false);

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.name) $('#vendor_name_error').text(errors.name[0]);
                            if (errors.email) $('#vendor_email_error').text(errors.email[0]);
                            if (errors.phone) $('#vendor_phone_error').text(errors.phone[0]);
                            if (errors.state_code) $('#state_code_error').text(errors.state_code[0]);
                            if (errors.pan_number) $('#pan_number_error').text(errors.pan_number[0]);
                            if (errors.gst_number) $('#gst_number_error').text(errors.gst_number[0]);
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            Swal.fire({ title: 'Error', text: xhr.responseJSON.message, icon: 'error', confirmButtonText: 'OK' });
                        } else {
                            Swal.fire({ title: 'Error', text: 'Something went wrong!', icon: 'error', confirmButtonText: 'OK' });
                        }
                    }
                });
            });

            // Reset form on modal close
            $('#addVendorModal').on('hidden.bs.modal', function() {
                $('#addVendorForm')[0].reset();
                $('#addVendorForm .text-danger').text('');
            });
            $(document).on('change', 'input[name="purchase_type_option"]', function() {
                const url = $(this).data('url');
                if (url) {
                    window.location.href = url;
                }
            });

            // Initialize Select2
            $(".vendor-select,.product-select,.category-select,#bank_id,.payment-mode-select,.purchase-status-select,.paid-type-container")
                .select2({
                    tags: true,
                });

            // ✅ Common formatting handlers
            $(document).on("focus", ".price-input, .product-discount-amount-input, #shipping, #amount_input, #cash_amount_input, #upi_amount_input", function() {
                let val = $(this).val();
                if (val) {
                    $(this).val(parseIndianNumber(val) || '');
                }
            });

            $(document).on("blur", ".price-input, .product-discount-amount-input, #shipping, #amount_input, #cash_amount_input, #upi_amount_input", function() {
                let val = $(this).val();
                if (val !== "" && !isNaN(parseIndianNumber(val))) {
                    $(this).val(formatIndianNumber(parseIndianNumber(val)));
                }
            });
            //  $(".select2, .category-select").select2({
            //     tags: true,
            // });
            let invalid = false;

            function setRowMaterialPurchaseRow($row, item) {
                $row.find('.category-select').val(item.category_id || '').trigger('change');
                $row.find('.product-select').prop('disabled', false).val(item.row_material_id || '').trigger('change');
                $row.find('.price-input').val(item.price || '');
                $row.find('.quantity-input').val(item.quantity || 1);
                $row.find('.product-discount-input').val(item.discount_percent || 0);
                $row.find('.product-discount-amount-input').val(item.discount_amount || 0);
                $row.find('.total-input').val(item.amount_total || 0);
            }

            function populateRowMaterialPurchaseForm(data) {
                const invoice = data.invoice || {};
                const vendor = data.vendor || {};
                const items = data.items || [];
                const grandTotal = parseFloat(invoice.grand_total || 0);
                const remainingAmount = parseFloat(invoice.remaining_amount || 0);
                const paidAmount = Math.max(grandTotal - remainingAmount, 0);
                const purchaseDate = invoice.purchase_date || '';

                const formatPurchaseDate = function(value) {
                    if (!value) return '';

                    const date = new Date(value);
                    if (isNaN(date.getTime())) {
                        return value;
                    }

                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();

                    return `${day}/${month}/${year}`;
                };

                $('#vendor_name').val(invoice.vendor_id || vendor.id || '').trigger('change');
                $('#vendor_phone').val(vendor.phone || '');
                $('#bill_no').val(invoice.bill_no || '');
                $('#purchase_date').val(formatPurchaseDate(purchaseDate));
                $('#remark').val(invoice.remark || '');
                $('#shipping').val(invoice.shipping || 0);
                $('select[name="status"]').val(invoice.status || 'pending').trigger('change');
                $('input[name="gst_option"][value="' + (invoice.gst_option === 'with_gst' ? 'with' : 'without') + '"]').prop('checked', true).trigger('change');

                $('#form-container').empty();

                if (items.length) {
                    setRowMaterialPurchaseRow($('.form-row').first(), items[0]);

                    items.slice(1).forEach(function(item) {
                        $('.add-row').first().trigger('click');
                        setRowMaterialPurchaseRow($('#form-container .form-row').last(), item);
                    });
                }

                if ((data.payment_status || '').toLowerCase() === 'completed') {
                    $('#payment_mode').val('cash').trigger('change');
                    $('#paid_type').val('full').trigger('change');
                    $('#amount_input').val(grandTotal.toFixed(2));
                    $('#pending_amount').val('0.00');
                } else if ((data.payment_status || '').toLowerCase() === 'partially') {
                    $('#payment_mode').val('cash').trigger('change');
                    $('#paid_type').val('partial').trigger('change');
                    $('#amount_input').val(paidAmount.toFixed(2));
                    $('#pending_amount').val(remainingAmount.toFixed(2));
                } else {
                    $('#payment_mode').val('pending').trigger('change');
                    $('#pending_amount').val(remainingAmount.toFixed(2));
                }

                setTimeout(function() {
                    calculateTotal();
                }, 0);
            }

            if (isRowMaterialPurchase && isEditPurchase && editPurchaseId) {
                $.ajax({
                    url: `/api/row-material-purchase/${editPurchaseId}`,
                    type: 'GET',
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    data: {
                        selectedSubAdminId: localStorage.getItem("selectedSubAdminId")
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            populateRowMaterialPurchaseForm(response.data);
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: "Error",
                            text: xhr.responseJSON?.message || "Failed to load row material purchase.",
                            icon: "error",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location.href = purchaseRedirectUrl;
                        });
                    }
                });
            }

            $('#upi_amount_input').on('keyup input', function() {
                let value = parseIndianNumber($(this).val());
                let $errorSpan = $('#upi_amount_error');

                if (!isNaN(value) && value < 0) {
                    invalid = true;
                    $errorSpan.text('Negative amount is not valid');
                } else {
                    invalid = false;
                    $errorSpan.text('');
                }
            });
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('price-input')) {
                    let val = e.target.value;

                    // allow empty while typing
                    if (val === '') return;

                    // prevent negative numbers
                    if (parseIndianNumber(val) < 0) {
                        e.target.value = 0;
                    }
                }
            });
            $('#amount_input').on('keyup input', function() {
                let value = parseIndianNumber($(this).val());
                let $errorSpan = $('#amount_error');

                if (!isNaN(value) && value < 0) {
                    invalid = true;
                    $errorSpan.text('Negative amount is not valid');
                } else {
                    invalid = false;
                    $errorSpan.text('');
                }
            });
            $('#cash_amount_input').on('keyup input', function() {
                let value = parseIndianNumber($(this).val());
                let $errorSpan = $('#cash_amount_error');

                if (!isNaN(value) && value < 0) {
                    invalid = true;
                    $errorSpan.text('Negative amount is not valid');
                } else {
                    invalid = false;
                    $errorSpan.text('');
                }
            });

            $('#payment_mode').prop('disabled', true);

            // Watch for changes in the payable amount field
            const target = document.getElementById('grand-total');

            const observer = new MutationObserver(() => {
                let amount = parseIndianNumber($('#grand-total').text());
                if (!isNaN(amount) && amount > 0) {
                    $('#payment_mode').prop('disabled', false);
                } else {
                    $('#payment_mode').val('').prop('disabled', true);
                }
            });

            observer.observe(target, {
                childList: true,
                characterData: true,
                subtree: true
            });

            $("#payment_mode").change(function() {
                const selectedMode = $(this).val();

                // Reset values
                $("#paid_type").val("");
                $("#bank_id").val("").trigger('change');
                $("#amount_input, #cash_amount_input, #upi_amount_input, #pending_amount").val("");

                // Hide everything first
                $("#paid_type_container, #amount_input_container, #cash_amount_input_container, #upi_amount_input_container, #pending_amount_container, #bank_container")
                    .addClass("d-none");

                // ✅ ONLY show Paid Type for real payment modes
                if (selectedMode === "cash" || selectedMode === "online" || selectedMode === "cashonline") {
                    $("#paid_type_container").removeClass("d-none");
                }

                if (selectedMode === "online" || selectedMode === "cashonline") {
                    $("#bank_container").removeClass("d-none");
                }

                // ✅ Pending = no paid type, no amount, no validation
                if (selectedMode === "pending") {
                    return; // stop here
                }
            });


            $("#paid_type").change(function() {
                const type = $(this).val();
                const selectedMode = $("#payment_mode").val();

                // Clear all fields and errors
                $("#amount_input, #cash_amount_input, #upi_amount_input, #pending_amount").val("");
                $("#cash_amount_error, #upi_amount_error").text("");

                // Hide all input containers first
                $("#amount_input_container, #pending_amount_container, #cash_amount_input_container, #upi_amount_input_container")
                    .addClass("d-none");

                // Remove required attributes initially
                $("#amount_input, #cash_amount_input, #upi_amount_input, #pending_amount").prop("required",
                    false);

                if (type === "full") {
                    invalid1 = false;
                    if (selectedMode === "cash") {
                        // $("#amount_input_container").removeClass("d-none");
                        // $("#amount_input").prop("required", true);

                    } else if (selectedMode === "online") {
                        // $("#amount_input_container").removeClass("d-none");
                        // $("#amount_input").prop("required", true);

                    } else if (selectedMode === "cashonline") {
                        // Show both if cash + online
                        $("#cash_amount_input_container").removeClass("d-none");
                        $("#upi_amount_input_container").removeClass("d-none");

                        $("#cash_amount_input").prop("required", true);
                        $("#upi_amount_input").prop("required", true);
                    }

                } else if (type === "partial") {
                    if (selectedMode === "cash" || selectedMode === "online") {
                        // Normal partial
                        $("#amount_input_container").removeClass("d-none");
                        $("#pending_amount_container").removeClass("d-none");

                        $("#amount_input").prop("required", true);
                        $("#pending_amount").prop("required", true);

                    } else if (selectedMode === "cashonline") {
                        // Partial with cash + online
                        $("#cash_amount_input_container").removeClass("d-none");
                        $("#upi_amount_input_container").removeClass("d-none");
                        $("#pending_amount_container").removeClass("d-none");

                        $("#cash_amount_input").prop("required", true);
                        $("#upi_amount_input").prop("required", true);
                        $("#pending_amount").prop("required", true);
                    }
                }
            });

            $("#amount_input").on("input", function() {
                const partialPaid = parseIndianNumber($(this).val());
                const payable = parseIndianNumber($("#grand-total").text());
                const pending = payable - partialPaid;

                $("#pending_amount").val(pending > 0 ? formatIndianNumber(pending) : "0.00");
            });
            let invalid1 = false;
            $("#cash_amount_input, #upi_amount_input").on("input", function() {
                const cashPaid = parseIndianNumber($("#cash_amount_input").val());
                const onlinePaid = parseIndianNumber($("#upi_amount_input").val());
                const payable = parseIndianNumber($("#grand-total").text());

                const totalPaid = cashPaid + onlinePaid;
                const pending = payable - totalPaid;

                // Update pending field
                $("#pending_amount").val(pending >= 0 ? formatIndianNumber(pending) : "0.00");


                // Validation
                if (pending < 0 || isNaN(pending)) {
                    invalid1 = true;
                    $("#pending_error").text("Enter correct amount cash + online"); // <-- your error span
                } else {
                    invalid1 = false;
                    $("#pending_error").text("");
                }
            });

            $("#amount_input").on("input", function() {
                const cashPaid = parseIndianNumber($("#amount_input").val());

                const payable = parseIndianNumber($("#grand-total").text());

                const totalPaid = cashPaid;
                const pending = payable - totalPaid;

                // Update pending field
                $("#pending_amount").val(pending > 0 ? formatIndianNumber(pending) : "0.00");

                // Validation
                if (pending <= 0 || isNaN(pending)) {
                    invalid1 = true;
                    $("#pending_error").text(
                        "Cannot enter more than payable amount"); // <-- your error span
                } else {
                    invalid1 = false;
                    $("#pending_error").text("");
                }
            });

            function parseIndianNumber(value) {
                return parseFloat(String(value).replace(/,/g, '')) || 0;
            }

            function formatIndianNumber(num) {
                num = parseFloat(num) || 0;
                return num.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function validateFullPayment() {
                const payable = parseIndianNumber($("#grand-total").text());
                const cash = parseIndianNumber($("#cash_amount_input").val());
                const upi = parseIndianNumber($("#upi_amount_input").val());
                const total = cash + upi;

                // Clear previous messages
                $("#cash_amount_error").text("");
                $("#upi_amount_error").text("");

                if ($("#paid_type").val() === "full") {
                    if (total !== payable) {
                        const msg = `Cash + UPI must equal payable amount (₹${payable})`;
                        $("#cash_amount_error").text(msg);
                        $("#upi_amount_error").text(msg);
                        return false;
                    }
                }

                return true;
            }


            // Bind validation on input
            $("#cash_amount_input, #upi_amount_input").on("input", function() {
                validateFullPayment();
            });
            // Add new form row dynamically
            $(document).on("click", ".add-row", function() {
                let row = `
                                            <div class="row form-row">

                                                <div class="col-lg-2 col-sm-12 col-6">
                                                    <div class="form-group">
                                                        <label>Category Name</label>
                                                        <select name="category_name[]" class="form-control category-select">
                                                            <option value="">Category Name</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}" data-price="{{ $category->price }}">{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="error text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-sm-12 col-6">
                                                    <div class="form-group">
                                                        <label>{{ $itemLabel }}</label>
                                                        <select name="product_name[]" class="form-control select2 product-select" disabled>
                                                            <option value="">{{ $itemLabel }}</option>
                                                            @foreach ($itemOptions as $product)
                                                                <option value="{{ $product['id'] }}" data-price="{{ $product['price'] }}" data-category="{{ $product['category_id'] }}" data-gst-option="{{ $product['gst_option'] ?? 'without_gst' }}" data-gst='@json($product['product_gst'] ?? null)'>{{ $product['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="product-gst-info mt-1"></div>
                                                        <span class="error text-danger product-error"></span>
                                                        @unless ($isRowMaterialPurchase)
                                                            <div class="row-material-hint"></div>
                                                            <div class="purchase-rate-wrap mt-1" style="display:none;">
                                                                <label class="mb-0" style="font-size:12px;">Add stock to</label>
                                                                <select class="form-control purchase-rate-select" style="font-size:12px;"></select>
                                                            </div>
                                                        @endunless
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-sm-12 col-6">
                                                    <div class="form-group">
                                                        <label>{{ $itemPriceLabel }}</label>
                                                        <input type="text"  inputmode="decimal" name="price[]" class="form-control price-input" placeholder="Enter Price" min="0" oninput="this.value = this.value < 0 ? 0 : this.value">
                                                        <span class="error text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-12 col-6">
                                                    <div class="form-group">
                                                        <label>Quantity</label>
                                                        <input type="number" name="quantity[]" class="form-control quantity-input" placeholder="Qty" value="1" min="0" oninput="this.value = this.value < 0 ? 0 : this.value">
                                                        <span class="error text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-12 col-6">
                                                    <div class="form-group">
                                                        <label>Disc %</label>
                                                        <input type="text" inputmode="decimal" name="product_discount[]" class="form-control product-discount-input" placeholder="0.00" value="0">
                                                        <span class="error text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-12 col-6">
                                                    <div class="form-group">
                                                        <label>Disc-Amt</label>
                                                        <input type="text" name="product_discount_amount[]" class="form-control product-discount-amount-input" placeholder="0.00" value="0" min="0">
                                                        <span class="error text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-sm-12 col-12">
                                                    <div class="form-group">
                                                        <label>Total Amount</label>
                                                        <input type="text" name="total[]" class="form-control total-input" placeholder="0" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-sm-12 add-row-btn">
                                                    <button type="button" class="btn btn-danger remove-row">-</button>
                                                </div>
                                            </div>`;

                let newRow = $(row).appendTo("#form-container");

                // Reinitialize Select2 for dynamically added elements
                newRow.find(".select2, .category-select").select2({
                    tags: true
                });

                // Scroll to the new row
                newRow[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            });

            // Remove form row dynamically
            $(document).on("click", ".remove-row", function() {
                $(this).closest(".form-row").remove();
                calculateTotal();
            });

            // Update Vendor Phone when Vendor is selected
            $("#vendor_name").on("change", function() {
                let phone = $(this).find(":selected").data("phone");
                $("#vendor_phone").val(phone ? phone : "");
            });



            // Update Product Price when selecting a Product
            $(document).ready(function() {

                $('.category-select').on('change', function() {
                    var selectedCategory = $(this).val();
                    var $row = $(this).closest('.form-row');
                    var $productDropdown = $(this).closest('.col-lg-3, .col-sm-12').siblings().find(
                        '.product-select');

                    // Clear current options
                    let currentValue = $productDropdown.val();
                    $productDropdown.empty();
                    $row.find(".product-gst-info").empty();

                    if (currentValue && isNaN(currentValue)) {
                        // preserve typed product
                        $productDropdown.append(
                            `<option value="${currentValue}" selected>${currentValue}</option>`
                        );
                    }


                    if (selectedCategory) {
                        // Enable dropdown when category is selected
                        $productDropdown.prop('disabled', false);

                        // Add default placeholder
                        $productDropdown.append('<option value="">{{ $itemLabel }}</option>');

                        // Filter products by category
                        var filteredProducts = products.filter(p => p.category_id ==
                            selectedCategory);

                        filteredProducts.forEach(function(product) {
                            // Ensure product_gst is a string if it's an object, or use it directly if it's already a string
                            let gstVal = (typeof product.product_gst === 'object') ? JSON
                                .stringify(product.product_gst) : product.product_gst;
                            $productDropdown.append(
                                `<option value="${product.id}" data-price="${product.price}" data-gst-option="${product.gst_option}" data-gst='${gstVal}'>${product.name}</option>`
                            );
                        });
                    } else {
                        // No category selected, disable product dropdown and add placeholder
                        $productDropdown.prop('disabled', true);
                        $productDropdown.append('<option value="">{{ $itemLabel }}</option>');
                    }

                    // Refresh Select2
                    $productDropdown.trigger('change.select2');
                });
            });



            $(document).ready(function() {
                // Delegated change event for category-select
                // $(document).on('change', '.category-select', function() {
                //     var selectedCategory = $(this).val();

                //     var $row = $(this).closest('.form-row'); // find the row of this select
                //     var $productDropdown = $row.find('.product-select');

                //     // Clear current options
                //     let currentValue = $productDropdown.val();
                //     $productDropdown.empty();
                //     $row.find(".product-gst-info").empty();

                //     if (currentValue && isNaN(currentValue)) {
                //         // preserve typed product
                //         $productDropdown.append(
                //             `<option value="${currentValue}" selected>${currentValue}</option>`
                //         );
                //     }

                //     if (selectedCategory) {
                //         $productDropdown.prop('disabled', false);

                //         $productDropdown.append('<option value="">Product Name</option>');

                //         // Filter products by category
                //         var filteredProducts = products.filter(p => p.category_id ==
                //             selectedCategory);

                //         filteredProducts.forEach(function(product) {
                //             // Ensure product_gst is a string if it's an object, or use it directly if it's already a string
                //             let gstVal = (typeof product.product_gst === 'object') ? JSON.stringify(product.product_gst) : product.product_gst;
                //             $productDropdown.append(
                //                 `<option value="${product.id}" data-price="${product.price}" data-gst-option="${product.gst_option}" data-gst='${gstVal}'>${product.name}</option>`
                //             );
                //         });
                //     } else {
                //         $productDropdown.prop('disabled', true);
                //         $productDropdown.append('<option value="">Product Name</option>');
                //     }

                //     // Refresh Select2
                //     $productDropdown.trigger('change.select2');
                // });
                function renderRowMaterialHint($row, selectedCategory) {
                    if (isRowMaterialPurchase) {
                        return;
                    }

                    const $hint = $row.find('.row-material-hint');
                    $hint.empty();

                    if (!selectedCategory || isNaN(selectedCategory)) {
                        return;
                    }

                    const filteredMaterials = rowMaterials.filter(material => material.category_id == selectedCategory);

                    if (!filteredMaterials.length) {
                        return;
                    }

                    const materialHtml = filteredMaterials.map(material =>
                        `<span class="row-material-chip">${material.name} (${material.quantity ?? 0})</span>`
                    ).join('');

                    $hint.html(`<strong>Row Materials:</strong> ${materialHtml}`);
                }

                $(document).on('change', '.category-select', function() {

                    let selectedCategory = $(this).val();
                    let $row = $(this).closest('.form-row');
                    let $productDropdown = $row.find('.product-select');
                    let $error = $row.find('.product-error');

                    // Reset
                    $productDropdown.empty();
                    $error.text('');
                    renderRowMaterialHint($row, selectedCategory);
                    $row.find(".product-gst-info").empty();

                    // 👉 NEW CATEGORY (typed by user)
                    if (selectedCategory && isNaN(selectedCategory)) {
                        $productDropdown.prop('disabled', false);

                        $productDropdown.append('<option value="">{{ $itemLabel }}</option>');
                        $productDropdown.select2({
                            tags: true
                        });

                        return; // stop here
                    }

                    // 👉 NO CATEGORY
                    if (!selectedCategory) {
                        $productDropdown.prop('disabled', true);
                        $productDropdown.append('<option value="">{{ $itemLabel }}</option>');
                        return;
                    }

                    // 👉 EXISTING CATEGORY
                    let filteredProducts = products.filter(p => p.category_id == selectedCategory);

                    $productDropdown.prop('disabled', false);
                        $productDropdown.append('<option value="">{{ $itemLabel }}</option>');

                    if (filteredProducts.length === 0) {
                        // ❌ Category has NO products
                        $error.text('No {{ strtolower($itemLabel) }} available for this category{{ $isRowMaterialPurchase ? '' : '. Row materials are shown below.' }}');
                        return;
                    }

                    // ✅ Category has products
                    filteredProducts.forEach(product => {
                        let gstVal = typeof product.product_gst === 'object' ?
                            JSON.stringify(product.product_gst) :
                            product.product_gst;

                        $productDropdown.append(
                            `<option value="${product.id}"
                data-price="${product.price}"
                data-gst-option="${product.gst_option}"
                data-gst='${gstVal}'>
                ${product.name}
            </option>`
                        );
                    });

                    $productDropdown.trigger('change.select2');
                });

                $('.form-row').each(function() {
                    renderRowMaterialHint($(this), $(this).find('.category-select').val());
                });




                // Delegated change event for product-select
                $(document).on('change', '.product-select', function() {

                    let selectedOption = $(this).find("option:selected");
                    let price = selectedOption.data("price") || 0;

                    let $row = $(this).closest('.form-row');

                    // set price
                    $row.find(".price-input").val(price);

                    // get quantity
                    let quantity = parseInt($row.find(".quantity-input").val()) || 0;

                    // calculate base total
                    let baseTotal = price * quantity;

                    // set total immediately
                    $row.find(".total-input").val(baseTotal.toFixed(2));

                    // update gst
                    updateProductGstInfo($row);

                    fillPurchaseRateSelect($row);

                    // recalc discount & gst properly
                    $row.find(".product-discount-input").trigger("input");

                    // update grand total
                    calculateTotal();
                });
                $(document).on("input", ".price-input", function() {
                    let val = this.value;

                    // allow only digits + one dot
                    val = val.replace(/[^0-9.]/g, '');
                    if ((val.match(/\./g) || []).length > 1) {
                        val = val.slice(0, -1);
                    }

                    this.value = val;

                    // calculate totals WITHOUT touching price again
                    let row = $(this).closest(".form-row");
                    let price = parseFloat(val) || 0;
                    let quantity = parseInt(row.find(".quantity-input").val()) || 0;

                    row.find(".total-input").val((price * quantity).toFixed(2));
                    updateProductGstInfo(row);
                    calculateTotal();
                });
                $(document).on("blur", ".price-input", function() {
                    let val = parseFloat(this.value);
                    if (!isNaN(val)) {
                        this.value = val.toFixed(2);
                    }
                });

                $(document).on("input", ".product-discount-input", function() {
                    let val = this.value.toString().replace(/[^0-9.]/g, '');
                    let dotIndex = val.indexOf('.');

                    if (dotIndex !== -1) {
                        val = val.slice(0, dotIndex + 1) + val.slice(dotIndex + 1).replace(/\./g, '');
                        let decimals = val.slice(dotIndex + 1);
                        if (decimals.length > 2) {
                            val = val.slice(0, dotIndex + 3);
                        }
                    }

                    this.value = val;
                });

                $(document).on("blur", ".product-discount-input", function() {
                    let val = parseFloat(this.value);
                    if (isNaN(val)) {
                        this.value = '0.00';
                        return;
                    }

                    val = Math.max(0, Math.min(100, val));
                    this.value = val.toFixed(2);
                });

                $(document).on("input", ".price-input, .quantity-input, .product-discount-input",
                    function() {
                        let row = $(this).closest(".form-row");
                        let price = parseFloat(row.find(".price-input").val()) || 0;
                        let quantity = parseInt(row.find(".quantity-input").val()) || 0;
                        let discountPercent = parseFloat(row.find(".product-discount-input").val()) ||
                            0;

                        let baseTotal = price * quantity;

                        // Calculate GST first (to match create order logic)
                        let gstAmount = 0;
                        if ($('#with_gst').is(':checked')) {
                            let selectedOption = row.find(".product-select option:selected");
                            let gstData = selectedOption.data("gst");
                            if (gstData) {
                                try {
                                    let taxes = gstData;
                                    if (typeof taxes === "string") taxes = JSON.parse(taxes);
                                    if (typeof taxes === "string") taxes = JSON.parse(taxes);
                                    if (Array.isArray(taxes)) {
                                        taxes.forEach(tax => {
                                            let taxRate = parseFloat(tax.tax_rate) || 0;
                                            gstAmount += (baseTotal * taxRate) / 100;
                                        });
                                    }
                                } catch (e) {}
                            }
                        }

                        let amountForDiscount = $('#with_gst').is(':checked') ?
                            baseTotal + gstAmount :
                            baseTotal;

                        let discountAmount = (amountForDiscount * discountPercent) / 100;
                        let finalRowTotal = amountForDiscount - discountAmount;

                        row.find(".product-discount-amount-input").val(discountAmount.toFixed(2));
                        row.find(".total-input").val(finalRowTotal.toFixed(2));
                        updateProductGstInfo(row);
                        calculateTotal();
                    });

                $(document).on("input", ".product-discount-amount-input", function() {
                    let row = $(this).closest(".form-row");
                    let price = parseFloat(row.find(".price-input").val()) || 0;
                    let quantity = parseInt(row.find(".quantity-input").val()) || 0;
                    let discountAmount = parseFloat(row.find(".product-discount-amount-input")
                        .val()) || 0;

                    let baseTotal = price * quantity;

                    // Calculate GST first (to match create order logic)
                    let gstAmount = 0;
                    if ($('#with_gst').is(':checked')) {
                        let selectedOption = row.find(".product-select option:selected");
                        let gstData = selectedOption.data("gst");
                        if (gstData) {
                            try {
                                let taxes = gstData;
                                if (typeof taxes === "string") taxes = JSON.parse(taxes);
                                if (typeof taxes === "string") taxes = JSON.parse(taxes);
                                if (Array.isArray(taxes)) {
                                    taxes.forEach(tax => {
                                        let taxRate = parseFloat(tax.tax_rate) || 0;
                                        gstAmount += (baseTotal * taxRate) / 100;
                                    });
                                }
                            } catch (e) {}
                        }
                    }

                    let amountForDiscount = $('#with_gst').is(':checked') ?
                        baseTotal + gstAmount :
                        baseTotal;
                    let discountPercent = 0;
                    if (amountForDiscount > 0) {
                        discountPercent = (discountAmount / amountForDiscount) * 100;
                        // if (discountPercent > 100) discountPercent = 100;
                        // if (discountAmount > totalWithGst) {
                        //     discountAmount = totalWithGst;
                        //     $(this).val(discountAmount.toFixed(2));
                        // }
                    }
                    let finalRowTotal = amountForDiscount - discountAmount;
                    // let finalRowTotal = totalWithGst - discountAmount;

                    row.find(".product-discount-input").val(discountPercent.toFixed(2));
                    row.find(".total-input").val(finalRowTotal.toFixed(2));
                    updateProductGstInfo(row);
                    calculateTotal();
                });
                $('input[name="gst_option"]').on('change', function() {

                    $(".form-row").each(function() {

                        let row = $(this);

                        // GST info update
                        updateProductGstInfo(row);

                        // 🔹 recalc discount percent → amount
                        row.find(".product-discount-input").trigger("input");

                        // 🔹 recalc discount amount → percent
                        row.find(".product-discount-amount-input").trigger("input");

                    });

                    calculateTotal();
                });

                function updateProductGstInfo($row) {
                    let selectedOption = $row.find(".product-select option:selected");
                    let gstOption = selectedOption.data("gst-option");
                    let gstData = selectedOption.data("gst");
                    let price = parseFloat($row.find(".price-input").val()) || 0;
                    let quantity = parseInt($row.find(".quantity-input").val()) || 0;
                    let discountPercent = parseFloat($row.find(".product-discount-input").val()) || 0;

                    let baseTotal = price * quantity;

                    let $gstContainer = $row.find(".product-gst-info");
                    $gstContainer.empty();

                    // Check global toggle first
                    if (!$('#with_gst').is(':checked')) {
                        return;
                    }

                    if (gstOption === 'with_gst' && gstData) {
                        try {
                            let taxes = gstData;
                            if (typeof taxes === "string") taxes = JSON.parse(taxes);
                            if (typeof taxes === "string") taxes = JSON.parse(taxes);

                            if (Array.isArray(taxes)) {
                                let totalTaxAmount = 0;
                                let taxDetails = taxes.map(tax => {
                                    let taxRate = parseFloat(tax.tax_rate) || 0;
                                    let taxAmount = (baseTotal * taxRate) / 100;
                                    totalTaxAmount += taxAmount;
                                    return `${tax.tax_name}: ${tax.tax_rate}%`;
                                }).join(', ');

                                let gstHtml = `<div style="font-size: 11px; color: #666; background: #f8f9fa; padding: 5px; border-radius: 4px; border-left: 3px solid #1b2850; margin-top: 5px;">
                                    <div><strong>Total GST: {{ $currencySymbol }}${totalTaxAmount.toFixed(2)}</strong></div>
                                    <div style="font-size: 10px;">(${taxDetails})</div>
                                </div>`;
                                $gstContainer.html(gstHtml);
                            }
                        } catch (e) {
                            // console.error("Error parsing GST data", e);
                        }
                    } else if (gstOption === 'without_gst') {
                        $gstContainer.html(
                            '<small class="text-muted" style="font-size: 11px;">No GST for this product</small>'
                        );
                    }
                }
            });





            // Calculate Total when Price or Quantity is updated


            function calculateTotal() {
                let totalBaseAmount = 0;
                let totalDiscountAmount = 0;
                let totalTaxAmount = 0;
                let products = [];
                let taxTotals = {};

                $(".form-row").each(function() {
                    let productOption = $(this).find(".product-select option:selected");
                    let productId = productOption.val();
                    let categoryId = $(this).find(".category-select").val();
                    let price = parseIndianNumber($(this).find(".price-input").val());
                    let quantity = parseInt($(this).find(".quantity-input").val()) || 0;
                    let discountPercent = parseIndianNumber($(this).find(".product-discount-input").val());

                    let baseTotal = price * quantity;
                    let itemGstAmount = 0;

                    // Product-wise GST calculation
                    if ($('#with_gst').is(':checked')) {
                        let gstData = productOption.data("gst");
                        if (gstData) {
                            try {
                                let taxes = gstData;
                                if (typeof taxes === "string") taxes = JSON.parse(taxes);
                                if (typeof taxes === "string") taxes = JSON.parse(taxes);

                                if (Array.isArray(taxes)) {
                                    taxes.forEach(tax => {
                                        let taxName = tax.tax_name;
                                        let taxRate = parseFloat(tax.tax_rate) || 0;
                                        let taxAmount = (baseTotal * taxRate) / 100;
                                        itemGstAmount += taxAmount;

                                        if (!taxTotals[taxName]) {
                                            taxTotals[taxName] = {
                                                id: tax.tax_id,
                                                rate: tax.tax_rate,
                                                amount: 0
                                            };
                                        }
                                        taxTotals[taxName].amount += taxAmount;
                                    });
                                }
                            } catch (e) {
                                // console.error("Error parsing GST data in calculation", e);
                            }
                        }
                    }

                    let totalWithGst = baseTotal + itemGstAmount;
                    let itemDiscountAmount = (totalWithGst * discountPercent) / 100;
                    let finalRowTotal = totalWithGst - itemDiscountAmount;

                    if (productId) {
                        let line = {
                            id: productId,
                            category_id: categoryId,
                            price: price,
                            quantity: quantity,
                            discount_percent: discountPercent,
                            discount_amount: itemDiscountAmount,
                            total: finalRowTotal,
                        };
                        let $rateWrap = $(this).find('.purchase-rate-wrap');
                        let ratePrice = $(this).find('.purchase-rate-select').val();
                        if ($rateWrap.is(':visible') && ratePrice !== undefined && ratePrice !== '') {
                            line.selling_rate_price = ratePrice;
                        }
                        products.push(line);
                    }

                    totalBaseAmount += baseTotal;
                    totalTaxAmount += itemGstAmount;
                    totalDiscountAmount += itemDiscountAmount;

                    $(this).find(".total-input").val(formatIndianNumber(finalRowTotal));
                });

                // **Update Summary Display**
                $("#total-product-amount").text(formatIndianNumber(totalBaseAmount));

                // let shipping = parseFloat($("#shipping").val()) || 0;
                let shipping = parseIndianNumber($("#shipping").val());

                if ($('#with_gst').is(':checked')) {
                    $('#gst-section').show();
                    $("#total-gst-amount").text(formatIndianNumber(totalTaxAmount));
                } else {
                    $('#gst-section').hide();
                    $("#total-gst-amount").text('0.00');
                }

                // Grand Total = Base + GST - ItemDiscounts + Shipping
                let grandTotal = (totalBaseAmount + totalTaxAmount - totalDiscountAmount) + shipping;

                $("#grand-total").text(formatIndianNumber(grandTotal));
                $("#shipping-amount").text(formatIndianNumber(shipping));
                $("#total-discount-amount").text(formatIndianNumber(totalDiscountAmount));
                $("#price-after-discount").text(formatIndianNumber(totalBaseAmount - totalDiscountAmount));

                return {
                    products: products,
                    totalProductAmount: totalBaseAmount,
                    totalAmount: totalBaseAmount,
                    discount: 0, // General discount removed/not used here
                    discountAmount: totalDiscountAmount,
                    taxAmount: totalTaxAmount,
                    taxTotals: taxTotals,
                    shipping: shipping,
                    grandTotal: grandTotal,
                    gstOption: $('#with_gst').is(':checked') ? 'with' : 'without'
                };
            }

            // Update calculation when any relevant input changes
            $(document).on("input",
                "#discount, #shipping, .price-input, .quantity-input, .product-discount-input, .product-discount-amount-input",
                function() {
                    calculateTotal();
                });

            function validatePaymentSection() {
                $(".error-payment_mode, .error-paid_type").text("");
                $("#amount_error, #cash_amount_error, #upi_amount_error, #pending_error").text("");

                const paymentMode = $("#payment_mode").val();
                const paidType = $("#paid_type").val();
                const grandTotal = parseIndianNumber($("#grand-total").text());
                const amount = parseIndianNumber($("#amount_input").val());
                // const amount = parseFloat($("#amount_input").val()) || 0;
                const cash = parseIndianNumber($("#cash_amount_input"));
                const online = parseIndianNumber($("#upi_amount_input"));
                const totalPaid = cash + online;

                // ❌ Payment mode missing
                if (!paymentMode) {
                    $(".error-payment_mode").text("Payment mode is required");
                    return false;
                }

                // ✅ Pending → skip everything
                if (paymentMode === "pending") {
                    return true;
                }

                // ❌ Paid type required
                if (!paidType) {
                    $(".error-paid_type").text("Paid type is required");
                    return false;
                }

                // 🔸 PARTIAL
                if (paidType === "partial") {

                    if (paymentMode === "cash" || paymentMode === "online") {
                        if (amount <= 0) {
                            $("#amount_error").text("Amount is required");
                            return false;
                        }
                        if (amount >= grandTotal) {
                            $("#amount_error").text("Amount must be less than total");
                            return false;
                        }
                    }

                    if (paymentMode === "cashonline") {
                        if (cash <= 0) {
                            $("#cash_amount_error").text("Cash amount is required");
                            return false;
                        }
                        if (online <= 0) {
                            $("#upi_amount_error").text("Online amount is required");
                            return false;
                        }
                        if (totalPaid >= grandTotal) {
                            $("#pending_error").text("Cash + Online must be less than total");
                            return false;
                        }
                    }
                }

                // 🔸 FULL
                if (paidType === "full" && paymentMode === "cashonline") {
                    if (totalPaid !== grandTotal) {
                        $("#pending_error").text("Cash + Online must equal total");
                        return false;
                    }
                }

                if ((paymentMode === "online" || paymentMode === "cashonline") && !$("#bank_id").val()) {
                    $(".error-bank_id").text("Please select a bank");
                    return false;
                }

                return true;
            }

            // Add Bank Modal handling
            const addBankModalEl = document.getElementById('addBankModal');
            const addBankModal = addBankModalEl ? new bootstrap.Modal(addBankModalEl) : null;

            function resetAddBankForm() {
                $('#addBankForm')[0].reset();
                $('#add_opening_balance').val('0');
                $('#add_bank_status').val('1');
                $('#addBankForm .text-danger').text('');
            }

            function upsertBankOption(bank) {
                if (!bank || !bank.id) return;
                const bankId = String(bank.id);
                const bankName = bank.bank_name || 'Unnamed Bank';
                const existing = $('#bank_id option[value="' + bankId + '"]');
                if (existing.length) {
                    existing.text(bankName);
                } else {
                    $('#bank_id').append(new Option(bankName, bankId));
                }
                $('#bank_id').val(bankId).trigger('change');
            }

            $('#openAddBankModal').on('click', function() {
                resetAddBankForm();
                if (addBankModal) addBankModal.show();
            });

            $('#addBankForm').on('submit', function(e) {
                e.preventDefault();
                $('#addBankForm .text-danger').text('');

                const formData = new FormData(this);
                const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
                if (selectedSubAdminId) formData.append('selectedSubAdminId', selectedSubAdminId);

                const saveBtn = $('#saveBankBtn');
                saveBtn.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: '/api/banks',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        upsertBankOption(response.data || null);
                        if (addBankModal) addBankModal.hide();
                        Swal.fire({ icon: 'success', title: 'Success', text: response.message || 'Bank added successfully.', confirmButtonText: 'OK' });
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || {};
                        $('#addBankNameError').text(errors.bank_name?.[0] || '');
                        $('#addAccountNumberError').text(errors.account_number?.[0] || '');
                        $('#addIfscCodeError').text(errors.ifsc_code?.[0] || '');
                        $('#addBranchNameError').text(errors.branch_name?.[0] || '');
                        $('#addOpeningBalanceError').text(errors.opening_balance?.[0] || '');
                        $('#addBankStatusError').text(errors.status?.[0] || '');
                        if (!Object.keys(errors).length) {
                            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to add bank.' });
                        }
                    },
                    complete: function() {
                        saveBtn.prop('disabled', false).text('Save Bank');
                    }
                });
            });

            // Form Validation
            function validateForm() {
                let isValid = true;

                // Clear previous errors
                $(".error").text("");

                // Validate Vendor
                if ($("#vendor_name").val() === "") {
                    $("#vendor_name").closest(".form-group").find(".error").text("Vendor is required.");
                    isValid = false;
                }
                if ($("#bill_no").val().trim() === "") {
                    $("#bill_no").closest(".form-group").find(".error").text("Bill number is required.");
                    isValid = false;
                }

                // Validate Product Fields
                $(".form-row").each(function() {
                    let productSelect = $(this).find(".product-select");
                    let categorySelect = $(this).find(".category-select");
                    let priceInput = $(this).find(".price-input");
                    let quantityInput = $(this).find(".quantity-input");
                    let discountInput = $(this).find(".product-discount-input");

                    if (productSelect.val() === "") {
                        productSelect.closest(".form-group").find(".error").text("Product is required.");
                        isValid = false;
                    }
                    if (categorySelect.val() === "") {
                        categorySelect.closest(".form-group").find(".error").text("Category is required.");
                        isValid = false;
                    }
                    if (priceInput.val().trim() === "" || parseFloat(priceInput.val()) <= 0) {
                        priceInput.closest(".form-group").find(".error").text("Valid price is required.");
                        isValid = false;
                    }
                    if (quantityInput.val().trim() === "" || parseInt(quantityInput.val()) <= 0) {
                        quantityInput.closest(".form-group").find(".error").text(
                            "Valid quantity is required.");
                        isValid = false;
                    }
                    if (discountInput.val().trim() !== "" && (parseFloat(discountInput.val()) < 0 ||
                            parseFloat(discountInput.val()) > 100)) {
                        discountInput.closest(".form-group").find(".error").text(
                            "Discount must be 0-100%.");
                        isValid = false;
                    }
                });

                // Validate Discount & Shipping
                let discount = parseFloat($("#discount").val()) || 0;
                // let shipping = parseFloat($("#shipping").val()) || 0;
                let shipping = parseIndianNumber($("#shipping").val());

                if (discount < 0 || discount > 100) {
                    $("#discount").closest(".form-group").find(".error").text("Discount must be between 0-100%.");
                    isValid = false;
                }
                if (shipping < 0) {
                    $("#shipping").closest(".form-group").find(".error").text("Shipping cost cannot be negative.");
                    isValid = false;
                }

                // Validate Status
                if ($("select[name='status']").val() === "") {
                    $("select[name='status']").closest(".form-group").find(".error").text(
                        "Please select a status.");
                    isValid = false;
                }

                return isValid;
            }


            // Submit Form via AJAX
            $(".btn-submit").click(function(e) {                e.preventDefault();

                let $btn = $(this);
                let originalContent = $btn.html();

                // Show spinner and disable button
                $btn.html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
                ).css("pointer-events", "none");

                if (!validateForm()) {
                    $btn.html(originalContent).css("pointer-events", "auto");
                    return;
                }

                if (!validatePaymentSection()) {
                    $btn.html(originalContent).css("pointer-events", "auto");
                    return;
                }

                let formData = calculateTotal();
                formData.vendor_id = $("#vendor_name").val();
                formData.vendor_phone = $("#vendor_phone").val();
                formData.status = $("select[name='status']").val();

                let taxes = [];
                let gstOption = $("input[name='gst_option']:checked").val();

                if (gstOption === "with") {
                    for (let taxName in formData.taxTotals) {
                        let taxData = formData.taxTotals[taxName];
                        taxes.push({
                            id: taxData.id,
                            name: taxName,
                            rate: taxData.rate,
                            amount: taxData.amount
                        });
                    }
                }
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                // Get GST option (radio)
                let gst_option = $("input[name='gst_option']:checked").val();
                // Get payment details
                let bill_no = $("#bill_no").val();
                let purchase_date = $("#purchase_date").val();
                let remark = $("#remark").val();
                let payment_mode = $("#payment_mode").val();
                let bank_id = $("#bank_id").val();
                let paid_type = $("#paid_type").val();
                let cash_amount = $("#cash_amount_input").val();
                let upi_amount = $("#upi_amount_input").val();
                let amount = null;

                if (paid_type === 'full') {
                    amount = formData.grandTotal;
                } else {
                    amount = $("#amount_input").val();
                }

                // console.log(amount);
                // console.log(paid_type);
                // console.log(formData.grandTotal);
                let remaining_amount = $("#pending_amount").val();

                $.ajax({
                    url: isEditPurchase && isRowMaterialPurchase ? `/api/row-material-purchase/${editPurchaseId}` : purchaseSubmitUrl,
                    type: isEditPurchase && isRowMaterialPurchase ? "PUT" : "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        "Authorization": "Bearer " + authToken,
                    },
                    contentType: "application/json",
                    data: JSON.stringify({
                        vendor_id: formData.vendor_id,
                        vendor_phone: formData.vendor_phone,
                        status: formData.status,
                        discount: formData.discount,
                        shipping: formData.shipping,
                        grand_total: formData.grandTotal,
                        products: formData.products,
                        taxes: taxes,
                        gst_option: gst_option,
                        payment_mode: payment_mode,
                        bank_id: bank_id,
                        bill_no: bill_no,
                        purchase_date: purchase_date,
                        paid_type: paid_type,
                        cash_amount: cash_amount,
                        upi_amount: upi_amount,
                        amount: amount,
                        remaining_amount: remaining_amount,
                        remark: remark,
                        selectedSubAdminId: selectedSubAdminId
                    }),
                    success: function(response) {
                        $btn.html(originalContent).css("pointer-events", "auto");

                        const purchaseId = response.purchase_id;

                        if (response.success) {
                            Swal.fire({
                                title: "Success",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43" // Set custom button color
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    if (isEditPurchase && isRowMaterialPurchase) {
                                        window.location.href = purchaseRedirectUrl;
                                    } else if (isRowMaterialPurchase) {
                                        window.location.href = purchaseRedirectUrl;
                                    } else {
                                        window.open("/purchase/invoice/pdf/" + purchaseId,
                                            "_blank");
                                        window.location.href = purchaseRedirectUrl + purchaseId;
                                    }
                                }
                            });
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        $btn.html(originalContent).css("pointer-events", "auto");
                        $(".error").text(""); // Clear previous

                        const errors = xhr.responseJSON?.errors || {};

                        if (errors.bill_no) {
                            $("#bill_no").closest(".form-group").find(".error").text(errors.bill_no[0]);
                            $("#bill_no").focus();
                            return;
                        }

                        if (errors.payment_mode) {
                            $(".error-payment_mode").text(errors.payment_mode[0]);
                            return;
                        }

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            let message = xhr.responseJSON.message;

                            if (message.includes("phone number")) {
                                $("#vendor_phone").closest(".form-group").find(".error").text(
                                    message);
                            } else {
                                Swal.fire({
                                    title: "Error",
                                    text: message,
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            }
                        } else {
                            alert("Something went wrong!");
                        }

                        // console.error(xhr.responseText); // For debugging
                    }

                });
            });
        });
    </script>
@endpush
