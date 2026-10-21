@extends('layout.app')

@section('title', 'Meeting List')

@section('content')
    @php
        $canViewMeeting   = app('hasPermission')(31, 'view');
        $canAddMeeting    = app('hasPermission')(31, 'add');
        $canEditMeeting   = app('hasPermission')(31, 'edit');
        $canDeleteMeeting = app('hasPermission')(31, 'delete');
    @endphp
    <style>
        /* Status badges */
        .status-badge {
            padding: 4px 8px; border-radius: 4px; font-size: 12px;
            font-weight: 500; text-align: center; white-space: nowrap;
        }
        .status-scheduled  { background-color: #dbeafe; color: #2563eb; }
        .status-completed  { background-color: #d1fae5; color: #059669; }
        .status-cancelled  { background-color: #fee2e2; color: #dc2626; }

        /* Hide default DataTables controls */
        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate { display: none !important; }

        /* Custom Pagination */
        .pagination .page-item .page-link {
            background-color: #5d6d7e; color: #fff; border: none;
            margin: 0 3px; padding: 4px 10px; border-radius: 6px; font-weight: bold;
        }
        .pagination .page-item.active .page-link          { background-color: #ff9f43 !important; color: #fff; }
        .pagination .page-item .page-link:hover           { background-color: #4a5766; color: #fff; }
        .pagination .page-item.active .page-link:hover    { background-color: #e68a35 !important; }
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link      { background-color: #fff; color: #6c757d; border: 1px solid #dee2e6; }
        .pagination .page-item:first-child .page-link:hover,
        .pagination .page-item:last-child .page-link:hover { background-color: #f8f9fa; color: #495057; }
        .pagination .page-item.disabled .page-link        {
            background-color: #fff !important; color: #dee2e6 !important;
            border: 1px solid #dee2e6 !important; cursor: not-allowed !important; pointer-events: none !important;
        }

        /* Search input */
        .search-input { position: relative; display: flex; align-items: center; }
        .search-input input { padding-left: 35px !important; border-radius: 5px; }
        .btn-searchset { position: absolute; left: 10px; z-index: 10; padding: 0; top: 7px !important; }

        /* Table */
        .table-container { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .meeting-card-list { display: none; }
        .table-container table thead th { white-space: nowrap; }
        table.datanew tbody td {
            white-space: normal !important;
            overflow-wrap: anywhere;
            word-break: break-word;
            vertical-align: top;
        }
        table.datanew tbody td:nth-child(7),
        table.datanew tbody td:nth-child(8) {
            white-space: nowrap !important;
        }
        .cell-wrap {
            display: block;
            white-space: normal !important;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        /* Desktop: hide Details toggle column */
        @media (min-width: 769px) {
            table.datanew thead th, table.datanew tbody td { display: table-cell !important; }
            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(8) { display: none !important; }
        }

        /* Mobile: show only Customer + Details columns */
        /* @media (max-width: 768px) {
            .meeting-page .table-container { display: none; }
            .meeting-card-list { display: flex; flex-direction: column; gap: 8px; }
            .meeting-card { padding: 12px; background: #fff; border-radius: 10px; box-shadow: 0 1px 3px rgba(16,24,40,.08); }
            .meeting-card-top { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; }
            .meeting-card-name { color:#1b2850; font-size:15px; font-weight:600; min-width:0; overflow-wrap:anywhere; }
            .meeting-card-sub { color:#8b93a7; font-size:11px; margin-top:3px; }
            .meeting-card-badges { display:flex; flex-wrap:wrap; gap:6px; margin:6px 0 8px; }
            .meeting-card-bottom { display:flex; align-items:flex-end; flex-wrap:wrap; gap:12px 18px; }
            .meeting-card-stat span { display:block; color:#8b93a7; font-size:11px; }
            .meeting-card-stat strong { display:block; max-width:120px; color:#1b2850; font-size:12px; font-weight:600; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
            .meeting-card-actions { display:flex; margin-left:auto; }
            table.datanew thead th:nth-child(n+2),
            table.datanew tbody td:nth-child(n+2) { display: none !important; }
            table.datanew thead th:first-child,
            table.datanew tbody td:first-child { display: table-cell !important; }
            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(8) {
                display: table-cell !important;
                width: 56px;
                min-width: 56px;
                max-width: 56px;
                vertical-align: top;
                text-align: right;
                padding-top: 12px !important;
            }
            table.datanew tbody td:first-child {
                width: calc(100% - 56px);
                vertical-align: top;
            }
            .toggle-details i { font-size: 24px; }
            .toggle-details {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 30px;
                height: 30px;
            }
            .search-set { margin-right: 1rem !important; }
            .table-container { overflow-x: auto; -webkit-overflow-scrolling: touch !important; }
        } */
            /* .meeting-card-list { display: flex; flex-direction: column; gap: 8px; } */
            .meeting-card-list { display: none; flex-direction: column; gap: 8px; }
.meeting-card {
    padding: 12px; background: #fff; border: 1px solid #eceef3;
    border-radius: 12px; box-shadow: 0 1px 3px rgba(16,24,40,.05);
}
.meeting-card-top {
    display: flex; align-items: flex-start; justify-content: space-between; gap: 8px;
}
.meeting-card-name {
    color: #1b2850; font-size: 14px; font-weight: 600; text-transform: uppercase;
    line-height: 1.3; min-width: 0; overflow-wrap: anywhere;
}
.meeting-card-status {
    display: flex; flex-direction: column; align-items: flex-end;
    gap: 2px; flex-shrink: 0;
}
.meeting-card-status > span:first-child { font-size: 11px; color: #8b93a7; }
.meeting-card-sub {
    color: #8b93a7; font-size: 11px; margin-top: 3px; overflow-wrap: anywhere;
}
.meeting-card-badges { display: flex; flex-wrap: wrap; gap: 6px; margin: 6px 0 0; }
.meeting-type-chip {
    font-size: 10px; font-weight: 600; padding: 2px 9px; border-radius: 12px;
    background: #fff4e8; color: #ff9f43; overflow-wrap: anywhere;
}
.meeting-card-info {
    display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto; gap: 4px;
    margin-top: 8px;
}
.meeting-card-foot {
    display: flex; align-items: flex-end; gap: 16px;
    margin-top: 10px; padding-top: 8px; border-top: 1px dashed #eceef3;
}
.meeting-card-stat { min-width: 0; }
.meeting-card-stat span { display: block; font-size: 11px; color: #8b93a7; }
.meeting-card-stat strong {
    display: block; font-size: 12px; font-weight: 600; color: #1b2850;
    white-space: normal; overflow-wrap: anywhere;   /* date have kapase nahi */
}
.meeting-card-actions { margin-left: auto; flex-shrink: 0; }
.meeting-card-actions .listing-action-dropdown { width: auto; }
.meeting-card-actions .action-menu-toggle {
    width: 32px; height: 32px; border: 0; background: transparent;
    color: #8b93a7; font-size: 17px; box-shadow: none;
}
.meeting-card-actions .action-menu-toggle i { transform: rotate(90deg); }
/* Mobile: table hide, cards show */
@media (max-width: 768px) {
    .meeting-page .table-container { display: none !important; }
    .meeting-page .search-input { width: 100%; }
    .meeting-page .search-input input { min-height: 38px; border-radius: 8px; }
    .meeting-card-list { display: flex; }
    .search-set { margin-right: 1rem !important; }
}

/* Desktop / tablet: cards hide, table show */
@media (min-width: 769px) {
    .meeting-card-list { display: none !important; }
}

        /* Collapsible details */
        /* .collapse-details {
            padding: 12px 14px; background-color: #f8f9fa;
            border-radius: 5px; margin: 0;
        } */
        .detail-item  {
            display: flex; justify-content: space-between; gap: 14px;
            margin-bottom: 5px; font-size: 14px; border-bottom: 1px solid #eceff1;
            padding: 4px 0;
        }
        .detail-item:last-child { margin-bottom: 0; }
        .detail-label { font-weight: 600; min-width: 110px; color: #495057; }
        .detail-value { color: #212529; flex: 1; text-align: right; }
        .mobile-actions {
            display: flex; gap: 15px;
        }
        .mobile-actions a {
            display: flex; align-items: center; justify-content: center;
            width: 25px; height: 25px; border-radius: 50%; background-color: #f8f9fa; transition: all 0.3s ease;
        }
        .mobile-actions a:hover { background-color: #e9ecef; transform: translateY(-2px); }
        .mobile-actions svg { width: 18px; height: 18px; }
        .delete-meeting svg path { fill: #dc3545 !important; }

        .listing-action-dropdown { display: inline-flex; justify-content: center; width: 100%; }
        .listing-action-dropdown .action-menu-toggle {
            align-items: center; background: #fff; 
            color: #1b2850; display: inline-flex; height: 28px; justify-content: center; padding: 0; width: 34px;
        }
        .listing-action-menu {
            border: 1px solid #e8ebed; border-radius: 6px; box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
            min-width: 168px; padding: 6px; z-index: 999999999999;
        }
        .listing-action-menu-floating {
            position: fixed !important;
            z-index: 999999999999 !important;
        }
        .listing-action-menu .dropdown-item {
            align-items: center; border-radius: 4px; color: #344054; display: flex; font-size: 12px;
            gap: 8px; padding: 7px 8px; width: 100%;
        }
        .listing-action-menu .dropdown-item i { color: #667085; font-size: 13px; text-align: center; width: 15px; }
        .listing-action-menu .dropdown-item.text-danger,
        .listing-action-menu .dropdown-item.text-danger i { color: #ea5455 !important; }
        .listing-action-menu .dropdown-item:hover { background: #f4f6f8; color: #1b2850; }
        table.datanew tbody tr.child td {
            padding: 0 !important;
            border-top: 0 !important;
            background: #fff !important;
        }
    </style>

    <div class="content meeting-page">
        <div class="page-header">
            <div class="page-title"><h4>Meetings</h4></div>
            <div class="page-btn d-flex gap-2">
                @if ($canAddMeeting)
                    <a href="{{ route('meeting.add') }}" class="btn btn-added btn-sm">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-1" alt="img">New Meeting
                    </a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="mb-2">
                    <div class="search-set d-flex justify-content-md-start justify-content-start w-100">
                        <div class="search-path"></div>
                        <div class="search-input">
                            <a class="btn btn-searchset">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img">
                            </a>
                            <input type="text" id="search-input" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Meeting Title</th>
                                <th>Meeting Type</th>
                                <th>Status</th>
                                <th>Scheduled Date &amp; Time</th>
                                <th>Assigned To</th>
                                <th>Action</th>
                                <th class="details-column">Details</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="meeting-card-list" id="meetingCardList"></div>

                <!-- Pagination Controls -->
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
    const userRole        = "{{ auth()->user()->role }}";
    const canViewMeeting  = @json((bool) $canViewMeeting);
    const canEditMeeting  = @json((bool) $canEditMeeting);
    const canDeleteMeeting = @json((bool) $canDeleteMeeting);

    $(document).ready(function () {
        var authToken          = localStorage.getItem("authToken");
        const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

        var table = $('.datanew').DataTable({
            destroy: true, bFilter: false, paging: false,
            info: false, searching: false, dom: 't', ordering: false
        });

        let currentPage = 1;
        let lastPage    = 1;
        let perPage     = 10;
        let searchQuery = '';
        const mobileDetailsMap = {};

        // Initial load
        fetchMeetings(1);

        // Search
        $('#search-input').on('keyup', function () {
            searchQuery = $(this).val();
            fetchMeetings(1);
        });

        // Per-page change
        $('#per-page-select').on('change', function () {
            perPage = parseInt($(this).val());
            fetchMeetings(1);
        });

        // Pagination click
        $(document).on('click', '#pagination-numbers .page-link', function (e) {
            e.preventDefault();
            let page   = $(this).data('page');
            let action = $(this).data('action');

            if (action === 'prev-group') {
                fetchMeetings(Math.max(1, page - 1));
                return;
            }
            if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                fetchMeetings(page);
            }
        });

        function fetchMeetings(page) {
            let url = `/api/getAllMeetings?page=${page}&per_page=${perPage}`;
            if (selectedSubAdminId) url += `&selectedSubAdminId=${selectedSubAdminId}`;
            if (searchQuery)        url += `&search=${encodeURIComponent(searchQuery)}`;

            $.ajax({
                url: url, type: "GET", dataType: "json",
                headers: { "Authorization": "Bearer " + authToken },
                success: function (response) {
                    if (response.status) {
                        let meetings   = response.data;
                        let pagination = response.pagination;

                        currentPage = pagination.current_page;
                        lastPage    = pagination.last_page;

                        updatePaginationUI(pagination);

                        Object.keys(mobileDetailsMap).forEach(function (key) {
                            delete mobileDetailsMap[key];
                        });

                        let tableBody = [];
                        let cards = [];
                        meetings.forEach(function (meeting) {
                            let statusBadge = `<span class="status-badge status-${meeting.status.toLowerCase()}">${meeting.status}</span>`;

                            let detailsToggle = `
                                <a href="javascript:void(0);" class="toggle-details" data-id="${meeting.id}">
                                    <i class="fas fa-plus-circle" style="color:#ff9f43;"></i>
                                </a>`;

                            let deleteBtn = '';
                            if (canDeleteMeeting && userRole !== 'sales-manager' && userRole !== 'purchase-manager' && userRole !== 'inventory-manager') {
                                deleteBtn = `
                                    <a class="me-2 confirm-text delete-meeting" data-id="${meeting.id}" href="javascript:void(0);">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 4H15V6H9V4ZM8 8H17V20H7V8H8Z" fill="#092C4C"></path>
                                            <path d="M9 10H11V18H9V10ZM13 10H15V18H13V10Z" fill="#092C4C"></path>
                                        </svg>
                                    </a>`;
                            }

                            let actionButtons = '';
                            if (canViewMeeting) {
                                actionButtons += `
                                    <a class="dropdown-item" href="/meeting-view/${meeting.id}">
                                        <i class="fas fa-eye"></i> View
                                    </a>`;
                            }
                            if (canEditMeeting) {
                                actionButtons += `
                                    <a class="dropdown-item" href="/edit-meeting/${meeting.id}">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>`;
                            }
                            if (deleteBtn) {
                                actionButtons += `<a class="dropdown-item text-danger confirm-text delete-meeting" data-id="${meeting.id}" href="javascript:void(0);"><i class="fas fa-trash"></i> Delete</a>`;
                            }
                            actionButtons = actionButtons.trim() ? `
                                <div class="dropdown listing-action-dropdown">
                                    <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end listing-action-menu">${actionButtons}</div>
                                </div>
                            ` : '<span class="text-muted">N/A</span>';

                            let mobileActions = `
                                <div class="mobile-actions">
                                    ${canViewMeeting ? `<a href="/meeting-view/${meeting.id}"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"></path><path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"></path></svg></a>` : ''}
                                    ${canEditMeeting ? `<a href="/edit-meeting/${meeting.id}"><svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"></path></svg></a>` : ''}
                                    ${deleteBtn}
                                </div>`;

                            mobileDetailsMap[meeting.id] = `
                                <div class="collapse-details">
                                    <div class="detail-item"><span class="detail-label">Date:</span><span class="detail-value">${meeting.formatted_scheduled_on || 'N/A'}</span></div>
                                    <div class="detail-item"><span class="detail-label">Customer Name:</span><span class="detail-value">${meeting.customer ? meeting.customer.name : 'N/A'}</span></div>
                                    <div class="detail-item"><span class="detail-label">Meeting Title:</span><span class="detail-value">${meeting.meeting_title || 'N/A'}</span></div>
                                    <div class="detail-item"><span class="detail-label">Meeting Type:</span><span class="detail-value">${meeting.meeting_type || 'N/A'}</span></div>
                                    <div class="detail-item"><span class="detail-label">Status:</span><span class="detail-value">${statusBadge}</span></div>
                                    <div class="detail-item"><span class="detail-label">Assigned To:</span><span class="detail-value">${meeting.assigned_user ? meeting.assigned_user.name : 'N/A'}</span></div>
                                    <div class="detail-item"><span class="detail-label">Agenda:</span><span class="detail-value">${meeting.agenda ? meeting.agenda.substring(0, 100) + (meeting.agenda.length > 100 ? '...' : '') : 'N/A'}</span></div>
                                    ${mobileActions}
                                </div>`;

                            tableBody.push([
                                `<div>
                                    <div class="cell-wrap">
                                        <a href="/meeting-view/${meeting.id}">${meeting.customer ? meeting.customer.name : 'N/A'}</a>
                                    </div>
                                </div>`,
                                `<div class="cell-wrap"><a href="/meeting-view/${meeting.id}">${meeting.meeting_title || 'N/A'}</a></div>`,
                                `<div class="cell-wrap">${meeting.meeting_type || 'N/A'}</div>`,
                                statusBadge,
                                `<div class="cell-wrap">${meeting.formatted_scheduled_on || 'N/A'}</div>`,
                                `<div class="cell-wrap">${meeting.assigned_user ? meeting.assigned_user.name : 'N/A'}</div>`,
                                actionButtons,
                                detailsToggle
                            ]);
                            // cards.push(`<div class="meeting-card">
                            //         <div class="meeting-card-top"><div><a class="meeting-card-name" href="/meeting-view/${meeting.id}">${meeting.customer ? meeting.customer.name : 'N/A'}</a><div class="meeting-card-sub">${meeting.meeting_title || 'N/A'}</div></div></div>
                            //         <div class="meeting-card-badges">${statusBadge}</div>
                            //         <div class="meeting-card-bottom">
                            //             <div class="meeting-card-stat"><span>Scheduled</span><strong>${meeting.formatted_scheduled_on || 'N/A'}</strong></div>
                            //             <div class="meeting-card-stat"><span>Type</span><strong>${meeting.meeting_type || 'N/A'}</strong></div>
                            //             <div class="meeting-card-stat"><span>Assigned To</span><strong>${meeting.assigned_user ? meeting.assigned_user.name : 'N/A'}</strong></div>
                            //             <div class="meeting-card-actions">${actionButtons}</div>
                            //         </div>
                            //     </div>`);
                            // });
                            const customerName = meeting.customer ? meeting.customer.name : 'N/A';
                            const assignedName = meeting.assigned_user ? meeting.assigned_user.name : 'N/A';

                            cards.push(`<div class="meeting-card">
                                <div class="meeting-card-top">
                                    <a class="meeting-card-name" href="/meeting-view/${meeting.id}">${customerName}</a>
                                </div>
                                <div class="meeting-card-info">
                                    <div class="meeting-card-stat"><span>Meeting Title</span><strong>${meeting.meeting_title || 'N/A'}</strong></div>
                                    <div class="meeting-card-stat"><span>Meeting Type</span><strong>${meeting.meeting_type || 'N/A'}</strong></div>
                                    <div class="meeting-card-status">
                                        <span>Status</span>
                                        ${statusBadge}
                                    </div>
                                </div>
                                <div class="meeting-card-foot">
                                    <div class="meeting-card-stat"><span>Scheduled</span><strong>${meeting.formatted_scheduled_on || 'N/A'}</strong></div>
                                    <div class="meeting-card-stat"><span>Assigned To</span><strong>${assignedName}</strong></div>
                                    <div class="meeting-card-actions">${actionButtons}</div>
                                </div>
                            </div>`);
                        });

                        table.clear().rows.add(tableBody).draw();
                        $('#meetingCardList').html(cards.join('') || '<div class="text-center text-muted py-3">No meetings found</div>');
                    } else {
                        table.clear().draw();
                        $('#meetingCardList').html('<div class="text-center text-muted py-3">No meetings found</div>');
                        $(".datanew tbody").html('<tr><td colspan="8" class="text-center">No meetings found</td></tr>');
                        Object.keys(mobileDetailsMap).forEach(function (key) {
                            delete mobileDetailsMap[key];
                        });
                        updatePaginationUI({ current_page: 1, last_page: 1, per_page: perPage, total: 0 });
                    }
                },
                error: function () {
                    table.clear().draw();
                    $(".datanew tbody").html('<tr><td colspan="8" class="text-center">Error loading data</td></tr>');
                    Object.keys(mobileDetailsMap).forEach(function (key) {
                        delete mobileDetailsMap[key];
                    });
                }
            });
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

        function updatePaginationUI(pagination) {
            let from = pagination.total === 0 ? 0 : (pagination.current_page - 1) * pagination.per_page + 1;
            let to   = Math.min(pagination.current_page * pagination.per_page, pagination.total);

            $('#pagination-from').text(from);
            $('#pagination-to').text(to);
            $('#pagination-total').text(pagination.total);

            const visiblePageCount = 2;
            let startPage = Math.floor((pagination.current_page - 1) / visiblePageCount) * visiblePageCount + 1;
            let endPage   = Math.min(pagination.last_page, startPage + visiblePageCount - 1);

            let html = `<li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                            <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page - 1}">Previous</a>
                        </li>`;

            if (startPage > 1) {
                html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" data-page="${startPage - 1}" data-action="prev-group">..</a></li>`;
            }

            for (let i = startPage; i <= endPage; i++) {
                html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                         </li>`;
            }

            if (endPage < pagination.last_page) {
                if (endPage < pagination.last_page - 1) {
                    html += `<li class="page-item disabled"><a class="page-link" href="javascript:void(0);">..</a></li>`;
                }
                html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" data-page="${pagination.last_page}">${pagination.last_page}</a></li>`;
            }

            html += `<li class="page-item ${pagination.current_page === pagination.last_page || pagination.last_page === 0 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page + 1}">Next</a>
                     </li>`;

            $('#pagination-numbers').html(html);
            $('.pagination-controls').show();
        }

        // Toggle mobile details icon
        $(document).on('click', '.toggle-details', function (e) {
            e.preventDefault();
            let toggle = $(this);
            let icon = toggle.find('i');
            let meetingId = toggle.data('id');
            let tr = toggle.closest('tr');
            let row = table.row(tr);
            let detailHtml = mobileDetailsMap[meetingId] || '';

            if (!detailHtml) return;

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                icon.removeClass('fa-minus-circle').addClass('fa-plus-circle').css('color', '#ff9f43');
            } else {
                $('.datanew tbody tr.shown').each(function () {
                    let openRow = table.row(this);
                    if (openRow.child.isShown()) openRow.child.hide();
                    $(this).removeClass('shown')
                        .find('.toggle-details i')
                        .removeClass('fa-minus-circle')
                        .addClass('fa-plus-circle')
                        .css('color', '#ff9f43');
                });

                row.child(detailHtml, 'meeting-mobile-child-row').show();
                tr.addClass('shown');
                icon.removeClass('fa-plus-circle').addClass('fa-minus-circle').css('color', 'red');
            }
        });

        // Delete meeting
        $(document).on('click', '.delete-meeting', function () {
            var meetingId = $(this).data('id');
            Swal.fire({
                title: "Are you sure?", text: "You won't be able to revert this!",
                icon: "warning", showCancelButton: true,
                confirmButtonColor: "#ff9f43", cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/meeting/${meetingId}/delete`, type: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            "Authorization": "Bearer " + authToken,
                        },
                        success: function (response) {
                            if (response.status) {
                                Swal.fire({ title: "Deleted!", text: "Meeting has been deleted.", icon: "success", confirmButtonColor: "#ff9f43" })
                                    .then(() => fetchMeetings(currentPage));
                            } else {
                                Swal.fire({ title: "Error!", text: response.message || "Failed to delete.", icon: "error", confirmButtonColor: "#ff9f43" });
                            }
                        },
                        error: function () {
                            Swal.fire({ title: "Error!", text: "Failed to delete. Please try again.", icon: "error", confirmButtonColor: "#ff9f43" });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
