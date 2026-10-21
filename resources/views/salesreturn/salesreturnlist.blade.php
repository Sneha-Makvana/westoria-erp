@extends('layout.app')

@section('title', 'Sales Return List')

@section('content')
    <style>
        .items-modal-table {
            font-size: 13px;
        }

        .items-modal-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .view-items-btn {
            background: #ff9f43;
            color: white;
            border: none;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .view-items-btn:hover {
            background: #ff8c2e;
            transform: scale(1.05);
        }

        .modal-header .btn-close {
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 1;
        }

        .modal-header .btn-close:hover {
            background: #c82333;
        }

        /* Hide default DataTables search box completely */
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

        .pagination .page-item.disabled .page-link {
            background-color: #fff !important;
            color: #dee2e6 !important;
            border: 1px solid #dee2e6 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }

        /* Action button styling */
        .icon-btn {
            background: #ff9f43;
            border-radius: 4px;
            padding: 4px 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .icon-btn:hover {
            background: #ff8c2e;
            transform: scale(1.05);
        }

        .icon-btn svg {
            width: 18px;
            height: 18px;
        }

        .icon-btn svg path {
            fill: white;
        }

        .vendor-action-dropdown {
            display: inline-flex;
            justify-content: center;
            width: 100%;
        }

        .vendor-action-dropdown .action-menu-toggle {
            align-items: center;
            background: #fff;
            border: 1px solid #d7dde8;
            border-radius: 4px;
            color: #1b2850;
            display: inline-flex;
            height: 28px;
            justify-content: center;
            padding: 0;
            width: 34px;
        }

        .vendor-action-dropdown .action-menu-toggle:hover,
        .vendor-action-dropdown .action-menu-toggle:focus {
            background: #f8fafc;
            border-color: #b8c2d2;
            color: #1b2850;
        }

        .vendor-action-menu {
            border: 1px solid #e8ebed;
            border-radius: 6px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
            min-width: 168px;
            padding: 6px;
            z-index: 999999999999;
        }

        .vendor-action-menu-floating {
            position: fixed !important;
            z-index: 999999999999 !important;
        }

        .vendor-action-menu .dropdown-item {
            align-items: center;
            border-radius: 4px;
            color: #344054;
            display: flex;
            font-size: 12px;
            gap: 8px;
            padding: 7px 8px;
            width: 100%;
        }

        .vendor-action-menu .dropdown-item i {
            color: #667085;
            font-size: 13px;
            text-align: center;
            width: 15px;
        }

        .vendor-action-menu .dropdown-item.text-danger,
        .vendor-action-menu .dropdown-item.text-danger i {
            color: #ea5455 !important;
        }

        .vendor-action-menu .dropdown-item:hover {
            background: #f4f6f8;
            color: #1b2850;
        }

        /* Mobile cards are rendered separately; the desktop table remains unchanged. */
        .mobile-order-card {
            display: none;
        }

        #itemsModal .table-responsive {
            display: block !important;
        }

        @media screen and (max-width: 1024.98px) {
            .content > .card > .card-body {
                background: #f5f6f8;
            }

            .table-responsive {
                display: none !important;
            }

            .mobile-order-card {
                display: grid;
                gap: 10px;
                margin-top: 0 !important;
            }

            .sr-mobile-card {
                min-width: 0;
                padding: 12px;
                border: 1px solid #e9ecf1;
                border-radius: 10px;
                background: #fff;
                box-shadow: 0 1px 3px rgba(16, 24, 40, 0.07);
            }

            .sr-mobile-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 10px;
            }

            .sr-mobile-return {
                min-width: 0;
                color: #1b2850;
                font-size: 13px;
                font-weight: 700;
                line-height: 1.35;
                overflow-wrap: anywhere;
            }

            .sr-mobile-return-label {
                margin-right: 3px;
                color: #8b93a7;
                font-size: 10px;
                font-weight: 600;
                letter-spacing: 0.04em;
                text-transform: uppercase;
            }

            .sr-mobile-total {
                flex: 0 0 auto;
                color: #1b2850;
                font-size: 14px;
                font-weight: 700;
                white-space: nowrap;
            }

            .sr-mobile-meta {
                display: flex;
                align-items: baseline;
                flex-wrap: wrap;
                gap: 4px 12px;
                margin-top: 5px;
                color: #667085;
                font-size: 11px;
                line-height: 1.4;
            }

            .sr-mobile-customer {
                min-width: 0;
                color: #475467;
                font-size: 12px;
                font-weight: 600;
                overflow-wrap: anywhere;
            }

            .sr-mobile-order-link {
                color: #e8872e;
                font-weight: 600;
                text-decoration: none;
            }

            .sr-mobile-order-link:focus,
            .sr-mobile-order-link:hover {
                color: #c96d17;
                text-decoration: underline;
            }

            .sr-mobile-summary {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 6px 14px;
                margin-top: 9px;
                padding-bottom: 9px;
                border-bottom: 1px dashed #eceef3;
            }

            .sr-mobile-summary-item {
                color: #8b93a7;
                font-size: 10px;
                line-height: 1.4;
            }

            .sr-mobile-summary-item strong {
                margin-left: 3px;
                color: #1b2850;
                font-size: 11px;
                font-weight: 700;
            }

            .sr-mobile-items {
                display: flex;
                align-items: baseline;
                gap: 6px;
                margin-top: 7px;
                color: #8b93a7;
                font-size: 10px;
                line-height: 1.4;
            }

            .sr-mobile-items-label {
                flex: 0 0 auto;
                font-weight: 600;
            }

            .sr-mobile-items-names {
                min-width: 0;
                color: #667085;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                overflow: hidden;
                overflow-wrap: anywhere;
            }

            .sr-mobile-stats {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr)) 36px;
                gap: 8px;
                align-items: center;
                padding-top: 8px;
            }

            .sr-mobile-stat {
                min-width: 0;
            }

            .sr-mobile-stat span {
                display: block;
                color: #8b93a7;
                font-size: 10px;
                line-height: 1.3;
            }

            .sr-mobile-stat strong {
                display: block;
                margin-top: 2px;
                color: #344054;
                font-size: 11px;
                font-weight: 600;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .sr-mobile-actions {
                display: flex;
                justify-content: flex-end;
                align-items: center;
            }

            .sr-mobile-actions .vendor-action-dropdown {
                width: auto;
            }

            .sr-mobile-actions .action-menu-toggle {
                width: 36px;
                height: 36px;
                border: 0;
                border-radius: 8px;
                background: #fff4e8;
                color: #d97719;
            }

            .sr-mobile-actions .action-menu-toggle:hover,
            .sr-mobile-actions .action-menu-toggle:focus {
                border: 0;
                background: #ffecd8;
                color: #b9600b;
                box-shadow: none;
            }

            .sr-mobile-actions .action-menu-toggle i {
                transform: rotate(90deg);
            }

            .sr-mobile-empty {
                padding: 18px 12px;
                border: 1px dashed #d7dde8;
                border-radius: 10px;
                background: #fff;
                color: #667085;
                font-size: 13px;
                text-align: center;
            }

            .pagination-controls {
                flex-wrap: wrap;
                gap: 10px;
            }

            .pagination .page-item .page-link {
                padding: 4px 10px;
                font-size: 12px;
            }
            .sr-mobile-meta + .sr-mobile-stats {
                margin-top: 9px;
                padding-top: 9px;
                border-top: 1px dashed #eceef3;
            }

            .sr-mobile-return .sr-mobile-summary-item {
                white-space: nowrap;
            }

            .sr-mobile-items-inline {
                min-width: 0;
                max-width: 100%;
                color: #667085;
                font-size: 10px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .sr-mobile-items-inline .sr-mobile-items-label {
                color: #8b93a7;
                font-weight: 600;
            }
            .sr-mobile-return {
                display: flex;
                align-items: baseline;
                flex-wrap: nowrap;
                gap: 8px;
                white-space: nowrap;
            }

            .sr-mobile-return .sr-mobile-summary-item {
                margin-left: 0 !important;
            }

            .sr-mobile-return .sr-mobile-return-label {
                margin-right: 0;
            }
            .sr-mobile-meta-qty {
                gap: 4px 14px;
            }

            .sr-mobile-meta-qty .sr-mobile-summary-item strong {
                margin-left: 4px;
            }
        }

        @media screen and (min-width: 1025px) {
            .mobile-order-card {
                display: none !important;
            }

            .table-responsive {
                display: block !important;
                overflow-x: auto;
            }
            #itemsModalBody tr td {
            white-space: normal !important;
            max-width: 100px;
        }
        }

        @media screen and (min-width: 768px) and (max-width: 1024.98px) {
            .mobile-order-card {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                align-items: start;
            }

            .mm-page .mm-head.d-md-none {
                display: flex !important;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-bottom: 8px;
                padding: 12px 10px;
                border-radius: 12px;
                background: #fff;
                box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
            }

            .mm-page > .page-header {
                display: none !important;
            }

            .mm-page > .card {
                margin: 0 !important;
                border: 0 !important;
                background: transparent !important;
                box-shadow: none !important;
            }

            .mm-page > .card > .card-body {
                padding: 0 !important;
                background: #f5f6f8;
            }

            .mm-page .mm-search {
                margin: 0 0 8px !important;
                padding: 8px 10px;
                border-radius: 12px;
                background: #fff;
                box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
            }

            .mm-page .mm-search .search-set,
            .mm-page .mm-search .search-input {
                width: 100% !important;
                margin: 0 !important;
            }

            .mm-page .mm-search .search-input {
                position: relative;
                display: flex;
                align-items: center;
            }

            .mm-page .mm-search .search-input input {
                width: 100% !important;
                height: 40px !important;
                padding-left: 36px !important;
                border-radius: 10px !important;
                font-size: 13px;
            }

            .mm-page .mm-search .btn-searchset {
                position: absolute;
                top: 50% !important;
                left: 10px;
                z-index: 2;
                padding: 0;
                transform: translateY(-50%);
            }

            .pagination-controls {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 10px;
            }

            .pagination-controls > .d-flex {
                margin-bottom: 0 !important;
            }

            .pagination-controls nav {
                align-self: center;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 850.98px) {
            .mm-page.content {
                max-width: 768px;
                margin: 0 auto;
                padding: 6px 10px 90px !important;
            }
        }

        #itemsModal .modal-body {
            max-height: 60vh;
            overflow-y: auto;
        }

        /* Ensure table visible */
        #itemsModal .table-responsive {
            overflow-x: auto;
            overflow-y: visible !important;
        }

        /* Prevent collapse */
        #itemsModalBody tr td {
            white-space: normal !important;
            max-width: 100px;
        }
        /* Word wrapping for customer name in desktop table */
        .table-responsive table td:nth-child(5),
        .table-responsive table th:nth-child(5) {
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal !important;
            max-width: 200px;
            min-width: 100px;
        }

        /* Ensure all other cells maintain normal behavior */
        .table-responsive table td {
            white-space: nowrap;
        }

        /* Only customer column should wrap */
        .table-responsive table td:nth-child(5) {
            white-space: normal !important;
        }
        @media screen and (min-width: 768px) and (max-width: 991px) {
            .page-wrapper {
                padding-top: 0 !important;
            }
        }
    </style>

    <div class="content mm-page">
        <div class="mm-head d-md-none">
    <div>
        <div class="mm-title">Sales Returns</div>
        <div class="mm-count" id="mmCount"></div>
    </div>
    @if (app('hasPermission')(2, 'add'))
        <a href="{{ route('salesreturn.add') }}" class="btn btn-sm mm-add"><i class="fas fa-plus me-1"></i> New Return</a>
    @endif
