@extends('layout.app')

@section('title', 'Debit Note List')

@push('css')
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

    .table tbody tr td {
        /* white-space: normal !important; */
        word-break: break-word;
        word-wrap: break-word;
    }

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
        border-color: #ff9f43 !important;
        color: #fff !important;
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

    .table-container {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-container table thead th {
        white-space: nowrap;
    }

    .datatable {
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
        gap: 25px;
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

    .debit-note-mobile-ref {
        display: block;
        font-weight: 500;
    }

    .debit-note-mobile-party {
        display: none;
    }

    /* Responsive Table Styling */
    @media screen and (max-width: 767px) {
        .table-container {
            overflow-x: visible !important;
            -webkit-overflow-scrolling: touch !important;
        }

        .datatable {
            width: 100% !important;
            table-layout: auto !important;
        }

        .datatable thead th:nth-child(n+3),
        .datatable tbody td:nth-child(n+3) {
            display: none !important;
        }

        .datatable thead th:first-child,
        .datatable tbody td:first-child {
            display: table-cell !important;
            width: auto !important;
            word-wrap: break-word;
        }

        .datatable thead th.details-column,
        .datatable tbody td.details-control {
            display: table-cell !important;
            text-align: center;
            vertical-align: middle;
            width: 60px !important;
        }

        .debit-note-mobile-party {
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
        .datatable {
            min-width: 980px;
        }

        .datatable thead th.details-column,
        .datatable tbody td.details-control {
            display: none !important;
        }
    }

    @media (max-width: 575px) {
        .page-header .page-btn {
            margin-top: 0 !important;
        }
    }

    @media (max-width: 420px) {
        .page-actions {
            display: flex !important;
            flex-direction: row !important;
            justify-content: end !important;
            align-items: center !important;
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

        .page-actions > div {
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

    .datatable th:last-child,
    .datatable td:last-child {
        width: 120px !important;
        min-width: 120px;
        max-width: 120px;
        text-align: center;
        vertical-align: middle;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
    }

    .action-buttons img {
        width: 18px;
        height: 18px;
    }

    /* Dark Mode Overrides */
    :root[data-theme="dark"] .action-buttons img {
        filter: brightness(0) invert(1);
    }

    .total-settlement-box {
        background-color: #f8f9fa;
        color: #ff9f43;
        font-weight: bold;
        padding: 7px 10px;
        border: 1px solid #d1d1d1;
        border-radius: 8px;
    }
    
    :root[data-theme="dark"] .total-settlement-box {
        background-color: #0f172a !important;
        border-color: #334155 !important;
    }

    .total-settlement-label {
        color: #495057;
        margin-right: 8px;
        font-size: 14px;
    }

    :root[data-theme="dark"] .total-settlement-label {
        color: #d1d5db !important;
    }

    @media (max-width: 767px) {
        .datatable td:last-child {
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
        .debit-desktop-total {
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
    .debit-note-action-dropdown {
        position: relative;
    }

    .debit-note-action-dropdown .action-menu-toggle {
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

    .debit-note-action-dropdown .action-menu-toggle:hover,
    .debit-note-action-dropdown .action-menu-toggle:focus {
        background: #f1f5f9 !important;
        color: #1b2850 !important;
    }

    .debit-note-action-menu {
        border: 1px solid #e8ebed;
        border-radius: 8px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
        min-width: 140px;
        padding: 6px;
        z-index: 9999;
        background: #ffffff;
    }

    .debit-note-action-menu-floating {
        position: fixed !important;
        z-index: 999999 !important;
    }

    .debit-note-action-menu .dropdown-item {
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

    .debit-note-action-menu .dropdown-item i {
        color: #667085;
        font-size: 14px;
        text-align: center;
        width: 16px;
    }

    .debit-note-action-menu .dropdown-item.text-danger,
    .debit-note-action-menu .dropdown-item.text-danger i {
        color: #ea5455 !important;
    }

    .debit-note-action-menu .dropdown-item:hover {
        background: #f4f6f8;
        color: #1b2850;
    }

    .debit-note-action-menu .dropdown-item.text-danger:hover {
        background: #fef2f2;
        color: #dc2626 !important;
    }

    :root[data-theme="dark"] .debit-note-action-dropdown .action-menu-toggle {
        color: #94a3b8 !important;
    }

    :root[data-theme="dark"] .debit-note-action-dropdown .action-menu-toggle:hover,
    :root[data-theme="dark"] .debit-note-action-dropdown .action-menu-toggle:focus {
        background: #334155 !important;
        color: #f1f5f9 !important;
    }

    :root[data-theme="dark"] .debit-note-action-menu {
        background: #1e293b;
        border-color: #334155;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
    }

    :root[data-theme="dark"] .debit-note-action-menu .dropdown-item {
        color: #e2e8f0;
    }

    :root[data-theme="dark"] .debit-note-action-menu .dropdown-item i {
        color: #94a3b8;
    }

    :root[data-theme="dark"] .debit-note-action-menu .dropdown-item:hover {
        background: #334155;
        color: #fff;
    }

    :root[data-theme="dark"] .debit-note-action-menu .dropdown-item.text-danger:hover {
        background: rgba(234, 84, 85, 0.15);
    }
</style>
@endpush

@section('content')
<div class="content mm-page">
    <div class="mm-head d-md-none">
        <div>
            <div class="mm-title">Debit Note List</div>
            <div class="mm-count" id="mmCount"></div>
        </div>
        @if (app('hasPermission')(27, 'add'))
            <a href="{{ route('debit-notes-items.create') }}" class="btn btn-sm mm-add">
                <i class="fas fa-plus me-1"></i> New Item
            </a>
        @endif
    </div>

    <div class="page-header">
        <div class="page-title">
            <h4>Debit Note List</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap page-actions">

            <div class="page-btn">
                @if (app('hasPermission')(27, 'add'))
                    <a href="{{ route('debit-notes-items.create') }}" class="btn btn-added">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img"
                            class="me-1">Add <span class="d-none d-sm-inline">Debit Note Item</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="mm-settlement-card d-md-none mb-2">
        <span class="mm-settlement-label">Total Settlement:</span>
        <span class="mm-settlement-val">₹<span id="total_settlement_amount_mobile">0.00</span></span>
    </div>

    <div class="d-none d-md-flex justify-content-end debit-desktop-total">
        <div class="d-flex align-items-center credit-mobile mb-2 total-settlement-box">
            <span class="total-settlement-label">Total Settlement Amount:</span>
            <span style="font-size: 16px;">₹<span id="total_settlement_amount">0.00</span></span>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-2 mm-search">
                <div class="search-set d-flex justify-content-md-start justify-content-start w-100">
                    <div class="search-path"></div>
                    <div class="search-input">
                        <a class="btn btn-searchset">
                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img">
                        </a>
                        <input type="text" id="debit-note-search-input" class="form-control" placeholder="SEARCH BY NAME, PHONE, ORDER NO...">
                    </div>
                </div>
            </div>
            <div class="table-container table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Order / Bill Number</th>
                            <th class="details-column">Details</th>
                            <th>Customer / Vendor Name</th>
                            <th>Type</th>
                            <th>Total Amount</th>
                            <th>Settlement Amount</th>
                            <th>Final Total</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody id="debit_note_items_table_body">
                        {{-- Data will be loaded via AJAX --}}
                    </tbody>
                </table>
            </div>

            <div class="mm-card-list d-md-none" id="mmCardList"></div>

            <div
                class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
                    <select id="debit-note-per-page-select" class="form-select form-select-sm"
                        style="width: auto; border: 1px solid #ddd;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="ms-3" style="font-size: 14px; color: #555;">
                        <span id="debit-note-pagination-from">0</span> - <span id="debit-note-pagination-to">0</span> of
                        <span id="debit-note-pagination-total">0</span> items
                    </span>
                </div>
                <nav aria-label="Debit note pagination">
                    <ul class="pagination pagination-sm mb-0" id="debit-note-pagination-numbers"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
@include('partials.master-mobile-ui')
<script>
$(document).ready(function () {
    var authToken = localStorage.getItem("authToken");
    var selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
    const canViewDebitNoteItem = @json(app('hasPermission')(27, 'view'));
    const canEditDebitNoteItem = @json(app('hasPermission')(27, 'edit'));
    const canDeleteDebitNoteItem = @json(app('hasPermission')(27, 'delete'));
    let currentPage = 1;
    let lastPage = 1;
    let perPage = 10;
    let searchQuery = '';
    let currencySymbol = "{{ $currencySymbol ?? '₹' }}";
    let currencyPosition = "{{ $currencyPosition ?? 'left' }}";

    // Global data map for expandable rows
    window.debitNoteDataMap = {};

    function formatCurrency(amount) {
        let formatted = parseFloat(amount || 0).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return currencyPosition === 'left' ? currencySymbol + formatted : formatted + currencySymbol;
    }

    function buildDebitNoteActionLinks(itemId) {
        let actions = '';

        if (canViewDebitNoteItem) {
            actions += `
                <a href="{{ url('debit-note-items/view') }}/${itemId}">
                    <img src="{{ env('ImagePath') . ('admin/assets/img/icons/eye.svg') }}">
                </a>
            `;
        }

        if (canEditDebitNoteItem) {
            actions += `
                <a href="{{ url('debit-note-items/edit') }}/${itemId}">
                    <img src="{{ env('ImagePath') . ('admin/assets/img/icons/edit.svg') }}">
                </a>
            `;
        }

        if (canDeleteDebitNoteItem) {
            actions += `
                <a class="confirm-text delete-item" data-id="${itemId}">
                    <img src="{{ env('ImagePath') . ('admin/assets/img/icons/delete.svg') }}">
                </a>
            `;
        }

        return actions || '<span class="text-muted">-</span>';
    }

    function buildMobileDebitNoteActionLinks(itemId) {
        let items = '';

        if (canViewDebitNoteItem) {
            items += `
                <a class="dropdown-item" href="{{ url('debit-note-items/view') }}/${itemId}">
                    <i class="fas fa-eye"></i> View
                </a>
            `;
        }

        if (canEditDebitNoteItem) {
            items += `
                <a class="dropdown-item" href="{{ url('debit-note-items/edit') }}/${itemId}">
                    <i class="fas fa-edit"></i> Edit
                </a>
            `;
        }

        if (canDeleteDebitNoteItem) {
            items += `
                <a class="dropdown-item text-danger confirm-text delete-item" data-id="${itemId}" href="javascript:void(0);">
                    <i class="fas fa-trash"></i> Delete
                </a>
            `;
        }

        if (!items.trim()) {
            return '';
        }

        return `
            <div class="dropdown debit-note-action-dropdown">
                <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end debit-note-action-menu">
                    ${items}
                </div>
            </div>
        `;
    }

    if ($.fn.DataTable.isDataTable('.datatable')) {
        $('.datatable').DataTable().destroy();
    }

    let table = $('.datatable').DataTable({
        bFilter: false,
        destroy: true,
        paging: false,
        info: false,
        searching: false,
        dom: 't',
        ordering: true,
        order: [],
        language: {
            emptyTable: "No debit note items found",
        },
        columnDefs: [
            { targets: 1, className: 'details-control', orderable: false }
        ]
    });

    $('#debit-note-search-input').on('keyup', function() {
        searchQuery = $(this).val();
        loadDebitNoteItems(1);
    });

    $('#debit-note-per-page-select').on('change', function() {
        perPage = $(this).val();
        loadDebitNoteItems(1);
    });

    /** ----------------------------------------------
     * 📱 Mobile Expandable Row Functions
     * ---------------------------------------------- */
    function buildDebitNoteExpandableRowContent(item) {
        let displayName = 'N/A';
        if (item.transaction_type === 'receipt') {
            displayName = (item.order && item.order.user) ? item.order.user.name : 'N/A';
        } else {
            displayName = (item.purchase_invoice && item.purchase_invoice.vendor)
                ? item.purchase_invoice.vendor.name
                : 'N/A';
        }

        let typeName = item.credit_note_type ? item.credit_note_type.type_name : 'N/A';

        return `
            <div class="details-content">
                <div class="detail-item">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value">${displayName}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Type:</span>
                    <span class="detail-value">${typeName}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value">${formatCurrency(item.grand_total)}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Settlement Amount:</span>
                    <span class="detail-value" style="color: #ff9f43; font-weight: 600;">${formatCurrency(item.settlement_amount)}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Final Total:</span>
                    <span class="detail-value" style="color: #28a745; font-weight: 600;">${formatCurrency(item.total)}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Action:</span>
                    <span class="detail-value">${buildDebitNoteActionLinks(item.id)}</span>
                </div>
            </div>
        `;
    }

    window.toggleDebitNoteRowDetails = function(itemId) {
        // Only work on mobile/tablet (≤767px)
        if ($(window).width() > 767) {
            return;
        }

        const row = $(`tr[data-item-id="${itemId}"]`);
        if (row.length === 0) return;

        const detailsRow = row.next(`tr.details-row[data-details-id="${itemId}"]`);
        const toggleBtn = row.find('.mobile-toggle-btn-table');

        if (detailsRow.length === 0) {
            // Create expandable row if it doesn't exist
            const item = window.debitNoteDataMap[itemId];
            if (!item) return;

            const expandableContent = buildDebitNoteExpandableRowContent(item);
            const newRow = $(`
                <tr class="details-row" data-details-id="${itemId}">
                    <td colspan="100%">${expandableContent}</td>
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

    function loadDebitNoteItems(page = 1) {
        let url = "{{ url('api/debit-note-items') }}" + `?page=${page}&per_page=${perPage}`;
        if (selectedSubAdminId && selectedSubAdminId !== 'null') {
            url += `&selectedSubAdminId=${encodeURIComponent(selectedSubAdminId)}`;
        }
        if (searchQuery) {
            url += `&search=${encodeURIComponent(searchQuery)}`;
        }

        $.ajax({
            url: url,
            type: "GET",
            headers: {
                "Authorization": "Bearer " + authToken
            },
            success: function (response) {
                if (response.status === 'success') {
                    table.clear();
                    currentPage = response.pagination?.current_page || 1;
                    lastPage = response.pagination?.last_page || 1;

                    // Clear and update data map
                    window.debitNoteDataMap = {};

                    let mmCards = [];

                    response.data.forEach(function (item, index) {
                        // Store item in map for expansion
                        window.debitNoteDataMap[item.id] = item;

                        let displayName = 'N/A';
                        let transactionNumber = 'N/A';

                        if (item.transaction_type === 'receipt') {
                            displayName = (item.order && item.order.user) ? item.order.user.name : 'N/A';
                            transactionNumber = item.order ? item.order.order_number : 'N/A';
                        } else {
                            displayName = (item.purchase_invoice && item.purchase_invoice.vendor)
                                ? item.purchase_invoice.vendor.name
                                : 'N/A';
                            transactionNumber = item.purchase_invoice ? (item.purchase_invoice.bill_no || item.purchase_invoice.invoice_number) : 'N/A';
                        }

                        let typeName = item.credit_note_type
                            ? item.credit_note_type.type_name
                            : 'N/A';

                        let rowNode = table.row.add([
                            `
                            <span class="debit-note-mobile-party">${displayName}</span>
                                <span class="debit-note-mobile-ref">${transactionNumber}</span>
                            `,
                            `<button class="mobile-toggle-btn-table" onclick="toggleDebitNoteRowDetails('${item.id}')">+</button>`,
                            displayName,
                            typeName,
                            formatCurrency(item.grand_total),
                            formatCurrency(item.settlement_amount),
                            formatCurrency(item.total),
                            `<div class="action-buttons">${buildDebitNoteActionLinks(item.id)}</div>`
                        ]).node();

                        $(rowNode).attr('data-item-id', item.id);

                        let cardTitle = (displayName && displayName !== 'N/A') ? displayName : transactionNumber;
                        let cardSub = (displayName && displayName !== 'N/A' && transactionNumber !== 'N/A')
                            ? (transactionNumber + (typeName && typeName !== 'N/A' ? ' • ' + typeName : ''))
                            : (typeName && typeName !== 'N/A' ? typeName : '');

                        mmCards.push(MasterMobileUI.card({
                            title: cardTitle,
                            sub: cardSub,
                            actions: buildMobileDebitNoteActionLinks(item.id),
                            stats: [
                                { label: 'Total Amount', value: formatCurrency(item.grand_total) },
                                { label: 'Settlement Amount', html: `<span style="color:#ff9f43;font-weight:700;">${formatCurrency(item.settlement_amount)}</span>` },
                                { label: 'Final Total', html: `<span style="color:#28a745;font-weight:700;">${formatCurrency(item.total)}</span>` }
                            ]
                        }));
                    });

                    updatePaginationUI(response.pagination || {
                        current_page: 1,
                        last_page: 1,
                        per_page: perPage,
                        total: response.data.length
                    });

                    $('#total_settlement_amount, #total_settlement_amount_mobile').text(parseFloat(response.total_settlement || 0).toFixed(2));
                    table.draw();
                    MasterMobileUI.render(mmCards, 'No debit note items found');
                } else {
                    MasterMobileUI.render([], 'No debit note items found');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                MasterMobileUI.render([], 'Error loading debit note items');
            }
        });
    }

    function updatePaginationUI(pagination) {
        let from = (pagination.current_page - 1) * pagination.per_page + 1;
        let to = pagination.current_page * pagination.per_page;

        if (to > pagination.total) {
            to = pagination.total;
        }

        if (pagination.total === 0) {
            from = 0;
        }

        $('#debit-note-pagination-from').text(from);
        $('#debit-note-pagination-to').text(to);
        $('#debit-note-pagination-total, #pagination-total').text(pagination.total);
        $('#mmCount').text(pagination.total ? pagination.total + (pagination.total === 1 ? ' item' : ' items') : '');

        let paginationHtml = '';

        // Previous Button
        paginationHtml += `
            <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link debit-note-page-link" href="javascript:void(0);" data-page="${pagination.current_page - 1}">Previous</a>
            </li>
        `;

        // Show only 2 page numbers at a time as in saleslist
        const visiblePageCount = 2;
        let startPage = Math.floor((pagination.current_page - 1) / visiblePageCount) * visiblePageCount + 1;
        let endPage = Math.min(pagination.last_page, startPage + visiblePageCount - 1);

        // Show previous ellipsis if there are pages before startPage
        if (startPage > 1) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link debit-note-page-link" href="javascript:void(0);" data-page="${startPage - 1}" data-action="prev-group">..</a>
                </li>
            `;
        }

        // Generate page numbers
        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `
                <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link debit-note-page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                </li>
            `;
        }

        // Show next ellipsis if there are more pages after endPage
        if (endPage < pagination.last_page) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link debit-note-page-link" href="javascript:void(0);" data-page="${endPage + 1}" data-action="next-group">..</a>
                </li>
            `;
        }

        // Next Button
        paginationHtml += `
            <li class="page-item ${pagination.current_page === pagination.last_page || pagination.last_page === 0 ? 'disabled' : ''}">
                <a class="page-link debit-note-page-link" href="javascript:void(0);" data-page="${pagination.current_page + 1}">Next</a>
            </li>
        `;

        $('#debit-note-pagination-numbers').html(paginationHtml);
        $('.pagination-controls').toggle(pagination.total > 0);
    }

    $(document).on('click', '.debit-note-page-link', function(e) {
        e.preventDefault();
        let page = $(this).data('page');
        let action = $(this).data('action');

        // Handle ellipsis clicks to load next/previous groups
        if (action === 'next-group') {
            if (page && page <= lastPage) {
                loadDebitNoteItems(page);
            }
            return;
        }

        if (action === 'prev-group') {
            let prevStartPage = Math.max(1, page - 2);
            if (prevStartPage >= 1 && prevStartPage <= lastPage) {
                loadDebitNoteItems(prevStartPage);
            }
            return;
        }

        if (page && page !== currentPage && page >= 1 && page <= lastPage) {
            loadDebitNoteItems(page);
        }
    });

    loadDebitNoteItems(currentPage);

    $(document).on('click', '.delete-item', function() {
        if (!canDeleteDebitNoteItem) return;

        let id = $(this).data('id');
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
                    url: `/api/debit-note-items/delete/${id}`,
                    type: "POST",
                    headers: { "Authorization": "Bearer " + authToken },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire("Deleted!", response.message, "success");
                            if (currentPage > 1 && table.rows().count() === 1) {
                                currentPage--;
                            }
                            loadDebitNoteItems(currentPage);
                        }
                    }
                });
            }
        });
    });

    function positionDebitNoteActionMenu(dropdown) {
        const toggle = dropdown.querySelector('.action-menu-toggle');
        const menu = dropdown.querySelector('.debit-note-action-menu');
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

        menu.classList.add('debit-note-action-menu-floating');
        menu.style.setProperty('left', `${Math.max(8, left)}px`, 'important');
        menu.style.setProperty('top', `${Math.max(8, top)}px`, 'important');
        menu.style.setProperty('right', 'auto', 'important');
        menu.style.setProperty('bottom', 'auto', 'important');
        menu.style.setProperty('transform', 'none', 'important');
    }

    $(document).on('shown.bs.dropdown', '.debit-note-action-dropdown', function() {
        positionDebitNoteActionMenu(this);
        requestAnimationFrame(() => positionDebitNoteActionMenu(this));
    });

    $(document).on('hidden.bs.dropdown', '.debit-note-action-dropdown', function() {
        const menu = this.querySelector('.debit-note-action-menu');
        if (!menu) {
            return;
        }

        menu.classList.remove('debit-note-action-menu-floating');
        menu.removeAttribute('style');
    });

    $(window).on('scroll resize', function() {
        $('.debit-note-action-dropdown.show').each(function() {
            positionDebitNoteActionMenu(this);
        });
    });
});
</script>
@endpush
