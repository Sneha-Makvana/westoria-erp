@extends('layout.app')

@section('title', 'Manage Leads')

@section('content')
    @php
        $canViewLead = app('hasPermission')(32, 'view');
        $canAddLead = app('hasPermission')(32, 'add');
        $canEditLead = app('hasPermission')(32, 'edit');
        $canDeleteLead = app('hasPermission')(32, 'delete');
    @endphp

    <style>
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

        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }

        .dataTables_wrapper .row:first-child {
            display: none !important;
        }

        .lead-status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .lead-converted-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            color: #0f766e;
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            white-space: nowrap;
        }

        .status-new { background: #e0f2fe; color: #0369a1; }
        .status-qualified { background: #dcfce7; color: #15803d; }
        .status-working { background: #fef3c7; color: #b45309; }
        .status-ready-to-close { background: #ede9fe; color: #6d28d9; }
        .status-closed-won { background: #d1fae5; color: #047857; }
        .status-closed-lost { background: #fee2e2; color: #dc2626; }

        .lead-page {
            max-width: 100%;
            min-width: 0;
        }

        .lead-page .card {
            max-width: 100%;
            min-width: 0;
            overflow: hidden;
        }

        .lead-page .card-body {
            max-width: 100%;
            min-width: 0;
        }

        .lead-page .table-responsive {
            display: block;
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            position: relative;
            background: #fff;
        }

        .lead-page .table-responsive::-webkit-scrollbar {
            height: 7px;
        }

        .lead-page .table-responsive::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .lead-page .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .lead-page .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .lead-table {
            width: 100% !important;
            min-width: 860px;
            margin-bottom: 0 !important;
        }

        .lead-table td,
        .lead-table th {
            vertical-align: middle;
            padding: 10px 12px !important;
        }

        .lead-table tbody td {
            white-space: nowrap;
        }

        .lead-table tbody td:nth-child(3) {
            white-space: normal;
            min-width: 180px;
            max-width: 250px;
        }

        .lead-actions {
            display: inline-flex;
            gap: 6px;
            flex-wrap: nowrap;
        }

        .lead-mobile-summary {
            display: none;
        }

        .lead-card-list {
            display: none;
        }

        .lead-actions a,
        .lead-actions button {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 6px;
            color: #334155;
        }

        /* .lead-actions a:hover,
        .lead-actions button:hover {
            background: #f8fafc;
        } */

        .action-buttons {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }

        .action-buttons .btn {
            font-size: 12px;
            padding: 4px 8px;
        }

        .icon-btn {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            /* background: #f5f5f5; */
        }

        /* .icon-btn:hover {
            background: #ececec;
        } */

        .listing-action-dropdown {
            display: inline-flex;
            justify-content: center;
            width: 100%;
        }

        .listing-action-dropdown .action-menu-toggle {
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

        .listing-action-menu {
            border: 1px solid #e8ebed;
            border-radius: 6px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
            min-width: 168px;
            padding: 6px;
            z-index: 999999999999;
        }

        .listing-action-menu-floating {
            position: fixed !important;
            z-index: 999999999999 !important;
        }

        .listing-action-menu .dropdown-item {
            align-items: center;
            border-radius: 4px;
            color: #344054;
            display: flex;
            font-size: 12px;
            gap: 8px;
            padding: 7px 8px;
            width: 100%;
        }

        .listing-action-menu .dropdown-item i {
            color: #667085;
            font-size: 13px;
            text-align: center;
            width: 15px;
        }

        .listing-action-menu .dropdown-item.text-danger,
        .listing-action-menu .dropdown-item.text-danger i {
            color: #ea5455 !important;
        }

        .listing-action-menu .dropdown-item:hover {
            background: #f4f6f8;
            color: #1b2850;
        }

        .pagination-controls {
            display: flex;
        }

        .pagination .page-item .page-link {
            background-color: #5d6d7e;
            color: #fff;
            border: none;
            margin: 0 3px;
            padding: 4px 10px;
            font-weight: bold;
        }

        .pagination .page-item.active .page-link {
            background-color: #ff9f43 !important;
            color: #fff;
        }

        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            background-color: #fff;
            color: #6c757d;
            border: 1px solid #dee2e6;
        }

        .pagination .page-item.disabled .page-link {
            background-color: #fff !important;
            color: #dee2e6 !important;
            border: 1px solid #dee2e6 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }

        .lead-toolbar {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .lead-toolbar-search {
            flex: 1 1 320px;
            min-width: 220px;
            max-width: 320px;
        }

        .lead-toolbar-actions {
            display: flex;
            gap: 8px;
            margin-left: auto;
            flex-wrap: wrap;
        }

        .lead-export-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .lead-toolbar-actions .btn {
            white-space: nowrap;
        }

        .search-input {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .search-input input {
            padding-left: 35px !important;
            border-radius: 5px;
            width: 100%;
        }

        .btn-searchset {
            position: absolute;
            left: 10px;
            z-index: 10;
            padding: 0;
            top: 7px !important;
        }

        .details-column {
            display: none;
        }

        .lead-toggle {
            display: none;
        }

        .mobile-toggle-btn-table {
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
            line-height: 1;
            padding: 0;
            flex-shrink: 0;
            appearance: none;
            transition: all 0.3s;
        }

        .mobile-toggle-btn-table .toggle-icon {
            display: inline-block;
            width: 100%;
            text-align: center;
            line-height: 1;
            font-size: 20px;
            font-weight: 700;
            font-family: Arial, sans-serif;
            color: inherit;
            pointer-events: none;
            transform: translateY(-1px);
        }

        .mobile-toggle-btn-table:hover {
            background: #ff8c2e;
        }

        .mobile-toggle-btn-table.minus {
            background: #dc3545;
        }

        .mobile-toggle-btn-table.minus:hover {
            background: #c82333;
        }

        .lead-details-row {
            display: none;
        }

        .lead-details-row.show {
            display: table-row;
        }

        .lead-details-content {
            padding: 15px;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        .lead-detail-row-simple {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .lead-detail-row-simple:last-of-type {
            border-bottom: none;
        }

        .lead-detail-label-simple {
            font-weight: 600;
            color: #595b5d;
            font-size: 14px;
        }

        .lead-detail-value-simple {
            color: #1b2850;
            font-size: 14px;
            text-align: right;
        }

        .mobile-action-buttons-simple {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-start;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }

        .btn-icon-mobile,
        button.btn-icon-mobile {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #1b2850;
            background: transparent;
            transition: all 0.3s;
            border: 2px solid #1b2850;
        }

        .btn-icon-mobile:hover {
            background: #1b2850;
            color: #fff;
        }

        .btn-icon-mobile i {
            font-size: 16px;
        }

        .toggle-details i {
            color: #fff;
        }

        .lead-details-card {
            padding: 10px 4px 4px;
        }

        .lead-details-card p {
            margin-bottom: 6px;
        }

        @media (min-width: 768px) {
            .lead-page .table-responsive {
                display: block !important;
            }

            .table.datanew thead th,
            .table.datanew tbody td {
                display: table-cell !important;
            }

            .table.datanew thead th.details-column,
            .table.datanew tbody tr.lead-main-row td.details-column {
                display: none !important;
            }
        }

        /* iPad Pro and tablet optimizations (768px – 1199px) */
        @media (min-width: 768px) and (max-width: 1199px) {
            .lead-page .table-responsive {
                border: 1px solid #eef2f6;
                border-radius: 8px;
            }

            .lead-page .lead-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                flex-wrap: wrap;
            }

            .lead-page .lead-toolbar-search {
                flex: 1 1 240px;
                max-width: 320px;
                min-width: 180px;
            }

            .lead-page .lead-toolbar-actions {
                margin-left: auto;
                flex: 0 0 auto;
            }
        }

        @media (max-width: 767px) {
            .lead-toolbar-actions {
                width: 100%;
                margin-left: 0;
            }

            .lead-export-buttons {
                width: 100%;
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px;
            }

            .lead-export-buttons .btn {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                min-width: 0;
                height: 34px;
                padding: 0 6px;
                border: 0;
                border-radius: 8px;
                font-size: 11.5px;
                font-weight: 600;
                white-space: nowrap;
                line-height: 1;
                box-shadow: none;
            }

            .lead-export-buttons .btn i {
                margin: 0 !important;
                font-size: 13px;
            }

            .lead-export-buttons #exportLeadExcel {
                background: #e7f6ec !important;
                border-color: #e7f6ec !important;
                color: #198754 !important;
            }

            .lead-export-buttons #exportLeadPdf {
                background: #fdecee !important;
                border-color: #fdecee !important;
                color: #dc3545 !important;
            }

            .lead-page .card-body {
                padding: 10px !important;
            }

            .lead-page .table-responsive {
                display: none;
            }

            .lead-card-list {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .lead-card {
                padding: 12px;
                background: #fff;
                border-radius: 10px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            }

            .lead-card-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 8px;
            }

            .lead-card-name {
                min-width: 0;
                color: #1b2850;
                font-size: 15px;
                font-weight: 600;
                overflow-wrap: anywhere;
            }

            .lead-card-company {
                margin-top: 2px;
                color: #8b93a7;
                font-size: 11px;
            }

            .lead-card-badges {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 6px;
                margin: 6px 0 8px;
            }

            .lead-card-bottom {
                display: flex;
                align-items: flex-end;
                gap: 16px;
            }

            .lead-card-stat {
                min-width: 0;
            }

            .lead-card-stat span {
                display: block;
                color: #8b93a7;
                font-size: 11px;
            }

            .lead-card-stat strong {
                display: block;
                max-width: 105px;
                overflow: hidden;
                color: #1b2850;
                font-size: 12px;
                font-weight: 600;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .lead-card-actions {
                display: flex;
                margin-left: auto;
            }

            .lead-page .pagination-controls > div {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>

    <div class="content lead-page">
        <div class="page-header">
            <div class="page-title">
                <h4>Manage Leads</h4>
            </div>
            <div class="page-btn d-flex gap-2">
                @if ($canAddLead)
                    <a href="{{ route('lead.add') }}" class="btn btn-added btn-sm">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-1" alt="img">Add Lead
                    </a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="lead-toolbar">
                    {{-- <div class="fw-semibold">Active Enquiries</div> --}}
                    <div class="lead-toolbar-search">
                        <div class="search-input position-relative">
                            <a class="btn btn-searchset">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img">
                            </a>
                            <input type="text" id="search-input" class="form-control form-control-sm ps-5" placeholder="Search leads...">
                        </div>
                    </div>
                    <div class="lead-toolbar-actions">
                        @if ($canViewLead)
                            <div class="lead-export-buttons">
                                <button id="exportLeadExcel" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-excel me-1"></i> Excel
                                </button>
                                <button id="exportLeadPdf" class="btn btn-sm btn-danger">
                                    <i class="fas fa-file-pdf me-1"></i> PDF
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover lead-table datanew">
                        <thead class="table-light">
                            <tr>
                                <th style="width:70px;">Sr.No</th>
                                <th class="details-column" style="width:56px;"></th>
                                <th>Lead Name</th>
                                <th>Lead Source</th>
                                <th>Assigned To</th>
                                <th>Created At</th>
                                <th>Status</th>
                                <th style="width:120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="leadTableBody"></tbody>
                    </table>
                </div>

                <div class="lead-card-list d-md-none" id="leadCardList"></div>

                <div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted">Show per page:</span>
                        <select id="per-page-select" class="form-select form-select-sm" style="width: auto;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="text-muted">
                            <span id="pagination-from">0</span> - <span id="pagination-to">0</span> of <span id="pagination-total">0</span>
                        </span>
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="pagination-numbers"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    const canViewLead = @json((bool) $canViewLead);
    const canEditLead = @json((bool) $canEditLead);
    const canDeleteLead = @json((bool) $canDeleteLead);

    let currentPage = 1;
    let lastPage = 1;
    let perPage = 10;
    let searchQuery = '';

    function statusClass(status) {
        return 'status-' + String(status || '').toLowerCase().replace(/\s+/g, '-');
    }

    function formatDate(value) {
        if (!value) return 'N/A';
        const date = new Date(value);
        if (isNaN(date.getTime())) return value;

        return date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }).replace(/ /g, '-').toLowerCase();
    }

    function renderPagination() {
        const pagination = $('#pagination-numbers');
        pagination.empty();

        const createItem = (label, page, disabled = false, active = false, action = '') => {
            const li = $(`<li class="page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}"></li>`);
            const a = $(`<a href="javascript:void(0);" class="page-link" data-page="${page}" data-action="${action}">${label}</a>`);
            a.on('click', function(e) {
                e.preventDefault();
                if (!disabled && !active) {
                    const targetPage = parseInt($(this).data('page'), 10);
                    const targetAction = $(this).data('action');

                    if (targetAction === 'next-group') {
                        if (targetPage && targetPage <= lastPage) fetchLeads(targetPage);
                        return;
                    }

                    if (targetAction === 'prev-group') {
                        const prevStartPage = Math.max(1, targetPage - 2);
                        if (prevStartPage >= 1 && prevStartPage <= lastPage) fetchLeads(prevStartPage);
                        return;
                    }

                    if (targetPage && targetPage !== currentPage && targetPage >= 1 && targetPage <= lastPage) {
                        fetchLeads(targetPage);
                    }
                }
            });
            li.append(a);
            return li;
        };

        pagination.append(createItem('Previous', Math.max(1, currentPage - 1), currentPage === 1));

        const visiblePageCount = 2;
        const startPage = Math.floor((currentPage - 1) / visiblePageCount) * visiblePageCount + 1;
        const endPage = Math.min(lastPage, startPage + visiblePageCount - 1);

        if (startPage > 1) {
            pagination.append(createItem('..', startPage - 1, false, false, 'prev-group'));
        }

        for (let i = startPage; i <= endPage; i++) {
            pagination.append(createItem(String(i), i, false, i === currentPage));
        }

        if (endPage < lastPage) {
            pagination.append(createItem('..', endPage + 1, false, false, 'next-group'));
        }

        pagination.append(createItem('Next', Math.min(lastPage, currentPage + 1), currentPage === lastPage));
        $('.pagination-controls').show();
    }

    function positionListingActionMenu(dropdownEl) {
        const $dropdown = $(dropdownEl);
        const $menu = $dropdown.data('floating-menu') || $dropdown.find('.listing-action-menu');
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

    $(document).on('show.bs.dropdown', '.listing-action-dropdown', function() {
        const $dropdown = $(this);
        const $menu = $dropdown.find('.listing-action-menu');

        if (!$menu.length) {
            return;
        }

        $dropdown.data('floating-menu', $menu);
        $dropdown.data('menu-parent', $menu.parent());
        $dropdown.data('menu-next', $menu.next());
        $menu.addClass('listing-action-menu-floating').appendTo('body');
    });

    $(document).on('shown.bs.dropdown', '.listing-action-dropdown', function() {
        positionListingActionMenu(this);
    });

    $(document).on('hidden.bs.dropdown', '.listing-action-dropdown', function() {
        const $dropdown = $(this);
        const $menu = $dropdown.data('floating-menu');
        const $parent = $dropdown.data('menu-parent');
        const $next = $dropdown.data('menu-next');

        if ($menu && $menu.length && $parent && $parent.length) {
            $menu.removeClass('listing-action-menu-floating').removeAttr('style');
            if ($next && $next.length && $.contains($parent[0], $next[0])) {
                $menu.insertBefore($next);
            } else {
                $parent.append($menu);
            }
        }

        $dropdown.removeData('floating-menu menu-parent menu-next');
    });

    $(window).on('scroll resize', function() {
        $('.listing-action-dropdown.show').each(function() {
            positionListingActionMenu(this);
        });
    });

    function renderRows(items) {
        const tbody = $('#leadTableBody');
        tbody.empty();

        if (!items || items.length === 0) {
            tbody.append(`<tr><td colspan="8" class="text-center text-muted">No leads found</td></tr>`);
            return;
        }

        items.forEach((item, index) => {
            const status = item.lead_status || 'New';
            const assignedTo = item.assigned_user?.name || item.assignedUser?.name || 'N/A';
            const createdAt = item.created_at_display
                ? String(item.created_at_display).toLowerCase()
                : formatDate(item.created_at);

            const leadToggle = `
                <button class="mobile-toggle-btn-table" onclick="toggleLeadDetails('${item.id}')" data-lead-id="${item.id}" aria-label="Toggle lead details">
                    <span class="toggle-icon">+</span>
                </button>
            `;

            const actionItems = `
                ${!item.converted_customer_id ? `<a class="dropdown-item convert-lead" data-id="${item.id}" href="javascript:void(0);"><i class="fa fa-user-plus text-success"></i> Convert</a>` : ''}
                ${canViewLead ? `<a class="dropdown-item" href="/lead-view/${item.id}"><i class="fa fa-eye"></i> View</a>` : ''}
                ${canEditLead ? `<a class="dropdown-item" href="/edit-lead/${item.id}"><i class="fa fa-pen"></i> Edit</a>` : ''}
                ${canDeleteLead ? `<a class="dropdown-item text-danger confirm-text delete-lead" data-id="${item.id}" href="javascript:void(0);"><i class="fa fa-trash"></i> Delete</a>` : ''}
            `;
            const actionButtons = actionItems.trim() ? `
                <div class="dropdown listing-action-dropdown">
                    <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end listing-action-menu">
                        ${actionItems}
                    </div>
                </div>
            ` : '<span class="text-muted">N/A</span>';

            const mobileSummary = `
                <div class="lead-mobile-summary">
                    <span class="mobile-lead-name">
                        ${item.name || 'N/A'}
                        ${item.converted_customer_id ? '<span class="lead-converted-badge ms-1">Converted</span>' : ''}
                    </span>
                    <span class="mobile-lead-company">${item.company_name || ''}</span>
                </div>
            `;

            tbody.append(`
                <tr class="lead-main-row">
                    <td>
                        ${((currentPage - 1) * perPage) + index + 1}
                        ${mobileSummary}
                    </td>
                    <td class="details-column">${leadToggle}</td>
                    <td>
                        <div class="fw-semibold d-flex align-items-center flex-wrap gap-2">
                            <a href="/lead-view/${item.id}"><span>${item.name || 'N/A'}</span></a>
                            ${item.converted_customer_id ? '<span class="lead-converted-badge">Converted</span>' : ''}
                        </div>
                        <div class="text-muted small">${item.company_name || ''}</div>
                    </td>
                    <td>${item.lead_source || 'N/A'}</td>
                    <td>${assignedTo}</td>
                    <td>${createdAt}</td>
                    <td><span class="lead-status-badge ${statusClass(status)}">${status}</span></td>
                    <td>${actionButtons}</td>
                </tr>
                <tr class="lead-details-row" data-lead-id="${item.id}">
                    <td colspan="8">
                        <div class="lead-details-content">
                            <div class="lead-detail-row-simple">
                                <span class="lead-detail-label-simple">Lead Source:</span>
                                <span class="lead-detail-value-simple">${item.lead_source || 'N/A'}</span>
                            </div>
                            <div class="lead-detail-row-simple">
                                <span class="lead-detail-label-simple">Assigned To:</span>
                                <span class="lead-detail-value-simple">${assignedTo}</span>
                            </div>
                            <div class="lead-detail-row-simple">
                                <span class="lead-detail-label-simple">Created At:</span>
                                <span class="lead-detail-value-simple">${createdAt}</span>
                            </div>
                            <div class="lead-detail-row-simple">
                                <span class="lead-detail-label-simple">Status:</span>
                                <span class="lead-detail-value-simple"><span class="lead-status-badge ${statusClass(status)}">${status}</span></span>
                            </div>
                            <div class="mobile-action-buttons-simple">
                                ${!item.converted_customer_id ? `<a class="btn-icon-mobile convert-lead" data-id="${item.id}" href="javascript:void(0);" title="Convert to Customer"><i class="fa fa-user-plus"></i></a>` : ''}
                                ${canViewLead ? `<a class="btn-icon-mobile" href="/lead-view/${item.id}" title="View"><i class="fa fa-eye"></i></a>` : ''}
                                ${canEditLead ? `<a class="btn-icon-mobile" href="/edit-lead/${item.id}" title="Edit"><i class="fa fa-pen"></i></a>` : ''}
                                ${canDeleteLead ? `<a class="btn-icon-mobile delete-lead" data-id="${item.id}" href="javascript:void(0);" title="Delete"><i class="fa fa-trash"></i></a>` : ''}
                            </div>
                        </div>
                    </td>
                </tr>
            `);
        });
    }

    function escapeLeadHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function(character) {
            return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[character];
        });
    }

    function renderLeadCards(items) {
        const list = $('#leadCardList');
        if (!items || items.length === 0) {
            list.html('<div class="text-center text-muted py-3">No leads found</div>');
            return;
        }

        const cards = items.map(function(item) {
            const status = item.lead_status || 'New';
            const assignedTo = item.assigned_user?.name || item.assignedUser?.name || 'N/A';
            const createdAt = item.created_at_display
                ? String(item.created_at_display).toLowerCase()
                : formatDate(item.created_at);
            const name = escapeLeadHtml(item.name || 'N/A');
            const company = escapeLeadHtml(item.company_name || '');
            const source = escapeLeadHtml(item.lead_source || 'N/A');
            const assigned = escapeLeadHtml(assignedTo);
            const date = escapeLeadHtml(createdAt);
            const statusText = escapeLeadHtml(status);
            const convertedBadge = item.converted_customer_id
                ? '<span class="lead-converted-badge">Converted</span>'
                : '';
            const actionItems = `
                ${!item.converted_customer_id ? `<a class="dropdown-item convert-lead" data-id="${item.id}" href="javascript:void(0);"><i class="fa fa-user-plus text-success"></i> Convert</a>` : ''}
                ${canViewLead ? `<a class="dropdown-item" href="/lead-view/${item.id}"><i class="fa fa-eye"></i> View</a>` : ''}
                ${canEditLead ? `<a class="dropdown-item" href="/edit-lead/${item.id}"><i class="fa fa-pen"></i> Edit</a>` : ''}
                ${canDeleteLead ? `<a class="dropdown-item text-danger confirm-text delete-lead" data-id="${item.id}" href="javascript:void(0);"><i class="fa fa-trash"></i> Delete</a>` : ''}
            `;
            const actions = actionItems.trim() ? `
                <div class="dropdown listing-action-dropdown">
                    <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions"><i class="fas fa-ellipsis-h"></i></button>
                    <div class="dropdown-menu dropdown-menu-end listing-action-menu">${actionItems}</div>
                </div>` : '';

            return `<div class="lead-card">
                <div class="lead-card-top">
                    <div class="lead-card-name-wrap">
                        <a class="lead-card-name" href="/lead-view/${item.id}">${name}</a>
                        ${company ? `<div class="lead-card-company">${company}</div>` : ''}
                    </div>
                    ${convertedBadge}
                </div>
                <div class="lead-card-bottom">
                    <div class="lead-card-stat"><span>Lead Source</span><strong>${source}</strong></div>
                    <div class="lead-card-stat"><span>Assigned To</span><strong>${assigned}</strong></div>
                    <div class="lead-card-stat"><span>Created At</span><strong>${date}</strong></div>
                    <div class="lead-card-actions">${actions}</div>
                </div>
            </div>`;
        }).join('');

        list.html(cards);
    }

    function fetchLeads(page = 1) {
        const authToken = localStorage.getItem('authToken');
        const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
        let url = `/api/getAllLeads?page=${page}&per_page=${perPage}&search=${encodeURIComponent(searchQuery)}`;
        if (selectedSubAdminId) url += `&selectedSubAdminId=${selectedSubAdminId}`;

        $.ajax({
            url,
            method: 'GET',
            headers: { 'Authorization': 'Bearer ' + authToken },
            success: function(response) {
                if (!response.status) return;

                currentPage = response.pagination.current_page;
                lastPage = response.pagination.last_page;

                const total = response.pagination.total;
                const from = total === 0 ? 0 : ((currentPage - 1) * perPage) + 1;
                const to = Math.min(currentPage * perPage, total);

                $('#pagination-from').text(from);
                $('#pagination-to').text(to);
                $('#pagination-total').text(total);

                renderRows(response.data);
                renderLeadCards(response.data);
                renderPagination();
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unable to load leads.'
                });
            }
        });
    }

    $(document).ready(function() {
        fetchLeads(1);

        $('#search-input').on('keyup', function() {
            searchQuery = $(this).val();
            fetchLeads(1);
        });

        $('#per-page-select').on('change', function() {
            perPage = parseInt($(this).val(), 10) || 10;
            fetchLeads(1);
        });

        $(document).on('click', '.delete-lead', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Delete Lead?',
                text: 'This lead will be moved to deleted state.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (!result.isConfirmed) return;

                const authToken = localStorage.getItem('authToken');
                const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

                $.ajax({
                    url: `/api/lead/${id}/delete${selectedSubAdminId ? '?selectedSubAdminId=' + selectedSubAdminId : ''}`,
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + authToken },
                    success: function(resp) {
                        if (resp.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: resp.message || 'Lead deleted successfully.'
                            }).then(() => fetchLeads(currentPage));
                        }
                    }
                });
            });
        });

        $(document).on('click', '.convert-lead', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Convert to Customer?',
                text: 'This lead will be copied into customer records.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Convert'
            }).then((result) => {
                if (!result.isConfirmed) return;

                const authToken = localStorage.getItem('authToken');
                const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
                const formData = new FormData();
                if (selectedSubAdminId) formData.append('selectedSubAdminId', selectedSubAdminId);

                $.ajax({
                    url: `/api/lead/${id}/convert-to-customer`,
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + authToken },
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        if (resp.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Converted',
                                text: resp.message
                            }).then(() => fetchLeads(currentPage));
                            return;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Oops',
                            text: resp.message || 'Unable to convert lead.'
                        });
                    },
                    error: function(xhr) {
                        const validationErrors = xhr.responseJSON?.errors;
                        const message = validationErrors
                            ? Object.values(validationErrors).flat().join(' ')
                            : (xhr.responseJSON?.message || 'Unable to convert lead.');
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops',
                            text: message
                        });
                    }
                });
            });
        });

        $('#exportLeadExcel').on('click', function() {
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            const authToken = localStorage.getItem('authToken');
            let url = `/api/lead/export-excel?search=${encodeURIComponent(searchQuery)}`;
            if (selectedSubAdminId) url += `&selectedSubAdminId=${selectedSubAdminId}`;

            $.ajax({
                url,
                method: 'GET',
                headers: { 'Authorization': 'Bearer ' + authToken },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Exporting Excel...',
                        text: 'Please wait while we generate your file.',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.status && response.file_url) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Excel Exported!',
                            text: 'Click the button below to download your Excel file.',
                            showConfirmButton: true,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Download File'
                        }).then(() => {
                            const link = document.createElement('a');
                            link.href = response.file_url;
                            link.download = response.file_name || 'Leads.xlsx';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Export Failed!',
                            text: 'Unable to export Excel file. Please try again.'
                        });
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Export failed. Please try again.'
                    });
                }
            });
        });

        $('#exportLeadPdf').on('click', function() {
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            const authToken = localStorage.getItem('authToken');
            let url = `/api/lead/export-pdf?search=${encodeURIComponent(searchQuery)}`;
            if (selectedSubAdminId) url += `&selectedSubAdminId=${selectedSubAdminId}`;

            $.ajax({
                url,
                method: 'GET',
                headers: { 'Authorization': 'Bearer ' + authToken },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Generating PDF...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.status && response.file_url) {
                        Swal.fire({
                            icon: 'success',
                            title: 'PDF Generated Successfully!',
                            text: 'Your lead PDF is ready.',
                            showConfirmButton: true,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Download PDF'
                        }).then(() => {
                            const link = document.createElement('a');
                            link.href = response.file_url;
                            link.download = response.file_name || 'Leads.pdf';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed!',
                            text: 'Could not generate the PDF. Please try again.'
                        });
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Export PDF failed. Please try again.'
                    });
                }
            });
        });

        $(document).on('click', '.toggle-details', function() {
            const icon = $(this).find('i');
            if (icon.hasClass('fa-plus-circle')) {
                icon.removeClass('fa-plus-circle')
                    .addClass('fa-minus-circle')
                    .css('color', 'red');
            } else {
                icon.removeClass('fa-minus-circle')
                    .addClass('fa-plus-circle')
                    .css('color', '#ff9f43');
            }
        });

        window.toggleLeadDetails = function(leadId) {
            const detailsRow = $(`.lead-details-row[data-lead-id="${leadId}"]`);
            const btn = $(`.mobile-toggle-btn-table[data-lead-id="${leadId}"]`);
            const icon = btn.find('.toggle-icon');

            if (detailsRow.hasClass('show')) {
                detailsRow.removeClass('show');
                btn.removeClass('minus');
                icon.text('+');
            } else {
                detailsRow.addClass('show');
                btn.addClass('minus');
                icon.text('-');
            }
        };
    });
</script>
@endpush
