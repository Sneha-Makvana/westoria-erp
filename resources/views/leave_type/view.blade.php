@extends('layout.app')

@section('title', 'Leave Types')

@section('content')
    <style>
        .table-search-wrap {
            max-width: 280px;
        }

        /* Desktop: show all columns normally except details toggle */
        @media (min-width: 1200px) {
            .col-details,
            table#leaveTypeTable thead th.details-column,
            table#leaveTypeTable tbody td.col-details {
                display: none !important;
            }
        }

        /* Mobile: hide hash, leave stats, actions; show details toggle and main */
        @media (max-width: 1199px) {
            .col-hash,
            .col-num-leaves,
            .col-half-day,
            .col-action,
            table#leaveTypeTable thead th.col-hash,
            table#leaveTypeTable thead th.col-num-leaves,
            table#leaveTypeTable thead th.col-half-day,
            table#leaveTypeTable thead th.col-action,
            table#leaveTypeTable tbody td.col-hash,
            table#leaveTypeTable tbody td.col-num-leaves,
            table#leaveTypeTable tbody td.col-half-day,
            table#leaveTypeTable tbody td.col-action {
                display: none !important;
            }

            .col-details,
            .col-main,
            table#leaveTypeTable thead th.col-details,
            table#leaveTypeTable thead th.col-main,
            table#leaveTypeTable tbody td.col-details,
            table#leaveTypeTable tbody td.col-main {
                display: table-cell !important;
                text-align: left;
            }

            table#leaveTypeTable thead th.col-details,
            table#leaveTypeTable tbody td.col-details {
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
            
            table#leaveTypeTable tbody td.col-main {
                max-width: calc(100vw - 100px) !important;
            }
        }

        .action-buttons {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
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

        .pagination .page-item.disabled .page-link {
            background-color: #fff !important;
            color: #dee2e6 !important;
            border: 1px solid #dee2e6 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }
    </style>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Manage Leave Types</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('leave-type.create') }}" class="btn btn-added">
                    <i class="fa fa-plus me-1"></i> Add Leave Type
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-search-wrap mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                        <input type="text" class="form-control" id="leaveTypeSearch" placeholder="Search...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="leaveTypeTable">
                        <thead>
                            <tr>
                                <th class="col-hash">#</th>
                                <th class="col-main">Leave Type</th>
                                <th class="col-num-leaves">Number of Leaves</th>
                                <th class="col-half-day">Allow Half Day</th>
                                <th class="col-details details-column">Details</th>
                                <th class="col-action text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <!-- Pagination Controls -->
                <div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
                        <select id="leaveTypePerPage" class="form-select form-select-sm"
                            style="width: auto; border: 1px solid #ddd;">
                            <option value="10" selected>10</option>
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
        $(function() {
            const token = (typeof window.getAuthToken === 'function'
                ? window.getAuthToken()
                : (localStorage.getItem('authToken') || localStorage.getItem('token') || ''));
            const editOpenUrl = "{{ route('leave-type.edit.open') }}";
            const csrfToken = "{{ csrf_token() }}";
            const state = {
                page: 1,
                perPage: 10,
                lastPage: 1,
                total: 0,
                search: '',
            };
            let searchDebounceTimer = null;

            if (!token) {
                Swal.fire('Unauthorized', 'Please login again to continue.', 'warning');
                return;
            }

            $('#leaveTypePerPage').on('change', function() {
                state.perPage = Number($(this).val()) || 10;
                state.page = 1;
                loadLeaveTypes();
            });

            $('#leaveTypePrevPage').on('click', function() {
                if (state.page <= 1) {
                    return;
                }
                state.page -= 1;
                loadLeaveTypes();
            });

            $('#leaveTypeNextPage').on('click', function() {
                if (state.page >= state.lastPage) {
                    return;
                }
                state.page += 1;
                loadLeaveTypes();
            });

            $('#leaveTypeSearch').on('input', function() {
                const value = $(this).val().trim();
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(() => {
                    if (state.search === value) {
                        return;
                    }
                    state.search = value;
                    state.page = 1;
                    loadLeaveTypes();
                }, 350);
            });

            function loadLeaveTypes() {
                $.ajax({
                    url: '/api/leavetype',
                    method: 'GET',
                    data: {
                        page: state.page,
                        per_page: state.perPage,
                        search: state.search,
                    },
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                }).done((response) => {
                    const records = response.data || [];
                    const pagination = response.pagination || {};

                    state.page = Number(pagination.current_page || state.page || 1);
                    state.lastPage = Number(pagination.last_page || 1);
                    state.perPage = Number(pagination.per_page || state.perPage || 10);
                    state.total = Number(pagination.total ?? records.length);

                    if (records.length === 0 && state.page > 1 && state.page > state.lastPage) {
                        state.page = state.lastPage;
                        loadLeaveTypes();
                        return;
                    }

                    const startIndex = state.total === 0 ? 0 : ((state.page - 1) * state.perPage) + 1;
                    const rows = records.map((item, index) => {
                        const leaveType = item.leave_type ?? '-';
                        const numLeaves = item.number_of_leaves ?? 0;
                        const halfDay = Number(item.allow_half_day) === 1 ? 'Yes' : 'No';
                        const actions = `
                                <a class="me-3 openLeaveTypeEdit" data-id="${item.id}" href="javascript:void(0);">
                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}" alt="Edit">
                                </a>
                                <a class="me-3 confirm-text deleteLeaveType" data-id="${item.id}" href="javascript:void(0);">
                                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}" alt="Delete">
                                </a>
                        `;

                        return `
                        <tr>
                            <td class="col-hash">${startIndex + index}</td>
                            <td class="col-main">
                                <div style="display: flex;">
                                    <span style="color: #1b2850; font-weight: 500;">${leaveType}</span>
                                </div>
                                <div class="collapse mt-2 d-xl-none" id="details-${item.id}">
                                    <div>
                                        <p class="mb-1"><strong>#:</strong> ${startIndex + index}</p>
                                        <p class="mb-1"><strong>Number of Leaves:</strong> ${numLeaves}</p>
                                        <p class="mb-1"><strong>Allow Half Day:</strong> ${halfDay}</p>
                                        <div class="mt-2 action-buttons">
                                            ${actions}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-num-leaves">${numLeaves}</td>
                            <td class="col-half-day">${halfDay}</td>
                            <td class="col-details">
                                <a href="#details-${item.id}" class="toggle-details" data-bs-toggle="collapse">
                                    <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                </a>
                            </td>
                            <td class="col-action text-end">
                                <div class="action-buttons justify-content-end">
                                    ${actions}
                                </div>
                            </td>
                        </tr>
                    `}).join('');

                    $('#leaveTypeTable tbody').html(rows || '<tr><td colspan="6" class="text-center">No records found</td></tr>');
                    updatePaginationUI();
                }).fail(handleError);
            }

            $(document).on('click', '.deleteLeaveType', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Delete Leave Type?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ff9f43',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Delete'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: `/api/leavetype/${id}`,
                        method: 'DELETE',
                        headers: {
                            Authorization: `Bearer ${token}`
                        }
                    }).done((response) => {
                        Swal.fire('Deleted', response.message, 'success');
                        loadLeaveTypes();
                    }).fail(handleError);
                });
            });

            $(document).on('click', '.openLeaveTypeEdit', function() {
                const id = $(this).data('id');
                const form = $('<form>', {
                    method: 'POST',
                    action: editOpenUrl,
                    style: 'display:none;'
                });

                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: csrfToken
                }));
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'id',
                    value: id
                }));

                $('body').append(form);
                form.trigger('submit');
            });

            function updatePaginationUI() {
                let from = (state.page - 1) * state.perPage + 1;
                let to = state.page * state.perPage;
                if (to > state.total) to = state.total;
                if (state.total === 0) from = 0;

                $('#pagination-from').text(from);
                $('#pagination-to').text(to);
                $('#pagination-total').text(state.total);

                let paginationHtml = '';

                // Previous button
                paginationHtml += `
                    <li class="page-item ${state.page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${state.page - 1}">Previous</a>
                    </li>
                `;

                // Show only 2 page numbers at a time
                const visiblePageCount = 2;
                let startPage = Math.floor((state.page - 1) / visiblePageCount) * visiblePageCount + 1;
                let endPage = Math.min(state.lastPage, startPage + visiblePageCount - 1);

                // Show previous ellipsis if there are pages before startPage
                if (startPage > 1) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);" data-page="${startPage - 1}">..</a>
                        </li>
                    `;
                }

                // Generate page numbers
                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === state.page ? 'active' : ''}">
                            <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                        </li>
                    `;
                }

                // Show next ellipsis if there are more pages after endPage
                if (endPage < state.lastPage) {
                    if (endPage < state.lastPage - 1) {
                        paginationHtml += `
                            <li class="page-item disabled">
                                <a class="page-link" href="javascript:void(0);">..</a>
                            </li>
                        `;
                    }

                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0);" data-page="${state.lastPage}">${state.lastPage}</a>
                        </li>
                    `;
                }

                // Next button
                paginationHtml += `
                    <li class="page-item ${state.page === state.lastPage || state.lastPage === 0 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${state.page + 1}">Next</a>
                    </li>
                `;

                $('#pagination-numbers').html(paginationHtml);
                $('.pagination-controls').show();
            }

            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page) {
                    state.page = Number(page);
                    loadLeaveTypes();
                }
            });

            function handleError(xhr) {
                const message = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Something went wrong. Please try again.';
                Swal.fire('Error', message, 'error');
            }

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

            loadLeaveTypes();
        });
    </script>
@endpush