</div>
        <div class="page-header">
            <div class="page-title">
                <h4>Sales Return List</h4>
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(2, 'add'))
                <a href="{{ route('salesreturn.add') }}" class="btn btn-added">
                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img" class="me-2">Add
                    Sales Return
                </a>
            @endif

            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Search Section -->
                <div class="mb-3 mm-search">
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

                <!-- ========================
                             DESKTOP TABLE VIEW
                        ========================= -->
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order Number</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Return Qty</th>
                                <th>Subtotal</th>
                                <th>GST</th>
                                <th>Discount</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>

                <!-- ========================
                             MOBILE CARD VIEW
                             (same pattern as Sales List)
                        ========================= -->
                <div class="mobile-order-card mt-3" id="mobile-return-container">
                    <!-- JS will populate this -->
                </div>

                <!-- Pagination Controls (shared by both views) -->
                <div
                    class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
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
                            <!-- Page numbers populated by JS -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Modal -->
    <div class="modal fade" id="itemsModal" tabindex="-1" aria-labelledby="itemsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemsModalLabel">Return Items Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered items-modal-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Discount</th>
                                    <th>Subtotal</th>
                                    <th>GST</th>
                                </tr>
                            </thead>
                            <tbody id="itemsModalBody">
                                <!-- Items populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
