@extends('layout.app')

@section('title', 'Row Material Purchases')

@section('content')
    <style>
        .mobile-row-material-card {
            display: none;
        }

        .row-material-action-wrap {
            position: relative;
            display: inline-flex;
        }

        .row-material-action-toggle {
            width: 36px;
            height: 36px;
            border: 1px solid #d9e0ea;
            background: #fff;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #1b2850;
            padding: 0;
        }

        .row-material-action-toggle:hover {
            background: #f8f9fa;
            border-color: #ff9f43;
            color: #ff9f43;
        }

        .row-material-action-menu {
            min-width: 150px;
            padding: 6px 0;
        }

        .row-material-action-menu .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
        }

        .row-material-action-menu .dropdown-item i {
            width: 16px;
            text-align: center;
        }

        .row-material-action-menu .dropdown-item.text-danger:hover {
            background: #fff5f5;
        }

        @media screen and (max-width: 1024.98px) {
            .content > .card > .card-body {
                background: #f5f6f8;
            }

            .row-material-purchase-table-responsive {
                display: none !important;
            }

            .mobile-row-material-card {
                display: grid;
                gap: 10px;
                margin-top: 0 !important;
            }

            .rm-mobile-card {
                min-width: 0;
                padding: 12px;
                border: 1px solid #e9ecf1;
                border-radius: 10px;
                background: #fff;
                box-shadow: 0 1px 3px rgba(16, 24, 40, 0.07);
            }

            .rm-mobile-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 10px;
            }

            .rm-mobile-invoice {
                min-width: 0;
                color: #1b2850;
                font-size: 13px;
                font-weight: 700;
                line-height: 1.35;
                overflow-wrap: anywhere;
            }

            .rm-mobile-label {
                display: block;
                margin-bottom: 2px;
                color: #8b93a7;
                font-size: 10px;
                font-weight: 600;
                letter-spacing: 0.04em;
                text-transform: uppercase;
            }

            .rm-mobile-total {
                flex: 0 0 auto;
                color: #1b2850;
                font-size: 14px;
                font-weight: 700;
                white-space: nowrap;
                text-align: right;
            }

            .rm-mobile-meta {
                display: flex;
                align-items: baseline;
                flex-wrap: wrap;
                gap: 4px 12px;
                margin-top: 5px;
                color: #667085;
                font-size: 11px;
                line-height: 1.4;
            }

            .rm-mobile-vendor {
                min-width: 0;
                color: #475467;
                font-size: 12px;
                font-weight: 600;
                overflow-wrap: anywhere;
            }

            .rm-mobile-materials {
                display: flex;
                align-items: baseline;
                gap: 6px;
                margin-top: 8px;
                padding-bottom: 8px;
                border-bottom: 1px dashed #eceef3;
                color: #8b93a7;
                font-size: 10px;
                line-height: 1.4;
            }

            .rm-mobile-materials-label {
                flex: 0 0 auto;
                font-weight: 600;
            }

            .rm-mobile-material-names {
                min-width: 0;
                color: #667085;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                overflow: hidden;
                overflow-wrap: anywhere;
            }

            .rm-mobile-stats {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr)) 36px;
                gap: 8px;
                align-items: center;
                padding-top: 8px;
            }

            .rm-mobile-stat {
                min-width: 0;
            }

            .rm-mobile-stat-label {
                display: block;
                color: #8b93a7;
                font-size: 10px;
                line-height: 1.3;
            }

            .rm-mobile-stat-value {
                display: block;
                max-width: 100%;
                margin-top: 3px;
                color: #344054;
                font-size: 11px;
                font-weight: 600;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .rm-mobile-stat-value .badges {
                display: inline-block;
                max-width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
                vertical-align: middle;
                white-space: nowrap;
                font-size: 10px;
            }

            .rm-mobile-actions {
                display: flex;
                justify-content: flex-end;
                align-items: center;
            }

            .rm-mobile-actions .row-material-action-wrap {
                width: auto;
            }

            .rm-mobile-actions .row-material-action-toggle {
                width: 36px;
                height: 36px;
                border: 0;
                border-radius: 8px;
                background: #fff4e8;
                color: #d97719;
            }

            .rm-mobile-actions .row-material-action-toggle:hover,
            .rm-mobile-actions .row-material-action-toggle:focus {
                border: 0;
                background: #ffecd8;
                color: #b9600b;
                box-shadow: none;
            }

            .rm-mobile-actions .row-material-action-toggle i {
                transform: rotate(90deg);
            }

            .rm-mobile-empty {
                padding: 18px 12px;
                border: 1px dashed #d7dde8;
                border-radius: 10px;
                background: #fff;
                color: #667085;
                font-size: 13px;
                text-align: center;
            }

            .d-flex.justify-content-between.align-items-center.mt-3 {
                flex-direction: column;
                align-items: stretch !important;
                gap: 10px;
            }

            #row-material-purchase-summary {
                text-align: center;
            }

            .d-flex.gap-2 {
                justify-content: center;
            }
        }

        /* Keep wide desktop tables inside the page and expose horizontal scrolling. */
        .row-material-purchase-table-responsive {
            display: block;
            max-width: 100%;
            overflow-x: auto !important;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .mm-page .mm-search .search-input {
            position: relative;
        }

        .mm-page .mm-search .search-input input {
            padding-left: 36px !important;
        }

        .mm-page .mm-search .btn-searchset {
            position: absolute;
            top: 50% !important;
            left: 10px;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            padding: 0;
            transform: translateY(-50%);
        }

        .mm-page .mm-search .btn-searchset img {
            display: block;
            width: 14px;
            height: 14px;
        }

        .table,
        .table tbody,
        .table tr,
        .table td:last-child {
            overflow: visible !important;
        }

        @media screen and (max-width: 1024.98px) {
            .page-header {
                display: none !important;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .page-header .page-btn {
                width: 100%;
            }

            .page-header .page-btn .btn.btn-added {
                width: 100%;
                justify-content: center;
            }

            .d-flex.justify-content-between.align-items-center.mt-3 {
                flex-direction: column;
                align-items: stretch !important;
                gap: 10px;
            }

            #row-material-purchase-summary {
                text-align: center;
            }

            .d-flex.gap-2 {
                justify-content: center;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 1024.98px) {
            .mobile-row-material-card {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                align-items: start;
            }

            .mm-page.content {
                padding: 6px 10px 90px !important;
            }

            .mm-page .mm-head.d-md-none {
                display: flex !important;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-bottom: 8px;
                padding: 12px 10px;
                border-radius: 12px;
                background: #fff;
                box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
            }

            .mm-page .mm-title {
                margin-left: 2px;
                color: #1b2850;
                font-size: 15px;
                font-weight: 600;
                white-space: nowrap;
            }

            .mm-page .mm-count {
                margin: 1px 0 0 2px;
                color: #8a92a6;
                font-size: 11.5px;
            }

            .mm-page .mm-add {
                display: inline-flex !important;
                align-items: center;
                height: 30px;
                margin: 0 !important;
                padding: 0 10px !important;
                border: 1px solid #1b2850 !important;
                border-radius: 8px !important;
                background: #1b2850 !important;
                color: #fff !important;
                font-size: 12px !important;
                font-weight: 600;
                white-space: nowrap;
            }

            .mm-page > .card {
                margin: 0 !important;
                border: 0 !important;
                background: transparent !important;
                box-shadow: none !important;
            }

            .mm-page > .card > .card-body {
                padding: 0 !important;
                background: #f5f6f8;
            }

            .mm-page .mm-search {
                margin: 0 0 8px !important;
                padding: 8px 10px;
                border-radius: 12px;
                background: #fff;
                box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
            }

            .mm-page .mm-search .search-set,
            .mm-page .mm-search .search-input {
                width: 100% !important;
                margin: 0 !important;
            }

            .mm-page .mm-search .search-input {
                position: relative;
                display: flex;
                align-items: center;
            }

            .mm-page .mm-search .search-input input {
                width: 100% !important;
                height: 40px !important;
                padding-left: 36px !important;
                border-radius: 10px !important;
                font-size: 13px;
            }

            .mm-page .mm-search .btn-searchset {
                position: absolute;
                top: 50% !important;
                left: 10px;
                z-index: 2;
                padding: 0;
                transform: translateY(-50%);
            }
        }

        @media screen and (min-width: 768px) and (max-width: 850.98px) {
            .mm-page.content {
                max-width: 768px;
                margin: 0 auto;
            }
        }

        @media screen and (min-width: 851px) and (max-width: 991px) {
            .page-wrapper {
                padding-top: 30px !important;
            }
        }
    </style>

    <div class="content mm-page">
        <div class="mm-head d-md-none">
            <div>
                <div class="mm-title">Row Material Purchases</div>
                <div class="mm-count" id="rmCount"></div>
            </div>
            <a href="{{ route('purchase.row_material.add') }}" class="btn btn-sm mm-add">
                <i class="fas fa-plus me-1"></i> New Purchase
            </a>
        </div>

        <div class="page-header d-flex justify-content-between align-items-center">
            <div class="page-title">
                <h4>Row Material Purchases</h4>
            </div>
            <div>
                <a href="{{ route('purchase.row_material.add') }}" class="btn btn-added">
                    <i class="fa fa-plus me-2"></i>New Row Material Purchase
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="mb-3 mm-search">
                    <div class="search-set d-flex justify-content-md-start justify-content-start w-100">
                        <div class="search-path"></div>
                        <div class="search-input">
                            <a class="btn btn-searchset" aria-hidden="true">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="">
                            </a>
                            <input type="text" id="search-input" class="form-control" placeholder="Search by invoice, vendor, material...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive row-material-purchase-table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Invoice No</th>
                                <th>Vendor</th>
                                <th>Row Materials</th>
                                <th>Grand Total</th>
                                <th>Remaining</th>
                                <th>Purchase Status</th>
                                <th>Payment Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="row-material-purchase-table-body">
                            <tr>
                                <td colspan="10" class="text-center text-muted">Loading row material purchases...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mobile-row-material-card mt-3" id="mobile-row-material-container">
                    <div class="rm-mobile-empty">Loading row material purchases...</div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div id="row-material-purchase-summary" class="text-muted"></div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-secondary" id="prev-page">Prev</button>
                        <button type="button" class="btn btn-sm btn-secondary" id="next-page">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    @include('partials.master-mobile-ui')
    <script>
        $(function() {
            const authToken = localStorage.getItem('authToken');
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            let currentPage = 1;
            let lastPage = 1;

            function escapeHtml(value) {
                return String(value == null ? '' : value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function statusBadge(status) {
                const value = (status || '').toLowerCase();
                let badgeClass = 'bg-secondary';

                if (value === 'completed') badgeClass = 'bg-lightgreen';
                if (value === 'pending') badgeClass = 'bg-lightred';
                if (value === 'partially' || value === 'partial') badgeClass = 'bg-lightyellow';

                return `<span class="badges ${badgeClass}">${escapeHtml(status || '-')}</span>`;
            }

            function formatDisplayDate(dateValue) {
                if (!dateValue) return '-';

                const date = new Date(dateValue);
                if (isNaN(date.getTime())) {
                    return dateValue;
                }

                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();

                let hours = date.getHours(); const minutes = String(date.getMinutes()).padStart(2, '0'); const ampm = hours >= 12 ? 'PM' : 'AM'; hours = hours % 12; hours = hours ? hours : 12; const strTime = hours + ':' + minutes + ' ' + ampm; return `${day}/${month}/${year} ${strTime}`;
            }

            function renderMobileRowMaterialPurchases(items) {
                const container = $('#mobile-row-material-container');
                container.empty();

                if (!items || items.length === 0) {
                    container.html('<div class="rm-mobile-empty">No row material purchases found.</div>');
                    return;
                }

                items.forEach(function(item) {
                    const purchaseId = escapeHtml(item.id);
                    const invoiceNumber = escapeHtml(item.invoice_number || '-');
                    const vendorName = escapeHtml(item.vendor_name || '-');
                    const materialNames = escapeHtml(item.material_names || '-');
                    const displayDate = escapeHtml(formatDisplayDate(item.date));

                    const card = `
                        <article class="rm-mobile-card" data-purchase-id="${purchaseId}">
                            <div class="rm-mobile-top">
                                <div class="rm-mobile-invoice">
                                    <span class="rm-mobile-label">Invoice</span>
                                    ${invoiceNumber}
                                </div>
                                <div class="rm-mobile-total">
                                    <span class="rm-mobile-label">Grand total</span>
                                    ${parseFloat(item.grand_total || 0).toFixed(2)}
                                </div>
                            </div>
                            <div class="rm-mobile-meta">
                                <span class="rm-mobile-vendor">${vendorName}</span>
                                <span>${displayDate}</span>
                            </div>
                            <div class="rm-mobile-materials">
                                <span class="rm-mobile-materials-label">Materials</span>
                                <span class="rm-mobile-material-names">${materialNames}</span>
                            </div>
                            <div class="rm-mobile-stats">
                                <div class="rm-mobile-stat">
                                    <span class="rm-mobile-stat-label">Remaining</span>
                                    <strong class="rm-mobile-stat-value">${parseFloat(item.remaining_amount || 0).toFixed(2)}</strong>
                                </div>
                                <div class="rm-mobile-stat">
                                    <span class="rm-mobile-stat-label">Purchase</span>
                                    <strong class="rm-mobile-stat-value">${statusBadge(item.purchase_status)}</strong>
                                </div>
                                <div class="rm-mobile-stat">
                                    <span class="rm-mobile-stat-label">Payment</span>
                                    <strong class="rm-mobile-stat-value">${statusBadge(item.payment_status)}</strong>
                                </div>
                                <div class="rm-mobile-actions">
                                    <div class="dropdown row-material-action-wrap">
                                        <button type="button" class="row-material-action-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Actions" aria-label="Actions for ${invoiceNumber}">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end row-material-action-menu">
                                            <a class="dropdown-item" href="/view-row-material-purchase/${purchaseId}">
                                                <i class="fas fa-eye"></i>
                                                <span>View</span>
                                            </a>
                                            <a class="dropdown-item" href="/edit-row-material-purchase/${purchaseId}">
                                                <i class="fas fa-edit"></i>
                                                <span>Edit</span>
                                            </a>
                                            <a class="dropdown-item text-danger delete-row-material-purchase" href="javascript:void(0);" data-id="${purchaseId}">
                                                <i class="fas fa-trash"></i>
                                                <span>Delete</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    `;
                    container.append(card);
                });
            }

            function loadRowMaterialPurchases() {
                $.ajax({
                    url: '/api/row-material-purchase-list',
                    type: 'GET',
                    data: {
                        page: currentPage,
                        per_page: 10,
                        search: $('#search-input').val(),
                        selectedSubAdminId: selectedSubAdminId
                    },
                    headers: {
                        Authorization: 'Bearer ' + authToken
                    },
                    success: function(response) {
                        const items = response.data || [];
                        const rows = (response.data || []).map(function(item, index) {
                            return `
                                <tr>
                                    <td>${((currentPage - 1) * 10) + index + 1}</td>
                                    <td>${item.invoice_number || '-'}</td>
                                    <td>${item.vendor_name || '-'}</td>
                                    <td>${item.material_names || '-'}</td>
                                    <td>${parseFloat(item.grand_total || 0).toFixed(2)}</td>
                                    <td>${parseFloat(item.remaining_amount || 0).toFixed(2)}</td>
                                <td>${statusBadge(item.purchase_status)}</td>
                                <td>${statusBadge(item.payment_status)}</td>
                                <td>${formatDisplayDate(item.date)}</td>
                                <td>
                                    <div class="dropdown row-material-action-wrap">
                                        <button type="button" class="row-material-action-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end row-material-action-menu">
                                            <a class="dropdown-item" href="/view-row-material-purchase/${item.id}">
                                                <i class="fas fa-eye"></i>
                                                <span>View</span>
                                            </a>
                                            <a class="dropdown-item" href="/edit-row-material-purchase/${item.id}">
                                                <i class="fas fa-edit"></i>
                                                <span>Edit</span>
                                            </a>
                                            <a class="dropdown-item text-danger delete-row-material-purchase" href="javascript:void(0);" data-id="${item.id}">
                                                <i class="fas fa-trash"></i>
                                                <span>Delete</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                        }).join('');

                        $('#row-material-purchase-table-body').html(rows || '<tr><td colspan="10" class="text-center text-muted">No row material purchases found.</td></tr>');
                        renderMobileRowMaterialPurchases(items);

                        currentPage = response.pagination?.current_page || 1;
                        lastPage = response.pagination?.last_page || 1;
                        $('#rmCount').text(`${response.pagination?.total || items.length} items`);
                        $('#row-material-purchase-summary').text(`Page ${currentPage} of ${lastPage}`);
                        $('#prev-page').prop('disabled', currentPage <= 1);
                        $('#next-page').prop('disabled', currentPage >= lastPage);
                    },
                    error: function() {
                        $('#row-material-purchase-table-body').html('<tr><td colspan="10" class="text-center text-danger">Failed to load row material purchases.</td></tr>');
                        $('#mobile-row-material-container').html('<div class="text-center p-4 text-danger">Failed to load row material purchases.</div>');
                    }
                });
            }

            $(document).on('click', '.delete-row-material-purchase', function() {
                const purchaseId = $(this).data('id');

                Swal.fire({
                    title: 'Delete row material purchase?',
                    text: 'This will reverse the purchased stock if it has not already been consumed.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: `/api/row-material-purchase/${purchaseId}`,
                        type: 'DELETE',
                        data: {
                            selectedSubAdminId: selectedSubAdminId
                        },
                        headers: {
                            Authorization: 'Bearer ' + authToken
                        },
                        success: function(response) {
                            Swal.fire('Deleted', response.message || 'Row material purchase deleted successfully.', 'success');
                            loadRowMaterialPurchases();
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message || 'Failed to delete row material purchase.', 'error');
                        }
                    });
                });
            });

            $('#search-input').on('input', function() {
                currentPage = 1;
                loadRowMaterialPurchases();
            });

            $('#prev-page').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    loadRowMaterialPurchases();
                }
            });

            $('#next-page').on('click', function() {
                if (currentPage < lastPage) {
                    currentPage++;
                    loadRowMaterialPurchases();
                }
            });

            loadRowMaterialPurchases();
        });
    </script>
@endpush
