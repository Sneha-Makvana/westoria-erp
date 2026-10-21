@extends('layout.app')

@section('title', 'Account Branch List')

@section('content')
    <style>
        .sorting_1 {
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
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

        .table-scroll-top {
            display: none;
        }

        table.datanew {
            table-layout: auto !important;
            width: 100% !important;
        }

        table.datanew td,
        table.datanew th {
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: anywhere !important;
            vertical-align: middle;
            padding: 10px 8px !important;
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

        .pagination .page-item.disabled .page-link {
            background-color: #fff !important;
            color: #dee2e6 !important;
            border: 1px solid #dee2e6 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }

        .product-toolbar {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .product-toolbar-filters {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
            flex: 1 1 auto;
            min-width: 0;
        }

        .product-toolbar-search {
            flex: 1 1 320px;
            min-width: 220px;
            max-width: 260px;
        }

        .search-input input {
            padding-left: 35px !important;
            border-radius: 5px;
            width: 100%;
        }

        .search-input {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .btn-searchset {
            position: absolute;
            left: 10px;
            z-index: 10;
            padding: 0;
            top: 7px !important;
        }

        .product-toolbar-actions {
            display: flex;
            gap: 8px;
            margin-left: auto;
            flex-wrap: wrap;
        }

        .product-toolbar-actions .btn {
            white-space: nowrap;
        }

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
            background: #f5f5f5;
        }

        .icon-btn:hover {
            background: #ececec;
        }

        /* Desktop: show all columns normally */
        @media (min-width: 1200px) {
            table.datanew thead th,
            table.datanew tbody td {
                display: table-cell !important;
            }

            /* Hide the Details toggle column on desktop */
            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: none !important;
            }
        }

        /* Mobile: hide non-essential columns, show Details toggle */
        @media (max-width: 1199px) {
            table.datanew thead th:nth-child(n+3),
            table.datanew tbody td:nth-child(n+3) {
                display: none !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: table-cell !important;
                text-align: center;
                vertical-align: top !important;
                width: 56px !important;
                min-width: 56px !important;
                max-width: 56px !important;
                padding: 12px 6px !important;
            }

            .toggle-details i {
                font-size: 18px;
            }

            table.datanew tbody td:first-child {
                align-items: center !important;
                max-width: calc(100vw - 100px) !important;
            }
        }

        /* Tablet specific fixes */
        @media screen and (min-width: 769px) and (max-width: 1199px) {
            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: table-cell !important;
                width: 60px !important;
                min-width: 60px !important;
                max-width: 60px !important;
            }

            .toggle-details {
                display: inline-block !important;
                padding: 8px !important;
                z-index: 10 !important;
            }

            .toggle-details i {
                font-size: 20px !important;
                width: 24px !important;
                height: 24px !important;
                line-height: 24px !important;
            }
        }

    </style>

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>All Account Branches</h4>
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(1, 'add') || app('hasPermission')(16, 'add'))
                    <a href="{{ route('account_branch.add') }}" class="btn btn-sm btn-added"><img
                            src="{{ env('ImagePath') . '/admin/assets/img/icons/plus.svg' }}" alt="img"
                            class="me-1">New Branch</a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="product-toolbar">
                    <div class="product-toolbar-filters">
                        <div class="product-toolbar-search">
                            <label for="search-input" class="form-label mb-1">Search</label>
                            <div class="search-input">
                                <a class="btn btn-searchset">
                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img">
                                </a>
                                <input type="text" id="search-input" class="form-control" placeholder="Search...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>Branch Name</th>
                                <th class="details-column">Details</th>
                                <th>S.No</th>
                                <th>Branch Code</th>
                                <th>Email Id</th>
                                <th>Phone #1</th>
                                <th>City/Town</th>
                                <th>State</th>
                                <th>Status</th>
                                <th style="width: 145px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

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
                            <span id="pagination-from">0</span> - <span id="pagination-to">0</span> of <span
                                id="pagination-total">0</span> items
                        </span>
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0" id="pagination-numbers">
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
            var table = $('.datanew').DataTable({
                "destroy": true,
                "bFilter": false,
                "paging": false,
                "info": false,
                "searching": false,
                "ordering": false,
                "dom": 't'
            });

            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            function fetchAccountBranches(page = 1) {
                $.ajax({
                    url: "/api/getAllAccountBranch",
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    data: {
                        page: page,
                        per_page: perPage,
                        search: searchQuery,
                        sub_branch_id: selectedSubAdminId
                    },
                    success: function(response) {
                        if (response.status) {
                            let branches = response.data;
                            let tableBody = [];
                            let pagination = response.pagination || null;

                            if (pagination) {
                                currentPage = pagination.current_page;
                                lastPage = pagination.last_page;
                                updatePaginationUI(pagination);
                            }

                            $.each(branches, function(index, branch) {
                                let sno = (currentPage - 1) * perPage + index + 1;
                                let branchCode = branch.branch_code || 'N/A';
                                let name = branch.name || 'N/A';
                                let addressLine1 = branch.address || 'N/A';
                                let addressLine2 = branch.address_line_2 || 'N/A';
                                let addressLine3 = branch.address_line_3 || 'N/A';
                                let pinCode = branch.zip_code || 'N/A';
                                let city = branch.city || 'N/A';
                                let state = branch.state || 'N/A';
                                let phone1 = branch.phone || 'N/A';
                                let phone2 = branch.phone_2 || 'N/A';
                                let email = branch.email || 'N/A';
                                let tin = branch.tin || 'N/A';
                                let area = branch.area || 'N/A';
                                let branchCompanyName = branch.branch_company_name || 'N/A';
                                let openedOn = branch.opened_on || 'N/A';
                                let closedOn = branch.closed_on || 'N/A';
                                let branchType = branch.branch_type || 'N/A';
                                let gstin = branch.gstin || 'N/A';
                                let pan = branch.pan || 'N/A';
                                let status = branch.status || 'N/A';

                                let statusBadge = status === 'active' ? 
                                    '<span class="badges bg-lightgreen">Active</span>' : 
                                    '<span class="badges bg-lightred">InActive</span>';

                                let editUrl = `{{ url('edit-account-branch') }}/${branch.id}`;

                                let actionButtons = `
                                    <div class="action-buttons">
                                        @if (app('hasPermission')(1, 'edit') || app('hasPermission')(16, 'edit'))
                                            <a class="icon-btn" href="${editUrl}">
                                                <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"/>
                                                </svg>
                                            </a>
                                        @endif
                                        @if (app('hasPermission')(1, 'delete') || app('hasPermission')(16, 'delete'))
                                            <a class="confirm-text" href="javascript:void(0);" data-id="${branch.id}">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 4H15V6H9V4ZM8 8H17V20H7V8H8Z" fill="#092C4C"/>
                                                    <path d="M9 10H11V18H9V10ZM13 10H15V18H13V10Z" fill="#092C4C"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                `;

                                let detailsToggle = `
                                    <a href="#details-${branch.id}" class="toggle-details" data-bs-toggle="collapse">
                                        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                    </a>
                                `;

                                let branchInfo = `
                                    <div style="display: flex; align-items: center;">
                                        <a href="javascript:void(0);" style="color: #1b2850; font-weight: 500;">
                                            ${name}
                                        </a>
                                    </div>
                                    <div class="collapse mt-2 d-xl-none" id="details-${branch.id}">
                                        <div class="">
                                            <p class="mb-1"><strong>S.No:</strong> ${sno}</p>
                                            <p class="mb-1"><strong>Branch Code:</strong> ${branchCode}</p>
                                            <p class="mb-1"><strong>Email:</strong> ${email}</p>
                                            <p class="mb-1"><strong>Phone #1:</strong> ${phone1}</p>
                                            <p class="mb-1"><strong>City/Town:</strong> ${city}</p>
                                            <p class="mb-1"><strong>State:</strong> ${state}</p>
                                            <p class="mb-1"><strong>Status:</strong> ${statusBadge}</p>
                                            <div class="mt-2">
                                                ${actionButtons}
                                            </div>
                                        </div>
                                    </div>
                                `;

                                tableBody.push([
                                    branchInfo,
                                    detailsToggle,
                                    sno,
                                    branchCode,
                                    email,
                                    phone1,
                                    city,
                                    state,
                                    statusBadge,
                                    actionButtons
                                ]);
                            });

                            table.clear().rows.add(tableBody).draw(false);
                        } else {
                            table.clear().draw(false);
                            updatePaginationUI(null);
                        }
                    },
                    error: function(xhr) {
                        console.error('API Error:', xhr);
                        table.clear().draw(false);
                    }
                });
            }

            function updatePaginationUI(pagination) {
                let paginationHtml = '';
                if (pagination && pagination.total > 0) {
                    $('#pagination-from').text(pagination.from);
                    $('#pagination-to').text(pagination.to);
                    $('#pagination-total').text(pagination.total);

                    paginationHtml += `<li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>
                    </li>`;

                    let startPage = Math.max(1, pagination.current_page - 2);
                    let endPage = Math.min(pagination.last_page, startPage + 4);
                    if (endPage - startPage < 4) {
                        startPage = Math.max(1, endPage - 4);
                    }

                    if (startPage > 1) {
                        paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
                        if (startPage > 2) {
                            paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
                        }
                    }

                    for (let i = startPage; i <= endPage; i++) {
                        paginationHtml += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>`;
                    }

                    if (endPage < pagination.last_page) {
                        if (endPage < pagination.last_page - 1) {
                            paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
                        }
                        paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.last_page}">${pagination.last_page}</a></li>`;
                    }

                    paginationHtml += `<li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>
                    </li>`;
                } else {
                    $('#pagination-from').text('0');
                    $('#pagination-to').text('0');
                    $('#pagination-total').text('0');
                }

                $('#pagination-numbers').html(paginationHtml);
            }

            $(document).on('click', '.page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page > 0 && page <= lastPage) {
                    fetchAccountBranches(page);
                }
            });

            $('#per-page-select').change(function() {
                perPage = $(this).val();
                fetchAccountBranches(1);
            });

            let searchTimeout;
            $('#search-input').on('input', function() {
                clearTimeout(searchTimeout);
                searchQuery = $(this).val();
                searchTimeout = setTimeout(() => fetchAccountBranches(1), 500);
            });

            fetchAccountBranches();

            $(document).on('click', '.confirm-text', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/deleteAccountBranch/${id}`,
                            type: "POST",
                            headers: {
                                "Authorization": "Bearer " + authToken,
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire("Deleted!", response.message, "success");
                                    fetchAccountBranches(currentPage);
                                }
                            },
                            error: function(xhr) {
                                Swal.fire("Error!", "Failed to delete.", "error");
                            }
                        });
                    }
                });
            });

            // Handle toggle icon change
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
        });
    </script>
@endpush
