@extends('layout.app')

@section('title', 'Quick Update Products')

@section('content')
    <style>
        .qu-page-card {
            border: 1px solid #ece9f1;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
        }
        .qu-page-card .table thead th {
            white-space: nowrap;
            font-size: 12px;
            font-weight: 700;
            color: #5e5873;
            background: #f8f8f8;
            position: sticky;
            top: 0;
            z-index: 2;
        }
        .qu-page-table-wrap {
            max-height: calc(100vh - 280px);
            overflow: auto;
            border: 1px solid #ece9f1;
            border-radius: 8px;
        }
        .qu-page-card .form-control-sm {
            font-size: 12px;
            min-height: 32px;
        }
        #quickUpdateDetailsModal {
            z-index: 1065;
        }
        .qu-page .select2-container--default .select2-selection--single {
            height: 32px;
            border: 1px solid #ced4da;
            border-radius: 0.2rem;
        }
        .qu-page .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px;
            font-size: 12px;
            padding-left: 8px;
            color: #212529;
        }
        .qu-page .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 30px;
        }
        .select2-container--open {
            z-index: 2000;
        }
        .select2-container--default .select2-selection--single .select2-selection__clear {
            margin-right: 16px;
            color: #dc3545;
            font-weight: 700;
        }
        .qu-brand-none {
            color: #dc3545;
            font-weight: 600;
        }
        .qu-col-menu {
            width: 460px;
            max-width: calc(100vw - 32px);
            max-height: 65vh;
            overflow-y: auto;
            padding: 10px 12px;
            border: 1px solid #ece9f1;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
        }
        .qu-col-menu-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding-bottom: 8px;
            margin-bottom: 6px;
            border-bottom: 1px solid #f0eef5;
            font-size: 12px;
            font-weight: 700;
            color: #5e5873;
        }
        .qu-col-menu-head .btn-link {
            font-size: 12px;
            padding: 0 4px;
            text-decoration: none;
        }
        .qu-col-group-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #ff9f43;
            margin: 8px 0 4px;
        }
        .qu-col-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 2px 12px;
        }
        .qu-col-grid label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            padding: 4px 6px;
            border-radius: 6px;
            cursor: pointer;
            margin: 0;
            user-select: none;
        }
        .qu-col-grid label:hover {
            background: #fff6ec;
        }
        .qu-col-grid input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: #ff9f43;
            cursor: pointer;
        }
        #quickUpdateColumnsBtn .badge {
            background: #ff9f43;
            font-size: 10px;
        }

        @media (max-width: 991px) {
            .page-wrapper {
                min-height: auto !important;
            }
            .content {
                padding: 12px !important;
                max-width: 100vw !important;
                overflow-x: hidden !important;
            }
            .qu-page-card {
                max-width: 100% !important;
                overflow: hidden !important;
                margin-bottom: 15px !important;
            }
            .qu-page-card .card-body {
                padding: 12px !important;
                max-width: 100% !important;
            }
            .qu-page-table-wrap {
                width: 100% !important;
                max-width: 100% !important;
                max-height: 65vh !important;
                overflow-x: auto !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch !important;
                display: block !important;
            }
            .page-header {
                margin-bottom: 12px !important;
                flex-wrap: wrap !important;
                gap: 8px !important;
            }
        }

        @media (max-width: 575px) {
            .qu-page-search-row {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 8px !important;
            }
            .qu-page-search-row > div {
                min-width: 0 !important;
                width: 100% !important;
            }
            .qu-page-search-row button {
                width: 100% !important;
            }
            .qu-page-search-row .d-none-mob {
                display: none !important;
            }
            .qu-col-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4><i class="fas fa-bolt me-2" style="color:#ff9f43;"></i>Quick Update Products</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('product.list') }}" class="btn btn-sm btn-secondary"
                    style="height: 35px; display: inline-flex; align-items: center;">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Products
                </a>
            </div>
        </div>

        <div class="card qu-page-card qu-page">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 align-items-end mb-3 qu-page-search-row">
                    <div class="flex-grow-1" style="min-width: 220px;">
                        <label for="quickUpdateSearch" class="form-label mb-1" style="font-size:12px; font-weight:600;">Search</label>
                        <input type="text" id="quickUpdateSearch" class="form-control form-control-sm"
                            placeholder="Search name, SKU or barcode..." autofocus>
                    </div>
                    <div style="min-width: 180px;">
                        <label for="quickUpdateCategory" class="form-label mb-1" style="font-size:12px; font-weight:600;">Category</label>
                        <select id="quickUpdateCategory" class="form-control form-control-sm">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="dropdown">
                        <label class="form-label mb-1 d-block d-none-mob" style="font-size:12px; font-weight:600;">&nbsp;</label>
                        <button type="button" id="quickUpdateColumnsBtn" class="btn btn-sm btn-outline-warning dropdown-toggle"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <i class="fas fa-columns me-1"></i> Columns <span class="badge rounded-pill ms-1" id="quickUpdateColumnsCount">0</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end qu-col-menu" aria-labelledby="quickUpdateColumnsBtn">
                            <div class="qu-col-menu-head">
                                <span>Show fields in list</span>
                                <span>
                                    <button type="button" class="btn btn-link" id="quickUpdateColumnsAll">Select all</button>
                                    <button type="button" class="btn btn-link" id="quickUpdateColumnsDefault">Default</button>
                                </span>
                            </div>
                            <div id="quickUpdateColumnsList"></div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label mb-1 d-block d-none-mob" style="font-size:12px; font-weight:600;">&nbsp;</label>
                        <button type="button" id="quickUpdateRefreshBtn" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>

                <div class="qu-page-table-wrap table-responsive">
                    <table class="table table-sm table-bordered mb-0 align-middle" id="quickUpdateProductsTable" style="font-size: 12px;">
                        <thead>
                            <tr id="quickUpdateProductsHead"></tr>
                        </thead>
                        <tbody id="quickUpdateProductsBody">
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">Loading products…</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                    <div id="quickUpdatePageInfo" class="text-muted" style="font-size: 12px;">—</div>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" id="quickUpdatePrevBtn" disabled>Prev</button>
                        <button type="button" class="btn btn-outline-secondary" id="quickUpdateNextBtn" disabled>Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- More Details Modal (kept as popup on this page) -->
    <div class="modal fade" id="quickUpdateDetailsModal" tabindex="-1" aria-labelledby="quickUpdateDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="padding: 12px 16px;">
                    <h5 class="modal-title" id="quickUpdateDetailsModalLabel" style="font-weight: 700;">
                        <i class="fas fa-sliders-h me-2" style="color:#ff9f43;"></i>More Details
                        <small class="text-muted ms-2" id="quDetailsProductHint" style="font-size:12px; font-weight:500;"></small>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body" style="padding: 14px 16px;">
                    <input type="hidden" id="quDetailsProductId" value="">
                    <input type="hidden" id="quDetailsBaseName" value="">
                    <input type="hidden" id="quDetailsBaseSku" value="">
                    <input type="hidden" id="quDetailsBaseBarcode" value="">
                    <input type="hidden" id="quDetailsBaseMrp" value="">
                    <input type="hidden" id="quDetailsBasePrice" value="">
                    <input type="hidden" id="quDetailsBaseWholesale" value="">
                    <input type="hidden" id="quDetailsBasePurchase" value="">
                    <input type="hidden" id="quDetailsBaseQty" value="">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Category</label>
                            <select id="quDetailsCategory" class="form-control form-control-sm">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Brand</label>
                            <select id="quDetailsBrand" class="form-control form-control-sm">
                                <option value=""></option>
                                <option value="__none__">No brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->name }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Choose No brand to remove it, or type a new name to create it here.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:600;">HSN Code</label>
                            <input type="text" id="quDetailsHsn" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Product Code</label>
                            <input type="text" id="quDetailsProductCode" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Batch No</label>
                            <input type="text" id="quDetailsBatch" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Specific Qty</label>
                            <input type="text" id="quDetailsSpecificQty" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Label Code</label>
                            <input type="text" id="quDetailsLabelCode" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Label Reference</label>
                            <input type="text" id="quDetailsLabelRef" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Item Location</label>
                            <input type="text" id="quDetailsLocation" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Min Stock Qty</label>
                            <input type="number" step="0.01" min="0" id="quDetailsMinStock" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Cost Price</label>
                            <input type="number" step="0.01" min="0" id="quDetailsCost" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Landing Cost</label>
                            <input type="number" step="0.01" min="0" id="quDetailsLanding" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Rate 2 Price</label>
                            <input type="number" step="0.01" min="0" id="quDetailsRate2Price" class="form-control form-control-sm" placeholder="Optional">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Rate 2 MRP</label>
                            <input type="number" step="0.01" min="0" id="quDetailsRate2Mrp" class="form-control form-control-sm" placeholder="Optional">
                        </div>
                        <div class="col-12">
                            <label class="form-label mb-1" style="font-size:12px; font-weight:600;">Description</label>
                            <textarea id="quDetailsDescription" class="form-control form-control-sm" rows="2" placeholder="Optional notes…"></textarea>
                        </div>
                    </div>
                    <div id="quDetailsLoading" class="text-center text-muted py-3 d-none">
                        <i class="fas fa-spinner fa-spin me-1"></i> Loading details…
                    </div>
                </div>
                <div class="modal-footer" style="padding: 10px 16px;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-warning" id="quDetailsSaveBtn" style="color:#fff;">
                        <i class="fas fa-save me-1"></i> Update Details
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem('authToken');
            var quBrands = @json($brands->map(fn ($brand) => ['id' => $brand->id, 'name' => $brand->name])->values());

            function findBrandByName(name) {
                var key = String(name || '').trim().toLowerCase();
                if (!key) return null;
                for (var i = 0; i < quBrands.length; i++) {
                    if (String(quBrands[i].name || '').trim().toLowerCase() === key) {
                        return quBrands[i];
                    }
                }
                return null;
            }

            function addBrandOption(name) {
                name = String(name || '').trim();
                if (!name) return;
                $('#quDetailsBrand, #quickUpdateProductsBody .qu-brand').each(function() {
                    var $sel = $(this);
                    var exists = false;
                    $sel.find('option').each(function() {
                        if ($(this).val() === name) exists = true;
                    });
                    if (!exists) {
                        $sel.append(new Option(name, name, false, false));
                    }
                });
            }

            function brandFieldValue($sel) {
                var val = ($sel && $sel.length) ? $sel.val() : '';
                if (val == null || val === '' || val === '__none__') return '';
                return String(val).trim();
            }

            function setBrandValue($sel, name) {
                name = String(name || '').trim();
                if (name === '__none__') name = '';
                if (!$sel || !$sel.length) return;
                if (name) {
                    var exists = false;
                    $sel.find('option').each(function() {
                        if ($(this).val() === name) exists = true;
                    });
                    if (!exists) {
                        $sel.append(new Option(name, name, true, true));
                    }
                }
                $sel.val(name).trigger('change');
            }

            function destroyBrandSelects($scope) {
                $scope.find('.qu-brand').each(function() {
                    if ($(this).data('select2')) {
                        $(this).select2('destroy');
                    }
                });
            }

            function initBrandSelect($selects, inModal) {
                if (!$.fn.select2) return;
                $selects.each(function() {
                    var $el = $(this);
                    if ($el.data('select2')) return;
                    $el.select2({
                        tags: true,
                        placeholder: 'Type or select',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: inModal ? $('#quickUpdateDetailsModal') : $(document.body),
                        templateResult: function(item) {
                            if (item.id === '__none__') {
                                return $('<span class="qu-brand-none">No brand</span>');
                            }
                            return item.text;
                        },
                        templateSelection: function(item) {
                            if (item.id === '__none__') {
                                return 'No brand';
                            }
                            return item.text;
                        },
                        matcher: function(params, data) {
                            if (data.id === '__none__') return data;
                            if ($.trim(params.term || '') === '') return data;
                            if (typeof data.text === 'undefined') return null;
                            if (String(data.text).toLowerCase().indexOf(String(params.term).toLowerCase()) > -1) {
                                return data;
                            }
                            return null;
                        },
                        createTag: function(params) {
                            var term = $.trim(params.term || '');
                            if (!term) return null;
                            return { id: term, text: term, newTag: true };
                        }
                    });
                });
            }

            function brandSelectHtml(selectedName) {
                selectedName = String(selectedName || '').trim();
                var html = '<select class="form-control form-control-sm qu-brand"><option value=""></option><option value="__none__">No brand</option>';
                var matched = false;
                quBrands.forEach(function(b) {
                    var name = String(b.name || '');
                    var sel = (name === selectedName) ? ' selected' : '';
                    if (sel) matched = true;
                    html += '<option value="' + escapeHtml(name) + '"' + sel + '>' + escapeHtml(name) + '</option>';
                });
                if (selectedName && !matched) {
                    html += '<option value="' + escapeHtml(selectedName) + '" selected>' + escapeHtml(selectedName) + '</option>';
                }
                html += '</select>';
                return html;
            }

            function rememberBrand(brand) {
                if (!brand || !brand.id) return;
                var name = String(brand.name || '').trim();
                if (!findBrandByName(name)) {
                    quBrands.push({ id: brand.id, name: name });
                    addBrandOption(name);
                }
            }

            function ensureBrandId(name) {
                name = String(name || '').trim();
                var deferred = $.Deferred();
                if (!name) {
                    deferred.resolve('');
                    return deferred.promise();
                }
                var existing = findBrandByName(name);
                if (existing) {
                    deferred.resolve(String(existing.id));
                    return deferred.promise();
                }
                $.ajax({
                    url: '/api/addBrand',
                    method: 'POST',
                    data: {
                        name: name,
                        sub_admin_id: localStorage.getItem('selectedSubAdminId') || '',
                        selectedSubAdminId: localStorage.getItem('selectedSubAdminId') || ''
                    },
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        var brand = (res && (res.brand || res.data)) ? (res.brand || res.data) : null;
                        if (!brand || !brand.id) {
                            deferred.reject({ message: 'Could not create brand.' });
                            return;
                        }
                        rememberBrand(brand);
                        deferred.resolve(String(brand.id));
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error))
                            ? (xhr.responseJSON.message || xhr.responseJSON.error)
                            : 'Could not create brand.';
                        if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.name) {
                            msg = xhr.responseJSON.errors.name[0];
                        }
                        if (xhr.status === 422) {
                            var again = findBrandByName(name);
                            if (again) {
                                deferred.resolve(String(again.id));
                                return;
                            }
                        }
                        deferred.reject({ message: msg });
                    }
                });
                return deferred.promise();
            }
            var quPage = 1;
            var quLastPage = 1;
            var quPerPage = 25;
            var quSearchTimer = null;
            var quLoading = false;
            var quCategories = @json($categories->map(fn ($category) => ['id' => $category->id, 'name' => $category->name])->values());
            var quRows = [];
            var quRowData = {};
            var quDrafts = {};
            var QU_COLUMNS_STORAGE = 'quickUpdateColumns.v1';

            function numCol(key, label, field, width, getter) {
                return {
                    key: key, label: label, field: field, type: 'number', width: width || 90,
                    get: getter || function(p) { return numOrEmpty(p[field]); }
                };
            }
            function textCol(key, label, field, width) {
                return {
                    key: key, label: label, field: field, type: 'text', width: width || 110,
                    get: function(p) { return p[field] == null ? '' : p[field]; }
                };
            }

            var QU_COLUMNS = [
                $.extend(textCol('name', 'Name', 'name', 200), { group: 'Basic', def: true }),
                $.extend(textCol('name_hindi', 'Hindi Name', 'name_hindi', 180), { group: 'Basic' }),
                $.extend(textCol('sku', 'SKU', 'SKU', 90), { group: 'Basic', def: true }),
                $.extend(textCol('barcode', 'Barcode', 'barcode', 110), { group: 'Basic', def: true }),
                { key: 'brand', label: 'Brand', field: 'brand_id', type: 'brand', width: 160, group: 'Basic', def: true, modal: '#quDetailsBrand',
                    get: function(p) { return productBrandName(p); } },
                { key: 'category', label: 'Category', field: 'category_id', type: 'category', width: 160, group: 'Basic', modal: '#quDetailsCategory',
                    get: function(p) { return p.category_id ? String(p.category_id) : ''; } },
                $.extend(numCol('mrp', 'MRP', 'mrp', 90), { group: 'Price & Stock', def: true }),
                $.extend(numCol('price', 'Price', 'price', 90), { group: 'Price & Stock', def: true }),
                $.extend(numCol('wholesale', 'Wholesale', 'wholesale_price', 100, function(p) { return numOrEmpty(extractWholesale(p)); }), { group: 'Price & Stock', def: true }),
                $.extend(numCol('purchase', 'Purchase', 'purchase_price', 100), { group: 'Price & Stock', def: true }),
                $.extend(numCol('qty', 'Qty', 'quantity', 80), { group: 'Price & Stock', def: true }),
                $.extend(numCol('rate2_price', 'Rate 2 Price', 'rate2_price', 100, function(p) { return numOrEmpty(extractRate2(p).price); }), { group: 'Price & Stock', modal: '#quDetailsRate2Price' }),
                $.extend(numCol('rate2_mrp', 'Rate 2 MRP', 'rate2_mrp', 100, function(p) { return numOrEmpty(extractRate2(p).mrp); }), { group: 'Price & Stock', modal: '#quDetailsRate2Mrp' }),
                $.extend(numCol('cost_price', 'Cost Price', 'cost_price', 100), { group: 'Price & Stock', modal: '#quDetailsCost' }),
                $.extend(numCol('landing_cost', 'Landing Cost', 'landing_cost', 100), { group: 'Price & Stock', modal: '#quDetailsLanding' }),
                $.extend(numCol('minimum_stock_qty', 'Min Stock Qty', 'minimum_stock_qty', 100), { group: 'Price & Stock', modal: '#quDetailsMinStock' }),
                $.extend(textCol('hsn_code', 'HSN Code', 'hsn_code', 110), { group: 'Details', modal: '#quDetailsHsn' }),
                $.extend(textCol('product_code', 'Product Code', 'product_code', 110), { group: 'Details', modal: '#quDetailsProductCode' }),
                $.extend(textCol('batch_no', 'Batch No', 'batch_no', 110), { group: 'Details', modal: '#quDetailsBatch' }),
                $.extend(textCol('specific_qty', 'Specific Qty', 'specific_qty', 100), { group: 'Details', modal: '#quDetailsSpecificQty' }),
                $.extend(textCol('label_code', 'Label Code', 'label_code', 110), { group: 'Details', modal: '#quDetailsLabelCode' }),
                $.extend(textCol('label_reference', 'Label Reference', 'label_reference', 120), { group: 'Details', modal: '#quDetailsLabelRef' }),
                $.extend(textCol('item_location', 'Item Location', 'item_location', 120), { group: 'Details', modal: '#quDetailsLocation' }),
                $.extend(textCol('description', 'Description', 'description', 220), { group: 'Details', modal: '#quDetailsDescription' })
            ];
            var QU_COLUMN_MAP = {};
            QU_COLUMNS.forEach(function(c) { QU_COLUMN_MAP[c.key] = c; });

            function defaultColumnKeys() {
                return QU_COLUMNS.filter(function(c) { return c.def; }).map(function(c) { return c.key; });
            }

            function loadColumnKeys() {
                try {
                    var saved = JSON.parse(localStorage.getItem(QU_COLUMNS_STORAGE) || 'null');
                    if (Array.isArray(saved)) {
                        return saved.filter(function(k) { return !!QU_COLUMN_MAP[k]; });
                    }
                } catch (e) {}
                return defaultColumnKeys();
            }

            var quVisibleKeys = loadColumnKeys();

            function visibleColumns() {
                return QU_COLUMNS.filter(function(c) { return quVisibleKeys.indexOf(c.key) !== -1; });
            }

            function isColumnVisible(key) {
                return quVisibleKeys.indexOf(key) !== -1;
            }

            function tableColspan() {
                return visibleColumns().length + 2;
            }

            function productBrandName(p) {
                if (p.brand && p.brand.name) return p.brand.name;
                if (p.brand_id) {
                    var known = quBrands.filter(function(b) { return String(b.id) === String(p.brand_id); })[0];
                    if (known) return known.name;
                }
                return '';
            }

            function categorySelectHtml(selectedId) {
                selectedId = String(selectedId || '');
                var html = '<select class="form-control form-control-sm qu-cell" data-col="category"><option value="">Select Category</option>';
                quCategories.forEach(function(c) {
                    var sel = String(c.id) === selectedId ? ' selected' : '';
                    html += '<option value="' + escapeHtml(c.id) + '"' + sel + '>' + escapeHtml(c.name) + '</option>';
                });
                return html + '</select>';
            }

            function cellHtml(col, value) {
                if (col.type === 'brand') {
                    return brandSelectHtml(value).replace('class="form-control form-control-sm qu-brand"', 'class="form-control form-control-sm qu-brand qu-cell" data-col="brand"');
                }
                if (col.type === 'category') {
                    return categorySelectHtml(value);
                }
                if (col.type === 'number') {
                    return '<input type="number" step="0.01" min="0" class="form-control form-control-sm qu-cell" data-col="' + col.key + '" value="' + escapeHtml(value) + '" placeholder="—">';
                }
                return '<input type="text" class="form-control form-control-sm qu-cell" data-col="' + col.key + '" value="' + escapeHtml(value) + '">';
            }

            function cellEl($row, key) {
                return $row.find('[data-col="' + key + '"]');
            }

            function cellValue($row, key) {
                var $el = cellEl($row, key);
                if (!$el.length) return undefined;
                if (key === 'brand') return brandFieldValue($el);
                var v = $el.val();
                return v == null ? '' : String(v);
            }

            function origValue(id, key) {
                var p = quRowData[id];
                if (!p || !QU_COLUMN_MAP[key]) return '';
                var v = QU_COLUMN_MAP[key].get(p);
                return v == null ? '' : String(v);
            }

            function rowValue(id, $row, key) {
                var v = ($row && $row.length) ? cellValue($row, key) : undefined;
                return v === undefined ? origValue(id, key) : v;
            }

            function captureDrafts() {
                $('#quickUpdateProductsBody tr.quick-update-row').each(function() {
                    var $row = $(this);
                    var id = $row.data('id');
                    visibleColumns().forEach(function(col) {
                        var v = cellValue($row, col.key);
                        if (v === undefined) return;
                        quDrafts[id] = quDrafts[id] || {};
                        if (v !== origValue(id, col.key)) {
                            quDrafts[id][col.key] = v;
                        } else {
                            delete quDrafts[id][col.key];
                        }
                    });
                });
            }

            function renderColumnChooser() {
                var groups = {};
                var order = [];
                QU_COLUMNS.forEach(function(c) {
                    if (!groups[c.group]) { groups[c.group] = []; order.push(c.group); }
                    groups[c.group].push(c);
                });
                var html = '';
                order.forEach(function(g) {
                    html += '<div class="qu-col-group-title">' + escapeHtml(g) + '</div><div class="qu-col-grid">';
                    groups[g].forEach(function(c) {
                        html += '<label><input type="checkbox" class="qu-col-toggle" value="' + c.key + '"'
                            + (isColumnVisible(c.key) ? ' checked' : '') + '> ' + escapeHtml(c.label) + '</label>';
                    });
                    html += '</div>';
                });
                $('#quickUpdateColumnsList').html(html);
                $('#quickUpdateColumnsCount').text(quVisibleKeys.length);
            }

            function renderTableHead() {
                var html = '<th style="min-width: 50px;">#</th>';
                visibleColumns().forEach(function(c) {
                    html += '<th style="min-width: ' + c.width + 'px;">' + escapeHtml(c.label) + '</th>';
                });
                html += '<th style="min-width: 170px;">Action</th>';
                $('#quickUpdateProductsHead').html(html);
            }

            function rowHtml(p, sno) {
                var drafts = quDrafts[p.id] || {};
                var html = '<tr class="quick-update-row" data-id="' + p.id + '"><td>' + sno + '</td>';
                visibleColumns().forEach(function(col) {
                    var value = Object.prototype.hasOwnProperty.call(drafts, col.key) ? drafts[col.key] : col.get(p);
                    html += '<td>' + cellHtml(col, value == null ? '' : value) + '</td>';
                });
                html += '<td><div class="d-flex gap-1 flex-nowrap">'
                    + '<button type="button" class="btn btn-sm btn-warning qu-save-btn" style="color:#fff; min-width:72px;"><i class="fas fa-save me-1"></i>Update</button>'
                    + '<button type="button" class="btn btn-sm btn-outline-secondary qu-more-btn" title="More details"><i class="fas fa-ellipsis-h"></i> More</button>'
                    + '</div></td></tr>';
                return html;
            }

            function renderRows() {
                var $body = $('#quickUpdateProductsBody');
                destroyBrandSelects($body);
                if (!quRows.length) {
                    $body.html('<tr><td colspan="' + tableColspan() + '" class="text-center text-muted py-4">No products found.</td></tr>');
                    return;
                }
                var html = '';
                quRows.forEach(function(p, idx) {
                    html += rowHtml(p, ((quPage - 1) * quPerPage) + idx + 1);
                });
                $body.html(html);
                initBrandSelect($body.find('.qu-brand'), false);
            }

            function applyColumnChange(keys) {
                captureDrafts();
                quVisibleKeys = QU_COLUMNS.map(function(c) { return c.key; }).filter(function(k) { return keys.indexOf(k) !== -1; });
                try { localStorage.setItem(QU_COLUMNS_STORAGE, JSON.stringify(quVisibleKeys)); } catch (e) {}
                renderColumnChooser();
                renderTableHead();
                if (!quLoading) renderRows();
            }

            function mergeSavedProduct(id, sent, resData) {
                var p = quRowData[id];
                if (!p) return;
                if (resData && typeof resData === 'object') {
                    $.extend(p, resData);
                }
                ['name_hindi', 'hsn_code', 'product_code', 'batch_no', 'specific_qty', 'label_code', 'label_reference',
                    'item_location', 'minimum_stock_qty', 'cost_price', 'landing_cost', 'description', 'category_id'].forEach(function(f) {
                    if (Object.prototype.hasOwnProperty.call(sent, f)) p[f] = sent[f];
                });
                if (Object.prototype.hasOwnProperty.call(sent, 'brand_id')) {
                    p.brand_id = sent.brand_id || null;
                    var b = quBrands.filter(function(x) { return String(x.id) === String(sent.brand_id); })[0];
                    p.brand = b ? { id: b.id, name: b.name } : null;
                }
                delete quDrafts[id];
            }

            function syncRowInputs($row, id) {
                var p = quRowData[id];
                if (!p || !$row.length) return;
                visibleColumns().forEach(function(col) {
                    var $el = cellEl($row, col.key);
                    if (!$el.length) return;
                    if (col.key === 'brand') {
                        setBrandValue($el, col.get(p));
                    } else {
                        $el.val(col.get(p));
                    }
                });
            }

            function escapeHtml(str) {
                return String(str == null ? '' : str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;');
            }

            function extractWholesale(product) {
                var rates = product && product.selling_prices;
                if (typeof rates === 'string') {
                    try { rates = JSON.parse(rates); } catch (e) { rates = []; }
                }
                if (!Array.isArray(rates)) return '';
                for (var i = 0; i < rates.length; i++) {
                    var name = String((rates[i] && rates[i].name) || '');
                    if (/wholesale/i.test(name) && rates[i].price != null && rates[i].price !== '') {
                        return rates[i].price;
                    }
                }
                return '';
            }

            function extractRate2(product) {
                var rates = product && product.selling_prices;
                if (typeof rates === 'string') {
                    try { rates = JSON.parse(rates); } catch (e) { rates = []; }
                }
                if (!Array.isArray(rates)) {
                    return { price: '', mrp: '', name: 'Rate 2' };
                }
                for (var i = 0; i < rates.length; i++) {
                    var name = String((rates[i] && rates[i].name) || '');
                    if (/^rate\s*2$/i.test(name.trim())) {
                        return {
                            price: rates[i].price != null ? rates[i].price : '',
                            mrp: rates[i].mrp != null ? rates[i].mrp : '',
                            name: name || 'Rate 2'
                        };
                    }
                }
                for (var j = 0; j < rates.length; j++) {
                    var n = String((rates[j] && rates[j].name) || '').trim();
                    if (/wholesale/i.test(n) || n === '' || /^rate\s*1$/i.test(n)) continue;
                    return {
                        price: rates[j].price != null ? rates[j].price : '',
                        mrp: rates[j].mrp != null ? rates[j].mrp : '',
                        name: n || 'Rate 2'
                    };
                }
                return { price: '', mrp: '', name: 'Rate 2' };
            }

            function numOrEmpty(v) {
                if (v === null || v === undefined || v === '') return '';
                var n = parseFloat(v);
                return Number.isFinite(n) ? n : '';
            }

            function loadQuickUpdateProducts(page) {
                if (quLoading) return;
                quLoading = true;
                quPage = page || 1;
                var $body = $('#quickUpdateProductsBody');
                destroyBrandSelects($body);
                $body.html('<tr><td colspan="' + tableColspan() + '" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> Loading…</td></tr>');
                $('#quickUpdatePrevBtn, #quickUpdateNextBtn').prop('disabled', true);

                $.ajax({
                    url: '/api/getAllProduct',
                    method: 'GET',
                    data: {
                        page: quPage,
                        per_page: quPerPage,
                        search: ($('#quickUpdateSearch').val() || '').trim(),
                        category_id: $('#quickUpdateCategory').val() || '',
                        sub_branch_id: localStorage.getItem('selectedSubAdminId') || '',
                        quick_update: 1
                    },
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        quLoading = false;
                        if (!res || !res.status) {
                            $body.html('<tr><td colspan="' + tableColspan() + '" class="text-center text-danger py-4">Failed to load products.</td></tr>');
                            return;
                        }
                        var rows = Array.isArray(res.data) ? res.data : [];
                        var pagination = res.pagination || {};
                        quPage = pagination.current_page || quPage;
                        quLastPage = pagination.last_page || 1;
                        var total = pagination.total != null ? pagination.total : rows.length;

                        quRows = rows;
                        quRowData = {};
                        quDrafts = {};
                        rows.forEach(function(p) { quRowData[p.id] = p; });
                        renderRows();

                        $('#quickUpdatePageInfo').text('Page ' + quPage + ' of ' + quLastPage + ' · ' + total + ' products');
                        $('#quickUpdatePrevBtn').prop('disabled', quPage <= 1);
                        $('#quickUpdateNextBtn').prop('disabled', quPage >= quLastPage);
                    },
                    error: function(xhr) {
                        quLoading = false;
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to load products.';
                        $body.html('<tr><td colspan="' + tableColspan() + '" class="text-center text-danger py-4">' + escapeHtml(msg) + '</td></tr>');
                    }
                });
            }

            initBrandSelect($('#quDetailsBrand'), true);
            renderColumnChooser();
            renderTableHead();
            loadQuickUpdateProducts(1);

            $(document).on('change', '#quickUpdateColumnsList .qu-col-toggle', function() {
                var keys = $('#quickUpdateColumnsList .qu-col-toggle:checked').map(function() { return this.value; }).get();
                applyColumnChange(keys);
            });
            $('#quickUpdateColumnsAll').on('click', function() {
                applyColumnChange(QU_COLUMNS.map(function(c) { return c.key; }));
            });
            $('#quickUpdateColumnsDefault').on('click', function() {
                applyColumnChange(defaultColumnKeys());
            });
            $(document).on('keydown', '#quickUpdateProductsBody input.qu-cell', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $(this).closest('tr.quick-update-row').find('.qu-save-btn').trigger('click');
                }
            });

            $('#quickUpdateRefreshBtn').on('click', function() {
                loadQuickUpdateProducts(quPage);
            });
            $('#quickUpdatePrevBtn').on('click', function() {
                if (quPage > 1) loadQuickUpdateProducts(quPage - 1);
            });
            $('#quickUpdateNextBtn').on('click', function() {
                if (quPage < quLastPage) loadQuickUpdateProducts(quPage + 1);
            });
            $('#quickUpdateSearch').on('input', function() {
                clearTimeout(quSearchTimer);
                quSearchTimer = setTimeout(function() {
                    loadQuickUpdateProducts(1);
                }, 350);
            });
            $('#quickUpdateCategory').on('change', function() {
                loadQuickUpdateProducts(1);
            });

            $(document).on('click', '#quickUpdateProductsBody .qu-save-btn', function() {
                var $btn = $(this);
                var $row = $btn.closest('tr.quick-update-row');
                var id = $row.data('id');
                if (!id) return;

                var name = String(rowValue(id, $row, 'name') || '').trim();
                var price = rowValue(id, $row, 'price');
                if (!name) {
                    Swal.fire('Required', 'Product name is required.', 'warning');
                    return;
                }
                if (price === '' || isNaN(parseFloat(price))) {
                    Swal.fire('Required', 'Valid price is required.', 'warning');
                    return;
                }

                var data = { product_id: id, name: name, price: price };
                visibleColumns().forEach(function(col) {
                    if (col.key === 'name' || col.key === 'price' || col.key === 'brand'
                        || col.key === 'rate2_price' || col.key === 'rate2_mrp') return;
                    data[col.field] = cellValue($row, col.key);
                });
                if (isColumnVisible('rate2_price') || isColumnVisible('rate2_mrp')) {
                    data.rate2_price = rowValue(id, $row, 'rate2_price');
                    data.rate2_mrp = rowValue(id, $row, 'rate2_mrp');
                    data.rate2_name = extractRate2(quRowData[id] || {}).name || 'Rate 2';
                }
                var brandVisible = isColumnVisible('brand');

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                ensureBrandId(brandVisible ? cellValue($row, 'brand') : '').done(function(brandId) {
                if (brandVisible) {
                    data.brand_id = brandId;
                }
                $.ajax({
                    url: '/api/quick-update-product',
                    method: 'POST',
                    data: data,
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update');
                        if (!res || !res.status) {
                            Swal.fire('Error', (res && res.message) || 'Update failed.', 'error');
                            return;
                        }
                        mergeSavedProduct(id, data, res.data);
                        if (brandVisible && !brandId) {
                            setBrandValue(cellEl($row, 'brand'), '');
                        }
                        $row.css('background', '#e8f8ef');
                        setTimeout(function() { $row.css('background', ''); }, 900);
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update');
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Update failed.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
                }).fail(function(err) {
                    $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update');
                    Swal.fire('Brand', (err && err.message) ? err.message : 'Could not create brand.', 'error');
                });
            });

            function resetQuickUpdateDetailsForm() {
                $('#quDetailsProductId').val('');
                $('#quDetailsProductHint').text('');
                $('#quDetailsBaseName, #quDetailsBaseSku, #quDetailsBaseBarcode, #quDetailsBaseMrp, #quDetailsBasePrice, #quDetailsBaseWholesale, #quDetailsBasePurchase, #quDetailsBaseQty').val('');
                $('#quDetailsCategory').val('');
                setBrandValue($('#quDetailsBrand'), '');
                $('#quDetailsHsn, #quDetailsProductCode, #quDetailsBatch, #quDetailsSpecificQty, #quDetailsLabelCode, #quDetailsLabelRef, #quDetailsLocation, #quDetailsDescription').val('');
                $('#quDetailsMinStock, #quDetailsCost, #quDetailsLanding, #quDetailsRate2Price, #quDetailsRate2Mrp').val('');
            }

            function fillQuickUpdateDetails(product, $row) {
                $('#quDetailsProductId').val(product.id);
                $('#quDetailsProductHint').text(product.name ? ('— ' + product.name) : '');
                function rowOr(key) {
                    var v = ($row && $row.length) ? cellValue($row, key) : undefined;
                    return v === undefined ? QU_COLUMN_MAP[key].get(product) : v;
                }
                $('#quDetailsBaseName').val(String(rowOr('name') || '').trim());
                $('#quDetailsBaseSku').val(rowOr('sku'));
                $('#quDetailsBaseBarcode').val(rowOr('barcode'));
                $('#quDetailsBaseMrp').val(rowOr('mrp'));
                $('#quDetailsBasePrice').val(rowOr('price'));
                $('#quDetailsBaseWholesale').val(rowOr('wholesale'));
                $('#quDetailsBasePurchase').val(rowOr('purchase'));
                $('#quDetailsBaseQty').val(rowOr('qty'));
                $('#quDetailsCategory').val(rowOr('category') || '');
                setBrandValue($('#quDetailsBrand'), rowOr('brand'));
                $('#quDetailsHsn').val(product.hsn_code || '');
                $('#quDetailsProductCode').val(product.product_code || '');
                $('#quDetailsBatch').val(product.batch_no || '');
                $('#quDetailsSpecificQty').val(product.specific_qty || '');
                $('#quDetailsLabelCode').val(product.label_code || '');
                $('#quDetailsLabelRef').val(product.label_reference || '');
                $('#quDetailsLocation').val(product.item_location || '');
                $('#quDetailsMinStock').val(numOrEmpty(product.minimum_stock_qty));
                $('#quDetailsCost').val(numOrEmpty(product.cost_price));
                $('#quDetailsLanding').val(numOrEmpty(product.landing_cost));
                $('#quDetailsDescription').val(product.description || '');
                var rate2 = extractRate2(product);
                $('#quDetailsRate2Price').val(numOrEmpty(rate2.price));
                $('#quDetailsRate2Mrp').val(numOrEmpty(rate2.mrp));
                $('#quDetailsRate2Price').data('rate2-name', rate2.name || 'Rate 2');
                if ($row && $row.length) {
                    QU_COLUMNS.forEach(function(col) {
                        if (!col.modal || col.key === 'brand' || col.key === 'category') return;
                        var v = cellValue($row, col.key);
                        if (v !== undefined) $(col.modal).val(v);
                    });
                }
            }

            $(document).on('click', '#quickUpdateProductsBody .qu-more-btn', function() {
                var $row = $(this).closest('tr.quick-update-row');
                var id = $row.data('id');
                if (!id) return;

                resetQuickUpdateDetailsForm();
                $('#quDetailsLoading').removeClass('d-none');
                var modalEl = document.getElementById('quickUpdateDetailsModal');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                } else {
                    $('#quickUpdateDetailsModal').modal('show');
                }

                var hintName = rowValue(id, $row, 'name');
                $('#quDetailsProductHint').text(hintName ? ('— ' + hintName) : '');
                $('#quDetailsProductId').val(id);

                $.ajax({
                    url: '/api/product-view/' + id,
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $('#quDetailsLoading').addClass('d-none');
                        var product = (res && res.product) ? res.product : res;
                        if (!product || !product.id) {
                            Swal.fire('Error', 'Could not load product details.', 'error');
                            return;
                        }
                        fillQuickUpdateDetails(product, $row);
                    },
                    error: function(xhr) {
                        $('#quDetailsLoading').addClass('d-none');
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Could not load product details.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            $('#quDetailsSaveBtn').on('click', function() {
                var $btn = $(this);
                var id = $('#quDetailsProductId').val();
                if (!id) return;

                var $row = $('#quickUpdateProductsBody tr.quick-update-row[data-id="' + id + '"]');
                function rowOrBase(key, baseSel) {
                    var v = $row.length ? cellValue($row, key) : undefined;
                    return v === undefined ? $(baseSel).val() : v;
                }
                var name = String(rowOrBase('name', '#quDetailsBaseName') || '').trim();
                var price = rowOrBase('price', '#quDetailsBasePrice');

                if (!name) {
                    Swal.fire('Required', 'Product name is required.', 'warning');
                    return;
                }
                if (price === '' || isNaN(parseFloat(price))) {
                    Swal.fire('Required', 'Valid price is required.', 'warning');
                    return;
                }

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving…');
                ensureBrandId(brandFieldValue($('#quDetailsBrand'))).done(function(brandId) {
                var data = {
                        product_id: id,
                        name: name,
                        SKU: rowOrBase('sku', '#quDetailsBaseSku'),
                        barcode: rowOrBase('barcode', '#quDetailsBaseBarcode'),
                        mrp: rowOrBase('mrp', '#quDetailsBaseMrp'),
                        price: price,
                        wholesale_price: rowOrBase('wholesale', '#quDetailsBaseWholesale'),
                        purchase_price: rowOrBase('purchase', '#quDetailsBasePurchase'),
                        quantity: rowOrBase('qty', '#quDetailsBaseQty'),
                        category_id: $('#quDetailsCategory').val(),
                        brand_id: brandId,
                        hsn_code: $('#quDetailsHsn').val(),
                        product_code: $('#quDetailsProductCode').val(),
                        batch_no: $('#quDetailsBatch').val(),
                        specific_qty: $('#quDetailsSpecificQty').val(),
                        label_code: $('#quDetailsLabelCode').val(),
                        label_reference: $('#quDetailsLabelRef').val(),
                        item_location: $('#quDetailsLocation').val(),
                        minimum_stock_qty: $('#quDetailsMinStock').val(),
                        cost_price: $('#quDetailsCost').val(),
                        landing_cost: $('#quDetailsLanding').val(),
                        description: $('#quDetailsDescription').val(),
                        rate2_price: $('#quDetailsRate2Price').val(),
                        rate2_mrp: $('#quDetailsRate2Mrp').val(),
                        rate2_name: $('#quDetailsRate2Price').data('rate2-name') || 'Rate 2'
                };
                if ($row.length && cellValue($row, 'name_hindi') !== undefined) {
                    data.name_hindi = cellValue($row, 'name_hindi');
                }
                $.ajax({
                    url: '/api/quick-update-product',
                    method: 'POST',
                    data: data,
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Update Details');
                        if (!res || !res.status) {
                            Swal.fire('Error', (res && res.message) || 'Update failed.', 'error');
                            return;
                        }
                        var detailsEl = document.getElementById('quickUpdateDetailsModal');
                        if (detailsEl && typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getOrCreateInstance(detailsEl).hide();
                        } else {
                            $('#quickUpdateDetailsModal').modal('hide');
                        }
                        mergeSavedProduct(id, data, res.data);
                        if ($row.length) {
                            syncRowInputs($row, id);
                            $row.css('background', '#e8f8ef');
                            setTimeout(function() { $row.css('background', ''); }, 900);
                        }
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Details updated',
                            showConfirmButton: false,
                            timer: 1600
                        });
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Update Details');
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Update failed.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
                }).fail(function(err) {
                    $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Update Details');
                    Swal.fire('Brand', (err && err.message) ? err.message : 'Could not create brand.', 'error');
                });
            });
        });
    </script>
@endpush
