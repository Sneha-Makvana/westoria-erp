@extends('layout.app')

@section('title', 'Inventory List')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />
<style>
    /* ---------- Base / desktop ---------- */
    .search-input {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-input input { border-radius: 5px; }

    .btn-searchset {
        position: absolute;
        left: 10px;
        z-index: 10;
        padding: 0;
        top: 18px !important;
    }

    .hs-search-icon { display: none; }

    .dataTables_filter,
    .dataTables_length,
    .dataTables_info,
    .dataTables_paginate {
        display: none !important;
    }

    .dataTables_wrapper .row:first-child { display: none !important; }

    .dataTables_wrapper {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    #history-table { width: 100% !important; }

    .table th,
    .table td { padding: 12px 10px; }

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

    .pagination .page-item.active .page-link:hover { background-color: #e68a35 !important; }

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

    /* ---------- New History UI (mobile + tablet) ---------- */
    .hs-quick-links,
    .hs-card { display: none; }

    @media (max-width: 1024.98px),
        (width: 1032px) and (height: 1376px),
        (width: 1280px) and (height: 800px) {

        /* Quick header */
        .hs-quick-links {
            display: block;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            padding: 12px 10px;
            margin-bottom: 10px;
        }

        .hs-quick-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .hs-quick-title {
            font-size: 15px;
            font-weight: 600;
            color: #1b2850;
            margin-left: 4px;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .hs-quick-head .btn { height: 30px; padding: 0 10px !important; font-size: 12px !important; font-weight: 600; flex-shrink: 0; }

        /* default page-header hide */
        .content:has(.hs-quick-links) > .page-header { display: none !important; }
        .content:has(.hs-quick-links) { padding-left: 6px !important; padding-right: 6px !important; }

        /* card wrapper transparent */
        .content:has(.hs-quick-links) > .card {
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
            margin: 0 !important;
        }

        .content:has(.hs-quick-links) > .card > .card-body { padding: 0 !important; }

        /* search box white card */
        .content:has(.hs-quick-links) .card-body > .hs-search-row {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            padding: 8px 10px;
            margin: 0 0 8px !important;
        }

        .hs-search-row > div {
            flex: 0 0 100% !important;
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
        }

        .hs-search-icon { display: inline-block; }

        .content:has(.hs-quick-links) .hs-search-row .btn-searchset { top: 9px !important; }

        .content:has(.hs-quick-links) .hs-search-row #history-search-input {
            width: 100% !important;
            height: 38px !important;
            font-size: 13px;
            border-radius: 8px;
            padding-left: 35px !important;
        }

        /* Table -> Cards */
        .table-responsive { overflow: visible !important; border: 0 !important; padding: 0 !important; }

        table#history-table,
        table#history-table tbody {
            display: block !important;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            border: 0 !important;
        }

        table#history-table thead { display: none !important; }

        table#history-table tbody tr {
            display: block !important;
            background: #fff;
            border: 1px solid #eef0f4 !important;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .06);
            margin: 0 0 10px;
            padding: 10px 12px !important;
        }

        /* hide all td except first (card) */
        table#history-table tbody tr > td { display: none !important; }

        table#history-table tbody tr > td:first-child {
            display: block !important;
            width: 100% !important;
            padding: 0 !important;
            border: 0 !important;
            background: transparent !important;
        }

        table#history-table tbody td.dataTables_empty {
            display: block !important;
            text-align: center;
            padding: 16px !important;
        }

        table#history-table tbody td:first-child .hs-desktop-only { display: none !important; }

        .hs-card { display: block; }

        .hs-card-line1 {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .hs-card-type {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 12px;
            background: #fff3df;
            color: #e8960c;
            max-width: 70%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .hs-card-qty { font-size: 15px; font-weight: 700; }
        .hs-card-qty.pos { color: #1b8a52; }
        .hs-card-qty.neg { color: #dc3545; }

        .hs-card-line2 {
            display: flex;
            flex-wrap: wrap;
            gap: 2px 12px;
            margin-top: 6px;
            font-size: 11px;
            color: #595b5d;
        }

        .hs-card-line2 span { white-space: nowrap; }
        .hs-card-line2 i,
        .hs-card-remarks i { width: 14px; color: #8b93a7; }

        .hs-card-remarks {
            margin-top: 4px;
            font-size: 11px;
            color: #595b5d;
            word-break: break-word;
        }
    }

    /* Tablet: 2 column grid */
    @media screen and (min-width: 768px) and (max-width: 1024.98px),
        screen and (width: 1032px) and (height: 1376px),
        screen and (width: 1280px) and (height: 800px) {
        table#history-table tbody {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            align-items: start;
        }

        table#history-table tbody tr { margin: 0 !important; }
        table#history-table tbody tr:has(> td.dataTables_empty) { grid-column: 1 / -1; }
    }

    /* iPad Mini / small tablet */
    @media screen and (min-width: 768px) and (max-width: 1024.98px) {
        .page-wrapper:has(.hs-quick-links) { padding-top: 0 !important; }
        .content:has(.hs-quick-links) { padding-top: 6px !important; margin-top: 0 !important; }
    }

    /* iPad Pro / Nest Hub Max: fixed header niche content */
    @media screen and (width: 1032px) and (height: 1376px),
        screen and (width: 1280px) and (height: 800px) {
        .content:has(.hs-quick-links) { padding-top: 16px !important; }
    }
</style>

@if (session('error'))
<div class="alert alert-danger" id="error-message">
    {{ session('error') }}
</div>
@endif

<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>
                View Inventory
                @if (!empty($product?->name))
                    - {{ $product->name }}
                @endif
            </h4>
        </div>
        <div class="page-btn">
            @if (app('hasPermission')(17, 'view'))
            <a href="{{ route('inventory.list') }}" class="btn btn-sm btn-added"><i class="fa fa-arrow-left me-1"></i> Back</a>
            @endif
        </div>
    </div>

    {{-- Mobile / tablet header --}}
    <div class="hs-quick-links">
        <div class="hs-quick-head">
            <div class="hs-quick-title" id="hs-quick-title">
                View Inventory
                @if (!empty($product?->name))
                    - {{ $product->name }}
                @endif
            </div>
            @if (app('hasPermission')(17, 'view'))
                <a href="{{ route('inventory.list') }}" class="btn btn-sm btn-added">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3 hs-search-row">
                <div class="col-md-3 col-6">
                    <div class="search-input position-relative">
                        <a class="btn btn-searchset position-absolute">
                            <img class="hs-search-icon" src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img">
                        </a>
                        <input type="text" id="history-search-input" class="form-control form-control-sm" placeholder="Search...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="history-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Current Stock</th>
                            <th>Quantity</th>
                            <th>Create By</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3" style="display: none;">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
                    <select id="per-page-select" class="form-select form-select-sm" style="width: auto; border: 1px solid #ddd;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="ms-3" style="font-size: 14px; color: #555;">
                        <span id="pagination-from">0</span> - <span id="pagination-to">0</span> of <span id="pagination-total">0</span> items
                    </span>
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0" id="pagination-numbers"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    var historyTable;
    let currentPage = 1;
    let lastPage = 1;
    let perPage = 10;
    let searchQuery = '';

    function esc(v) {
        return String(v ?? '').replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    $(document).ready(function() {
        var authToken = localStorage.getItem("authToken");
        let urlObj = new URL(window.location.href);
        let pathSegments = urlObj.pathname.split('/').filter(Boolean);
        let productId = pathSegments[pathSegments.length - 1];
        let variantId = urlObj.searchParams.get('variant_id');

        historyTable = $('#history-table').DataTable({
            searching: false,
            paging: false,
            info: false,
            lengthChange: false,
            ordering: false,
            autoWidth: false,
            dom: 't'
        });

        function fetchHistory(page = 1) {
            let apiUrl = `/api/getQuantityHistory_inventory/${productId}?page=${page}&per_page=${perPage}`;
            if (variantId) {
                apiUrl += `&variant_id=${variantId}`;
            }
            if (searchQuery) {
                apiUrl += `&search=${encodeURIComponent(searchQuery)}`;
            }

            $.ajax({
                url: apiUrl,
                type: "GET",
                headers: { "Authorization": "Bearer " + authToken },
                success: function(response) {
                    if (response.status) {
                        let history = response.history || [];
                        let pagination = response.pagination;

                        currentPage = pagination.current_page;
                        lastPage = pagination.last_page;
                        updatePaginationUI(pagination);

                        let tableBody = [];

                        if (history.length > 0 && history[0].variant_name) {
                            const newTitle = 'View Inventory - ' + (response.product || '') + ' (' + history[0].variant_name + ')';
                            $('.page-title h4').text(newTitle);
                            $('#hs-quick-title').text(newTitle);
                        }

                        $.each(history, function(index, item) {
                            let initialStock = item.initial_stock ?? '-';
                            let currentStock = item.current_stock ?? '-';
                            let type = item.type ?? 'System';
                            let createdBy = item.name ?? 'System';
                            let signedQty = item.quantity ?? 'N/A';
                            let remarks = item.remarks ?? '';
                            let date = item.date ?? '-';

                            const isNeg = String(signedQty).trim().startsWith('-');
                            const hasRemarks = remarks && remarks !== 'N/A';

                            const firstColumn = `
                                <span class="hs-desktop-only">${esc(type)}</span>
                                <div class="hs-card">
                                    <div class="hs-card-line1">
                                        <span class="hs-card-type">${esc(type)}</span>
                                        <span class="hs-card-qty ${isNeg ? 'neg' : 'pos'}">${esc(signedQty)}</span>
                                    </div>
                                    <div class="hs-card-line2">
                                        <span><i class="fas fa-cubes"></i> ${esc(initialStock)} &rarr; ${esc(currentStock)}</span>
                                        <span><i class="fas fa-user"></i> ${esc(createdBy)}</span>
                                        <span><i class="fas fa-calendar"></i> ${esc(date)}</span>
                                    </div>
                                    ${hasRemarks ? `<div class="hs-card-remarks"><i class="fas fa-comment"></i> ${esc(remarks)}</div>` : ''}
                                </div>`;

                            tableBody.push([
                                firstColumn,
                                currentStock,
                                signedQty,
                                createdBy,
                                date
                            ]);
                        });

                        historyTable.clear().rows.add(tableBody).draw();
                        $('.pagination-controls').show();
                    } else {
                        historyTable.clear().draw();
                        $('.pagination-controls').hide();
                    }
                },
                error: function() {
                    historyTable.clear().draw();
                    $('.pagination-controls').hide();
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load history', confirmButtonColor: '#ff9f43' });
                }
            });
        }

        function updatePaginationUI(pagination) {
            let from = (pagination.current_page - 1) * pagination.per_page + 1;
            let to = pagination.current_page * pagination.per_page;
            if (to > pagination.total) to = pagination.total;
            if (pagination.total === 0) from = 0;

            $('#pagination-from').text(from);
            $('#pagination-to').text(to);
            $('#pagination-total').text(pagination.total);

            let paginationHtml = '';

            paginationHtml += `
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
            $('.pagination-controls').show();
        }

        $('#history-search-input').on('keyup', function() {
            searchQuery = $(this).val();
            fetchHistory(1);
        });

        $('#per-page-select').on('change', function() {
            perPage = $(this).val();
            fetchHistory(1);
        });

        $(document).on('click', '#pagination-numbers .page-link', function(e) {
            e.preventDefault();
            let page = $(this).data('page');
            let action = $(this).data('action');

            if (action === 'next-group') {
                if (page && page <= lastPage) {
                    fetchHistory(page);
                }
                return;
            }

            if (action === 'prev-group') {
                let prevStartPage = Math.max(1, page - 2);
                if (prevStartPage >= 1 && prevStartPage <= lastPage) {
                    fetchHistory(prevStartPage);
                }
                return;
            }

            if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                fetchHistory(page);
            }
        });

        fetchHistory(1);
    });
</script>
@endpush