@extends('layout.app')

@section('title', 'Banks List')

@section('content')
    <style>
        .sorting_1 {
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
        }

        .toggle-details {
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-details i {
            transition: transform 0.2s ease;
        }

        .toggle-details:hover i {
            transform: scale(1.15);
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

        table.datanew1 td:nth-child(1),
        table.datanew1 td:nth-child(5) {
            word-break: break-word;
            overflow-wrap: anywhere;
            line-height: 1.35;
        }

        table.datanew1 td:nth-child(3),
        table.datanew1 td:nth-child(4),
        table.datanew1 td:nth-child(6),
        table.datanew1 td:nth-child(7),
        table.datanew1 td:nth-child(8) {
            white-space: nowrap;
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
            border-radius: 8px;
        }

        .btn-searchset {
            position: absolute;
            left: 10px;
            z-index: 10;
            top: 9px !important;
            padding: 0;
        }

        /* Desktop specific styling */
        @media (min-width: 1200px) {
            table.datanew1 thead th,
            table.datanew1 tbody td {
                display: table-cell !important;
            }

            table.datanew1 thead th.details-column,
            table.datanew1 tbody td:nth-child(2) {
                display: none !important;
            }
        }

        /* Mobile & Tablet Devices (< 1200px: Mobile phones, iPad Mini, iPad Air, iPad Pro) */
        @media screen and (max-width: 1199.98px) {
            .content {
                padding: 10px 10px 80px !important;
            }

            .page-header {
                display: none !important;
            }

            .table-container {
                display: none !important;
            }

            .card {
                border-radius: 12px !important;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .05) !important;
            }

            .card-body {
                padding: 12px !important;
            }

            /* Mobile Header */
            .bk-m-head {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
                padding: 12px 14px;
                margin-bottom: 10px;
            }

            .bk-m-head-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
            }

            .bk-m-title {
                font-size: 16px;
                font-weight: 700;
                color: #1b2850;
            }

            .bk-m-head-actions .btn {
                display: inline-flex;
                align-items: center;
                height: 32px;
                padding: 0 12px !important;
                font-size: 12px !important;
                font-weight: 600;
                border-radius: 8px !important;
                background: #ff9f43 !important;
                border: 1px solid #ff9f43 !important;
                color: #fff !important;
            }

            .search-set {
                width: 100% !important;
                margin: 0 !important;
            }

            .search-input {
                width: 100% !important;
            }

            .search-input input {
                width: 100% !important;
            }

            /* Mobile Cards */
            .bk-m-cards {
                display: flex;
                flex-direction: column;
                gap: 10px;
                margin-top: 10px;
            }

            .bk-card {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 14px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .05);
                transition: transform .15s ease, box-shadow .15s ease;
            }

            .bk-card-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 8px;
                padding-bottom: 10px;
                border-bottom: 1px dashed #e2e8f0;
                margin-bottom: 10px;
            }

            .bk-card-bank-info {
                display: flex;
                align-items: center;
                gap: 10px;
                min-width: 0;
            }

            .bk-icon {
                width: 40px;
                height: 40px;
                border-radius: 10px;
                background: linear-gradient(135deg, #fff7ef, #ffedd5);
                color: #f97316;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 17px;
                flex-shrink: 0;
            }

            .bk-name {
                font-size: 14.5px;
                font-weight: 700;
                color: #1b2850;
                margin: 0 0 2px;
                line-height: 1.3;
            }

            .bk-branch {
                display: block;
                font-size: 11.5px;
                color: #64748b;
            }

            .bk-badge {
                display: inline-flex;
                align-items: center;
                padding: 3px 8px;
                border-radius: 999px;
                font-size: 11px;
                font-weight: 600;
                white-space: nowrap;
            }

            .bk-badge.active {
                background: #dcfce7;
                color: #166534;
            }

            .bk-badge.inactive {
                background: #fee2e2;
                color: #991b1b;
            }

            .bk-card-body {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px 12px;
                margin-bottom: 10px;
            }

            .bk-stat-item {
                min-width: 0;
            }

            .bk-stat-item:last-child {
                grid-column: span 2;
            }

            .bk-stat-label {
                display: block;
                font-size: 11px;
                color: #64748b;
                margin-bottom: 2px;
                font-weight: 500;
            }

            .bk-stat-val {
                display: block;
                font-size: 13px;
                font-weight: 600;
                color: #1e293b;
                word-break: break-all;
            }

            .bk-card-foot {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding-top: 10px;
                border-top: 1px solid #f1f5f9;
            }

            .bk-actions {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .bk-act-btn {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
                transition: all .15s ease;
                border: 1px solid transparent;
                text-decoration: none;
            }

            .bk-act-btn.edit {
                background: #eff6ff;
                color: #2563eb;
                border-color: #dbeafe;
            }

            .bk-act-btn.delete {
                background: #fef2f2;
                color: #dc2626;
                border-color: #fee2e2;
            }

            .pagination-controls {
                flex-direction: column !important;
                gap: 12px;
                align-items: center !important;
                margin-top: 16px !important;
            }

            .pagination-controls > div {
                margin-bottom: 0 !important;
                justify-content: center;
                flex-wrap: wrap;
            }
        }

        /* Tablet Optimizations: iPad Mini & iPad Pro (768px - 1199.98px) */
        @media screen and (min-width: 768px) and (max-width: 1199.98px) {
            .content {
                padding: 16px 16px 80px !important;
            }

            .bk-m-cards {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 14px;
            }

            .bk-card {
                margin-bottom: 0;
            }
        }

        /* Dark Mode Overrides */
        :root[data-theme="dark"] .bk-m-head,
        :root[data-theme="dark"] .bk-card {
            background: #1e293b !important;
            border-color: #334155 !important;
        }

        :root[data-theme="dark"] .bk-m-title,
        :root[data-theme="dark"] .bk-name,
        :root[data-theme="dark"] .bk-stat-val {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .bk-card-head,
        :root[data-theme="dark"] .bk-card-foot {
            border-color: #334155 !important;
        }

        :root[data-theme="dark"] .bk-branch,
        :root[data-theme="dark"] .bk-stat-label {
            color: #94a3b8 !important;
        }

        :root[data-theme="dark"] .bk-icon {
            background: #0f172a !important;
            color: #fb923c !important;
        }

        :root[data-theme="dark"] .bk-act-btn.edit {
            background: #1e3a8a !important;
            color: #93c5fd !important;
            border-color: #1e40af !important;
        }

        :root[data-theme="dark"] .bk-act-btn.delete {
            background: #7f1d1d !important;
            color: #fca5a5 !important;
            border-color: #991b1b !important;
        }
    </style>

    <div class="content">
        <!-- Mobile Header (<1200px) -->
        <div class="bk-m-head d-xl-none">
            <div class="bk-m-head-top">
                <div class="bk-m-title">All Bank Master</div>
                <div class="bk-m-head-actions">
                    @if (app('hasPermission')(16, 'add'))
                        <a href="{{ route('banks.create') }}" class="btn btn-added btn-sm">
                            <i class="fas fa-plus me-1"></i> New Banks
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Desktop Header (>=1200px) -->
        <div class="page-header d-none d-xl-flex">
            <div class="page-title">
                <h4>All Bank Master</h4>
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(16, 'add'))
                    <a href="{{ route('banks.create') }}" class="btn btn-added btn-sm">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-1" alt="img">New Banks
                    </a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <div class="search-set d-flex justify-content-start w-100">
                        <div class="search-input w-100">
                            <a class="btn btn-searchset">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img">
                            </a>
                            <input type="text" id="search-input" class="form-control" placeholder="Search banks, account number, IFSC, branch...">
                        </div>
                    </div>
                </div>

                <!-- Desktop Table Container (>=1200px) -->
                <div class="table-container d-none d-xl-block">
                    <table class="table datanew1">
                        <thead>
                            <tr>
                                <th>Bank Name</th>
                                <th class="text-center details-column">Details</th>
                                <th>Account Number</th>
                                <th>IFSC Code</th>
                                <th>Branch</th>
                                <th>Opening Balance</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <!-- Mobile & Tablet Cards Container (<1200px) -->
                <div class="bk-m-cards d-xl-none" id="mobileBankCards">
                    <div class="text-center p-4 text-muted">Loading banks...</div>
                </div>

                <!-- Pagination Controls -->
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
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

            const canEdit = {{ app('hasPermission')(16, 'edit') ? 'true' : 'false' }};
            const canDelete = {{ app('hasPermission')(16, 'delete') ? 'true' : 'false' }};

            // Initialize DataTable WITHOUT built-in paging/search
            var table = $('.datanew1').DataTable({
                destroy: true,
                paging: false,
                info: false,
                searching: false,
                dom: 't',
                ordering: false
            });

            // Pagination state
            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';

            // Global map for bank data
            window.bankDataMap = {};

            function escapeHtml(text) {
                if (text === null || text === undefined) return '';
                return String(text)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            // Helper: format currency in Indian format
            function formatCurrencyIN(amount) {
                let num = parseFloat(amount || 0);
                return '₹' + num.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Initial fetch
            fetchBanks(currentPage);

            // Search input handler with debounce
            let searchTimeout;
            $('#search-input').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchQuery = $(this).val().trim();
                searchTimeout = setTimeout(function() {
                    fetchBanks(1);
                }, 300);
            });

            // Per‑page change handler
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchBanks(1);
            });

            // Handle page number clicks
            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                let action = $(this).data('action');

                if (action === 'next-group') {
                    if (page && page <= lastPage) {
                        fetchBanks(page);
                    }
                    return;
                }

                if (action === 'prev-group') {
                    let prevStartPage = Math.max(1, page - 2);
                    if (prevStartPage >= 1 && prevStartPage <= lastPage) {
                        fetchBanks(prevStartPage);
                    }
                    return;
                }

                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchBanks(page);
                }
            });

            // Build expandable row content for desktop table toggle
            function buildExpandableRowContent(bank) {
                let statusBadge = bank.status == 1 ?
                    `<span class="badges bg-lightgreen">Active</span>` :
                    `<span class="badges bg-lightred">Inactive</span>`;

                let actions = `
                    <div class="mobile-actions">
                        ${canEdit ? `
                        <a href="/edit-bank/${bank.id}" class="me-2">
                            <svg width="18" height="18" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"/>
                            </svg>
                        </a>` : ''}
                        ${canDelete ? `
                        <a href="javascript:void(0);" class="delete-bank" data-id="${bank.id}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 4H15V6H9V4ZM8 8H17V20H7V8H8Z" fill="#092C4C"/>
                                <path d="M9 10H11V18H9V10ZM13 10H15V18H13V10Z" fill="#092C4C"/>
                            </svg>
                        </a>` : ''}
                    </div>`;

                return `
                    <div class="collapse-details">
                        <div class="detail-item">
                            <div class="detail-label">A/C Number:</div>
                            <div class="detail-value">${escapeHtml(bank.account_number ?? 'N/A')}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">IFSC Code:</div>
                            <div class="detail-value">${escapeHtml(bank.ifsc_code ?? 'N/A')}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Branch:</div>
                            <div class="detail-value">${escapeHtml(bank.branch_name ?? 'N/A')}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Opening Bal:</div>
                            <div class="detail-value">${formatCurrencyIN(bank.opening_balance)}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Status:</div>
                            <div class="detail-value">${statusBadge}</div>
                        </div>
                        ${actions}
                    </div>
                `;
            }

            // Main fetch function
            function fetchBanks(page = 1) {
                let url = `{{ route('banks.data') }}?page=${page}&per_page=${perPage}`;
                if (selectedSubAdminId) {
                    url += `&selectedSubAdminId=${selectedSubAdminId}`;
                }
                if (searchQuery) {
                    url += `&search=${encodeURIComponent(searchQuery)}`;
                }

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            let banks = response.data;
                            let pagination = response.pagination;

                            // Update pagination state
                            currentPage = pagination.current_page;
                            lastPage = pagination.last_page;
                            updatePaginationUI(pagination);

                            // Rebuild maps
                            window.bankDataMap = {};
                            let rows = [];
                            let mobileCardsHtml = '';

                            if (banks.length > 0) {
                                banks.forEach(bank => {
                                    window.bankDataMap[bank.id] = bank;

                                    let statusBadge = bank.status == 1 ?
                                        `<span class="badges bg-lightgreen">Active</span>` :
                                        `<span class="badges bg-lightred">Inactive</span>`;

                                    let mobileStatusBadge = bank.status == 1 ?
                                        `<span class="bk-badge active"><i class="fas fa-check-circle me-1"></i>Active</span>` :
                                        `<span class="bk-badge inactive"><i class="fas fa-times-circle me-1"></i>Inactive</span>`;

                                    let toggleBtn = `
                                        <a href="javascript:void(0);" class="toggle-details" data-id="${bank.id}">
                                            <i class="fas fa-plus-circle" style="color:#ff9f43;"></i>
                                        </a>`;

                                    let actions = `
                                        ${canEdit ? `
                                        <a class="me-2" href="/edit-bank/${bank.id}">
                                            <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"/>
                                            </svg>
                                        </a>` : ''}
                                        ${canDelete ? `
                                        <a class="me-2 confirm-text delete-bank" data-id="${bank.id}" href="javascript:void(0);">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 4H15V6H9V4ZM8 8H17V20H7V8H8Z" fill="#092C4C"/>
                                                <path d="M9 10H11V18H9V10ZM13 10H15V18H13V10Z" fill="#092C4C"/>
                                            </svg>
                                        </a>` : ''}
                                    `;

                                    // Desktop table row
                                    rows.push([
                                        escapeHtml(bank.bank_name ?? 'N/A'),
                                        toggleBtn,
                                        escapeHtml(bank.account_number ?? 'N/A'),
                                        escapeHtml(bank.ifsc_code ?? 'N/A'),
                                        escapeHtml(bank.branch_name ?? 'N/A'),
                                        formatCurrencyIN(bank.opening_balance),
                                        statusBadge,
                                        actions
                                    ]);

                                    // Mobile & Tablet Card
                                    let mobileEditBtn = canEdit ? `
                                        <a href="/edit-bank/${bank.id}" class="bk-act-btn edit" title="Edit Bank">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>` : '';

                                    let mobileDeleteBtn = canDelete ? `
                                        <a href="javascript:void(0);" class="bk-act-btn delete delete-bank" data-id="${bank.id}" title="Delete Bank">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>` : '';

                                    mobileCardsHtml += `
                                        <div class="bk-card">
                                            <div class="bk-card-head">
                                                <div class="bk-card-bank-info">
                                                    <div class="bk-icon"><i class="fas fa-university"></i></div>
                                                    <div>
                                                        <h5 class="bk-name">${escapeHtml(bank.bank_name || 'N/A')}</h5>
                                                        <span class="bk-branch"><i class="fas fa-map-marker-alt me-1 text-muted"></i>${escapeHtml(bank.branch_name || 'N/A')}</span>
                                                    </div>
                                                </div>
                                                ${mobileStatusBadge}
                                            </div>
                                            <div class="bk-card-body">
                                                <div class="bk-stat-item">
                                                    <span class="bk-stat-label">Account Number</span>
                                                    <span class="bk-stat-val">${escapeHtml(bank.account_number || 'N/A')}</span>
                                                </div>
                                                <div class="bk-stat-item">
                                                    <span class="bk-stat-label">IFSC Code</span>
                                                    <span class="bk-stat-val">${escapeHtml(bank.ifsc_code || 'N/A')}</span>
                                                </div>
                                                <div class="bk-stat-item">
                                                    <span class="bk-stat-label">Opening Balance</span>
                                                    <span class="bk-stat-val text-success font-weight-bold" style="font-size: 14.5px;">${formatCurrencyIN(bank.opening_balance)}</span>
                                                </div>
                                            </div>
                                            <div class="bk-card-foot">
                                                <span class="text-muted small">Bank ID: #${bank.id}</span>
                                                <div class="bk-actions">
                                                    ${mobileEditBtn}
                                                    ${mobileDeleteBtn}
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                });
                            } else {
                                mobileCardsHtml = `<div class="p-4 text-center text-muted bg-white rounded shadow-sm">No banks found</div>`;
                            }

                            table.clear().rows.add(rows).draw();
                            $('#mobileBankCards').html(mobileCardsHtml);
                            $('.pagination-controls').show();
                        } else {
                            table.clear().draw();
                            $('#mobileBankCards').html(`<div class="p-4 text-center text-muted bg-white rounded shadow-sm">No banks found</div>`);
                            $('.pagination-controls').hide();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Failed to fetch bank data!',
                            confirmButtonColor: '#ff9f43'
                        });
                        $('.pagination-controls').hide();
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

                // Previous button
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

                // Next button
                paginationHtml += `
                    <li class="page-item ${pagination.current_page === pagination.last_page || pagination.last_page === 0 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page + 1}">Next</a>
                    </li>
                `;

                $('#pagination-numbers').html(paginationHtml);
                $('.pagination-controls').show();
            }

            // Desktop Table Toggle details (delegated event)
            $(document).on('click', '.toggle-details', function() {
                let tr = $(this).closest('tr');
                let bankId = $(this).data('id');
                let bank = window.bankDataMap[bankId];
                let icon = $(this).find('i');

                if (tr.next().hasClass('collapse-details-row')) {
                    tr.next().remove();
                    icon.removeClass('fa-minus-circle')
                        .addClass('fa-plus-circle')
                        .css('color', '#ff9f43');
                } else {
                    let detailsHtml = buildExpandableRowContent(bank);
                    tr.after(`<tr class="collapse-details-row"><td colspan="2">${detailsHtml}</td></tr>`);
                    icon.removeClass('fa-plus-circle')
                        .addClass('fa-minus-circle')
                        .css('color', 'red');
                }
            });

            // Delete bank (works on both table & card action buttons)
            $(document).on('click', '.delete-bank', function() {
                var bankId = $(this).data('id');
                var authToken = localStorage.getItem("authToken");

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
                            url: `/api/delete/banks/${bankId}`,
                            type: 'DELETE',
                            headers: {
                                "Authorization": "Bearer " + authToken,
                            },
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: response.message,
                                        icon: "success",
                                        confirmButtonColor: "#ff9f43",
                                        confirmButtonText: "OK"
                                    }).then(() => {
                                        fetchBanks(currentPage);
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: response.message,
                                        icon: "error",
                                        confirmButtonColor: "#ff9f43",
                                        confirmButtonText: "OK"
                                    });
                                }
                            },
                            error: function(xhr) {
                                let message = "Something went wrong!";
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: "Error!",
                                    text: message,
                                    icon: "error",
                                    confirmButtonColor: "#ff9f43",
                                    confirmButtonText: "OK"
                                });
                            }
                        });
                    }
                });
            });

            // Reset details when switching to desktop
            $(window).on('resize', function() {
                if ($(window).width() >= 1200) {
                    $('.collapse-details-row').remove();
                    $('.toggle-details i')
                        .removeClass('fa-minus-circle')
                        .addClass('fa-plus-circle')
                        .css('color', '#ff9f43');
                }
            });
        });
    </script>
@endpush
