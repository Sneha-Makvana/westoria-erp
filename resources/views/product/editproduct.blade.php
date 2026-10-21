@extends('layout.app')

@section('title', 'Product Edit')

@section('content')
    <style>
        @media screen and (max-width: 768px) {
            .select2-container {
                width: 0 !important;
            }

            .form-group {
                margin-bottom: 10px !important
            }
        }

        .image-upload .image-uploads h4 {
            font-size: 12px !important;
        }

        a.btn.back-button {
            background: #ff9f43;
            color: #fff;
        }

        .form-label-icon {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            margin-bottom: 6px;
            text-decoration: none !important;
            border-bottom: 0 !important;
        }

        .form-label-icon i,
        .form-label-icon span {
            text-decoration: none !important;
            border-bottom: 0 !important;
        }

        .form-label-icon i {
            color: #ff9f43;
            font-size: 13px;
            width: 14px;
            text-align: center;
        }

        .form-label-icon .required {
            color: #dc3545;
            margin-left: 2px;
        }

        .barcode-input-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        #auto_generate_barcode_btn {
            flex-shrink: 0;
        }

        .barcode-input-group .form-control {
            flex: 1;
            min-width: 0;
        }

        .barcode-scan-btn {
            flex-shrink: 0;
            width: 38px;
            height: 38px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #ff9f43;
            background: #fff;
            color: #ff9f43;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }

        .barcode-scan-btn:hover,
        .barcode-scan-btn:focus {
            background: #ff9f43;
            color: #fff;
            outline: none;
        }

        #barcode-qr-reader {
            width: 100%;
            min-height: 300px;
        }

        .field-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 6px;
        }

        .quick-add-btn {
            border: 0;
            background: #ff9f43;
            color: #fff;
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
            white-space: nowrap;
        }

        .quick-add-btn:hover,
        .quick-add-btn:focus {
            background: #f38e27;
            color: #fff;
        }

        @media screen and (max-width: 576px) {
            .quick-add-btn {
                width: 28px;
                min-width: 28px;
                height: 28px;
                padding: 0;
                border-radius: 7px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 0;
            }

            .quick-add-btn::before {
                content: "+";
                font-size: 18px;
                font-weight: 700;
                line-height: 1;
            }
        }

        /* ===========================
       Dark Theme - Select2
    =========================== */

        [data-layout-mode="dark"] .select2-container--default .select2-selection--single,
        html[data-theme="dark"] .select2-container--default .select2-selection--single,
        .dark .select2-container--default .select2-selection--single,
        body.dark .select2-container--default .select2-selection--single {
            background: #111827 !important;
            border: 1px solid #374151 !important;
            color: #fff !important;
            height: 38px;
        }

        [data-layout-mode="dark"] .select2-selection__rendered,
        html[data-theme="dark"] .select2-selection__rendered,
        .dark .select2-selection__rendered,
        body.dark .select2-selection__rendered {
            color: #fff !important;
        }

        [data-layout-mode="dark"] .select2-selection__placeholder,
        html[data-theme="dark"] .select2-selection__placeholder,
        .dark .select2-selection__placeholder,
        body.dark .select2-selection__placeholder {
            color: #9ca3af !important;
        }

        [data-layout-mode="dark"] .select2-dropdown,
        html[data-theme="dark"] .select2-dropdown,
        .dark .select2-dropdown,
        body.dark .select2-dropdown {
            background: #1f2937 !important;
            border: 1px solid #374151 !important;
        }

        [data-layout-mode="dark"] .select2-results__option,
        html[data-theme="dark"] .select2-results__option,
        .dark .select2-results__option,
        body.dark .select2-results__option {
            color: #fff !important;
            background: #1f2937 !important;
        }

        [data-layout-mode="dark"] .select2-results__option--highlighted,
        html[data-theme="dark"] .select2-results__option--highlighted,
        .dark .select2-results__option--highlighted,
        body.dark .select2-results__option--highlighted {
            background: #ff9f43 !important;
            color: #fff !important;
        }

        [data-layout-mode="dark"] .select2-search__field,
        html[data-theme="dark"] .select2-search__field,
        .dark .select2-search__field,
        body.dark .select2-search__field {
            background: #111827 !important;
            color: #fff !important;
            border: 1px solid #374151 !important;
        }

        [data-layout-mode="dark"] .select2-selection__arrow b,
        html[data-theme="dark"] .select2-selection__arrow b,
        .dark .select2-selection__arrow b,
        body.dark .select2-selection__arrow b {
            border-top-color: #fff !important;

            }[data-layout-mode="dark"] .form-control,
            [data-layout-mode="dark"] textarea,
            [data-layout-mode="dark"] input[type="date"] {
                background: #111827 !important;
                color: #fff !important;
                border: 1px solid #374151 !important;
            }

            [data-layout-mode="dark"] .form-control::placeholder {
                color: #9ca3af !important;
            }

            /* Modal Buttons Dark Mode Fixes */
            html[data-theme="dark"] .modal .btn-secondary,
            [data-layout-mode="dark"] .modal .btn-secondary,
            .dark .modal .btn-secondary,
            body.dark .modal .btn-secondary {
                background-color: #374151 !important;
                border-color: #374151 !important;
                color: #ffffff !important;
            }

            html[data-theme="dark"] .modal .btn-close,
            [data-layout-mode="dark"] .modal .btn-close,
            .dark .modal .btn-close,
            body.dark .modal .btn-close {
                background-color: transparent !important;
                color: #ffffff !important;
            }

            html[data-theme="dark"] .modal .btn-close:hover,
            [data-layout-mode="dark"] .modal .btn-close:hover,
            .dark .modal .btn-close:hover,
            body.dark .modal .btn-close:hover {
                color: #ff9f43 !important;
            }

            /* Select2 Multiple tags dark mode fix */
            html[data-theme="dark"] .select2-container--default .select2-selection--multiple .select2-selection__choice,
            [data-layout-mode="dark"] .select2-container--default .select2-selection--multiple .select2-selection__choice,
            .dark .select2-container--default .select2-selection--multiple .select2-selection__choice,
            body.dark .select2-container--default .select2-selection--multiple .select2-selection__choice {
                background-color: #1f2937 !important;
                border-color: #374151 !important;
                color: #ffffff !important;
            }

            html[data-theme="dark"] .select2-container--default .select2-selection--multiple .select2-selection__choice__remove,
            [data-layout-mode="dark"] .select2-container--default .select2-selection--multiple .select2-selection__choice__remove,
            .dark .select2-container--default .select2-selection--multiple .select2-selection__choice__remove,
            body.dark .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
                color: #ffffff !important;
                border-right-color: #374151 !important;
            }

            html[data-theme="dark"] .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover,
            [data-layout-mode="dark"] .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover,
            .dark .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover,
            body.dark .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
                background-color: #374151 !important;
                color: #ff9f43 !important;
            }

/* Styling for the barcode chips */
.barcode-chip {
    display: inline-flex;
    align-items: center;
    background-color: #f1f3f5;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 13px;
    font-weight: 500;
    color: #495057;
}
.barcode-chip .remove-chip {
    margin-left: 6px;
    cursor: pointer;
    color: #868e96;
    font-weight: bold;
}
.barcode-chip .remove-chip:hover {
    color: #dc3545;
}
html[data-theme="dark"] .barcode-chip,
[data-layout-mode="dark"] .barcode-chip,
.dark .barcode-chip,
body.dark .barcode-chip {
    background-color: #1f2937;
    border-color: #374151;
    color: #ffffff;
}

