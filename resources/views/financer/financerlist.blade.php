@extends('layout.app')

@section('title', 'Financer List')

@section('content')
<style>
    #global-loader {
        display: none !important;
    }

    .financer-status {
        display: inline-block;
        min-width: 70px;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 12px;
        color: #fff;
        text-align: center;
    }

    .financer-status.active {
        background: #28c76f;
    }

    .financer-status.inactive {
        background: #ea5455;
    }

    .financer-mobile-head,
    .financer-card-list {
        display: none;
    }

    /* Desktop: show all columns normally */
    @media (min-width: 1200px) {
        table.financer-table thead th.details-column,
        table.financer-table tbody td:nth-child(2) {
            display: none !important;
        }
    }

    /* Mobile: hide non-essential columns, show Details toggle */
    @media (max-width: 1199px) {
        .financer-mobile-head {
            align-items: center;
            justify-content: space-between;
            display: flex;
            margin: 0 0 10px;
            padding: 10px 12px;
            background: #fff;
            border-radius: 10px;
        }
        .financer-mobile-title {
            color: #1b2850;
            font-size: 14px;
            font-weight: 700;
        }
        .financer-mobile-actions {
            display: flex;
            gap: 6px;
        }
        .financer-mobile-actions .btn {
            border-radius: 7px;
            font-size: 11px;
            font-weight: 600;
            padding: 6px 8px;
        }
        .financer-page .card-body {
            padding: 10px !important;
        }
        .financer-page .search-input {
            width: 100%;
        }
        .financer-page .search-input input {
            border-radius: 8px;
        }
        .financer-page .table-container {
            display: none;
        }
        .financer-card-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .financer-card {
            padding: 12px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
        }
        .financer-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            gap: 8px;
        }
        .financer-card-name {
            color: #1b2850;
            font-size: 15px;
            font-weight: 600;
            min-width: 0;
            overflow-wrap: anywhere;
        }
        .financer-card-status {
            display: inline-block;
            flex: 0 0 auto;
            padding: 3px 10px;
            border-radius: 12px;
            background: #e7f8ef;
            color: #12a150;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }
        .financer-card-status.inactive {
            background: #fdecec;
            color: #e5483d;
        }
        .financer-card-bottom {
            display: flex;
            align-items: flex-end;
            gap: 22px;
            margin-top: 8px;
        }
        .financer-stat span {
            display: block;
            color: #8b93a7;
            font-size: 12px;
        }
        .financer-stat strong {
            display: block;
            color: #1b2850;
            font-size: 13px;
            font-weight: 600;
            overflow-wrap: anywhere;
        }
        .financer-card-actions {
            display: flex;
            gap: 2px;
            margin-left: auto;
        }
        .financer-page .pagination-controls {
            gap: 10px;
        }
        .financer-page .pagination-controls > div {
            flex-wrap: wrap;
            justify-content: center;
        }
        .financer-empty {
            padding: 22px 12px;
            color: #667085;
            text-align: center;
            background: #fff;
            border-radius: 10px;
        }
    }

    /* Action buttons fix */
    .action-buttons {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
    }

    .vendor-action-dropdown {
        display: inline-flex;
        justify-content: center;
        width: 100%;
    }

    .vendor-action-dropdown .action-menu-toggle {
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

    .vendor-action-dropdown .action-menu-toggle:hover,
    .vendor-action-dropdown .action-menu-toggle:focus {
        background: #f8fafc;
        border-color: #b8c2d2;
        color: #1b2850;
    }

    .vendor-action-menu {
        border: 1px solid #e8ebed;
        border-radius: 6px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
        min-width: 168px;
        padding: 6px;
        z-index: 999999999999;
    }

    .vendor-action-menu-floating {
        position: fixed !important;
        z-index: 999999999999 !important;
    }

    .vendor-action-menu .dropdown-item {
        align-items: center;
        border-radius: 4px;
        color: #344054;
        display: flex;
        font-size: 12px;
        gap: 8px;
        padding: 7px 8px;
        width: 100%;
    }

    .vendor-action-menu .dropdown-item i {
        color: #667085;
        font-size: 13px;
        text-align: center;
        width: 15px;
    }

    .vendor-action-menu .dropdown-item.text-danger,
    .vendor-action-menu .dropdown-item.text-danger i {
        color: #ea5455 !important;
    }

    .vendor-action-menu .dropdown-item:hover {
        background: #f4f6f8;
        color: #1b2850;
    }
</style>

<div class="content financer-page">
    <div class="page-header">
        <div class="page-title">
            <h4>All Financers</h4>
        </div>
        <div class="page-btn d-flex gap-2">
            @if (app('hasPermission')(10, 'add'))
                <a href="{{ route('financer.add') }}" class="btn btn-added btn-sm">
                    <i class="fa-solid fa-plus me-1"></i> Add
                </a>
                <a href="{{ route('financer.import') }}" class="btn btn-added btn-sm">
                    <i class="fa-solid fa-file-import me-1"></i> Import
                </a>
            @endif
        </div>
    </div>

    <div class="financer-mobile-head d-xl-none">
        <div class="financer-mobile-title">All Financers</div>
        <div class="financer-mobile-actions">
            @if (app('hasPermission')(10, 'add'))
                <a href="{{ route('financer.add') }}" class="btn btn-added btn-sm"><i class="fa-solid fa-plus me-1"></i> Add</a>
                <a href="{{ route('financer.import') }}" class="btn btn-added btn-sm"><i class="fa-solid fa-file-import me-1"></i> Import</a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-2">
                <div class="search-set d-flex justify-content-md-start justify-content-start w-100">
                    <div class="search-path"></div>
                    <div class="search-input">
                        {{-- <a class="btn btn-searchset">
                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img">
                        </a> --}}
                        <input type="text" id="search-input" class="form-control" placeholder="Search...">
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="table financer-table">
                    <thead>
                                <tr>
                                    <th>Financer Name</th>
                                    <th class="details-column">Details</th>
                                    <th>Phone</th>
                                    <th>City</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                    </thead>
                            <tbody id="financer-table-body">
                                <tr>
                                    <td colspan="6" class="text-center">Loading financers...</td>
                                </tr>
                            </tbody>
                </table>
            </div>

            <div class="financer-card-list d-xl-none" id="financerCardList"></div>

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
    let currentPage = 1;
    let perPage = 10;
    const baseFinancerViewUrl = "{{ url('/financer') }}";
    const baseFinancerEditUrl = "{{ url('/edit-financer') }}";

    var authToken = localStorage.getItem('authToken');

    fetchFinancers();

    $('#search-input').on('input', function() {
        currentPage = 1;
        fetchFinancers();
    });

    $('#per-page-select').on('change', function() {
        perPage = $(this).val();
        currentPage = 1;
        fetchFinancers();
    });

    $(document).on('click', '#pagination-numbers .page-link', function() {
        const page = $(this).data('page');
        if (page) {
            currentPage = page;
            fetchFinancers();
        }
    });

    function fetchFinancers() {
        const selectedSubAdminId = localStorage.getItem('selectedSubAdminId') || '';
        const search = $('#search-input').val() || '';
        let url = `{{ route('financer.data') }}?page=${currentPage}&per_page=${perPage}&search=${encodeURIComponent(search)}`;

        if (selectedSubAdminId) {
            url += `&selectedSubAdminId=${selectedSubAdminId}`;
        }

        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: function(response) {
                const financers = response.data || [];
                renderRows(financers);
                renderCards(financers);
                renderPagination(response.pagination || {});
            },
            error: function() {
                $('#financer-table-body').html('<tr><td colspan="12" class="text-center text-danger">Error loading financer data</td></tr>');
            }
        });
    }

    function renderRows(financers) {
        if (!financers.length) {
            $('#financer-table-body').html('<tr><td colspan="6" class="text-center">No financers found</td></tr>');
            return;
        }

        const rows = financers.map(function(financer) {
            const statusClass = financer.status ? 'active' : 'inactive';
            const statusText = financer.status ? 'Active' : 'Inactive';
            const name = escapeHtml(financer.name || 'N/A');
            const phone = escapeHtml(financer.phone || 'N/A');
            const city = escapeHtml(financer.city || 'N/A');
            
            let menuItems = '';
            @if (app('hasPermission')(10, 'view'))
                menuItems += `<a class="dropdown-item" href="${baseFinancerViewUrl}/${financer.id || ''}/view">
                    <i class="fas fa-eye"></i><span>View</span>
                </a>`;
            @endif
            @if (app('hasPermission')(10, 'edit'))
                menuItems += `<a class="dropdown-item" href="${baseFinancerEditUrl}/${financer.id || ''}">
                    <i class="fas fa-edit"></i><span>Edit</span>
                </a>`;
            @endif
            @if (app('hasPermission')(10, 'delete'))
                menuItems += `<a class="dropdown-item text-danger delete-financer" data-id="${financer.id || ''}" href="javascript:void(0);">
                    <i class="fas fa-trash"></i><span>Delete</span>
                </a>`;
            @endif
            if (!menuItems) {
                menuItems = '<span class="dropdown-item text-muted">No actions</span>';
            }

            const actions = `
                <div class="dropdown vendor-action-dropdown">
                    <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end vendor-action-menu">
                        ${menuItems}
                    </div>
                </div>
            `;


            return `
                <tr>
                    <td>
                        <div style="display: flex;">
                            <span style="color: #1b2850; font-weight: 500;">${name}</span>
                        </div>
                        <div class="collapse mt-2 d-xl-none" id="details-${financer.id}">
                            <div>
                                <p class="mb-1"><strong>Phone:</strong> ${phone}</p>
                                <p class="mb-1"><strong>City:</strong> ${city}</p>
                                <p class="mb-1"><strong>Status:</strong> <span class="financer-status ${statusClass}">${statusText}</span></p>
                                <div class="mt-2 action-buttons">
                                    ${actions}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="#details-${financer.id}" class="toggle-details" data-bs-toggle="collapse">
                            <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                        </a>
                    </td>
                    <td>${phone}</td>
                    <td>${city}</td>
                    <td><span class="financer-status ${statusClass}">${statusText}</span></td>
                    <td>
                        <div class="action-buttons">
                            ${actions}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        $('#financer-table-body').html(rows);
    }

    function renderCards(financers) {
        if (!financers.length) {
            $('#financerCardList').html('<div class="financer-empty">No financers found</div>');
            return;
        }

        const cards = financers.map(function(financer) {
            const statusClass = financer.status ? 'active' : 'inactive';
            const statusText = financer.status ? 'Active' : 'Inactive';
            const name = escapeHtml(financer.name || 'N/A');
            const phone = escapeHtml(financer.phone || 'N/A');
            const city = escapeHtml(financer.city || 'N/A');

            let menuItems = '';
            @if (app('hasPermission')(10, 'view'))
                menuItems += `<a class="dropdown-item" href="${baseFinancerViewUrl}/${financer.id || ''}/view"><i class="fas fa-eye"></i><span>View</span></a>`;
            @endif
            @if (app('hasPermission')(10, 'edit'))
                menuItems += `<a class="dropdown-item" href="${baseFinancerEditUrl}/${financer.id || ''}"><i class="fas fa-edit"></i><span>Edit</span></a>`;
            @endif
            @if (app('hasPermission')(10, 'delete'))
                menuItems += `<a class="dropdown-item text-danger delete-financer" data-id="${financer.id || ''}" href="javascript:void(0);"><i class="fas fa-trash"></i><span>Delete</span></a>`;
            @endif
            if (!menuItems) menuItems = '<span class="dropdown-item text-muted">No actions</span>';

            const actions = `<div class="dropdown vendor-action-dropdown">
                <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions"><i class="fas fa-ellipsis-h"></i></button>
                <div class="dropdown-menu dropdown-menu-end vendor-action-menu">${menuItems}</div>
            </div>`;

            return `<div class="financer-card">
                <div class="financer-card-top">
                    <span class="financer-card-name">${name}</span>
                    <span class="financer-card-status ${statusClass}">${statusText.toUpperCase()}</span>
                </div>
                <div class="financer-card-bottom">
                    <div class="financer-stat"><span>Phone</span><strong>${phone}</strong></div>
                    <div class="financer-stat"><span>City</span><strong>${city}</strong></div>
                    <div class="financer-card-actions">
                        ${actions}
                    </div>
                </div>
            </div>`;
        }).join('');

        $('#financerCardList').html(cards);
    }

    function positionVendorActionMenu(dropdownEl) {
        const $dropdown = $(dropdownEl);
        const $menu = $dropdown.data('floating-menu') || $dropdown.find('.vendor-action-menu');
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

    $(document).on('show.bs.dropdown', '.vendor-action-dropdown', function() {
        const $dropdown = $(this);
        const $menu = $dropdown.find('.vendor-action-menu');

        if (!$menu.length) {
            return;
        }

        $dropdown.data('floating-menu', $menu);
        $dropdown.data('menu-parent', $menu.parent());
        $dropdown.data('menu-next', $menu.next());
        $menu.addClass('vendor-action-menu-floating').appendTo('body');
    });

    $(document).on('shown.bs.dropdown', '.vendor-action-dropdown', function() {
        positionVendorActionMenu(this);
    });

    $(document).on('hidden.bs.dropdown', '.vendor-action-dropdown', function() {
        const $dropdown = $(this);
        const $menu = $dropdown.data('floating-menu');
        const $parent = $dropdown.data('menu-parent');
        const $next = $dropdown.data('menu-next');

        if ($menu && $menu.length && $parent && $parent.length) {
            $menu.removeClass('vendor-action-menu-floating').removeAttr('style');
            if ($next && $next.length && $.contains($parent[0], $next[0])) {
                $menu.insertBefore($next);
            } else {
                $parent.append($menu);
            }
        }

        $dropdown.removeData('floating-menu menu-parent menu-next');
    });

    $(window).on('scroll resize', function() {
        $('.vendor-action-dropdown.show').each(function() {
            positionVendorActionMenu(this);
        });
    });

    // permission-controlled buttons rendered inline via Blade

    $(document).on('click', '.delete-financer', function(e) {
        e.preventDefault();
        const financerId = $(this).data('id');
        if (!financerId) return;

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
                    url: `/api/deleteFinancer/${financerId}`,
                    type: 'POST',
                    headers: Object.assign({ 'X-Requested-With': 'XMLHttpRequest' }, authToken ? { 'Authorization': 'Bearer ' + authToken } : {}),
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: "Deleted!",
                                text: response.message || 'Financer deleted',
                                icon: "success",
                                confirmButtonColor: "#ff9f43",
                                confirmButtonText: "OK"
                            }).then(() => {
                                fetchFinancers();
                            });
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: response.message || 'Could not delete financer',
                                icon: "error",
                                confirmButtonColor: "#ff9f43",
                                confirmButtonText: "OK"
                            });
                        }
                    },
                    error: function(xhr) {
                        let message = 'Something went wrong!';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            message = xhr.responseJSON.error;
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

    function renderPagination(pagination) {
        $('#pagination-from').text(pagination.from || 0);
        $('#pagination-to').text(pagination.to || 0);
        $('#pagination-total').text(pagination.total || 0);

        let html = '';
        const current = pagination.current_page || 1;
        const last = pagination.last_page || 1;

        html += `<li class="page-item ${current === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0);" data-page="${current - 1}">Previous</a>
        </li>`;

        for (let page = 1; page <= last; page++) {
            html += `<li class="page-item ${page === current ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0);" data-page="${page}">${page}</a>
            </li>`;
        }

        html += `<li class="page-item ${current === last ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0);" data-page="${current + 1}">Next</a>
        </li>`;

        $('#pagination-numbers').html(html);
    }

    function escapeHtml(value) {
        return $('<div>').text(value).html();
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
});
</script>
@endpush
