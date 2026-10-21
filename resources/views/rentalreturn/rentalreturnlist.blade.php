@extends('layout.app')

@section('title', 'Rental Return List')

@section('content')
    <style>
        .items-modal-table { font-size: 13px; }
        .items-modal-table th { background-color: #f8f9fa; font-weight: 600; }

        .dataTables_filter, .dataTables_length, .dataTables_info, .dataTables_paginate { display: none !important; }
        .dataTables_wrapper .row:first-child { display: none !important; }
        .dataTables_wrapper { margin-top: 0 !important; padding-top: 0 !important; }

        .search-input input { padding-left: 35px !important; border-radius: 5px; }
        .search-input { position: relative; display: flex; align-items: center; }
        .btn-searchset { position: absolute; left: 10px; z-index: 10; padding: 0; top: 7px !important; }

        .pagination .page-item .page-link {
            background-color: #5d6d7e; color: #fff; border: none;
            margin: 0 3px; padding: 4px 10px; border-radius: 6px; font-weight: bold;
        }
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            background-color: #fff; color: #6c757d; border: 1px solid #dee2e6;
        }
        .pagination .page-item.active .page-link { background-color: #ff9f43 !important; color: #fff; }
        .pagination .page-item .page-link:hover { background-color: #4a5766; color: #fff; }
        .pagination .page-item.active .page-link:hover { background-color: #e68a35 !important; }
        .pagination .page-item.disabled .page-link {
            background-color: #fff !important; color: #dee2e6 !important;
            border: 1px solid #dee2e6 !important; cursor: not-allowed !important; pointer-events: none !important;
        }

        .vendor-action-dropdown {
            display: inline-flex; justify-content: center; width: 100%;
        }
        .vendor-action-dropdown .action-menu-toggle {
            align-items: center; background: #fff; border: 1px solid #d7dde8;
            border-radius: 4px; color: #1b2850; display: inline-flex;
            height: 28px; justify-content: center; padding: 0; width: 34px;
        }
        .vendor-action-dropdown .action-menu-toggle:hover { background: #f8fafc; border-color: #b8c2d2; }
        .vendor-action-menu {
            border: 1px solid #e8ebed; border-radius: 6px;
            box-shadow: 0 8px 20px rgba(15,23,42,.12); min-width: 168px; padding: 6px; z-index: 999999999999;
        }
        .vendor-action-menu-floating { position: fixed !important; z-index: 999999999999 !important; }
        .vendor-action-menu .dropdown-item {
            align-items: center; border-radius: 4px; color: #344054;
            display: flex; font-size: 12px; gap: 8px; padding: 7px 8px; width: 100%;
        }
        .vendor-action-menu .dropdown-item i { color: #667085; font-size: 13px; width: 15px; }
        .vendor-action-menu .dropdown-item:hover { background: #f4f6f8; color: #1b2850; }

        /* Badge for full/partial return */
        .badge-full-return  { background: #d4edda; color: #155724; font-size: 11px; padding: 3px 8px; border-radius: 4px; }
        .badge-partial-return { background: #fff3cd; color: #856404; font-size: 11px; padding: 3px 8px; border-radius: 4px; }

        /* Mobile cards */
        .mobile-order-card { display: none; }
        .mobile-order-header-row {
            display: table; width: 100%; table-layout: fixed;
            background: #f8f9fa; border-radius: 8px 8px 0 0;
            padding: 12px 15px; margin-bottom: 10px; font-weight: bold;
            color: #1b2850; border-bottom: 2px solid #e0e0e0;
        }
        .mobile-order-header-cell { display: table-cell; vertical-align: middle; }
        .mobile-order-header-cell:first-child { width: 70%; }
        .mobile-order-header-cell:last-child { width: 30%; text-align: center; }
        .mobile-order-row { display: table; width: 100%; table-layout: fixed; margin-bottom: 10px; }
        .mobile-order-cell { display: table-cell; vertical-align: middle; border-bottom: 1px solid #f0f0f0; }
        .mobile-order-number { font-size: 16px; color: #1b2850; width: 70%; padding: 15px; }
        .mobile-order-ref { display: block; font-weight: 600; }
        .mobile-order-party {
            display: block; margin-top: 4px; font-size: 16px; line-height: 1.4;
            color: #495057; white-space: normal; word-break: break-word; overflow-wrap: anywhere;
        }
        .mobile-order-details-cell { text-align: center; width: 30%; }
        .mobile-toggle-btn {
            background: #ff9f43; border: none; border-radius: 50%; width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: white; font-size: 20px; font-weight: bold; transition: all .3s; margin: 0 auto;
        }
        .mobile-toggle-btn:hover { background: #ff8c2e; }
        .mobile-toggle-btn.minus { background: #dc3545; }
        .mobile-order-details { display: none; margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0; }
        .mobile-order-details.active { display: block; }
        .mobile-detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
        .mobile-detail-row:last-child { border-bottom: none; }
        .mobile-detail-label { font-weight: 600; color: #595b5d; font-size: 14px; }
        .mobile-detail-value { color: #1b2850; font-size: 14px; text-align: right; }
        .mobile-action-buttons { 
            display: flex; gap: 8px; flex-wrap: nowrap; align-items: center; justify-content: center; 
            margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0; 
            overflow-x: auto; -webkit-overflow-scrolling: touch;
        }
        .btn-icon-mobile {
            width: 32px; height: 32px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            text-decoration: none; color: #fff; font-size: 14px;
        }
        .btn-icon-mobile.btn-view { background-color: #637381; }
        .btn-icon-mobile.btn-print { background-color: #00bcd4; }
        .btn-icon-mobile:hover { filter: brightness(90%); color: #fff; }

        @media screen and (max-width: 1024.98px) {
            .table-responsive { display: none !important; }
            .mobile-order-card { display: block; }
        }
        @media screen and (min-width: 1025px) {
            .mobile-order-card { display: none !important; }
            .table-responsive { display: block !important; overflow-x: auto; }
        }
        #itemsModal .modal-body { max-height: 60vh; overflow-y: auto; }
        #itemsModal .table-responsive { overflow-x: auto; overflow-y: visible !important; display: block !important; }
        #itemsModalBody tr td { white-space: normal !important; max-width: 150px; }
    @media screen and (max-width: 1024.98px) {
    .content > .card > .card-body { background: #f5f6f8; }
    .table-responsive { display: none !important; }

    .mobile-order-card { display: grid; gap: 10px; margin-top: 0 !important; }

    .sr-mobile-card {
        min-width: 0; padding: 12px; border: 1px solid #e9ecf1;
        border-radius: 10px; background: #fff; box-shadow: 0 1px 3px rgba(16,24,40,.07);
    }
    .sr-mobile-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
    .sr-mobile-return { min-width: 0; color: #1b2850; font-size: 13px; font-weight: 700; line-height: 1.35; overflow-wrap: anywhere; }
    .sr-mobile-return-label {
        margin-right: 3px; color: #8b93a7; font-size: 10px; font-weight: 600;
        letter-spacing: .04em; text-transform: uppercase;
    }
    .sr-mobile-return .sr-mobile-summary-item { white-space: nowrap; }
    .sr-mobile-total { flex: 0 0 auto; color: #1b2850; font-size: 14px; font-weight: 700; white-space: nowrap; }

    .sr-mobile-meta {
        display: flex; align-items: baseline; flex-wrap: wrap; gap: 4px 12px;
        margin-top: 5px; color: #667085; font-size: 11px; line-height: 1.4;
    }
    .sr-mobile-customer { min-width: 0; color: #475467; font-size: 12px; font-weight: 600; overflow-wrap: anywhere; }
    .sr-mobile-order-link { color: #e8872e; font-weight: 600; text-decoration: none; }

    .sr-mobile-summary-item { color: #8b93a7; font-size: 10px; line-height: 1.4; }
    .sr-mobile-summary-item strong { margin-left: 3px; color: #1b2850; font-size: 11px; font-weight: 700; }

    .sr-mobile-items-inline {
        min-width: 0; max-width: 100%; color: #667085; font-size: 10px;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .sr-mobile-items-label { font-weight: 600; color: #8b93a7; }

    .sr-mobile-meta + .sr-mobile-stats {
        margin-top: 9px; padding-top: 9px; border-top: 1px dashed #eceef3;
    }
    .sr-mobile-stats {
        display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)) 36px;
        gap: 8px; align-items: center; padding-top: 8px;
    }
    .sr-mobile-stat { min-width: 0; }
    .sr-mobile-stat span { display: block; color: #8b93a7; font-size: 10px; line-height: 1.3; }
    .sr-mobile-stat strong {
        display: block; margin-top: 2px; color: #344054; font-size: 11px; font-weight: 600;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }

    .sr-mobile-actions { display: flex; justify-content: flex-end; align-items: center; }
    .sr-mobile-actions .vendor-action-dropdown { width: auto; }
    .sr-mobile-actions .action-menu-toggle {
        width: 36px; height: 36px; border: 0; border-radius: 8px;
        background: #fff4e8; color: #d97719;
    }
    .sr-mobile-actions .action-menu-toggle:hover,
    .sr-mobile-actions .action-menu-toggle:focus {
        border: 0; background: #ffecd8; color: #b9600b; box-shadow: none;
    }
    .sr-mobile-actions .action-menu-toggle i { transform: rotate(90deg); }

    .sr-mobile-empty {
        padding: 18px 12px; border: 1px dashed #d7dde8; border-radius: 10px;
        background: #fff; color: #667085; font-size: 13px; text-align: center;
    }

    .pagination-controls { flex-wrap: wrap; gap: 10px; }
    .pagination .page-item .page-link { padding: 4px 10px; font-size: 12px; }
}

@media screen and (min-width: 768px) and (max-width: 1024.98px) {
    .mobile-order-card {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        align-items: start;
    }

    .sr-mobile-stat strong .badge-full-return,
    .sr-mobile-stat strong .badge-partial-return {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        max-width: 100%;
        padding: 2px 8px;
        font-size: 10px;
        line-height: 1.2;
        white-space: nowrap;
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
        box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
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
        box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
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

    </style>

    <div class="content mm-page">
    <div class="mm-head d-md-none">
            <div>
                <div class="mm-title">Rental Returns</div>
                <div class="mm-count" id="mmCount"></div>
            </div>
            <a href="{{ route('rentalreturn.add') }}" class="btn btn-sm mm-add"><i class="fas fa-plus me-1"></i> New Return</a>
        </div>
        <div class="page-header">
            <div class="page-title">
                <h4>Rental Return List</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('rentalreturn.add') }}" class="btn btn-added">
                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img" class="me-2">
                    Add Rental Return
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Search -->
                <div class="mb-3 mm-search">
                    <div class="search-set d-flex justify-content-md-start justify-content-start w-100">
                        <div class="search-input">
                            <a class="btn btn-searchset">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img">
                            </a>
                            <input type="text" id="search-input" class="form-control"
                                placeholder="Search by customer, order no…">
                        </div>
                    </div>
                </div>

                <!-- Desktop Table -->
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order Number</th>
                                <th>Return Date</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total Qty Returned</th>
                                <th>Settlement Amount</th>
                                <th>Payment Method</th>
                                <th>Type</th>
                                <th>Deposit Settled</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="mobile-order-card mt-3" id="mobile-return-container"></div>

                <!-- Pagination -->
                <div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="me-2" style="font-size:14px;color:#555;">Show per page :</span>
                        <select id="per-page-select" class="form-select form-select-sm" style="width:auto;border:1px solid #ddd;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="ms-3" style="font-size:14px;color:#555;">
                            <span id="pagination-from">0</span> - <span id="pagination-to">0</span>
                            of <span id="pagination-total">0</span> items
                        </span>
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="pagination-numbers"></ul>
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
                    <h5 class="modal-title" id="itemsModalLabel">Returned Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered items-modal-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Qty Returned</th>
                                    <th>Settlement Amount</th>
                                </tr>
                            </thead>
                            <tbody id="itemsModalBody"></tbody>
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
$(document).ready(function () {
    const authToken           = localStorage.getItem('authToken');
    const selectedSubAdminId  = localStorage.getItem('selectedSubAdminId');

    const table = $('.datanew').DataTable({
        destroy: true, bFilter: false, paging: false,
        info: false, searching: false, dom: 't', ordering: false
    });

    let currencySymbol   = '₹';
    let currencyPosition = 'left';
    let allReturnData    = [];
    let currentPage      = 1;
    let lastPage         = 1;
    let perPage          = 10;
    let searchQuery      = '';

    function formatCurrency(amount) {
        const num = Number(amount || 0);
        const formatted = num.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return currencyPosition === 'left' ? `${currencySymbol}${formatted}` : `${formatted}${currencySymbol}`;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    function renderEmpty(msg) {
        table.clear().draw();
        $('.datanew tbody').html(`<tr><td colspan="11" class="text-center">${msg}</td></tr>`);
        $('#mobile-return-container').html(`<div class="sr-mobile-empty">${msg}</div>`);
        $('.pagination-controls').hide();
    }

    /* ── View Items Modal ── */
    window.showItemsModal = function (returnId) {
        const row = allReturnData.find(r => r.id === returnId);
        if (!row || !row.items || row.items.length === 0) {
            Swal.fire({ icon: 'info', title: 'No Items', text: 'No items found for this return.', confirmButtonColor: '#ff9f43' });
            return;
        }
        let html = '';
        row.items.forEach((item, i) => {
            html += `<tr>
                <td>${i + 1}</td>
                <td>${escapeHtml(item.product_name)}</td>
                <td>${parseFloat(item.qty_returned).toFixed(2)}</td>
                <td>${formatCurrency(row.final_settlement_amount)}</td>
            </tr>`;
        });
        $('#itemsModalBody').html(html);
        setTimeout(() => new bootstrap.Modal(document.getElementById('itemsModal')).show(), 50);
    };

    /* ── Action menu ── */
    function buildActionMenu(returnId) {
        return `
            <div class="dropdown vendor-action-dropdown">
                <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end vendor-action-menu">
                    <a class="dropdown-item" href="javascript:void(0);" onclick="showItemsModal(${returnId})">
                        <i class="fas fa-eye"></i><span>View Items</span>
                    </a>
                    <a class="dropdown-item" href="/rental-return-pdf/${returnId}" target="_blank">
                        <i class="fas fa-file-pdf"></i><span>PDF</span>
                    </a>
                </div>
            </div>`;
    }

    /* ── Mobile Action menu ── */
    function buildMobileActionMenu(returnId) {
        return `
            <a class="btn-icon-mobile btn-view" href="javascript:void(0);" onclick="showItemsModal(${returnId})" title="View Items">
                <i class="fas fa-eye"></i>
            </a>
            <a class="btn-icon-mobile btn-print" href="/rental-return-pdf/${returnId}" target="_blank" title="PDF">
                <i class="fas fa-file-pdf"></i>
            </a>
        `;
    }

    /* ── Mobile toggle ── */
    window.toggleMobileDetails = function (id) {
        const $det = $(`#mobile-details-${id}`);
        const $btn = $(`.mobile-toggle-btn[data-id="${id}"]`);
        if ($det.hasClass('active')) {
            $det.removeClass('active'); $btn.removeClass('minus'); $btn.find('.toggle-icon').text('+');
        } else {
            $det.addClass('active'); $btn.addClass('minus'); $btn.find('.toggle-icon').text('−');
        }
    };

    /* ── Mobile cards ── */
    function renderMobileCards(data) {
        const $c = $('#mobile-return-container').empty();
        if (!data || data.length === 0) {
            $c.html('<div class="sr-mobile-empty">No rental return records found.</div>');
            return;
        }

        data.forEach(item => {
            const orderNo = escapeHtml(item.order_number) || '-';
            const orderNoHtml = item.order_id ?
                `<a href="/sales-details/${item.order_id}" class="sr-mobile-order-link">${orderNo}</a>` :
                orderNo;
            const customerName = escapeHtml(item.customer) || '-';

            const typeBadge = item.is_full_return
                ? '<span class="badge-full-return">Full</span>'
                : '<span class="badge-partial-return">Partial</span>';
            const depositBadge = item.deposit_settled
                ? '<span class="badge-full-return">Settled</span>'
                : '<span style="color:#6c757d;">No</span>';

            const returnedProductNames = Array.isArray(item.items) ?
                item.items
                    .map(r => r && r.product_name)
                    .filter(n => n !== undefined && n !== null && String(n).trim() !== '')
                    .map(n => escapeHtml(String(n)))
                    .join(', ') : '';

            $c.append(`
                <article class="sr-mobile-card" data-return-id="${escapeHtml(item.id)}">
                    <div class="sr-mobile-top">
                        <div class="sr-mobile-return">
                            <span class="sr-mobile-return-label">Order</span>${orderNoHtml}
                            <span class="sr-mobile-summary-item" style="margin-left:10px;">Items<strong>${Number(item.items_count || 0)}</strong></span>
                            <span class="sr-mobile-summary-item">Qty<strong>${parseFloat(item.total_qty_returned || 0).toFixed(2)}</strong></span>
                        </div>
                        <div class="sr-mobile-total">${formatCurrency(item.final_settlement_amount)}</div>
                    </div>
                    <div class="sr-mobile-meta">
                        <span class="sr-mobile-customer">${customerName}</span>
                        <span>${escapeHtml(item.return_date) || '-'}</span>
                        ${returnedProductNames ? `<span class="sr-mobile-items-inline"><span class="sr-mobile-items-label">Products</span> ${returnedProductNames}</span>` : ''}
                    </div>
                    <div class="sr-mobile-stats">
                        <div class="sr-mobile-stat"><span>Payment</span><strong>${escapeHtml(item.payment_method) || '-'}</strong></div>
                        <div class="sr-mobile-stat"><span>Type</span><strong>${typeBadge}</strong></div>
                        <div class="sr-mobile-stat"><span>Deposit</span><strong>${depositBadge}</strong></div>
                        <div class="sr-mobile-actions">${buildActionMenu(item.id)}</div>
                    </div>
                </article>
            `);
        });
    }

    /* ── Fetch list ── */
    function fetchList(page = 1) {
        let url = `/api/rental-return-list?page=${page}&per_page=${perPage}`;
        if (selectedSubAdminId && selectedSubAdminId !== 'null' && selectedSubAdminId !== 'undefined') {
            url += `&selectedSubAdminId=${encodeURIComponent(selectedSubAdminId)}`;
        }
        if (searchQuery) url += `&search=${encodeURIComponent(searchQuery)}`;

        $.ajax({
            url, type: 'GET', dataType: 'json',
            headers: { 'Authorization': 'Bearer ' + authToken },
            success: function (res) {
                if (!res.status) { renderEmpty(res.message || 'No rental return records found.'); return; }

                currencySymbol   = res.currency_symbol   || currencySymbol;
                currencyPosition = res.currency_position || currencyPosition;
                allReturnData    = res.data || [];

                const pg = res.pagination;
                if (pg) { currentPage = pg.current_page; lastPage = pg.last_page; updatePagination(pg);  $('#mmCount').text(pg.total + ' items');}

                const rows = allReturnData.map((item, i) => {
                    const sn = ((currentPage - 1) * perPage) + i + 1;
                    const typeBadge = item.is_full_return
                        ? '<span class="badge-full-return">Full Return</span>'
                        : '<span class="badge-partial-return">Partial</span>';
                    const depositBadge = item.deposit_settled
                        ? '<span class="badge-full-return">Settled</span>'
                        : '<span style="color:#6c757d;">No</span>';
                    return [
                        sn,
                        item.order_number ? `<a href="/sales-details/${item.order_id}" style="color:#ff9f43;font-weight:600;">${escapeHtml(item.order_number)}</a>` : '-',
                        item.return_date || '-',
                        escapeHtml(item.customer) || '-',
                        item.items_count,
                        parseFloat(item.total_qty_returned).toFixed(2),
                        `<strong class="text-success">${formatCurrency(item.final_settlement_amount)}</strong>`,
                        escapeHtml(item.payment_method),
                        typeBadge,
                        depositBadge,
                        buildActionMenu(item.id)
                    ];
                });

                table.clear();
                if (rows.length > 0) { table.rows.add(rows).draw(); $('.pagination-controls').show(); }
                else { renderEmpty('No rental return records found.'); }

                renderMobileCards(allReturnData);
            },
            error: function () { renderEmpty('Unable to load rental return records.'); }
        });
    }

    /* ── Pagination ── */
    function updatePagination(pg) {
        let from = (pg.current_page - 1) * pg.per_page + 1;
        let to   = Math.min(pg.current_page * pg.per_page, pg.total);
        if (pg.total === 0) from = 0;
        $('#pagination-from').text(from);
        $('#pagination-to').text(to);
        $('#pagination-total').text(pg.total);

        let html = `<li class="page-item ${pg.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0);" data-page="${pg.current_page - 1}">Previous</a></li>`;

        const visible = 2;
        let start = Math.floor((pg.current_page - 1) / visible) * visible + 1;
        let end   = Math.min(pg.last_page, start + visible - 1);

        if (start > 1) html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" data-page="${start - 1}">..</a></li>`;
        for (let i = start; i <= end; i++) {
            html += `<li class="page-item ${i === pg.current_page ? 'active' : ''}"><a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a></li>`;
        }
        if (end < pg.last_page) html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" data-page="${end + 1}">..</a></li>`;
        html += `<li class="page-item ${pg.current_page === pg.last_page || pg.last_page === 0 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0);" data-page="${pg.current_page + 1}">Next</a></li>`;

        $('#pagination-numbers').html(html);
        $('.pagination-controls').toggle(pg.total > 0);
    }

    /* ── Floating dropdown ── */
    function positionMenu(el) {
        const $d = $(el), $m = $d.data('floating-menu'), btn = $d.find('.action-menu-toggle')[0];
        if (!$m || !btn) return;
        const r = btn.getBoundingClientRect(), mw = $m[0].offsetWidth || 168, mh = $m[0].offsetHeight || 120;
        const vw = window.innerWidth, vh = window.innerHeight, gap = 4;
        let left = Math.max(8, Math.min(r.right - mw, vw - mw - 8));
        let top  = r.bottom + gap;
        if (top + mh > vh - 8) top = Math.max(8, r.top - mh - gap);
        $m.css({ left: `${left}px`, position: 'fixed', top: `${top}px`, transform: 'none' });
    }

    $(document).on('show.bs.dropdown', '.vendor-action-dropdown', function () {
        const $d = $(this), $m = $d.find('.vendor-action-menu');
        $d.data('floating-menu', $m).data('menu-parent', $m.parent()).data('menu-next', $m.next());
        $m.addClass('vendor-action-menu-floating').appendTo('body');
    });
    $(document).on('shown.bs.dropdown', '.vendor-action-dropdown', function () { positionMenu(this); });
    $(document).on('hidden.bs.dropdown', '.vendor-action-dropdown', function () {
        const $d = $(this), $m = $d.data('floating-menu'), $p = $d.data('menu-parent'), $n = $d.data('menu-next');
        if ($m && $p) {
            $m.removeClass('vendor-action-menu-floating').removeAttr('style');
            $n && $n.length && $.contains($p[0], $n[0]) ? $m.insertBefore($n) : $p.append($m);
        }
        $d.removeData('floating-menu menu-parent menu-next');
    });
    $(window).on('scroll resize', function () { $('.vendor-action-dropdown.show').each(function () { positionMenu(this); }); });

    /* ── Events ── */
    $('#search-input').on('keyup', function () { searchQuery = $(this).val(); fetchList(1); });
    $(document).on('click', '#pagination-numbers .page-link', function (e) {
        e.preventDefault();
        const pg = $(this).data('page');
        if (pg && pg !== currentPage && pg >= 1 && pg <= lastPage) fetchList(pg);
    });
    $('#per-page-select').on('change', function () { perPage = $(this).val(); fetchList(1); });

    if (!authToken) { renderEmpty('Authentication token not found. Please login again.'); return; }
    fetchList(1);
});
</script>
@endpush