@include('partials.master-mobile-ui')
    <script>
        $(document).ready(function() {
            const authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

            // Initialize DataTable
            const table = $('.datanew').DataTable({
                "destroy": true,
                "bFilter": false,
                "paging": false,
                "info": false,
                "searching": false,
                "dom": 't',
                "ordering": false
            });

            let currencySymbol = '₹';
            let currencyPosition = 'left';
            let allReturnData = [];

            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';

            function formatCurrency(amount) {
                const number = Number(amount || 0);
                const formattedValue = number.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                return currencyPosition === 'left' ?
                    `${currencySymbol}${formattedValue}` :
                    `${formattedValue}${currencySymbol}`;
            }

            function renderEmpty(message) {
                table.clear().draw();
                $(".datanew tbody").html(`<tr><td colspan="11" class="text-center">${message}</td></tr>`);
                $('#mobile-return-container').html(`<div class="sr-mobile-empty">${message}</div>`);
                $('.pagination-controls').hide();
            }

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // =============================================
            // Show Items Modal (global)
            // =============================================
            window.showItemsModal = function(returnId) {
                const returnData = allReturnData.find(item => item.id === returnId);

                if (!returnData || !returnData.items || returnData.items.length === 0) {
                    Swal.fire({
                        title: 'No Items',
                        text: 'No items found for this sales return.',
                        icon: 'info',
                        confirmButtonColor: '#ff9f43'
                    });
                    return;
                }

                let itemsHtml = '';
                returnData.items.forEach((item, index) => {
                    let gstText = 'N/A';
                    if (Array.isArray(item.gst_details) && item.gst_details.length > 0) {
                        gstText = item.gst_details.map(tax => {
                            return `${tax.tax_name}: ${tax.tax_rate}% (${formatCurrency(tax.tax_amount)})`;
                        }).join('<br>');
                    } else if (item.gst_details === 'inclusive') {
                        gstText = 'Inclusive';
                    }

                    itemsHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${escapeHtml(item.product_name)}</td>
                            <td>${parseFloat(item.quantity).toFixed(2)}</td>
                            <td>${formatCurrency(item.price)}</td>
                            <td>${formatCurrency(item.discount_amount)} (${item.discount}%)</td>
                            <td>${formatCurrency(item.subtotal)}</td>
                            <td>${gstText}</td>
                        </tr>
                    `;
                });

                $('#itemsModalBody').html(itemsHtml);

                setTimeout(() => {
                    const modal = new bootstrap.Modal(
                        document.getElementById('itemsModal')
                    );
                    modal.show();
                }, 50);
            }
            if (!authToken) {
                renderEmpty('Authentication token not found. Please login again.');
                return;
            }

            function buildReturnActionMenu(returnId) {
                let menuItems = '';
                @if (app('hasPermission')(2, 'add'))
                    menuItems += `<a class="dropdown-item" href="javascript:void(0);" onclick="window.showItemsModal(${returnId});">
                        <i class="fas fa-eye"></i><span>View Items</span>
                    </a>`;
                @endif
                    menuItems += `<a class="dropdown-item" href="/sales-return-pdf/${returnId}" target="_blank">
                        <i class="fas fa-file-pdf"></i><span>PDF</span>
                    </a>`;
                if (!menuItems) {
                    menuItems = '<span class="dropdown-item text-muted">No actions</span>';
                }

                return `
                    <div class="dropdown vendor-action-dropdown">
                        <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end vendor-action-menu">
                            ${menuItems}
                        </div>
                    </div>
                `;
            }

            // =============================================
            // Render Mobile Return Cards
            // =============================================
            function renderMobileReturnCards(data) {
                const container = $('#mobile-return-container');
                container.html('');

                if (!data || data.length === 0) {
                    container.html('<div class="sr-mobile-empty">No sales return records found.</div>');
                    return;
                }

                data.forEach(item => {
                    const discountAmount = formatCurrency(item.discount_amount);
                    const returnNumber = escapeHtml(item.return_number) || `Return #${escapeHtml(item.id)}`;
                    const orderLink = item.order_number ?
                        `<a href="/sales-details/${item.order_id}" class="sr-mobile-order-link">${escapeHtml(item.order_number)}</a>` :
                        '';
                    const customerName = escapeHtml(item.customer) || '-';
                    const returnedProductNames = Array.isArray(item.items) ?
                        item.items
                            .map(returnedItem => returnedItem && returnedItem.product_name)
                            .filter(name => name !== undefined && name !== null && String(name).trim() !== '')
                            .map(name => escapeHtml(String(name)))
                            .join(', ') :
                        '';
                    const orderNoHtml = item.order_number ?
                    `<a href="/sales-details/${item.order_id}" class="sr-mobile-order-link">${escapeHtml(item.order_number)}</a>` :
                    '-';

                const cardHtml = `
                    <article class="sr-mobile-card" data-return-id="${escapeHtml(item.id)}">
                        <div class="sr-mobile-top">
                            <div class="sr-mobile-return">
                                <span class="sr-mobile-return-label">Order:</span>${orderNoHtml}
                            </div>
                            <div class="sr-mobile-total">${formatCurrency(item.total_amount)}</div>
                        </div>
                         <div class="sr-mobile-meta">
                            <span class="sr-mobile-customer">${customerName}</span>
                            <span>${escapeHtml(item.date) || '-'}</span>
                            <span><span class="sr-mobile-return-label">Return</span><strong>${returnNumber}</strong></span>
                        </div>
                        <div class="sr-mobile-meta sr-mobile-meta-qty">
                            <span class="sr-mobile-summary-item">Products:<strong>${Number(item.items_count || 0)}</strong></span>
                            <span class="sr-mobile-summary-item">Return Qty:<strong>${parseFloat(item.return_qty || 0).toFixed(2)}</strong></span>
                            ${returnedProductNames ? `<span class="sr-mobile-items-inline"><span class="sr-mobile-items-label">Items:</span> ${returnedProductNames}</span>` : ''}
                        </div>
                        <div class="sr-mobile-stats">
                            <div class="sr-mobile-stat">
                                <span>Subtotal</span>
                                <strong>${formatCurrency(item.subtotal)}</strong>
                            </div>
                            <div class="sr-mobile-stat">
                                <span>GST</span>
                                <strong>${formatCurrency(item.tax_amount)}</strong>
                            </div>
                            <div class="sr-mobile-stat">
                                <span>Discount</span>
                                <strong>${discountAmount}</strong>
                            </div>
                            <div class="sr-mobile-actions">
                                ${buildReturnActionMenu(item.id)}
                            </div>
                        </div>
                    </article>
                `;
                    container.append(cardHtml);
                });
            }

            // =============================================
            // Fetch Sales Returns
            // =============================================
            function fetchSalesReturns(page = 1) {
                let url = `/api/sales-return-list?page=${page}&per_page=${perPage}`;

                if (selectedSubAdminId && selectedSubAdminId !== "null" && selectedSubAdminId !== "undefined") {
                    url += `&selectedSubAdminId=${encodeURIComponent(selectedSubAdminId)}`;
                }

                if (searchQuery) {
                    url += `&search=${encodeURIComponent(searchQuery)}`;
                }

                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (!response.status) {
                            renderEmpty(response.message || "No sales return records found.");
                            $('.pagination-controls').hide();
                            return;
                        }

                        currencySymbol = response.currency_symbol || currencySymbol;
                        currencyPosition = response.currency_position || currencyPosition;
                        allReturnData = response.data || [];

                        const pagination = response.pagination;
                        if (pagination) {
                            currentPage = pagination.current_page;
                            lastPage = pagination.last_page;
                            updatePaginationUI(pagination);
                            $('#mmCount').text(pagination.total + ' items');
                        }

                        // Desktop table rows
                        const rows = allReturnData.map((item, index) => {
                            const discount = Number(item.discount || 0).toFixed(2);
                            const discountAmount = formatCurrency(item.discount_amount);
                            const serialNumber = ((currentPage - 1) * perPage) + (index + 1);

                            return [
                                serialNumber,
                                item.order_number ?
                                `<a href="/sales-details/${item.order_id}" class="order-link">${item.order_number}</a>` :
                                "-",
                                item.date || "-",
                                item.customer || "-",
                                `<span>${Number(item.items_count || 0)}</span>`,
                                `<span>${parseFloat(item.return_qty || 0).toFixed(2)}</span>`,
                                formatCurrency(item.subtotal),
                                formatCurrency(item.tax_amount),
                                `${discountAmount}`,
                                `<strong class="text-success">${formatCurrency(item.total_amount)}</strong>`,
                                buildReturnActionMenu(item.id)
                            ];
                        });

                        table.clear();
                        if (rows.length > 0) {
                            table.rows.add(rows).draw();
                            $('.pagination-controls').show();
                        } else {
                            table.draw();
                            renderEmpty("No sales return records found.");
                            $('.pagination-controls').hide();
                        }

                        // Render mobile cards
                        renderMobileReturnCards(allReturnData);
                    },
                    error: function(xhr) {
                        console.error("Sales return list API error:", xhr);
                        renderEmpty("Unable to load sales return records.");
                        $('.pagination-controls').hide();
                    }
                });
            }

            // Update pagination UI
            function updatePaginationUI(pagination) {
                let from = (pagination.current_page - 1) * pagination.per_page + 1;
                let to = pagination.current_page * pagination.per_page;
                if (to > pagination.total) to = pagination.total;
                if (pagination.total === 0) from = 0;

                $('#pagination-from').text(from);
                $('#pagination-to').text(to);
                $('#pagination-total').text(pagination.total);
                {
                let paginationHtml = `
                    <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page - 1}">Previous</a>
                    </li>
                `;

                const visiblePageCount = 2;
                let startPage = Math.floor((pagination.current_page - 1) / visiblePageCount) * visiblePageCount + 1;
                let endPage = Math.min(pagination.last_page, startPage + visiblePageCount - 1);

                if (startPage > 1) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);" data-page="${startPage - 1}" data-action="prev-group">..</a>
                        </li>
                    `;
                }

                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                        </li>
                    `;
                }

                if (endPage < pagination.last_page) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);" data-page="${endPage + 1}" data-action="next-group">..</a>
                        </li>
                    `;
                }

                paginationHtml += `
                    <li class="page-item ${pagination.current_page === pagination.last_page || pagination.last_page === 0 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page + 1}">Next</a>
                    </li>
                `;

                $('#pagination-numbers').html(paginationHtml);
                $('.pagination-controls').toggle(pagination.total > 0);
                return;
                }

                let paginationHtml = '';

                if (pagination.current_page > 1) {
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page - 1}">«</a></li>`;
                }

                let startPage = Math.max(1, pagination.current_page - 2);
                let endPage = Math.min(pagination.last_page, startPage + 4);
                if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                        </li>
                    `;
                }

                if (pagination.current_page < pagination.last_page) {
                    paginationHtml +=
                        `<li class="page-item"><a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page + 1}">»</a></li>`;
                }

                $('#pagination-numbers').html(paginationHtml);
            }

            function positionVendorActionMenu(dropdownEl) {
                const $dropdown = $(dropdownEl);
                const $menu = $dropdown.data('floating-menu') || $dropdown.find('.vendor-action-menu');
                const button = $dropdown.find('.action-menu-toggle')[0];

                if (!$menu.length || !button) {
                    return;
                }

                const rect = button.getBoundingClientRect();
                const menuEl = $menu[0];
                const menuWidth = menuEl.offsetWidth || 168;
                const menuHeight = menuEl.offsetHeight || 220;
                const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
                const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
                const gap = 4;

                let left = rect.right - menuWidth;
                left = Math.max(8, Math.min(left, viewportWidth - menuWidth - 8));

                let top = rect.bottom + gap;
                if (top + menuHeight > viewportHeight - 8) {
                    top = Math.max(8, rect.top - menuHeight - gap);
                }

                $menu.css({
                    left: `${left}px`,
                    position: 'fixed',
                    top: `${top}px`,
                    transform: 'none'
                });
            }

            $(document).on('show.bs.dropdown', '.vendor-action-dropdown', function() {
                const $dropdown = $(this);
                const $menu = $dropdown.find('.vendor-action-menu');

                if (!$menu.length) {
                    return;
                }

                $dropdown.data('floating-menu', $menu);
                $dropdown.data('menu-parent', $menu.parent());
                $dropdown.data('menu-next', $menu.next());
                $menu.addClass('vendor-action-menu-floating').appendTo('body');
            });

            $(document).on('shown.bs.dropdown', '.vendor-action-dropdown', function() {
                positionVendorActionMenu(this);
            });

            $(document).on('hidden.bs.dropdown', '.vendor-action-dropdown', function() {
                const $dropdown = $(this);
                const $menu = $dropdown.data('floating-menu');
                const $parent = $dropdown.data('menu-parent');
                const $next = $dropdown.data('menu-next');

                if ($menu && $menu.length && $parent && $parent.length) {
                    $menu.removeClass('vendor-action-menu-floating').removeAttr('style');
                    if ($next && $next.length && $.contains($parent[0], $next[0])) {
                        $menu.insertBefore($next);
                    } else {
                        $parent.append($menu);
                    }
                }

                $dropdown.removeData('floating-menu menu-parent menu-next');
            });

            $(window).on('scroll resize', function() {
                $('.vendor-action-dropdown.show').each(function() {
                    positionVendorActionMenu(this);
                });
            });

            // Search
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                fetchSalesReturns(1);
            });

            // Pagination click
            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchSalesReturns(page);
                }
            });

            // Per-page change
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchSalesReturns(1);
            });

            // Initial fetch
            fetchSalesReturns(currentPage);
        });
    </script>
@endpush
