@extends('layout.app')

@section('title', 'Material Inventory List')

@section('content')
    <style>
        /* Inventory Tabs Navigation */
        .inventory-tab-nav {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .inventory-tab-btn {
            font-size: 13px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 6px;
            white-space: nowrap;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        /* Action Buttons Group */
        .inventory-actions-wrap {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-wrap: nowrap;
        }

        .inventory-act-btn {
            color: #fff !important;
            font-size: 12px !important;
            padding: 5px 10px !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
            border-radius: 4px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-decoration: none !important;
        }

        .inventory-act-btn:hover {
            opacity: 0.9;
            color: #fff !important;
        }

        @media screen and (max-width: 768px) {
            .inventory-tab-nav {
                display: flex !important;
                flex-wrap: nowrap !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
                padding-bottom: 6px !important;
                margin-bottom: 12px !important;
                gap: 6px !important;
                width: 100% !important;
            }

            .inventory-tab-btn {
                font-size: 12px !important;
                padding: 6px 10px !important;
                flex-shrink: 0 !important;
            }

            .inventory-actions-wrap {
                display: flex !important;
                flex-direction: column !important;
                gap: 4px !important;
                align-items: stretch !important;
                min-width: 105px !important;
            }

            .inventory-act-btn {
                font-size: 11px !important;
                padding: 4px 6px !important;
                white-space: nowrap !important;
                width: 100% !important;
                text-align: center !important;
                border-radius: 4px !important;
            }
        }

        .table tbody tr td {
            white-space: normal !important;
        }

        .sorting_1 {
            /* display: flex !important; */
            /* align-items: center !important;
                                            gap: 5px !important; */
        }

        .table-scroll-top {
            overflow-x: auto;
            overflow-y: hidden;
            height: 20px;
            width: 100%;
            margin-bottom: 5px;
            display: none;
        }

        .table-scroll-top div {
            height: 1px;
        }


        /* Product Name column word wrap only */
        #inventory-table tbody td:nth-child(1),
        #inventory-table thead th:nth-child(1) {
            white-space: normal !important;
            /* word-break: break-word; */
            overflow-wrap: break-word;
            max-width: 180px;
        }

        /* Pagination Styling */
        .pagination .page-item .page-link {
            background-color: #5d6d7e;
            color: #fff;
            border: none;
             margin: 0 3px;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: bold;
        }

        .pagination .page-item.active .page-link {
            background-color: #ff9f43 !important;
            color: #fff;
        }

        .pagination .page-item .page-link:hover {
            background-color: #4a5766;
            color: #fff;
        }

        .pagination .page-item.active .page-link:hover {
            background-color: #e68a35 !important;
        }

        /* Previous and Next buttons */
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            background-color: #fff;
            color: #6c757d;
            border: 1px solid #dee2e6;
        }

        .pagination .page-item:first-child .page-link:hover,
        .pagination .page-item:last-child .page-link:hover {
            background-color: #f8f9fa;
            color: #495057;
            border-color: #dee2e6;
        }

        /* Responsive breakpoints for all screen sizes */

        /* Extra small devices (phones, less than 576px) */
        @media screen and (max-width: 575.98px) {
            /* Filter Section Mobile Styles */
            /* .card-body > .d-flex { */
            /* flex-direction: column !important; */
            /* gap: 10px !important; */
            /* } */

            .search-set {
                width: 100% !important;
                margin: 0 0 10px 0 !important;
            }

            .search-set.d-flex.mt-2 {
                width: 100% !important;
                margin: 0 0 15px 0 !important;
            }

            .search-set.d-flex.mt-2>.d-flex {
                flex-direction: column !important;
                width: 100% !important;
                gap: 10px !important;
            }

            .search-set.d-flex.mt-2>.d-flex>div {
                width: 100% !important;
            }

            .search-set.d-flex.mt-2 label {
                font-size: 13px;
                margin-bottom: 5px;
            }

            .search-set.d-flex.mt-2 input[type="date"] {
                width: 100% !important;
                font-size: 14px;
                padding: 8px;
            }

            .table-responsive {
                display: block !important;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table-scroll-top {
                display: block;
            }

            .table {
                font-size: 11px;
            }

            .table th,
            .table td {
                padding: 6px 3px;
                white-space: nowrap;
            }

            .inventory-toggle-btn-table {
                margin: 0 auto;
                display: block;
            }

            select#filterCategory,
            select#filterBrand {
                font-size: 12px !important;
            }

            .form-control-sm {
                min-height: calc(1.5em + .5rem + 2px);
                padding: .25rem .5rem;
                font-size: 13px !important;
                border-radius: .2rem;
            }

            div#DataTables_Table_0_filter {
                margin-top: 10px !important;
            }

            .table-top {
                flex-direction: row;
                margin-bottom: 0 !important;
            }

            .dataTables_length {
                margin-left: .8rem !important;
                margin-bottom: .1rem !important;
            }

            .dataTables_filter {
                text-align: left !important;
            }
        }

        /* Small devices (landscape phones, 576px and up) */
        @media screen and (min-width: 576px) and (max-width: 767.98px) {

            /* Filter Section Small Mobile Styles */
            .card-body>.d-flex {
                flex-wrap: wrap !important;
                gap: 10px !important;
            }

            .search-set.d-flex.mt-2>.d-flex {
                flex-direction: row !important;
                gap: 10px !important;
            }

            .search-set.d-flex.mt-2>.d-flex>div {
                flex: 1;
                min-width: 0;
            }

            .search-set.d-flex.mt-2 input[type="date"] {
                width: 100% !important;
                font-size: 14px;
            }

            .table-responsive {
                display: block !important;
                overflow-x: auto;
            }

            .table-scroll-top {
                display: block;
            }

            .table {
                font-size: 12px;
            }

            .table th,
            .table td {
                padding: 8px 4px;
                white-space: nowrap;
            }

            .inventory-toggle-btn-table {
                margin: 0 auto;
                display: block;
            }

            .inventory-toggle-btn-table {
                margin: 0 auto;
                display: block;
            }
        }

        /* Medium devices (tablets, 768px and up to 1024px) */
        @media screen and (min-width: 768px) and (max-width: 1024px) {
            .table-responsive {
                display: block !important;
                overflow-x: auto;
            }

            .table-scroll-top {
                display: block;
            }

            .table {
                font-size: 13px;
            }

            .table th,
            .table td {
                padding: 8px 6px;
                white-space: nowrap;
            }

            .inventory-toggle-btn-table {
                margin: 0 auto;
                display: block;
            }

            /* Show expandable rows on tablets */
            .inventory-details-row {
                display: none;
            }

            .inventory-details-row.show {
                display: table-row;
            }
        }

        /* Large devices (desktops, 1025px and up) */
        @media screen and (min-width: 1025px) {
            .table-responsive {
                display: block !important;
            }

            .table {
                font-size: 14px;
            }

            .table th,
            .table td {
                padding: 12px 10px;
                white-space: nowrap;
            }
        }

        /* Expandable row details - available for all screen sizes */
        .inventory-details-row {
            display: none;
        }

        .inventory-details-row.show {
            display: table-row;
        }

        /* Expandable content styles */
        .inventory-details-content {
            padding: 15px;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        .inventory-details-list {
            margin-bottom: 15px;
        }

        .inventory-detail-row-simple {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .inventory-detail-row-simple:last-of-type {
            border-bottom: none;
        }

        .inventory-detail-label-simple {
            font-weight: 600;
            color: #595b5d;
            font-size: 14px;
        }

        .inventory-detail-value-simple {
            color: #1b2850;
            font-size: 14px;
            text-align: right;
        }

        .inventory-action-buttons-simple {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-start;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }

        /* Toggle button styles */
        .inventory-toggle-btn-table {
            background: #ff9f43;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .inventory-toggle-btn-table:hover {
            background: #ff8c2e;
        }

        .inventory-toggle-btn-table.minus {
            background: #dc3545;
        }

        .inventory-toggle-btn-table.minus:hover {
            background: #c82333;
        }

        .modal-content {
            width: 80% !important;
        }

        /* Custom Pagination Styling */
        .pagination .page-item .page-link {
            background-color: #5d6d7e;
            /* Dark gray for other pages */
            color: #fff;
            border: none;
             margin: 0 3px;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: bold;
        }

        .pagination .page-item.active .page-link {
            background-color: #ff9f43 !important;
            /* Orange for active page */
            color: #fff;
        }

        .pagination .page-item .page-link:hover {
            background-color: #4a5766;
            color: #fff;
        }

        .pagination .page-item.active .page-link:hover {
            background-color: #e68a35 !important;
        }

        /* Search input styling */
        .search-input input {
            padding-left: 35px !important;
            border-radius: 5px;
        }

        .search-input {
            position: relative;
            display: flex;
            align-items: center;
        }

        .btn-searchset {
            position: absolute;
            left: 10px;
            z-index: 10;
            padding: 0;
            top: 18px !important;
        }

        /* ✅ Hide default DataTables search box completely */
        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }

        /* Remove extra top spacing created by DataTables */
        .dataTables_wrapper .row:first-child {
            display: none !important;
        }

        /* Remove unwanted search input alignment space */
        .dataTables_wrapper {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        /* Dark Mode Overrides for Inventory Expandable Variants */
        :root[data-theme="dark"] .variant-container {
            background-color: #0f172a !important;
        }

        :root[data-theme="dark"] .variant-table {
            background-color: #0f172a !important;
            color: #d1d5db !important;
            border-color: #323248 !important;
        }

        :root[data-theme="dark"] .variant-data-row {
            background-color: #0f172a !important;
        }

        :root[data-theme="dark"] .variant-data-row td {
            border-bottom-color: #323248 !important;
            color: #d1d5db !important;
        }

        :root[data-theme="dark"] .variant-table thead.table-dark th {
            color: #fff !important;
            border-color: #323248 !important;
            background-color:#334155 !important;
        }
    </style>
    @include('inventory.partials.mobile-ui-css')
    @if (session('error'))
        <div class="alert alert-danger" id="error-message">
            {{ session('error') }}
        </div>

        <style>
            .fade-out {
                opacity: 1;
                transition: opacity 0.5s ease-out;
            }

            .fade-out.hidden {
                opacity: 0;
            }
        </style>

        <script>
            setTimeout(function() {
                let alert = document.getElementById('error-message');
                if (alert) {
                    alert.classList.add('hidden'); // Triggers the fade-out transition
                    // Remove the element from DOM after fadeout (optional)
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500); // match the CSS transition duration (0.5s)
                }
            }, 4000);
        </script>
    @endif

    <!-- Quantity History Modal (unchanged) -->
    <div class="modal fade" id="quantityHistoryModal" tabindex="-1" aria-labelledby="quantityHistoryLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quantityHistoryLabel">Quantity History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="quantityHistoryContent"></div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Material Inventory</h4>
            </div>
            <div class="page-btn">
                
                @if (app('hasPermission')(49, 'add'))
                    <a href="{{ route('row_material.add') }}" class="btn btn-sm btn-added">
                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/plus.svg' }}" alt="img" class="me-1"/>New
                        Row Material
                    </a>
                @endif
            </div>
        </div>

        <div class="inv-m-head d-md-none">
            <div class="inv-m-head-top">
                <h4>Material Inventory</h4>
                @if (app('hasPermission')(49, 'add'))
                <a href="{{ route('row_material.add') }}" class="inv-m-link"><i class="fa fa-plus"></i> New Material</a>
                @endif
            </div>
            <div class="inv-m-tabs">
                <a href="{{ route('inventory.list') }}" class="{{ request()->routeIs('inventory.list') ? 'is-active' : '' }}">Stock</a>
                <a href="{{ route('material-inventory.list') }}" class="{{ request()->routeIs('material-inventory.list') ? 'is-active' : '' }}">Material</a>
                <a href="{{ route('exchange-inventory.list') }}" class="{{ request()->routeIs('exchange-inventory.list') ? 'is-active' : '' }}">Exchange</a>
            </div>
        </div>

        <div class="inventory-tab-nav mb-3 inv-tabs-row">
            <a href="{{ route('inventory.list') }}" class="btn btn-sm inventory-tab-btn {{ request()->routeIs('inventory.list') ? 'btn-primary' : 'btn-outline-primary' }}">Stock Inventory</a>
            <a href="{{ route('material-inventory.list') }}" class="btn btn-sm inventory-tab-btn {{ request()->routeIs('material-inventory.list') ? 'btn-primary' : 'btn-outline-primary' }}">Material Inventory</a>
            <a href="{{ route('exchange-inventory.list') }}" class="btn btn-sm inventory-tab-btn {{ request()->routeIs('exchange-inventory.list') ? 'btn-primary' : 'btn-outline-primary' }}">Exchange Inventory</a>
        </div>

        <div class="card inv-main-card">
            <div class="card-body">
                <!-- SINGLE ROW FILTER SECTION -->
                <div class="row g-2 align-items-end mb-3 inv-filter-row">
                    <!-- Search (left) -->
                    <div class="col-md-3 col-12 inv-search-col">
                        <button type="button" class="inv-m-filter-btn d-md-none" id="invDateToggle" aria-label="Date filter" aria-expanded="false">
                            <i class="fa fa-filter"></i>
                        </button>
                        <div class="search-input position-relative">
                            <a class="btn btn-searchset position-absolute"
                                style="left: 10px; top: 50%; transform: translateY(-50%); z-index: 10;">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}"
                                    alt="img">
                            </a>
                            <input type="text" id="inventory-search-input" class="form-control form-control-sm"
                                placeholder="Search..." style="padding-left: 35px;">
                        </div>
                    </div>

                    <!-- Right-aligned date filters -->
                    <div class="col-md-auto ms-auto inv-date-col">
                        <div class="d-flex  row ">
                            <div class="col-6">
                                <label class="form-label mb-1">Start Date</label>
                                <input type="date" id="startDate" class="form-control form-control-sm">
                            </div>
                            <div class="col-6">
                                <label class="form-label mb-1">End Date</label>
                                <input type="date" id="endDate" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>


                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="table" id="inventory-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Initial Stock</th>
                                <th>Current Stock</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="inv-card-list d-md-none" id="inventoryCardList"></div>

                <!-- PAGINATION CONTROLS -->
                <div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3"
                    style="display: none;">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
                        <select id="per-page-select-bottom" class="form-select form-select-sm"
                            style="width: auto; border: 1px solid #ddd;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="ms-3" style="font-size: 14px; color: #555;">
                            <span id="pagination-from">0</span> - <span id="pagination-to">0</span> of <span
                                id="pagination-total">0</span> items
                        </span>
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0" id="pagination-numbers"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    @if (app('hasPermission')(17, 'add') || app('hasPermission')(17, 'edit'))
    <!-- Edit Product Modal -->
    <div class="modal fade ep-modal" id="editProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="ep-head">
                        <h5 class="modal-title">Update Stock</h5>
                        <small>Add or remove quantity for this product</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
                </div>
                <form id="editProductForm">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editProductId">
                        <input type="hidden" name="product_variant_id" id="editProductVariantId">

                        <div class="ep-summary">
                            <input type="text" class="ep-name" name="name" id="editProductName" readonly tabindex="-1">
                            <div class="ep-stats">
                                <div class="ep-stat">
                                    <span>Price</span>
                                    <div class="ep-stat-val"><em>₹</em><input type="number" name="price" id="editProductPrice" readonly tabindex="-1"></div>
                                </div>
                                <div class="ep-stat">
                                    <span>Initial</span>
                                    <input type="number" name="initial_stock" id="editProductinitialStock" step="0.01" readonly tabindex="-1">
                                </div>
                                <div class="ep-stat is-current">
                                    <span>Current</span>
                                    <input type="number" name="current_stock" id="editProductStock" step="0.01" readonly tabindex="-1">
                                </div>
                            </div>
                        </div>

                        <label class="ep-label">What do you want to do?</label>
                        <div class="ep-actions">
                            <input type="radio" name="stock_action" id="addStock" value="add">
                            <label for="addStock" class="ep-action is-add"><i class="fa fa-plus"></i> Add Stock</label>
                            <input type="radio" name="stock_action" id="minusStock" value="minus">
                            <label for="minusStock" class="ep-action is-minus"><i class="fa fa-minus"></i> Remove Stock</label>
                        </div>

                        <div class="ep-qty d-none" id="stockQuantityBox">
                            <label class="ep-label" id="stockQuantityLabel" for="stockQuantity">Quantity</label>
                            <div class="ep-stepper">
                                <button type="button" class="ep-step" data-step="-1" aria-label="Decrease">−</button>
                                <input type="number" class="form-control" name="stock_quantity" id="stockQuantity"
                                    step="0.01" min="0.01" inputmode="decimal" placeholder="0">
                                <button type="button" class="ep-step" data-step="1" aria-label="Increase">+</button>
                            </div>
                            <div class="ep-preview" id="epNewStock"></div>
                        </div>

                        <div id="stockError" class="ep-error" style="display:none;"></div>

                        <label class="ep-label" for="stockRemarks">Remarks <small>(optional)</small></label>
                        <textarea class="form-control" name="remarks" id="stockRemarks" rows="2"
                            placeholder="e.g. New purchase, damaged items"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light ep-btn" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary ep-btn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Transfer Stock Modal -->
    <div class="modal fade ep-modal" id="transferStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="ep-head">
                        <h5 class="modal-title">Transfer Stock</h5>
                        <small>Move quantity to another branch</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
                </div>
                <form id="transferStockForm">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="transferProductId">
                        <input type="hidden" id="transferFromBranchId" name="from_branch_id">

                        <div class="ep-summary">
                            <input type="text" class="ep-name" id="transferProductName" readonly tabindex="-1">
                            <div class="ep-stats ep-stats-1">
                                <div class="ep-stat is-current">
                                    <span>Available Stock</span>
                                    <input type="number" id="transferAvailableStock" readonly tabindex="-1">
                                </div>
                            </div>
                        </div>

                        <div class="ep-branches">
                            <div class="ep-branch">
                                <label class="ep-label">From</label>
                                <input type="text" class="form-control" id="transferFromBranchName" readonly tabindex="-1">
                            </div>
                            <div class="ep-branch-arrow" aria-hidden="true"><i class="fa fa-arrow-right"></i></div>
                            <div class="ep-branch">
                                <label class="ep-label" for="transferToBranch">To <span class="text-danger">*</span></label>
                                <select class="form-select" name="to_branch_id" id="transferToBranch" required>
                                    <option value="">Select branch</option>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="ep-qty">
                            <label class="ep-label" for="transferQuantity">Transfer Quantity <span class="text-danger">*</span></label>
                            <div class="ep-stepper">
                                <button type="button" class="ep-step" data-target="#transferQuantity" data-step="-1" aria-label="Decrease">−</button>
                                <input type="number" class="form-control" name="quantity" id="transferQuantity"
                                    step="0.01" min="0.01" inputmode="decimal" placeholder="0" required>
                                <button type="button" class="ep-step" data-target="#transferQuantity" data-step="1" aria-label="Increase">+</button>
                            </div>
                            <div class="ep-preview" id="transferPreview"></div>
                        </div>

                        <div id="transferError" class="ep-error" style="display:none;"></div>

                        <label class="ep-label" for="transferRemarks">Remarks <small>(optional)</small></label>
                        <textarea class="form-control" name="remarks" id="transferRemarks" rows="2"
                            placeholder="e.g. Sent for festival sale"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light ep-btn" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary ep-btn"><i class="fa fa-exchange-alt me-1"></i> Transfer Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@push('js')
    @include('inventory.partials.mobile-ui-js')
    {{-- <script>
        // Global variables
        var inventoryTable;

        function formatInventoryPrice(value) {
            if (value === null || value === undefined || value === '' || value === 'N/A') {
                return 'N/A';
            }

            const numericValue = Number(value);
            if (Number.isNaN(numericValue)) {
                return value;
            }

            return numericValue.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Helper function to build expandable row content
        function buildInventoryExpandableRowContent(product) {
            let actionBtns = `
                <a class="btn btn-sm btn-primary me-2" href="/inventory-View/${product.id}" style="color: white;font-size: 13px;">
                    View History
                </a>
                <a class="btn btn-sm btn-primary edit-product me-2" data-id="${product.id}" href="javascript:void(0);" style="color: white;font-size: 13px;">
                    Add / Edit Stock
                </a>
                <a class="btn btn-sm btn-success transfer-stock" data-id="${product.id}" href="javascript:void(0);" style="color: white;font-size: 13px;">
                    Transfer Stock
                </a>
            `;

            return `
                <td colspan="7" class="inventory-details-content">
                    <div class="inventory-details-list">
                        <div class="inventory-detail-row-simple">
                            <span class="inventory-detail-label-simple">SKU:</span>
                            <span class="inventory-detail-value-simple">${product.sku || 'N/A'}</span>
                        </div>
                        <div class="inventory-detail-row-simple">
                            <span class="inventory-detail-label-simple">Price:</span>
                            <span class="inventory-detail-value-simple">${formatInventoryPrice(product.price)}</span>
                        </div>
                        <div class="inventory-detail-row-simple">
                            <span class="inventory-detail-label-simple">Initial Stock:</span>
                            <span class="inventory-detail-value-simple">${product.initial_stock ?? 'N/A'}</span>
                        </div>
                        <div class="inventory-detail-row-simple">
                            <span class="inventory-detail-label-simple">Current Stock:</span>
                            <span class="inventory-detail-value-simple">${product.current_stock ?? 'N/A'}</span>
                        </div>
                        <div class="inventory-detail-row-simple">
                            <span class="inventory-detail-label-simple">Remarks:</span>
                            <span class="inventory-detail-value-simple">${product.remarks || 'N/A'}</span>
                        </div>
                    </div>
                    <div class="inventory-action-buttons-simple">
                        ${actionBtns}
                    </div>
                </td>
            `;
        }

        // Toggle function for table rows - must be global
        window.toggleInventoryRowDetails = function(productId) {
            const btn = $(`.inventory-toggle-btn-table[data-product-id="${productId}"]`);
            if (btn.length === 0) {
                // console.error('Toggle button not found for product:', productId);
                return;
            }

            const row = btn.closest('tr');
            let detailsRow = row.next(`tr.inventory-details-row[data-product-id="${productId}"]`);
            const icon = btn.find('.toggle-icon');

            // If expandable row doesn't exist, create it
            if (detailsRow.length === 0) {
                const productData = window.inventoryDataMap && window.inventoryDataMap[productId];
                if (productData) {
                    detailsRow = $('<tr>')
                        .addClass('inventory-details-row')
                        .attr('data-product-id', productId)
                        .html(buildInventoryExpandableRowContent(productData));
                    row.after(detailsRow);
                } else {
                    // console.error('Product data not found for product:', productId);
                    return;
                }
            }

            if (detailsRow.hasClass('show')) {
                detailsRow.removeClass('show');
                btn.removeClass('minus');
                icon.text('+');
            } else {
                detailsRow.addClass('show');
                btn.addClass('minus');
                icon.text('−');
            }
        }

        // Function to add expandable rows - must be global
        window.addInventoryExpandableRows = function(dt) {
            if (!dt) return;

            const currentWidth = $(window).width();
            const isMobileOrTablet = currentWidth <= 1024;

            if (!isMobileOrTablet) {
                // Remove expandable rows on desktop
                $('tr.inventory-details-row').remove();
                return;
            }

            dt.rows().every(function() {
                const row = this.node();
                const toggleBtn = $(row).find('.inventory-toggle-btn-table');
                if (toggleBtn.length > 0) {
                    const productId = toggleBtn.data('product-id');
                    const productData = window.inventoryDataMap && window.inventoryDataMap[productId];
                    if (productData && !$(row).next('tr.inventory-details-row[data-product-id="' + productId +
                            '"]').length) {
                        const expandableRow = $('<tr>')
                            .addClass('inventory-details-row')
                            .attr('data-product-id', productId)
                            .html(buildInventoryExpandableRowContent(productData));
                        $(row).after(expandableRow);
                    }
                }
            });
        };

        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            inventoryTable = null;

            // Listen for changes on startDate and endDate
            $(document).on("change", "#startDate, #endDate", function() {
                let startDate = $("#startDate").val();
                let endDate = $("#endDate").val();
                var subBranchId1 = localStorage.getItem('selectedSubAdminId');

                // Run only if both dates are selected
                if (!startDate || !endDate) {
                    return;
                }

                $.ajax({
                    url: "/api/products/filter",
                    type: "GET",
                    data: {
                        start_date: startDate,
                        end_date: endDate,
                        sub_branch_id: subBranchId1
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        // console.log(response);

                        if (response.inventory && response.inventory.length > 0) {
                            let tableBody = [];

                            // Store product data for expandable rows
                            if (!window.inventoryDataMap) {
                                window.inventoryDataMap = {};
                            }

                            response.inventory.forEach(p => {
                                let productName = `<a href="/product-view/${p.id}" style="color: inherit; text-decoration: none;">${p.product_name || 'N/A'}</a>`;
                                let initialStock = p.initial_stock ?? 'N/A';
                                let currentStock = p.current_stock ?? 'N/A';
                                let rawPrice = p.price;
                                let price = formatInventoryPrice(rawPrice);
                                let remarks = p.remarks ?? 'N/A';

                                // Store product data for expandable row
                                const productData = {
                                    ...p,
                                    sku: p.sku || 'N/A',
                                    price: rawPrice,
                                    initial_stock: initialStock,
                                    current_stock: currentStock,
                                    remarks: remarks
                                };
                                window.inventoryDataMap[p.id] = productData;

                                let actionBtns = `
                            <a class="me-3 btn btn-primary" href="/inventory-View/${p.id}" style="color: white;font-size: 13px;">
                                View History
                            </a>
                            <a class="me-3 edit-product btn btn-primary" data-id="${p.id}" href="javascript:void(0);" style="color: white;font-size: 13px;">
                                Add / Edit Stock
                            </a>
                            <a class="me-3 transfer-stock btn btn-success" data-id="${p.id}" href="javascript:void(0);" style="color: white;font-size: 13px;">
                                Transfer Stock
                            </a>
                        `;

                                let toggleBtn = '';
                                if (p.variants && p.variants.length > 0) {
                                    toggleBtn = `
                                        <button class="inventory-toggle-btn-table flex-shrink-0 me-2" onclick="toggleInventoryRowDetails('${p.id}')" data-product-id="${p.id}" style="width: 24px; height: 24px; line-height: 20px; padding: 0; font-size: 14px; border-radius: 50%; border: 1px solid #ff9f43; background: #ff9f43; color: white; cursor: pointer;">
                                            <span class="toggle-icon">+</span>
                                        </button>
                                    `;
                                }
                                let productNameWithToggle = `<div class=" align-items-center">${toggleBtn}${productName}</div>`;

                                // ✅ Match thead columns exactly (6 columns)
                                tableBody.push([
                                    productNameWithToggle, // 0 Product Name
                                    price, // 1
                                    initialStock, // 2
                                    currentStock, // 3
                                    remarks, // 4
                                    actionBtns // 5
                                ]);

                            });

                            if (inventoryTable) {
                                inventoryTable.clear().rows.add(tableBody).draw();
                                inventoryTable.off('draw').on('draw', function() {
                                    if (window.addInventoryExpandableRows) {
                                        window.addInventoryExpandableRows(
                                            inventoryTable);
                                    }
                                });
                                setTimeout(function() {
                                    if (window.addInventoryExpandableRows) {
                                        window.addInventoryExpandableRows(
                                            inventoryTable);
                                    }
                                }, 100);
                            } else {
                                table.clear().rows.add(tableBody).draw();
                            }
                        } else {
                            table.clear().draw();
                        }
                    },
                    error: function(xhr) {
                        // console.error(xhr.responseText);
                    }
                });
            });


            // Capitalize helper
            function capitalizeWords(str) {
                if (!str || str.trim() === '') return 'N/A';
                return str.replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
            }
            const stockBox = $("#stockQuantityBox");
            const stockLabel = $("#stockQuantityLabel");

            $("#addStock").on("change", function() {
                stockBox.removeClass("d-none");
                stockLabel.text("How much to Add?");
            });

            $("#minusStock").on("change", function() {
                stockBox.removeClass("d-none");
                stockLabel.text("How much to Minus?");
            });

            function fetchinventory() {
                var categoryId = $('#filterCategory').val();
                var brandId = $('#filterBrand').val();
                var subBranchId = localStorage.getItem('selectedSubAdminId');

                $.ajax({
                    url: "/api/inventory-list",
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    data: {
                        category_id: categoryId,
                        brand_id: brandId,
                        sub_branch_id: subBranchId
                    },
                    success: function(response) {


                        if (response.status) {
                            let products = response.inventory || [];
                            let lowStockThreshold = response.lowStockThreshold || 0;
                            let tableBody = [];

                            // Store product data for expandable rows
                            if (!window.inventoryDataMap) {
                                window.inventoryDataMap = {};
                            }

                            $.each(products, function(index, product) {
                                let id = product.id;
                                let formattedProductName = capitalizeWords(product.product_name || 'N/A');
                                let productName = `<a href="/product-view/${id}" style="color: inherit; text-decoration: none;">${formattedProductName}</a>`;
                                let rawPrice = product.price;
                                let price = formatInventoryPrice(rawPrice);
                                let initialStock = product.initial_stock ?? 'N/A';
                                let currentStock = product.current_stock ?? 'N/A';
                                let remarks = product.remarks ?? 'N/A';

                                // Store product data for expandable row
                                const productData = {
                                    ...product,
                                    sku: product.sku || 'N/A',
                                    price: rawPrice,
                                    initial_stock: initialStock,
                                    current_stock: currentStock,
                                    remarks: remarks
                                };
                                window.inventoryDataMap[id] = productData;

                                // Low stock badge
                                let stockDisplay =
                                    `<span class="quantity-history" data-id="${id}" style="cursor:pointer;">${currentStock}</span>`;
                                if (currentStock !== 'N/A' && parseInt(currentStock) <= response
                                    .lowStockThreshold) {
                                    stockDisplay +=
                                        ` <span class="badge bg-danger ms-1">Low Stock</span>`;
                                }

                                // Action buttons
                                let actionBtns = `
        <a class="me-3 btn btn-primary" href="/inventory-View/${id}" style="color: white;font-size: 13px;">View History</a>
        <a class="me-3 edit-product btn btn-primary" data-id="${id}" href="javascript:void(0);" style="color: white;font-size: 13px;">Add / Edit Stock</a>
        <a class="me-3 transfer-stock btn btn-success" data-id="${id}" href="javascript:void(0);" style="color: white;font-size: 13px;">Transfer Stock</a>
                    `;

                                let toggleBtn = '';
                                if (product.variants && product.variants.length > 0) {
                                    toggleBtn = `
                                        <button class="inventory-toggle-btn-table flex-shrink-0 me-2" onclick="toggleInventoryRowDetails('${id}')" data-product-id="${id}" style="width: 24px; height: 24px; line-height: 20px; padding: 0; font-size: 14px; border-radius: 50%; border: 1px solid #ff9f43; background: #ff9f43; color: white; cursor: pointer;">
                                            <span class="toggle-icon">+</span>
                                        </button>
                                    `;
                                }
                                let productNameWithToggle = `<div class=" align-items-center">${toggleBtn}${productName}</div>`;

                                // ✅ Match thead columns exactly (6 columns)
                                tableBody.push([
                                    productNameWithToggle, // Product Name
                                    price,
                                    initialStock,
                                    stockDisplay,
                                    remarks,
                                    actionBtns

                                ]);
                            });

                            // Populate DataTable
                            if (inventoryTable) {
                                inventoryTable.clear().rows.add(tableBody).draw();
                                inventoryTable.off('draw').on('draw', function() {
                                    if (window.addInventoryExpandableRows) {
                                        window.addInventoryExpandableRows(inventoryTable);
                                    }
                                });
                                setTimeout(function() {
                                    if (window.addInventoryExpandableRows) {
                                        window.addInventoryExpandableRows(inventoryTable);
                                    }
                                }, 100);
                            } else {
                                table.clear().rows.add(tableBody).draw();
                            }
                        } else {
                            table.clear().draw();
                            $(".table tbody").html('<tr><td colspan="7">No inventory found</td></tr>');
                        }
                    },
                    error: function() {
                        alert("Error fetching inventory.");
                    }
                });
            }

            // ✅ Initialize DataTable
            inventoryTable = $('#inventory-table').DataTable({
                responsive: false,
                searching: true,
                paging: true,
                info: true,
                autoWidth: false,
                ordering: false,
                 dom: '<"top"f>rt<"bottom"lip><"clear">'
            });

            // Set table reference for date filter
            table = inventoryTable;


            // ✅ Load products on page load
            fetchinventory();

            // ✅ Reload when filters change
            $('#filterCategory, #filterBrand').on('change', function() {
                fetchinventory();
            });
            // ✅ Open Edit Modal
            $(document).on("click", ".edit-product", function() {
                var authToken = localStorage.getItem("authToken");
                let productId = $(this).data("id");

                // alert(productId)
                // Fetch product details from API
                $.ajax({
                    url: "/api/products_edit_inventroy/" + productId,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        if (response.status) {
                            let product = response.product;
                            // console.log(product);

                            // Fill modal fields
                            $("#editProductId").val(product.id);
                            $("#editProductName").val(product.product_name);
                            $("#editProductSku").val(product.sku);
                            $("#editProductPrice").val(product.price);
                            $("#editProductinitialStock").val(product.initial_stock);
                            $("#editProductStock").val(product.current_stock);
                            $("input[name='stock_action']").prop("checked", false);
                            $("#stockQuantity").val("");
                            $("#stockRemarks").val("");
                            $("#stockQuantityBox").addClass("d-none");
                            $("#stockError").hide().text("");

                            // Show modal
                            $("#editProductModal").modal("show");
                        } else {
                            alert("Product not found!");
                        }
                    }
                });
            });

            // ✅ Handle Save Changes
            // $("#editProductForm").on("submit", function(e) {
            //     e.preventDefault();

            //     let productId = $("#editProductId").val();
            //     const sub_branch_id = localStorage.getItem('selectedSubAdminId');
            //     var authToken = localStorage.getItem("authToken");
            //     let stockAction = $("input[name='stock_action']:checked").val(); // 'add' or 'minus'
            //     let stockQuantity = $("#stockQuantity").val(); // the textbox value
            //     let formData = {
            //         name: $("#editProductName").val(),
            //         sku: $("#editProductSku").val(),
            //         price: $("#editProductPrice").val(),
            //         current_stock: $("#editProductStock").val(),
            //         initial_stock: $("#editProductinitialStock").val(),
            //         sub_branch_id: sub_branch_id,
            //         stock_action: stockAction, // <-- add this
            //         stock_quantity: stockQuantity // <-- and this
            //     };

            //     $.ajax({
            //         url: "/api/inventory_update/" + productId,
            //         type: "POST",
            //         headers: {
            //             "Authorization": "Bearer " + authToken
            //         },
            //         data: formData,
            //         success: function(response) {
            //             if (response.status) {
            //                 $("#editProductModal").modal("hide");
            //                 fetchinventory(); // refresh table
            //             } else {
            //                 alert("Update failed!");
            //             }
            //         }
            //     });
            // });
            $("#editProductForm").on("submit", function(e) {
                e.preventDefault();
                const $submitBtn = $(this).find('button[type="submit"]');
                if ($submitBtn.prop('disabled')) {
                    return;
                }
                if (!$submitBtn.data('default-html')) {
                    $submitBtn.data('default-html', $submitBtn.html());
                }
                function toggleEditProductSubmitLoading(isLoading) {
                    if (isLoading) {
                        $submitBtn
                            .prop('disabled', true)
                            .html(
                                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...'
                            );
                    } else {
                        $submitBtn
                            .prop('disabled', false)
                            .html($submitBtn.data('default-html'));
                    }
                }

                let productId = $("#editProductId").val();
                let stockAction = $("input[name='stock_action']:checked").val();
                let stockQuantity = $("#stockQuantity").val();

                $("#stockError").hide().text("");

                // Validate radio
                if (!stockAction) {
                    $("#stockError").text("Please select Add Quantity or Minus Quantity").show();
                    return;
                }

                // Validate quantity
                if (!stockQuantity || stockQuantity <= 0) {
                    $("#stockError").text("Please enter quantity").show();
                    return;
                }

                const sub_branch_id = localStorage.getItem('selectedSubAdminId');
                var authToken = localStorage.getItem("authToken");

                let formData = {
                    name: $("#editProductName").val(),
                    sku: $("#editProductSku").val(),
                    price: $("#editProductPrice").val(),
                    current_stock: $("#editProductStock").val(),
                    initial_stock: $("#editProductinitialStock").val(),
                    sub_branch_id: sub_branch_id,
                    stock_action: stockAction,
                    stock_quantity: stockQuantity,
                    remarks: $("#stockRemarks").val().trim()
                };

                $.ajax({
                    url: "/api/inventory_update/" + productId,
                    type: "POST",
                    beforeSend: function() {
                        toggleEditProductSubmitLoading(true);
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    data: formData,
                    success: function(response) {

                        if (response.status) {

                            $("#editProductModal").modal("hide");

                            Swal.fire({
                                icon: "success",
                                title: "Success",
                                text: "Quantity successfully updated",
                                confirmButtonColor: "#ff9f43"
                            }).then(() => {
                                location.reload();
                            });

                        } else {

                            $("#stockError").text("Update failed").show();

                        }
                    },
                    complete: function() {
                        toggleEditProductSubmitLoading(false);
                    }
                });

            });

            // Resize handler for responsive behavior
            let resizeTimer;
            let lastWidth = $(window).width();

            function forceInventoryCSSRecalculation() {
                const temp = document.createElement('div');
                temp.style.width = '1px';
                temp.style.height = '1px';
                temp.style.position = 'absolute';
                temp.style.visibility = 'hidden';
                document.body.appendChild(temp);
                void temp.offsetWidth;
                void temp.offsetHeight;
                document.body.removeChild(temp);

                void window.innerWidth;
                void window.innerHeight;
                void document.documentElement.offsetWidth;
                void document.documentElement.offsetHeight;
            }

            function handleInventoryResize() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const currentWidth = $(window).width();
                    lastWidth = currentWidth;

                    // Force CSS recalculation
                    forceInventoryCSSRecalculation();

                    const table = document.getElementById('inventory-table');
                    const tableResponsive = document.querySelector('.table-responsive');

                    [table, tableResponsive].forEach(function(el) {
                        if (el) {
                            void el.offsetHeight;
                            void el.offsetWidth;
                            el.style.display = 'none';
                            void el.offsetHeight;
                            el.style.display = '';
                        }
                    });

                    if (inventoryTable && table) {
                        $('tr.inventory-details-row').remove();

                        try {
                            inventoryTable.columns.adjust();
                            inventoryTable.draw(false);

                            setTimeout(function() {
                                inventoryTable.columns.adjust();
                                inventoryTable.draw(false);

                                setTimeout(function() {
                                    if (window.addInventoryExpandableRows) {
                                        window.addInventoryExpandableRows(inventoryTable);
                                    }
                                    forceInventoryCSSRecalculation();
                                    void table.offsetHeight;
                                }, 100);
                            }, 100);
                        } catch (e) {
                            // console.error('DataTables adjustment error:', e);
                            inventoryTable.draw(false);
                            setTimeout(function() {
                                if (window.addInventoryExpandableRows) {
                                    window.addInventoryExpandableRows(inventoryTable);
                                }
                                forceInventoryCSSRecalculation();
                            }, 150);
                        }
                    } else {
                        forceInventoryCSSRecalculation();
                    }
                }, 50);
            }

            // Window resize handler
            $(window).off('resize.inventory').on('resize.inventory', handleInventoryResize);

            if (window.inventoryResizeHandler) {
                window.removeEventListener('resize', window.inventoryResizeHandler);
            }
            window.inventoryResizeHandler = handleInventoryResize;
            window.addEventListener('resize', window.inventoryResizeHandler, {
                passive: true
            });

            // Orientation change handler
            $(window).off('orientationchange.inventory').on('orientationchange.inventory', function() {
                setTimeout(function() {
                    lastWidth = $(window).width();
                    handleInventoryResize();
                }, 300);
            });

            window.addEventListener('orientationchange', function() {
                setTimeout(function() {
                    lastWidth = $(window).width();
                    handleInventoryResize();
                }, 500);
            });

            // MatchMedia listeners for breakpoint changes
            const queries = [
                window.matchMedia('(max-width: 575.98px)'),
                window.matchMedia('(min-width: 576px) and (max-width: 767.98px)'),
                window.matchMedia('(min-width: 768px) and (max-width: 1024px)'),
                window.matchMedia('(min-width: 1025px)')
            ];

            queries.forEach(function(query) {
                if (query.addEventListener) {
                    query.addEventListener('change', function() {
                        setTimeout(handleInventoryResize, 100);
                    });
                } else if (query.addListener) {
                    query.addListener(function() {
                        setTimeout(handleInventoryResize, 100);
                    });
                }
            });

            // Initial width set and call
            lastWidth = $(window).width();

            $(window).on('load', function() {
                setTimeout(function() {
                    lastWidth = $(window).width();
                    handleInventoryResize();
                }, 500);
            });

            setTimeout(function() {
                if (inventoryTable) {
                    handleInventoryResize();
                }
            }, 1000);

            window.handleInventoryResize = handleInventoryResize;

        });
    </script> --}}

    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            var selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

            // Pagination state
            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';
            let currentStartDate = '';
            let currentEndDate = '';

            // Helper functions
            function capitalizeWords(str) {
                if (!str || str.trim() === '') return 'N/A';
                return str.replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
            }

            function formatInventoryPrice(value) {
                if (value === null || value === undefined || value === '' || value === 'N/A') return 'N/A';
                const numericValue = Number(value);
                if (Number.isNaN(numericValue)) return value;
                return numericValue.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            const INVENTORY_MODULE_ID = 49;
            const materialCardOptions = {
                moduleId: INVENTORY_MODULE_ID,
                historyUrl: '/material-inventory-View/',
                transfer: false,
                emptyText: 'No materials found'
            };

            function buildInventoryActionButtons(productId) {
                let html = '';

                if (hasStaffPermission(INVENTORY_MODULE_ID, 'view')) {
                    html += `<a class="btn btn-sm btn-primary inventory-act-btn" href="/material-inventory-View/${productId}">View History</a>`;
                }

                if (hasStaffPermission(INVENTORY_MODULE_ID, 'add') || hasStaffPermission(INVENTORY_MODULE_ID, 'edit')) {
                    html += `<a class="btn btn-sm btn-primary inventory-act-btn edit-product" data-id="${productId}" href="javascript:void(0);">Add / Edit Stock</a>`;
                }

                if (!html) return '<span class="text-muted">-</span>';
                return `<div class="inventory-actions-wrap">${html}</div>`;
            }

            // Initialize DataTable (display only, no search/paging)
            let inventoryTable = $('#inventory-table').DataTable({
                responsive: true,
                searching: false,
                paging: false,
                info: false,
                lengthChange: false,
                ordering: false,
                autoWidth: false,
                responsive: false,
                dom: 't',
                columnDefs: [{
                    targets: [1, 2, 3, 4, 5],
                    className: 'inventory-column'
                }]
            });

            // Store product data for expandable rows
            window.inventoryDataMap = {};

            // Function to build expandable row content (variant sub-rows)
            function buildInventoryExpandableRowContent(product) {
                if (product.variants && product.variants.length > 0) {
                    let rowsHtml = product.variants.map(v => {
                        let sizeColor = `${v.size || ''} ${v.color || ''}`.trim() || 'Default';
                        let vPrice = formatInventoryPrice(v.price || product.price);
                        let vStock = v.stock ?? 0;
                        let actionBtns = '';

                        if (hasStaffPermission(INVENTORY_MODULE_ID, 'view')) {
                            actionBtns += `<a class="btn btn-sm btn-primary inventory-act-btn" href="/material-inventory-View/${product.id}?variant_id=${v.variant_id}">View History</a>`;
                        }
                        if (hasStaffPermission(INVENTORY_MODULE_ID, 'add') || hasStaffPermission(INVENTORY_MODULE_ID, 'edit')) {
                            actionBtns += `<a class="btn btn-sm btn-primary inventory-act-btn edit-product" data-id="${product.id}" data-variant-id="${v.variant_id}" href="javascript:void(0);">Add / Edit Stock</a>`;
                        }
                        actionBtns = `<div class="inventory-actions-wrap">${actionBtns}</div>`;

                        let vInitialStock = v.initial_stock !== undefined && v.initial_stock !== null ? v.initial_stock : 'N/A';
                        let vCurrentStock = v.current_stock !== undefined && v.current_stock !== null ? v.current_stock : (v.stock ?? 0);
                        let vRemarks = v.remarks || 'N/A';

                        return `
                            <tr class="variant-data-row" style="background-color: #f9fbfd;">
                                <td style="padding-left: 30px; font-weight: 500;">
                                    <i class="fa fa-angle-right me-2 text-muted"></i>Variant: ${sizeColor}
                                </td>
                                <td>${vPrice}</td>
                                <td>${vInitialStock}</td>
                                <td><strong style="color: #ff9f43;">${vCurrentStock}</strong></td>
                                <td>${vRemarks}</td>
                                <td>${actionBtns}</td>
                            </tr>
                        `;
                    }).join('');

                    return `
                        <td colspan="6" class="p-0">
                            <div class="table-responsive bg-light p-2 border-start border-3 border-warning variant-container">
                                <table class="table table-sm table-bordered mb-0 variant-table" style="background: white;">
                                    <thead class="table-dark" style="font-size: 12px; color: #ffffff;">
                                        <tr style="color: #ffffff;">
                                            <th style="padding-left: 30px; color: #ffffff;">Size / Color</th>
                                            <th style="color: #ffffff;">Price</th>
                                            <th style="color: #ffffff;">Initial Stock</th>
                                            <th style="color: #ffffff;">Current Stock</th>
                                            <th style="color: #ffffff;">Remarks</th>
                                            <th style="color: #ffffff;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 13px;">
                                        ${rowsHtml}
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    `;
                }

                const actionBtns = buildInventoryActionButtons(product.id);
                return `
                <td colspan="6" class="inventory-details-content">
                    <div class="inventory-details-list">
                        <div class="inventory-detail-row-simple">
                            <span class="inventory-detail-label-simple">SKU:</span>
                            <span class="inventory-detail-value-simple">${product.sku || 'N/A'}</span>
                        </div>
                        <div class="inventory-detail-row-simple">
                            <span class="inventory-detail-label-simple">Price:</span>
                            <span class="inventory-detail-value-simple">${formatInventoryPrice(product.price)}</span>
                        </div>
                        <div class="inventory-detail-row-simple">
                            <span class="inventory-detail-label-simple">Initial Stock:</span>
                            <span class="inventory-detail-value-simple">${product.initial_stock ?? 'N/A'}</span>
                        </div>
                        <div class="inventory-detail-row-simple">
                            <span class="inventory-detail-label-simple">Current Stock:</span>
                            <span class="inventory-detail-value-simple">${product.current_stock ?? 'N/A'}</span>
                        </div>
                        <div class="inventory-detail-row-simple">
                            <span class="inventory-detail-label-simple">Remarks:</span>
                            <span class="inventory-detail-value-simple">${product.remarks || 'N/A'}</span>
                        </div>
                    </div>
                    <div class="inventory-action-buttons-simple">
                        ${actionBtns}
                    </div>
                </td>
            `;
            }

            // Toggle function for expandable rows
            window.toggleInventoryRowDetails = function(productId) {
                const btn = $(`.inventory-toggle-btn-table[data-product-id="${productId}"]`);
                if (btn.length === 0) return;
                const row = btn.closest('tr');
                let detailsRow = row.next(`tr.inventory-details-row[data-product-id="${productId}"]`);
                const icon = btn.find('.toggle-icon');
                if (detailsRow.length === 0) {
                    const productData = window.inventoryDataMap[productId];
                    if (productData) {
                        detailsRow = $('<tr>')
                            .addClass('inventory-details-row')
                            .attr('data-product-id', productId)
                            .html(buildInventoryExpandableRowContent(productData));
                        row.after(detailsRow);
                    } else {
                        return;
                    }
                }
                if (detailsRow.hasClass('show')) {
                    detailsRow.removeClass('show');
                    btn.removeClass('minus');
                    icon.text('+');
                } else {
                    detailsRow.addClass('show');
                    btn.addClass('minus');
                    icon.text('−');
                }
            };

            // Add expandable rows after table draw (for mobile/tablet)
            function addInventoryExpandableRows() {
                const currentWidth = $(window).width();
                const isMobileOrTablet = currentWidth <= 1024;
                if (!isMobileOrTablet) {
                    $('tr.inventory-details-row').remove();
                    return;
                }
                inventoryTable.rows().every(function() {
                    const row = this.node();
                    const toggleBtn = $(row).find('.inventory-toggle-btn-table');
                    if (toggleBtn.length > 0) {
                        const productId = toggleBtn.data('product-id');
                        const productData = window.inventoryDataMap[productId];
                        if (productData && !$(row).next('tr.inventory-details-row[data-product-id="' +
                                productId + '"]').length) {
                            const expandableRow = $('<tr>')
                                .addClass('inventory-details-row')
                                .attr('data-product-id', productId)
                                .html(buildInventoryExpandableRowContent(productData));
                            $(row).after(expandableRow);
                        }
                    }
                });
            }

            // Fetch inventory with pagination
            function fetchInventory(page = 1) {
                let url = `/api/material-inventory-list?page=${page}&per_page=${perPage}`;
                if (selectedSubAdminId) url += `&sub_branch_id=${selectedSubAdminId}`;
                if (searchQuery) url += `&search=${encodeURIComponent(searchQuery)}`;
                if (currentStartDate) url += `&start_date=${currentStartDate}`;
                if (currentEndDate) url += `&end_date=${currentEndDate}`;
                // Include category and brand filters if present (they exist in the original blade but might not be used)
                let categoryId = $('#filterCategory').val();
                let brandId = $('#filterBrand').val();
                if (categoryId) url += `&category_id=${categoryId}`;
                if (brandId) url += `&brand_id=${brandId}`;

                $.ajax({
                    url: url,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        if (response.status) {
                            let products = response.inventory || [];
                            let pagination = response.pagination;

                            // Update pagination state
                            currentPage = pagination.current_page;
                            lastPage = pagination.last_page;
                            updatePaginationUI(pagination);

                            // Build table rows
                            let tableBody = [];
                            products.forEach((product, index) => {
                                let id = product.id;
                                let formattedProductName = capitalizeWords(product.product_name || 'N/A');
                                let productName = `<a href="/product-view/${id}" style="color: inherit; text-decoration: none;">${formattedProductName}</a>`;
                                let rawPrice = product.price;
                                let price = formatInventoryPrice(rawPrice);
                                let initialStock = product.initial_stock ?? 'N/A';
                                let currentStock = product.current_stock ?? 'N/A';
                                let remarks = product.remarks ?? 'N/A';

                                // Store product data for expandable rows
                                window.inventoryDataMap[id] = {
                                    ...product,
                                    sku: product.sku || 'N/A',
                                    price: rawPrice,
                                    initial_stock: initialStock,
                                    current_stock: currentStock,
                                    remarks: remarks
                                };

                                let stockDisplay =
                                    `<span class="quantity-history" data-id="${id}" style="cursor:pointer;">${currentStock}</span>`;
                                if (currentStock !== 'N/A' && parseInt(currentStock) <= (
                                        response.lowStockThreshold || 0)) {
                                    stockDisplay +=
                                        ` <span class="badge bg-danger ms-1">Low Stock</span>`;
                                }

                                const actionBtns = buildInventoryActionButtons(id);

                                let toggleBtn = '';
                                if (product.variants && product.variants.length > 0) {
                                    toggleBtn = `
                                        <button class="inventory-toggle-btn-table flex-shrink-0 me-2" onclick="toggleInventoryRowDetails('${id}')" data-product-id="${id}" style="width: 24px; height: 24px; line-height: 20px; padding: 0; font-size: 14px; border-radius: 50%; border: 1px solid #ff9f43; background: #ff9f43; color: white; cursor: pointer;">
                                            <span class="toggle-icon">+</span>
                                        </button>
                                    `;
                                }
                                let productNameWithToggle = `<div class=" align-items-center">${toggleBtn}${productName}</div>`;

                                // ✅ Match thead columns exactly (6 columns)
                                tableBody.push([
                                    productNameWithToggle, // Product Name
                                    price,
                                    initialStock,
                                    stockDisplay,
                                    remarks,
                                    actionBtns
                                ]);
                            });

                            inventoryTable.clear().rows.add(tableBody).draw();
                            InventoryMobileUI.renderCards(products, response.lowStockThreshold, materialCardOptions);
                            // After draw, add expandable rows if needed
                            setTimeout(() => addInventoryExpandableRows(), 100);
                            $('.pagination-controls').show();
                        } else {
                            inventoryTable.clear().draw();
                            InventoryMobileUI.renderCards([], 0, materialCardOptions);
                            $('.pagination-controls').hide();
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        inventoryTable.clear().draw();
                        InventoryMobileUI.renderCards([], 0, materialCardOptions);
                        $('.pagination-controls').hide();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load inventory data',
                            confirmButtonColor: '#ff9f43'
                        });
                    }
                });
            }

            // Update pagination UI
            function updatePaginationUI(pagination) {
                let from = (pagination.current_page - 1) * pagination.per_page ;
                let to = pagination.current_page * pagination.per_page;
                if (to > pagination.total) to = pagination.total;
                if (pagination.total === 0) from = 0;

                $('#pagination-from').text(from);
                $('#pagination-to').text(to);
                $('#pagination-total').text(pagination.total);

                let paginationHtml = '';

                // Previous button
                paginationHtml += `
                    <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page - 1}">Previous</a>
                    </li>
                `;

                // Show only 3 page numbers at a time
                const visiblePageCount = 2;
                let startPage = Math.floor((pagination.current_page - 1) / visiblePageCount) * visiblePageCount + 1;
                let endPage = Math.min(pagination.last_page, startPage + visiblePageCount - 1);

                // Show previous ellipsis if there are pages before startPage
                if (startPage > 1) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);" data-page="${startPage - 1}" data-action="prev-group">..</a>
                        </li>
                    `;
                }

                // Generate page numbers
                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                        </li>
                    `;
                }

                // Show next ellipsis if there are more pages after endPage
                if (endPage < pagination.last_page) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);" data-page="${endPage + 1}" data-action="next-group">..</a>
                        </li>
                    `;
                }

                // Next button
                paginationHtml += `
                    <li class="page-item ${pagination.current_page === pagination.last_page || pagination.last_page === 0 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page + 1}">Next</a>
                    </li>
                `;

                $('#pagination-numbers').html(paginationHtml);
                $('.pagination-controls').show();
            }

            // Event handlers
            $('#inventory-search-input').on('keyup', function() {
                searchQuery = $(this).val();
                fetchInventory(1);
            });

            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchInventory(1);
            });

            // Handle page number clicks with ellipsis support
            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                let action = $(this).data('action');

                // Handle ellipsis clicks to load next/previous groups
                if (action === 'next-group') {
                    // Load the page that starts the next group
                    if (page && page <= lastPage) {
                        fetchInventory(page);
                    }
                    return;
                }

                if (action === 'prev-group') {
                    // Load the previous group's starting page
                    let prevStartPage = Math.max(1, page - 2);
                    if (prevStartPage >= 1 && prevStartPage <= lastPage) {
                        fetchInventory(prevStartPage);
                    }
                    return;
                }

                // Regular page navigation
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchInventory(page);
                }
            });

            // Date filters
            $('#startDate, #endDate').on('change', function() {
                currentStartDate = $('#startDate').val();
                currentEndDate = $('#endDate').val();
                fetchInventory(1);
            });

            // Category and brand filters (if they exist)
            $('#filterCategory, #filterBrand').on('change', function() {
                fetchInventory(1);
            });

            // Initial load
            fetchInventory(1);

            // ✅ Open Edit Modal
            $('#editProductModal').on('show.bs.modal', function() {
                $('input[name="stock_action"]').prop('checked', false);
                $('#stockQuantityBox').addClass('d-none');
                $('#stockQuantity').val('');
                $('#stockRemarks').val('');
                $('#stockError').hide().text('');
            });

            // Show/hide quantity box based on radio selection
            $("#addStock, #minusStock").on("change", function() {
                const stockBox = $("#stockQuantityBox");
                const stockLabel = $("#stockQuantityLabel");
                if ($(this).val() === "add") {
                    stockBox.removeClass("d-none");
                    stockLabel.text("How much to Add?");
                } else if ($(this).val() === "minus") {
                    stockBox.removeClass("d-none");
                    stockLabel.text("How much to Minus?");
                }
            });

            // Open Edit Modal (handles both product level and variant level)
            $(document).on("click", ".edit-product", function() {
                var authToken = localStorage.getItem("authToken");
                let productId = $(this).data("id");
                let variantId = $(this).data("variant-id");

                let reqUrl = "/api/material_edit_inventory/" + productId;
                if (variantId) {
                    reqUrl += "?variant_id=" + variantId;
                }

                $.ajax({
                    url: reqUrl,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        if (response.status) {
                            let product = response.product;
                            $("#editProductId").val(product.id);
                            $("#editProductVariantId").val(product.product_variant_id || variantId || "");
                            $("#editProductName").val(product.product_name);
                            $("#editProductSku").val(product.sku);
                            $("#editProductPrice").val(product.price);
                            $("#editProductinitialStock").val(product.initial_stock);
                            $("#editProductStock").val(product.current_stock);
                            $("#stockRemarks").val("");
                            $("#editProductModal").modal("show");
                        } else {
                            alert("Product not found!");
                        }
                    }
                });
            });

            // Save Changes
            $("#editProductForm").on("submit", function(e) {
                e.preventDefault();
                const $submitBtn = $(this).find('button[type="submit"]');
                if ($submitBtn.prop('disabled')) {
                    return;
                }
                if (!$submitBtn.data('default-html')) {
                    $submitBtn.data('default-html', $submitBtn.html());
                }
                function toggleEditProductSubmitLoading(isLoading) {
                    if (isLoading) {
                        $submitBtn
                            .prop('disabled', true)
                            .html(
                                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...'
                            );
                    } else {
                        $submitBtn
                            .prop('disabled', false)
                            .html($submitBtn.data('default-html'));
                    }
                }

                let productId = $("#editProductId").val();
                let variantId = $("#editProductVariantId").val();
                let stockAction = $("input[name='stock_action']:checked").val();
                let stockQuantity = $("#stockQuantity").val();

                $("#stockError").hide().text("");
                if (!stockAction) {
                    $("#stockError").text("Please select Add Quantity or Minus Quantity").show();
                    return;
                }
                if (!stockQuantity || stockQuantity <= 0) {
                    $("#stockError").text("Please enter quantity").show();
                    return;
                }

                const sub_branch_id = localStorage.getItem('selectedSubAdminId');
                var authToken = localStorage.getItem("authToken");
                let formData = {
                    name: $("#editProductName").val(),
                    sku: $("#editProductSku").val(),
                    price: $("#editProductPrice").val(),
                    current_stock: $("#editProductStock").val(),
                    initial_stock: $("#editProductinitialStock").val(),
                    sub_branch_id: sub_branch_id,
                    stock_action: stockAction,
                    stock_quantity: stockQuantity,
                    remarks: $("#stockRemarks").val().trim()
                };

                if (variantId) {
                    formData.product_variant_id = variantId;
                }

                $.ajax({
                    url: "/api/material_inventory_update/" + productId,
                    type: "POST",
                    beforeSend: function() {
                        toggleEditProductSubmitLoading(true);
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    data: formData,
                    success: function(response) {
                        if (response.status) {
                            $("#editProductModal").modal("hide");
                            Swal.fire({
                                icon: "success",
                                title: "Success",
                                text: "Quantity successfully updated",
                                confirmButtonColor: "#ff9f43"
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            $("#stockError").text("Update failed").show();
                        }
                    },
                    complete: function() {
                        toggleEditProductSubmitLoading(false);
                    }
                });
            });
            // Resize handler for responsive behavior
            let resizeTimer;
            let lastWidth = $(window).width();

            function forceInventoryCSSRecalculation() {
                const temp = document.createElement('div');
                temp.style.width = '1px';
                temp.style.height = '1px';
                temp.style.position = 'absolute';
                temp.style.visibility = 'hidden';
                document.body.appendChild(temp);
                void temp.offsetWidth;
                void temp.offsetHeight;
                document.body.removeChild(temp);

                void window.innerWidth;
                void window.innerHeight;
                void document.documentElement.offsetWidth;
                void document.documentElement.offsetHeight;
            }

            function handleInventoryResize() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const currentWidth = $(window).width();
                    lastWidth = currentWidth;

                    // Force CSS recalculation
                    forceInventoryCSSRecalculation();

                    const table = document.getElementById('inventory-table');
                    const tableResponsive = document.querySelector('.table-responsive');

                    [table, tableResponsive].forEach(function(el) {
                        if (el) {
                            void el.offsetHeight;
                            void el.offsetWidth;
                            el.style.display = 'none';
                            void el.offsetHeight;
                            el.style.display = '';
                        }
                    });

                    if (inventoryTable && table) {
                        $('tr.inventory-details-row').remove();

                        try {
                            inventoryTable.columns.adjust();
                            inventoryTable.draw(false);

                            setTimeout(function() {
                                inventoryTable.columns.adjust();
                                inventoryTable.draw(false);

                                setTimeout(function() {
                                    if (window.addInventoryExpandableRows) {
                                        window.addInventoryExpandableRows(inventoryTable);
                                    }
                                    forceInventoryCSSRecalculation();
                                    void table.offsetHeight;
                                }, 100);
                            }, 100);
                        } catch (e) {
                            // console.error('DataTables adjustment error:', e);
                            inventoryTable.draw(false);
                            setTimeout(function() {
                                if (window.addInventoryExpandableRows) {
                                    window.addInventoryExpandableRows(inventoryTable);
                                }
                                forceInventoryCSSRecalculation();
                            }, 150);
                        }
                    } else {
                        forceInventoryCSSRecalculation();
                    }
                }, 50);
            }

            // Window resize handler
            $(window).off('resize.inventory').on('resize.inventory', handleInventoryResize);

            if (window.inventoryResizeHandler) {
                window.removeEventListener('resize', window.inventoryResizeHandler);
            }
            window.inventoryResizeHandler = handleInventoryResize;
            window.addEventListener('resize', window.inventoryResizeHandler, {
                passive: true
            });

            // Orientation change handler
            $(window).off('orientationchange.inventory').on('orientationchange.inventory', function() {
                setTimeout(function() {
                    lastWidth = $(window).width();
                    handleInventoryResize();
                }, 300);
            });

            window.addEventListener('orientationchange', function() {
                setTimeout(function() {
                    lastWidth = $(window).width();
                    handleInventoryResize();
                }, 500);
            });

            // MatchMedia listeners for breakpoint changes
            const queries = [
                window.matchMedia('(max-width: 575.98px)'),
                window.matchMedia('(min-width: 576px) and (max-width: 767.98px)'),
                window.matchMedia('(min-width: 768px) and (max-width: 1024px)'),
                window.matchMedia('(min-width: 1025px)')
            ];

            queries.forEach(function(query) {
                if (query.addEventListener) {
                    query.addEventListener('change', function() {
                        setTimeout(handleInventoryResize, 100);
                    });
                } else if (query.addListener) {
                    query.addListener(function() {
                        setTimeout(handleInventoryResize, 100);
                    });
                }
            });

            // Initial width set and call
            lastWidth = $(window).width();

            $(window).on('load', function() {
                setTimeout(function() {
                    lastWidth = $(window).width();
                    handleInventoryResize();
                }, 500);
            });

            // ✅ Open Transfer Modal
            $(document).on("click", ".transfer-stock", function() {
                var authToken = localStorage.getItem("authToken");
                let productId = $(this).data("id");
                
                const product = window.inventoryDataMap && window.inventoryDataMap[productId];
                if (product) {
                    $("#transferProductId").val(product.id);
                    $("#transferProductName").val(product.product_name);
                    $("#transferAvailableStock").val(product.current_stock);
                    $("#transferFromBranchId").val(product.branch_id);
                    
                    const branchesList = @json($branches);
                    const fromBranch = branchesList.find(b => b.id == product.branch_id);
                    $("#transferFromBranchName").val(fromBranch ? fromBranch.name : 'Unknown Branch');
                    
                    // Filter To Branch dropdown: exclude From Branch
                    $("#transferToBranch option").show();
                    if (product.branch_id) {
                        $(`#transferToBranch option[value="${product.branch_id}"]`).hide();
                    }
                    
                    $("#transferToBranch").val("");
                    $("#transferQuantity").val("");
                    $("#transferRemarks").val("");
                    $("#transferError").hide().text("");
                    
                    $("#transferStockModal").modal("show");
                } else {
                    alert("Product data not found!");
                }
            });

            // ✅ Handle Save Transfer
            $("#transferStockForm").on("submit", function(e) {
                e.preventDefault();
                const $submitBtn = $(this).find('button[type="submit"]');
                if ($submitBtn.prop('disabled')) {
                    return;
                }
                
                let productId = $("#transferProductId").val();
                let toBranchId = $("#transferToBranch").val();
                let quantity = parseFloat($("#transferQuantity").val());
                let availableStock = parseFloat($("#transferAvailableStock").val());
                
                $("#transferError").hide().text("");
                
                if (!toBranchId) {
                    $("#transferError").text("Please select a target branch").show();
                    return;
                }
                
                if (isNaN(quantity) || quantity <= 0) {
                    $("#transferError").text("Transfer quantity must be greater than 0").show();
                    return;
                }
                
                if (quantity > availableStock) {
                    $("#transferError").text("Transfer quantity cannot exceed available stock").show();
                    return;
                }
                
                if (!$submitBtn.data('default-html')) {
                    $submitBtn.data('default-html', $submitBtn.html());
                }
                
                $submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Transferring...'
                );
                
                var authToken = localStorage.getItem("authToken");
                let formData = {
                    to_branch_id: toBranchId,
                    quantity: quantity,
                    remarks: $("#transferRemarks").val().trim()
                };
                
                $.ajax({
                    url: "/api/inventory_transfer/" + productId,
                    type: "POST",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    data: formData,
                    success: function(response) {
                        $submitBtn.prop('disabled', false).html($submitBtn.data('default-html'));
                        if (response.status) {
                            $("#transferStockModal").modal("hide");
                            Swal.fire({
                                icon: "success",
                                title: "Success!",
                                text: response.message || "Stock transferred successfully",
                                confirmButtonColor: "#ff9f43"
                            }).then(() => {
                                fetchinventory(); // refresh table
                            });
                        } else {
                            $("#transferError").text(response.message || "Transfer failed!").show();
                        }
                    },
                    error: function(xhr) {
                        $submitBtn.prop('disabled', false).html($submitBtn.data('default-html'));
                        let errMsg = "An error occurred during transfer.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errs = Object.values(xhr.responseJSON.errors).flat();
                            if (errs.length > 0) errMsg = errs[0];
                        }
                        $("#transferError").text(errMsg).show();
                    }
                });
            });

            window.handleInventoryResize = handleInventoryResize;

        });
    </script>
@endpush