.product-form-grid > .row {
    --bs-gutter-x: 1.5rem;
    align-items: start;
}
.product-form-grid .form-group {
    margin-bottom: 14px;
}
.product-form-grid .form-label-icon,
.product-form-grid .form-label-icon i,
.product-form-grid .form-label-icon span {
    text-decoration: none !important;
    border-bottom: 0 !important;
}
.product-image-box {
    border: 1px dashed #d9d9d9;
    border-radius: 10px;
    background: #fafbfc;
    padding: 10px;
    height: 100%;
}
.product-image-box .image-upload {
    min-height: 88px;
}
.product-image-box .image-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    align-items: flex-start;
}
.product-form-tabs {
    border-bottom: 1px solid #ececec;
    gap: 8px;
    margin-top: 8px;
}
.product-form-tabs .nav-link {
    color: #6e6b7b;
    font-weight: 700;
    border: 0;
    border-bottom: 2px solid transparent;
    background: transparent;
    padding: 10px 4px;
    margin-right: 18px;
}
.product-form-tabs .nav-link.active {
    color: #ff9f43;
    border-bottom-color: #ff9f43;
}
.product-tab-panel {
    border: 1px solid #f0f0f0;
    border-radius: 10px;
    padding: 16px 14px 8px;
    margin-top: 12px;
    background: #fff;
}
.product-form-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 18px;
}
@media screen and (max-width: 991px) {
    .product-image-box {
        min-height: 120px;
    }
}
@media screen and (max-width: 576px) {
    .product-tab-panel .form-label-icon {
        white-space: nowrap;
        font-size: 12px;
    }
}

    </style>
    <div class="content">
        {{-- <div class="page-header">
            <div class="page-title">
                <h4>Edit Product</h4>
            </div>
        </div> --}}
        <div class="page-header ">
            <div class="page-title">
                <h4>Edit Product</h4>
            </div>
            <div class="back-button">
                <a href="{{ route('product.list') }}" class="btn back-button"> <i class="fa-solid fa-arrow-left"></i>
                    Back</a></br>
                <span class="success_submit text-danger"></span>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="productForm">
                    <input type="hidden" name="product_id" id="product_id">
                    
                    <!-- Product Type Selection -->
                    @php
                        $setting = \App\Models\Setting::where('branch_id', auth()->user()->branch_id)->first() ?? \App\Models\Setting::first();
                        $enableVariants = $setting ? (bool)$setting->enable_variant_product : true;
                    @endphp
                    <div class="d-flex align-items-center flex-wrap gap-3 mb-3">
                        <label class="form-label-icon mb-0"><i class="fa-solid fa-boxes-stacked"></i> Product Type</label>
                        <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input" type="radio" name="item_type" id="type_normal" value="normal" {{ (!isset($product) || $product->item_type == 'normal' || empty($product->item_type)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_normal" style="cursor:pointer;">Normal Product</label>
                                </div>
                                @if($enableVariants)
                        <div class="form-check form-check-inline mb-0">
                                    <input class="form-check-input" type="radio" name="item_type" id="type_variant" value="variant" {{ (isset($product) && $product->item_type == 'variant') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_variant" style="cursor:pointer;">Variant Product</label>
                                </div>
                                @endif
                            </div>
                        @php
                            $currentUser = auth()->user();
                            $showBranchSelect = strtolower($currentUser->role ?? '') === 'admin' && empty($currentUser->branch_id);
                        @endphp

                    <div class="product-form-grid">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-6">
                            <div class="form-group">
                                    <label class="form-label-icon"><i class="fa-solid fa-box"></i> Product Name <span class="required">*</span></label>
                                <input type="text" name="name" id="name">
                                <span class="error_name text-danger"></span>
                            </div>
                        </div>
                            <div class="col-lg-4 col-md-6 col-6">
                            <div class="form-group">
                                    <div class="field-label-row">
                                        <label class="form-label-icon mb-0"><i class="fa-solid fa-language"></i> Hindi Name</label>
                                        <button type="button" class="quick-add-btn" id="translateNameBtn">Translate</button>
                                    </div>
                                <input type="text" name="name_hindi" id="name_hindi" placeholder="Hindi product name" style="font-family: 'Nirmala UI', 'Noto Sans Devanagari', sans-serif;">
                                <small class="text-muted">Saved on this product and used when printing in Hindi.</small>
                            </div>
                        </div>
                            <div class="col-lg-4 col-md-6 col-6">
                            <div class="form-group">
                                <div class="field-label-row">
                                        <label class="form-label-icon mb-0"><i class="fa-solid fa-layer-group"></i> Category <span class="required">*</span></label>
                                        <button type="button" class="quick-add-btn" id="openCategoryModal">Add Category</button>
                                </div>
                                <select class="select" name="category_id" id="category_id">
                                    <option value="">Choose Category</option>
                                </select>
                                <span class="error_category_id text-danger"></span>
                            </div>
                        </div>
                            <div class="col-lg-3 col-md-6 col-6">
                                <div class="form-group">
                                    <div class="field-label-row">
                                        <label class="form-label-icon mb-0"><i class="fa-solid fa-ruler-combined"></i> Unit <span class="required">*</span></label>
                                        <button type="button" class="quick-add-btn" id="openUnitModal">Add</button>
                                    </div>
                                    <select class="select" name="unit_id" id="unit_id">
                                        <option value="">Choose Unit</option>
                                    </select>
                                    <span class="error_unit_id text-danger"></span>
                                </div>
                            </div>

                            <div class="col-lg-5 col-md-6 col-12 normal-product-field">
                                <div class="form-group">
                                    <label class="form-label-icon"><i class="fa-solid fa-barcode"></i> Barcode</label>
                                    <input type="hidden" name="barcode" id="actual_barcodes" value="{{ $product->barcode ?? '' }}">
                                    <div class="barcode-input-group">
                                        <input type="text" id="barcode_input" class="form-control" placeholder="Enter Barcode (e.g. 11MH66)" oninput="this.value=this.value.replace(/[^A-Za-z0-9\-_.\/+]/g,'')">
                                        <button class="btn btn-secondary px-2" type="button" id="add_barcode_btn" style="height: 38px;">Add</button>
                                        <button class="btn px-2" type="button" id="auto_generate_barcode_btn" style="height: 38px; background: #ff9f43; color: #fff; white-space: nowrap;" title="Generate unique random barcodes">Auto</button>
                                        <button type="button" class="barcode-scan-btn" id="openBarcodeScanner" title="Scan Barcode">
                                            <i class="fa-solid fa-camera"></i>
                                        </button>
                                    </div>
                                    <div id="barcode_tags_container" class="d-flex flex-wrap gap-2 mt-2"></div>
                                    <span class="error_barcode text-danger"></span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-6">
                                <div class="form-group">
                                    <label class="form-label-icon"><i class="fa-solid fa-hashtag"></i> Item Code / SKU</label>
                                    <input type="number" class="form-control" name="SKU" id="SKU" value="{{ $product->SKU ?: $product->id }}" readonly>
                                    <span class="error_SKU text-danger"></span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-6">
                            <div class="form-group">
                                <div class="field-label-row">
                                    <label class="form-label-icon mb-0"><i class="fa-solid fa-tag"></i> Brand</label>
                                    <button type="button" class="quick-add-btn" id="openBrandModal">Add Brand</button>
                                </div>
                                <select class="select" name="brand_id" id="brand_id">
                                    <option value="">Choose Brand</option>
                                </select>
                                <span class="error_brand_id text-danger"></span>
                            </div>
                        </div>

                            <div class="col-lg-5 col-md-12 col-12">
                            <div class="form-group">
                                    <label class="form-label-icon"><i class="fa-solid fa-align-left"></i> Description</label>
                                    <textarea class="form-control" name="description" id="description" rows="2"></textarea>
                                    <span class="error_description text-danger"></span>
                            </div>
                        </div>
                            <div class="col-lg-4 col-md-6 col-6">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-hashtag"></i> HSN Code</label>
                                <input type="text" class="form-control" name="hsn_code" id="hsn_code">
                                <span class="error_hsn_code text-danger"></span>
                            </div>
                                @if($showBranchSelect)
                                <div class="form-group" id="branch_select_wrapper">
                                    <label class="form-label-icon"><i class="fa-solid fa-code-branch"></i> Select Branch</label>
                                    <select class="select" name="branch_id" id="branch_id">
                                        <option value="">Choose Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ $branch->id == ($product->branch_id ?? auth()->id()) ? 'selected' : '' }}>{{ $branch->name }}</option>
                                        @endforeach
                                </select>
                                    <span class="error_branch_id text-danger"></span>
                            </div>
                                @endif
                        </div>
                            <div class="col-lg-3 col-md-6 col-6">
                                <div class="form-group mb-0">
                                    <label class="form-label-icon d-flex justify-content-between align-items-center" style="width: 100%;">
                                        <span><i class="fa-solid fa-image"></i> Image</span>
                                        <span class="d-flex align-items-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="openProductImageCamera" title="Capture with camera" style="font-size: 11px; padding: 3px 7px;">
                                                <i class="fa-solid fa-camera"></i> Capture
                                            </button>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="forceRegenerateAiImage()" style="font-size: 11px; padding: 3px 7px;">AI Image</button>
                                        </span>
                                    </label>
                                    <div class="product-image-box">
                                        <div class="image-upload">
                                            <input type="file" name="images[]" id="images" accept="image/*" multiple>
                                            <input type="hidden" name="generated_image_path" id="generated_image_path">
                                            <input type="hidden" name="generated_image_paths" id="generated_image_paths" value="">
                                            <div class="image-uploads">
                                                <img src="{{ env('ImagePath') . '/admin/assets/img/icons/upload.svg' }}" alt="img">
                                                <h4>Add images (multiple allowed)</h4>
                                </div>
                            </div>
                                        <div class="image-preview" style="margin-top: 10px;"></div>
                                        <span class="error_images text-danger"></span>
                        </div>
                                </div>
                            </div>
                            </div>
                        </div>

                    <ul class="nav product-form-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#productTabPricing">Pricing</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#productTabStock">Stock</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#productTabMore">More</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="productTabPricing">
                            <div class="product-tab-panel">
                                <div class="row">
                                    <div class="col-lg-2 col-md-4 col-6 normal-product-field">
                            <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-indian-rupee-sign"></i> Price (Rate 1)</label>
                                            <input type="number" name="price" id="price" class="form-control decimal-6" min="0" step="0.01">
                                            <span class="error_price text-danger"></span>
                            </div>
                        </div>
                                    <div class="col-lg-2 col-md-4 col-6 normal-product-field">
                            <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-percent"></i> MRP Markup (%)</label>
                                            <input type="number" id="mrp_markup_percent" class="form-control decimal-2" min="0" step="0.01" placeholder="e.g. 10" autocomplete="off">
                                            <small class="text-muted d-block">Rate 100 + 10% = MRP 110</small>
                            </div>
                        </div>
                                    <div class="col-lg-2 col-md-4 col-6 normal-product-field">
                            <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-indian-rupee-sign"></i> MRP (Rate 1)</label>
                                            <input type="number" name="mrp" id="mrp" class="form-control decimal-6" min="0" step="0.01">
                                <span class="error_mrp text-danger"></span>
                            </div>
                        </div>
                                    <div class="col-lg-3 col-md-6 col-6 normal-product-field">
                            <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-percent"></i> Wholesale Markup (%)</label>
                                            <input type="number" id="wholesale_markup_percent" class="form-control decimal-2" min="0" step="0.01" placeholder="e.g. 10" autocomplete="off">
                                            <small class="text-muted d-block">Rate 100 − 10% = Wholesale 90</small>
                            </div>
                        </div>
                                    <div class="col-lg-3 col-md-6 col-12 normal-product-field">
                            <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-indian-rupee-sign"></i> Wholesale</label>
                                            <input type="number" name="wholesale_price" id="wholesale_price" class="form-control decimal-6" min="0" step="0.01" placeholder="0.00">
                                            <span class="error_wholesale_price text-danger"></span>
                            </div>
                        </div>
                                    <div class="col-lg-12 normal-product-field mb-2">
                                        <label class="form-label-icon"><i class="fa-solid fa-tags"></i> Extra MRP &amp; Rate</label>
                                        <div id="sellingRatesList" class="d-flex flex-column gap-2"></div>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addSellingRateBtn">+ Add MRP + Rate</button>
                                        <small class="text-muted d-block">Add more pairs: Rate + MRP % auto-calculates MRP, rounded to nearest 0/5 (91→90, 93→95, 97→100). Wholesale % comes off that rate (100 − 10% = 90). Qty is the stock for that price. Rate 1 stock is Quantity on the Stock tab.</small>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-6 normal-product-field">
                            <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-indian-rupee-sign"></i> Purchase Price</label>
                                            <input type="number" name="purchase_price" id="purchase_price" class="form-control decimal-2" min="0" step="0.01" placeholder="0.00">
                                            <span class="error_purchase_price text-danger"></span>
                            </div>
                        </div>
                                    <div class="col-lg-4 col-md-6 col-6">
                            <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-percent"></i> GST Option</label>
                                            <select class="select" name="gst_option" id="gst_option">
                                                <option value="">Choose GST Option</option>
                                                <option value="without_gst">Without GST</option>
                                                <option value="with_gst">With GST</option>
                                            </select>
                                            <span class="error_gst_option text-danger"></span>
                            </div>
                        </div>
                                    <div class="col-lg-4 col-md-6 col-6" id="gst_dropdown_container" style="display: none;">
                            <div class="form-group">
                                            <div class="field-label-row">
                                                <label class="form-label-icon mb-0"><i class="fa-solid fa-receipt"></i> Product GST</label>
                                                <button type="button" class="quick-add-btn" id="openTaxModal">Add Tax</button>
                            </div>
                                            <select class="select" name="product_gst[]" id="product_gst" multiple>
                                                <option value="">Choose GST Rate</option>
                                            </select>
                                            <span class="error_product_gst text-danger"></span>
                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-6 normal-product-field">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-percent"></i> Tax Rate (%)</label>
                                            <input type="number" name="tax_rate" id="tax_rate" class="form-control decimal-2" min="0" step="0.01">
                                <span class="error_tax_rate text-danger"></span>
                            </div>
                        </div>
                                    <div class="col-lg-4 col-md-6 col-6 normal-product-field">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-tags"></i> Discount Type</label>
                                <select class="select" name="discount_type" id="discount_type">
                                    <option value="">Select Type</option>
                                                <option value="percentage">Percentage</option>
                                                <option value="amount">Amount</option>
                                </select>
                                <span class="error_discount_type text-danger"></span>
                            </div>
                        </div>
                                    <div class="col-lg-4 col-md-6 col-6 normal-product-field">
                            <div class="form-group">
                                <label class="form-label-icon"><i class="fa-solid fa-tag"></i> Sale Discount</label>
                                            <input type="number" name="sale_discount" id="sale_discount" class="form-control decimal-2" min="0" step="0.01" value="0">
                                <span class="error_sale_discount text-danger"></span>
                            </div>
                        </div>
                                    <div class="col-lg-4 col-md-6 col-6 normal-product-field" style="display: flex; align-items: center;">
                            <div class="form-group mb-0 w-100 mt-4 pt-1">
                                <label class="custom_check w-100">
                                                <input type="checkbox" name="inclusive_of_tax" id="inclusive_of_tax" value="1">
                                    <span class="checkmark"></span> Inclusive Of Tax
                                </label>
                            </div>
                        </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="productTabStock">
                            <div class="product-tab-panel">
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 col-6 normal-product-field">
                            <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-cubes"></i> Quantity (Rate 1)</label>
                                            <input type="number" name="quantity" id="quantity" class="form-control" min="0" step="0.01">
                                            <span class="error_quantity text-danger"></span>
                            </div>
                        </div>
                                    <div class="col-lg-4 col-md-6 col-6 serial-no-container" style="display: none;">
                                        <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-fingerprint"></i> IMEI No <a href="javascript:void(0)" class="edit-serial-btn text-warning ml-1" title="Edit Serial Numbers"><i class="fas fa-edit"></i></a></label>
                                            <div class="serial-status text-muted" style="font-size: 11px; line-height: 1.2;">0/0 IMEI numbers added</div>
                                            <input type="hidden" id="imei_no" name="imei_no" value="[]">
                                            <span class="error_imei_no text-danger"></span>
                                </div>
                            </div>
                                    <div class="col-lg-4 col-md-6 col-6">
                                <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-warehouse"></i> Stock</label>
                                            <select class="select" name="availablility" id="availablility" disabled>
                                                <option value="in_stock">In Stock</option>
                                                <option value="out_stock">Out Of Stock</option>
                                    </select>
                                            <span class="error_availablility text-danger"></span>
                                </div>
                            </div>
                                    <div class="col-lg-4 col-md-6 col-6">
                                <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-toggle-on"></i> Status</label>
                                            <select class="select" name="status" id="status">
                                                <option value="active">Active</option>
                                                <option value="inactive">InActive</option>
                                            </select>
                                            <span class="error_status text-danger"></span>
                                </div>
                            </div>
                                    <div class="col-lg-4 col-md-6 col-6 normal-product-field">
                            <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-battery-quarter"></i> Minimum Stock Qty</label>
                                            <input type="number" name="minimum_stock_qty" id="minimum_stock_qty" class="form-control decimal-2" min="0" step="0.01" value="0">
                                            <span class="error_minimum_stock_qty text-danger"></span>
                            </div>
                        </div>
                                    <div class="col-lg-4 col-md-6 col-6 normal-product-field">
                            <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-location-dot"></i> Item Location</label>
                                            <input type="text" name="item_location" id="item_location" class="form-control">
                                            <span class="error_item_location text-danger"></span>
                            </div>
                        </div>
                                    <div class="col-lg-4 col-md-6 col-6 normal-product-field">
                            <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-tag"></i> Batch No</label>
                                            <input type="text" name="batch_no" id="batch_no" class="form-control">
                                            <span class="error_batch_no text-danger"></span>
                            </div>
                        </div>
                                    <div class="col-lg-4 col-md-6 col-6 normal-product-field">
                            <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-asterisk"></i> Other</label>
                                            <input type="text" name="specific_qty" id="specific_qty" class="form-control">
                                            <span class="error_specific_qty text-danger"></span>
                            </div>
                        </div>
                            </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="productTabMore">
                            <div class="product-tab-panel">
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 col-6 normal-product-field">
                            <div class="form-group">
                                            <label class="form-label-icon mb-0"><i class="fa-solid fa-ruler"></i> Secondary Unit</label>
                                            <select class="select" name="secondary_unit_id" id="secondary_unit_id">
                                                <option value="">Choose Unit</option>
                                                @if(isset($units))
                                                    @foreach($units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                                    @endforeach
                                                @endif
                                </select>
                                            <span class="error_secondary_unit_id text-danger"></span>
                            </div>
                        </div>
                                    <div class="col-lg-4 col-md-6 col-6 normal-product-field" id="conversion_rate_container">
                            <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-arrow-right-arrow-left"></i> Conversion Rate</label>
                                            <input type="number" name="conversion_rate" id="conversion_rate" class="form-control decimal-4" min="0" step="0.0001">
                                            <small class="text-muted" style="font-size: 11px;">Base Unit = Rate * Secondary Unit</small>
                                            <span class="error_conversion_rate text-danger"></span>
                            </div>
                        </div>
                                    @if(\App\Models\Setting::first()?->rental)
                                    <div class="col-lg-4 col-md-6 col-12" style="display: flex; align-items: center;">
                                        <div class="form-group mb-0 w-100 mt-4 pt-1">
                                            <label class="custom_check w-100">
                                                <input type="checkbox" name="is_rent_available" id="is_rent_available" value="1">
                                                <span class="checkmark"></span> Available for Rent
                                            </label>
                            </div>
                        </div>
                                    <div class="col-lg-4 col-md-6 col-12 rental-fields" style="display: none;">
                            <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-clock"></i> Rent Type</label>
                                            <select class="select" name="rent_period" id="rent_period">
                                    <option value="">Select</option>
                                                <option value="hour">Hour</option>
                                                <option value="day">Day</option>
                                                <option value="week">Week</option>
                                                <option value="month">Month</option>
                                                <option value="year">Year</option>
                                </select>
                                            <span class="error_rent_period text-danger"></span>
                            </div>
                        </div>
                                    <div class="col-lg-4 col-md-6 col-12 rental-fields" style="display: none;">
                            <div class="form-group">
                                            <label class="form-label-icon"><i class="fa-solid fa-indian-rupee-sign"></i> Rent Price</label>
                                            <input type="number" name="rent_price" id="rent_price" class="form-control decimal-2" min="0" step="0.01">
                                            <span class="error_rent_price text-danger"></span>
                            </div>
                        </div>
                                    @endif
                            </div>
                        </div>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-3 variant-product-field">
                            <div class="card">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Product Variants</h5>
                                    <button type="button" class="btn btn-primary btn-sm" id="addVariantBtn">+ Add More</button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered border" id="variantsTable">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Size <span class="text-danger"></span></th>
                                                    <th>Color <span class="text-danger"></span></th>
                                                    <th>Qty <span class="text-danger"></span></th>
                                                    <th>Purchase Price <span class="text-danger"></span></th>
                                                    <th>Price <span class="text-danger"></span></th>
                                                    <th>Barcode</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="variantsBody">
                                                @if(isset($product->variants) && $product->variants->count() > 0)
                                                    @foreach($product->variants as $index => $variant)
                                                        <tr>
                                                            <td><input type="text" name="variants[{{ $index }}][size]" class="form-control" value="{{ $variant->size }}" required placeholder="e.g. M"></td>
                                                            <td><input type="text" name="variants[{{ $index }}][color]" class="form-control" value="{{ $variant->color }}" required placeholder="e.g. Red"></td>
                                                            <td><input type="number" name="variants[{{ $index }}][qty]" class="form-control" value="{{ $variant->qty }}" min="0" required placeholder="0"></td>
                                                            <td><input type="number" name="variants[{{ $index }}][purchase_price]" class="form-control" value="{{ number_format($variant->purchase_price ?? 0, 2, '.', '') }}" min="0" step="0.01" placeholder="0.00"></td>
                                                            <td><input type="number" name="variants[{{ $index }}][price]" class="form-control" value="{{ number_format($variant->price ?? 0, 2, '.', '') }}" min="0" step="0.01" required placeholder="0.00"></td>
                                                            <td>
                                                                <input type="hidden" name="variants[{{ $index }}][barcode]" class="variant-actual-barcode" value="{{ $variant->barcode }}">
                                                                <div class="barcode-input-group mb-1">
                                                                    <input type="text" class="form-control variant-barcode-input" placeholder="Barcode" oninput="this.value=this.value.replace(/[^A-Za-z0-9\-_.\/+]/g,'')">
                                                                    <button class="btn btn-secondary px-2 add-variant-barcode-btn" type="button" style="height: 38px; padding-left: 6px !important; padding-right: 6px !important;">Add</button>
                                                                    <button type="button" class="barcode-scan-btn variant-scan-btn" title="Scan Barcode">
                                                                        <i class="fa-solid fa-camera"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="variant-barcode-tags-container d-flex flex-wrap gap-2"></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-danger btn-sm remove-variant" {{ $product->variants->count() == 1 ? 'disabled' : '' }}><i class="fa fa-trash"></i></button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td><input type="text" name="variants[0][size]" class="form-control" required placeholder="e.g. M"></td>
                                                        <td><input type="text" name="variants[0][color]" class="form-control" required placeholder="e.g. Red"></td>
                                                        <td><input type="number" name="variants[0][qty]" class="form-control" min="0" required placeholder="0"></td>
                                                        <td><input type="number" name="variants[0][purchase_price]" class="form-control" min="0" step="0.01" placeholder="0.00"></td>
                                                        <td><input type="number" name="variants[0][price]" class="form-control" min="0" step="0.01" required placeholder="0.00"></td>
                                                        <td>
                                                            <input type="hidden" name="variants[0][barcode]" class="variant-actual-barcode">
                                                            <div class="barcode-input-group mb-1">
                                                                <input type="text" class="form-control variant-barcode-input" placeholder="Barcode" oninput="this.value=this.value.replace(/[^A-Za-z0-9\-_.\/+]/g,'')">
                                                                <button class="btn btn-secondary px-2 add-variant-barcode-btn" type="button" style="height: 38px; padding-left: 6px !important; padding-right: 6px !important;">Add</button>
                                                                <button type="button" class="barcode-scan-btn variant-scan-btn" title="Scan Barcode">
                                                                    <i class="fa-solid fa-camera"></i>
                                                                </button>
                                                            </div>
                                                            <div class="variant-barcode-tags-container d-flex flex-wrap gap-2"></div>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger btn-sm remove-variant" disabled><i class="fa fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <div class="d-none">
                        <input type="hidden" name="label_code" id="label_code" value="{{ $product->label_code ?? '' }}">
                        <input type="number" name="cost_price" id="cost_price" class="form-control decimal-2" min="0" step="0.01" placeholder="0.00">
                        <input type="number" name="landing_cost" id="landing_cost" class="form-control decimal-2" min="0" step="0.01" placeholder="0.00">
                        <input type="text" class="form-control" name="product_code" id="product_code" placeholder="e.g. 10025">
                        <input type="text" class="form-control" name="supplier_code" id="supplier_code" placeholder="Supplier I/M Code">
                        <input type="text" class="form-control" name="international_code" id="international_code" placeholder="International I/M Code">
                        <select class="select" name="serial_no_status" id="serial_no_status">
                            <option value="">Select</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                        <input type="text" class="form-control" name="non_inventory_type" id="non_inventory_type" placeholder="e.g. *None">
                        <select class="select" name="stock_validation_status" id="stock_validation_status">
                            <option value="">Select</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                        <select class="select" name="goods_type" id="goods_type">
                            <option value="Goods">Goods</option>
                            <option value="Services">Services</option>
                        </select>
                        <select class="select" name="discount_print_status" id="discount_print_status">
                            <option value="">Select</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                        <input type="date" class="form-control" name="item_created_on" id="item_created_on">
                    </div>

                    <div class="product-form-actions">
                            <a href="javascript:void(0);" class="btn btn-submit me-2 submit">Submit</a>
                        <a href="{{ route('product.list') }}" class="btn btn-cancel">Cancel</a>
                            <span class="success_submit text-danger"></span>
                        </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const qtyInput = document.getElementById('quantity');
                            if (qtyInput) {
                                qtyInput.addEventListener('input', function() {
                                    const qty = parseInt(this.value);
                                    if (!isNaN(qty) && qty === 0) {
                                        $('#availablility').val('out_stock').trigger('change');
                                    } else {
                                        $('#availablility').val('in_stock').trigger('change');
                                    }
                                });
                            }
                            document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(tab) {
                                tab.addEventListener('shown.bs.tab', function() {
                                    if (window.jQuery) {
                                        jQuery('#productForm .select').each(function() {
                                            if (jQuery(this).data('select2')) {
                                                jQuery(this).next('.select2-container').css('width', '100%');
                                            }
                                        });
                                    }
                                });
                            });
                        });
                    </script>
                </form>

            </div>

        </div>

    @include('product.partials.product-image-capture')

    <!-- Scanner Modal -->
    <div class="modal fade" id="barcodeScannerModal" tabindex="-1" aria-labelledby="barcodeScannerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="barcodeScannerModalLabel">Scan Barcode</h5>
                    <button type="button" class="btn-close bg-white text-black" data-bs-dismiss="modal"
                        aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <div id="barcode-qr-reader"></div>
                    <div id="barcode-scan-message" class="text-center mt-2 small text-muted">
                        Initializing camera...
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

        <!-- Custom Modal -->
        <div class="modal fade" id="customModal" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="customModalLabel">Add New</h5>
                        <button type="button" class="btn-close bg-white text-black" data-bs-dismiss="modal"
                            aria-label="Close" onclick="modalOpen=false;">x</button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="custom_name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="custom_name" placeholder="Enter name">
                            <span class="error_custom_name text-danger"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            onclick="modalOpen=false;">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveCustomBtn">Save</button>
                    </div>
                    <span class="text-danger error_model"></span>
                </div>
            </div>
        </div>
        <!-- Tax Modal -->
        <div class="modal fade" id="taxModal" tabindex="-1" aria-labelledby="taxModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="taxModalLabel">Add TAX</h5>
                        <button type="button" class="btn-close bg-white text-black" data-bs-dismiss="modal"
                            aria-label="Close" onclick="modalOpen=false;">x</button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tax_name" class="form-label">Tax Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tax_name" placeholder="Enter tax name">
                            <span class="error_tax_name text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <label for="tax_rate" class="form-label">Tax Rate(%)<span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="tax_rate" placeholder="Enter tax rate"
                                min="0" step="0.01">
                            <span class="error_tax_rate text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <label for="tax_status" class="form-label">Status</label>
                            <select class="form-control" id="tax_status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <span class="error_tax_status text-danger"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" style="background-color: #ff9f43; color: white; border: none;"
                            class="btn btn-primary" id="saveTaxBtn">Confirm</button>
                        <button type="button" style="background-color: #1e293b; color: white; border: none;"
                            class="btn btn-secondary" data-bs-dismiss="modal" onclick="modalOpen=false;">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Serial Number Modal -->
        <div class="modal fade" id="serialNumberModal" tabindex="-1" aria-labelledby="serialNumberModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="serialNumberModalLabel">Enter Serial Numbers (IMEI)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body" id="serialNumberModalBody">
                        <!-- Dynamic inputs will be appended here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveSerialNumbersBtn">Save</button>
                    </div>
                </div>
            </div>
        </div>


    @endsection
    @push('js')
        <script src="{{ image_path('admin/assets/js/product-images-manager.js') }}"></script>
        <script>
            const IMAGE_PATH = "{{ rtrim(env('ImagePath'), '/') }}";
        </script>

        <script>
            $(document).ready(function() {
                if (window.ProductImagesManager) {
                    window.ProductImagesManager.init({ imagePath: IMAGE_PATH });
                }
                // Main Product Barcode Logic
                let mainBarcodes = [];
                
                function initMainBarcodes() {
                    let existingVal = $('#actual_barcodes').val();
                    if (existingVal && existingVal.trim() !== '') {
                        mainBarcodes = existingVal.split(',').filter(b => b.trim() !== '');
                    } else {
                        // Auto-generate a unique 5-digit barcode
                        let autoBarcode = String(Math.floor(10000 + Math.random() * 90000));
                        mainBarcodes = [autoBarcode];
                        $('#actual_barcodes').val(autoBarcode);
                    }
                    renderMainChips();
                }

                function renderMainChips() {
                    let container = $('#barcode_tags_container');
                    container.empty();
                    mainBarcodes.forEach(function(barcode) {
                        let chipHtml = `
                            <div class="barcode-chip">
                                ${barcode} 
                                <span class="remove-chip" data-barcode="${barcode}" data-target="main">&times;</span>
                            </div>
                        `;
                        container.append(chipHtml);
                    });
                    $('#actual_barcodes').val(mainBarcodes.join(','));
                }

                $('#add_barcode_btn').on('click', function(e) {
                    e.preventDefault();
                    let val = $('#barcode_input').val().trim().replace(/[^A-Za-z0-9\-_.\/+]/g, '');
                    if (val !== "") {

                        if (!mainBarcodes.includes(val)) {
                            mainBarcodes.push(val);
                            renderMainChips();
                        }
                        $('#barcode_input').val('');
                    }
                });

                $('#auto_generate_barcode_btn').on('click', function(e) {
                    e.preventDefault();
                    var btn = $(this);
                    if (btn.prop('disabled')) {
                        return;
                    }
                    btn.prop('disabled', true);
                    $('.error_barcode').text('');
                    $.ajax({
                        url: '/api/products/generate-barcodes',
                        type: 'POST',
                        data: {
                            count: 1,
                            exclude: mainBarcodes
                        },
                        headers: {
                            "Authorization": "Bearer " + localStorage.getItem("authToken")
                        },
                        success: function(response) {
                            var codes = response.barcodes || [];
                            codes.forEach(function(code) {
                                code = String(code);
                                if (!mainBarcodes.includes(code)) {
                                    mainBarcodes.push(code);
                                }
                            });
                            renderMainChips();
                            if (!codes.length) {
                                $('.error_barcode').text('Could not generate a unique barcode. Try again.');
                            }
                        },
                        error: function() {
                            $('.error_barcode').text('Could not generate barcode.');
                        },
                        complete: function() {
                            btn.prop('disabled', false);
                        }
                    });
                });

                $('#barcode_input').on('keypress', function(e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        $('#add_barcode_btn').click();
                    }
                });

                $(document).on('click', '.remove-chip', function() {
                    let target = $(this).data('target');
                    let barcodeToRemove = String($(this).data('barcode'));
                    
                    if (target === 'main') {
                        mainBarcodes = mainBarcodes.filter(b => b !== barcodeToRemove);
                        renderMainChips();
                    } else if (target === 'variant') {
                        let row = $(this).closest('td');
                        let actualInput = row.find('.variant-actual-barcode');
                        let currentBarcodes = actualInput.val() ? actualInput.val().split(',') : [];
                        currentBarcodes = currentBarcodes.filter(b => b !== barcodeToRemove);
                        actualInput.val(currentBarcodes.join(','));
                        renderVariantChips(row, currentBarcodes);
                    }
                });

                // Variant Product Barcode Logic
                function renderVariantChips(row, barcodes) {
                    let container = row.find('.variant-barcode-tags-container');
                    container.empty();
                    barcodes.forEach(function(barcode) {
                        let chipHtml = `
                            <div class="barcode-chip">
                                ${barcode} 
                                <span class="remove-chip" data-barcode="${barcode}" data-target="variant">&times;</span>
                            </div>
                        `;
                        container.append(chipHtml);
                    });
                }
                
                function initVariantBarcodes() {
                    $('#variantsBody tr').each(function() {
                        let row = $(this);
                        let actualInput = row.find('.variant-actual-barcode');
                        if(actualInput.length > 0) {
                            let val = actualInput.val();
                            if(val) {
                                let barcodes = val.split(',').filter(b => b.trim() !== '');
                                renderVariantChips(row, barcodes);
                            }
                        }
                    });
                }

                $(document).on('click', '.add-variant-barcode-btn', function(e) {
                    e.preventDefault();
                    let row = $(this).closest('td');
                    let input = row.find('.variant-barcode-input');
                    let actualInput = row.find('.variant-actual-barcode');
                    let val = input.val().trim().replace(/[^A-Za-z0-9\-_.\/+]/g, '');
                    
                    if (val !== "") {

                        let currentBarcodes = actualInput.val() ? actualInput.val().split(',') : [];
                        if (!currentBarcodes.includes(val)) {
                            currentBarcodes.push(val);
                            actualInput.val(currentBarcodes.join(','));
                            renderVariantChips(row, currentBarcodes);
                        }
                        input.val('');
                    }
                });

                $(document).on('keypress', '.variant-barcode-input', function(e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        $(this).siblings('.add-variant-barcode-btn').click();
                    }
                });
                
                // Initialize on load
                initMainBarcodes();
                initVariantBarcodes();
            });
            
            function normalizeSelectText(value) {
                return String(value ?? '').trim().replace(/\s+/g, ' ').toLowerCase();
            }

            function findExistingOptionByText($select, text) {
                const normalizedText = normalizeSelectText(text);
                if (!normalizedText) return $();

                let $match = $();
                $select.find('option').each(function() {
                    const $option = $(this);
                    if (!$option.val() || !$option.data('from-api')) return;

                    if (normalizeSelectText($option.text()) === normalizedText) {
                        $match = $option;
                        return false;
                    }
                });

                return $match;
            }

            function selectExistingOptionIfDuplicateTag($select, text) {
                const $existingOption = findExistingOptionByText($select, text);
                if (!$existingOption.length) return false;

                $select.val($existingOption.val()).trigger('change');
                return true;
            }

            function createTagOnlyWhenNew(selector) {
                return function(params) {
                    const term = $.trim(params.term || '');
                    if (!term) return null;

                    return findExistingOptionByText($(selector), term).length ? null : {
                        id: term,
                        text: term,
                        newTag: true
                    };
                };
            }

            function isWarrantyCategorySelected() {
                return normalizeSelectText($('#category_id option:selected').text()) === 'warranty';
            }

            function applyWarrantyDefaults() {
                if (!isWarrantyCategorySelected()) return;

                if ($('#quantity').val() === '') {
                    $('#quantity').val('0');
                }

                if ($('#price').val() === '') {
                    $('#price').val('0');
                }

                $('.error_quantity, .error_price').text('');
            }

            $(document).on("click", ".remove-image", function(e) {
                e.preventDefault(); // ❗ prevent page reload and form submission

                var authToken = localStorage.getItem("authToken");
                var productId = $(this).data("id");
                var imageName = $(this).data("image");
                var imageContainer = $(this).parent();

                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you really want to delete this image?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "Cancel",
                    confirmButtonColor: "#ff9f43",
                    cancelButtonColor: "#6c757d"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/api/remove-product-image",
                            type: "POST",
                            headers: {
                                "Authorization": "Bearer " + authToken,
                            },
                            data: {
                                product_id: productId,
                                image: imageName,
                                _token: $('meta[name="csrf-token"]').attr("content")
                            },
                            success: function(response) {
                                if (response.success) {
                                    imageContainer.remove();
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: "Product image removed successfully!",
                                        icon: "success",
                                        confirmButtonColor: "#ff9f43"
                                    }).then(() => {

                                    });
                                } else {
                                    Swal.fire("Error", "Error removing image.", "error");
                                }
                            },
                            error: function() {
                                Swal.fire("Error", "Something went wrong.", "error");
                            }
                        });
                    }
                });
            });
            $(document).on("input", "#price", function() {
                let value = parseFloat($(this).val());
                let errorSpan = $(".error_price");

                if (value < 0) {
                    $(this).val(""); // clear invalid value
                    errorSpan.text("Price cannot be negative.");
                } else {
                    errorSpan.text(""); // clear error if valid
                }
                if (typeof window.calculateMrpFromMarkupPercent === 'function') {
                    window.calculateMrpFromMarkupPercent();
                }
                if (typeof window.calculateWholesaleFromMarkupPercent === 'function') {
                    window.calculateWholesaleFromMarkupPercent();
                }
            });

            function formatMrpCalcValue(value) {
                if (!Number.isFinite(value)) {
                    return '';
                }
                if (Math.abs(value - Math.round(value)) < 0.001) {
                    return String(Math.round(value));
                }
                return value.toFixed(2);
            }

            function roundMrpRetailNumber(value) {
                const n = Math.round(parseFloat(value));
                if (!Number.isFinite(n) || n < 0) {
                    return null;
                }
                if (n === 0) {
                    return 0;
                }
                const base = Math.floor(n / 10) * 10;
                const last = n % 10;
                if (last <= 2) {
                    return base;
                }
                if (last <= 6) {
                    return base + 5;
                }
                return base + 10;
            }

            function formatCalculatedMrp(value) {
                const rounded = roundMrpRetailNumber(value);
                if (rounded === null) {
                    return '';
                }
                return formatMrpCalcValue(rounded);
            }

            window.calculateMrpFromMarkupPercent = function() {
                const price = parseFloat($('#price').val());
                const markupRaw = $('#mrp_markup_percent').val();
                if (markupRaw === '' || markupRaw === null) {
                    return;
                }
                const markup = parseFloat(markupRaw);
                if (!Number.isFinite(price) || price < 0 || !Number.isFinite(markup)) {
                    return;
                }
                $('#mrp').val(formatCalculatedMrp(price * (1 + markup / 100)));
            };

            window.syncMrpMarkupPercentFromMrp = function() {
                const price = parseFloat($('#price').val());
                const mrpRaw = $('#mrp').val();
                if (mrpRaw === '' || mrpRaw === null) {
                    $('#mrp_markup_percent').val('');
                    return;
                }
                const mrp = parseFloat(mrpRaw);
                if (!Number.isFinite(price) || price <= 0 || !Number.isFinite(mrp)) {
                    return;
                }
                $('#mrp_markup_percent').val(formatMrpCalcValue(((mrp - price) / price) * 100));
            };

            $(document).on('input', '#mrp_markup_percent', window.calculateMrpFromMarkupPercent);
            $(document).on('input', '#mrp', window.syncMrpMarkupPercentFromMrp);

            function formatWholesaleFromRate(price, markupPercent) {
                const p = parseFloat(price);
                const markup = parseFloat(markupPercent);
                if (!Number.isFinite(p) || p < 0 || !Number.isFinite(markup)) {
                    return '';
                }
                return formatMrpCalcValue(Math.max(0, p * (1 - markup / 100)));
            }

            function computeWholesaleMarkupPercent(price, wholesale) {
                const p = parseFloat(price);
                const w = parseFloat(wholesale);
                if (!Number.isFinite(p) || p <= 0 || !Number.isFinite(w)) {
                    return '';
                }
                return formatMrpCalcValue(((p - w) / p) * 100);
            }

            window.calculateWholesaleFromMarkupPercent = function() {
                const price = parseFloat($('#price').val());
                const markupRaw = $('#wholesale_markup_percent').val();
                if (markupRaw === '' || markupRaw === null) {
                    return;
                }
                const markup = parseFloat(markupRaw);
                if (!Number.isFinite(price) || price < 0 || !Number.isFinite(markup)) {
                    return;
                }
                $('#wholesale_price').val(formatWholesaleFromRate(price, markup));
            };

            window.syncWholesaleMarkupPercentFromWholesale = function() {
                const price = parseFloat($('#price').val());
                const wholesaleRaw = $('#wholesale_price').val();
                if (wholesaleRaw === '' || wholesaleRaw === null) {
                    $('#wholesale_markup_percent').val('');
                    return;
                }
                const wholesale = parseFloat(wholesaleRaw);
                if (!Number.isFinite(price) || price <= 0 || !Number.isFinite(wholesale)) {
                    return;
                }
                $('#wholesale_markup_percent').val(computeWholesaleMarkupPercent(price, wholesale));
            };

            $(document).on('input', '#wholesale_markup_percent', window.calculateWholesaleFromMarkupPercent);
            $(document).on('input', '#wholesale_price', window.syncWholesaleMarkupPercentFromWholesale);

            function computeSellingRateMarkupPercent(price, mrp) {
                const p = parseFloat(price);
                const m = parseFloat(mrp);
                if (!Number.isFinite(p) || p <= 0 || !Number.isFinite(m)) {
                    return '';
                }
                return formatMrpCalcValue(((m - p) / p) * 100);
            }

            $(document).on('input', '.selling-rate-price, .selling-rate-markup', function() {
                const $row = $(this).closest('.selling-rate-row');
                const price = parseFloat($row.find('.selling-rate-price').val());
                const markupRaw = $row.find('.selling-rate-markup').val();
                if (markupRaw === '' || markupRaw === null) {
                    return;
                }
                const markup = parseFloat(markupRaw);
                if (!Number.isFinite(price) || price < 0 || !Number.isFinite(markup)) {
                    return;
                }
                $row.find('.selling-rate-mrp').val(formatCalculatedMrp(price * (1 + markup / 100)));
            });

            $(document).on('input', '.selling-rate-price, .selling-rate-wholesale-markup', function() {
                const $row = $(this).closest('.selling-rate-row');
                const price = parseFloat($row.find('.selling-rate-price').val());
                const markupRaw = $row.find('.selling-rate-wholesale-markup').val();
                if (markupRaw === '' || markupRaw === null) {
                    return;
                }
                const markup = parseFloat(markupRaw);
                if (!Number.isFinite(price) || price < 0 || !Number.isFinite(markup)) {
                    return;
                }
                $row.find('.selling-rate-wholesale').val(formatWholesaleFromRate(price, markup));
            });

            $(document).on('input', '.selling-rate-wholesale', function() {
                const $row = $(this).closest('.selling-rate-row');
                const price = parseFloat($row.find('.selling-rate-price').val());
                const wholesaleRaw = $row.find('.selling-rate-wholesale').val();
                if (wholesaleRaw === '' || wholesaleRaw === null) {
                    $row.find('.selling-rate-wholesale-markup').val('');
                    return;
                }
                const wholesale = parseFloat(wholesaleRaw);
                if (!Number.isFinite(price) || price <= 0 || !Number.isFinite(wholesale)) {
                    return;
                }
                $row.find('.selling-rate-wholesale-markup').val(computeWholesaleMarkupPercent(price, wholesale));
            });

            $(document).on('input', '.selling-rate-mrp', function() {
                const $row = $(this).closest('.selling-rate-row');
                const price = parseFloat($row.find('.selling-rate-price').val());
                const mrpRaw = $row.find('.selling-rate-mrp').val();
                if (mrpRaw === '' || mrpRaw === null) {
                    $row.find('.selling-rate-markup').val('');
                    return;
                }
                const mrp = parseFloat(mrpRaw);
                if (!Number.isFinite(price) || price <= 0 || !Number.isFinite(mrp)) {
                    return;
                }
                $row.find('.selling-rate-markup').val(formatMrpCalcValue(((mrp - price) / price) * 100));
            });
            $(document).on("change", "#category_id", applyWarrantyDefaults);

            // Rental fields toggle logic
            $(document).on("change", "#is_rent_available", function() {
                if ($(this).is(":checked")) {
                    $(".rental-fields").show();
                } else {
                    $(".rental-fields").hide();
                    $("#rent_period").val("").trigger("change");
                    $("#rent_price").val("");
                }
            });

            $(document).ready(function() {
                $(document).on('click', '.submit', function(e) {
                    e.preventDefault();

                    const itemType = $('input[name="item_type"]:checked').val();
                    
                    if (itemType === 'normal') {
                        if ($('#quantity').val() === '' || $('#quantity').val() === null || $('#quantity').val() === undefined) {
                            $('#quantity').val('0');
                        }
                        $('.error_quantity').text('');

                        let price = $('#price').val();
                        let hasError = false;

                        if (price === '' || price === null || price === undefined) {
                            $('.error_price').text('Price is required.');
                            hasError = true;
                        } else {
                            $('.error_price').text('');
                        }

                        if (hasError) {
                            return;
                        }
                    }

                    if (itemType === 'variant') {
                        // Validate variants and calculate total quantity & base price
                        let totalQty = 0;
                        let basePrice = 0;
                        let validVariantsCount = 0;
                        let variantValidationError = '';

                        $('#variantsBody tr').each(function(index) {
                            let size = $(this).find('input[name$="[size]"]').val();
                            let color = $(this).find('input[name$="[color]"]').val();
                            let qtyStr = $(this).find('input[name$="[qty]"]').val();
                            let priceStr = $(this).find('input[name$="[price]"]').val();
                            
                            if (size === undefined) return; // Not a valid variant row

                            size = size.trim();
                            color = color.trim();
                            
                            if (size === '' || color === '' || qtyStr === '' || priceStr === '') {
                                variantValidationError = `Please fill all fields (Size, Color, Qty, Price) in Variant Row ${index + 1}.`;
                                return false; // break the loop
                            }

                            let qty = parseFloat(qtyStr) || 0;
                            let price = parseFloat(priceStr) || 0;
                            
                            if (validVariantsCount === 0) {
                                basePrice = price;
                            }
                            totalQty += qty;
                            validVariantsCount++;
                        });

                        if (variantValidationError !== '') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: variantValidationError
                            });
                            return; // Prevent submission
                        }

                        if (validVariantsCount === 0) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: 'Please add at least one product variant.'
                            });
                            return;
                        }

                        // Update hidden main quantity and price fields
                        $('#quantity').val(totalQty);
                        $('#price').val(basePrice);
                    }

                    var $btn = $(this); // cache the button
                    var originalText = $btn.html();

                    // Show loading text and disable the button
                    $btn.html(
                        '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...'
                    ).prop('disabled', true);

                    var authToken = localStorage.getItem("authToken");
                    applyWarrantyDefaults();
                    if (window.ProductImagesManager) {
                        window.ProductImagesManager.prepareForSubmit();
                    }
                    let formData = new FormData($('#productForm')[0]);

                    if (isWarrantyCategorySelected()) {
                        formData.set('quantity', $('#quantity').val() || '0');
                        formData.set('price', $('#price').val() || '0');
                    } else {
                        formData.set('quantity', $('#quantity').val() === '' ? '0' : $('#quantity').val());
                    }

                    // Remove product_gst if "without_gst" is selected
                    const gstOption = $('#gst_option').val();
                    if (gstOption !== 'with_gst') {
                        formData.delete('product_gst[]');
                    }
                    $('.error_name, .error_category_id, .error_brand_id, .error_SKU, .error_hsn_code, .error_gst_option, .error_product_gst, .error_quantity, .error_unit_id, .error_price, .error_status, .error_barcode, .error_description, .error_availablility, .error_images, .error_imei_no, .error_cost_price, .error_rent_period, .error_rent_price')
                        .text('');

                    $.ajax({
                        url: "/api/updateProduct",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            "Authorization": "Bearer " + authToken,
                        },
                        success: function(response) {
                            $btn.html(originalText).prop('disabled', false);

                            if (response.status) {
                                Swal.fire({
                                    title: "Success!",
                                    text: "Product updated successfully!",
                                    icon: "success",
                                    confirmButtonText: "OK",
                                    confirmButtonColor: "#ff9f43" // your custom color
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href =
                                            "{{ route('product.list') }}";
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            $btn.html(originalText).prop('disabled', false);

                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    let errorKey = key.split('.')[0];
                                    $('.error_' + errorKey).text(value[0]);
                                });
                            } else {
                                Swal.fire("Error", "Something went wrong.", "error");
                            }
                        }
                    });
                });
            });

            $(document).on('click', '#translateNameBtn', function() {
                var english = ($('#name').val() || '').trim();
                if (!english) {
                    Swal.fire('Translate', 'Enter the product name first.', 'warning');
                    return;
                }
                var $btn = $(this);
                $btn.prop('disabled', true).text('...');
                $.ajax({
                    url: '/api/translate-product-name',
                    method: 'GET',
                    data: { text: english },
                    headers: { Authorization: 'Bearer ' + localStorage.getItem('authToken') },
                    success: function(res) {
                        if (res && res.status && res.hindi) {
                            $('#name_hindi').val(res.hindi);
                        } else {
                            Swal.fire('Translate', (res && res.message) ? res.message : 'Could not translate this product name.', 'warning');
                        }
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Could not translate this product name.';
                        Swal.fire('Translate', msg, 'warning');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Translate');
                    }
                });
            });

            $(document).ready(function() {
                const sub_branch_id = localStorage.getItem('selectedSubAdminId');
                var authToken = localStorage.getItem("authToken");

                const currentUserId = @json(auth()->id());
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                const isSubBranch = selectedSubAdminId
                    && selectedSubAdminId !== 'null'
                    && selectedSubAdminId !== 'undefined'
                    && String(selectedSubAdminId) !== String(currentUserId);

                if (isSubBranch) {
                    $('#branch_select_wrapper').hide();
                    $('#branch_id').val(selectedSubAdminId).trigger('change');
                }

                let url = window.location.pathname;
                let productId = url.split("/").pop(); // e.g. /product/edit/5 -> 5
                let sellingRateIndex = 0;
                function addSellingRateRow(name, price, mrp, qty, wholesale) {
                    sellingRateIndex += 1;
                    let i = sellingRateIndex;
                    let safeName = (name || '').replace(/"/g, '&quot;');
                    let priceVal = (price !== undefined && price !== null && price !== '') ? price : '';
                    let mrpVal = (mrp !== undefined && mrp !== null && mrp !== '') ? mrp : '';
                    let qtyVal = (qty !== undefined && qty !== null && qty !== '') ? qty : '';
                    let wholesaleVal = (wholesale !== undefined && wholesale !== null && wholesale !== '') ? wholesale : '';
                    let markupVal = computeSellingRateMarkupPercent(priceVal, mrpVal);
                    let wholesaleMarkupVal = computeWholesaleMarkupPercent(priceVal, wholesaleVal);
                    $('#sellingRatesList').append(`<div class="selling-rate-row border rounded p-2">
                        <div class="row g-2 align-items-end">
                        <div class="col-md-2 col-6">
                            <label class="form-label mb-1" style="font-size:12px;font-weight:600;">Rate</label>
                            <input type="number" name="selling_rates[${i}][price]" class="form-control selling-rate-price" min="0" step="0.01" placeholder="Rate" value="${priceVal}">
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label mb-1" style="font-size:12px;font-weight:600;">MRP Markup (%)</label>
                            <input type="number" class="form-control selling-rate-markup decimal-2" min="0" step="0.01" placeholder="MRP %" value="${markupVal}">
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label mb-1" style="font-size:12px;font-weight:600;">MRP</label>
                            <input type="number" name="selling_rates[${i}][mrp]" class="form-control selling-rate-mrp" min="0" step="0.01" placeholder="MRP" value="${mrpVal}">
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label mb-1" style="font-size:12px;font-weight:600;">Qty</label>
                            <input type="number" name="selling_rates[${i}][qty]" class="form-control selling-rate-qty" min="0" step="0.01" placeholder="Qty" value="${qtyVal}">
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label mb-1" style="font-size:12px;font-weight:600;">Name</label>
                            <input type="text" name="selling_rates[${i}][name]" class="form-control selling-rate-name" placeholder="Rate 2" value="${safeName}">
                        </div>
                        <div class="col-md-2 col-2">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-selling-rate">Remove</button>
                        </div>
                        </div>
                        <div class="row g-2 align-items-end mt-1">
                        <div class="col-md-2 col-6">
                            <label class="form-label mb-1" style="font-size:12px;font-weight:600;">Wholesale Markup (%)</label>
                            <input type="number" class="form-control selling-rate-wholesale-markup decimal-2" min="0" step="0.01" placeholder="e.g. 10" value="${wholesaleMarkupVal}">
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label mb-1" style="font-size:12px;font-weight:600;">Wholesale</label>
                            <input type="number" name="selling_rates[${i}][wholesale]" class="form-control selling-rate-wholesale" min="0" step="0.01" placeholder="Wholesale" value="${wholesaleVal}">
                        </div>
                        </div>
                    </div>`);
                }
                $('#addSellingRateBtn').on('click', function() {
                    addSellingRateRow('', '', '');
                });
                $(document).on('click', '.remove-selling-rate', function() {
                    $(this).closest('.selling-rate-row').remove();
                });

                function get_unit(selectedId = null) {
                    let url = sub_branch_id ?
                        `/api/get-units?sub_branch_id=${sub_branch_id}` :
                        `/api/get-units`;

                    $.ajax({
                        url: url,
                        type: "GET",
                        headers: {
                            "Authorization": "Bearer " + authToken
                        },
                        success: function(response) {
                            if (response.status) {
                                let select = $("#unit_id");
                                select.empty().append('<option value="">Choose Unit</option>');

                                $.each(response.data, function(key, item) {
                                    select.append(
                                        $('<option>', {
                                            value: item.id,
                                            text: item.unit_name
                                        }).data('from-api', true)
                                    );
                                });

                                if (selectedId) {
                                    select.val(selectedId).trigger('change');
                                }
                            }
                        }
                    });
                }

                // 🔹 Fetch Brands
                function get_brand(selectedId = null) {
                    let url = sub_branch_id ?
                        `/api/get-brand?sub_branch_id=${sub_branch_id}` :
                        `/api/get-brand`;

                    $.ajax({
                        url: url,
                        type: "GET",
                        headers: {
                            "Authorization": "Bearer " + authToken
                        },
                        success: function(response) {
                            if (response.status) {
                                let select = $("#brand_id");
                                select.empty().append('<option value="">Choose Brand</option>');

                                $.each(response.data, function(key, item) {
                                    select.append(
                                        $('<option>', {
                                            value: item.id,
                                            text: item.name
                                        }).data('from-api', true)
                                    );
                                });

                                if (selectedId) {
                                    select.val(selectedId).trigger('change');
                                }
                            }
                        }
                    });
                }

                // 🔹 Fetch Categories
                function get_category(selectedId = null) {
                    let url = sub_branch_id ?
                        `/api/get-category?sub_branch_id=${sub_branch_id}` :
                        `/api/get-category`;

                    $.ajax({
                        url: url,
                        type: "GET",
                        headers: {
                            "Authorization": "Bearer " + authToken
                        },
                        success: function(response) {
                            if (response.status) {
                                let select = $("#category_id");
                                select.empty().append('<option value="">Choose Category</option>');

                                $.each(response.data, function(key, item) {
                                    select.append(
                                        $('<option>', {
                                            value: item.id,
                                            text: item.name
                                        }).data('from-api', true)
                                    );
                                });

                                if (selectedId) {
                                    select.val(selectedId).trigger('change');
                                }
                            }
                        }
                    });
                }

                // 🔹 Select2 Init
                $('#brand_id').select2({
                    placeholder: "Select or Add Brand",
                    tags: true,
                    createTag: createTagOnlyWhenNew('#brand_id'),
                    width: '100%',
                    allowClear: true
                });
                $('#category_id').select2({
                    placeholder: "Select or Add Category",
                    tags: true,
                    createTag: createTagOnlyWhenNew('#category_id'),
                    width: '100%',
                    allowClear: true
                });
                $('#product_type_id').select2({
                    placeholder: "Select or Add Product Type",
                    tags: true,
                    createTag: createTagOnlyWhenNew('#product_type_id'),
                    width: '100%',
                    allowClear: true
                });
                $('#unit_id').select2({
                    placeholder: "Select or Add Unit",
                    tags: true,
                    createTag: createTagOnlyWhenNew('#unit_id'),
                    width: '100%',
                    allowClear: true
                });

                // 🔹 Load Product and prefill form
                $.ajax({
                    url: `/api/edit_product/${productId}`,
                    method: 'GET',
                    headers: {
                        Authorization: `Bearer ${authToken}`
                    },
                    success: function(response) {
                        if (!response.status) {
                            alert("Product not found!");
                            return;
                        }

                        const product = response.product;

                        // // console.log("Fetched product data:", product);

                        // ✅ Populate form fields
                        $('#product_id').val(product.id);
                        $('#expiry_date').val(product.expiry_date);
                        $('#name').val(product.name);
                        $('#name_hindi').val(product.name_hindi || '');
                        $('#SKU').val(product.SKU);
                        $('#hsn_code').val(product.hsn_code);
                        $('#quantity').val(product.quantity);
                        $('#price').val(product.price);
                        $('#mrp').val(product.mrp);
                        if (typeof window.syncMrpMarkupPercentFromMrp === 'function') {
                            window.syncMrpMarkupPercentFromMrp();
                        }
                        let resolvedWholesalePrice = product.wholesale_price;
                        if ((resolvedWholesalePrice === null || resolvedWholesalePrice === undefined || resolvedWholesalePrice === '') && product.label_reference) {
                            const legacyWholesale = parseFloat(product.label_reference);
                            if (Number.isFinite(legacyWholesale)) {
                                resolvedWholesalePrice = legacyWholesale;
                            }
                        }
                        $('#wholesale_price').val(
                            resolvedWholesalePrice !== null && resolvedWholesalePrice !== undefined && resolvedWholesalePrice !== ''
                                ? resolvedWholesalePrice
                                : ''
                        );
                        if (typeof window.syncWholesaleMarkupPercentFromWholesale === 'function') {
                            window.syncWholesaleMarkupPercentFromWholesale();
                        }
                        $('#sellingRatesList').empty();
                        sellingRateIndex = 0;
                        let extraRates = product.selling_price_options || product.selling_prices || [];
                        const wholesalePriceForSkip = parseFloat(resolvedWholesalePrice);
                        extraRates.forEach(function(rate, idx) {
                            if (!rate) return;
                            let rateName = (rate.name || '').toString();
                            let ratePrice = parseFloat(rate.price);
                            if (idx === 0 && Math.abs(ratePrice - parseFloat(product.price || 0)) < 0.0001) {
                                return;
                            }
                            if (/wholesale/i.test(rateName)) {
                                return;
                            }
                            if (Number.isFinite(wholesalePriceForSkip) && Math.abs(ratePrice - wholesalePriceForSkip) < 0.0001) {
                                return;
                            }
                            addSellingRateRow(rateName, ratePrice, rate.mrp, rate.qty, rate.wholesale);
                        });
                        $('#barcode').val(product.barcode);
                        $('#description').val(product.description);
                        $('#availablility').val(product.availablility).trigger('change');
                        $('#status').val(product.status).trigger('change');
                        $('#warranty').val(product.warranty).trigger('change');
                        $('#voltage').val(product.voltage).trigger('change');
                        $('#capacity').val(product.capacity).trigger('change');
                        $('#gst_option').val(product.gst_option || 'without_gst').trigger('change');
                        // ✅ New fields from Excel import
                        $('#cost_price').val(product.cost_price);
                        $('#landing_cost').val(product.landing_cost);
                        $('#product_code').val(product.product_code);
                        $('#supplier_code').val(product.supplier_code);
                        $('#international_code').val(product.international_code);
                        $('#serial_no_status').val(product.serial_no_status || '').trigger('change');
                        $('#non_inventory_type').val(product.non_inventory_type);
                        $('#stock_validation_status').val(product.stock_validation_status || '').trigger(
                            'change');
                        if (product.item_type) {
                            $(`input[name="item_type"][value="${product.item_type}"]`).prop('checked', true).trigger('change');
                        }
                        $('#discount_print_status').val(product.discount_print_status || '').trigger(
                            'change');
                        $('#imei_no').val(product.imei_no || '[]');
                        if (product.item_created_on) {
                            $('#item_created_on').val(product.item_created_on.substring(0, 10));
                        }
                        // Clear old preview first
                        $('.image-preview').empty();
                        if (window.ProductImagesManager) {
                            window.ProductImagesManager.clearPending();
                            window.ProductImagesManager.clearGeneratedPaths();
                        }

                        if (product.images) {
                            let images = JSON.parse(product.images);
                            if (window.ProductImagesManager) {
                                window.ProductImagesManager.renderServerImages(images, product.id);
                            }
                        }
                        // ✅ Call dropdown loaders with preselected value
                        get_unit(product.unit_id);
                        get_brand(product.brand_id);
                        get_category(product.category_id);
                        // get_product_type(product.product_type_id);

                        // Load GST if with_gst is selected
                        if (product.gst_option === 'with_gst') {
                            let gstIds = [];
                            if (product.product_gst) {
                                let gstData = JSON.parse(product.product_gst);
                                gstIds = gstData.map(item => item.tax_id);
                            }
                            fetch_gst_rates_edit(gstIds);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        // window.location.href = "{{ route('product.list') }}";
                    }
                });

                let modalOpen = false;
                let currentType = null;
                let currentCustomValue = null;

                // 🔹 Add Category / Brand / Unit button click → open modal directly
                $(document).on('click', '#openCategoryModal, #openBrandModal, #openUnitModal', function() {
                    if (modalOpen) return;

                    const buttonId = $(this).attr('id');
                    const typeMap = {
                        openCategoryModal: 'category',
                        openBrandModal: 'brand',
                        openUnitModal: 'unit'
                    };

                    const type = typeMap[buttonId];
                    if (!type) return;

                    modalOpen = true;
                    currentType = type;
                    currentCustomValue = null;
                    $('#customModalLabel').text("Add New " + type.charAt(0).toUpperCase() + type.slice(1));
                    $('#custom_name').val('');
                    $(".error_custom_name").text('');
                    $(".error_model").text('');
                    $('#customModal').modal('show');
                });

                // 🔹 Listen for Select2 New Tag
                $('#brand_id, #category_id, #unit_id').on('select2:select', function(e) {
                    if (modalOpen) return;

                    let data = e.params.data;
                    let selectedValue = data.id;
                    let selectedText = data.text;
                    let targetId = $(this).attr('id');

                    // A value is "custom" (user-typed tag) if the option was NOT loaded from the API
                    const $opt = $(this).find(`option[value="${selectedValue}"]`);
                    const isNewTag = !$opt.data('from-api');

                    if (isNewTag) {
                        if (selectExistingOptionIfDuplicateTag($(this), selectedText)) return;

                        currentCustomValue = selectedValue;
                        modalOpen = true;

                        if (targetId === "brand_id") {
                            currentType = "brand";
                        } else if (targetId === "category_id") {
                            currentType = "category";
                        } else if (targetId === "unit_id") {
                            currentType = "unit";
                        }

                        // Open Modal
                        $('#customModalLabel').text("Add New " + currentType.charAt(0).toUpperCase() +
                            currentType.slice(1));
                        $('#custom_name').val(selectedText);
                        $(".error_model").text('');
                        $('#customModal').modal('show');
                    }
                });

                // Reset modal state when closed
                $('#customModal').on('hidden.bs.modal', function() {
                    modalOpen = false;
                    currentCustomValue = null;
                    currentType = null;
                    $(".error_custom_name").text('');
                });
                $('#saveCustomBtn').on('click', function() {
                    const name = $('#custom_name').val().trim();
                    let sub_admin_id = sub_branch_id;

                    $(".error_custom_name").text(""); // reset error

                    if (name === "") {
                        $(".error_custom_name").text("Please enter a name");
                        return;
                    }

                    let apiUrl = "";
                    let targetDropdown = "";
                    let postData = {
                        sub_admin_id: sub_admin_id,
                        name: name
                    };

                    if (currentType === "brand") {
                        apiUrl = "/api/addBrand";
                        targetDropdown = "#brand_id";
                    } else if (currentType === "category") {
                        apiUrl = "/api/addcategory";
                        targetDropdown = "#category_id";
                    } else if (currentType === "unit") {
                        apiUrl = "/api/add-units";
                        targetDropdown = "#unit_id";
                        postData = {
                            unitname: name,
                            selectedSubAdminId: sub_branch_id
                        };
                    }

                    if (!apiUrl) return;

                    // 🔹 Show loader
                    let btn = $("#saveCustomBtn");
                    btn.prop("disabled", true).html("Saving... <i class='fa fa-spinner fa-spin'></i>");

                    $.ajax({
                        url: apiUrl,
                        type: "POST",
                        data: postData,
                        headers: {
                            "Authorization": "Bearer " + authToken
                        },
                        success: function(response) {
                            let id = null;
                            let text = null;

                            if (currentType === "brand") {
                                let brandData = response.brand ?? response.data ?? null;
                                if (brandData) {
                                    id = brandData.id;
                                    text = brandData.name;
                                }
                            } else if (currentType === "category") {
                                if (response.category) {
                                    id = response.category.id;
                                    text = response.category.name;
                                }
                            } else if (currentType === "unit") {
                                let unitData = response.data ?? response.unit ?? null;
                                if (unitData) {
                                    id = unitData.id;
                                    text = unitData.unit_name ?? unitData.name ?? null;
                                }
                            }

                            if (!id) {
                                $(".error_custom_name").text("Invalid response format");
                                btn.prop("disabled", false).text("Save");
                                return;
                            }

                            // remove temp option
                            if (currentCustomValue && !/^\d+$/.test(String(currentCustomValue))) {
                                $(targetDropdown).find(`option[value="${currentCustomValue}"]`)
                                    .remove();
                            }

                            // add new option
                            if ($(targetDropdown).find(`option[value="${id}"]`).length === 0) {
                                $(targetDropdown).append(new Option(text, id, true, true));
                            }

                            // select new
                            $(targetDropdown).val(id).trigger('change');

                            // refresh dropdown data
                            if (currentType === "brand") {
                                get_brand(id);
                            } else if (currentType === "category") {
                                get_category(id);
                            } else if (currentType === "unit") {
                                get_unit(id);
                            }

                            // ✅ Success: show SweetAlert then close modal
                            btn.prop("disabled", false).text("Save");
                            const typeName = currentType.charAt(0).toUpperCase() + currentType
                                .slice(1);
                            $('#customModal').modal('hide');
                            modalOpen = false;
                            currentCustomValue = null;
                            currentType = null;
                            Swal.fire({
                                title: "Added!",
                                text: typeName + " added successfully.",
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: "#ff9f43",
                                timer: 2000,
                                timerProgressBar: true,
                            });
                        },
                        error: function(xhr) {
                            let msg = "Error while saving " + currentType;
                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.errors) {
                                    let firstKey = Object.keys(xhr.responseJSON.errors)[0];
                                    msg = xhr.responseJSON.errors[firstKey][0];
                                } else if (xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                            }
                            $(".error_custom_name").text(msg);
                            btn.prop("disabled", false).text("Save");
                            console.error(xhr.responseText);
                        }
                    });
                });

                // 🔹 GST Option Change Handler
                $(document).on('change', '#gst_option', function() {
                    const gstOption = $(this).val();
                    const gstContainer = $('#gst_dropdown_container');

                    if (gstOption === 'with_gst') {
                        gstContainer.show();
                        fetch_gst_rates_edit();
                        $('.error_product_gst').text('');
                    } else {
                        gstContainer.hide();
                        $('#product_gst').val(null).trigger('change');
                        $('.error_product_gst').text('');
                    }
                });

                // 🔹 Fetch GST Rates for Edit
                function fetch_gst_rates_edit(preSelectIds = []) {
                    const sub_branch_id = localStorage.getItem('selectedSubAdminId');
                    let url = sub_branch_id ?
                        `/api/get-tax-rates?sub_branch_id=${sub_branch_id}` :
                        `/api/get-tax-rates`;

                    $.ajax({
                        url: url,
                        type: "GET",
                        headers: {
                            "Authorization": "Bearer " + authToken,
                        },
                        success: function(response) {
                            if (response.status) {
                                let select = $("#product_gst");
                                select.empty();

                                $.each(response.data, function(key, item) {
                                    select.append(
                                        `<option value="${item.id}">${item.tax_name} (${item.tax_rate}%)</option>`
                                    );
                                });

                                if (preSelectIds.length > 0) {
                                    select.val(preSelectIds).trigger('change');
                                }

                                if (select.hasClass('select2-hidden-accessible')) {
                                    select.trigger('change');
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error("Error fetching GST rates:", xhr.responseText);
                        }
                    });
                }

                // IMEI / Serial Number Handling
                function updateSerialUI() {
                    let categorySelect = $('#category_id');
                    if (!categorySelect.length) return;
                    let categoryText = categorySelect.find('option:selected').text().trim().toLowerCase();
                    let serialContainer = $('.serial-no-container');
                    let qty = parseFloat($('#quantity').val()) || 0;
                    let imeiInput = $('#imei_no');

                    if (categoryText.includes('mobile')) {
                        serialContainer.show();
                        let existingSerials = [];
                        try {
                            let val = imeiInput.val();
                            existingSerials = val ? JSON.parse(val) : [];
                        } catch (e) {}
                        let count = existingSerials.length;
                        serialContainer.find('.serial-status').text(`${count}/${qty} IMEI numbers added`);
                        if (count < qty && qty > 0) {
                            serialContainer.find('.serial-status').removeClass('text-success').addClass('text-danger');
                        } else {
                            serialContainer.find('.serial-status').removeClass('text-danger').addClass('text-success');
                        }
                    } else {
                        serialContainer.hide();
                        imeiInput.val('[]');
                    }
                }

                $(document).on('click', '.edit-serial-btn', function() {
                    let qty = parseFloat($('#quantity').val()) || 0;

                    if (qty <= 0) {
                        alert("Please enter a valid quantity first.");
                        return;
                    }

                    let existingSerials = [];
                    try {
                        let val = $('#imei_no').val();
                        existingSerials = val ? JSON.parse(val) : [];
                    } catch (e) {}

                    let modalBody = $('#serialNumberModalBody');
                    modalBody.empty();

                    for (let i = 0; i < qty; i++) {
                        let val = existingSerials[i] || '';
                        modalBody.append(`
                            <div class="form-group mb-2">
                                <label>IMEI No ${i + 1}</label>
                                <input type="text" class="form-control serial-input-item" value="${val}" placeholder="Enter IMEI / Serial No">
                            </div>
                        `);
                    }

                    $('#serialNumberModal').modal('show');
                });

                $(document).on('click', '#saveSerialNumbersBtn', function() {
                    let serials = [];
                    $('#serialNumberModalBody .serial-input-item').each(function() {
                        let val = $(this).val().trim();
                        if (val !== '') {
                            serials.push(val);
                        }
                    });

                    $('#imei_no').val(JSON.stringify(serials));
                    updateSerialUI();
                    $('#serialNumberModal').modal('hide');
                });

                $(document).on('change', '#category_id', function() {
                    updateSerialUI();
                });

                $(document).on('input change', '#quantity', function() {
                    updateSerialUI();
                });

                // Trigger update on page load/ready
                setTimeout(updateSerialUI, 1000); // short delay to ensure select option is set and loaded

                // Tax Modal Logic
                $(document).on('click', '#openTaxModal', function() {
                    if (typeof modalOpen !== 'undefined' && modalOpen) return;
                    if (typeof modalOpen !== 'undefined') modalOpen = true;

                    $('#tax_name').val('');
                    $('#tax_rate').val('');
                    $('#tax_status').val('active');
                    $('.error_tax_name, .error_tax_rate, .error_tax_status').text('');
                    $('#taxModal').modal('show');
                });

                $('#taxModal').on('hidden.bs.modal', function() {
                    if (typeof modalOpen !== 'undefined') modalOpen = false;
                });

                $('#saveTaxBtn').on('click', function() {
                    const taxName = $('#tax_name').val().trim();
                    const taxRate = $('#tax_rate').val().trim();
                    const taxStatus = $('#tax_status').val();

                    $('.error_tax_name, .error_tax_rate, .error_tax_status').text('');
                    let hasError = false;

                    if (taxName === "") {
                        $('.error_tax_name').text("Tax Name is required.");
                        hasError = true;
                    }
                    if (taxRate === "") {
                        $('.error_tax_rate').text("Tax Rate is required.");
                        hasError = true;
                    }

                    if (hasError) return;

                    let btn = $(this);
                    let originalText = btn.html();
                    btn.prop("disabled", true).html("Saving... <i class='fa fa-spinner fa-spin'></i>");

                    var authToken = localStorage.getItem("authToken");
                    const sub_branch_id = localStorage.getItem('selectedSubAdminId');

                    $.ajax({
                        url: "/api/tax-rates/store",
                        type: "POST",
                        data: {
                            name: taxName,
                            rate: taxRate,
                            status: (taxStatus || 'active').toLowerCase(),
                            sub_admin_id: sub_branch_id
                        },
                        headers: {
                            "Authorization": "Bearer " + authToken,
                        },
                        success: function(response) {
                            btn.prop("disabled", false).html(originalText);

                            if (response.status) {
                                $('#taxModal').modal('hide');
                                if (typeof modalOpen !== 'undefined') modalOpen = false;
                                Swal.fire({
                                    title: "Added!",
                                    text: "Tax rate added successfully.",
                                    icon: "success",
                                    confirmButtonText: "OK",
                                    confirmButtonColor: "#ff9f43",
                                    timer: 2000,
                                    timerProgressBar: true,
                                });

                                const newTaxId = response.taxRate ? String(response.taxRate.id) : (
                                    response.data ? String(response.data.id) : null);
                                let currentSelected = ($('#product_gst').val() || []).map(String)
                                    .filter(Boolean);
                                if (newTaxId && currentSelected.indexOf(newTaxId) === -1) {
                                    currentSelected.push(newTaxId);
                                }

                                if (typeof fetch_gst_rates_edit === 'function') {
                                    fetch_gst_rates_edit(currentSelected);
                                }
                            } else {
                                Swal.fire("Error", response.message || "Something went wrong",
                                    "error");
                            }
                        },
                        error: function(xhr) {
                            btn.prop("disabled", false).html(originalText);

                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.errors) {
                                    if (xhr.responseJSON.errors.name) {
                                        $('.error_tax_name').text(xhr.responseJSON.errors.name[0]);
                                    }
                                    if (xhr.responseJSON.errors.rate) {
                                        $('.error_tax_rate').text(xhr.responseJSON.errors.rate[0]);
                                    }
                                    if (xhr.responseJSON.errors.status) {
                                        $('.error_tax_status').text(xhr.responseJSON.errors.status[
                                            0]);
                                    }
                                } else if (xhr.responseJSON.message) {
                                    Swal.fire("Error", xhr.responseJSON.message, "error");
                                }
                            } else {
                                console.error(xhr.responseText);
                            }
                        }
                    });
                });

                // Add More Variant Row
                let variantIndex = $('#variantsBody tr').length;
                $('#addVariantBtn').on('click', function() {
                    let newRow = `
                        <tr>
                            <td><input type="text" name="variants[${variantIndex}][size]" class="form-control" required placeholder="e.g. M"></td>
                            <td><input type="text" name="variants[${variantIndex}][color]" class="form-control" required placeholder="e.g. Red"></td>
                            <td><input type="number" name="variants[${variantIndex}][qty]" class="form-control" min="0" required placeholder="0"></td>
                            <td><input type="number" name="variants[${variantIndex}][purchase_price]" class="form-control" min="0" step="0.01" placeholder="0.00"></td>
                            <td><input type="number" name="variants[${variantIndex}][price]" class="form-control" min="0" step="0.01" required placeholder="0.00"></td>
                            <td>
                                <input type="hidden" name="variants[${variantIndex}][barcode]" class="variant-actual-barcode">
                                <div class="barcode-input-group mb-1">
                                    <input type="text" class="form-control variant-barcode-input" placeholder="Barcode" oninput="this.value=this.value.replace(/[^A-Za-z0-9\-_.\/+]/g,'')">
                                    <button class="btn btn-secondary px-2 add-variant-barcode-btn" type="button" style="height: 38px; padding-left: 6px !important; padding-right: 6px !important;">Add</button>
                                    <button type="button" class="barcode-scan-btn variant-scan-btn" title="Scan Barcode">
                                        <i class="fa-solid fa-camera"></i>
                                    </button>
                                </div>
                                <div class="variant-barcode-tags-container d-flex flex-wrap gap-2"></div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm remove-variant"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                    $('#variantsBody').append(newRow);
                    variantIndex++;
                    updateRemoveButtons();
                });

            let barcodeScanner = null;
            let barcodeScriptLoading = null;
            let activeBarcodeTarget = '#barcode_input';

            function sanitizeBarcodeValue(barcode) {
                return String(barcode || '').trim().replace(/[^A-Za-z0-9\-_.\/+]/g, '');
            }

            function getBarcodeScannerConfig() {
                const config = {
                    fps: 10,
                    qrbox: function(viewfinderWidth, viewfinderHeight) {
                        return {
                            width: Math.max(220, Math.min(320, Math.floor(viewfinderWidth * 0.9))),
                            height: Math.max(80, Math.min(140, Math.floor(viewfinderHeight * 0.35)))
                        };
                    }
                };

                if (typeof Html5QrcodeSupportedFormats !== 'undefined') {
                    config.formatsToSupport = [
                        Html5QrcodeSupportedFormats.CODE_128,
                        Html5QrcodeSupportedFormats.CODE_39,
                        Html5QrcodeSupportedFormats.CODE_93,
                        Html5QrcodeSupportedFormats.QR_CODE,
                        Html5QrcodeSupportedFormats.DATA_MATRIX,
                        Html5QrcodeSupportedFormats.EAN_13,
                        Html5QrcodeSupportedFormats.EAN_8,
                        Html5QrcodeSupportedFormats.UPC_A,
                        Html5QrcodeSupportedFormats.UPC_E,
                        Html5QrcodeSupportedFormats.ITF,
                        Html5QrcodeSupportedFormats.CODABAR
                    ];
                }

                return config;
            }

            function handleScannedBarcode(barcode) {
                const cleanedBarcode = sanitizeBarcodeValue(barcode);

                if (!cleanedBarcode) {
                    return false;
                }

                const $target = (activeBarcodeTarget && typeof activeBarcodeTarget === 'object' && activeBarcodeTarget.jquery)
                    ? activeBarcodeTarget
                    : null;

                if ($target && $target.length && $target.hasClass('variant-barcode-input')) {
                    $target.val(cleanedBarcode);
                    $target.closest('.barcode-input-group').find('.add-variant-barcode-btn').trigger('click');
                    return true;
                }

                $('#barcode_input').val(cleanedBarcode);
                $('#add_barcode_btn').trigger('click');
                return true;
            }

            function stopBarcodeScanner() {
                if (!barcodeScanner) {
                    return;
                }

                if (barcodeScanner.isScanning) {
                    barcodeScanner.stop().then(function() {
                        barcodeScanner = null;
                    }).catch(function() {
                        barcodeScanner = null;
                    });
                } else {
                    barcodeScanner = null;
                }
            }

            function showManualBarcodeInput() {
                $('#barcode-scan-message').hide();
                $('#barcode-qr-reader').html(`
                    <div style="text-align:center; padding: 30px 20px;">
                        <p style="color:#666; font-size:14px; margin-bottom:16px;">
                            Camera is not available.<br>Enter barcode manually below:
                        </p>
                        <div style="display:flex; gap:8px; justify-content:center; flex-wrap:wrap;">
                            <input type="text" id="manualBarcodeInput" class="form-control"
                                placeholder="Enter barcode / product code"
                                style="max-width:260px; font-size:14px;">
                            <button type="button" class="btn btn-primary" id="manualBarcodeSubmit">Add</button>
                        </div>
                        <div id="manualBarcodeError" class="text-danger" style="font-size:14px; margin-top:8px;"></div>
                    </div>
                `);
            }

            function submitManualBarcode() {
                const barcode = $('#manualBarcodeInput').val().trim();

                if (!barcode) {
                    $('#manualBarcodeError').text('Please enter a barcode.');
                    return;
                }

                $('#manualBarcodeSubmit').prop('disabled', true).text('Adding...');
                $('#manualBarcodeError').text('');

                try {
                    const found = handleScannedBarcode(barcode);

                    if (found) {
                        $('#barcodeScannerModal').modal('hide');
                        $('#manualBarcodeInput').val('');
                    }
                } catch (error) {
                    $('#manualBarcodeError').text('Unable to add barcode.');
                } finally {
                    $('#manualBarcodeSubmit').prop('disabled', false).text('Add');
                }
            }

            function bindManualBarcodeEvents() {
                $('#barcode-qr-reader').off('click', '#manualBarcodeSubmit').on('click', '#manualBarcodeSubmit',
                    function() {
                        submitManualBarcode();
                    });

                $('#barcode-qr-reader').off('keydown', '#manualBarcodeInput').on('keydown',
                    '#manualBarcodeInput',
                    function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            submitManualBarcode();
                        }
                    });
            }

            function onBarcodeScanSuccess(decodedText) {
                stopBarcodeScanner();
                $('#barcodeScannerModal').modal('hide');
                $('#barcode-scan-message').text('');
                handleScannedBarcode(decodedText);
            }

            function onBarcodeScanError() {}

            function loadBarcodeScannerLibrary() {
                if (typeof Html5Qrcode !== 'undefined') {
                    return Promise.resolve();
                }

                if (barcodeScriptLoading) {
                    return barcodeScriptLoading;
                }

                barcodeScriptLoading = new Promise(function(resolve, reject) {
                    const script = document.createElement('script');
                    script.src =
                        'https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js';
                    script.onload = resolve;
                    script.onerror = reject;
                    document.body.appendChild(script);
                });

                return barcodeScriptLoading;
            }

            function startBarcodeScanner() {
                loadBarcodeScannerLibrary().then(function() {
                    $('#barcode-scan-message').show().text('Starting camera...').css('color', '');
                    $('#barcode-qr-reader').html('').css('min-height', '300px');

                    try {
                        barcodeScanner = new Html5Qrcode("barcode-qr-reader");
                    } catch (error) {
                        console.error('Failed to initialize barcode scanner:', error);
                        showManualBarcodeInput();
                        bindManualBarcodeEvents();
                        return;
                    }

                    const config = getBarcodeScannerConfig();

                    Html5Qrcode.getCameras().then(function(devices) {
                        if (!devices || !devices.length) {
                            stopBarcodeScanner();
                            showManualBarcodeInput();
                            bindManualBarcodeEvents();
                            return;
                        }

                        let cameraId = null;

                        for (let i = 0; i < devices.length; i++) {
                            const label = (devices[i].label || '').toLowerCase();
                            if (label.includes('back') || label.includes('rear')) {
                                cameraId = devices[i].id;
                                break;
                            }
                        }

                        if (!cameraId) {
                            cameraId = devices[0].id;
                        }

                        barcodeScanner.start(
                            cameraId,
                            config,
                            onBarcodeScanSuccess,
                            onBarcodeScanError
                        ).then(function() {
                            $('#barcode-scan-message').show().text('Point camera at barcode').css(
                                'color',
                                'green');
                        }).catch(function(error) {
                            console.error('Camera start failed:', error);
                            stopBarcodeScanner();
                            $('#barcode-scan-message').show().text(
                                'Unable to access camera. You can enter barcode manually below.'
                            ).css('color', 'red');
                            showManualBarcodeInput();
                            bindManualBarcodeEvents();
                        });
                    }).catch(function(error) {
                        console.error('Camera detection failed:', error);
                        stopBarcodeScanner();
                        $('#barcode-scan-message').show().text(
                            'Unable to access camera. You can enter barcode manually below.'
                        ).css('color', 'red');
                        showManualBarcodeInput();
                        bindManualBarcodeEvents();
                    });
                }).catch(function() {
                    $('#barcode-scan-message').show().text(
                        'Scanner library failed to load. Enter barcode manually below.'
                    ).css('color', 'red');
                    showManualBarcodeInput();
                    bindManualBarcodeEvents();
                });
            }

            $('#openBarcodeScanner').on('click', function() {
                activeBarcodeTarget = '#barcode_input';
                $('#barcodeScannerModal').modal('show');
            });

            $(document).on('click', '.variant-scan-btn', function() {
                activeBarcodeTarget = $(this).siblings('input.variant-barcode-input');
                $('#barcodeScannerModal').modal('show');
            });

            $('#barcodeScannerModal').on('shown.bs.modal', function() {
                $('#barcode-qr-reader').html('').css('min-height', '300px');
                $('#barcode-scan-message').show().text('Initializing camera...').css('color', '');
                startBarcodeScanner();
            });

            $('#barcodeScannerModal').on('hidden.bs.modal', function() {
                stopBarcodeScanner();
                $('#barcode-qr-reader').html('').css('min-height', '300px');
                $('#barcode-scan-message').show().text('Initializing camera...').css('color', '');
            });


                // Remove Variant Row
                $(document).on('click', '.remove-variant', function() {
                    if ($('#variantsBody tr').length > 1) {
                        $(this).closest('tr').remove();
                        updateRemoveButtons();
                    }
                });

                function updateRemoveButtons() {
                    let rowCount = $('#variantsBody tr').length;
                    if (rowCount === 1) {
                        $('.remove-variant').prop('disabled', true);
                    } else {
                        $('.remove-variant').prop('disabled', false);
                    }
                }

                // Toggle Product Type Logic
                function toggleProductType() {
                    const selectedType = $('input[name="item_type"]:checked').val();
                    if (selectedType === 'normal') {
                        $('.normal-product-field').show();
                        $('.variant-product-field').hide();
                    } else {
                        $('.normal-product-field').hide();
                        $('.variant-product-field').show();
                    }
                }

                $('input[name="item_type"]').on('change', toggleProductType);
                
                // Initial call to set correct visibility on load
                toggleProductType();

                // AI Image Generation Logic
                let aiImageDebounceTimer;
                let lastGeneratedName = '';
                let currentAiImagePath = '';
        
                function generateAiImage(productName) {
                    lastGeneratedName = productName;
                    const previewDiv = document.querySelector(".image-preview");
                    const dropzoneText = document.querySelector(".image-uploads h4");
                    
                    // Show loading state
                    dropzoneText.innerHTML = "Generating AI image... <i class='fa fa-spinner fa-spin'></i>";
                    
                    $.ajax({
                        url: '/api/products/generate-image',
                        type: 'POST',
                        data: {
                            product_name: productName
                        },
                        headers: {
                            "Authorization": "Bearer " + localStorage.getItem("authToken"),
                        },
                        success: function(response) {
                            dropzoneText.innerHTML = "Add images (multiple allowed)";
                            
                            if (response.success && window.ProductImagesManager) {
                                window.ProductImagesManager.appendGeneratedImage(response.image_path, { showAiBadge: true });
                                currentAiImagePath = response.image_path;
                            }   
                        },
                        error: function(xhr) {
                            dropzoneText.innerHTML = "Drag and drop a file to upload";
                            console.error("AI Image Generation failed:", xhr);
                        }
                    });
                }
        
                window.removeAiImage = function(imagePath) {
                    if (imagePath && window.ProductImagesManager) {
                        window.ProductImagesManager.removeGeneratedImage(imagePath);
                    } else if (window.ProductImagesManager) {
                        window.ProductImagesManager.previewEl.querySelectorAll('[data-generated-path]').forEach(function(node) {
                            window.ProductImagesManager.removeGeneratedImage(node.getAttribute('data-generated-path'));
                        });
                    }
                    lastGeneratedName = '';
                    currentAiImagePath = '';
                };

                window.removeAiBg = function(imagePath) {
                    let btn = document.getElementById('removeBgBtn');
                    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Removing...'; }

                    $.ajax({
                        url: '/api/products/remove-background',
                        type: 'POST',
                        data: JSON.stringify({ image_path: imagePath }),
                        contentType: 'application/json',
                        headers: { "Authorization": "Bearer " + localStorage.getItem("authToken") },
                        success: function(response) {
                            if (response.success) {
                                const basePath = "{{ rtrim(env('ImagePath'), '/') }}";
                                currentAiImagePath = response.image_path;
                                $('#generated_image_path').val(response.image_path);
                                let img = document.getElementById('ai-preview-img');
                                if (img) img.src = basePath + '/storage/' + response.image_path + '?t=' + Date.now();
                                if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-check"></i> BG Removed'; btn.classList.remove('btn-outline-success'); btn.classList.add('btn-success'); }
                            } else {
                                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-eraser"></i> Remove BG'; }
                                Swal.fire({ icon: 'error', title: 'Failed', text: response.message || 'Could not remove background.', confirmButtonColor: '#ff9f43' });
                            }
                        },
                        error: function(xhr) {
                            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-eraser"></i> Remove BG'; }
                            var msg = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'Background removal request failed.';
                            Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#ff9f43' });
                        }
                    });
                };
        
                window.forceRegenerateAiImage = function() {
                    const productName = $('#name').val().trim();
                    if (productName.length >= 3) {
                        generateAiImage(productName);
                    } else {
                        Swal.fire("Error", "Product name must be at least 3 characters to generate an image.", "error");
                    }
                };
            });
        </script>
    @endpush
