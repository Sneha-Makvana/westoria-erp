@extends('layout.app')

@section('title', 'Products Delivery')

@section('content')
    <style>
        .delivery-page-shell { padding: 24px; }
        .delivery-page-title { font-size: 24px; font-weight: 700; color: #1f2937; margin-bottom: 4px; }
        .delivery-page-subtitle { color: #6b7280; margin-bottom: 22px; }
        .delivery-filter-card,
        .delivery-table-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03); }
        .delivery-filter-card { padding: 18px 20px; margin-bottom: 18px; }
        .delivery-table-card { padding: 18px 20px; }
        .delivery-label { font-size: 14px; font-weight: 500; color: #111827; margin-bottom: 8px; }
        .delivery-table thead th { background: #f8fafc; color: #111827; font-weight: 600; border-bottom: 1px solid #e5e7eb; padding: 14px 12px; white-space: nowrap; }
        .delivery-table tbody td { padding: 14px 12px; vertical-align: middle; white-space: nowrap; }
        .delivery-status { min-width: 150px; width: 150px; }
        .delivery-details-column { display: none; }
        .delivery-details-row { display: none; }
        .delivery-details-row.show { display: table-row; }
        .delivery-details-content { background: #fff; padding: 12px !important; }
        .delivery-details-list { display: grid; gap: 9px; }
        .delivery-details-item { display: flex; align-items: center; justify-content: space-between; gap: 12px; font-size: 13px; color: #334155; }
        .delivery-details-item strong { color: #0f172a; font-weight: 600; }
        .delivery-toggle-btn-table {
            width: 24px;
            height: 24px;
            border: none;
            border-radius: 50%;
            background: #ff9f43;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            line-height: 24px;
            text-align: center;
            padding: 0;
        }
        .delivery-toggle-btn-table.minus {
            background: #ffb25f;
        }
        .pdf-btn { border: 1px solid #ff9f43; color: #ff9f43; background: #fff; padding: 4px 12px; border-radius: 4px; text-decoration: none; display: inline-block; }
        .pdf-btn:hover { background: #fff7ec; color: #ff9f43; }
        .btn-back { display: inline-flex; align-items: center; gap: 6px; border: 1px solid #ff9f43; background: #ff9f43; color: #fff; border-radius: 4px; padding: 6px 16px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s ease; }
        .btn-back:hover { background: #e58e3c; color: #fff; text-decoration: none; }
        .btn-back i { font-size: 14px; }
        :root[data-theme="dark"] .pdf-btn { background: transparent; }
        :root[data-theme="dark"] .pdf-btn:hover { background: #ff9f43; color: #fff; }
        @media (max-width: 767px) {
            .delivery-page-shell { padding: 16px 12px; }
            .delivery-table-card, .delivery-filter-card { padding: 14px; }
            .delivery-filter-card .row { --bs-gutter-x: 10px; --bs-gutter-y: 10px; }
            .delivery-label { font-size: 12px; margin-bottom: 5px; }
            .delivery-filter-card .form-control,
            .delivery-filter-card .form-select { font-size: 12px; padding: 7px 8px; }
            .delivery-table-card { overflow: hidden; }
            .delivery-table { table-layout: fixed; min-width: 0; width: 100%; }
            .delivery-table th,
            .delivery-table td { font-size: 10px; padding: 10px 5px; white-space: normal; word-break: break-word; }
            .delivery-table th:nth-child(3),
            .delivery-table td:nth-child(3),
            .delivery-table th:nth-child(4),
            .delivery-table td:nth-child(4),
            .delivery-table th:nth-child(5),
            .delivery-table td:nth-child(5),
            .delivery-table th:nth-child(6),
            .delivery-table td:nth-child(6),
            .delivery-table th:nth-child(7),
            .delivery-table td:nth-child(7) { display: none; }
            .delivery-details-column {
                display: table-cell;
                text-align: center;
                width: 42px;
            }
            .delivery-table th:nth-child(1) { width: 44%; }
            .delivery-table th:nth-child(2) { width: 38%; }
            .delivery-status { min-width: 130px; width: 130px; }
        }

        .dl-search-row { display: flex; align-items: center; gap: 8px; }
        .dl-m-head, .dl-card-list { display: none; }

        @media (max-width: 1024.98px), (width: 1032px) and (height: 1376px), (width: 1280px) and (height: 800px) {
            .delivery-page-shell { padding: 6px 6px 90px; }
            .delivery-page-shell > .dl-desktop-head,
            .delivery-table-card > .table-responsive { display: none !important; }

            .dl-m-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-bottom: 8px;
                padding: 12px 10px;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            }
            .dl-m-title { margin-left: 2px; font-size: 15px; font-weight: 600; color: #1b2850; }
            .dl-m-count { margin: 1px 0 0 2px; font-size: 11.5px; color: #8a92a6; }
            .dl-m-count:empty { display: none; }
            .dl-m-back {
                display: inline-flex;
                align-items: center;
                height: 30px;
                padding: 0 12px;
                border-radius: 8px;
                background: #ff9f43;
                color: #fff !important;
                font-size: 12px;
                font-weight: 600;
                text-decoration: none;
            }

            .delivery-filter-card {
                margin-bottom: 8px;
                padding: 8px 10px;
                border: 0;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            }
            .delivery-filter-card .row { --bs-gutter-y: 8px; }
            .delivery-filter-card .dl-search-col { flex: 0 0 100%; max-width: 100%; }
            .delivery-filter-card .dl-search-col .delivery-label { display: none; }
            .delivery-filter-card .dl-search-col .form-control {
                height: 40px;
                border-radius: 10px;
                font-size: 13px;
                padding: 0 12px;
            }
            .delivery-filter-card .dl-extra-filter { display: none; }
            .delivery-filter-card.is-filters-open .dl-extra-filter { display: block; }
            .delivery-filter-card .dl-extra-filter:has(select) { flex: 0 0 100%; max-width: 100%; }
            .delivery-filter-card .dl-extra-filter .form-control,
            .delivery-filter-card .dl-extra-filter .form-select { height: 38px; border-radius: 10px; }
            .dl-filter-btn {
                position: relative;
                flex: 0 0 38px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 38px;
                height: 38px;
                padding: 0;
                border: 0;
                border-radius: 50%;
                background: #f05a6a;
                color: #fff;
                font-size: 15px;
                box-shadow: 0 2px 6px rgba(240, 90, 106, .35);
            }
            .dl-filter-btn.has-active { background: #e8334a; }
            .dl-filter-count {
                position: absolute;
                top: -4px;
                right: -4px;
                min-width: 18px;
                height: 18px;
                padding: 0 5px;
                border-radius: 999px;
                background: #1b2850;
                color: #fff;
                font-size: 11px;
                line-height: 18px;
                text-align: center;
            }

            .delivery-table-card {
                padding: 0;
                border: 0;
                background: transparent;
                box-shadow: none;
                overflow: visible;
            }
            .dl-card-list { display: flex; flex-direction: column; gap: 8px; }
            .dl-card {
                padding: 12px;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            }
            .dl-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
            .dl-order { min-width: 0; color: #1b2850 !important; font-size: 14px; font-weight: 700; text-decoration: none; word-break: break-all; }
            .dl-total { flex-shrink: 0; color: #1b2850; font-size: 14px; font-weight: 800; }
            .dl-sub { margin-top: 3px; color: #8a92a6; font-size: 12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .dl-foot {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-top: 10px;
                padding-top: 10px;
                border-top: 1px dashed #e5e7eb;
            }
            .dl-by { flex: 1 1 auto; min-width: 0; }
            .dl-by span { display: block; color: #8a92a6; font-size: 11px; }
            .dl-by strong { display: block; overflow: hidden; color: #1b2850; font-size: 12.5px; text-overflow: ellipsis; white-space: nowrap; }
            .dl-card .dl-status {
                flex: 0 0 auto;
                width: auto !important;
                min-width: 0 !important;
                max-width: 170px;
                height: 32px;
                padding: 0 30px 0 10px;
                background-position: right 8px center;
                text-overflow: ellipsis;
                border: 0;
                border-radius: 999px;
                font-size: 11.5px;
                font-weight: 700;
            }
            .dl-card .dl-status.is-pending { background-color: #fff3e6; color: #c26a12; }
            .dl-card .dl-status.is-delivered { background-color: #e7f6ec; color: #198754; }
            .dl-card .dl-status.is-partially_delivered { background-color: #e6f0ff; color: #2563eb; }
            .dl-card .dl-status.is-cancelled { background-color: #fdecee; color: #e5484d; }
            .dl-pdf {
                flex: 0 0 auto;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                height: 32px;
                padding: 0 10px;
                border-radius: 8px;
                background: #e5483d;
                color: #fff !important;
                font-size: 11px;
                font-weight: 700;
                text-decoration: none;
            }
            .dl-empty {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 8px;
                padding: 32px 12px;
                background: #fff;
                border-radius: 12px;
                color: #8a92a6;
                font-size: 13px;
            }
            .dl-empty i { font-size: 28px; color: #d0d5dd; }
            .delivery-table-card .pagination-controls {
                margin-top: 10px !important;
                padding: 10px !important;
                border-top: 0 !important;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
                gap: 8px;
            }
            .delivery-table-card .pagination-controls > .d-flex {
                flex-wrap: wrap;
                justify-content: center;
                margin-bottom: 0 !important;
                font-size: 12px;
            }

            :root[data-theme="dark"] .dl-m-head,
            :root[data-theme="dark"] .dl-card,
            :root[data-theme="dark"] .dl-empty,
            :root[data-theme="dark"] .delivery-table-card .pagination-controls { background: #1e293b; }
            :root[data-theme="dark"] .delivery-table-card { background: transparent; }
            :root[data-theme="dark"] .dl-m-title,
            :root[data-theme="dark"] .dl-order,
            :root[data-theme="dark"] .dl-total,
            :root[data-theme="dark"] .dl-by strong { color: #f8fafc !important; }
        }
        @media screen and (min-width: 768px) and (max-width: 1024.98px),
        screen and (width: 1032px) and (height: 1376px),
        screen and (width: 1280px) and (height: 800px) {

        .delivery-page-shell {
            padding: 6px 12px 100px;
        }

        /* Bootstrap d-md-none override */
        .dl-m-head.d-md-none {
            display: flex !important;
        }

        .dl-filter-btn.d-md-none {
            display: inline-flex !important;
        }

        /* Cards: 2 columns */
        .dl-card-list.d-md-none {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            align-items: start;
        }

        /* Loading / empty message puri width ma */
        .dl-card-list > .dl-empty {
            grid-column: 1 / -1;
        }

        /* Extra filters (Status, From, To) 3 ek row ma */
        .delivery-filter-card .dl-extra-filter,
        .delivery-filter-card .dl-extra-filter:has(select) {
            flex: 0 0 33.3333%;
            max-width: 33.3333%;
        }
    }
    </style>

    <div class="delivery-page-shell">
        <div class="dl-m-head d-md-none">
            <div>
                <div class="dl-m-title">Products Delivery</div>
                <div class="dl-m-count" id="dlCount"></div>
            </div>
            <a href="{{ route('sales.list') }}" class="dl-m-back"><i class="fa fa-arrow-left me-1"></i> Back</a>
        </div>

        <div class="mb-4 d-flex justify-content-between align-items-center dl-desktop-head">
            <div>
                <div class="delivery-page-title">Products Delivery</div>
                <div class="delivery-page-subtitle mb-0">View and manage delivery status for all sales orders</div>
            </div>
            <div>
                <a href="{{ route('sales.list') }}" class="btn-back">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="delivery-filter-card">
            <form method="GET" action="{{ route('sales.products_delivery') }}" onsubmit="return false;">
                <div class="row g-3 align-items-end">
                    <div class="col-6 col-lg-3 col-md-6 dl-search-col">
                        <label class="delivery-label">Order #</label>
                        <div class="dl-search-row">
                            <input type="text" name="order_no" value="{{ $orderNo }}" class="form-control" placeholder="Search order number">
                            <button type="button" class="dl-filter-btn d-md-none" id="dlFilterToggle" aria-label="Filters">
                                <i class="fas fa-filter"></i>
                                <span class="dl-filter-count d-none" id="dlFilterCount">0</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6 dl-extra-filter">
                        <label class="delivery-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" @selected($status === 'pending')>Pending</option>
                            <option value="delivered" @selected($status === 'delivered')>Delivered</option>
                            <option value="partially_delivered" @selected($status === 'partially_delivered')>Partially Delivered</option>
                            <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-6 col-lg-2 col-md-6 dl-extra-filter">
                        <label class="delivery-label">From</label>
                        <input type="date" name="from" value="{{ $fromDate }}" class="form-control">
                    </div>
                    <div class="col-6 col-lg-2 col-md-6 dl-extra-filter">
                        <label class="delivery-label">To</label>
                        <input type="date" name="to" value="{{ $toDate }}" class="form-control">
                    </div>
                </div>
            </form>
        </div>

        <div class="delivery-table-card">
            <div class="table-responsive">
                <table class="table delivery-table mb-0">
                    <thead>
                        <tr>
                            <th>Order No.</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Delivered By</th>
                            <th>Status</th>
                            <th>Action</th>
                            <th class="delivery-details-column">Details</th>
                        </tr>
                    </thead>
                    <tbody id="deliveryTableBody">
                        <tr><td colspan="7" class="text-center text-muted py-4">Loading delivery orders...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="dl-card-list d-md-none" id="dlCardList"></div>

            <!-- PAGINATION CONTROLS -->
            <div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3"
                style="display: none; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
                    <select id="per-page-select" class="form-select form-select-sm" style="width: auto; border: 1px solid #ddd;">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
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
@endsection

@push('js')
    <script>
        let deliveryFilterTimer = null;
        let currentDeliveryPage = 1;
        let lastDeliveryPage = 1;
        let deliveryPerPage = 25;
        const deliveryListUrl = @json(route('sales.products_delivery.data'));

        $(function() {
            window.history.replaceState({}, '', window.location.pathname);
        });

        function triggerDeliveryLoad() {
            clearTimeout(deliveryFilterTimer);
            deliveryFilterTimer = setTimeout(() => loadDeliveryRows(1), 250);
        }

        $(document).on('change', '.delivery-filter-card select, .delivery-filter-card input[type="date"]', function() {
            triggerDeliveryLoad();
        });

        $(document).on('input', '.delivery-filter-card input[name="order_no"]', function() {
            triggerDeliveryLoad();
        });

        function escDelivery(value) {
            return String(value == null ? '' : value).replace(/[&<>"']/g, function(ch) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
            });
        }

        function deliveryStatusOptions(statusValue) {
            return [
                ['pending', 'Pending'],
                ['delivered', 'Delivered'],
                ['partially_delivered', 'Partially Delivered'],
                ['cancelled', 'Cancelled']
            ].map(([value, label]) => `<option value="${value}" ${statusValue === value ? 'selected' : ''}>${label}</option>`).join('');
        }

        function syncDeliveryStatusClass($select) {
            $select.removeClass('is-pending is-delivered is-partially_delivered is-cancelled')
                .addClass('is-' + ($select.val() || 'pending'));
        }

        function renderDeliveryMessage(text, icon) {
            $('#dlCardList').html(`<div class="dl-empty"><i class="fas ${icon || 'fa-truck'}"></i><span>${escDelivery(text)}</span></div>`);
            $('#dlCount').text('');
        }

        function renderDeliveryCards(rows) {
            const html = rows.map((row) => {
                const statusValue = String(row.status || 'pending').toLowerCase().replace(/[\s-]/g, '_');
                const currencySymbol = row.currency_symbol || '₹';
                const total = Number(row.total || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const sub = [row.customer_name, row.created_at].filter(Boolean).map(escDelivery).join(' &middot; ');
                return `
                    <div class="dl-card">
                        <div class="dl-top">
                            <a class="dl-order" href="${escDelivery(row.delivery_url || '#')}">${escDelivery(row.order_number || row.order_id || '--')}</a>
                            <span class="dl-total">${escDelivery(currencySymbol)}${total}</span>
                        </div>
                        <div class="dl-sub">${sub || '&nbsp;'}</div>
                        <div class="dl-foot">
                            <div class="dl-by"><span>Delivered By</span><strong>${escDelivery(row.delivered_by || '--')}</strong></div>
                            <select class="form-select pending-delivery-status dl-status is-${statusValue}" data-url="${escDelivery(row.status_update_url || '')}" data-current-status="${statusValue}">
                                ${deliveryStatusOptions(statusValue)}
                            </select>
                            ${row.pdf_url ? `<a href="${escDelivery(row.pdf_url)}" target="_blank" class="dl-pdf">PDF</a>` : ''}
                        </div>
                    </div>`;
            }).join('');
            $('#dlCardList').html(html);
        }

        function renderDeliveryRows(rows) {
            const $tbody = $('#deliveryTableBody');
            if (!rows || !rows.length) {
                $tbody.html('<tr><td colspan="7" class="text-center text-muted py-4">No delivery orders found</td></tr>');
                renderDeliveryMessage('No delivery orders found');
                $('.pagination-controls').hide();
                return;
            }
            renderDeliveryCards(rows);

            const html = rows.map((row) => {
                const statusValue = String(row.status || 'pending').toLowerCase().replace(/[\s-]/g, '_');
                const currencySymbol = row.currency_symbol || '₹';
                const total = Number(row.total || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const rowId = row.order_id || row.id || Math.random().toString(36).slice(2);
                return `
                    <tr data-delivery-row-id="${rowId}">
                        <td><a href="${row.delivery_url}">${row.order_number || row.order_id || '--'}</a></td>
                        <td>${row.created_at || '--'}</td>
                        <td>${row.customer_name || '--'}</td>
                        <td>${currencySymbol}${total}</td>
                        <td>${row.delivered_by || '--'}</td>
                        <td>
                            <select class="form-control form-control-sm pending-delivery-status delivery-status" data-url="${row.status_update_url || ''}" data-current-status="${statusValue}">
                                <option value="pending" ${statusValue === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="delivered" ${statusValue === 'delivered' ? 'selected' : ''}>Delivered</option>
                                <option value="partially_delivered" ${statusValue === 'partially_delivered' ? 'selected' : ''}>Partially Delivered</option>
                                <option value="cancelled" ${statusValue === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                            </select>
                        </td>
                        <td><a href="${row.pdf_url}" target="_blank" class="pdf-btn">PDF</a></td>
                        <td class="delivery-details-column">
                            <button type="button" class="delivery-toggle-btn-table" data-delivery-id="${rowId}">+</button>
                        </td>
                    </tr>
                    <tr class="delivery-details-row" data-delivery-details-id="${rowId}">
                        <td colspan="8" class="delivery-details-content">
                            <div class="delivery-details-list">
                                <div class="delivery-details-item">
                                    <strong>Customer:</strong>
                                    <span>${row.customer_name || '--'}</span>
                                </div>
                                <div class="delivery-details-item">
                                    <strong>Total:</strong>
                                    <span>${currencySymbol}${total}</span>
                                </div>
                                <div class="delivery-details-item">
                                    <strong>Delivered By:</strong>
                                    <span>${row.delivered_by || '--'}</span>
                                </div>
                                <div class="delivery-details-item">
                                    <strong>Status:</strong>
                                    <select class="form-control form-control-sm pending-delivery-status delivery-status"
                                        data-url="${row.status_update_url || ''}"
                                        data-current-status="${statusValue}">
                                        <option value="pending" ${statusValue === 'pending' ? 'selected' : ''}>Pending</option>
                                        <option value="delivered" ${statusValue === 'delivered' ? 'selected' : ''}>Delivered</option>
                                        <option value="partially_delivered" ${statusValue === 'partially_delivered' ? 'selected' : ''}>Partially Delivered</option>
                                        <option value="cancelled" ${statusValue === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="delivery-details-item">
                                    <strong>Action:</strong>
                                    <a href="${row.pdf_url}" target="_blank" class="pdf-btn">PDF</a>
                                </div>
                            </div>
                        </td>
                    </tr>`;
            }).join('');
            $tbody.html(html);
        }

        function updateDeliveryPaginationUI(pagination) {
            if (!pagination) return;
            
            currentDeliveryPage = pagination.current_page;
            lastDeliveryPage = pagination.last_page;
            
            $('#pagination-from').text(pagination.from);
            $('#pagination-to').text(pagination.to);
            $('#pagination-total').text(pagination.total);
            $('#dlCount').text(pagination.total ? pagination.total + (Number(pagination.total) === 1 ? ' order' : ' orders') : '');
            
            let paginationHtml = `
                <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page - 1}">Previous</a>
                </li>
            `;
            
            for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
                paginationHtml += `
                    <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                    </li>
                `;
            }
            
            paginationHtml += `
                <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page + 1}">Next</a>
                </li>
            `;
            
            $('#pagination-numbers').html(paginationHtml).data('total-pages', pagination.last_page);
            $('.pagination-controls').show();
        }

        $(document).on('click', '.delivery-toggle-btn-table', function() {
            const $btn = $(this);
            const deliveryId = $btn.data('delivery-id');
            const $detailsRow = $(`.delivery-details-row[data-delivery-details-id="${deliveryId}"]`);

            $('.delivery-details-row').not($detailsRow).removeClass('show');
            $('.delivery-toggle-btn-table').not($btn).removeClass('minus').text('+');

            $detailsRow.toggleClass('show');
            $btn.toggleClass('minus', $detailsRow.hasClass('show')).text($detailsRow.hasClass('show') ? '-' : '+');
        });

        $(document).on('click', '#pagination-numbers .page-link', function(e) {
            e.preventDefault();
            let page = parseInt($(this).data('page'), 10);
            if (page && page >= 1 && page <= lastDeliveryPage && page !== currentDeliveryPage) {
                loadDeliveryRows(page);
            }
        });

        $('#per-page-select').on('change', function() {
            deliveryPerPage = parseInt($(this).val());
            loadDeliveryRows(1);
        });

        function loadDeliveryRows(page = 1) {
            const filters = {
                page: page,
                per_page: deliveryPerPage,
                order_no: $('.delivery-filter-card input[name="order_no"]').val() || '',
                status: $('.delivery-filter-card select[name="status"]').val() || '',
                from: $('.delivery-filter-card input[name="from"]').val() || '',
                to: $('.delivery-filter-card input[name="to"]').val() || '',
                selectedSubAdminId: localStorage.getItem('selectedSubAdminId') || ''
            };

            $('#deliveryTableBody').html('<tr><td colspan="7" class="text-center text-muted py-4">Loading delivery orders...</td></tr>');
            renderDeliveryMessage('Loading delivery orders...', 'fa-spinner fa-spin');
            $.ajax({
                url: deliveryListUrl,
                method: 'GET',
                data: filters,
                success: function(response) { 
                    renderDeliveryRows(response?.data || []);
                    if (response?.pagination) {
                        updateDeliveryPaginationUI(response.pagination);
                    }
                },
                error: function() { 
                    $('#deliveryTableBody').html('<tr><td colspan="7" class="text-center text-danger py-4">Unable to load delivery orders.</td></tr>');
                    renderDeliveryMessage('Unable to load delivery orders.', 'fa-exclamation-circle');
                    $('.pagination-controls').hide();
                }
            });
        }

        $(document).on('change', '.pending-delivery-status', function() {
            const $select = $(this);
            const url = $select.data('url');
            const status = $select.val();
            const currentStatus = $select.data('current-status');
            if (!url) return;
            $select.prop('disabled', true).addClass('is-saving');
            $.ajax({
                url: url,
                method: 'POST',
                data: { status: status, _token: $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response && response.status) {
                        $select.data('current-status', status);
                        Swal.fire({ icon: 'success', title: 'Success', text: response.message || 'Delivery status updated successfully.', confirmButtonColor: '#ff9f43', timer: 1800, showConfirmButton: false });
                        return;
                    }
                    $select.val(currentStatus);
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Unable to update delivery status.', confirmButtonColor: '#ff9f43' });
                },
                error: function(xhr) {
                    $select.val(currentStatus);
                    Swal.fire({ icon: 'error', title: 'Error', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Unable to update delivery status.', confirmButtonColor: '#ff9f43' });
                },
                complete: function() {
                    $select.prop('disabled', false).removeClass('is-saving');
                    if ($select.hasClass('dl-status')) syncDeliveryStatusClass($select);
                }
            });
        });

        $(document).on('change', '.dl-status', function() {
            syncDeliveryStatusClass($(this));
        });

        function updateDeliveryFilterCount() {
            const n = ['select[name="status"]', 'input[name="from"]', 'input[name="to"]'].filter((sel) => {
                return !!$('.delivery-filter-card ' + sel).val();
            }).length;
            $('#dlFilterCount').text(n).toggleClass('d-none', n === 0);
            $('#dlFilterToggle').toggleClass('has-active', n > 0);
        }

        $(document).on('click', '#dlFilterToggle', function() {
            $('.delivery-filter-card').toggleClass('is-filters-open');
        });
        $(document).on('change', '.delivery-filter-card select, .delivery-filter-card input[type="date"]', updateDeliveryFilterCount);

        updateDeliveryFilterCount();
        loadDeliveryRows(1);
    </script>
@endpush
