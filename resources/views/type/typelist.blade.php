@extends('layout.app')

@section('title', 'Type List')

@section('content')
    <style>
        /* === Pagination styling === */
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
            top: 7px !important;
        }

        /* Hide default DataTables elements */
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

        .type-name {
            display: inline-block;
            font-size: 14px;
            line-height: 1.4;
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
        }

        @media screen and (min-width: 768px) and (max-width: 1024.98px) {
            .page-wrapper {
                padding-top: 0 !important;
            }

            .page-wrapper > .content.mm-page {
                margin-top: 0 !important;
                padding-top: 6px !important;
            }

            .mm-page > .page-header {
                display: none !important;
            }

            .mm-page > .mm-head.d-md-none {
                display: flex !important;
            }

            .mm-page .mm-card-list {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                align-items: start;
            }
        }

        /* Desktop */
        @media (min-width: 769px) {
            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: none !important;
            }
            .mobile-action-buttons {
                display: none !important;
            }
        }

        /* Mobile responsive design */
        @media (max-width: 768px) {
            table.datanew thead th:nth-child(3),
            table.datanew thead th:nth-child(4),
            table.datanew tbody td:nth-child(3),
            table.datanew tbody td:nth-child(4) {
                display: none !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(2) {
                display: table-cell !important;
                text-align: center;
                vertical-align: top !important;
                width: 56px !important;
                padding: 12px 6px !important;
            }

            .desktop-actions {
                display: none !important;
            }

            table.datanew tbody td:first-child {
                display: table-cell !important;
                width: auto;
                max-width: calc(100vw - 80px);
                padding: 12px 8px;
            }

            .mobile-action-buttons {
                display: flex !important;
                flex-direction: row;
                justify-content: flex-start;
                align-items: center;
                gap: 12px;
                margin-top: 12px;
                padding-top: 12px;
                border-top: 1px solid #e9ecef;
            }

            .mobile-action-buttons a {
                display: inline-flex !important;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                text-decoration: none;
                color: #2c3e50;
                font-size: 13px;
                font-weight: 500;
                cursor: pointer;
            }

            .pagination-controls {
                flex-direction: column;
                gap: 15px;
            }
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .action-buttons a {
            display: inline-flex;
            align-items: center;
            transition: 0.2s;
            cursor: pointer;
        }

        .action-buttons img {
            width: 20px;
            height: 20px;
        }

        .table-container {
            width: 100%;
        }

        table.datanew {
            width: 100%;
            min-width: 300px;
        }
    </style>

    <div class="content mm-page">
        <div class="mm-head d-md-none">
            <div>
                <div class="mm-title">Type Master</div>
                <div class="mm-count" id="mmCount"></div>
            </div>
            <a href="javascript:void(0);" class="btn btn-sm mm-add" data-bs-toggle="modal" data-bs-target="#addTypeModal"><i class="fas fa-plus me-1"></i> New Type</a>
        </div>

        <div class="page-header">
            <div class="page-title">
                <h4>All Types</h4>
                <h6>Manage Type / Category Masters</h6>
            </div>
            <div class="page-btn">
                <a href="javascript:void(0);" class="btn btn-sm btn-added" data-bs-toggle="modal" data-bs-target="#addTypeModal">
                    <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-1" alt="img">
                    New Type
                </a>
            </div>
        </div>

        <!-- Add Type Modal -->
        <div class="modal fade" id="addTypeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <form id="addTypeForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Type Name <span class="text-danger">*</span></label>
                                <input type="text" name="type_name" class="form-control" placeholder="e.g. Copper, Tambu" required>
                                <span class="error_type_name text-danger"></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="saveTypeBtn">Save Type</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Type Modal -->
        <div class="modal fade" id="editTypeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <form id="editTypeForm">
                        @csrf
                        <input type="hidden" id="edit_type_id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Type Name <span class="text-danger">*</span></label>
                                <input type="text" id="edit_type_name" class="form-control" placeholder="Enter Type Name" required>
                                <span class="error_edit_type_name text-danger"></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm" id="updateTypeBtn">Update Type</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-body">
                <div class="mb-3 mm-search">
                    <div class="search-set d-flex justify-content-start w-100">
                        <div class="search-input">
                            <a class="btn btn-searchset">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img">
                            </a>
                            <input type="text" id="search-input" class="form-control" placeholder="Search by type name...">
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>Type Name</th>
                                <th class="details-column">Details</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="type-table-body">
                            <!-- Populated via JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div class="mm-card-list d-md-none" id="mmCardList"></div>

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
                            <!-- Page numbers -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    @include('partials.master-mobile-ui')
    <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            var selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            function getHeaders() {
                var headers = {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                };
                if (authToken) {
                    headers['Authorization'] = 'Bearer ' + authToken;
                }
                return headers;
            }

            function escapeHtml(str) {
                if (!str) return '';
                return String(str)
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            var table = $('.datanew').DataTable({
                "destroy": true,
                "bFilter": false,
                "paging": false,
                "info": false,
                "searching": false,
                "dom": 't',
                "ordering": false
            });

            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';

            fetchTypes(currentPage);

            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                fetchTypes(1);
            });

            function fetchTypes(page = 1) {
                let url = `/api/types?page=${page}&per_page=${perPage}`;
                if (selectedSubAdminId) {
                    url += `&selectedSubAdminId=${selectedSubAdminId}`;
                }
                if (searchQuery) {
                    url += `&search=${encodeURIComponent(searchQuery)}`;
                }

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: getHeaders(),
                    success: function(response) {
                        if (response.status) {
                            let types = response.data;
                            let pagination = response.pagination;

                            currentPage = pagination.current_page;
                            lastPage = pagination.last_page;
                            updatePaginationUI(pagination);

                            let rowsHtml = '';
                            let mmCards = [];

                            if (types.length === 0) {
                                rowsHtml = `<tr><td colspan="4" class="text-center py-4 text-muted">No types found.</td></tr>`;
                            } else {
                                types.forEach(type => {
                                    let createdAt = 'N/A';
                                    if (type.created_at) {
                                        let d = new Date(type.created_at);
                                        if (!isNaN(d)) {
                                            createdAt = ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth() + 1)).slice(-2) + '/' + d.getFullYear();
                                        }
                                    }

                                    let typeName = escapeHtml(type.type_name);

                                    let detailsToggle = `
                                        <a href="javascript:void(0);" class="toggle-details" data-target="details-${type.id}">
                                            <i class="fas fa-plus-circle" style="color: #ff9f43; font-size: 20px;"></i>
                                        </a>
                                    `;

                                    let actionsHtml = `
                                        <div class="action-buttons desktop-actions">
                                            <a class="edit-type" href="javascript:void(0);" data-id="${type.id}" data-name="${typeName}" title="Edit">
                                                <img src="{{ env('ImagePath') . '/admin/assets/img/icons/edit.svg' }}" alt="Edit">
                                            </a>
                                            <a class="delete-type" href="javascript:void(0);" data-id="${type.id}" title="Delete">
                                                <img src="{{ env('ImagePath') . '/admin/assets/img/icons/delete.svg' }}" alt="Delete">
                                            </a>
                                        </div>
                                    `;

                                    rowsHtml += `
                                        <tr>
                                            <td>
                                                <span class="type-name fw-bold text-dark">${typeName}</span>
                                            </td>
                                            <td class="details-column text-center">${detailsToggle}</td>
                                            <td>${createdAt}</td>
                                            <td>${actionsHtml}</td>
                                        </tr>
                                        <tr class="collapse-row" id="details-${type.id}" style="display: none;">
                                            <td colspan="4">
                                                <div class="p-2 bg-light rounded">
                                                    <p class="mb-1"><strong>Created:</strong> ${createdAt}</p>
                                                    <div class="mobile-action-buttons">
                                                        <a class="edit-type text-primary" href="javascript:void(0);" data-id="${type.id}" data-name="${typeName}">
                                                            <i class="fas fa-edit me-1"></i> Edit
                                                        </a>
                                                        <a class="delete-type text-danger" href="javascript:void(0);" data-id="${type.id}">
                                                            <i class="fas fa-trash me-1"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    `;

                                    mmCards.push(MasterMobileUI.card({
                                        title: type.type_name,
                                        sub: 'Created ' + createdAt,
                                        actions: `
                                            <a class="mm-act is-edit edit-type" href="javascript:void(0);" data-id="${type.id}" data-name="${typeName}" title="Edit"><i class="fas fa-pen"></i></a>
                                            <a class="mm-act is-delete delete-type" href="javascript:void(0);" data-id="${type.id}" title="Delete"><i class="fas fa-trash"></i></a>`
                                    }));
                                });
                            }

                            $('#type-table-body').html(rowsHtml);
                            MasterMobileUI.render(mmCards, 'No types found');
                        }
                    },
                    error: function(xhr) {
                        $('#type-table-body').html(`<tr><td colspan="4" class="text-center py-4 text-danger">Failed to load types.</td></tr>`);
                        MasterMobileUI.render([], 'Failed to load types');
                    }
                });
            }

            // Mobile details toggle
            $(document).on('click', '.toggle-details', function(e) {
                e.preventDefault();
                let targetId = $(this).data('target');
                let $target = $('#' + targetId);
                let $icon = $(this).find('i');

                if ($target.is(':visible')) {
                    $target.hide();
                    $icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
                } else {
                    $target.show();
                    $icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
                }
            });

            function updatePaginationUI(pagination) {
                let from = pagination.total === 0 ? 0 : (pagination.current_page - 1) * pagination.per_page + 1;
                let to = Math.min(pagination.current_page * pagination.per_page, pagination.total);

                $('#pagination-from').text(from);
                $('#pagination-to').text(to);
                $('#pagination-total').text(pagination.total);

                let html = `
                    <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page - 1}">Previous</a>
                    </li>
                `;

                let startPage = Math.max(1, pagination.current_page - 2);
                let endPage = Math.min(pagination.last_page, startPage + 4);
                if (endPage - startPage < 4) {
                    startPage = Math.max(1, endPage - 4);
                }

                for (let i = startPage; i <= endPage; i++) {
                    html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                    </li>`;
                }

                html += `
                    <li class="page-item ${pagination.current_page === pagination.last_page || pagination.last_page === 0 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page + 1}">Next</a>
                    </li>
                `;

                $('#pagination-numbers').html(html);
            }

            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchTypes(page);
                }
            });

            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchTypes(1);
            });

            // Add Type form submit
            $('#addTypeForm').submit(function(e) {
                e.preventDefault();
                let $btn = $('#saveTypeBtn');
                $btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...').prop('disabled', true);
                $('.error_type_name').text('');

                let payload = {
                    type_name: $(this).find('input[name="type_name"]').val()
                };
                if (selectedSubAdminId) payload.selectedSubAdminId = selectedSubAdminId;

                $.ajax({
                    url: '/api/types',
                    type: 'POST',
                    data: JSON.stringify(payload),
                    contentType: 'application/json',
                    headers: getHeaders(),
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#ff9f43',
                                confirmButtonText: 'OK'
                            });
                            $('#addTypeModal').modal('hide');
                            $('#addTypeForm')[0].reset();
                            fetchTypes(currentPage);
                        }
                        $btn.html('Save Type').prop('disabled', false);
                    },
                    error: function(xhr) {
                        $btn.html('Save Type').prop('disabled', false);
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            if (xhr.responseJSON.errors.type_name) {
                                $('.error_type_name').text(xhr.responseJSON.errors.type_name[0]);
                            }
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'Something went wrong!',
                                icon: 'error',
                                confirmButtonColor: '#ff9f43'
                            });
                        }
                    }
                });
            });

            // Edit populate modal
            $(document).on('click', '.edit-type', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                let name = $(this).data('name');

                $('#edit_type_id').val(id);
                $('#edit_type_name').val(name);
                $('.error_edit_type_name').text('');
                $('#editTypeModal').modal('show');
            });

            // Update Type form submit
            $('#editTypeForm').submit(function(e) {
                e.preventDefault();
                let $btn = $('#updateTypeBtn');
                $btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Updating...').prop('disabled', true);
                $('.error_edit_type_name').text('');

                let id = $('#edit_type_id').val();
                let payload = {
                    type_name: $('#edit_type_name').val()
                };
                if (selectedSubAdminId) payload.selectedSubAdminId = selectedSubAdminId;

                $.ajax({
                    url: `/api/types/${id}`,
                    type: 'PUT',
                    data: JSON.stringify(payload),
                    contentType: 'application/json',
                    headers: getHeaders(),
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: 'Updated!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#ff9f43',
                                confirmButtonText: 'OK'
                            });
                            $('#editTypeModal').modal('hide');
                            fetchTypes(currentPage);
                        }
                        $btn.html('Update Type').prop('disabled', false);
                    },
                    error: function(xhr) {
                        $btn.html('Update Type').prop('disabled', false);
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            if (xhr.responseJSON.errors.type_name) {
                                $('.error_edit_type_name').text(xhr.responseJSON.errors.type_name[0]);
                            }
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'Failed to update type',
                                icon: 'error',
                                confirmButtonColor: '#ff9f43'
                            });
                        }
                    }
                });
            });

            // Delete Type
            $(document).on('click', '.delete-type', function(e) {
                e.preventDefault();
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this Type? This cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ff9f43',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/types/${id}`,
                            type: 'DELETE',
                            headers: getHeaders(),
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: response.message,
                                        icon: 'success',
                                        confirmButtonColor: '#ff9f43'
                                    });
                                    fetchTypes(currentPage);
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: xhr.responseJSON?.message || 'Failed to delete type.',
                                    icon: 'error',
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
