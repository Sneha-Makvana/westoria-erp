@extends('layout.app')

@section('title', 'HSN Summary')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />

<!-- Mobile & Tablet Filter Backdrop & Bottom Sheet (<=1024.98px) -->
<div class="hsn-filter-backdrop hsn-responsive-only" id="hsnFilterBackdrop"></div>
<div class="hsn-filter-sheet hsn-responsive-only" id="hsnFilterSheet" role="dialog" aria-modal="true" aria-labelledby="hsnFilterSheetTitle">
    <div class="hsn-filter-sheet-head">
        <span id="hsnFilterSheetTitle"><i class="fas fa-sliders-h me-2 text-warning"></i>Filter HSN Summary</span>
        <button type="button" class="hsn-filter-sheet-close" id="hsnFilterSheetClose" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="hsn-filter-sheet-body" id="hsnFilterSheetBody"></div>
    <div class="hsn-filter-sheet-foot">
        <button type="button" class="hsn-filter-clear" id="hsnFilterClear">Reset</button>
        <button type="button" class="hsn-filter-apply" id="hsnFilterApply">Apply Filters</button>
    </div>
</div>

<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>HSN Summary</h4>
            <h6 class="text-muted mb-0">GSTR-1 HSN summary list (IGST / CGST / SGST by Place of Supply)</h6>
        </div>
        <div class="page-btn">
            <a class="btn btn-success" id="exportHsnSummary">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Responsive Search & Filter Toolbar (<=1024.98px) -->
            <div class="hsn-toolbar hsn-responsive-only mb-2">
                <div class="hsn-search-bar">
                    <span class="hsn-search-icon">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}" alt="img">
                    </span>
                    <input type="text" id="hsnSearchInput" class="form-control" placeholder="SEARCH HSN OR DESCRIPTION...">
                </div>
                <button type="button" class="hsn-filter-toggle" id="hsnFilterToggle" aria-label="Filters" title="Filters">
                    <i class="fas fa-filter"></i>
                    <span class="hsn-filter-count d-none" id="hsnFilterCount">0</span>
                </button>
            </div>
            <div class="hsn-filter-active-bar hsn-responsive-only mb-3" id="hsnFilterSummaryTrigger" title="Click to edit filters">
                <i class="fas fa-sliders-h text-warning me-1"></i>
                <span class="hsn-filter-summary-text" id="hsnFilterSummaryText">Tap to filter by date, month, year, orders...</span>
                <span class="hsn-filter-edit-hint ms-auto"><i class="fas fa-pen me-1"></i>Filter</span>
            </div>

            <!-- Desktop Filter Container (>1024.98px) -->
            <div id="desktopFilterContainer" class="hsn-desktop-only mb-3">
                <div id="hsnFilterFormWrap" class="gst-report-filters">
                    <div class="row g-2 align-items-end">
                    <div class="col-md-2 col-6">
                        <label for="from_date" class="form-label fw-bold mb-1">Start Date</label>
                        <input type="date" id="from_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 col-6">
                        <label for="to_date" class="form-label fw-bold mb-1">End Date</label>
                        <input type="date" id="to_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 col-6">
                        <label for="month_from" class="form-label fw-bold mb-1">From Month</label>
                        <select id="month_from" class="form-control form-control-sm gst-month-field">
                            <option value="">-- From --</option>
                            @php $monthCursor = \Carbon\Carbon::now()->startOfMonth(); @endphp
                            @for ($i = 0; $i < 36; $i++)
                                @php $m = $monthCursor->copy()->subMonths($i); @endphp
                                <option value="{{ $m->format('Y-m') }}">{{ $m->format('M Y') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label for="month_to" class="form-label fw-bold mb-1">To Month</label>
                        <select id="month_to" class="form-control form-control-sm gst-month-field">
                            <option value="">-- To --</option>
                            @for ($i = 0; $i < 36; $i++)
                                @php $m = $monthCursor->copy()->subMonths($i); @endphp
                                <option value="{{ $m->format('Y-m') }}">{{ $m->format('M Y') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label for="month_preset" class="form-label fw-bold mb-1">Quick Month</label>
                        <select id="month_preset" class="form-control form-control-sm">
                            <option value="">-- Quick --</option>
                            <option value="1">This Month</option>
                            <option value="3">Last 3 Months</option>
                            <option value="6">Last 6 Months</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label for="year_select" class="form-label fw-bold mb-1">Year</label>
                        <select id="year_select" class="form-control form-control-sm">
                            <option value="">-- Select Year --</option>
                            @for ($i = date('Y'); $i >= 2020; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="row g-2 align-items-end mt-1">
                    <div class="col-md-6 col-12">
                        <label class="form-label fw-bold mb-1">Orders</label>
                        <div class="hsn-order-picker" id="hsnOrderPicker">
                            <button type="button" class="form-control form-control-sm text-start hsn-order-toggle" id="hsnOrderToggle">
                                <span id="hsnOrderToggleText">All Orders</span>
                                <i class="fa fa-chevron-down float-end mt-1"></i>
                            </button>
                            <div class="hsn-order-panel" id="hsnOrderPanel" style="display:none;">
                                <input type="text" class="form-control form-control-sm mb-2" id="hsnOrderSearch" placeholder="Filter orders...">
                                <div class="d-flex gap-2 mb-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0" id="hsnOrderSelectAll">Select all</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0" id="hsnOrderClear">Clear</button>
                                </div>
                                <div class="hsn-order-list" id="hsnOrderList">
                                    <div class="text-muted small p-2">Loading orders...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    <div class="d-flex flex-wrap gap-2 mt-2 align-items-center hsn-desktop-actions">
                        <button type="button" class="btn btn-primary btn-sm" id="btnLoadHsn">
                            <i class="fa fa-search"></i> Show List
                        </button>
                        <small class="text-muted">
                            Click Orders to open the list, then tick one or more. Leave empty for <strong>All Orders</strong>.
                        </small>
                        <small class="text-muted" id="hsnFilterHint"></small>
                    </div>
                </div>
            </div>

            <style>
                .hsn-order-picker { position: relative; }
                .hsn-order-toggle {
                    background: #fff;
                    cursor: pointer;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }
                .hsn-order-panel {
                    position: absolute;
                    z-index: 1050;
                    left: 0;
                    right: 0;
                    top: calc(100% + 2px);
                    background: #fff;
                    border: 1px solid #ced4da;
                    border-radius: 4px;
                    padding: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,.12);
                }
                .hsn-order-list {
                    max-height: 260px;
                    overflow-y: auto;
                    border: 1px solid #eee;
                    border-radius: 3px;
                }
                .hsn-order-item {
                    display: flex;
                    align-items: flex-start;
                    gap: 8px;
                    padding: 6px 8px;
                    margin: 0;
                    cursor: pointer;
                    border-bottom: 1px solid #f3f3f3;
                    font-size: 12px;
                    line-height: 1.35;
                }
                .hsn-order-item:last-child { border-bottom: 0; }
                .hsn-order-item:hover { background: #fff4ee; }
                .hsn-order-item input { margin-top: 2px; flex-shrink: 0; }
                .hsn-order-item.hidden { display: none; }

                /* Hide responsive elements on desktop (>1024.98px) */
                .hsn-responsive-only,
                #hsnCardsList,
                .hsn-filter-backdrop,
                .hsn-filter-sheet {
                    display: none;
                }
                .hsn-desktop-only {
                    display: block;
                }

                /* Responsive styles (<= 1024.98px) */
                @media (max-width: 1024.98px), (width: 1032px) and (height: 1376px), (width: 1280px) and (height: 800px) {
                    .hsn-desktop-only {
                        display: none !important;
                    }

                    .hsn-responsive-only {
                        display: flex !important;
                    }

                    .hsn-filter-backdrop.hsn-responsive-only {
                        display: block !important;
                    }

                    .hsn-filter-sheet.hsn-responsive-only {
                        display: flex !important;
                    }

                    .table-responsive {
                        display: none !important;
                    }

                    #hsnCardsList {
                        display: grid !important;
                        gap: 10px;
                    }

                    .content > .card {
                        background: transparent !important;
                        border: 0 !important;
                        box-shadow: none !important;
                    }

                    .content > .card > .card-body {
                        padding: 0 !important;
                    }

                    #hsnSummaryCards > div > div {
                        border-radius: 10px !important;
                        border-color: #eceef3 !important;
                        background: #fff !important;
                        box-shadow: 0 1px 3px rgba(16, 24, 40, .05) !important;
                    }
                }

                /* Tablet 2 columns grid (768px - 1024.98px) */
                @media screen and (min-width: 768px) and (max-width: 1024.98px) {
                    #hsnCardsList {
                        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                        align-items: start;
                    }
                }

                /* Mobile phone single column (< 768px) */
                @media (max-width: 767.98px) {
                    #hsnCardsList {
                        grid-template-columns: 1fr !important;
                    }

                    .content {
                        padding: 12px 10px !important;
                    }

                    .page-header {
                        margin-bottom: 12px !important;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        gap: 8px;
                    }

                    .page-header .page-title h4 {
                        font-size: 18px;
                        margin-bottom: 2px;
                    }

                    .page-header .page-title h6 {
                        font-size: 11.5px;
                    }

                    .page-header .page-btn .btn {
                        padding: 6px 12px;
                        font-size: 12px;
                        white-space: nowrap;
                    }
                }

                /* Responsive Toolbar (Search + Filter Toggle) */
                .hsn-toolbar {
                    align-items: center;
                    gap: 10px;
                    background: #fff;
                    border-radius: 12px;
                    padding: 8px 10px;
                    box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
                }

                .hsn-search-bar {
                    position: relative;
                    flex: 1 1 auto;
                    min-width: 0;
                    display: flex;
                    align-items: center;
                }

                .hsn-search-icon {
                    position: absolute;
                    left: 12px;
                    top: 50%;
                    transform: translateY(-50%);
                    z-index: 2;
                    display: flex;
                    align-items: center;
                    pointer-events: none;
                }

                .hsn-search-icon img {
                    width: 15px;
                    height: 15px;
                }

                .hsn-search-bar input {
                    width: 100%;
                    height: 40px;
                    padding-left: 38px;
                    border: 1px solid #e2e8f0;
                    border-radius: 10px;
                    font-size: 13px;
                    background: #fff;
                    color: #1b2850;
                    box-shadow: none;
                }

                .hsn-search-bar input::placeholder {
                    color: #94a3b8;
                    font-size: 12px;
                    letter-spacing: 0.5px;
                    text-transform: uppercase;
                }

                .hsn-search-bar input:focus {
                    border-color: #ff9f43;
                    box-shadow: 0 0 0 3px rgba(255, 159, 67, 0.15);
                }

                .hsn-filter-toggle {
                    position: relative;
                    flex: 0 0 40px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    width: 40px;
                    height: 40px;
                    padding: 0;
                    border: 0;
                    border-radius: 50%;
                    background: #f05a6a;
                    color: #fff;
                    font-size: 15px;
                    box-shadow: 0 2px 6px rgba(240, 90, 106, .35);
                    cursor: pointer;
                    transition: background .15s ease, transform .1s ease;
                }

                .hsn-filter-toggle:hover {
                    background: #e04859;
                }

                .hsn-filter-toggle:active {
                    transform: scale(0.95);
                }

                .hsn-filter-toggle.has-active {
                    background: #d92d40;
                }

                .hsn-filter-count {
                    position: absolute;
                    top: -4px;
                    right: -4px;
                    min-width: 18px;
                    height: 18px;
                    padding: 0 5px;
                    border-radius: 999px;
                    background: #1b2850;
                    color: #fff;
                    font-size: 10.5px;
                    font-weight: 700;
                    line-height: 18px;
                    text-align: center;
                }

                .hsn-filter-active-bar {
                    align-items: center;
                    gap: 6px;
                    padding: 8px 12px;
                    background: #fff;
                    border: 1px solid #eef0f4;
                    border-radius: 10px;
                    box-shadow: 0 1px 2px rgba(16, 24, 40, .04);
                    cursor: pointer;
                    font-size: 12.5px;
                }

                .hsn-filter-summary-text {
                    color: #475467;
                    font-weight: 500;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }

                .hsn-filter-edit-hint {
                    color: #ff9f43 !important;
                    font-weight: 600;
                    font-size: 11.5px;
                }

                /* HSN Cards */
                .hsn-card {
                    background: #fff;
                    border: 1px solid #eceef3;
                    border-radius: 12px;
                    box-shadow: 0 1px 3px rgba(16, 24, 40, .05);
                    padding: 12px;
                    transition: transform .15s ease, box-shadow .15s ease;
                }

                .hsn-card:hover {
                    box-shadow: 0 4px 12px rgba(16, 24, 40, .08);
                }

                .hsn-card-head {
                    display: flex;
                    align-items: flex-start;
                    gap: 10px;
                }

                .hsn-avatar {
                    width: 44px;
                    height: 44px;
                    flex-shrink: 0;
                    border-radius: 10px;
                    background: #fff3e6;
                    color: #e07b14;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: 700;
                    font-size: 13px;
                    letter-spacing: 0.5px;
                }

                .hsn-main-info {
                    flex: 1 1 auto;
                    min-width: 0;
                }

                .hsn-card-desc {
                    font-size: 13.5px;
                    font-weight: 600;
                    color: #1b2850;
                    text-transform: uppercase;
                    line-height: 1.3;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }

                .hsn-card-meta {
                    display: flex;
                    align-items: center;
                    flex-wrap: wrap;
                    gap: 4px 6px;
                    margin-top: 4px;
                }

                .hsn-tag {
                    font-size: 11px;
                    color: #64748b;
                    background: #f1f5f9;
                    padding: 1px 6px;
                    border-radius: 6px;
                    font-weight: 500;
                    white-space: nowrap;
                }

                .hsn-tag.uqc-tag {
                    background: #f8fafc;
                    border: 1px solid #e2e8f0;
                    color: #475467;
                }

                .hsn-rate-badge {
                    font-size: 10.5px;
                    font-weight: 600;
                    padding: 1px 7px;
                    border-radius: 10px;
                    background: #e7f8ef;
                    color: #12a150;
                    white-space: nowrap;
                }

                .hsn-card-amount {
                    flex-shrink: 0;
                    text-align: right;
                }

                .hsn-amt-label {
                    display: block;
                    font-size: 10px;
                    color: #8b93a7;
                    text-transform: uppercase;
                }

                .hsn-amt-val {
                    font-size: 14px;
                    font-weight: 700;
                    color: #1b2850;
                    white-space: nowrap;
                }

                .hsn-card-foot {
                    display: flex;
                    align-items: flex-end;
                    justify-content: space-between;
                    gap: 8px;
                    padding-top: 8px;
                    margin-top: 8px;
                    border-top: 1px dashed #eceef3;
                    flex-wrap: wrap;
                }

                .hsn-stat {
                    min-width: 0;
                    flex: 1 1 auto;
                }

                .hsn-stat .stat-lbl {
                    display: block;
                    font-size: 10.5px;
                    color: #8b93a7;
                    margin-bottom: 1px;
                }

                .hsn-stat .stat-val {
                    display: block;
                    font-size: 12px;
                    color: #1b2850;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }

                .hsn-pos-stat {
                    max-width: 100px;
                }

                /* Bottom Sheet Filter */
                .hsn-filter-backdrop {
                    position: fixed;
                    inset: 0;
                    z-index: 1040;
                    background: rgba(16, 24, 40, .45);
                    opacity: 0;
                    pointer-events: none;
                    transition: opacity .2s ease;
                }

                .hsn-filter-sheet {
                    position: fixed;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    z-index: 1045;
                    flex-direction: column;
                    max-height: 85vh;
                    background: #fff;
                    border-radius: 16px 16px 0 0;
                    box-shadow: 0 -8px 24px rgba(16, 24, 40, .18);
                    transform: translateY(105%);
                    transition: transform .25s ease;
                }

                @media (min-width: 600px) and (max-width: 1024.98px) {
                    .hsn-filter-sheet {
                        max-width: 580px;
                        left: 50% !important;
                        transform: translate(-50%, 105%) !important;
                        border-radius: 16px !important;
                        bottom: 20px !important;
                        max-height: 80vh !important;
                    }

                    body.hsn-filter-open .hsn-filter-sheet {
                        transform: translate(-50%, 0) !important;
                    }
                }

                body.hsn-filter-open .hsn-filter-backdrop {
                    opacity: 1;
                    pointer-events: auto;
                }

                body.hsn-filter-open:not(:has(.hsn-filter-sheet[style*="translate(-50%"])) .hsn-filter-sheet {
                    transform: translateY(0);
                }

                body.hsn-filter-open {
                    overflow: hidden;
                }

                body.hsn-filter-open .mobile-bottom-nav,
                body.hsn-filter-open .floating-calculator-wrapper {
                    display: none !important;
                }

                .hsn-filter-sheet-head {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 16px 18px;
                    border-bottom: 1px solid #eef0f4;
                    font-size: 16px;
                    font-weight: 600;
                    color: #1b2850;
                }

                .hsn-filter-sheet-close {
                    width: 32px;
                    height: 32px;
                    padding: 0;
                    border: 0;
                    background: transparent;
                    color: #64748b;
                    font-size: 20px;
                    cursor: pointer;
                }

                .hsn-filter-sheet-body {
                    flex: 1 1 auto;
                    overflow-y: auto;
                    padding: 14px 18px;
                }

                .hsn-filter-sheet-body #hsnFilterFormWrap .row {
                    margin: 0 !important;
                }

                .hsn-filter-sheet-body #hsnFilterFormWrap .col-md-2,
                .hsn-filter-sheet-body #hsnFilterFormWrap .col-md-6,
                .hsn-filter-sheet-body #hsnFilterFormWrap .col-6,
                .hsn-filter-sheet-body #hsnFilterFormWrap .col-12 {
                    width: 100% !important;
                    max-width: 100% !important;
                    flex: 0 0 100% !important;
                    padding: 0 !important;
                    margin-bottom: 12px !important;
                }

                .hsn-filter-sheet-body #hsnFilterFormWrap .hsn-desktop-actions {
                    display: none !important;
                }

                .hsn-filter-sheet-body label {
                    display: block;
                    margin-bottom: 6px !important;
                    font-size: 13px !important;
                    font-weight: 600 !important;
                    color: #344054 !important;
                }

                .hsn-filter-sheet-body .form-control {
                    height: 42px !important;
                    border-radius: 8px !important;
                    border-color: #d0d5dd !important;
                    font-size: 14px !important;
                    width: 100% !important;
                }

                .hsn-filter-sheet-foot {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 12px;
                    padding: 14px 18px calc(14px + env(safe-area-inset-bottom));
                    border-top: 1px solid #eef0f4;
                    background: #fff;
                }

                .hsn-filter-clear,
                .hsn-filter-apply {
                    height: 44px;
                    border: 0;
                    border-radius: 22px;
                    font-size: 14.5px;
                    font-weight: 600;
                    cursor: pointer;
                }

                .hsn-filter-clear {
                    background: #f1f5f9;
                    color: #475467;
                }

                .hsn-filter-apply {
                    background: #ff9f43;
                    color: #fff;
                }

                :root[data-theme="dark"] .hsn-toolbar,
                :root[data-theme="dark"] .hsn-filter-active-bar,
                :root[data-theme="dark"] .hsn-filter-sheet,
                :root[data-theme="dark"] .hsn-filter-sheet-head,
                :root[data-theme="dark"] .hsn-filter-sheet-foot {
                    background: #1e293b !important;
                    border-color: #334155 !important;
                    color: #f8fafc !important;
                }

                :root[data-theme="dark"] .hsn-search-bar input {
                    background: #0f172a !important;
                    border-color: #334155 !important;
                    color: #f8fafc !important;
                }

                :root[data-theme="dark"] .hsn-search-bar input::placeholder {
                    color: #64748b !important;
                }

                :root[data-theme="dark"] .hsn-filter-summary-text,
                :root[data-theme="dark"] .hsn-filter-sheet-body label {
                    color: #cbd5e1 !important;
                }

                :root[data-theme="dark"] .hsn-filter-clear {
                    background: #334155 !important;
                    color: #e2e8f0 !important;
                }
            </style>

            <div class="row g-2 mb-3" id="hsnSummaryCards">
                <div class="col-6 col-md-3 col-xl">
                    <div class="border rounded p-2 bg-light">
                        <div class="text-muted small">No. of HSN</div>
                        <div class="fw-bold" id="sumHsnCount">0</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-xl">
                    <div class="border rounded p-2 bg-light">
                        <div class="text-muted small">Total Value</div>
                        <div class="fw-bold" id="sumTotalValue">0.00</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-xl">
                    <div class="border rounded p-2 bg-light">
                        <div class="text-muted small">Taxable Value</div>
                        <div class="fw-bold" id="sumTaxable">0.00</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-xl">
                    <div class="border rounded p-2 bg-light">
                        <div class="text-muted small">IGST</div>
                        <div class="fw-bold" id="sumIgst">0.00</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-xl">
                    <div class="border rounded p-2 bg-light">
                        <div class="text-muted small">CGST</div>
                        <div class="fw-bold" id="sumCgst">0.00</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-xl">
                    <div class="border rounded p-2 bg-light">
                        <div class="text-muted small">SGST</div>
                        <div class="fw-bold" id="sumSgst">0.00</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-xl">
                    <div class="border rounded p-2 bg-light">
                        <div class="text-muted small">Cess</div>
                        <div class="fw-bold" id="sumCess">0.00</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="hsnSummaryTable">
                    <thead>
                        <tr style="background:#F7CAAC;">
                            <th>HSN</th>
                            <th>Description</th>
                            <th>UQC</th>
                            <th>Total Qty</th>
                            <th>Total Value</th>
                            <th>Rate</th>
                            <th>Taxable Value</th>
                            <th>IGST</th>
                            <th>CGST</th>
                            <th>SGST</th>
                            <th>Cess</th>
                            <th>Place Of Supply</th>
                        </tr>
                    </thead>
                    <tbody id="hsnSummaryBody">
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">
                                Choose filters and click <strong>Show List</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Responsive Cards List (<=1024.98px) -->
            <div id="hsnCardsList" class="mb-3">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2 text-warning"></i>
                    <div>Loading HSN records...</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function buildLastNMonths(count) {
        const values = [];
        const now = new Date();
        for (let i = 0; i < count; i++) {
            const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
            values.push(d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0'));
        }
        return values;
    }

    function buildMonthRange(fromYm, toYm) {
        if (!fromYm && !toYm) return [];
        let start = fromYm || toYm;
        let end = toYm || fromYm;
        if (start > end) {
            const tmp = start;
            start = end;
            end = tmp;
        }
        const [sy, sm] = start.split('-').map(Number);
        const [ey, em] = end.split('-').map(Number);
        const values = [];
        let y = sy, m = sm;
        while (y < ey || (y === ey && m <= em)) {
            values.push(y + '-' + String(m).padStart(2, '0'));
            m += 1;
            if (m > 12) { m = 1; y += 1; }
        }
        return values;
    }

    function getSelectedMonths() {
        const preset = $('#month_preset').val();
        if (preset) return buildLastNMonths(parseInt(preset, 10));
        return buildMonthRange($('#month_from').val(), $('#month_to').val());
    }

    function getSelectedOrderIds() {
        return $('#hsnOrderList input.hsn-order-check:checked').map(function () {
            return $(this).val();
        }).get();
    }

    function isAllOrdersSelected() {
        const total = $('#hsnOrderList .hsn-order-check').length;
        const selected = getSelectedOrderIds().length;
        return total > 0 && selected > 0 && selected >= total;
    }

    function updateOrderToggleText() {
        const ids = getSelectedOrderIds();
        if (!ids.length) {
            $('#hsnOrderToggleText').text('All Orders');
            return;
        }
        if (isAllOrdersSelected()) {
            $('#hsnOrderToggleText').text('All Orders (' + ids.length + ')');
            return;
        }
        if (ids.length === 1) {
            const label = $('#hsnOrderList input.hsn-order-check:checked').first().data('label') || ids[0];
            $('#hsnOrderToggleText').text(label);
            return;
        }
        $('#hsnOrderToggleText').text(ids.length + ' orders selected');
    }

    function getGstExportFilters() {
        const orderIds = getSelectedOrderIds();
        const allOrders = isAllOrdersSelected();

        return {
            from_date: $('#from_date').val(),
            to_date: $('#to_date').val(),
            year: $('#year_select').val(),
            months: getSelectedMonths(),
            // Avoid huge payloads / broken array serialization.
            all_orders: allOrders ? 1 : 0,
            // Comma-separated so every selected order reaches the server.
            order_ids: allOrders ? '' : orderIds.join(','),
            order_count: allOrders ? 0 : orderIds.length,
            selectedSubAdminId: localStorage.getItem('selectedSubAdminId'),
        };
    }

    function loadOrderOptions(clearSelection) {
        const authToken = localStorage.getItem('authToken');
        const selected = clearSelection ? [] : getSelectedOrderIds();
        const selectedSet = new Set((selected || []).map(String));
        const $list = $('#hsnOrderList');

        $list.html('<div class="text-muted small p-2">Loading orders...</div>');

        return $.ajax({
            url: '/api/gstr1/hsn-summary/orders',
            method: 'GET',
            traditional: true,
            headers: { Authorization: 'Bearer ' + authToken },
            data: {
                q: '',
                selectedSubAdminId: localStorage.getItem('selectedSubAdminId'),
            },
            success: function (data) {
                const results = data.results || [];
                if (!results.length) {
                    $list.html('<div class="text-muted small p-2">No orders found</div>');
                    updateOrderToggleText();
                    return;
                }

                let html = '';
                results.forEach(function (item) {
                    const checked = selectedSet.has(String(item.id)) ? ' checked' : '';
                    const orderNo = String(item.order_number || item.id);
                    const full = String(item.text || orderNo);
                    const safeAttr = $('<div>').text(full).html().replace(/"/g, '&quot;');
                    const safeOrderNo = $('<div>').text(orderNo).html();
                    html += `<label class="hsn-order-item" data-search="${safeAttr.toLowerCase()}">
                        <input type="checkbox" class="hsn-order-check" value="${item.id}" data-label="${safeAttr}"${checked}>
                        <span>${safeOrderNo}</span>
                    </label>`;
                });
                $list.html(html);
                updateOrderToggleText();
            },
            error: function () {
                $list.html('<div class="text-danger small p-2">Failed to load orders</div>');
            }
        });
    }

    function openOrderPanel() {
        $('#hsnOrderPanel').show();
        $('#hsnOrderSearch').val('').trigger('input');
        $('#hsnOrderSearch').trigger('focus');
    }

    function closeOrderPanel() {
        $('#hsnOrderPanel').hide();
    }

    function initOrderSelect() {
        $('#hsnOrderToggle').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if ($('#hsnOrderPanel').is(':visible')) {
                closeOrderPanel();
            } else {
                openOrderPanel();
            }
        });

        $('#hsnOrderPanel').on('click', function (e) {
            e.stopPropagation();
        });

        $(document).on('click.hsnOrderPicker', function () {
            closeOrderPanel();
        });

        $('#hsnOrderSearch').on('input', function () {
            const q = ($(this).val() || '').toLowerCase().trim();
            $('#hsnOrderList .hsn-order-item').each(function () {
                const hay = $(this).attr('data-search') || '';
                $(this).toggleClass('hidden', q !== '' && hay.indexOf(q) === -1);
            });
        });

        $('#hsnOrderList').on('change', '.hsn-order-check', function () {
            updateOrderToggleText();
        });

        $('#hsnOrderSelectAll').on('click', function () {
            $('#hsnOrderList .hsn-order-item:not(.hidden) .hsn-order-check').prop('checked', true);
            updateOrderToggleText();
        });

        $('#hsnOrderClear').on('click', function () {
            $('#hsnOrderList .hsn-order-check').prop('checked', false);
            updateOrderToggleText();
        });

        loadOrderOptions(false);
    }

    function refreshOrdersOnFilterChange() {
        // Order list is full branch list — keep selection; no reload on date change.
    }

    function formatNum(n) {
        return Number(n || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function clearDateWhenMonthOrYear() {
        $('#from_date, #to_date').val('');
    }

    function clearMonthWhenDateOrYear() {
        $('#month_from, #month_to, #month_preset').val('');
    }

    function clearYearWhenDateOrMonth() {
        $('#year_select').val('');
    }

    $('#from_date, #to_date').on('change', function () {
        if ($('#from_date').val() || $('#to_date').val()) {
            clearMonthWhenDateOrYear();
            clearYearWhenDateOrMonth();
        }
    });
    $('#month_from, #month_to').on('change', function () {
        if ($('#month_from').val() || $('#month_to').val()) {
            $('#month_preset').val('');
            clearDateWhenMonthOrYear();
            clearYearWhenDateOrMonth();
        }
    });
    $('#month_preset').on('change', function () {
        if ($(this).val()) {
            $('#month_from, #month_to').val('');
            clearDateWhenMonthOrYear();
            clearYearWhenDateOrMonth();
        }
    });
    $('#year_select').on('change', function () {
        if ($(this).val()) {
            clearDateWhenMonthOrYear();
            clearMonthWhenDateOrYear();
        }
    });

    function renderHsnRows(rows) {
        const $body = $('#hsnSummaryBody');
        const $cards = $('#hsnCardsList');
        if (!rows || !rows.length) {
            $body.html('<tr><td colspan="12" class="text-center text-muted py-4">No HSN records found for selected filters</td></tr>');
            $cards.html('<div class="hsn-empty-cards text-center text-muted py-5" style="grid-column: 1 / -1;"><i class="fas fa-inbox fa-3x mb-2 text-muted opacity-50"></i><div>No HSN records found for selected filters</div></div>');
            return;
        }
        let tableHtml = '';
        let cardsHtml = '';
        rows.forEach(function (row) {
            const hsnCode = row.hsn && row.hsn !== 'N/A' ? row.hsn : 'N/A';
            const desc = row.description || 'No description';
            const uqc = row.uqc || 'OTH';
            const rate = row.rate || '0';
            const pos = row.place_of_supply || '';
            const qty = formatNum(row.total_quantity);
            const totalVal = formatNum(row.total_value);
            const taxableVal = formatNum(row.taxable_value);
            const igst = formatNum(row.igst);
            const cgst = formatNum(row.cgst);
            const sgst = formatNum(row.sgst);
            const cess = formatNum(row.cess);

            // Table row (desktop)
            tableHtml += `<tr>
                <td>${row.hsn || ''}</td>
                <td>${desc}</td>
                <td>${uqc}</td>
                <td class="text-end">${qty}</td>
                <td class="text-end">${totalVal}</td>
                <td class="text-end">${rate}</td>
                <td class="text-end">${taxableVal}</td>
                <td class="text-end">${igst}</td>
                <td class="text-end">${cgst}</td>
                <td class="text-end">${sgst}</td>
                <td class="text-end">${cess}</td>
                <td>${pos}</td>
            </tr>`;

            // Badge initials or prefix
            const badgeText = hsnCode !== 'N/A' ? (hsnCode.length > 4 ? hsnCode.substring(0, 4) : hsnCode) : 'HSN';

            // Card row (tablet & mobile)
            cardsHtml += `
            <div class="hsn-card" data-search="${(hsnCode + ' ' + desc + ' ' + uqc + ' ' + pos).toLowerCase()}">
                <div class="hsn-card-head">
                    <div class="hsn-avatar">
                        <span>${badgeText}</span>
                    </div>
                    <div class="hsn-main-info">
                        <div class="hsn-card-desc" title="${desc}">${desc}</div>
                        <div class="hsn-card-meta">
                            <span class="hsn-tag"><i class="fas fa-barcode me-1"></i>${hsnCode}</span>
                            <span class="hsn-tag uqc-tag">${uqc}</span>
                            <span class="hsn-rate-badge">${rate}% Rate</span>
                        </div>
                    </div>
                    <div class="hsn-card-amount">
                        <span class="hsn-amt-label">Total Value</span>
                        <span class="hsn-amt-val">₹${totalVal}</span>
                    </div>
                </div>
                <div class="hsn-card-foot">
                    <div class="hsn-stat">
                        <span class="stat-lbl">Qty</span>
                        <strong class="stat-val">${qty}</strong>
                    </div>
                    <div class="hsn-stat">
                        <span class="stat-lbl">Taxable</span>
                        <strong class="stat-val">₹${taxableVal}</strong>
                    </div>
                    <div class="hsn-stat">
                        <span class="stat-lbl">IGST</span>
                        <strong class="stat-val">₹${igst}</strong>
                    </div>
                    <div class="hsn-stat">
                        <span class="stat-lbl">CGST</span>
                        <strong class="stat-val">₹${cgst}</strong>
                    </div>
                    <div class="hsn-stat">
                        <span class="stat-lbl">SGST</span>
                        <strong class="stat-val">₹${sgst}</strong>
                    </div>
                    ${pos ? `
                    <div class="hsn-stat hsn-pos-stat">
                        <span class="stat-lbl">POS</span>
                        <strong class="stat-val" title="${pos}">${pos}</strong>
                    </div>` : ''}
                </div>
            </div>`;
        });
        $body.html(tableHtml);
        $cards.html(cardsHtml);

        const curQ = ($('#hsnSearchInput').val() || '').toLowerCase().trim();
        if (curQ) {
            $('#hsnSearchInput').trigger('input');
        }
    }

    // Live search filter across cards and table rows
    $(document).on('input', '#hsnSearchInput', function () {
        const q = ($(this).val() || '').toLowerCase().trim();
        if (!q) {
            $('#hsnCardsList .hsn-card').show();
            $('#hsnSummaryBody tr').show();
            $('.hsn-card-search-empty').remove();
            return;
        }
        let visibleCount = 0;
        $('#hsnCardsList .hsn-card').each(function () {
            const text = $(this).attr('data-search') || '';
            const match = text.indexOf(q) !== -1;
            $(this).toggle(match);
            if (match) visibleCount++;
        });

        $('#hsnSummaryBody tr').each(function () {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(q) !== -1);
        });

        $('.hsn-card-search-empty').remove();
        if (visibleCount === 0 && $('#hsnCardsList .hsn-card').length > 0) {
            $('#hsnCardsList').append('<div class="hsn-card-search-empty text-center text-muted py-5 col-12" style="grid-column: 1 / -1;"><i class="fas fa-search fa-2x mb-2 opacity-50"></i><div>No matching HSN records found</div></div>');
        }
    });

    function renderSummary(summary) {
        $('#sumHsnCount').text(summary.hsn_count || 0);
        $('#sumTotalValue').text(formatNum(summary.total_value));
        $('#sumTaxable').text(formatNum(summary.total_taxable));
        $('#sumIgst').text(formatNum(summary.total_integrated));
        $('#sumCgst').text(formatNum(summary.total_central));
        $('#sumSgst').text(formatNum(summary.total_state));
        $('#sumCess').text(formatNum(summary.total_cess));
    }

    function loadHsnList() {
        const authToken = localStorage.getItem('authToken');
        const btn = $('#btnLoadHsn');
        const original = btn.html();
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');

        $.ajax({
            url: '/api/gstr1/hsn-summary',
            method: 'POST',
            data: getGstExportFilters(),
            traditional: true,
            headers: { Authorization: 'Bearer ' + authToken },
            success: function (res) {
                if (!res.status) {
                    alert(res.message || 'Failed to load HSN summary');
                    btn.prop('disabled', false).html(original);
                    return;
                }
                renderSummary(res.summary || {});
                renderHsnRows(res.rows || []);
                const applied = parseInt(res.orders_applied || 0, 10);
                const withItems = parseInt((res.summary || {}).orders_with_items || 0, 10);
                if (applied > 0) {
                    $('#hsnFilterHint').text(
                        'Applied ' + applied + ' selected order(s); ' + withItems +
                        ' order(s) have HSN items. Same HSN + rate + place of supply are combined into one row.'
                    );
                } else if (res.all_orders) {
                    $('#hsnFilterHint').text(
                        'Showing all branch orders (' + withItems + ' with HSN items). Same HSN rows are combined.'
                    );
                } else {
                    $('#hsnFilterHint').text('');
                }
                btn.prop('disabled', false).html(original);
            },
            error: function (xhr) {
                let msg = 'Failed to load HSN summary';
                if (xhr.status === 414 || xhr.status === 431) {
                    msg = 'Too many orders selected for request. Use Select all, or clear and leave All Orders.';
                } else {
                    try { msg = JSON.parse(xhr.responseText).message || msg; } catch (e) {}
                }
                alert(msg);
                btn.prop('disabled', false).html(original);
            }
        });
    }

    $('#btnLoadHsn').on('click', loadHsnList);

    $('#exportHsnSummary').on('click', function (e) {
        e.preventDefault();
        const authToken = localStorage.getItem('authToken');
        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Exporting...');

        $.ajax({
            url: '/api/gstr1/export-hsn-summary',
            method: 'POST',
            data: getGstExportFilters(),
            traditional: true,
            headers: { Authorization: 'Bearer ' + authToken },
            xhrFields: { responseType: 'blob' },
            success: function (blob) {
                if (blob.type && blob.type.indexOf('spreadsheet') === -1 && blob.type.indexOf('excel') === -1 && blob.type.indexOf('octet-stream') === -1) {
                    const reader = new FileReader();
                    reader.onload = function () {
                        try {
                            const err = JSON.parse(reader.result);
                            alert('Export failed: ' + (err.message || err.error || 'Server error'));
                        } catch (e) {
                            alert('Export failed: invalid response / session.');
                        }
                    };
                    reader.readAsText(blob);
                    btn.prop('disabled', false).html(originalHtml);
                    return;
                }
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'HSN_Summary.xlsx';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                btn.prop('disabled', false).html(originalHtml);
            },
            error: function (xhr) {
                let msg = 'Failed to export HSN Summary Excel!';
                if (xhr.status === 414 || xhr.status === 431) {
                    msg = 'Too many orders selected for request. Use Select all, or clear and leave All Orders.';
                } else {
                    try { msg = JSON.parse(xhr.responseText).message || msg; } catch (e) {}
                }
                alert(msg);
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Default: this month list
    $('#month_preset').val('1');
    initOrderSelect();
    loadHsnList();

    // Mobile Filter Bottom Sheet Logic
    initMobileFilterSheet();

    function initMobileFilterSheet() {
        const sheetBody = document.getElementById('hsnFilterSheetBody');
        const desktopContainer = document.getElementById('desktopFilterContainer');
        const formWrap = document.getElementById('hsnFilterFormWrap');
        const filterQuery = window.matchMedia('(max-width: 1024.98px), (width: 1032px) and (height: 1376px), (width: 1280px) and (height: 800px)');

        function placeFilterFields() {
            if (!sheetBody || !desktopContainer || !formWrap) return;
            if (filterQuery.matches) {
                if (!sheetBody.contains(formWrap)) {
                    sheetBody.appendChild(formWrap);
                }
            } else {
                if (!desktopContainer.contains(formWrap)) {
                    desktopContainer.appendChild(formWrap);
                    closeHsnFilterSheet();
                }
            }
        }

        function openHsnFilterSheet() {
            placeFilterFields();
            document.body.classList.add('hsn-filter-open');
            $('#hsnFilterToggle').attr('aria-expanded', 'true');
        }

        function closeHsnFilterSheet() {
            document.body.classList.remove('hsn-filter-open');
            $('#hsnFilterToggle').attr('aria-expanded', 'false');
            closeOrderPanel();
        }

        window.closeHsnFilterSheet = closeHsnFilterSheet;

        function updateFilterSummary() {
            const fromDate = $('#from_date').val();
            const toDate = $('#to_date').val();
            const monthFrom = $('#month_from').val();
            const monthTo = $('#month_to').val();
            const monthPreset = $('#month_preset').val();
            const year = $('#year_select').val();
            const orderIds = getSelectedOrderIds();

            let count = 0;
            let parts = [];

            if (fromDate || toDate) {
                count++;
                if (fromDate && toDate) parts.push(`${fromDate} to ${toDate}`);
                else if (fromDate) parts.push(`From: ${fromDate}`);
                else parts.push(`To: ${toDate}`);
            } else if (monthPreset) {
                count++;
                const presetText = $('#month_preset option:selected').text();
                parts.push(presetText);
            } else if (monthFrom || monthTo) {
                count++;
                if (monthFrom && monthTo) parts.push(`${monthFrom} to ${monthTo}`);
                else if (monthFrom) parts.push(`Month: ${monthFrom}`);
                else parts.push(`Month: ${monthTo}`);
            } else if (year) {
                count++;
                parts.push(year);
            }

            if (orderIds.length > 0) {
                count++;
                if (isAllOrdersSelected()) {
                    parts.push('All Orders');
                } else {
                    parts.push(`${orderIds.length} orders`);
                }
            }

            const countEl = $('#hsnFilterCount');
            const toggleEl = $('#hsnFilterToggle');
            if (count > 0) {
                countEl.text(count).removeClass('d-none');
                toggleEl.addClass('has-active');
            } else {
                countEl.addClass('d-none');
                toggleEl.removeClass('has-active');
            }

            const summaryText = parts.length > 0 ? parts.join(' • ') : 'Tap to filter by date, month, year, orders...';
            $('#hsnFilterSummaryText').text(summaryText);
        }

        window.updateHsnFilterSummary = updateFilterSummary;

        $(document).on('click', '#hsnFilterToggle, #hsnFilterSummaryTrigger', function() {
            openHsnFilterSheet();
        });

        $(document).on('click', '#hsnFilterSheetClose, #hsnFilterBackdrop', function() {
            closeHsnFilterSheet();
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') closeHsnFilterSheet();
        });

        $('#hsnFilterClear').on('click', function() {
            $('#from_date, #to_date, #month_from, #month_to, #year_select').val('');
            $('#month_preset').val('');
            $('#hsnOrderList .hsn-order-check').prop('checked', false);
            updateOrderToggleText();
            updateFilterSummary();
            loadHsnList();
        });

        $('#hsnFilterApply').on('click', function() {
            closeHsnFilterSheet();
            updateFilterSummary();
            loadHsnList();
        });

        $(document).on('change', '#from_date, #to_date, #month_from, #month_to, #month_preset, #year_select', function() {
            updateFilterSummary();
        });

        $('#hsnOrderList').on('change', '.hsn-order-check', function () {
            updateFilterSummary();
        });

        $('#hsnOrderSelectAll, #hsnOrderClear').on('click', function () {
            setTimeout(updateFilterSummary, 50);
        });

        placeFilterFields();
        updateFilterSummary();

        if (typeof filterQuery.addEventListener === 'function') {
            filterQuery.addEventListener('change', placeFilterFields);
        } else if (typeof filterQuery.addListener === 'function') {
            filterQuery.addListener(placeFilterFields);
        }
    }
</script>
@endpush
