@extends('layout.app')
@section('title', 'Table Truncate')

@section('content')
    <style>
        .table-top .search-set {
            width: 100%;
        }
        .table-top .search-input {
            max-width: 320px;
        }
        .table-top .search-input .form-control {
            height: 38px;
        }
        .truncate-toolbar {
            width: 100%;
            gap: 10px;
        }
        .truncate-toolbar .search-input {
            flex: 1 1 auto;
            min-width: 0;
        }
        .truncate-toolbar .truncate-all-wrap {
            flex: 0 0 auto;
        }
        #truncate-table_wrapper .dataTables_paginate,
        #truncate-table_wrapper .dataTables_info,
        #truncate-table_wrapper .dataTables_length,
        #truncate-table_wrapper .dataTables_filter {
            display: none !important;
        }

        /* Desktop: show all columns normally */
        @media (min-width: 1200px) {
            table#truncate-table thead th.details-column,
            table#truncate-table tbody td:nth-child(2) {
                display: none !important;
            }
        }

        /* Mobile: hide non-essential columns, show Details toggle */
        @media (max-width: 1199px) {
            table#truncate-table thead th:nth-child(n+3),
            table#truncate-table tbody td:nth-child(n+3) {
                display: none !important;
            }

            table#truncate-table thead th.details-column,
            table#truncate-table tbody td:nth-child(2) {
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
            
            table#truncate-table tbody td:first-child {
                max-width: calc(100vw - 100px) !important;
            }
        }

        @media (max-width: 576px) {
            .table-top .search-input {
                max-width: 100%;
            }
            .truncate-toolbar {
                flex-direction: column;
                align-items: stretch !important;
            }
            .truncate-toolbar .truncate-all-wrap,
            .truncate-toolbar .truncate-all-wrap .btn {
                width: 100%;
            }
            .pagination-controls {
                margin-top: 10px !important;
            }
            .widtth{
                width: 330px !important;
            }
                
        }

        .action-buttons {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }
    </style>

        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Table Truncate</h4>
                    <h6>Branch-wise table data cleanup</h6>
                </div>
                <div class="page-btn">
                    <a href="{{ route('auth.dashboard') }}" class="btn btn-added">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success d-none" id="truncate-success-message">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger d-none" id="truncate-error-message">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <p class="mb-3"><strong>Selected Branch ID:</strong> {{ $branchId }}</p>
                    <div class="table-top mb-3">
                        <div class="search-set d-flex justify-content-between align-items-center truncate-toolbar">
                            <div class="search-input">
                                <input type="text" id="truncate-search-input" class="form-control" placeholder="Search...">
                            </div>
                            <form method="POST" action="{{ route('table-truncate.truncate-all') }}" id="truncate-all-form" class="d-inline truncate-all-wrap">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm" title="Truncate all supported tables for this branch">
                                    <i class="fa fa-trash me-1"></i> Truncate All
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered widtth" id="truncate-table">
                            <thead>
                                <tr>
                                    <th>Table Name</th>
                                    <th class="details-column">Details</th>
                                    <th>Branch Records</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tables as $table)
                                    <tr>
                                        <td>
                                            <div style="display: flex;">
                                                <span style="color: #1b2850; font-weight: 500;">{{ $table['name'] }}</span>
                                            </div>
                                            <div class="collapse mt-2 d-xl-none" id="details-{{ Str::slug($table['name']) }}">
                                                <div>
                                                    <p class="mb-1"><strong>Branch Records:</strong> 
                                                        @if ($table['has_branch_id'])
                                                            {{ $table['branch_records'] }}
                                                        @else
                                                            <span class="badge bg-secondary">No branch_id/created_by column</span>
                                                        @endif
                                                    </p>
                                                    <div class="mt-2 action-buttons">
                                                        @if ($table['has_branch_id'])
                                                            <form method="POST"
                                                                action="{{ route('table-truncate.truncate', ['table' => $table['name']]) }}"
                                                                class="truncate-form d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-danger"
                                                                    title="Truncate branch data">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-light" disabled>Not Allowed</button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="#details-{{ Str::slug($table['name']) }}" class="toggle-details" data-bs-toggle="collapse">
                                                <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                            </a>
                                        </td>
                                        <td>
                                            @if ($table['has_branch_id'])
                                                {{ $table['branch_records'] }}
                                            @else
                                                <span class="badge bg-secondary">No branch_id/created_by column</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($table['has_branch_id'])
                                                <form method="POST"
                                                    action="{{ route('table-truncate.truncate', ['table' => $table['name']]) }}"
                                                    class="truncate-form d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        title="Truncate branch data">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-sm btn-light" disabled>Not Allowed</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No tables found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
                        <div class="d-flex align-items-center mb-3 mb-md-0">
                            <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
                            <select id="truncate-per-page-select" class="form-select form-select-sm" style="width: auto; border: 1px solid #ddd;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="ms-3" style="font-size: 14px; color: #555;">
                                <span id="truncate-pagination-from">0</span> - <span id="truncate-pagination-to">0</span> of <span id="truncate-pagination-total">0</span> items
                            </span>
                        </div>
                        <nav aria-label="Table truncate pagination">
                            <ul class="pagination pagination-sm mb-0" id="truncate-pagination-numbers"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMessageEl = document.getElementById('truncate-success-message');
            const errorMessageEl = document.getElementById('truncate-error-message');
            const successMessage = @json(session('success'));
            const errorMessage = @json(session('error'));

            const showSweetAlert = function(icon, title, text) {
                if (window.Swal && typeof window.Swal.fire === 'function') {
                    window.Swal.fire({
                        icon: icon,
                        title: title,
                        text: text,
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert(text);
                }
            };

            if (successMessage && String(successMessage).trim() !== '') {
                showSweetAlert('success', 'Success', String(successMessage).trim());
            } else if (successMessageEl && successMessageEl.textContent.trim() !== '') {
                showSweetAlert('success', 'Success', successMessageEl.textContent.trim());
            }

            if (errorMessage && String(errorMessage).trim() !== '') {
                showSweetAlert('error', 'Error', String(errorMessage).trim());
            } else if (errorMessageEl && errorMessageEl.textContent.trim() !== '') {
                showSweetAlert('error', 'Error', errorMessageEl.textContent.trim());
            }

            document.querySelectorAll('.truncate-form').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (window.Swal && typeof window.Swal.fire === 'function') {
                        window.Swal.fire({
                            title: 'Are you sure?',
                            text: 'Are you sure you want to delete this record?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else {
                        if (confirm('Are you sure you want to delete this record?')) {
                            form.submit();
                        }
                    }
                });
            });

            const truncateAllForm = document.getElementById('truncate-all-form');
            if (truncateAllForm) {
                truncateAllForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (window.Swal && typeof window.Swal.fire === 'function') {
                        window.Swal.fire({
                            title: 'Truncate all tables?',
                            text: 'This will truncate all supported tables for selected branch, keeping protected data.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, truncate all',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                truncateAllForm.submit();
                            }
                        });
                    } else if (confirm('Are you sure you want to truncate all supported tables?')) {
                        truncateAllForm.submit();
                    }
                });
            }

            if (window.jQuery && $.fn.DataTable) {
                if ($.fn.DataTable.isDataTable('#truncate-table')) {
                    $('#truncate-table').DataTable().destroy();
                }

                const table = $('#truncate-table').DataTable({
                    bFilter: false,
                    paging: true,
                    info: false,
                    searching: true,
                    lengthChange: false,
                    pageLength: 10,
                    dom: 't',
                    ordering: true,
                    order: [],
                    columnDefs: [
                        { targets: 1, orderable: false }
                    ]
                });

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

                function renderPagination() {
                    const info = table.page.info();
                    const currentPage = info.page + 1;
                    const lastPage = info.pages || 1;
                    const total = info.recordsDisplay || 0;
                    const from = total === 0 ? 0 : info.start + 1;
                    const to = total === 0 ? 0 : info.end;

                    $('#truncate-pagination-from').text(from);
                    $('#truncate-pagination-to').text(to);
                    $('#truncate-pagination-total').text(total);

                    let paginationHtml = '';
                    paginationHtml += `
                        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                            <a class="page-link truncate-page-link" href="javascript:void(0);" data-page="${currentPage - 1}">Previous</a>
                        </li>
                    `;

                    const visiblePageCount = 2;
                    const startPage = Math.floor((currentPage - 1) / visiblePageCount) * visiblePageCount + 1;
                    const endPage = Math.min(lastPage, startPage + visiblePageCount - 1);

                    if (startPage > 1) {
                        paginationHtml += `
                            <li class="page-item">
                                <a class="page-link truncate-page-link" href="javascript:void(0);" data-page="${startPage - 1}">..</a>
                            </li>
                        `;
                    }

                    for (let i = startPage; i <= endPage; i++) {
                        paginationHtml += `
                            <li class="page-item ${i === currentPage ? 'active' : ''}">
                                <a class="page-link truncate-page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                            </li>
                        `;
                    }

                    if (endPage < lastPage) {
                        paginationHtml += `
                            <li class="page-item">
                                <a class="page-link truncate-page-link" href="javascript:void(0);" data-page="${endPage + 1}">..</a>
                            </li>
                        `;
                    }

                    paginationHtml += `
                        <li class="page-item ${currentPage === lastPage || total === 0 ? 'disabled' : ''}">
                            <a class="page-link truncate-page-link" href="javascript:void(0);" data-page="${currentPage + 1}">Next</a>
                        </li>
                    `;

                    $('#truncate-pagination-numbers').html(paginationHtml);
                    $('.pagination-controls').toggle(total > 0);
                }

                $('#truncate-search-input').on('keyup', function() {
                    table.search($(this).val()).draw();
                    renderPagination();
                });

                $('#truncate-per-page-select').on('change', function() {
                    const value = parseInt($(this).val(), 10) || 10;
                    table.page.len(value).draw();
                    renderPagination();
                });

                $(document).on('click', '.truncate-page-link', function(e) {
                    e.preventDefault();
                    const page = parseInt($(this).data('page'), 10);
                    const info = table.page.info();
                    if (!Number.isNaN(page) && page >= 1 && page <= info.pages) {
                        table.page(page - 1).draw('page');
                        renderPagination();
                    }
                });



                table.on('draw', function() {
                    renderPagination();
                });

                renderPagination();
            }
        });
    </script>
@endsection
