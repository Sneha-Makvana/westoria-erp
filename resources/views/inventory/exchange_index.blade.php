@extends('layout.app')

@section('title', 'Exchange Inventory')

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
        }

        .exchange-stat {
            border: 1px solid #f1f1f1;
            border-radius: 10px;
            padding: 12px 16px;
            background: #fff;
        }
        .exchange-stat .label {
            font-size: 12px;
            color: #6c757d;
        }
        .exchange-stat .value {
            font-size: 20px;
            font-weight: 700;
            color: #092C4C;
        }
        #exchange-summary-table tbody tr {
            cursor: pointer;
        }
        #exchange-summary-table tbody tr.is-active td {
            background: #fff3e6;
        }
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

        @media (max-width: 767.98px) {
            .xc-sub {
                display: block;
                font-size: 11px;
                color: #8b93a7;
                margin-top: 2px;
            }
            .xc-card {
                background: transparent !important;
                border: 0 !important;
                box-shadow: none !important;
            }
            .xc-card > .card-body {
                padding: 0 !important;
            }
            .xc-search {
                flex: 1 1 auto;
                min-width: 0;
            }
            .xc-search input {
                height: 38px;
                border-radius: 8px;
            }
            .xc-stats {
                --bs-gutter-x: 6px;
                margin-bottom: 8px !important;
            }
            .xc-stats > div {
                flex: 1 1 0 !important;
                width: auto !important;
            }
            .xc-stats .exchange-stat {
                padding: 8px;
                height: 100%;
                border: 0;
                box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
            }
            .xc-stats .exchange-stat .label {
                font-size: 10px;
                line-height: 1.2;
            }
            .xc-stats .exchange-stat .value {
                font-size: 15px;
                margin-top: 2px;
            }
            .xc-title {
                font-size: 14px;
                font-weight: 700;
                color: #1b2850;
                margin: 4px 2px 8px !important;
            }
            .xc-title small {
                font-size: 11px;
                font-weight: 400;
                color: #8b93a7;
            }
            .xc-entries-head .btn {
                font-size: 11px;
                padding: 3px 8px;
            }
            .xc-card .table-responsive {
                overflow: visible;
            }
            #exchange-summary-table,
            #exchange-summary-table tbody,
            #exchange-entries-table,
            #exchange-entries-table tbody {
                display: block;
                width: 100%;
                border: 0;
                margin: 0;
            }
            #exchange-summary-table thead,
            #exchange-entries-table thead {
                display: none;
            }
            #exchange-summary-table tbody tr,
            #exchange-entries-table tbody tr {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 6px 10px;
                background: #fff;
                border-radius: 10px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
                padding: 12px;
                margin-bottom: 8px;
            }
            #exchange-summary-table tbody tr.is-active {
                box-shadow: 0 0 0 2px #ff9f43;
            }
            #exchange-summary-table tbody tr.is-active td {
                background: transparent;
            }
            #exchange-summary-table td,
            #exchange-entries-table td {
                display: block;
                padding: 0 !important;
                border: 0 !important;
                text-align: left !important;
                white-space: normal !important;
                min-width: 0;
                font-size: 13px;
                color: #1b2850;
            }
            #exchange-summary-table td[colspan],
            #exchange-entries-table td[colspan] {
                grid-column: 1 / -1;
                text-align: center !important;
                padding: 12px 0 !important;
            }
            .xc-card td.xc-name {
                grid-column: 1 / -1;
                font-size: 14px;
                font-weight: 600;
                text-transform: uppercase;
            }
            #exchange-summary-table td.xc-type,
            #exchange-entries-table td.xc-type {
                justify-self: start;
                font-size: 11px !important;
                font-weight: 600;
                color: #ff9f43 !important;
                background: #fff4e8;
                border-radius: 12px;
                padding: 2px 10px !important;
            }
            #exchange-summary-table td.xc-type {
                grid-column: 1 / -1;
            }
            .xc-card td.xc-stat::before {
                content: attr(data-label);
                display: block;
                font-size: 11px;
                font-weight: 400;
                color: #8b93a7;
            }
            .xc-card td.xc-stat {
                font-weight: 700;
            }
            #exchange-summary-table td.xc-wide {
                grid-column: span 2;
            }
            #exchange-entries-table td.xc-name {
                grid-row: 1;
                grid-column: 1 / 3;
            }
            #exchange-entries-table td.xc-date {
                grid-row: 1;
                grid-column: 3;
                text-align: right !important;
                font-size: 12px;
                color: #8b93a7;
            }
            #exchange-entries-table td.xc-inv {
                grid-row: 2;
                grid-column: 1 / -1;
                font-size: 12px;
            }
            #exchange-entries-table td.xc-mat {
                grid-row: 3;
                grid-column: 1 / 3;
                font-weight: 600;
            }
            #exchange-entries-table td.xc-type {
                grid-row: 3;
                grid-column: 3;
                justify-self: end;
            }
            #exchange-entries-table td.xc-stat {
                grid-row: 4;
            }
            .xc-pager {
                background: #fff;
                border-radius: 12px;
                padding: 10px;
            }
        }
    </style>
    @include('inventory.partials.mobile-ui-css')

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Exchange Inventory</h4>
                <h6>Material received from customers in exchange on sales bills</h6>
            </div>
        </div>

        <div class="inv-m-head d-md-none">
            <div class="inv-m-head-top">
                <div>
                    <h4>Exchange Inventory</h4>
                    <small class="xc-sub">Material received in exchange on sales bills</small>
                </div>
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

        <div class="card xc-card">
            <div class="card-body">
                <div class="row g-2 align-items-end mb-3 inv-filter-row">
                    <div class="col-md-4 col-12 inv-search-col">
                        <button type="button" class="inv-m-filter-btn d-md-none" id="invDateToggle" aria-label="Date filter" aria-expanded="false">
                            <i class="fa fa-filter"></i>
                        </button>
                        <div class="xc-search">
                            <label class="form-label mb-1 d-none d-md-block" for="exchange-search">Search</label>
                            <input type="text" id="exchange-search" class="form-control form-control-sm" placeholder="Material, type, customer or invoice">
                        </div>
                    </div>
                    <div class="col-md-auto ms-auto col-12 inv-date-col">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label mb-1">Start Date</label>
                                <input type="date" id="exchange-start" class="form-control form-control-sm">
                            </div>
                            <div class="col-6">
                                <label class="form-label mb-1">End Date</label>
                                <input type="date" id="exchange-end" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-3 xc-stats">
                    <div class="col-md-4 col-12">
                        <div class="exchange-stat">
                            <div class="label">Total Qty / Weight Received</div>
                            <div class="value" id="exchange-total-qty">0</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-6">
                        <div class="exchange-stat">
                            <div class="label">Total Exchange Amount</div>
                            <div class="value" id="exchange-total-amount">₹0.00</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-6">
                        <div class="exchange-stat">
                            <div class="label">Bills With Exchange</div>
                            <div class="value" id="exchange-total-bills">0</div>
                        </div>
                    </div>
                </div>

                <h6 class="mb-2 xc-title">Stock by Material <small class="d-md-none">· tap to filter entries</small></h6>
                <div class="table-responsive mb-4">
                    <table class="table" id="exchange-summary-table">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Type</th>
                                <th class="text-end">Qty / Weight</th>
                                <th class="text-end">Avg Price</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Bills</th>
                                <th>Last Received</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-2 xc-entries-head">
                    <h6 class="mb-0 xc-title">Exchange Entries <span id="exchange-entries-filter" class="text-muted small"></span></h6>
                    <button type="button" id="exchange-clear-filter" class="btn btn-sm btn-outline-secondary d-none">Show all materials</button>
                </div>
                <div class="table-responsive">
                    <table class="table" id="exchange-entries-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Material</th>
                                <th>Type</th>
                                <th class="text-end">Qty / Weight</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2 xc-pager">
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
                        <select id="exchange-per-page" class="form-select form-select-sm" style="width: auto;">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span id="exchange-page-info" class="ms-3" style="font-size: 14px; color: #555;"></span>
                    </div>
                    <nav><ul class="pagination mb-0" id="exchange-pagination"></ul></nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    @include('inventory.partials.mobile-ui-js')
    <script>
        $(function() {
            var state = { page: 1, materialId: '', typeId: '', label: '' };
            var salesDetailsUrl = @json(url('/sales-details'));
            var searchTimer = null;

            function esc(value) {
                return $('<div>').text(value == null ? '' : String(value)).html();
            }

            function qty(value) {
                var n = parseFloat(value) || 0;
                return n.toFixed(3).replace(/\.?0+$/, '');
            }

            function money(value) {
                return '₹' + (parseFloat(value) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function load() {
                var params = {
                    page: state.page,
                    per_page: $('#exchange-per-page').val(),
                    search: $('#exchange-search').val(),
                    start_date: $('#exchange-start').val(),
                    end_date: $('#exchange-end').val(),
                    material_id: state.materialId,
                    type_id: state.typeId
                };
                var subAdmin = localStorage.getItem('selectedSubAdminId');
                if (subAdmin) params.selectedSubAdminId = subAdmin;

                $('#exchange-entries-table tbody').html('<tr><td colspan="8" class="text-center text-muted">Loading…</td></tr>');
                $.ajax({
                    url: '/api/exchange-inventory-list',
                    type: 'GET',
                    data: params,
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('authToken') },
                    success: function(res) {
                        if (!res || !res.status) {
                            $('#exchange-entries-table tbody').html('<tr><td colspan="8" class="text-center text-danger">Could not load exchange inventory</td></tr>');
                            return;
                        }
                        renderTotals(res.totals || {});
                        renderSummary(res.summary || []);
                        renderEntries(res.entries || []);
                        renderPagination(res.pagination || {});
                    },
                    error: function() {
                        $('#exchange-entries-table tbody').html('<tr><td colspan="8" class="text-center text-danger">Could not load exchange inventory</td></tr>');
                    }
                });
            }

            function renderTotals(totals) {
                $('#exchange-total-qty').text(qty(totals.qty));
                $('#exchange-total-amount').text(money(totals.amount));
                $('#exchange-total-bills').text(totals.bills || 0);
            }

            function renderSummary(rows) {
                var $body = $('#exchange-summary-table tbody').empty();
                if (!rows.length) {
                    $body.html('<tr><td colspan="7" class="text-center text-muted">No exchange received in this period</td></tr>');
                    return;
                }
                rows.forEach(function(row) {
                    var active = state.materialId !== '' && String(row.material_id) === state.materialId && String(row.type_id) === state.typeId;
                    var $tr = $('<tr></tr>').toggleClass('is-active', active)
                        .attr('data-material', row.material_id)
                        .attr('data-type', row.type_id)
                        .attr('data-label', row.material_name + (row.type_name ? ' - ' + row.type_name : ''));
                    $tr.html(
                        '<td class="fw-semibold xc-name">' + esc(row.material_name) + '</td>' +
                        '<td class="xc-type">' + esc(row.type_name || '-') + '</td>' +
                        '<td class="text-end fw-semibold xc-stat" data-label="Qty / Weight">' + qty(row.qty) + '</td>' +
                        '<td class="text-end xc-stat" data-label="Avg Price">' + money(row.avg_price) + '</td>' +
                        '<td class="text-end xc-stat" data-label="Amount">' + money(row.amount) + '</td>' +
                        '<td class="text-end xc-stat" data-label="Bills">' + (row.bills || 0) + '</td>' +
                        '<td class="xc-stat xc-wide" data-label="Last Received">' + esc(row.last_date) + '</td>'
                    );
                    $body.append($tr);
                });
            }

            function renderEntries(rows) {
                var $body = $('#exchange-entries-table tbody').empty();
                if (!rows.length) {
                    $body.html('<tr><td colspan="8" class="text-center text-muted">No exchange entries</td></tr>');
                    return;
                }
                rows.forEach(function(row) {
                    var invoice = row.order_number
                        ? '<a href="' + salesDetailsUrl + '/' + encodeURIComponent(row.order_id) + '">' + esc(row.order_number) + '</a>'
                        : '-';
                    $body.append(
                        '<tr>' +
                        '<td class="xc-date">' + esc(row.date) + '</td>' +
                        '<td class="xc-inv">' + invoice + '</td>' +
                        '<td class="xc-name">' + esc(row.customer) + (row.phone ? '<div class="small text-muted">' + esc(row.phone) + '</div>' : '') + '</td>' +
                        '<td class="xc-mat">' + esc(row.material_name) + '</td>' +
                        '<td class="xc-type">' + esc(row.type_name || '-') + '</td>' +
                        '<td class="text-end fw-semibold xc-stat" data-label="Qty / Weight">' + qty(row.qty) + '</td>' +
                        '<td class="text-end xc-stat" data-label="Price">' + money(row.price) + '</td>' +
                        '<td class="text-end xc-stat" data-label="Amount">' + money(row.amount) + '</td>' +
                        '</tr>'
                    );
                });
            }

            function renderPagination(p) {
                var $ul = $('#exchange-pagination').empty();
                $('#exchange-page-info').text(p.total ? ('Showing ' + p.from + ' to ' + p.to + ' of ' + p.total) : '');
                if (!p.last_page || p.last_page <= 1) return;
                function item(label, page, disabled, active) {
                    var $li = $('<li class="page-item"></li>').toggleClass('disabled', !!disabled).toggleClass('active', !!active);
                    $('<a class="page-link" href="#"></a>').text(label).attr('data-page', page).appendTo($li);
                    $ul.append($li);
                }
                item('‹', p.current_page - 1, p.current_page <= 1);
                var start = Math.max(1, p.current_page - 2);
                var end = Math.min(p.last_page, start + 4);
                for (var i = start; i <= end; i++) item(String(i), i, false, i === p.current_page);
                item('›', p.current_page + 1, p.current_page >= p.last_page);
            }

            function setMaterialFilter(materialId, typeId, label) {
                state.materialId = materialId;
                state.typeId = typeId;
                state.label = label;
                state.page = 1;
                $('#exchange-entries-filter').text(materialId !== '' ? ('· ' + label) : '');
                $('#exchange-clear-filter').toggleClass('d-none', materialId === '');
                load();
            }

            $('#exchange-summary-table').on('click', 'tbody tr[data-material]', function() {
                var $tr = $(this);
                var materialId = String($tr.attr('data-material') || '');
                var typeId = String($tr.attr('data-type') || '');
                if (materialId === state.materialId && typeId === state.typeId) {
                    setMaterialFilter('', '', '');
                } else {
                    setMaterialFilter(materialId, typeId, $tr.attr('data-label') || '');
                }
            });

            $('#exchange-clear-filter').on('click', function() {
                setMaterialFilter('', '', '');
            });

            $('#exchange-pagination').on('click', 'a[data-page]', function(e) {
                e.preventDefault();
                var $li = $(this).parent();
                if ($li.hasClass('disabled') || $li.hasClass('active')) return;
                state.page = parseInt($(this).attr('data-page'), 10) || 1;
                load();
            });

            $('#exchange-per-page, #exchange-start, #exchange-end').on('change', function() {
                state.page = 1;
                load();
            });

            $('#exchange-search').on('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    state.page = 1;
                    load();
                }, 350);
            });

            load();
        });
    </script>
@endpush
