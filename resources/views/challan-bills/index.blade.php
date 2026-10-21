@extends('layout.app')

@section('title', 'Challan Bills')

@section('content')
<style>
    /* Hide default DataTables search box */
    .dataTables_filter {
        display: none !important;
    }

    /* Search bar styling (matching Category page) */
    .mm-search {
        margin-bottom: 15px;
    }
    .mm-search .search-set {
        width: 100% !important;
        margin: 0 !important;
    }
    .mm-search .search-input {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        max-width: 320px;
    }
    .mm-search .search-input input {
        width: 100% !important;
        height: 40px !important;
        padding-left: 36px !important;
        border-radius: 8px !important;
        border: 1px solid #e4e7ec !important;
        font-size: 13px;
    }
    .mm-search .btn-searchset {
        position: absolute;
        left: 12px;
        top: 50% !important;
        z-index: 2;
        padding: 0;
        transform: translateY(-50%);
        pointer-events: none;
    }

    /* Card hidden on desktop */
    .cb-m-card {
        display: none;
    }

    /* Mobile view: Category/Product list style */
    @media (max-width: 1024.98px), (width: 1032px) and (height: 1376px), (width: 1280px) and (height: 800px), (width: 1368px) and (height: 912px) {
        .card {
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
            margin: 0 !important;
        }

        .card > .card-body {
            padding: 0 !important;
        }

        .mm-search {
            margin: 0 0 12px !important;
            padding: 8px 10px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
        }

        .mm-search .search-input {
            max-width: 100% !important;
            width: 100% !important;
        }

        .mm-search .search-input input {
            border-radius: 10px !important;
        }

        .table-responsive {
            overflow: visible !important;
            border: 0 !important;
        }

        table.datanew {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            border: 0 !important;
        }

        table.datanew thead {
            display: none !important;
        }

        table.datanew tbody {
            display: flex !important;
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }

        table.datanew tbody tr {
            display: block !important;
            width: 100%;
            padding: 10px 12px !important;
            border: 1px solid #eceef3 !important;
            border-radius: 12px !important;
            background: #fff !important;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .05) !important;
            margin-bottom: 8px;
        }

        table.datanew tbody tr > td:not(.challan-cell):not(.dataTables_empty) {
            display: none !important;
        }

        table.datanew tbody tr > td.challan-cell {
            display: block !important;
            width: 100% !important;
            padding: 0 !important;
            border: 0 !important;
            background: transparent !important;
        }

        table.datanew tbody tr > td.dataTables_empty {
            display: block !important;
            width: 100% !important;
            text-align: center;
            color: #8a92a6;
            padding: 20px !important;
        }

        .challan-desktop-title {
            display: none !important;
        }

        /* Mobile Card Grid */
        .cb-m-card {
            display: grid !important;
            grid-template-columns: 44px minmax(0, 1fr);
            column-gap: 10px;
            row-gap: 8px;
        }

        .cb-m-img {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            overflow: hidden;
            background: #fff4e8;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            flex-shrink: 0;
        }

        .cb-m-img i {
            font-size: 20px;
            color: #ff9f43;
        }

        .cb-m-body {
            min-width: 0;
            display: flex;
            align-items: center;
        }

        .cb-m-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            width: 100%;
        }

        .cb-m-name {
            font-size: 14px;
            font-weight: 700;
            color: #1b2850 !important;
            text-transform: uppercase;
            line-height: 1.3;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-decoration: none;
        }

        .cb-m-date {
            font-size: 13px;
            font-weight: 700;
            color: #1b2850;
            white-space: nowrap;
        }

        .cb-m-badges {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px 8px;
            margin-top: 5px;
            min-width: 0;
        }

        .cb-m-badge {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 9px;
            border-radius: 12px;
            white-space: nowrap;
        }

        .cb-m-badge.is-ok {
            background: #e7f8ef;
            color: #12a150;
        }

        .cb-m-code {
            font-size: 11px;
            color: #8b93a7;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100%;
        }

        .cb-m-foot {
            grid-column: 1 / -1;
            display: flex;
            align-items: flex-end;
            gap: 12px;
            padding-top: 8px;
            border-top: 1px dashed #eceef3;
        }

        .cb-m-stat {
            min-width: 0;
            flex: 1 1 auto;
        }

        .cb-m-stat span {
            display: block;
            font-size: 11px;
            color: #8b93a7;
            margin-bottom: 1px;
        }

        .cb-m-stat strong {
            display: block;
            font-size: 13px;
            color: #1b2850;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cb-m-acts {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
        }

        .cb-quick-action-btn {
            width: 32px !important;
            min-width: 32px !important;
            height: 32px !important;
            padding: 0 !important;
            border: 0 !important;
            border-radius: 8px !important;
            background: transparent !important;
            color: #8b93a7 !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
        }

        .cb-quick-action-btn:hover,
        .cb-quick-action-btn:active {
            background: #f1f2f6 !important;
            color: #ff9f43 !important;
        }

        .cb-quick-action-btn i {
            font-size: 16px;
        }

        .cb-action-dropdown .action-menu-toggle {
            width: 32px;
            height: 32px;
            font-size: 17px;
            box-shadow: none !important;
            padding: 0 !important;
            border: 0 !important;
            background: transparent !important;
            color: #8b93a7 !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
        }

        .cb-action-dropdown .action-menu-toggle:hover,
        .cb-action-dropdown .action-menu-toggle:active {
            background: #f1f2f6 !important;
            color: #1b2850 !important;
        }

        .cb-action-dropdown .action-menu-toggle i {
            transform: rotate(90deg);
        }
    }

    /* 3-dots dropdown menu styling */
    .cb-action-menu {
        border: 1px solid #e8ebed;
        border-radius: 8px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
        min-width: 150px;
        padding: 6px;
        z-index: 9999;
    }

    .cb-action-menu-floating {
        position: fixed !important;
        z-index: 999999 !important;
    }

    .cb-action-menu .dropdown-item {
        align-items: center;
        border-radius: 4px;
        color: #344054;
        display: flex;
        font-size: 13px;
        gap: 8px;
        padding: 7px 10px;
        width: 100%;
        cursor: pointer;
    }

    .cb-action-menu .dropdown-item i {
        color: #667085;
        font-size: 14px;
        text-align: center;
        width: 16px;
    }

    .cb-action-menu .dropdown-item.text-danger,
    .cb-action-menu .dropdown-item.text-danger i {
        color: #ea5455 !important;
    }

    .cb-action-menu .dropdown-item:hover {
        background: #f4f6f8;
        color: #1b2850;
    }

    /* Dark theme support */
    html[data-theme="dark"] .cb-m-card,
    body.dark .cb-m-card {
        background: #1f2937 !important;
        border-color: #374151 !important;
    }
    html[data-theme="dark"] .cb-m-name,
    body.dark .cb-m-name,
    html[data-theme="dark"] .cb-m-date,
    body.dark .cb-m-date,
    html[data-theme="dark"] .cb-m-stat strong,
    body.dark .cb-m-stat strong {
        color: #f9fafb !important;
    }
    html[data-theme="dark"] .cb-action-menu,
    body.dark .cb-action-menu {
        background: #1f2937 !important;
        border-color: #374151 !important;
    }
    html[data-theme="dark"] .cb-action-menu .dropdown-item,
    body.dark .cb-action-menu .dropdown-item {
        color: #e5e7eb !important;
    }
    html[data-theme="dark"] .cb-action-menu .dropdown-item:hover,
    body.dark .cb-action-menu .dropdown-item:hover {
        background: #374151 !important;
    }
    html[data-theme="dark"] .mm-search,
    body.dark .mm-search {
        background: #1f2937 !important;
        border-color: #374151 !important;
    }
    html[data-theme="dark"] .mm-search .search-input input,
    body.dark .mm-search .search-input input {
        background: #111827 !important;
        border-color: #374151 !important;
        color: #fff !important;
    }
    @media screen and (min-width: 768px) and (max-width: 1024.98px),
        screen and (width: 1032px) and (height: 1376px),
        screen and (width: 1280px) and (height: 800px),
        screen and (width: 1368px) and (height: 912px) {

        table.datanew tbody {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            align-items: start;
        }

        table.datanew tbody tr {
            min-width: 0;
            margin-bottom: 0 !important;
        }

        /* "No data" row puri width ma */
        table.datanew tbody tr:has(> td.dataTables_empty) {
            grid-column: 1 / -1;
        }
    }
