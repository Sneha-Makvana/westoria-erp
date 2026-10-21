@extends('layout.app')

@section('title', 'Branch List')

@section('content')
    <style>
        #DataTables_Table_0_info {
            float: left;
        }

        .table-scroll-top {
            display: none;
        }

        .table-scroll-top {
            overflow-x: auto;
            overflow-y: hidden;
            height: 20px;
            width: 100%;
            margin-bottom: 5px;
        }

        .table-scroll-top div {
            height: 1px;
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

        .pagination .page-item .page-link:hover {
            background-color: #4a5766;
            color: #fff;
        }

        .pagination .page-item.active .page-link:hover {
            background-color: #e68a35 !important;
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
            top: 4px !important;
        }

        /* ✅ Hide default DataTables search box completely */
        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }

        /* Remove extra top spacing created by DataTables */
        .dataTables_wrapper .row:first-child {
            display: none !important;
        }

        .dataTables_wrapper {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        /* Branch name styling with image */
        .branch-img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 12px;
        }

        .branch-name {
            display: inline-block;
            font-weight: 500;
            font-size: 14px;
            line-height: 1.4;
            word-break: break-word;
        }
        .fade-out {
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }

        .fade-out.hidden {
            opacity: 0;
        }
        /* Desktop base */
        table.datanew { width: 100%; }
        @media (min-width: 1025px) {
            table.datanew { min-width: 1000px; }
        }

        /* ---------- New Branch UI (mobile + tablet) ---------- */
        .st-quick-links,
        .st-fab-new,
        .st-card { display: none; }

        /* Action button: desktop boxed ⋯ */
        .staff-action-dropdown .action-menu-toggle {
            align-items: center;
            background: #fff;
            border: 1px solid #d7dde8;
            border-radius: 4px;
            color: #1b2850;
            display: inline-flex;
            height: 28px;
            width: 34px;
            justify-content: center;
            padding: 0;
        }
        .staff-action-dropdown .action-menu-toggle:hover,
        .staff-action-dropdown .action-menu-toggle:focus {
            background: #f8fafc;
            border-color: #b8c2d2;
            color: #1b2850;
        }
        .staff-action-dropdown .icon-mobile { display: none !important; }
        .staff-action-dropdown .icon-desktop { display: inline-block !important; }
        .staff-action-menu {
            border: 1px solid #e8ebed;
            border-radius: 6px;
            box-shadow: 0 8px 20px rgba(15,23,42,.12);
            min-width: 150px;
            padding: 6px;
        }
        .staff-action-menu-floating { position: fixed !important; z-index: 999999 !important; margin: 0 !important; }
        .staff-action-menu .dropdown-item {
            align-items: center;
            display: flex;
            font-size: 12px;
            gap: 8px;
            padding: 7px 8px;
            border-radius: 4px;
            color: #344054;
        }
        .staff-action-menu .dropdown-item i { width: 15px; text-align: center; color: #667085; }
        .staff-action-menu .dropdown-item.text-danger,
        .staff-action-menu .dropdown-item.text-danger i { color: #ea5455 !important; }
        .staff-action-menu .dropdown-item:hover { background: #f4f6f8; }

        @media (max-width: 1024.98px),
            (width: 1032px) and (height: 1376px),
            (width: 1280px) and (height: 800px) {

            .st-quick-links {
                display: block;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16,24,40,.08);
                padding: 12px 10px;
                margin-bottom: 10px;
            }
            .st-quick-head { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
            .st-quick-title { font-size: 15px; font-weight: 600; color: #1b2850; margin-left: 4px; }
            .st-quick-head .btn { height: 30px; padding: 0 10px !important; font-size: 12px !important; font-weight: 600; }

            .content:has(.st-quick-links) > .page-header { display: none !important; }
            .content:has(.st-quick-links) { padding-left: 6px !important; padding-right: 6px !important; }

            .content:has(.st-quick-links) > .card {
                background: transparent !important;
                border: 0 !important;
                box-shadow: none !important;
                margin: 0 !important;
            }
            .content:has(.st-quick-links) > .card > .card-body { padding: 0 !important; }

            /* search */
            .content:has(.st-quick-links) .card-body > .mb-2 {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16,24,40,.08);
                padding: 8px 10px;
                margin-bottom: 8px !important;
            }
            .content:has(.st-quick-links) .card-body > .mb-2 .search-set,
            .content:has(.st-quick-links) .card-body > .mb-2 .search-input {
                width: 100% !important;
                margin: 0 !important;
            }
            .content:has(.st-quick-links) .card-body > .mb-2 #search-input {
                width: 100% !important;
                height: 38px !important;
                font-size: 13px;
                border-radius: 8px;
            }
            .content:has(.st-quick-links) .card-body > .mb-2 .btn-searchset { top: 9px !important; }

            /* table -> cards */
            .table-container { overflow: visible !important; border: 0 !important; padding: 0 !important; }
            table.datanew,
            table.datanew tbody {
                display: block !important;
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                border: 0 !important;
            }
            table.datanew thead { display: none !important; }

            table.datanew tbody tr {
                display: block !important;
                position: relative;
                background: #fff;
                border: 1px solid #eef0f4;
                border-radius: 10px;
                box-shadow: 0 1px 3px rgba(16,24,40,.06);
                margin: 0 0 10px;
                padding: 10px 12px;
            }
            table.datanew tbody tr > td { display: none !important; }
            table.datanew tbody tr > td:first-child {
                display: block !important;
                width: 100% !important;
                max-width: none !important;
                padding: 0 !important;
                border: 0 !important;
                background: transparent !important;
            }
            /* 7th = Action */
            table.datanew tbody tr > td:nth-child(7):not(.dataTables_empty) {
                display: flex !important;
                align-items: center;
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                right: 8px;
                width: auto !important;
                padding: 0 !important;
                border: 0 !important;
                background: transparent !important;
            }
            table.datanew tbody td.dataTables_empty {
                display: block !important;
                text-align: center;
                padding: 16px !important;
            }

            table.datanew tbody td:first-child a.st-desktop-only { display: none !important; }

            .st-card { display: block; cursor: pointer; padding-right: 50px; min-height: 44px; }
            .st-card-top { display: flex; align-items: center; gap: 10px; }
            .st-card .branch-img { width: 44px; height: 44px; margin: 0; border-radius: 50%; flex-shrink: 0; }
            .st-card-text { min-width: 0; flex: 1; }
            .st-card-line1 { display: flex; align-items: center; flex-wrap: wrap; gap: 6px; }
            .st-card-name {
                font-size: 15px; font-weight: 600; color: #1b2850;
                text-transform: uppercase; white-space: nowrap; max-width: 100%;
                overflow: hidden; text-overflow: ellipsis;
            }
            .st-card-badge {
                display: inline-block; font-size: 10px; font-weight: 600;
                padding: 2px 8px; border-radius: 12px;
                background: #fff3df; color: #e8960c;
            }
            .st-card-line2 {
                display: flex; flex-wrap: wrap; gap: 2px 12px;
                margin-top: 4px; font-size: 11px; color: #595b5d;
            }
            .st-card-line2 span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; }
            .st-card-line2 i { width: 14px; color: #8b93a7; }

            /* floating button */
            .st-fab-new {
                position: fixed; left: 50%; bottom: 74px; transform: translateX(-50%);
                z-index: 1030; display: inline-flex; align-items: center; gap: 8px;
                padding: 12px 24px; border-radius: 30px;
                background: #e8334a; color: #fff !important;
                font-size: 15px; font-weight: 600; text-decoration: none;
                box-shadow: 0 6px 16px rgba(232,51,74,.35); white-space: nowrap;
            }
            .content:has(.st-fab-new) { padding-bottom: 100px !important; }

            /* mobile: vertical dots */
            .staff-action-dropdown .action-menu-toggle {
                background: transparent; border: 0; box-shadow: none;
                color: #8b93a7; width: 24px; height: 30px; font-size: 16px;
            }
            .staff-action-dropdown .action-menu-toggle:hover,
            .staff-action-dropdown .action-menu-toggle:focus {
                background: transparent; border: 0; color: #1b2850;
            }
            .staff-action-dropdown .icon-desktop { display: none !important; }
            .staff-action-dropdown .icon-mobile { display: inline-block !important; }
        }

        /* Tablet: 2 column */
        @media screen and (min-width: 768px) and (max-width: 1024.98px),
            screen and (width: 1032px) and (height: 1376px),
            screen and (width: 1280px) and (height: 800px) {
            table.datanew tbody {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
                align-items: start;
            }
            table.datanew tbody tr { margin: 0 !important; }
            table.datanew tbody tr:has(> td.dataTables_empty) { grid-column: 1 / -1; }
            .st-fab-new { bottom: 86px; font-size: 16px; }
        }

        /* header spacing */
        @media screen and (min-width: 768px) and (max-width: 1024.98px) {
            .page-wrapper:has(.st-quick-links) { padding-top: 0 !important; }
            .content:has(.st-quick-links) { padding-top: 6px !important; margin-top: 0 !important; }
        }
        @media screen and (width: 1032px) and (height: 1376px),
            screen and (width: 1280px) and (height: 800px) {
            .content:has(.st-quick-links) { padding-top: 16px !important; }
        }
    </style>

   <div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>All Branches</h4>
        </div>
        <div class="page-btn">
            @if (app('hasPermission')(8, 'add'))
                <a href="{{ route('subbranch.add') }}" class="btn btn-added btn-sm">
                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" alt="img">
                    New Branch
                </a>
            @endif
        </div>
    </div>

    <div class="st-quick-links">
        <div class="st-quick-head">
            <div class="st-quick-title">All Branches</div>
            @if (app('hasPermission')(8, 'add'))
                <a href="{{ route('subbranch.add') }}" class="btn btn-sm btn-added">
                    <i class="fas fa-plus me-1"></i> New Branch
                </a>
            @endif
        </div>
    </div>

    @if (app('hasPermission')(8, 'add'))
        <a href="{{ route('subbranch.add') }}" class="st-fab-new">
            <i class="fas fa-plus"></i> Add New Branch
        </a>
    @endif

        <div class="card">
            <div class="card-body">
                <div class="mb-2">
                    <div class="search-set d-flex justify-content-md-start justify-content-start w-100">
                        <div class="search-input">
                            <a class="btn btn-searchset">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img" style="margin-top: 5px;">
                            </a>
                            <input type="text" id="search-input" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>Branch Name</th>
                                <th>Branch Role</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Country</th>
                                <th>City</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded dynamically -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
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
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            const imagePath = "{{ env('ImagePath') }}";

            // Initialize DataTable
            var table = $('.datanew').DataTable({
                "destroy": true,
                "bFilter": false,
                "paging": false,
                "info": false,
                "searching": false,
                "dom": 't',
                "ordering": false
            });

            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            // Pagination state
            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';

            // Helper function to capitalize first letter
            function capitalizeFirstLetter(str) {
                if (!str) return 'N/A';
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            // Helper function to get action buttons HTML
            function getActionButtons(branchId, role) {
                const normalizedRole = (role || '').toLowerCase();
                let menuItems = '';
                @if (app('hasPermission')(8, 'view'))
                menuItems += `<a class="dropdown-item" href="/view-subbranch/${branchId}"><i class="fas fa-eye"></i><span>View</span></a>`;
                @endif
                @if (app('hasPermission')(8, 'edit'))
                menuItems += `<a class="dropdown-item" href="/edit-subbranch/${branchId}"><i class="fas fa-edit"></i><span>Edit</span></a>`;
                @endif
                @if (app('hasPermission')(8, 'delete'))
                if (normalizedRole !== 'admin') {
                    menuItems += `<a class="dropdown-item text-danger delete-btn" href="javascript:void(0);" data-id="${branchId}"><i class="fas fa-trash"></i><span>Delete</span></a>`;
                }
                @endif
                if (!menuItems) {
                    menuItems = '<span class="dropdown-item text-muted">No actions</span>';
                }

                return `
                    <div class="dropdown staff-action-dropdown">
                        <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                            <i class="fas fa-ellipsis-h icon-desktop"></i>
                            <i class="fas fa-ellipsis-v icon-mobile"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end staff-action-menu">${menuItems}</div>
                    </div>`;
            }

            // Fetch branches with pagination
            function fetchBranches(page = 1) {
                let url = `/api/getAllSubbranch?page=${page}&per_page=${perPage}`;
                if (selectedSubAdminId) {
                    url += `&selectedSubAdminId=${selectedSubAdminId}`;
                }
                if (searchQuery) {
                    url += `&search=${encodeURIComponent(searchQuery)}`;
                }

                $.ajax({
                    url: url,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    beforeSend: function() {
                        $(".datanew tbody").html(
                            `<tr><td colspan="8" class="text-center">Loading...</td></tr>`
                        );
                    },
                    success: function(res) {
                        if (res.status) {
                            let branches = res.data;
                            let pagination = res.pagination;

                            currentPage = pagination.current_page;
                            lastPage = pagination.last_page;
                            updatePaginationUI(pagination);

                            renderTable(branches);
                        } else {
                            $(".datanew tbody").html(
                                `<tr><td colspan="8" class="text-center">No branches found.</td></tr>`
                            );
                            $('.pagination-controls').hide();
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching branches:", xhr);
                        $(".datanew tbody").html(
                            `<tr><td colspan="8" class="text-center text-danger">Error loading data.</td></tr>`
                        );
                    }
                });
            }

            // Render table with expandable details for mobile
            function renderTable(branches) {
                let tableBody = [];

                branches.forEach(branch => {
                    let profileImage = branch.profile_image ?
                        imagePath + '/storage/' + branch.profile_image :
                        imagePath + 'admin/assets/img/customer/customer5.jpg';

                    let branchName = capitalizeFirstLetter(branch.name);
                    let branchRole = capitalizeFirstLetter(branch.role);
                    let country = capitalizeFirstLetter(branch.user_detail?.country);
                    let city = capitalizeFirstLetter(branch.user_detail?.city);
                    let phone = branch.phone ?? 'N/A';
                    let email = branch.email ?? 'N/A';

                    let firstColumn = `
                        <a href="/view-subbranch/${branch.id}" class="st-desktop-only" style="display:flex;align-items:center;text-decoration:none;color:inherit;">
                            <img src="${profileImage}" alt="Branch" class="branch-img">
                            <span class="branch-name">${branchName}</span>
                        </a>

                        <div class="st-card" data-branch-id="${branch.id}">
                            <div class="st-card-top">
                                <img src="${profileImage}" alt="Branch" class="branch-img">
                                <div class="st-card-text">
                                    <div class="st-card-line1">
                                        <span class="st-card-name">${branchName}</span>
                                        <span class="st-card-badge">${branchRole}</span>
                                    </div>
                                    <div class="st-card-line2">
                                        <span><i class="fas fa-phone"></i> ${phone}</span>
                                        <span><i class="fas fa-envelope"></i> ${email}</span>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                    tableBody.push([
                        firstColumn,
                        branchRole,
                        phone,
                        email,
                        country,
                        city,
                        getActionButtons(branch.id, branch.role)
                    ]);
                });

                table.clear().rows.add(tableBody).draw();
            }
            // Update pagination UI
            function updatePaginationUI(pagination) {
                let from = (pagination.current_page - 1) * pagination.per_page ;
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

                // Show only 3 page numbers at a time
                const visiblePageCount = 2;
                let startPage = Math.floor((pagination.current_page - 1) / visiblePageCount) * visiblePageCount + 1;
                let endPage = Math.min(pagination.last_page, startPage + visiblePageCount - 1);

                // Show previous ellipsis if there are pages before startPage
                if (startPage > 1) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);" data-page="${startPage - 1}" data-action="prev-group">..</a>
                        </li>
                    `;
                }

                // Generate page numbers
                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                        </li>
                    `;
                }

                // Show next ellipsis if there are more pages after endPage
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

            // Handle page number clicks with ellipsis support
            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                let action = $(this).data('action');

                // Handle ellipsis clicks to load next/previous groups
                if (action === 'next-group') {
                    // Load the page that starts the next group
                    if (page && page <= lastPage) {
                        fetchBranches(page);
                    }
                    return;
                }

                if (action === 'prev-group') {
                    // Load the previous group's starting page
                    let prevStartPage = Math.max(1, page - 2);
                    if (prevStartPage >= 1 && prevStartPage <= lastPage) {
                        fetchBranches(prevStartPage);
                    }
                    return;
                }

                // Regular page navigation
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchBranches(page);
                }
            });

            // Handle per-page change
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchBranches(1);
            });

            // Handle search input
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                fetchBranches(1);
            });

            // Toggle details icon
            $(document).on('click', '.toggle-details', function() {
                let icon = $(this).find('i');
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

            // Handle Delete
            $(document).on("click", ".delete-btn", function() {
                let branchId = $(this).data("id");

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
                            url: `/api/deleteSubbranch/${branchId}`,
                            type: "POST",
                            headers: {
                                "Authorization": "Bearer " + authToken,
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: response.status ? "Deleted!" : "Notice",
                                    text: response.message,
                                    icon: response.status ? "success" : "info",
                                    confirmButtonColor: "#ff9f43",
                                    confirmButtonText: "OK"
                                });

                                if (response.status) {
                                    fetchBranches(currentPage);
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = "Something went wrong.";
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    title: "Error!",
                                    text: errorMessage,
                                    icon: "error",
                                    confirmButtonColor: "#ff9f43",
                                    confirmButtonText: "OK"
                                });
                            },
                        });
                    }
                });
            });

            // Initial fetch
            $(document).on('click', '.st-card', function (e) {
                if ($(e.target).closest('a, button').length) return;
                window.location.href = '/view-subbranch/' + $(this).data('branch-id');
            });

            $(document).on('show.bs.dropdown', '.staff-action-dropdown', function () {
                const $dd = $(this), $menu = $dd.find('.staff-action-menu');
                if (!$menu.length) return;
                $dd.data('floating-menu', $menu).data('menu-parent', $menu.parent());
                $menu.addClass('staff-action-menu-floating').appendTo('body');
            });

            $(document).on('shown.bs.dropdown', '.staff-action-dropdown', function () {
                const $dd = $(this), $menu = $dd.data('floating-menu');
                const btn = $dd.find('.action-menu-toggle')[0];
                if (!$menu || !btn) return;
                const r = btn.getBoundingClientRect();
                const w = $menu[0].offsetWidth || 160, h = $menu[0].offsetHeight || 120;
                let left = Math.max(8, Math.min(r.right - w, window.innerWidth - w - 8));
                let top = r.bottom + 4;
                if (top + h > window.innerHeight - 8) top = Math.max(8, r.top - h - 4);
                $menu.css({ position: 'fixed', left: left + 'px', top: top + 'px', transform: 'none' });
            });

            $(document).on('hidden.bs.dropdown', '.staff-action-dropdown', function () {
                const $dd = $(this), $menu = $dd.data('floating-menu'), $parent = $dd.data('menu-parent');
                if ($menu && $parent) {
                    $menu.removeClass('staff-action-menu-floating').removeAttr('style');
                    $parent.append($menu);
                }
            });

            $(window).on('scroll resize', function () {
                $('.staff-action-dropdown.show .action-menu-toggle').dropdown('hide');
            });
            fetchBranches();
        });
    </script>
@endpush
