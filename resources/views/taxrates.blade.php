@extends('layout.app')

@section('title', 'Tax Rates')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />
    <style>
        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }

        .dataTables_wrapper .row:first-child {
            display: none !important;
        }

        .dataTables_wrapper {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        .search-input {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-input input {
            padding-left: 35px !important;
            border-radius: 5px;
        }

        .btn-searchset {
            position: absolute;
            left: 10px;
            z-index: 10;
            padding: 0;
            top: 7px !important;
        }

        #tax-rates-table {
            width: 100% !important;
            table-layout: auto;
        }

        /* ---------- Pagination ---------- */
        .pagination .page-item .page-link {
            background-color: #5d6d7e;
            color: #fff;
            border: none;
            margin: 0 3px;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: bold;
        }

        @media (max-width: 576px) {
            .pagination .page-item .page-link {
                padding: 4px 10px;
                margin: 0 3px;
                font-size: 12px;
            }
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

        .pagination .page-item.disabled .page-link {
            background-color: #fff !important;
            color: #dee2e6 !important;
            border: 1px solid #dee2e6 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }

        /* ---------- Action dropdown (base / desktop) ---------- */
        .tx-action-dropdown { display: none; }

        .tx-action-dropdown .action-menu-toggle {
            align-items: center;
            background: #fff;
            border: 1px solid #d7dde8;
            border-radius: 4px;
            color: #1b2850;
            display: inline-flex;
            height: 28px;
            width: 34px;
            justify-content: center;
            padding: 0;
        }

        .tx-action-menu {
            border: 1px solid #e8ebed;
            border-radius: 6px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .12);
            min-width: 150px;
            padding: 6px;
        }

        .tx-action-menu-floating { position: fixed !important; z-index: 999999 !important; margin: 0 !important; }

        .tx-action-menu .dropdown-item {
            align-items: center;
            display: flex;
            font-size: 12px;
            gap: 8px;
            padding: 7px 8px;
            border-radius: 4px;
            color: #344054;
        }

        .tx-action-menu .dropdown-item i { width: 15px; text-align: center; color: #667085; }
        .tx-action-menu .dropdown-item.text-danger,
        .tx-action-menu .dropdown-item.text-danger i { color: #ea5455 !important; }
        .tx-action-menu .dropdown-item:hover { background: #f4f6f8; }

        /* ---------- New Tax UI (mobile + tablet) ---------- */
        .tx-quick-links,
        .tx-fab-new,
        .tx-card { display: none; }

        @media (max-width: 1024.98px),
            (width: 1032px) and (height: 1376px),
            (width: 1280px) and (height: 800px) {

            /* Quick header */
            .tx-quick-links {
                display: block;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
                padding: 12px 10px;
                margin-bottom: 10px;
            }

            .tx-quick-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
            }

            .tx-quick-title { font-size: 15px; font-weight: 600; color: #1b2850; margin-left: 4px; }
            .tx-quick-head .btn { height: 30px; padding: 0 10px !important; font-size: 12px !important; font-weight: 600; }

            /* default page-header hide */
            .content:has(.tx-quick-links) > .page-header { display: none !important; }
            .content:has(.tx-quick-links) { padding-left: 6px !important; padding-right: 6px !important; }

            /* card wrapper transparent */
            .content:has(.tx-quick-links) > .card {
                background: transparent !important;
                border: 0 !important;
                box-shadow: none !important;
                margin: 0 !important;
            }

            .content:has(.tx-quick-links) > .card > .card-body { padding: 0 !important; }

            /* search box white card */
            .content:has(.tx-quick-links) .card-body > .mb-2 {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
                padding: 8px 10px;
                margin-bottom: 8px !important;
            }

            .content:has(.tx-quick-links) .card-body > .mb-2 .search-set,
            .content:has(.tx-quick-links) .card-body > .mb-2 .search-input {
                width: 100% !important;
                margin: 0 !important;
            }

            .content:has(.tx-quick-links) .card-body > .mb-2 #tax-search-input {
                width: 100% !important;
                height: 38px !important;
                font-size: 13px;
                border-radius: 8px;
            }

            .content:has(.tx-quick-links) .card-body > .mb-2 .btn-searchset { top: 9px !important; }

            /* Table -> Cards */
            .table-responsive { overflow: visible !important; border: 0 !important; padding: 0 !important; }

            table#tax-rates-table,
            table#tax-rates-table tbody {
                display: block !important;
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                border: 0 !important;
            }

            table#tax-rates-table thead { display: none !important; }

            table#tax-rates-table tbody tr {
                display: block !important;
                position: relative;
                background: #fff;
                border: 1px solid #eef0f4;
                border-radius: 10px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .06);
                margin: 0 0 10px;
                padding: 10px 12px !important;
            }

            /* hide all td except first (card) and 4th (Action) */
            table#tax-rates-table tbody tr > td { display: none !important; }

            table#tax-rates-table tbody tr > td:first-child {
                display: block !important;
                width: 100% !important;
                max-width: none !important;
                padding: 0 !important;
                border: 0 !important;
                background: transparent !important;
            }

            table#tax-rates-table tbody tr > td:nth-child(4):not(.dataTables_empty) {
                display: flex !important;
                align-items: center;
                position: absolute;
                right: 8px;
                top: 50%;
                transform: translateY(-50%);
                width: auto !important;
                padding: 0 !important;
                border: 0 !important;
                background: transparent !important;
            }

            table#tax-rates-table tbody td.dataTables_empty {
                display: block !important;
                text-align: center;
                padding: 16px !important;
            }

            /* desktop text hide, card show */
            table#tax-rates-table tbody td:first-child .tx-desktop-only { display: none !important; }

            .tx-card { display: block; padding-right: 50px; min-height: 40px; }
            .tx-card-line1 { display: flex; align-items: center; flex-wrap: wrap; gap: 6px; }
            .tx-card-name {
                font-size: 15px;
                font-weight: 600;
                color: #1b2850;
                text-transform: uppercase;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                max-width: 100%;
            }

            .tx-card-badge {
                display: inline-block;
                font-size: 10px;
                font-weight: 600;
                padding: 2px 8px;
                border-radius: 12px;
            }

            .tx-card-badge.rate { background: #fff3df; color: #e8960c; }
            .tx-card-badge.active { background: #e3f6ec; color: #1b8a52; }
            .tx-card-badge.inactive { background: #fde8e8; color: #dc3545; }

            /* Actions: icons -> 3 dots */
            .tx-action-icons { display: none !important; }
            .tx-action-dropdown { display: block; }

            .tx-action-dropdown .action-menu-toggle {
                background: transparent;
                border: 0;
                box-shadow: none;
                color: #8b93a7;
                width: 24px;
                height: 30px;
                font-size: 16px;
            }

            .tx-action-dropdown .action-menu-toggle:hover,
            .tx-action-dropdown .action-menu-toggle:focus {
                background: transparent;
                border: 0;
                color: #1b2850;
            }

            .tx-action-dropdown .icon-desktop { display: none !important; }
            .tx-action-dropdown .icon-mobile { display: inline-block !important; }

            /* Floating button */
            .tx-fab-new {
                position: fixed;
                left: 50%;
                bottom: 74px;
                transform: translateX(-50%);
                z-index: 1030;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 12px 24px;
                border-radius: 30px;
                background: #e8334a;
                color: #fff !important;
                font-size: 15px;
                font-weight: 600;
                text-decoration: none;
                box-shadow: 0 6px 16px rgba(232, 51, 74, .35);
                white-space: nowrap;
            }

            .content:has(.tx-fab-new) { padding-bottom: 100px !important; }
        }

        /* ---------- Bottom-sheet modals (mobile + tablet) ---------- */
        @media (max-width: 1024.98px),
            (width: 1032px) and (height: 1376px),
            (width: 1280px) and (height: 800px) {

            #addpayment .modal-dialog,
            #editpayment .modal-dialog {
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                min-height: 100% !important;
                align-items: flex-end !important;
            }

            #addpayment.modal.fade .modal-dialog,
            #editpayment.modal.fade .modal-dialog {
                transform: translateY(100%);
                transition: transform .3s ease-out;
            }

            #addpayment.modal.show .modal-dialog,
            #editpayment.modal.show .modal-dialog {
                transform: none;
            }

            #addpayment .modal-content,
            #editpayment .modal-content {
                width: 100%;
                max-height: 90vh;
                border: 0;
                border-radius: 20px 20px 0 0;
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }

            #addpayment .modal-header,
            #editpayment .modal-header {
                padding: 16px 20px;
                border-bottom: 1px solid #eef0f4;
            }

            #addpayment .modal-title,
            #editpayment .modal-title {
                font-size: 17px;
                font-weight: 700;
                color: #1b2850;
            }

            #addpayment .modal-body,
            #editpayment .modal-body {
                padding: 16px 20px 8px;
                overflow-y: auto;
            }

            /* Footer: Cancel (grey) + Confirm/Update (red), equal width */
            #addpayment .modal-footer,
            #editpayment .modal-footer {
                display: flex;
                flex-wrap: nowrap;
                gap: 12px;
                padding: 12px 20px calc(16px + env(safe-area-inset-bottom));
                border-top: 1px solid #eef0f4;
            }

            #addpayment .modal-footer .btn,
            #editpayment .modal-footer .btn {
                flex: 1 1 0;
                margin: 0 !important;
                height: 46px;
                border-radius: 24px !important;
                font-size: 15px;
                font-weight: 700;
                border: 0 !important;
            }

            #addpayment .modal-footer .btn-cancel,
            #editpayment .modal-footer .btn-cancel {
                order: 1;
                background: #f1f2f6 !important;
                color: #1b2850 !important;
            }

            #addpayment .modal-footer .btn-submit,
            #editpayment .modal-footer .btn-submit {
                order: 2;
                background: #e8334a !important;
                color: #fff !important;
            }
        }

        /* Tablet: 2 column grid */
        @media screen and (min-width: 768px) and (max-width: 1024.98px),
            screen and (width: 1032px) and (height: 1376px),
            screen and (width: 1280px) and (height: 800px) {
            table#tax-rates-table tbody {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
                align-items: start;
            }

            table#tax-rates-table tbody tr { margin: 0 !important; }
            table#tax-rates-table tbody tr:has(> td.dataTables_empty) { grid-column: 1 / -1; }
            .tx-fab-new { bottom: 86px; font-size: 16px; }
        }

        /* iPad Mini / small tablet */
        @media screen and (min-width: 768px) and (max-width: 1024.98px) {
            .page-wrapper:has(.tx-quick-links) { padding-top: 0 !important; }
            .content:has(.tx-quick-links) { padding-top: 6px !important; margin-top: 0 !important; }
        }

        /* iPad Pro / Nest Hub Max: fixed header niche content */
        @media screen and (width: 1032px) and (height: 1376px),
            screen and (width: 1280px) and (height: 800px) {
            .content:has(.tx-quick-links) { padding-top: 16px !important; }
        }
    </style>

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Tax Rates</h4>
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(15, 'add'))
                    <a class="btn btn-added" data-bs-toggle="modal" data-bs-target="#addpayment"><img
                            src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img"
                            class="me-1">New Tax Rates</a>
                @endif
            </div>
        </div>

        {{-- Mobile / tablet header --}}
        <div class="tx-quick-links">
            <div class="tx-quick-head">
                <div class="tx-quick-title">Tax Rates</div>
                @if (app('hasPermission')(15, 'add'))
                    <a href="javascript:void(0);" class="btn btn-sm btn-added" data-bs-toggle="modal"
                        data-bs-target="#addpayment">
                        <i class="fas fa-plus me-1"></i> New Tax
                    </a>
                @endif
            </div>
        </div>

        @if (app('hasPermission')(15, 'add'))
            <a href="javascript:void(0);" class="tx-fab-new" data-bs-toggle="modal" data-bs-target="#addpayment">
                <i class="fas fa-percent"></i> Add New Tax
            </a>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="mb-2">
                    <div class="search-set d-flex justify-content-md-start justify-content-start w-100">
                        <div class="search-path"></div>
                        <div class="search-input">
                            <a class="btn btn-searchset">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}"
                                    alt="img">
                            </a>
                            <input type="text" id="tax-search-input" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table datanew" id="tax-rates-table">
                        <thead>
                            <tr>
                                <th>Tax name</th>
                                <th>Tax (%)</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <div
                    class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
                        <select id="tax-per-page-select" class="form-select form-select-sm"
                            style="width: auto; border: 1px solid #ddd;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="ms-3" style="font-size: 14px; color: #555;">
                            <span id="tax-pagination-from">0</span> - <span id="tax-pagination-to">0</span> of
                            <span id="tax-pagination-total">0</span> items
                        </span>
                    </div>
                    <nav aria-label="Tax pagination">
                        <ul class="pagination pagination-sm mb-0" id="tax-pagination-numbers"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addpayment" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add TAX </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Tax Name<span class="manitory">*</span></label>
                                <input type="text">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Tax Rate(%)<span class="manitory">*</span></label>
                                <input type="text">
                                <small id="taxRateError" class="text-danger d-none">Tax rate cannot be negative.</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <label>Status</label>
                                <select class="select">
                                    <option value="active"> Active</option>
                                    <option value="inactive"> InActive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-submit btn-submit-add">Confirm</button>
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editpayment" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Tax</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Tax Name<span class="manitory">*</span></label>
                                <input type="text">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Tax Rate(%)<span class="manitory">*</span></label>
                                <input type="text">
                                <small id="taxRateError1" class="text-danger d-none">Tax rate cannot be negative.</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <label>Status</label>
                                <select class="select">
                                    <option value="active"> Active</option>
                                    <option value="inactive"> DeActive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-submit btn-submit-edit">Update</button>
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            var authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            const $addSubmitBtn = $(".btn-submit-add");
            const $editSubmitBtn = $(".btn-submit-edit");
            const addSubmitBtnDefaultHtml = $addSubmitBtn.html();
            const editSubmitBtnDefaultHtml = $editSubmitBtn.html();

            function toggleModalSubmitLoading(type, isLoading) {
                const isAdd = type === "add";
                const $btn = isAdd ? $addSubmitBtn : $editSubmitBtn;
                const defaultHtml = isAdd ? addSubmitBtnDefaultHtml : editSubmitBtnDefaultHtml;

                if (isLoading) {
                    $btn
                        .prop("disabled", true)
                        .addClass("disabled")
                        .attr("aria-disabled", "true")
                        .html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...');
                } else {
                    $btn
                        .prop("disabled", false)
                        .removeClass("disabled")
                        .removeAttr("aria-disabled")
                        .html(defaultHtml);
                }
            }

            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';
            let rowsOnPage = 0;

            var table = $("table.datanew").DataTable({
                destroy: true,
                bFilter: false,
                paging: false,
                info: false,
                searching: false,
                ordering: false,
                dom: 't',
                language: {
                    emptyTable: "No tax rates found"
                }
            });
            loadTaxRates(currentPage);

            $('#tax-search-input').on('keyup', function () {
                searchQuery = $(this).val();
                loadTaxRates(1);
            });

            $('#tax-per-page-select').on('change', function () {
                perPage = $(this).val();
                loadTaxRates(1);
            });

            function loadTaxRates(page = 1) {
                let url = `{{ route('tax-rates.index') }}?page=${page}&per_page=${perPage}`;
                if (selectedSubAdminId) {
                    url += "&selectedSubAdminId=" + encodeURIComponent(selectedSubAdminId);
                }
                if (searchQuery) {
                    url += "&search=" + encodeURIComponent(searchQuery);
                }

                $.ajax({
                    url: url,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function (response) {
                        let tableRows = [];
                        const taxRates = response.taxRates || response.data || [];
                        const pagination = response.pagination || {
                            current_page: 1,
                            last_page: 1,
                            per_page: perPage,
                            total: taxRates.length
                        };

                        rowsOnPage = taxRates.length;
                        currentPage = pagination.current_page;
                        lastPage = pagination.last_page;
                        updatePaginationUI(pagination);

                        taxRates.forEach(function (tax) {
                            const isActive = tax.status === "active";
                            const statusBadge = isActive ?
                                '<span class="badge bg-success">Active</span>' :
                                '<span class="badge bg-danger">DeActive</span>';

                            // Dropdown menu items (mobile / tablet)
                            let menuItems = '';
                            @if (app('hasPermission')(15, 'edit'))
                                menuItems += `<a class="dropdown-item edit-tax" href="javascript:void(0);" data-id="${tax.id}" data-bs-toggle="modal" data-bs-target="#editpayment"><i class="fas fa-edit"></i><span>Edit</span></a>`;
                            @endif
                            @if (app('hasPermission')(15, 'delete'))
                                menuItems += `<a class="dropdown-item text-danger delete-tax" href="javascript:void(0);" data-id="${tax.id}"><i class="fas fa-trash"></i><span>Delete</span></a>`;
                            @endif
                            if (!menuItems) {
                                menuItems = '<span class="dropdown-item text-muted">No actions</span>';
                            }

                            const firstColumn = `
                                <span class="tx-desktop-only">${tax.tax_name}</span>
                                <div class="tx-card">
                                    <div class="tx-card-line1">
                                        <span class="tx-card-name">${tax.tax_name}</span>
                                        <span class="tx-card-badge rate">${tax.tax_rate}%</span>
                                        <span class="tx-card-badge ${isActive ? 'active' : 'inactive'}">${isActive ? 'Active' : 'DeActive'}</span>
                                    </div>
                                </div>`;

                            const actionCell = `
                                <div class="text-end tx-action-icons">
                                    @if (app('hasPermission')(15, 'edit'))
                                    <a href="javascript:void(0);" class="edit-tax" data-id="${tax.id}" data-bs-toggle="modal" data-bs-target="#editpayment">
                                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}" alt="Edit">
                                    </a>
                                    @endif
                                    @if (app('hasPermission')(15, 'delete'))
                                    <a href="javascript:void(0);" class="delete-tax mx-3" data-id="${tax.id}">
                                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}" alt="Delete">
                                    </a>
                                    @endif
                                </div>
                                <div class="dropdown tx-action-dropdown">
                                    <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i class="fas fa-ellipsis-h icon-desktop"></i>
                                        <i class="fas fa-ellipsis-v icon-mobile"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end tx-action-menu">${menuItems}</div>
                                </div>`;

                            tableRows.push([
                                firstColumn,
                                `${tax.tax_rate}%`,
                                statusBadge,
                                actionCell
                            ]);
                        });

                        table.clear().rows.add(tableRows).draw();
                    }
                });
            }

            function updatePaginationUI(pagination) {
                let from = (pagination.current_page - 1) * pagination.per_page + 1;
                let to = pagination.current_page * pagination.per_page;
                if (to > pagination.total) to = pagination.total;
                if (pagination.total === 0) from = 0;

                $('#tax-pagination-from').text(from);
                $('#tax-pagination-to').text(to);
                $('#tax-pagination-total').text(pagination.total);

                let paginationHtml = '';

                paginationHtml += `
                    <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                        <a class="page-link tax-page-link" href="javascript:void(0);" data-page="${pagination.current_page - 1}">Previous</a>
                    </li>
                `;

                const visiblePageCount = 2;
                let startPage = Math.floor((pagination.current_page - 1) / visiblePageCount) * visiblePageCount + 1;
                let endPage = Math.min(pagination.last_page, startPage + visiblePageCount - 1);

                if (startPage > 1) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link tax-page-link" href="javascript:void(0);" data-page="${startPage - 1}" data-action="prev-group">..</a>
                        </li>
                    `;
                }

                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                            <a class="page-link tax-page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                        </li>
                    `;
                }

                if (endPage < pagination.last_page) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link tax-page-link" href="javascript:void(0);" data-page="${endPage + 1}" data-action="next-group">..</a>
                        </li>
                    `;
                }

                paginationHtml += `
                    <li class="page-item ${pagination.current_page === pagination.last_page || pagination.last_page === 0 ? 'disabled' : ''}">
                        <a class="page-link tax-page-link" href="javascript:void(0);" data-page="${pagination.current_page + 1}">Next</a>
                    </li>
                `;

                $('#tax-pagination-numbers').html(paginationHtml);
                $('.pagination-controls').show();
            }

            $(document).on('click', '#tax-pagination-numbers .tax-page-link', function (e) {
                e.preventDefault();
                let page = $(this).data('page');
                let action = $(this).data('action');

                if (action === 'next-group') {
                    if (page && page <= lastPage) {
                        loadTaxRates(page);
                    }
                    return;
                }

                if (action === 'prev-group') {
                    let prevStartPage = Math.max(1, page - 2);
                    if (prevStartPage >= 1 && prevStartPage <= lastPage) {
                        loadTaxRates(prevStartPage);
                    }
                    return;
                }

                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    loadTaxRates(page);
                }
            });

            /* ---------- Floating action dropdown (mobile / tablet) ---------- */
            $(document).on('show.bs.dropdown', '.tx-action-dropdown', function () {
                const $dd = $(this), $menu = $dd.find('.tx-action-menu');
                if (!$menu.length) return;
                $dd.data('floating-menu', $menu).data('menu-parent', $menu.parent());
                $menu.addClass('tx-action-menu-floating').appendTo('body');
            });

            $(document).on('shown.bs.dropdown', '.tx-action-dropdown', function () {
                const $dd = $(this), $menu = $dd.data('floating-menu');
                const btn = $dd.find('.action-menu-toggle')[0];
                if (!$menu || !btn) return;
                const r = btn.getBoundingClientRect();
                const w = $menu[0].offsetWidth || 160, h = $menu[0].offsetHeight || 120;
                let left = Math.max(8, Math.min(r.right - w, window.innerWidth - w - 8));
                let top = r.bottom + 4;
                if (top + h > window.innerHeight - 8) top = Math.max(8, r.top - h - 4);
                $menu.css({ position: 'fixed', left: left + 'px', top: top + 'px', transform: 'none' });
            });

            $(document).on('hidden.bs.dropdown', '.tx-action-dropdown', function () {
                const $dd = $(this), $menu = $dd.data('floating-menu'), $parent = $dd.data('menu-parent');
                if ($menu && $parent) {
                    $menu.removeClass('tx-action-menu-floating').removeAttr('style');
                    $parent.append($menu);
                }
            });

            $(window).on('scroll resize', function () {
                $('.tx-action-dropdown.show .action-menu-toggle').dropdown('hide');
            });

            /* ---------- Form helpers ---------- */
            function clearErrors() {
                $(".error-text").remove();
                $("input, select").removeClass("is-invalid");
            }

            function validateForm(modal) {
                clearErrors();
                let isValid = true;

                $(modal).find("input, select").each(function () {
                    let value = $(this).val().trim();
                    let label = $(this).prev("label").text().replace("*", "").trim();

                    if (value === "") {
                        $(this).addClass("is-invalid");
                        $(this).after(`<small class="text-danger error-text">${label} is required.</small>`);
                        isValid = false;
                    }
                });

                return isValid;
            }

            function resetForm(modal) {
                $(modal).find("input[type='text']").val("");
                $(modal).find("select").val("active");
                clearErrors();
            }

            function showFieldErrors(modalSel, xhr) {
                $(".error-text").remove();
                $(modalSel + " input, " + modalSel + " select").removeClass("is-invalid");

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON?.errors;
                    if (errors) {
                        $.each(errors, function (key, value) {
                            let $el = null;
                            if (key === "name") $el = $(modalSel + " input[type='text']").eq(0);
                            if (key === "rate") $el = $(modalSel + " input[type='text']").eq(1);
                            if (key === "status") $el = $(modalSel + " select");
                            if ($el) {
                                $el.addClass("is-invalid");
                                $el.after(`<small class="text-danger error-text">${value[0]}</small>`);
                            }
                        });
                    }
                } else if (xhr.status === 401) {
                    Swal.fire({
                        title: "Unauthorized!",
                        text: "Session expired. Please login again.",
                        icon: "warning"
                    }).then(() => {
                        window.location.href = "/login";
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: xhr.responseJSON?.message || "Something went wrong.",
                        icon: "error"
                    });
                }
            }

            /* ---------- Add ---------- */
            $(".btn-submit-add").on("click", function () {
                if ($addSubmitBtn.prop("disabled")) return;
                if (!validateForm("#addpayment")) return;

                let taxName = $("#addpayment input[type='text']").eq(0).val();
                let taxRate = $("#addpayment input[type='text']").eq(1).val();
                let taxStatus = $("#addpayment select").val();
                if (taxRate < 0) {
                    $("#taxRateError").removeClass("d-none");
                    return false;
                } else {
                    $("#taxRateError").addClass("d-none");
                }
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                $.ajax({
                    url: "{{ route('tax-rates.store') }}",
                    type: "POST",
                    beforeSend: function () {
                        toggleModalSubmitLoading("add", true);
                    },
                    data: {
                        name: taxName,
                        rate: taxRate,
                        status: taxStatus,
                        selectedSubAdminId: selectedSubAdminId,
                        _token: "{{ csrf_token() }}"
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function (response) {
                        $("#addpayment").modal("hide");
                        resetForm("#addpayment");

                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                        });

                        loadTaxRates();
                    },
                    error: function (xhr) {
                        showFieldErrors("#addpayment", xhr);
                    },
                    complete: function () {
                        toggleModalSubmitLoading("add", false);
                    }
                });
            });

            /* ---------- Edit ---------- */
            $(document).on("click", ".edit-tax", function () {
                let taxId = $(this).data("id");
                $.ajax({
                    url: `/api/tax-rates/${taxId}`,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function (response) {
                        $("#editpayment input[type='text']").eq(0).val(response.taxRate.tax_name);
                        $("#editpayment input[type='text']").eq(1).val(parseInt(response.taxRate.tax_rate));
                        $("#editpayment select").val(response.taxRate.status.toLowerCase()).trigger("change");
                        $("#editpayment").data("id", taxId);
                    }
                });
            });

            $(".btn-submit-edit").on("click", function () {
                if ($editSubmitBtn.prop("disabled")) return;
                if (!validateForm("#editpayment")) return;

                let taxId = $("#editpayment").data("id");
                let taxName = $("#editpayment input[type='text']").eq(0).val();
                let taxRate = $("#editpayment input[type='text']").eq(1).val();
                let taxStatus = $("#editpayment select").val();

                if (taxRate < 0) {
                    $("#taxRateError1").removeClass("d-none");
                    return false;
                } else {
                    $("#taxRateError1").addClass("d-none");
                }

                $.ajax({
                    url: `/api/tax-rates/update/${taxId}`,
                    type: "POST",
                    beforeSend: function () {
                        toggleModalSubmitLoading("edit", true);
                    },
                    data: {
                        name: taxName,
                        rate: taxRate,
                        status: taxStatus,
                        _token: "{{ csrf_token() }}"
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function (response) {
                        $("#editpayment").modal("hide");

                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                        });

                        loadTaxRates(currentPage);
                    },
                    error: function (xhr) {
                        showFieldErrors("#editpayment", xhr);
                    },
                    complete: function () {
                        toggleModalSubmitLoading("edit", false);
                    }
                });
            });

            /* ---------- Delete ---------- */
            $(document).on("click", ".delete-tax", function () {
                let taxId = $(this).data("id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ff9f43",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/tax-rates/delete/${taxId}`,
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                            },
                            headers: {
                                "Authorization": "Bearer " + authToken
                            },
                            success: function (response) {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonColor: "#ff9f43",
                                    confirmButtonText: "OK"
                                });
                                if (currentPage > 1 && rowsOnPage === 1) {
                                    currentPage--;
                                }
                                loadTaxRates(currentPage);
                            },
                            error: function (xhr) {
                                let msg = xhr.responseJSON?.error || xhr.responseJSON?.message || "An error occurred.";
                                Swal.fire({
                                    title: "Error!",
                                    text: msg,
                                    icon: "error",
                                    confirmButtonColor: "#d33",
                                    confirmButtonText: "OK"
                                });
                            }
                        });
                    }
                });
            });

            // Reset form when modal is closed
            $("#addpayment, #editpayment").on("hidden.bs.modal", function () {
                resetForm(this);
            });
        });
    </script>
@endpush