</style>

<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Challan Bills</h4>
            <h6>Manage your Challan Bills</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('challan-bills.create') }}" class="btn btn-added"><img src="{{ image_path('admin/assets/img/icons/plus.svg') }}" alt="img">Add Challan Bill</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-2 mm-search">
                <div class="search-set d-flex justify-content-md-start justify-content-start w-100">
                    <div class="search-path"></div>
                    <div class="search-input">
                        <a class="btn btn-searchset">
                            <i class="fas fa-search" style="color: #98a2b3; font-size: 14px;"></i>
                        </a>
                        <input type="text" id="challan-search" class="form-control" placeholder="SEARCH...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table datanew">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th class="challan-header">Challan Number</th>
                            <th>Customer Name</th>
                            <th>Vendor Name</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($challanBills as $index => $bill)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="challan-cell">
                                    <span class="challan-desktop-title fw-bold" style="color: #1b2850;">{{ $bill->challan_number }}</span>

                                    <!-- Mobile View Card -->
                                    <div class="cb-m-card">
                                        <a href="{{ route('challan-bills.show', $bill->id) }}" class="cb-m-img">
                                            <i class="fa-solid fa-receipt"></i>
                                        </a>
                                        <div class="cb-m-body">
                                            <div class="cb-m-top">
                                                <a href="{{ route('challan-bills.show', $bill->id) }}" class="cb-m-name">{{ $bill->challan_number }}</a>
                                                <span class="cb-m-date">{{ $bill->challan_date ? $bill->challan_date->format('d-m-Y') : 'N/A' }}</span>
                                            </div>
                                        </div>
                                        <div class="cb-m-foot">
                                            <div class="cb-m-stat">
                                                <span>Customer</span>
                                                <strong>{{ $bill->customer->name ?? 'N/A' }}</strong>
                                            </div>
                                            <div class="cb-m-stat">
                                                <span>Vendor</span>
                                                <strong>{{ $bill->vendor->name ?? 'N/A' }}</strong>
                                            </div>
                                            <div class="cb-m-acts">
                                                <a href="{{ route('challan-bills.pdf', $bill->id) }}" class="btn cb-quick-action-btn" title="Download / Print PDF">
                                                    <i class="fa-solid fa-print"></i>
                                                </a>
                                                <div class="dropdown cb-action-dropdown">
                                                    <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" data-toggle="dropdown" aria-expanded="false" title="Actions">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end cb-action-menu">
                                                        <a class="dropdown-item" href="{{ route('challan-bills.show', $bill->id) }}">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('challan-bills.pdf', $bill->id) }}">
                                                            <i class="fas fa-file-pdf text-danger"></i> PDF
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('challan-bills.edit', $bill->id) }}">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <a class="dropdown-item text-danger delete-btn-item" href="javascript:void(0);" data-form-id="delete-form-{{ $bill->id }}">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $bill->customer->name ?? 'N/A' }}</td>
                                <td>{{ $bill->vendor->name ?? 'N/A' }}</td>
                                <td>{{ $bill->challan_date ? $bill->challan_date->format('d-m-Y') : '' }}</td>
                                <td class="text-end">
                                    <a class="me-3" href="{{ route('challan-bills.show', $bill->id) }}">
                                        <img src="{{ image_path('admin/assets/img/icons/eye.svg') }}" alt="img">
                                    </a>
                                    <a class="me-3" href="{{ route('challan-bills.pdf', $bill->id) }}">
                                        <i class="fas fa-file-pdf text-danger" style="font-size: 18px;"></i>
                                    </a>
                                    <a class="me-3" href="{{ route('challan-bills.edit', $bill->id) }}">
                                        <img src="{{ image_path('admin/assets/img/icons/edit.svg') }}" alt="img">
                                    </a>
                                    <form action="{{ route('challan-bills.destroy', $bill->id) }}" method="POST" class="d-inline delete-form" id="delete-form-{{ $bill->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="border-0 bg-transparent delete-btn">
                                            <img src="{{ image_path('admin/assets/img/icons/delete.svg') }}" alt="img">
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        // Connect Category-style search box with DataTable
        function filterChallanTable(val) {
            if ($.fn.DataTable.isDataTable('.datanew')) {
                $('.datanew').DataTable().search(val).draw();
            }
        }

        $(document).on('keyup input change', '#challan-search', function() {
            filterChallanTable($(this).val());
        });

        // Floating dropdown position calculation for mobile cards
        function positionChallanActionMenu(dropdown) {
            const toggle = dropdown.querySelector('.action-menu-toggle');
            const menu = dropdown.querySelector('.cb-action-menu');
            if (!toggle || !menu) return;

            const rect = toggle.getBoundingClientRect();
            const menuWidth = menu.offsetWidth || 150;
            const menuHeight = menu.offsetHeight || 160;
            const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
            const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

            let left = rect.right - menuWidth;
            let top = rect.bottom + 6;

            if (left < 8) left = rect.left;
            if (left + menuWidth > viewportWidth - 8) left = viewportWidth - menuWidth - 8;
            if (top + menuHeight > viewportHeight - 8) top = Math.max(8, rect.top - menuHeight - 6);

            menu.classList.add('cb-action-menu-floating');
            menu.style.setProperty('left', `${Math.max(8, left)}px`, 'important');
            menu.style.setProperty('top', `${Math.max(8, top)}px`, 'important');
            menu.style.setProperty('right', 'auto', 'important');
            menu.style.setProperty('bottom', 'auto', 'important');
            menu.style.setProperty('transform', 'none', 'important');
        }

        $(document).on('shown.bs.dropdown', '.cb-action-dropdown', function() {
            positionChallanActionMenu(this);
            requestAnimationFrame(() => positionChallanActionMenu(this));
        });

        $(document).on('click', '.cb-action-dropdown .action-menu-toggle', function() {
            const dropdown = $(this).closest('.cb-action-dropdown')[0];
            if (dropdown) {
                setTimeout(() => positionChallanActionMenu(dropdown), 10);
            }
        });

        $(document).on('hidden.bs.dropdown', '.cb-action-dropdown', function() {
            const menu = this.querySelector('.cb-action-menu');
            if (!menu) return;
            menu.classList.remove('cb-action-menu-floating');
            menu.removeAttribute('style');
        });

        $(window).on('scroll resize', function() {
            $('.cb-action-dropdown.show, .cb-action-dropdown .show').each(function() {
                const dd = $(this).closest('.cb-action-dropdown')[0];
                if (dd) positionChallanActionMenu(dd);
            });
        });

        // Trigger delete from mobile dropdown item
        $(document).on('click', '.delete-btn-item', function(e) {
            e.preventDefault();
            var formId = $(this).data('form-id');
            $('#' + formId + ' .delete-btn').trigger('click');
        });

        // Delete SweetAlert confirmation
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var form = $(this).closest('.delete-form');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff9f43',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            if(response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
