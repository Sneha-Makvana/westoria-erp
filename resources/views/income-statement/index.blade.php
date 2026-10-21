@extends('layout.app')

@section('title', 'Income Statement')

@section('content')
    <style>
        .download-loader-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            z-index: 1060;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .download-loader-overlay.d-none {
            display: none !important;
        }

        .download-loader-box {
            width: min(460px, 100%);
            background: #fff;
            border-radius: 8px;
            padding: 24px 20px;
            text-align: center;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
        }

        .download-loader-box h4 {
            margin: 0 0 18px 0;
            font-size: 34px;
            color: #2c3e50;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .download-loader-box h4 {
                font-size: 28px;
            }
        }

        /* Mobile Responsive Styles (<768px) */
        @media screen and (max-width: 767.98px) {
            .content {
                padding: 10px 10px 80px !important;
            }

            .is-desktop-header,
            .is-desktop-filter {
                display: none !important;
            }

            /* Mobile Header */
            .is-m-head {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
                padding: 12px;
                margin-bottom: 10px;
            }

            .is-m-head-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-bottom: 10px;
            }

            .is-m-title {
                font-size: 16px;
                font-weight: 700;
                color: #1b2850;
            }

            .is-m-head-actions .btn {
                display: inline-flex;
                align-items: center;
                height: 30px;
                padding: 0 12px !important;
                font-size: 12px !important;
                font-weight: 600;
                border-radius: 8px !important;
                background: #ff9f43 !important;
                border: 1px solid #ff9f43 !important;
                color: #fff !important;
            }

            /* Mobile Tools (Excel, PDF) */
            .is-m-tools {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .is-m-tool {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                height: 34px;
                padding: 0 8px;
                border: 0;
                border-radius: 8px;
                font-size: 12px;
                font-weight: 600;
                text-decoration: none;
                white-space: nowrap;
                cursor: pointer;
            }

            .is-m-tool i {
                font-size: 13px;
            }

            .is-m-tool.is-green {
                background: #e7f6ec;
                color: #198754;
            }

            .is-m-tool.is-red {
                background: #fdecee;
                color: #dc3545;
            }

            /* Mobile Filter Trigger Bar */
            .is-filter-bar {
                display: flex;
                align-items: center;
                gap: 8px;
                background: #fff;
                border-radius: 12px;
                padding: 8px 10px;
                margin-bottom: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            }

            .is-filter-summary {
                flex: 1 1 auto;
                min-width: 0;
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 8px 10px;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                cursor: pointer;
            }

            .is-filter-summary-text {
                font-size: 12.5px;
                color: #475467;
                font-weight: 600;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .is-filter-toggle {
                position: relative;
                flex: 0 0 38px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 38px;
                height: 38px;
                padding: 0;
                border: 0;
                border-radius: 50%;
                background: #f05a6a;
                color: #fff;
                font-size: 15px;
                box-shadow: 0 2px 6px rgba(240, 90, 106, .35);
                cursor: pointer;
            }

            /* Bottom Sheet Filter */
            .is-filter-backdrop {
                display: block;
                position: fixed;
                inset: 0;
                z-index: 1055 !important;
                background: rgba(16, 24, 40, .45);
                opacity: 0;
                pointer-events: none;
                transition: opacity .2s ease;
            }

            .is-filter-sheet {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 1060 !important;
                display: flex;
                flex-direction: column;
                max-height: 85vh;
                background: #fff;
                border-radius: 16px 16px 0 0;
                box-shadow: 0 -8px 24px rgba(16, 24, 40, .18);
                transform: translateY(105%);
                transition: transform .25s ease;
            }

            body.is-filter-open .is-filter-backdrop,
            body.is-sheet-open .is-filter-backdrop {
                opacity: 1;
                pointer-events: auto;
            }

            body.is-filter-open .is-filter-sheet,
            body.is-sheet-open .is-filter-sheet {
                transform: translateY(0);
            }

            body.is-filter-open,
            body.is-sheet-open {
                overflow: hidden !important;
            }

            body.is-filter-open .mobile-bottom-nav,
            body.is-filter-open .floating-calculator-wrapper,
            body.is-sheet-open .mobile-bottom-nav,
            body.is-sheet-open .floating-calculator-wrapper {
                display: none !important;
            }

            .is-filter-sheet form {
                display: flex;
                flex-direction: column;
                flex: 1 1 auto;
                min-height: 0;
                overflow: hidden;
                margin: 0;
            }

            .is-filter-sheet-body {
                flex: 1 1 auto;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }

            .is-filter-sheet-foot {
                flex-shrink: 0;
            }

            .is-filter-sheet-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 16px 18px;
                border-bottom: 1px solid #eef0f4;
                font-size: 16px;
                font-weight: 600;
                color: #1b2850;
            }

            .is-filter-sheet-close {
                width: 32px;
                height: 32px;
                padding: 0;
                border: 0;
                background: transparent;
                color: #64748b;
                font-size: 20px;
                cursor: pointer;
            }

            .is-filter-sheet-body {
                flex: 1 1 auto;
                overflow-y: auto;
                padding: 14px 18px;
            }

            .is-date-presets {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                margin-bottom: 14px;
            }

            .is-preset-pill {
                padding: 5px 12px;
                border-radius: 20px;
                border: 1px solid #e2e8f0;
                background: #f8fafc;
                font-size: 12px;
                font-weight: 600;
                color: #475467;
                cursor: pointer;
            }

            .is-preset-pill:hover,
            .is-preset-pill.active {
                background: #ff9f43;
                border-color: #ff9f43;
                color: #fff;
            }

            .is-filter-sheet-foot {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                padding: 14px 18px calc(14px + env(safe-area-inset-bottom));
                border-top: 1px solid #eef0f4;
            }

            .is-filter-clear,
            .is-filter-apply {
                height: 44px;
                border: 0;
                border-radius: 22px;
                font-size: 14.5px;
                font-weight: 600;
                cursor: pointer;
            }

            .is-filter-clear {
                background: #f1f5f9;
                color: #475467;
            }

            .is-filter-apply {
                background: #ff9f43;
                color: #fff;
            }

            /* Hero KPI Card for Net Operating Income (EBIT) */
            .is-hero-kpi {
                background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
                color: #fff;
                border-radius: 12px;
                padding: 16px;
                margin-bottom: 12px;
                box-shadow: 0 4px 12px rgba(15, 23, 42, .15);
            }

            .is-hero-period {
                font-size: 11.5px;
                color: #94a3b8;
                font-weight: 500;
                margin-bottom: 4px;
            }

            .is-hero-label {
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: #cbd5e1;
                font-weight: 600;
            }

            .is-hero-amount {
                font-size: 24px;
                font-weight: 800;
                margin: 4px 0 12px;
            }

            .is-hero-amount.positive {
                color: #4ade80;
            }

            .is-hero-amount.negative {
                color: #f87171;
            }

            .is-hero-metrics {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                padding-top: 10px;
                border-top: 1px solid rgba(255, 255, 255, .1);
            }

            .is-hero-sublabel {
                font-size: 10.5px;
                color: #94a3b8;
                text-transform: uppercase;
                font-weight: 600;
            }

            .is-hero-subval {
                font-size: 13.5px;
                font-weight: 700;
                color: #f8fafc;
            }

            /* Section Breakdown Cards */
            .is-section-card {
                background: #fff;
                border-radius: 12px;
                border: 1px solid #eef0f4;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .05);
                margin-bottom: 12px;
                overflow: hidden;
            }

            .is-section-header {
                padding: 10px 14px;
                font-size: 13.5px;
                font-weight: 700;
                display: flex;
                align-items: center;
                justify-content: space-between;
                color: #fff;
            }

            .is-section-header.revenue {
                background: #ff9f43;
            }

            .is-section-header.cogs {
                background: #0ea5e9;
            }

            .is-section-header.expenses {
                background: #6366f1;
            }

            .is-item-list {
                padding: 4px 14px;
            }

            .is-item-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 8px 0;
                border-bottom: 1px solid #f1f5f9;
                font-size: 13px;
                color: #334155;
            }

            .is-item-row:last-child {
                border-bottom: 0;
            }

            .is-item-val {
                font-weight: 600;
                color: #1e293b;
            }

            .is-total-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 10px 14px;
                background: #f8fafc;
                border-top: 1px solid #e2e8f0;
                font-size: 13.5px;
                font-weight: 700;
                color: #0f172a;
            }

            .is-highlight-banner {
                border-radius: 10px;
                padding: 12px 14px;
                margin-bottom: 12px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                font-weight: 700;
            }

            .is-highlight-banner.gross-profit {
                background: #dcfce7;
                border: 1px solid #86efac;
                color: #166534;
            }

            .is-highlight-banner.operating-income {
                background: #e0f2fe;
                border: 1px solid #7dd3fc;
                color: #075985;
            }

            .is-highlight-banner .val {
                font-size: 16px;
            }

            .card {
                border-radius: 12px !important;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .05) !important;
            }

            .card-body {
                padding: 12px !important;
            }

            /* Dark Mode Overrides */
            :root[data-theme="dark"] .is-m-head,
            :root[data-theme="dark"] .is-filter-bar,
            :root[data-theme="dark"] .is-filter-sheet,
            :root[data-theme="dark"] .is-section-card {
                background: #1e293b !important;
                border-color: #334155 !important;
            }

            :root[data-theme="dark"] .is-m-title,
            :root[data-theme="dark"] .is-filter-sheet-head,
            :root[data-theme="dark"] .is-item-row,
            :root[data-theme="dark"] .is-item-val,
            :root[data-theme="dark"] .is-total-row {
                color: #f1f5f9 !important;
            }

            :root[data-theme="dark"] .is-filter-summary,
            :root[data-theme="dark"] .is-total-row {
                background: #0f172a !important;
                border-color: #334155 !important;
            }

            :root[data-theme="dark"] .is-filter-summary-text,
            :root[data-theme="dark"] .is-preset-pill {
                color: #94a3b8 !important;
                background: #0f172a !important;
                border-color: #334155 !important;
            }

            :root[data-theme="dark"] .is-item-row {
                border-color: #334155 !important;
            }

            :root[data-theme="dark"] .is-m-tool.is-green { background: #064e3b; color: #6ee7b7; }
            :root[data-theme="dark"] .is-m-tool.is-red { background: #7f1d1d; color: #fca5a5; }

            :root[data-theme="dark"] .is-highlight-banner.gross-profit {
                background: #064e3b !important;
                border-color: #047857 !important;
                color: #a7f3d0 !important;
            }

            :root[data-theme="dark"] .is-highlight-banner.operating-income {
                background: #0c4a6e !important;
                border-color: #0369a1 !important;
                color: #bae6fd !important;
            }

            body.is-sheet-open {
                overflow: hidden !important;
            }
        }
    </style>

    <!-- Mobile Filter Backdrop & Bottom Sheet -->
    <div class="is-filter-backdrop d-md-none" id="isFilterBackdrop"></div>
    <div class="is-filter-sheet d-md-none" id="isFilterSheet" role="dialog" aria-modal="true" aria-labelledby="isFilterSheetTitle">
        <div class="is-filter-sheet-head">
            <span id="isFilterSheetTitle"><i class="fas fa-sliders-h me-2 text-warning"></i>Filter Period</span>
            <button type="button" class="is-filter-sheet-close" id="isFilterSheetClose" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('income-statement.index') }}" method="GET" id="mobileFilterForm">
            <div class="is-filter-sheet-body">
                <label class="form-label mb-2 fw-bold text-muted small">QUICK PRESETS</label>
                <div class="is-date-presets">
                    <button type="button" class="is-preset-pill" data-preset="this-month">This Month</button>
                    <button type="button" class="is-preset-pill" data-preset="last-month">Last Month</button>
                    <button type="button" class="is-preset-pill" data-preset="this-quarter">This Quarter</button>
                    <button type="button" class="is-preset-pill" data-preset="this-year">This Year</button>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Start Date</label>
                    <input type="date" name="start_date" id="m_start_date" class="form-control"
                        value="{{ request('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">End Date</label>
                    <input type="date" name="end_date" id="m_end_date" class="form-control"
                        value="{{ request('end_date', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d')) }}">
                </div>
            </div>
            <div class="is-filter-sheet-foot">
                <button type="button" class="is-filter-clear" id="isFilterClear">Reset</button>
                <button type="submit" class="is-filter-apply" id="isFilterApply">Apply Filter</button>
            </div>
        </form>
    </div>

    <div class="content container-fluid">
        <!-- Mobile Header (<768px) -->
        <div class="is-m-head d-md-none">
            <div class="is-m-head-top">
                <div class="is-m-title">Income Statement</div>
                <div class="is-m-head-actions">
                    <a href="{{ route('auth.dashboard') }}" class="btn btn-sm btn-added">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="is-m-tools">
                <button type="button" class="is-m-tool is-green" data-is-proxy="#exportExcelBtn">
                    <i class="fas fa-file-excel"></i><span>Excel</span>
                </button>
                <button type="button" class="is-m-tool is-red" data-is-proxy="#exportPdfBtn">
                    <i class="fas fa-file-pdf"></i><span>PDF</span>
                </button>
            </div>
        </div>

        <!-- Mobile Filter Bar (<768px) -->
        <div class="is-filter-bar d-md-none">
            <div class="is-filter-summary" id="isFilterSummaryTrigger">
                <i class="far fa-calendar-alt text-warning me-2"></i>
                <span class="is-filter-summary-text">
                    {{ \Carbon\Carbon::parse($data['period']['start'])->format('d M Y') }} - {{ \Carbon\Carbon::parse($data['period']['end'])->format('d M Y') }}
                </span>
            </div>
            <button type="button" class="is-filter-toggle" id="isFilterToggle" aria-label="Filters">
                <i class="fas fa-sliders-h"></i>
            </button>
        </div>

        <!-- Desktop Page Header (>=768px) -->
        <div class="page-header is-desktop-header d-none d-md-flex">
            <div class="row w-100 align-items-center">
                <div class="col">
                    <h3 class="page-title">Income Statement</h3>
                </div>
                <div class="col-auto">
                    <a href="{{ route('auth.dashboard') }}" class="btn btn-added">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <!-- Desktop Search Filter (>=768px) -->
        <div class="card shadow-sm mb-4 is-desktop-filter d-none d-md-block">
            <div class="card-body">
                <form action="{{ route('income-statement.index') }}" method="GET" id="desktopFilterForm">
                    <div class="row g-3 align-items-end">

                        <!-- Start Date -->
                        <div class="col-6 col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="d_start_date" class="form-control"
                                value="{{ request('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}">
                        </div>

                        <!-- End Date -->
                        <div class="col-6 col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" id="d_end_date" class="form-control"
                                value="{{ request('end_date', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d')) }}">
                        </div>

                        <!-- Filter -->
                        <div class="col-12 col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>

                        <!-- 🔹 MOBILE LINE BREAK -->
                        <div class="w-100 d-md-none"></div>

                        <!-- PDF -->
                        <div class="col-6 col-md-2 d-grid">
                            <a id="exportPdfBtn" href="{{ route('income-statement.pdf', request()->only(['start_date', 'end_date'])) }}"
                                class="btn btn-danger w-100 income-download-btn" data-download-type="pdf">
                                <i class="fas fa-file-pdf me-1"></i> PDF
                            </a>
                        </div>

                        <!-- Excel -->
                        <div class="col-6 col-md-2 d-grid">
                            <a id="exportExcelBtn" href="{{ route('income-statement.excel', request()->only(['start_date', 'end_date'])) }}"
                                class="btn btn-success w-100 income-download-btn" data-download-type="excel">
                                <i class="fas fa-file-excel me-1"></i> Excel
                            </a>
                        </div>

                    </div>
                </form>
            </div>
        </div>



        <!-- Income Statement -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @php
                            $fmt = function($amt) use ($data) {
                                $val = number_format((float)($amt ?? 0), 2);
                                return $data['currency']['position'] === 'left' ? $data['currency']['symbol'] . $val : $val . $data['currency']['symbol'];
                            };
                            $ebit = (float)($data['operating_income'] ?? 0);
                            $isPositive = $ebit >= 0;
                        @endphp

                        <!-- Desktop Table View (>=768px) -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-striped custom-table">
                                <thead>
                                    <tr>
                                        <th class="text-left" colspan="2">
                                            For the Period:
                                            {{ \Carbon\Carbon::parse($data['period']['start'])->format('d M Y') }}
                                            to {{ \Carbon\Carbon::parse($data['period']['end'])->format('d M Y') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Revenue Section -->
                                    <tr class="table" style="background: #ff9f43 !important; color:white;">
                                        <td colspan="2"><strong style="color:white;">Revenue</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Sales in Cash</td>
                                        <td class="text-right">
                                            @if ($data['currency']['position'] === 'left')
                                                {{ $data['currency']['symbol'] }}{{ number_format($data['revenue']['sales_cash'], 2) }}
                                            @else
                                                {{ number_format($data['revenue']['sales_cash'], 2) }}{{ $data['currency']['symbol'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Sales in Online</td>
                                        <td class="text-right">
                                            @if ($data['currency']['position'] === 'left')
                                                {{ $data['currency']['symbol'] }}{{ number_format($data['revenue']['sales_online'], 2) }}
                                            @else
                                                {{ number_format($data['revenue']['sales_online'], 2) }}{{ $data['currency']['symbol'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Sales Revenue</td>
                                        <td class="text-right">
                                            @if ($data['currency']['position'] === 'left')
                                                {{ $data['currency']['symbol'] }}{{ number_format($data['revenue']['sales'], 2) }}
                                            @else
                                                {{ number_format($data['revenue']['sales'], 2) }}{{ $data['currency']['symbol'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <td>Total Revenue</td>
                                        <td class="text-right">
                                            @if ($data['currency']['position'] === 'left')
                                                {{ $data['currency']['symbol'] }}{{ number_format($data['revenue']['total_revenue'], 2) }}
                                            @else
                                                {{ number_format($data['revenue']['total_revenue'], 2) }}{{ $data['currency']['symbol'] }}
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Cost of Goods Sold -->
                                    <tr class="table-info">
                                        <td colspan="2"><strong>Cost of Goods Sold</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Purchases in Cash</td>
                                        <td class="text-right">
                                            @if ($data['currency']['position'] === 'left')
                                                {{ $data['currency']['symbol'] }}{{ number_format($data['cost_of_goods_sold']['purchase_cash'], 2) }}
                                            @else
                                                {{ number_format($data['cost_of_goods_sold']['purchases'], 2) }}{{ $data['currency']['symbol'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Purchases in Online</td>
                                        <td class="text-right">
                                            @if ($data['currency']['position'] === 'left')
                                                {{ $data['currency']['symbol'] }}{{ number_format($data['cost_of_goods_sold']['purchase_online'], 2) }}
                                            @else
                                                {{ number_format($data['cost_of_goods_sold']['purchases'], 2) }}{{ $data['currency']['symbol'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Purchases</td>
                                        <td class="text-right">
                                            @if ($data['currency']['position'] === 'left')
                                                {{ $data['currency']['symbol'] }}{{ number_format($data['cost_of_goods_sold']['purchases'], 2) }}
                                            @else
                                                {{ number_format($data['cost_of_goods_sold']['purchases'], 2) }}{{ $data['currency']['symbol'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <td>Total Cost of Goods Sold</td>
                                        <td class="text-right">
                                            @if ($data['currency']['position'] === 'left')
                                                {{ $data['currency']['symbol'] }}{{ number_format($data['cost_of_goods_sold']['total_cogs'], 2) }}
                                            @else
                                                {{ number_format($data['cost_of_goods_sold']['total_cogs'], 2) }}{{ $data['currency']['symbol'] }}
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Gross Profit -->
                                    <tr class="table-success font-weight-bold">
                                        <td>Gross Profit</td>
                                        <td class="text-right">
                                            @if ($data['currency']['position'] === 'left')
                                                {{ $data['currency']['symbol'] }}{{ number_format($data['gross_profit'], 2) }}
                                            @else
                                                {{ number_format($data['gross_profit'], 2) }}{{ $data['currency']['symbol'] }}
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Operating Expenses -->
                                    <tr class="table-info">
                                        <td colspan="2"><strong>Operating Expenses</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Expense in Cash</td>
                                        <td class="text-right">
                                            @if ($data['currency']['position'] === 'left')
                                                {{ $data['currency']['symbol'] }}{{ number_format($data['operating_expenses']['expense_cash'], 2) }}
                                            @else
                                                {{ number_format($data['operating_expenses']['expense_cash'], 2) }}{{ $data['currency']['symbol'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Expense in Bank</td>
                                        <td class="text-right">
                                            @if ($data['currency']['position'] === 'left')
                                                {{ $data['currency']['symbol'] }}{{ number_format($data['operating_expenses']['expense_bank'], 2) }}
                                            @else
                                                {{ number_format($data['operating_expenses']['expense_bank'], 2) }}{{ $data['currency']['symbol'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>General Expenses</td>
                                        <td class="text-right">
                                            @if ($data['currency']['position'] === 'left')
                                                {{ $data['currency']['symbol'] }}{{ number_format($data['operating_expenses']['general_expenses'], 2) }}
                                            @else
                                                {{ number_format($data['operating_expenses']['general_expenses'], 2) }}{{ $data['currency']['symbol'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <td>Total Operating Expenses</td>
                                        <td class="text-right">
                                            @if ($data['currency']['position'] === 'left')
                                                {{ $data['currency']['symbol'] }}{{ number_format($data['operating_expenses']['total_operating_expenses'], 2) }}
                                            @else
                                                {{ number_format($data['operating_expenses']['total_operating_expenses'], 2) }}{{ $data['currency']['symbol'] }}
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Operating Income -->
                                    <tr class="table-primary font-weight-bold">
                                        <td>Operating Income (EBIT)</td>
                                        <td class="text-right">
                                            @if ($data['currency']['position'] === 'left')
                                                {{ $data['currency']['symbol'] }}{{ number_format($data['operating_income'], 2) }}
                                            @else
                                                {{ number_format($data['operating_income'], 2) }}{{ $data['currency']['symbol'] }}
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Statement View (<768px) -->
                        <div class="is-m-statement d-md-none">
                            <!-- Hero KPI Card -->
                            <div class="is-hero-kpi">
                                <div class="is-hero-period">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ \Carbon\Carbon::parse($data['period']['start'])->format('d M Y') }} - {{ \Carbon\Carbon::parse($data['period']['end'])->format('d M Y') }}
                                </div>
                                <div class="is-hero-label">Operating Income (EBIT)</div>
                                <div class="is-hero-amount {{ $isPositive ? 'positive' : 'negative' }}">
                                    {{ $fmt($ebit) }}
                                </div>
                                <div class="is-hero-metrics">
                                    <div>
                                        <span class="is-hero-sublabel">Total Revenue</span>
                                        <span class="is-hero-subval">{{ $fmt($data['revenue']['total_revenue']) }}</span>
                                    </div>
                                    <div>
                                        <span class="is-hero-sublabel">Gross Profit</span>
                                        <span class="is-hero-subval">{{ $fmt($data['gross_profit']) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Revenue Card -->
                            <div class="is-section-card">
                                <div class="is-section-header revenue">
                                    <span><i class="fas fa-hand-holding-usd me-1"></i> Revenue</span>
                                    <span>{{ $fmt($data['revenue']['total_revenue']) }}</span>
                                </div>
                                <div class="is-item-list">
                                    <div class="is-item-row">
                                        <span>Sales in Cash</span>
                                        <span class="is-item-val">{{ $fmt($data['revenue']['sales_cash']) }}</span>
                                    </div>
                                    <div class="is-item-row">
                                        <span>Sales in Online</span>
                                        <span class="is-item-val">{{ $fmt($data['revenue']['sales_online']) }}</span>
                                    </div>
                                    <div class="is-item-row">
                                        <span>Sales Revenue</span>
                                        <span class="is-item-val">{{ $fmt($data['revenue']['sales']) }}</span>
                                    </div>
                                </div>
                                <div class="is-total-row">
                                    <span>Total Revenue</span>
                                    <span class="text-warning">{{ $fmt($data['revenue']['total_revenue']) }}</span>
                                </div>
                            </div>

                            <!-- Cost of Goods Sold Card -->
                            <div class="is-section-card">
                                <div class="is-section-header cogs">
                                    <span><i class="fas fa-shopping-cart me-1"></i> Cost of Goods Sold</span>
                                    <span>{{ $fmt($data['cost_of_goods_sold']['total_cogs']) }}</span>
                                </div>
                                <div class="is-item-list">
                                    <div class="is-item-row">
                                        <span>Purchases in Cash</span>
                                        <span class="is-item-val">{{ $fmt($data['cost_of_goods_sold']['purchase_cash']) }}</span>
                                    </div>
                                    <div class="is-item-row">
                                        <span>Purchases in Online</span>
                                        <span class="is-item-val">{{ $fmt($data['cost_of_goods_sold']['purchase_online']) }}</span>
                                    </div>
                                    <div class="is-item-row">
                                        <span>Purchases</span>
                                        <span class="is-item-val">{{ $fmt($data['cost_of_goods_sold']['purchases']) }}</span>
                                    </div>
                                </div>
                                <div class="is-total-row">
                                    <span>Total Cost of Goods Sold</span>
                                    <span class="text-info">{{ $fmt($data['cost_of_goods_sold']['total_cogs']) }}</span>
                                </div>
                            </div>

                            <!-- Gross Profit Banner -->
                            <div class="is-highlight-banner gross-profit">
                                <span><i class="fas fa-chart-line me-1"></i> Gross Profit</span>
                                <span class="val">{{ $fmt($data['gross_profit']) }}</span>
                            </div>

                            <!-- Operating Expenses Card -->
                            <div class="is-section-card">
                                <div class="is-section-header expenses">
                                    <span><i class="fas fa-file-invoice-dollar me-1"></i> Operating Expenses</span>
                                    <span>{{ $fmt($data['operating_expenses']['total_operating_expenses']) }}</span>
                                </div>
                                <div class="is-item-list">
                                    <div class="is-item-row">
                                        <span>Expense in Cash</span>
                                        <span class="is-item-val">{{ $fmt($data['operating_expenses']['expense_cash']) }}</span>
                                    </div>
                                    <div class="is-item-row">
                                        <span>Expense in Bank</span>
                                        <span class="is-item-val">{{ $fmt($data['operating_expenses']['expense_bank']) }}</span>
                                    </div>
                                    <div class="is-item-row">
                                        <span>General Expenses</span>
                                        <span class="is-item-val">{{ $fmt($data['operating_expenses']['general_expenses']) }}</span>
                                    </div>
                                </div>
                                <div class="is-total-row">
                                    <span>Total Operating Expenses</span>
                                    <span style="color: #6366f1;">{{ $fmt($data['operating_expenses']['total_operating_expenses']) }}</span>
                                </div>
                            </div>

                            <!-- Operating Income (EBIT) Final Banner -->
                            <div class="is-highlight-banner operating-income">
                                <span><i class="fas fa-coins me-1"></i> Operating Income (EBIT)</span>
                                <span class="val {{ $isPositive ? 'text-success' : 'text-danger' }}">{{ $fmt($data['operating_income']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="downloadLoaderOverlay" class="download-loader-overlay d-none" aria-live="polite" aria-busy="true">
        <div class="download-loader-box">
            <h4 id="downloadLoaderText">Generating PDF...</h4>
            <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
        </div>
    </div>
@endsection
@push('js')
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script>
        $(document).ready(function() {
            const authToken = localStorage.getItem("authToken");
            var selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            // console.log(selectedSubAdminId);

            // Dynamically get start and end of the current month
            let now = new Date();
            let startDate = new Date(now.getFullYear(), now.getMonth(), 1); // first day of month
            let endDate = new Date(now.getFullYear(), now.getMonth() + 1, 0); // last day of month

            // Format dates as YYYY-MM-DD
            const formatDate = (date) => {
                let month = (date.getMonth() + 1).toString().padStart(2, '0');
                let day = date.getDate().toString().padStart(2, '0');
                return `${date.getFullYear()}-${month}-${day}`;
            };

            startDate = formatDate(startDate);
            endDate = formatDate(endDate);

            // Make the API call immediately
            $.ajax({
                url: '/api/income-statement', // Your API route
                method: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    selectedSubAdminId: selectedSubAdminId,
                },
                headers: {
                    'Authorization': 'Bearer ' + authToken // if using auth
                },
                success: function(response) {
                    if (response.status) {
                        // console.log('Income Statement Data:', response.data);
                    } else {
                        console.error('Failed: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error: ' + xhr.responseText);
                }
            });

            const $downloadLoader = $("#downloadLoaderOverlay");
            const $downloadLoaderText = $("#downloadLoaderText");
            const $downloadButtons = $(".income-download-btn");

            function toggleDownloadLoader(isLoading, message) {
                if (isLoading) {
                    $downloadLoaderText.text(message || "Generating report...");
                    $downloadLoader.removeClass("d-none");
                    $downloadButtons.addClass("disabled").attr("aria-disabled", "true");
                } else {
                    $downloadLoader.addClass("d-none");
                    $downloadButtons.removeClass("disabled").removeAttr("aria-disabled");
                }
            }

            function getFileNameFromDisposition(contentDisposition, fallbackName) {
                if (!contentDisposition) return fallbackName;

                const utf8Match = contentDisposition.match(/filename\\*=UTF-8''([^;]+)/i);
                if (utf8Match && utf8Match[1]) {
                    return decodeURIComponent(utf8Match[1]);
                }

                const asciiMatch = contentDisposition.match(/filename=\"?([^\";]+)\"?/i);
                if (asciiMatch && asciiMatch[1]) {
                    return asciiMatch[1];
                }

                return fallbackName;
            }

            async function downloadStatement(url, type) {
                const isPdf = type === "pdf";
                const fallbackName = isPdf ? "income-statement.pdf" : "income-statement.xlsx";
                toggleDownloadLoader(true, isPdf ? "Generating PDF..." : "Generating Excel...");

                try {
                    const response = await fetch(url, {
                        method: "GET",
                        credentials: "same-origin",
                    });

                    if (!response.ok) {
                        throw new Error("Download request failed");
                    }

                    const blob = await response.blob();
                    const contentDisposition = response.headers.get("content-disposition");
                    const fileName = getFileNameFromDisposition(contentDisposition, fallbackName);

                    const downloadUrl = window.URL.createObjectURL(blob);
                    const link = document.createElement("a");
                    link.href = downloadUrl;
                    link.download = fileName;
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    window.URL.revokeObjectURL(downloadUrl);
                } catch (error) {
                    Swal.fire({
                        icon: "error",
                        title: "Download Failed",
                        text: "Unable to generate the file right now. Please try again.",
                        confirmButtonColor: "#ff9f43",
                    });
                } finally {
                    toggleDownloadLoader(false);
                }
            }

            $downloadButtons.on("click", function(e) {
                e.preventDefault();
                if ($(this).hasClass("disabled")) {
                    return;
                }
                const url = $(this).attr("href");
                const type = $(this).data("download-type");
                downloadStatement(url, type);
            });

            // --- Mobile Filter Bottom Sheet & Interactions ---
            const $filterSheet = $("#isFilterSheet");
            const $filterBackdrop = $("#isFilterBackdrop");

            function openFilterSheet() {
                $filterSheet.addClass("is-open");
                $filterBackdrop.addClass("is-open");
                $("body").addClass("is-sheet-open is-filter-open");
            }

            function closeFilterSheet() {
                $filterSheet.removeClass("is-open");
                $filterBackdrop.removeClass("is-open");
                $("body").removeClass("is-sheet-open is-filter-open");
            }

            $("#isFilterToggle, #isFilterSummaryTrigger").on("click", function(e) {
                e.preventDefault();
                openFilterSheet();
            });

            $("#isFilterSheetClose, #isFilterBackdrop").on("click", function(e) {
                e.preventDefault();
                closeFilterSheet();
            });

            // Format JS Date as YYYY-MM-DD
            function toDateInputFormat(d) {
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // Quick Preset Date Ranges
            $(".is-preset-pill").on("click", function(e) {
                e.preventDefault();
                $(".is-preset-pill").removeClass("active");
                $(this).addClass("active");

                const preset = $(this).data("preset");
                const today = new Date();
                let sDate, eDate;

                if (preset === "this-month") {
                    sDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    eDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                } else if (preset === "last-month") {
                    sDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    eDate = new Date(today.getFullYear(), today.getMonth(), 0);
                } else if (preset === "this-quarter") {
                    const quarter = Math.floor(today.getMonth() / 3);
                    sDate = new Date(today.getFullYear(), quarter * 3, 1);
                    eDate = new Date(today.getFullYear(), quarter * 3 + 3, 0);
                } else if (preset === "this-year") {
                    sDate = new Date(today.getFullYear(), 0, 1);
                    eDate = new Date(today.getFullYear(), 11, 31);
                }

                if (sDate && eDate) {
                    $("#m_start_date").val(toDateInputFormat(sDate));
                    $("#m_end_date").val(toDateInputFormat(eDate));
                }
            });

            // Reset Filter
            $("#isFilterClear").on("click", function(e) {
                e.preventDefault();
                const today = new Date();
                const sDate = new Date(today.getFullYear(), today.getMonth(), 1);
                const eDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);

                $("#m_start_date").val(toDateInputFormat(sDate));
                $("#m_end_date").val(toDateInputFormat(eDate));

                $(".is-preset-pill").removeClass("active");
                $('.is-preset-pill[data-preset="this-month"]').addClass("active");
            });

            // Proxy Mobile Tool Actions (Export Excel / PDF)
            $(document).on("click", ".is-m-tool[data-is-proxy]", function(e) {
                e.preventDefault();
                const target = $(this).data("is-proxy");
                if (target && $(target).length) {
                    $(target)[0].click();
                }
            });

            // Highlight active preset on load if matching
            (function() {
                const sVal = $("#m_start_date").val();
                const eVal = $("#m_end_date").val();
                const today = new Date();
                const tmStart = toDateInputFormat(new Date(today.getFullYear(), today.getMonth(), 1));
                const tmEnd = toDateInputFormat(new Date(today.getFullYear(), today.getMonth() + 1, 0));
                if (sVal === tmStart && eVal === tmEnd) {
                    $('.is-preset-pill[data-preset="this-month"]').addClass("active");
                }
            })();
        });
    </script>
@endpush
