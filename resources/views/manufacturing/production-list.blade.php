@extends('layout.app')

@section('title', 'Production')

@section('content')
    <style>
        .production-page .page-header {
            gap: 12px;
        }

        .production-page .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .production-page .badges {
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }

        /* Desktop View */
        .production-page .production-desktop-view {
            display: block;
        }

        .production-page .production-mobile-view {
            display: none;
        }

        /* Mobile / Tablet View (< 1200px) */
        @media screen and (max-width: 1199.98px) {
            .production-page .page-header {
                flex-direction: column;
                align-items: stretch;
                margin-bottom: 14px;
                text-align: center;
            }

            .production-page .page-title h4 {
                font-size: 18px;
                font-weight: 700;
                color: #1b2850;
                margin-bottom: 4px;
            }

            .production-page .page-title h6 {
                font-size: 13px;
                color: #6b7280;
                line-height: 1.4;
            }

            .production-page .page-btn {
                width: 100%;
                margin-top: 8px;
            }

            .production-page .page-btn .btn.btn-added {
                width: 100%;
                justify-content: center;
                background: #ff9f43;
                border-color: #ff9f43;
                color: #fff;
                font-weight: 600;
                border-radius: 8px;
                padding: 10px 16px;
            }

            .production-page .main-card {
                background: transparent !important;
                border: 0 !important;
                box-shadow: none !important;
                margin-bottom: 0 !important;
            }

            .production-page .main-card > .card-body {
                padding: 0 !important;
            }

            .production-page .production-desktop-view {
                display: none !important;
            }

            .production-page .production-mobile-view {
                display: block !important;
            }
        }

        /* Mobile Search Bar (matches material-inventory-list) */
        .prod-filter-row {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
            border: 1px solid #eef0f3;
            padding: 8px 10px;
            margin-bottom: 12px;
        }

        .prod-search-col {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .prod-search-box {
            position: relative;
            flex: 1 1 auto;
            display: flex;
            align-items: center;
        }

        .prod-search-box .search-icon {
            position: absolute;
            left: 12px;
            color: #8b93a7;
            font-size: 13px;
            pointer-events: none;
        }

        .prod-search-box input {
            width: 100%;
            height: 40px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fdfdfd;
            padding: 0 34px 0 34px;
            font-size: 13px;
            font-weight: 500;
            color: #1b2850;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .prod-search-box input:focus {
            border-color: #ff9f43;
            box-shadow: 0 0 0 3px rgba(255, 159, 67, 0.15);
            background: #fff;
        }

        .prod-search-box input::placeholder {
            color: #8b93a7;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .prod-search-box .search-clear {
            position: absolute;
            right: 10px;
            background: none;
            border: none;
            color: #8b93a7;
            cursor: pointer;
            font-size: 14px;
            padding: 4px;
            display: none;
        }

        /* Mobile Cards List */
        .prod-card-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .prod-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
            border: 1px solid #eef0f3;
            padding: 12px 14px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .prod-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
        }

        .prod-card-name {
            font-size: 15px;
            font-weight: 700;
            color: #1b2850;
            text-transform: uppercase;
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .prod-card-code {
            font-size: 14px;
            font-weight: 700;
            color: #1b2850;
            white-space: nowrap;
        }

        .prod-card-badges {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 6px 0 10px;
            min-width: 0;
            flex-wrap: wrap;
        }

        .prod-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 12px;
            white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .prod-badge.is-ok {
            background: #e7f8ef;
            color: #12a150;
        }

        .prod-badge.is-purple {
            background: #f3e8ff;
            color: #7e22ce;
        }

        .prod-badge.is-draft {
            background: #fef3c7;
            color: #b45309;
        }

        .prod-card-sku {
            font-size: 11px;
            color: #8b93a7;
            font-weight: 500;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .prod-card-bottom {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
        }

        .prod-stats {
            display: flex;
            align-items: flex-end;
            gap: 16px;
            flex-wrap: wrap;
        }

        .prod-stat span {
            display: block;
            font-size: 11px;
            color: #8b93a7;
            margin-bottom: 2px;
        }

        .prod-stat strong {
            font-size: 14px;
            color: #1b2850;
            font-weight: 700;
        }

        .prod-acts {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .prod-act {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #8b93a7;
            border-radius: 8px;
            background: #f8f9fa;
            border: 1px solid #eef0f3;
            transition: all 0.15s ease;
            text-decoration: none !important;
            font-size: 13px;
        }

        .prod-act:hover,
        .prod-act:active {
            color: #ff9f43;
            background: #fff4e8;
            border-color: #ffd2a6;
        }

        .prod-act.is-delete:hover,
        .prod-act.is-delete:active {
            color: #e5483d;
            background: #fdecec;
            border-color: #fca5a5;
        }

        .prod-empty {
            text-align: center;
            padding: 30px 15px;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #eef0f3;
            color: #8b93a7;
            font-size: 13px;
        }

        .prod-footer-counter {
            font-size: 12px;
            color: #8b93a7;
            text-align: center;
            padding: 10px 0 20px;
        }
    </style>

    <div class="content">
        <div class="production-page">
            <div class="page-header">
                <div class="page-title">
                    <h4>Production</h4>
                    <h6>Convert raw materials into finished goods</h6>
                </div>
                <div class="page-btn">
                    @if (app('hasPermission')(49, 'add'))
                    <a href="{{ route('inventory.production.add') }}" class="btn btn-added">
                        <i class="fa fa-plus me-2"></i>New Production
                    </a>
                    @endif
                </div>
            </div>

            <div class="card main-card">
                <div class="card-body">
                    <!-- Desktop Table View -->
                    <div class="production-desktop-view">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Production No</th>
                                        <th>Product</th>
                                        <th>BOM</th>
                                        <th>Production Qty</th>
                                        <th>Output Qty</th>
                                        <th>Total Cost</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="production-table-body">
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Loading productions...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile / Tablet Card View -->
                    <div class="production-mobile-view">
                        <div class="prod-filter-row">
                            <div class="prod-search-col">
                                <div class="prod-search-box">
                                    <i class="fa fa-search search-icon"></i>
                                    <input type="text" id="prodSearchInput" placeholder="SEARCH..." autocomplete="off">
                                    <button type="button" id="prodSearchClear" class="search-clear"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="prod-card-list" id="production-mobile-list">
                            <div class="prod-empty">Loading productions...</div>
                        </div>

                        <div class="prod-footer-counter" id="prodFooterCounter"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            const authToken = localStorage.getItem('authToken');
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            const eyeIcon = "{{ env('ImagePath') . '/admin/assets/img/icons/eye.svg' }}";
            const editIcon = "{{ env('ImagePath') . '/admin/assets/img/icons/edit.svg' }}";
            const deleteIcon = "{{ env('ImagePath') . '/admin/assets/img/icons/delete.svg' }}";
            let rawProductionsData = [];

            function esc(value) {
                return String(value ?? '').replace(/[&<>"']/g, function(c) {
                    return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c];
                });
            }

            function actionButtons(item, mobile = false) {
                if (mobile) {
                    let viewAction = '';
                    @if (app('hasPermission')(49, 'view'))
                    viewAction = `<a href="/inventory/productions/${item.id}" class="prod-act" title="View"><i class="fa fa-eye"></i></a>`;
                    @endif

                    let editAction = '';
                    @if (app('hasPermission')(49, 'edit'))
                    if (item.status === 'draft' || item.status === 'in_production') {
                        editAction = `<a href="/inventory/productions/${item.id}/edit" class="prod-act" title="Edit"><i class="fa fa-pen"></i></a>`;
                    }
                    @endif

                    let deleteAction = '';
                    @if (app('hasPermission')(49, 'delete'))
                    if (item.status === 'draft') {
                        deleteAction = `<a href="javascript:void(0);" class="prod-act is-delete delete-production" data-id="${item.id}" title="Delete"><i class="far fa-trash-alt"></i></a>`;
                    }
                    @endif

                    return `${viewAction}${editAction}${deleteAction}`;
                }

                let editAction = '';
                @if (app('hasPermission')(49, 'edit'))
                editAction = item.status === 'draft'
                    ? `<a href="/inventory/productions/${item.id}/edit" class="me-3" title="Edit"><img src="${editIcon}" alt="edit"></a>`
                    : (item.status === 'in_production'
                        ? `<a href="/inventory/productions/${item.id}/edit" class="me-3" title="Complete Production"><img src="${editIcon}" alt="edit"></a>`
                        : '');
                @endif

                let deleteAction = '';
                @if (app('hasPermission')(49, 'delete'))
                deleteAction = item.status === 'draft'
                    ? `<a href="javascript:void(0);" class="delete-production me-3" data-id="${item.id}" title="Delete"><img src="${deleteIcon}" alt="delete"></a>`
                    : '';
                @endif

                let viewAction = '';
                @if (app('hasPermission')(49, 'view'))
                viewAction = `<a href="/inventory/productions/${item.id}" title="View"><img src="${eyeIcon}" alt="view"></a>`;
                @endif

                const res = `${editAction}${deleteAction}${viewAction}`;
                return res || '<span class="text-muted">-</span>';
            }

            function mobileCard(item) {
                const statusClass = {
                    'completed': 'is-ok',
                    'in_production': 'is-purple',
                    'draft': 'is-draft'
                }[item.status] || 'is-draft';

                const statusLabel = {
                    'completed': 'COMPLETED',
                    'in_production': 'IN PRODUCTION',
                    'draft': 'DRAFT'
                }[item.status] || (esc(item.status || '-').toUpperCase());

                const prodName = esc(item.product?.name || 'N/A');
                const prodNo = esc(item.production_no || '-');
                const bomCode = esc(item.bom?.bom_code || '-');
                const unitName = esc(item.product?.unit?.unit_name || '');
                const prodQty = parseFloat(item.production_qty || 0).toFixed(3);
                const outQty = parseFloat(item.output_qty || 0).toFixed(3);
                const totalCost = parseFloat(item.total_cost || 0).toFixed(2);
                const date = esc(item.production_date || '-');

                return `
                    <div class="prod-card">
                        <div class="prod-card-top">
                            <span class="prod-card-name">${prodName}</span>
                            <span class="prod-card-code">${prodNo}</span>
                        </div>
                        <div class="prod-card-badges">
                            <span class="prod-badge ${statusClass}">${statusLabel}</span>
                            <span class="prod-card-sku">BOM: ${bomCode}</span>
                            <span class="prod-card-sku ms-auto"><i class="fa fa-calendar-alt me-1"></i>${date}</span>
                        </div>
                        <div class="prod-card-bottom">
                            <div class="prod-stats">
                                <div class="prod-stat">
                                    <span>Prod Qty</span>
                                    <strong>${prodQty} ${unitName}</strong>
                                </div>
                                <div class="prod-stat">
                                    <span>Output Qty</span>
                                    <strong>${outQty} ${unitName}</strong>
                                </div>
                                <div class="prod-stat">
                                    <span>Cost</span>
                                    <strong>₹${totalCost}</strong>
                                </div>
                            </div>
                            <div class="prod-acts">
                                ${actionButtons(item, true)}
                            </div>
                        </div>
                    </div>
                `;
            }

            function filterAndRender() {
                const q = ($('#prodSearchInput').val() || '').trim().toLowerCase();
                if (q) {
                    $('#prodSearchClear').show();
                } else {
                    $('#prodSearchClear').hide();
                }

                const filtered = rawProductionsData.filter(function(item) {
                    if (!q) return true;
                    const no = (item.production_no || '').toLowerCase();
                    const name = (item.product?.name || '').toLowerCase();
                    const bom = (item.bom?.bom_code || '').toLowerCase();
                    const status = (item.status || '').toLowerCase();
                    const date = (item.production_date || '').toLowerCase();
                    return no.includes(q) || name.includes(q) || bom.includes(q) || status.includes(q) || date.includes(q);
                });

                // Desktop Rows
                const rows = filtered.map(function(item) {
                    const statusBadgeClass = {
                        'completed': 'bg-lightgreen',
                        'in_production': 'bg-lightpurple',
                        'draft': 'bg-lightyellow'
                    }[item.status] || 'bg-lightyellow';

                    const statusBadgeLabel = {
                        'completed': 'Completed',
                        'in_production': 'In Production',
                        'draft': 'Draft'
                    }[item.status] || item.status || '-';

                    return `
                        <tr>
                            <td>${esc(item.production_no || '-')}</td>
                            <td>${esc(item.product?.name || 'N/A')}</td>
                            <td>${esc(item.bom?.bom_code || '-')}</td>
                            <td>${parseFloat(item.production_qty || 0).toFixed(3)} ${esc(item.product?.unit?.unit_name || '')}</td>
                            <td>${parseFloat(item.output_qty || 0).toFixed(3)} ${esc(item.product?.unit?.unit_name || '')}</td>
                            <td>${parseFloat(item.total_cost || 0).toFixed(2)}</td>
                            <td><span class="badges ${statusBadgeClass}">${statusBadgeLabel}</span></td>
                            <td>${esc(item.production_date || '-')}</td>
                            <td>${actionButtons(item, false)}</td>
                        </tr>
                    `;
                }).join('');
                $('#production-table-body').html(rows || '<tr><td colspan="9" class="text-center text-muted">No production records found.</td></tr>');

                // Mobile Cards
                const mobile = filtered.map(mobileCard).join('');
                $('#production-mobile-list').html(mobile || `<div class="prod-empty">No production records found.</div>`);

                // Counter
                if (rawProductionsData.length > 0) {
                    $('#prodFooterCounter').text(`Showing ${filtered.length} of ${rawProductionsData.length} items`);
                } else {
                    $('#prodFooterCounter').text('');
                }
            }

            function loadProductions() {
                $.ajax({
                    url: '/api/manufacturing/productions',
                    type: 'GET',
                    data: {
                        selectedSubAdminId: selectedSubAdminId
                    },
                    headers: {
                        Authorization: 'Bearer ' + authToken
                    },
                    success: function(response) {
                        rawProductionsData = response.data || [];
                        filterAndRender();
                    },
                    error: function() {
                        $('#production-table-body').html('<tr><td colspan="9" class="text-center text-danger">Failed to load production records.</td></tr>');
                        $('#production-mobile-list').html(`<div class="prod-empty text-danger">Failed to load production records.</div>`);
                        $('#prodFooterCounter').text('');
                    }
                });
            }

            $('#prodSearchInput').on('input', filterAndRender);
            $('#prodSearchClear').on('click', function() {
                $('#prodSearchInput').val('');
                filterAndRender();
            });

            $(document).on('click', '.delete-production', function() {
                const productionId = $(this).data('id');

                Swal.fire({
                    title: 'Delete production?',
                    text: 'This will permanently remove the draft production record.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: `/api/manufacturing/productions/${productionId}`,
                        type: 'DELETE',
                        data: {
                            selectedSubAdminId: selectedSubAdminId
                        },
                        headers: {
                            Authorization: 'Bearer ' + authToken
                        },
                        success: function(response) {
                            Swal.fire('Deleted', response.message || 'Production deleted successfully.', 'success');
                            loadProductions();
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message || 'Failed to delete production.', 'error');
                        }
                    });
                });
            });

            loadProductions();
        });
    </script>
@endpush
