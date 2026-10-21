@extends('layout.app')

@section('title', 'Bill of Materials')

@section('content')
    <style>
        .bom-page .page-header {
            gap: 12px;
        }

        .bom-page .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .bom-page .badges {
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }

        /* Desktop View */
        .bom-page .bom-desktop-view {
            display: block;
        }

        .bom-page .bom-mobile-view {
            display: none;
        }

        /* Mobile / Tablet View (< 1200px) */
        @media screen and (max-width: 1199.98px) {
            .bom-page .page-header {
                flex-direction: column;
                align-items: stretch;
                margin-bottom: 14px;
                text-align: center;
            }

            .bom-page .page-title h4 {
                font-size: 18px;
                font-weight: 700;
                color: #1b2850;
                margin-bottom: 4px;
            }

            .bom-page .page-title h6 {
                font-size: 13px;
                color: #6b7280;
                line-height: 1.4;
            }

            .bom-page .page-btn {
                width: 100%;
                margin-top: 8px;
            }

            .bom-page .page-btn .btn.btn-added {
                width: 100%;
                justify-content: center;
                background: #ff9f43;
                border-color: #ff9f43;
                color: #fff;
                font-weight: 600;
                border-radius: 8px;
                padding: 10px 16px;
            }

            .bom-page .main-card {
                background: transparent !important;
                border: 0 !important;
                box-shadow: none !important;
                margin-bottom: 0 !important;
            }

            .bom-page .main-card > .card-body {
                padding: 0 !important;
            }

            .bom-page .bom-desktop-view {
                display: none !important;
            }

            .bom-page .bom-mobile-view {
                display: block !important;
            }
        }

        /* Mobile Search Bar (matches material-inventory-list) */
        .bom-filter-row {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
            border: 1px solid #eef0f3;
            padding: 8px 10px;
            margin-bottom: 12px;
        }

        .bom-search-col {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bom-search-box {
            position: relative;
            flex: 1 1 auto;
            display: flex;
            align-items: center;
        }

        .bom-search-box .search-icon {
            position: absolute;
            left: 12px;
            color: #8b93a7;
            font-size: 13px;
            pointer-events: none;
        }

        .bom-search-box input {
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

        .bom-search-box input:focus {
            border-color: #ff9f43;
            box-shadow: 0 0 0 3px rgba(255, 159, 67, 0.15);
            background: #fff;
        }

        .bom-search-box input::placeholder {
            color: #8b93a7;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .bom-search-box .search-clear {
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
        .bom-card-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .bom-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
            border: 1px solid #eef0f3;
            padding: 12px 14px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .bom-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
        }

        .bom-card-name {
            font-size: 15px;
            font-weight: 700;
            color: #1b2850;
            text-transform: uppercase;
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .bom-card-code {
            font-size: 14px;
            font-weight: 700;
            color: #1b2850;
            white-space: nowrap;
        }

        .bom-card-badges {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 6px 0 10px;
            min-width: 0;
            flex-wrap: wrap;
        }

        .bom-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 12px;
            white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .bom-badge.is-ok {
            background: #e7f8ef;
            color: #12a150;
        }

        .bom-badge.is-low {
            background: #fdecec;
            color: #e5483d;
        }

        .bom-card-sku {
            font-size: 11px;
            color: #8b93a7;
            font-weight: 500;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .bom-card-bottom {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
        }

        .bom-stats {
            display: flex;
            align-items: flex-end;
            gap: 20px;
            flex-wrap: wrap;
        }

        .bom-stat span {
            display: block;
            font-size: 11px;
            color: #8b93a7;
            margin-bottom: 2px;
        }

        .bom-stat strong {
            font-size: 14px;
            color: #1b2850;
            font-weight: 700;
        }

        .bom-acts {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .bom-act {
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

        .bom-act:hover,
        .bom-act:active {
            color: #ff9f43;
            background: #fff4e8;
            border-color: #ffd2a6;
        }

        .bom-act.is-delete:hover,
        .bom-act.is-delete:active {
            color: #e5483d;
            background: #fdecec;
            border-color: #fca5a5;
        }

        .bom-empty {
            text-align: center;
            padding: 30px 15px;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #eef0f3;
            color: #8b93a7;
            font-size: 13px;
        }

        .bom-footer-counter {
            font-size: 12px;
            color: #8b93a7;
            text-align: center;
            padding: 10px 0 20px;
        }
    </style>

    <div class="content">
        <div class="bom-page">
            <div class="page-header">
                <div class="page-title">
                    <h4>Bill of Materials</h4>
                    <h6>Link finished goods with raw material recipes</h6>
                </div>
                <div class="page-btn">
                    @if (app('hasPermission')(49, 'add'))
                    <a href="{{ route('inventory.bom.add') }}" class="btn btn-added">
                        <i class="fa fa-plus me-2"></i>Create BOM
                    </a>
                    @endif
                </div>
            </div>

            <div class="card main-card">
                <div class="card-body">
                    <!-- Desktop Table View -->
                    <div class="bom-desktop-view">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>BOM Code</th>
                                        <th>Finished Product</th>
                                        <th>Base Qty</th>
                                        <th>Unit</th>
                                        <th>Materials</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="bom-table-body">
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Loading BOMs...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile / Tablet Card View -->
                    <div class="bom-mobile-view">
                        <div class="bom-filter-row">
                            <div class="bom-search-col">
                                <div class="bom-search-box">
                                    <i class="fa fa-search search-icon"></i>
                                    <input type="text" id="bomSearchInput" placeholder="SEARCH..." autocomplete="off">
                                    <button type="button" id="bomSearchClear" class="search-clear"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="bom-card-list" id="bom-mobile-list">
                            <div class="bom-empty">Loading BOMs...</div>
                        </div>

                        <div class="bom-footer-counter" id="bomFooterCounter"></div>
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
            let rawBomsData = [];

            function esc(value) {
                return String(value ?? '').replace(/[&<>"']/g, function(c) {
                    return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c];
                });
            }

            function actionButtons(bomId, mobile = false) {
                let html = '';
                if (mobile) {
                    @if (app('hasPermission')(49, 'view'))
                    html += `<a href="/inventory/boms/${bomId}/view" class="bom-act" title="View"><i class="fa fa-eye"></i></a>`;
                    @endif
                    @if (app('hasPermission')(49, 'edit'))
                    html += `<a href="/inventory/boms/${bomId}/edit" class="bom-act" title="Edit"><i class="fa fa-pen"></i></a>`;
                    @endif
                    @if (app('hasPermission')(49, 'delete'))
                    html += `<a href="javascript:void(0);" class="bom-act is-delete delete-bom" data-id="${bomId}" title="Delete"><i class="far fa-trash-alt"></i></a>`;
                    @endif
                    return html;
                }

                @if (app('hasPermission')(49, 'view'))
                html += `
                    <a href="/inventory/boms/${bomId}/view" class="me-2" title="View">
                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/eye.svg' }}" alt="view">
                    </a>`;
                @endif
                @if (app('hasPermission')(49, 'edit'))
                html += `
                    <a href="/inventory/boms/${bomId}/edit" class="me-2" title="Edit">
                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/edit.svg' }}" alt="edit">
                    </a>`;
                @endif
                @if (app('hasPermission')(49, 'delete'))
                html += `
                    <a href="javascript:void(0);" class="delete-bom" data-id="${bomId}" title="Delete">
                        <img src="{{ env('ImagePath') . '/admin/assets/img/icons/delete.svg' }}" alt="delete">
                    </a>`;
                @endif
                
                return html || '<span class="text-muted">-</span>';
            }

            function mobileCard(bom) {
                const isAct = (bom.status === 'active');
                const statusClass = isAct ? 'is-ok' : 'is-low';
                const statusLabel = isAct ? 'ACTIVE' : (esc(bom.status || 'INACTIVE').toUpperCase());
                const prodName = esc(bom.product?.name || 'N/A');
                const bomCode = esc(bom.bom_code || '-');
                const baseQty = parseFloat(bom.base_quantity || 0).toFixed(3);
                const unit = esc(bom.product?.unit?.unit_name || '-');
                const count = bom.items_count || 0;

                return `
                    <div class="bom-card">
                        <div class="bom-card-top">
                            <span class="bom-card-name">${prodName}</span>
                            <span class="bom-card-code">${bomCode}</span>
                        </div>
                        <div class="bom-card-badges">
                            <span class="bom-badge ${statusClass}">${statusLabel}</span>
                            <span class="bom-card-sku">${count} Materials</span>
                        </div>
                        <div class="bom-card-bottom">
                            <div class="bom-stats">
                                <div class="bom-stat">
                                    <span>Base Qty</span>
                                    <strong>${baseQty}</strong>
                                </div>
                                <div class="bom-stat">
                                    <span>Unit</span>
                                    <strong>${unit}</strong>
                                </div>
                            </div>
                            <div class="bom-acts">
                                ${actionButtons(bom.id, true)}
                            </div>
                        </div>
                    </div>
                `;
            }

            function filterAndRender() {
                const q = ($('#bomSearchInput').val() || '').trim().toLowerCase();
                if (q) {
                    $('#bomSearchClear').show();
                } else {
                    $('#bomSearchClear').hide();
                }

                const filtered = rawBomsData.filter(function(bom) {
                    if (!q) return true;
                    const code = (bom.bom_code || '').toLowerCase();
                    const name = (bom.product?.name || '').toLowerCase();
                    const status = (bom.status || '').toLowerCase();
                    const unit = (bom.product?.unit?.unit_name || '').toLowerCase();
                    return code.includes(q) || name.includes(q) || status.includes(q) || unit.includes(q);
                });

                // Desktop Table
                const rows = filtered.map(function(bom) {
                    return `
                        <tr>
                            <td>${esc(bom.bom_code || '-')}</td>
                            <td>${esc(bom.product?.name || 'N/A')}</td>
                            <td>${parseFloat(bom.base_quantity || 0).toFixed(3)}</td>
                            <td>${esc(bom.product?.unit?.unit_name || '-')}</td>
                            <td>${bom.items_count || 0}</td>
                            <td><span class="badges ${bom.status === 'active' ? 'bg-lightgreen' : 'bg-lightred'}">${esc(bom.status || '-')}</span></td>
                            <td>
                                ${actionButtons(bom.id, false)}
                            </td>
                        </tr>
                    `;
                }).join('');
                $('#bom-table-body').html(rows || '<tr><td colspan="7" class="text-center text-muted">No BOMs found.</td></tr>');

                // Mobile Cards
                const mobile = filtered.map(mobileCard).join('');
                $('#bom-mobile-list').html(mobile || `<div class="bom-empty">No BOMs found.</div>`);

                // Counter
                if (rawBomsData.length > 0) {
                    $('#bomFooterCounter').text(`Showing ${filtered.length} of ${rawBomsData.length} items`);
                } else {
                    $('#bomFooterCounter').text('');
                }
            }

            function loadBoms() {
                $.ajax({
                    url: '/api/manufacturing/boms',
                    type: 'GET',
                    data: {
                        selectedSubAdminId: selectedSubAdminId
                    },
                    headers: {
                        Authorization: 'Bearer ' + authToken
                    },
                    success: function(response) {
                        rawBomsData = response.data || [];
                        filterAndRender();
                    },
                    error: function() {
                        $('#bom-table-body').html('<tr><td colspan="7" class="text-center text-danger">Failed to load BOMs.</td></tr>');
                        $('#bom-mobile-list').html(`<div class="bom-empty text-danger">Failed to load BOMs.</div>`);
                        $('#bomFooterCounter').text('');
                    }
                });
            }

            $('#bomSearchInput').on('input', filterAndRender);
            $('#bomSearchClear').on('click', function() {
                $('#bomSearchInput').val('');
                filterAndRender();
            });

            $(document).on('click', '.delete-bom', function() {
                const bomId = $(this).data('id');

                Swal.fire({
                    title: 'Delete BOM?',
                    text: 'This will permanently remove the BOM if it has not been used in production.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: `/api/manufacturing/boms/${bomId}`,
                        type: 'DELETE',
                        data: {
                            selectedSubAdminId: selectedSubAdminId
                        },
                        headers: {
                            Authorization: 'Bearer ' + authToken
                        },
                        success: function(response) {
                            Swal.fire('Deleted', response.message || 'BOM deleted successfully.', 'success');
                            loadBoms();
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message || 'Failed to delete BOM.', 'error');
                        }
                    });
                });
            });

            loadBoms();
        });
    </script>
@endpush
