@extends('layout.app')

@section('title', 'Credit Note List')

@push('css')
    <style>
        #creditNoteItemsTable tbody tr td {
            /* white-space: normal !important; */
            word-break: break-word;
            word-wrap: break-word;
        }

        /* ✅ Hide default DataTables search/paging */
        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }

        /* Custom Pagination Styling */
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

        .pagination .page-item.disabled .page-link {
            background-color: #fff !important;
            color: #dee2e6 !important;
            border: 1px solid #dee2e6 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
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
            top: 7px !important;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        #creditNoteItemsTable {
            width: 100% !important;
        }

        .credit-mobile {
    width: fit-content;
    max-width: 100%;

}

@media (min-width: 768px) {
    .credit-mobile {
        display: inline-flex !important;
        align-items: center;
    }
}

        /* Mobile toggle button styles */
        .mobile-toggle-btn-table {
            background: #ff9f43;
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            min-width: 32px;
            min-height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s ease;
            padding: 0;
            margin: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .mobile-toggle-btn-table:hover {
            background: #ff8c2e;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .mobile-toggle-btn-table.minus {
            background: #dc3545;
        }

        .mobile-toggle-btn-table.minus:hover {
            background: #c82333;
        }

        .details-row {
            display: none;
        }

        .details-row.show {
            display: table-row;
        }

        /* .details-content {
            padding: 15px;
            background: #f8f9fa;
            border-top: 2px solid #ff9f43;
        } */

        .details-content .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
        }

        .details-content .detail-item:last-child {
            border-bottom: none;
        }

        .details-content .detail-label {
            font-weight: 600;
            color: #495057;
        }

        .details-content .detail-value {
            color: #212529;
        }

        .credit-note-mobile-ref {
            display: block;
            font-weight: 500;
        }

        .credit-note-mobile-party {
            display: none;
        }

        /* Responsive Table Styling */
        @media screen and (max-width: 767px) {
            .table-container {
                overflow-x: visible !important;
                -webkit-overflow-scrolling: touch !important;
            }

            #creditNoteItemsTable thead th:nth-child(n+3),
            #creditNoteItemsTable tbody td:nth-child(n+3) {
                display: none !important;
            }

            #creditNoteItemsTable thead th:first-child,
            #creditNoteItemsTable tbody td:first-child {
                display: table-cell !important;
                width: auto !important;
                word-wrap: break-word;
            }

            #creditNoteItemsTable thead th.details-column,
            #creditNoteItemsTable tbody td.details-control {
                display: table-cell !important;
                text-align: center;
                vertical-align: middle;
                width: 60px !important;
            }

            .credit-note-mobile-party {
                display: block;
                margin-top: 4px;
                font-size: 14px;
                line-height: 1.4;
                color: #495057;
                font-weight: 500;
                white-space: normal;
                word-break: break-word;
                overflow-wrap: anywhere;
            }
        }

        @media (min-width: 768px) {
            #creditNoteItemsTable {
                min-width: 980px;
            }

            #creditNoteItemsTable thead th.details-column,
            #creditNoteItemsTable tbody td.details-control {
                display: none !important;
            }
        }

        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-container table thead th {
            white-space: nowrap;
        }

        #creditNoteItemsTable {
            min-width: 100%;
        }

        @media (max-width: 575px) {
            .page-header .page-btn {
                margin-top: 0px !important;
            }
        }

        @media (max-width: 420px) {
            .page-actions {
                display: flex !important;
                flex-direction: row !important;
                justify-content: end !important;
                align-items: center !important;
                /* width: 100%; */
            }

            .credit-mobile {
                background-color: #f8f9fa;
                color: #ff9f43;
                font-weight: bold;
                padding: 3px 10px;
                border: 1px solid #d1d1d1;
                border-radius: 8px;
                margin-right: 5px;
                margin-top: 8px;
            }

            .page-actions>div {
                margin-right: 0 !important;
            }

            .page-btn a {
                width: auto !important;
                padding: 8px 15px !important;
                height: auto !important;
                margin-bottom: 0 !important;
            }

            .page-header .page-title h4 {
                margin-bottom: 10px;
            }
        }
        /* Action column fix */
        #creditNoteItemsTable th:last-child,
        #creditNoteItemsTable td:last-child {
            width: 120px !important;
            min-width: 120px;
            max-width: 120px;
            text-align: center;
            vertical-align: middle;
        }
        /* Action buttons alignment */
        .action-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }

        /* icon size fix */
        .action-buttons img {
            width: 18px;
            height: 18px;
        }

        /* Dark Mode Override for Action Icons */
        :root[data-theme="dark"] .action-buttons img {
            filter: brightness(0) invert(1);
        }

        .total-settlement-label {
            color: #495057;
        }

        :root[data-theme="dark"] .total-settlement-label {
            color: #d1d5db !important;
        }

        @media (max-width: 767px) {
            #creditNoteItemsTable td:last-child {
                width: 100px !important;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 1024.98px) {
            .page-wrapper {
                padding-top: 0 !important;
            }

            .page-wrapper > .content.mm-page {
                margin-top: 0 !important;
                padding-top: 6px !important;
            }

            .mm-page > .page-header,
            .credit-desktop-total {
                display: none !important;
            }

            .mm-page > .mm-head.d-md-none {
                display: flex !important;
            }

            .mm-page .mm-card-list {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 8px !important;
                align-items: start;
            }

            .mm-settlement-card.d-md-none {
                display: flex !important;
            }
        }

        .mm-act.is-view {
            background: #f1f5f9;
            color: #475569 !important;
        }

        .mm-settlement-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            border-radius: 12px;
            padding: 10px 14px;
            margin-bottom: 8px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
        }

        .mm-settlement-label {
            font-size: 13px;
            font-weight: 600;
            color: #475467;
        }

        .mm-settlement-val {
            font-size: 15px;
            font-weight: 700;
            color: #ff9f43;
        }

        :root[data-theme="dark"] .mm-act.is-view {
            background: #334155;
            color: #94a3b8 !important;
        }

        :root[data-theme="dark"] .mm-settlement-card {
            background: #1e293b;
        }

        :root[data-theme="dark"] .mm-settlement-label {
            color: #94a3b8;
        }

        /* 3-dot dropdown menu for responsive cards */
        .credit-note-action-dropdown {
            position: relative;
        }

        .credit-note-action-dropdown .action-menu-toggle {
            align-items: center;
            background: transparent !important;
            border: 0 !important;
            border-radius: 6px;
            color: #8b93a7 !important;
            display: inline-flex !important;
            height: 32px;
            justify-content: center;
            padding: 0 !important;
            width: 32px;
            box-shadow: none !important;
            font-size: 16px;
            cursor: pointer;
        }

        .credit-note-action-dropdown .action-menu-toggle:hover,
        .credit-note-action-dropdown .action-menu-toggle:focus {
            background: #f1f5f9 !important;
            color: #1b2850 !important;
        }

        .credit-note-action-menu {
            border: 1px solid #e8ebed;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
            min-width: 140px;
            padding: 6px;
            z-index: 9999;
            background: #ffffff;
        }

        .credit-note-action-menu-floating {
            position: fixed !important;
            z-index: 999999 !important;
        }

        .credit-note-action-menu .dropdown-item {
            align-items: center;
            border-radius: 6px;
            color: #344054;
            display: flex;
            font-size: 13px;
            font-weight: 500;
            gap: 8px;
            padding: 8px 12px;
            width: 100%;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .credit-note-action-menu .dropdown-item i {
            color: #667085;
            font-size: 14px;
            text-align: center;
            width: 16px;
        }

        .credit-note-action-menu .dropdown-item.text-danger,
        .credit-note-action-menu .dropdown-item.text-danger i {
            color: #ea5455 !important;
        }

        .credit-note-action-menu .dropdown-item:hover {
            background: #f4f6f8;
            color: #1b2850;
        }

        .credit-note-action-menu .dropdown-item.text-danger:hover {
            background: #fef2f2;
            color: #dc2626 !important;
        }

        :root[data-theme="dark"] .credit-note-action-dropdown .action-menu-toggle {
            color: #94a3b8 !important;
        }

        :root[data-theme="dark"] .credit-note-action-dropdown .action-menu-toggle:hover,
        :root[data-theme="dark"] .credit-note-action-dropdown .action-menu-toggle:focus {
            background: #334155 !important;
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .credit-note-action-menu {
            background: #1e293b;
            border-color: #334155;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        :root[data-theme="dark"] .credit-note-action-menu .dropdown-item {
            color: #e2e8f0;
        }

        :root[data-theme="dark"] .credit-note-action-menu .dropdown-item i {
            color: #94a3b8;
        }

        :root[data-theme="dark"] .credit-note-action-menu .dropdown-item:hover {
            background: #334155;
            color: #fff;
        }

        :root[data-theme="dark"] .credit-note-action-menu .dropdown-item.text-danger:hover {
            background: rgba(234, 84, 85, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="content mm-page">
        <div class="mm-head d-md-none">
            <div>
                <div class="mm-title">Credit Note List</div>
                <div class="mm-count" id="mmCount"></div>
            </div>
            @if (app('hasPermission')(27, 'add'))
                <a href="{{ route('credit-notes-items.create') }}" class="btn btn-sm mm-add">
                    <i class="fas fa-plus me-1"></i> New Item
                </a>
            @endif
        </div>

        <div class="page-header">
            <div class="page-title">
                <h4>Credit Note List</h4>
            </div>
            <div class="d-flex align-items-center flex-wrap page-actions">

                <div class="page-btn">
                    @if (app('hasPermission')(27, 'add'))
                        <a href="{{ route('credit-notes-items.create') }}" class="btn btn-added">
                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img"
                                class="me-1">
                            Add <span class="d-none d-sm-inline">Credit Note Item</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="mm-settlement-card d-md-none mb-2">
            <span class="mm-settlement-label">Total Settlement:</span>
            <span class="mm-settlement-val">{{ $currencySymbol }}<span id="total_settlement_amount_mobile">0.00</span></span>
        </div>

        <div class="d-none d-md-flex justify-content-end credit-desktop-total">
            <div class="d-flex align-items-center credit-mobile mb-2 ">
                <span class="total-settlement-label" style="margin-right: 8px; font-size: 14px;">Total Settlement Amount:</span>
                <span style="font-size: 16px;">{{ $currencySymbol }}<span
                        id="total_settlement_amount">0.00</span></span>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="mb-2 mm-search">
                    <div class="search-set d-flex justify-content-md-start justify-content-start w-100">
                        <div class="search-path"></div>
                        <div class="search-input">
                            <a class="btn btn-searchset">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}"
                                    alt="img">
                            </a>
                            <input type="text" id="search-input" class="form-control" placeholder="Search by name, phone, order no…">
                        </div>
                    </div>
                </div>
                <div class="table-container table-responsive">

                    <table class="table" id="creditNoteItemsTable" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Order / Bill  Number</th>
                                <th class="details-column">Details</th>
                                <th>Customer Name / Vendor Name</th>
                                <th>Type</th>
                                <th>Total Amount</th>
                                {{-- <th>Paid Amount</th> --}}
                                {{-- <th>Remaining Amount</th> --}}
                                <th>Settlement Amount</th>
                                <!-- <th>Final Total</th> -->
                                {{-- <th>Reason</th> --}}
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data will be loaded via AJAX --}}
                        </tbody>
                    </table>
                </div>

                <div class="mm-card-list d-md-none" id="mmCardList"></div>

                <!-- ✅ Pagination Controls -->
                <div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
                        <select id="per-page-select" class="form-select form-select-sm"
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
                        <ul class="pagination pagination-sm mb-0" id="pagination-numbers">
                            <!-- Page numbers will be populated by JS -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    @include('partials.master-mobile-ui')
    <script>
        let table;
        const canViewCreditNoteItem = @json(app('hasPermission')(27, 'view'));
        const canEditCreditNoteItem = @json(app('hasPermission')(27, 'edit'));
        const canDeleteCreditNoteItem = @json(app('hasPermission')(27, 'delete'));

        function buildActionLinks(id) {
            let actions = '';

            if (canViewCreditNoteItem) {
                actions += `
                    <a href="/view-credit-note-items/${id}">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/eye.svg' }}">
                    </a>
                `;
            }

            if (canEditCreditNoteItem) {
                actions += `
                    <a href="/edit-credit-note-items/${id}">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}">
                    </a>
                `;
            }

            if (canDeleteCreditNoteItem) {
                actions += `
                    <a class="confirm-text" data-id="${id}" href="javascript:void(0);">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}">
                    </a>
                `;
            }

            return actions || '<span class="text-muted">-</span>';
        }

        function buildMobileActionLinks(id) {
            let items = '';

            if (canViewCreditNoteItem) {
                items += `
                    <a class="dropdown-item" href="/view-credit-note-items/${id}">
                        <i class="fas fa-eye"></i> View
                    </a>
                `;
            }

            if (canEditCreditNoteItem) {
                items += `
                    <a class="dropdown-item" href="/edit-credit-note-items/${id}">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                `;
            }

            if (canDeleteCreditNoteItem) {
                items += `
                    <a class="dropdown-item text-danger confirm-text" data-id="${id}" href="javascript:void(0);">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                `;
            }

            if (!items.trim()) {
                return '';
            }

            return `
                <div class="dropdown credit-note-action-dropdown">
                    <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end credit-note-action-menu">
                        ${items}
                    </div>
                </div>
            `;
        }

        $(document).ready(function() {
            let currencySymbol = "{{ $currencySymbol }}";
            let currencyPosition = "{{ $currencyPosition }}";
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            // Pagination & Search state
            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';

            function formatCurrency(amount) {
                let formatted = parseFloat(amount).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                return currencyPosition === 'left' ?
                    currencySymbol + formatted :
                    formatted + currencySymbol;
            }

            // Handle search input
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                fetchCreditNoteItems(1);
            });

            // Handle per-page change
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchCreditNoteItems(1);
            });

            fetchCreditNoteItems();

            function fetchCreditNoteItems(page = 1) {
                currentPage = page;
                let ajaxUrl = "/api/credit-note-items-api";
                let ajaxData = {
                    page: currentPage,
                    per_page: perPage,
                    search: searchQuery
                };
                if (selectedSubAdminId) {
                    ajaxData.selectedSubAdminId = selectedSubAdminId;
                }

                $.ajax({
                    url: ajaxUrl,
                    type: "GET",
                    data: ajaxData,
                    headers: {
                        "Authorization": "Bearer " + localStorage.getItem("authToken")
                    },
                    success: function(response) {
                        // Destroy DataTable before injecting new HTML
                        if ($.fn.DataTable.isDataTable('#creditNoteItemsTable')) {
                            $('#creditNoteItemsTable').DataTable().clear().destroy();
                        }

                        let tableBody = "";
                        let mmCards = [];
                        if (response.data && response.data.length > 0) {
                            if (!window.creditNoteDataMap) window.creditNoteDataMap = {};

                            $.each(response.data, function(index, row) {
                                window.creditNoteDataMap[row.id] = row;

                                const isPayment = row.transaction_type === 'payment';

                                let transNumber = "N/A";
                                if (isPayment) {
                                    transNumber = row.purchase_invoice ? row.purchase_invoice.bill_no : "N/A";
                                } else {
                                    transNumber = row.order ? row.order.order_number : "N/A";
                                }

                                let displayName = "N/A";
                                if (isPayment) {
                                    displayName = row.purchase_invoice && row.purchase_invoice.vendor ? row.purchase_invoice.vendor.name : "N/A";
                                } else {
                                    displayName = row.order && row.order.user ? row.order.user.name : "N/A";
                                }

                                let typeName = row.credit_note ? row.credit_note.type_name : "N/A";

                                tableBody += `
                                    <tr data-item-id="${row.id}">
                                        <td>
                                            <span class="credit-note-mobile-party">${displayName}</span>
                                            <span class="credit-note-mobile-ref">${transNumber}</span>
                                        </td>
                                        <td class="details-control">
                                            <button class="mobile-toggle-btn-table" onclick="toggleCreditNoteRowDetails('${row.id}')">+</button>
                                        </td>
                                        <td>${displayName}</td>
                                        <td>${typeName}</td>
                                        <td>${formatCurrency(row.total_amt)}</td>
                                        <td>${formatCurrency(row.settlement_amount)}</td>
                                        <td><div class="action-buttons">${buildActionLinks(row.id)}</div></td>
                                    </tr>
                                `;

                                let cardTitle = (displayName && displayName !== 'N/A') ? displayName : transNumber;
                                let cardSub = (displayName && displayName !== 'N/A' && transNumber !== 'N/A')
                                    ? (transNumber + (typeName && typeName !== 'N/A' ? ' • ' + typeName : ''))
                                    : (typeName && typeName !== 'N/A' ? typeName : '');

                                mmCards.push(MasterMobileUI.card({
                                    title: cardTitle,
                                    sub: cardSub,
                                    actions: buildMobileActionLinks(row.id),
                                    stats: [
                                        { label: 'Total Amount', value: formatCurrency(row.total_amt) },
                                        { label: 'Settlement Amount', html: `<span style="color:#ff9f43;font-weight:700;">${formatCurrency(row.settlement_amount)}</span>` },
                                        { label: 'Type', value: typeName }
                                    ]
                                }));
                            });

                            $('#total_settlement_amount, #total_settlement_amount_mobile').text(response.total_settlement);
                        } else {
                            tableBody = '<tr><td colspan="7" class="text-center">No items found.</td></tr>';
                            $('#total_settlement_amount, #total_settlement_amount_mobile').text('0.00');
                        }

                        $('#creditNoteItemsTable tbody').html(tableBody);

                        // Update Pagination
                        renderPagination(response.pagination);

                        // Render Mobile Cards
                        MasterMobileUI.render(mmCards, 'No credit note items found');

                        // Reinitialize DataTable
                        $('#creditNoteItemsTable').DataTable({
                            responsive: true,
                            paging: false,
                            ordering: false,
                            info: false,
                            searching: false,
                            language: {
                                emptyTable: "No items found."
                            }
                        });
                    },
                    error: function(xhr) {
                        MasterMobileUI.render([], 'Error loading credit note items');
                    }
                });
            }

            // function renderPagination(pagination) {
            //     lastPage = pagination.last_page;
            //     currentPage = pagination.current_page;
            //     let total = pagination.total;
            //     let perPage = pagination.per_page;

            //     let from = total > 0 ? (currentPage - 1) * perPage + 1 : 0;
            //     let to = Math.min(currentPage * perPage, total);

            //     $("#pagination-from").text(from);
            //     $("#pagination-to").text(to);
            //     $("#pagination-total").text(total);

            //     let paginationHtml = "";

            //     paginationHtml += `
            //         <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            //             <a class="page-link" href="javascript:void(0);" onclick="changePage(${currentPage - 1})">Previous</a>
            //         </li>
            //     `;

            //     let startPage = Math.max(1, currentPage - 2);
            //     let endPage = Math.min(lastPage, startPage + 4);
            //     if (endPage - startPage < 4) {
            //         startPage = Math.max(1, endPage - 4);
            //     }

            //     for (let i = startPage; i <= endPage; i++) {
            //         paginationHtml += `
            //             <li class="page-item ${i === currentPage ? 'active' : ''}">
            //                 <a class="page-link" href="javascript:void(0);" onclick="changePage(${i})">${i}</a>
            //             </li>
            //         `;
            //     }

            //     paginationHtml += `
            //         <li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
            //             <a class="page-link" href="javascript:void(0);" onclick="changePage(${currentPage + 1})">Next</a>
            //         </li>
            //     `;

            //     $("#pagination-numbers").html(paginationHtml);
            // }

            function renderPagination(pagination) {
                lastPage = pagination.last_page;
                currentPage = pagination.current_page;
                let total = pagination.total;
                let perPage = pagination.per_page;

                let from = total > 0 ? (currentPage - 1) * perPage + 1 : 0;
                let to = Math.min(currentPage * perPage, total);

                $("#pagination-from").text(from);
                $("#pagination-to").text(to);
                $("#pagination-total").text(total);

                let paginationHtml = "";

                // Previous Button
                paginationHtml += `
                    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" onclick="changePage(${currentPage - 1})">Previous</a>
                    </li>
                `;

                const visiblePageCount = 2;
                let startPage = Math.floor((currentPage - 1) / visiblePageCount) * visiblePageCount + 1;
                let endPage = Math.min(lastPage, startPage + visiblePageCount - 1);

                if (startPage > 1) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);" onclick="changePage(${startPage - 1})">..</a>
                        </li>
                    `;
                }

                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0);" onclick="changePage(${i})">${i}</a>
                        </li>
                    `;
                }

                if (endPage < lastPage) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);" onclick="changePage(${endPage + 1})">..</a>
                        </li>
                    `;
                }

                // Next Button
                paginationHtml += `
                    <li class="page-item ${currentPage === lastPage || lastPage === 0 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" onclick="changePage(${currentPage + 1})">Next</a>
                    </li>
                `;

                $("#pagination-numbers").html(paginationHtml);
                $(".pagination-controls").toggle(total > 0);
            }
            window.changePage = function(page) {
                if (page < 1 || page > lastPage) return;
                fetchCreditNoteItems(page);
            };

            $(document).on('click', '.confirm-text', function() {
                if (!canDeleteCreditNoteItem) {
                    return;
                }

                let id = $(this).data('id');

                Swal.fire({
                    title: "Are you sure?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ff9f43"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/credit-note-items-api/delete/${id}`,
                            type: "POST",
                            headers: {
                                "Authorization": "Bearer " + localStorage.getItem(
                                    "authToken")
                            },
                            success: function(res) {
                                if (res.status) {
                                    Swal.fire("Deleted!", res.message, "success");
                                    fetchCreditNoteItems(currentPage);
                                }
                            }
                        });
                    }
                });
            });

            function positionCreditNoteActionMenu(dropdown) {
                const toggle = dropdown.querySelector('.action-menu-toggle');
                const menu = dropdown.querySelector('.credit-note-action-menu');
                if (!toggle || !menu) {
                    return;
                }

                const rect = toggle.getBoundingClientRect();
                const menuWidth = menu.offsetWidth || 140;
                const menuHeight = menu.offsetHeight || 120;
                const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
                const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

                let left = rect.right - menuWidth;
                let top = rect.bottom + 6;

                if (left < 8) {
                    left = rect.left;
                }

                if (left + menuWidth > viewportWidth - 8) {
                    left = viewportWidth - menuWidth - 8;
                }

                if (top + menuHeight > viewportHeight - 8) {
                    top = Math.max(8, rect.top - menuHeight - 6);
                }

                menu.classList.add('credit-note-action-menu-floating');
                menu.style.setProperty('left', `${Math.max(8, left)}px`, 'important');
                menu.style.setProperty('top', `${Math.max(8, top)}px`, 'important');
                menu.style.setProperty('right', 'auto', 'important');
                menu.style.setProperty('bottom', 'auto', 'important');
                menu.style.setProperty('transform', 'none', 'important');
            }

            $(document).on('shown.bs.dropdown', '.credit-note-action-dropdown', function() {
                positionCreditNoteActionMenu(this);
                requestAnimationFrame(() => positionCreditNoteActionMenu(this));
            });

            $(document).on('hidden.bs.dropdown', '.credit-note-action-dropdown', function() {
                const menu = this.querySelector('.credit-note-action-menu');
                if (!menu) {
                    return;
                }

                menu.classList.remove('credit-note-action-menu-floating');
                menu.removeAttribute('style');
            });

            $(window).on('scroll resize', function() {
                $('.credit-note-action-dropdown.show').each(function() {
                    positionCreditNoteActionMenu(this);
                });
            });
        });

        window.toggleCreditNoteRowDetails = function(id) {
            // Only work on mobile/tablet (≤767px)
            if ($(window).width() > 767) {
                return;
            }

            const row = $(`tr[data-item-id="${id}"]`);
            if (row.length === 0) return;

            const detailsRow = row.next(`tr.details-row[data-details-id="${id}"]`);
            const toggleBtn = row.find('.mobile-toggle-btn-table');

            if (detailsRow.length === 0) {
                // Create expandable row if it doesn't exist
                const data = window.creditNoteDataMap[id];
                if (!data) return;

                // Helper to format currency inside this function if needed
                const currencySymbol = "{{ $currencySymbol }}";
                const currencyPosition = "{{ $currencyPosition }}";

                function formatCurrencyLocal(amount) {
                    let formatted = parseFloat(amount || 0).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    return currencyPosition === 'left' ?
                        currencySymbol + formatted :
                        formatted + currencySymbol;
                }

                const content = `
                    <div class="details-content">
                        <div class="detail-item">
                            <span class="detail-label">${data.transaction_type === 'payment' ? 'Invoice Number:' : 'Order Number:'}</span>
                            <span class="detail-value">${data.transaction_type === 'payment' ? (data.purchase_invoice?.bill_no || 'N/A') : (data.order?.order_number || 'N/A')}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">${data.transaction_type === 'payment' ? 'Vendor Name:' : 'Customer Name:'}</span>
                            <span class="detail-value">${data.transaction_type === 'payment' ? (data.purchase_invoice?.vendor?.name || 'N/A') : (data.order?.user?.name || 'N/A')}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Type:</span>
                            <span class="detail-value">${data.credit_note?.type_name || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Total Amount:</span>
                            <span class="detail-value">${formatCurrencyLocal(data.total_amt)}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Settlement Amount:</span>
                            <span class="detail-value" style="color: #ff9f43; font-weight: 600;">${formatCurrencyLocal(data.settlement_amount)}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Action:</span>
                            <span class="detail-value">${buildActionLinks(data.id)}</span>
                        </div>
                    </div>
                `;

                const newRow = $(`
                    <tr class="details-row" data-details-id="${id}">
                        <td colspan="100%">${content}</td>
                    </tr>
                `);

                row.after(newRow);
                newRow.addClass('show');
                toggleBtn.text('-').addClass('minus');
            } else {
                // Toggle existing expandable row
                if (detailsRow.hasClass('show')) {
                    detailsRow.removeClass('show');
                    toggleBtn.text('+').removeClass('minus');
                } else {
                    detailsRow.addClass('show');
                    toggleBtn.text('-').addClass('minus');
                }
            }
        };
    </script>
@endpush
