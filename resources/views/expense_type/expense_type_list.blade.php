@extends('layout.app')

@section('title', 'Expense Type List')

@section('content')
    <style>
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
        .search-input {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .search-input input {
            padding-left: 36px !important;
            border-radius: 8px;
            height: 38px;
            border: 1px solid #d0d5dd;
            width: 100%;
        }

        .btn-searchset {
            position: absolute;
            left: 10px;
            z-index: 10;
            top: 8px !important;
            padding: 0;
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

        .table.datanew tbody td:nth-child(2),
        .table.datanew thead th:nth-child(2) {
            max-width: 400px;
            white-space: normal !important;
            word-break: break-word;
            overflow-wrap: anywhere;
            text-align: left;
        }

        .table.datanew tbody td:nth-child(3) {
            white-space: nowrap;
        }

        /* Mobile Expense Type Card Styles */
        .exp-type-m-card {
            background: #fff;
            border: 1px solid #edf2f7;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 8px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.05);
            border-left: 4px solid #ff9f43;
            transition: transform 0.15s ease;
        }

        .exp-type-m-sr {
            font-size: 11px;
            font-weight: 700;
            color: #ff9f43;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 6px;
            padding: 2px 7px;
            flex-shrink: 0;
        }

        .exp-type-m-title {
            font-size: 14.5px;
            font-weight: 700;
            color: #1b2850;
            word-break: break-word;
        }

        .exp-type-m-actions {
            flex-shrink: 0;
        }

        .exp-type-action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #334155;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 0;
        }

        .exp-type-action-btn:hover {
            background: #f8fafc;
        }

        .exp-type-action-btn.edit:hover {
            border-color: #fed7aa;
            background: #fff7ed;
        }

        .exp-type-action-btn.delete:hover {
            border-color: #fecdd3;
            background: #fff1f2;
        }

        .exp-type-action-btn img {
            width: 14px;
            height: 14px;
        }

        .expense-type-empty-card {
            background: #fff;
            border-radius: 12px;
            border: 1px dashed #cbd5e1;
            padding: 28px 16px;
            text-align: center;
            margin: 10px 0;
        }

        /* Tablets & Mobile (< 1200px) */
        @media screen and (max-width: 1199.98px) {
            .content {
                padding: 12px 12px 95px !important;
                max-width: 100vw;
            }

            .page-header {
                display: flex !important;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 12px !important;
                background: #fff;
                border-radius: 12px;
                padding: 12px 14px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            }

            .page-title h4 {
                font-size: 16px !important;
                font-weight: 700 !important;
                color: #1b2850 !important;
                margin: 0 !important;
            }

            .btn-added {
                display: inline-flex !important;
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

            .card {
                border-radius: 12px !important;
                border: 1px solid #edf2f7;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .06);
            }

            .card-body {
                padding: 14px !important;
            }
        }

        /* Mobile phones (< 768px) */
        @media screen and (max-width: 767.98px) {
            .search-set {
                width: 100% !important;
                margin-bottom: 12px;
            }

            .pagination-controls {
                gap: 10px;
            }

            .pagination-controls .d-flex {
                justify-content: space-between;
                width: 100%;
            }
        }

        /* Fade out animation for error messages */
        .fade-out {
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }

        .fade-out.hidden {
            opacity: 0;
        }
    </style>
    @if (session('error'))
        <div class="alert alert-danger" id="error-message">
            {{ session('error') }}
        </div>

        <style>
            .fade-out {
                opacity: 1;
                transition: opacity 0.5s ease-out;
            }

            .fade-out.hidden {
                opacity: 0;
            }

            .dataTables_info {
                float: left !important;
            }

            @media screen and (max-width: 768px) {
                input.form-control.form-control-sm {
                    margin-top: 10px;
                }
            }
        </style>

        <script>
            setTimeout(function() {
                let alert = document.getElementById('error-message');
                if (alert) {
                    alert.classList.add('hidden'); // Triggers the fade-out transition
                    // Remove the element from DOM after fadeout (optional)
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500); // match the CSS transition duration (0.5s)
                }
            }, 4000);
        </script>
    @endif
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>All Expenses Type</h4>
                <!--<h6>Manage your purchases</h6>-->
            </div>
            <div class="page-btn">
                @if (app('hasPermission')(5, 'view'))
                    <a href="{{ route('expensetype.add') }}" class="btn btn-sm btn-added"><img
                            src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-2" alt="img">New
                        Expense Type</a>
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
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}"
                                    alt="img">
                            </a>
                            <input type="text" id="search-input" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                </div>

                <div class="table-container d-none d-md-block">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Expense Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="expense-table-body">
                            <!-- Dynamic rows here -->
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards Container (< 768px) -->
                <div id="mobile-expense-type-cards" class="d-md-none">
                    <!-- Dynamic cards here -->
                </div>
                {{-- Pagination Controls (same as category) --}}
                <div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3"
                    style="display: none;">
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
                            {{-- Page numbers inserted here --}}
                        </ul>
                    </nav>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('js')
    <script>
        const userRole = "{{ auth()->user()->role }}";
    </script>

    {{-- <script>
        $(document).ready(function () {
            var authToken = localStorage.getItem("authToken");
            var table = $('.datanew').DataTable({
                "destroy": true,
                "bFilter": false,
                "paging": false,
                "info": false,
                "searching": false,
                "dom": 't',
                "ordering": false
            }); // Initialize DataTable
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            // Initialize DataTable with search logic enabled
            // var table = $('.datanew').DataTable({
            //     responsive: true,
            //     autoWidth: false,
            //     pageLength: 10,
            //     ordering: true,
            //     searching: true, // Enable search logic
            //     paging: true,
            //     destroy: true,
            //     language: {
            //         emptyTable: "No expenses found.",
            //         zeroRecords: "No expense record found.",
            //     },
            // });

            fetchExpenses();

            function fetchExpenses() {
                $.ajax({
                    url: '/api/expense-types', // Make sure this returns only isDeleted = 0
                    method: 'GET',
                    data: { selectedSubAdminId: selectedSubAdminId },
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function (response) {
                        let tableRows = [];

                        response.data.forEach(function (item, index) {
                               let deleteBtn = '';

                                if (
                                    userRole !== 'sales-manager' &&
                                    userRole !== 'purchase-manager' &&
                                    userRole !== 'inventory-manager'
                                ) {
                                        @if (app('hasPermission')(5, 'delete'))
                                    deleteBtn = `
                                        <a class="me-3 delete-btn" href="javascript:void(0);" data-id="${item.id}">
                                            <img src="{{ env('ImagePath').'admin/assets/img/icons/delete.svg' }}" alt="Delete">
                                        </a>`;
                                        @endif
                                }
                            tableRows.push([
                                index + 1, // S.No.
                                item.type,
                                `
                            @if (app('hasPermission')(5, 'edit'))
                        <a class="me-3" href="/edit-expense-type/${item.id}">
                            <img src="{{ env('ImagePath').'admin/assets/img/icons/edit.svg' }}" alt="Edit">
                        </a>
                        @endif
                       ${deleteBtn}
                        `
                            ]);
                        });

                        table.clear().rows.add(tableRows).draw();
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Failed to fetch expense types!',
                            confirmButtonColor: '#ff9f43'
                        });
                    }
                });
            }

            $(document).on("click", ".delete-btn", function () {
                let id = $(this).data("id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    confirmButtonColor: "#ff9f43", // Confirm button color (orange)
                    cancelButtonColor: "#6c757d", // Cancel button color (gray)
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/expenses-type/${id}`,
                            type: "POST",
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                                "Authorization": "Bearer " + authToken,
                            },
                            success: function (response) {
                                // Swal.fire("Deleted!", response.message, "success");
                                Swal.fire({
                                    title: "Deleted!",
                                    text: "Delete Record successfully",
                                    icon: "success",
                                    confirmButtonColor: "#ff9f43", // Custom OK button color
                                    confirmButtonText: "OK"
                                });
                                fetchExpenses(); // Refresh the table after deletion
                            },
                            error: function (xhr) {

                                let message = 'Something went wrong.';

                                if (xhr.responseJSON) {
                                    if (xhr.responseJSON.message) {
                                        message = xhr.responseJSON.message;
                                    }
                                }

                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Cannot Delete',
                                    text: message,
                                    confirmButtonColor: '#ff9f43'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script> --}}

    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

            // Initialize DataTable WITHOUT built-in search/pagination
            var table = $('.datanew').DataTable({
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

            // Initial fetch
            fetchExpenseTypes(currentPage);

            // Search input handler
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                fetchExpenseTypes(1);
            });

            // Per‑page change handler
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchExpenseTypes(1);
            });

            // Page number click handler
            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchExpenseTypes(page);
                }
            });

            function capitalizeWords(text) {
                if (!text) return '';
                return text.replace(/\b\w/g, char => char.toUpperCase());
            }

            function fetchExpenseTypes(page = 1) {
                let url = `/api/expense-types?page=${page}&per_page=${perPage}`;
                if (selectedSubAdminId) {
                    url += `&selectedSubAdminId=${selectedSubAdminId}`;
                }
                if (searchQuery) {
                    url += `&search=${encodeURIComponent(searchQuery)}`;
                }

                $.ajax({
                    url: url,
                    method: 'GET',
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            let items = response.data;
                            let pagination = response.pagination;

                            currentPage = pagination.current_page;
                            lastPage = pagination.last_page;

                            updatePaginationUI(pagination);

                            // Build table rows
                            let tableRows = [];
                            items.forEach(function(item, index) {
                                // Calculate correct serial number based on current page and perPage
                                let serial = (pagination.current_page - 1) * pagination
                                    .per_page + (index + 1);

                                // Delete button permission (same as before)
                                let deleteBtn = '';
                                if (
                                    userRole !== 'sales-manager' &&
                                    userRole !== 'purchase-manager' &&
                                    userRole !== 'inventory-manager'
                                ) {
                                    @if (app('hasPermission')(5, 'delete'))
                                        deleteBtn = `
                                        <a class="me-3 delete-btn" href="javascript:void(0);" data-id="${item.id}">
                                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}" alt="Delete">
                                        </a>`;
                                    @endif
                                }

                                // Action buttons (edit always shown if permission)
                                let actionButtons = `
                                @if (app('hasPermission')(5, 'edit'))
                                    <a class="me-3" href="/edit-expense-type/${item.id}">
                                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}" alt="Edit">
                                    </a>
                                @endif
                                ${deleteBtn}
                            `;

                                tableRows.push([
                                    serial,
                                    capitalizeWords(item.type),
                                    actionButtons
                                ]);
                            });

                            table.clear().rows.add(tableRows).draw();
                            renderMobileExpenseTypeCards(items, pagination);
                            $('.pagination-controls').show();
                        } else {
                            table.clear().draw();
                            $(".datanew tbody").html(
                                '<tr><td colspan="3">No expense types found</td></tr>');
                            renderMobileExpenseTypeCards([], pagination || {});
                            $('.pagination-controls').hide();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Failed to fetch expense types!',
                            confirmButtonColor: '#ff9f43'
                        });
                        renderMobileExpenseTypeCards([], {});
                        $('.pagination-controls').hide();
                    }
                });
            }

            function updatePaginationUI(pagination) {
                let from = pagination.from || 0;
                let to = pagination.to || 0;
                $('#pagination-from').text(from);
                $('#pagination-to').text(to);
                $('#pagination-total').text(pagination.total);

                let paginationHtml = '';
                const currentPage = pagination.current_page || 1;
                const totalPages = pagination.last_page || 1;
                paginationHtml += `
                    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${currentPage - 1}">Previous</a>
                    </li>
                `;

                const visiblePageCount = 2;
                let startPage = Math.floor((currentPage - 1) / visiblePageCount) * visiblePageCount + 1;
                let endPage = Math.min(totalPages, startPage + visiblePageCount - 1);

                if (startPage > 1) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);" data-page="${startPage - 1}" data-action="prev-group">..</a>
                        </li>
                    `;
                }

                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                        </li>
                    `;
                }

                if (endPage < totalPages) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);" data-page="${endPage + 1}" data-action="next-group">..</a>
                        </li>
                    `;
                }

                paginationHtml += `
                    <li class="page-item ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${currentPage + 1}">Next</a>
                    </li>
                `;

                $('#pagination-numbers').html(paginationHtml);
            }

            function renderMobileExpenseTypeCards(items, pagination) {
                const $container = $('#mobile-expense-type-cards');
                if (!items || items.length === 0) {
                    $container.html(`
                        <div class="expense-type-empty-card">
                            <i class="fas fa-tag fa-2x text-muted mb-2"></i>
                            <h6 class="text-muted fw-bold mb-1">No expense types found</h6>
                            <p class="text-muted small mb-0">Try searching for a different keyword</p>
                        </div>
                    `);
                    return;
                }

                const imagePath = "{{ env('ImagePath') }}";
                let html = '';

                items.forEach(function(item, index) {
                    let serial = ((pagination?.current_page || 1) - 1) * (pagination?.per_page || perPage) + (index + 1);

                    let deleteBtn = '';
                    if (
                        userRole !== 'sales-manager' &&
                        userRole !== 'purchase-manager' &&
                        userRole !== 'inventory-manager'
                    ) {
                        @if (app('hasPermission')(5, 'delete'))
                            deleteBtn = `
                            <button type="button" class="exp-type-action-btn delete delete-btn" data-id="${item.id}" title="Delete">
                                <img src="${imagePath}admin/assets/img/icons/delete.svg" alt="Delete">
                            </button>`;
                        @endif
                    }

                    let editBtn = `
                    @if (app('hasPermission')(5, 'edit'))
                        <a class="exp-type-action-btn edit" href="/edit-expense-type/${item.id}" title="Edit">
                            <img src="${imagePath}admin/assets/img/icons/edit.svg" alt="Edit">
                        </a>
                    @endif`;

                    html += `
                        <div class="exp-type-m-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="exp-type-m-sr">#${serial}</span>
                                    <h6 class="exp-type-m-title mb-0">${capitalizeWords(item.type)}</h6>
                                </div>
                                <div class="exp-type-m-actions d-flex align-items-center gap-1">
                                    ${editBtn}
                                    ${deleteBtn}
                                </div>
                            </div>
                        </div>
                    `;
                });

                $container.html(html);
            }

            // Delete handler (unchanged)
            $(document).on("click", ".delete-btn", function() {
                let id = $(this).data("id");

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
                            url: `/api/expenses-type/${id}`,
                            type: "POST",
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                    "content"),
                                "Authorization": "Bearer " + authToken,
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: "Delete Record successfully",
                                    icon: "success",
                                    confirmButtonColor: "#ff9f43",
                                    confirmButtonText: "OK"
                                });
                                fetchExpenseTypes(currentPage); // Refresh current page
                            },
                            error: function(xhr) {
                                let message = 'Something went wrong.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Cannot Delete',
                                    text: message,
                                    confirmButtonColor: '#ff9f43'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
