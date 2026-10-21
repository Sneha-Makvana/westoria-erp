@extends('layout.app')

@section('title', 'Balance Sheet')

@section('content')
    @php
        $sym = $currency_symbol ?? '₹';
        $pos = $currency_position ?? 'left';
        function money_fmt($v, $sym, $pos)
        {
            $v = number_format((float) $v, 2);
            return $pos === 'left' ? $sym . $v : $v . $sym;
        }
    @endphp

    <style>
        .bs-container {
            background: #fff;
            padding: 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            max-width: 900px;
            margin: auto;
            color: #333;
        }

        .bs-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .bs-header h3 {
            margin: 0 0 5px;
            font-weight: bold;
            font-size: 20px;
        }

        .bs-header p {
            margin: 2px 0;
            font-size: 14px;
            color: #444;
        }

        .bs-title {
            margin-top: 15px;
            font-weight: bold;
            font-size: 18px;
            text-decoration: underline;
        }

        .bs-period {
            font-size: 13px;
            color: #555;
            margin-top: 5px;
        }

        .bs-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .bs-table th, .bs-table td {
            padding: 8px 10px;
            vertical-align: top;
        }

        .bs-table th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            text-align: left;
            font-weight: bold;
        }

        .col-divider {
            border-left: 1px solid #000;
        }
        
        .header-date {
            text-align: right;
            font-weight: normal;
            font-size: 13px;
        }

        .group-title {
            font-weight: bold;
            padding-top: 15px;
            padding-bottom: 5px;
        }

        .group-total {
            font-weight: bold;
            text-align: right;
            padding-top: 15px;
            padding-bottom: 5px;
        }

        .account-row td {
            padding: 2px 10px;
            font-style: italic;
            font-size: 13px;
        }
        
        .account-name {
            padding-left: 20px !important;
        }

        .account-amount {
            text-align: right;
        }
        
        .bottom-border {
            border-bottom: 1px solid #ccc;
        }
        
        .total-row td {
            border-top: 1px solid #000;
            border-bottom: 2px solid #000;
            font-weight: bold;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        
        .text-right {
            text-align: right;
        }

        .table-half {
            width: 50%;
        }

        .bs-report-wrap {
            width: 100%;
            overflow-x: hidden;
        }

        /* Mobile & Tablet Devices (< 1200px: Mobile phones, iPad Mini, iPad Air, iPad Pro) */
        @media screen and (max-width: 1199.98px) {
            .content {
                padding: 10px 10px 80px !important;
            }

            .balance-sheet-page .page-header {
                display: none !important;
            }

            .card {
                border-radius: 12px !important;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .05) !important;
            }

            .card-body {
                padding: 12px !important;
            }

            /* Mobile Header */
            .bs-m-head {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
                padding: 12px;
                margin-bottom: 10px;
            }

            .bs-m-head-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-bottom: 10px;
            }

            .bs-m-title {
                font-size: 16px;
                font-weight: 700;
                color: #1b2850;
            }

            .bs-m-head-actions .btn {
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

            .bs-m-tools {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .bs-m-tool {
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
                cursor: pointer;
            }

            .bs-m-tool.is-green { background: #e7f6ec; color: #198754; }
            .bs-m-tool.is-red { background: #fdecee; color: #dc3545; }

            /* Filter Bar */
            .bs-filter-bar {
                display: flex;
                align-items: center;
                gap: 8px;
                background: #fff;
                border-radius: 12px;
                padding: 8px 10px;
                margin-bottom: 10px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            }

            .bs-filter-summary {
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

            .bs-filter-summary-text {
                font-size: 12.5px;
                color: #475467;
                font-weight: 500;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .bs-filter-toggle {
                flex: 0 0 38px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 38px;
                height: 38px;
                padding: 0;
                border: 0;
                border-radius: 50%;
                background: #ff9f43;
                color: #fff;
                font-size: 14px;
                box-shadow: 0 2px 6px rgba(255, 159, 67, .35);
                cursor: pointer;
            }

            /* Bottom Sheet Filter */
            .bs-filter-backdrop {
                display: block;
                position: fixed;
                inset: 0;
                z-index: 1055 !important;
                background: rgba(16, 24, 40, .45);
                opacity: 0;
                pointer-events: none;
                transition: opacity .2s ease;
            }

            .bs-filter-backdrop.is-open {
                opacity: 1;
                pointer-events: auto;
            }

            .bs-filter-sheet {
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

            .bs-filter-sheet.is-open {
                transform: translateY(0);
            }

            body.bs-sheet-open,
            body.bs-filter-open {
                overflow: hidden !important;
            }

            body.bs-sheet-open .mobile-bottom-nav,
            body.bs-sheet-open .floating-calculator-wrapper,
            body.bs-filter-open .mobile-bottom-nav,
            body.bs-filter-open .floating-calculator-wrapper {
                display: none !important;
            }

            .bs-filter-sheet-head {
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 16px 18px;
                border-bottom: 1px solid #eef0f4;
                font-size: 15px;
                font-weight: 700;
                color: #1b2850;
            }

            .bs-filter-sheet-close {
                border: 0;
                background: transparent;
                color: #64748b;
                font-size: 18px;
                cursor: pointer;
            }

            .bs-filter-sheet form {
                display: flex;
                flex-direction: column;
                flex: 1 1 auto;
                min-height: 0;
                overflow: hidden;
                margin: 0;
            }

            .bs-filter-sheet-body {
                flex: 1 1 auto;
                padding: 16px 18px;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }

            .bs-date-presets {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
                margin-bottom: 14px;
            }

            .bs-preset-pill {
                padding: 7px 10px;
                border: 1px solid #e2e8f0;
                background: #f8fafc;
                border-radius: 8px;
                font-size: 12px;
                font-weight: 600;
                color: #475467;
                cursor: pointer;
                text-align: center;
            }

            .bs-preset-pill.active {
                background: #fff7ef;
                border-color: #ff9f43;
                color: #d97706;
            }

            .bs-filter-sheet-foot {
                flex-shrink: 0;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                padding: 14px 18px calc(18px + env(safe-area-inset-bottom));
                border-top: 1px solid #eef0f4;
                background: #fff;
            }

            .bs-filter-clear,
            .bs-filter-apply {
                height: 44px;
                border: 0;
                border-radius: 10px;
                font-size: 13.5px;
                font-weight: 600;
                cursor: pointer;
            }

            .bs-filter-clear { background: #f1f5f9; color: #475467; }
            .bs-filter-apply { background: #ff9f43; color: #fff; }

            /* Hero KPI Card */
            .bs-hero-card {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 14px;
                margin-bottom: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .05);
            }

            .bs-company-info {
                text-align: center;
                margin-bottom: 12px;
                padding-bottom: 10px;
                border-bottom: 1px dashed #e2e8f0;
            }

            .bs-comp-name {
                font-size: 15px;
                font-weight: 700;
                color: #1b2850;
                margin-bottom: 2px;
            }

            .bs-comp-sub {
                font-size: 11.5px;
                color: #64748b;
                margin-bottom: 2px;
            }

            .bs-date-badge {
                display: inline-block;
                background: #fff7ef;
                color: #d97706;
                border: 1px solid #fed7aa;
                border-radius: 20px;
                padding: 3px 10px;
                font-size: 11px;
                font-weight: 600;
                margin-top: 6px;
            }

            .bs-kpi-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                margin-bottom: 10px;
            }

            .bs-kpi-tile {
                border-radius: 10px;
                padding: 10px 8px;
                text-align: center;
            }

            .bs-kpi-tile.is-liab {
                background: #eff6ff;
                border: 1px solid #bfdbfe;
            }

            .bs-kpi-tile.is-assets {
                background: #f0fdf4;
                border: 1px solid #bbf7d0;
            }

            .bs-kpi-label {
                display: block;
                font-size: 11px;
                font-weight: 600;
                color: #64748b;
            }

            .bs-kpi-val {
                display: block;
                font-size: 14px;
                font-weight: 800;
                margin-top: 2px;
            }

            .bs-kpi-tile.is-liab .bs-kpi-val { color: #1d4ed8; }
            .bs-kpi-tile.is-assets .bs-kpi-val { color: #15803d; }

            .bs-balance-status {
                text-align: center;
                padding: 7px 10px;
                border-radius: 8px;
                font-size: 12px;
                font-weight: 600;
            }

            .bs-balance-status.is-balanced {
                background: #dcfce7;
                color: #166534;
            }

            .bs-balance-status.is-unbalanced {
                background: #fef3c7;
                color: #92400e;
            }

            /* Section Cards */
            .bs-section-card {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                overflow: hidden;
                margin-bottom: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .05);
            }

            .bs-section-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 10px 14px;
                font-weight: 700;
                font-size: 13.5px;
                color: #fff;
            }

            .bs-section-header.liabilities {
                background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            }

            .bs-section-header.assets {
                background: linear-gradient(135deg, #065f46, #10b981);
            }

            .bs-group-block {
                border-bottom: 1px solid #f1f5f9;
            }

            .bs-group-block:last-child {
                border-bottom: none;
            }

            .bs-group-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 8px 14px;
                background: #f8fafc;
                font-size: 12.5px;
                font-weight: 700;
                color: #334155;
            }

            .bs-account-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 6px 14px 6px 24px;
                font-size: 12px;
                color: #475467;
                border-top: 1px dashed #f1f5f9;
            }

            .bs-account-row:first-child {
                border-top: none;
            }

            .bs-acc-amt {
                font-weight: 600;
                color: #1e293b;
            }

            .bs-section-total {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 10px 14px;
                background: #f8fafc;
                border-top: 2px solid #e2e8f0;
                font-size: 13px;
                font-weight: 700;
                color: #1e293b;
            }

            .bs-total-val {
                font-size: 14px;
                font-weight: 800;
            }

            /* Tablet Layout: iPad Mini & iPad Pro (768px - 1199.98px) */
            @media screen and (min-width: 768px) and (max-width: 1199.98px) {
                .content {
                    padding: 16px 16px 80px !important;
                }

                .bs-filter-sheet {
                    max-width: 580px;
                    left: 50% !important;
                    right: auto !important;
                    transform: translate(-50%, 105%);
                    border-radius: 16px 16px 0 0;
                    width: 100%;
                }

                body.bs-sheet-open .bs-filter-sheet {
                    transform: translate(-50%, 0) !important;
                }

                .bs-m-cards {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 16px;
                }

                .bs-section-card {
                    margin-bottom: 0;
                }
            }

            /* Dark Mode Overrides */
            :root[data-theme="dark"] .bs-m-head,
            :root[data-theme="dark"] .bs-filter-bar,
            :root[data-theme="dark"] .bs-filter-sheet,
            :root[data-theme="dark"] .bs-hero-card,
            :root[data-theme="dark"] .bs-section-card,
            :root[data-theme="dark"] .bs-filter-sheet-foot {
                background: #1e293b !important;
                border-color: #334155 !important;
            }

            :root[data-theme="dark"] .bs-m-title,
            :root[data-theme="dark"] .bs-filter-sheet-head,
            :root[data-theme="dark"] .bs-comp-name,
            :root[data-theme="dark"] .bs-group-header,
            :root[data-theme="dark"] .bs-acc-amt,
            :root[data-theme="dark"] .bs-section-total {
                color: #f1f5f9 !important;
            }

            :root[data-theme="dark"] .bs-filter-summary,
            :root[data-theme="dark"] .bs-group-header,
            :root[data-theme="dark"] .bs-section-total,
            :root[data-theme="dark"] .bs-preset-pill {
                background: #0f172a !important;
                border-color: #334155 !important;
                color: #94a3b8 !important;
            }

            :root[data-theme="dark"] .bs-account-row {
                border-color: #334155 !important;
                color: #cbd5e1 !important;
            }

            :root[data-theme="dark"] .bs-kpi-tile.is-liab {
                background: #172554 !important;
                border-color: #1e40af !important;
            }

            :root[data-theme="dark"] .bs-kpi-tile.is-assets {
                background: #052e16 !important;
                border-color: #166534 !important;
            }

            :root[data-theme="dark"] .bs-kpi-tile.is-liab .bs-kpi-val { color: #93c5fd !important; }
            :root[data-theme="dark"] .bs-kpi-tile.is-assets .bs-kpi-val { color: #86efac !important; }
        }
    </style>

    <!-- Mobile Filter Backdrop & Bottom Sheet (<1200px) -->
    <div class="bs-filter-backdrop d-xl-none" id="bsFilterBackdrop"></div>
    <div class="bs-filter-sheet d-xl-none" id="bsFilterSheet" role="dialog" aria-modal="true" aria-labelledby="bsFilterSheetTitle">
        <div class="bs-filter-sheet-head">
            <span id="bsFilterSheetTitle"><i class="fas fa-sliders-h me-2 text-warning"></i>Filter Period</span>
            <button type="button" class="bs-filter-sheet-close" id="bsFilterSheetClose" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="GET" action="{{ url()->current() }}" id="mobileFilterForm">
            <div class="bs-filter-sheet-body">
                <label class="form-label mb-2 fw-bold text-muted small">QUICK PRESETS</label>
                <div class="bs-date-presets">
                    <button type="button" class="bs-preset-pill" data-preset="this-month">This Month</button>
                    <button type="button" class="bs-preset-pill" data-preset="last-month">Last Month</button>
                    <button type="button" class="bs-preset-pill" data-preset="this-quarter">This Quarter</button>
                    <button type="button" class="bs-preset-pill" data-preset="this-year">This Year</button>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Start Date</label>
                    <input type="date" name="start_date" id="m_start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">End Date</label>
                    <input type="date" name="end_date" id="m_end_date" class="form-control" value="{{ $endDate }}">
                </div>
            </div>
            <div class="bs-filter-sheet-foot">
                <button type="button" class="bs-filter-clear" id="bsFilterClear">Reset</button>
                <button type="submit" class="bs-filter-apply" id="bsFilterApply">Apply Filter</button>
            </div>
        </form>
    </div>

    <div class="content balance-sheet-page">
        <!-- Mobile Header (<1200px) -->
        <div class="bs-m-head d-xl-none">
            <div class="bs-m-head-top">
                <div class="bs-m-title">Balance Sheet</div>
                <div class="bs-m-head-actions">
                    <a href="{{ route('auth.dashboard') }}" class="btn btn-sm btn-added">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="bs-m-tools">
                <button type="button" class="bs-m-tool is-green" onclick="downloadReport('excel')">
                    <i class="fas fa-file-excel"></i><span>Excel</span>
                </button>
                <button type="button" class="bs-m-tool is-red" onclick="downloadReport('pdf')">
                    <i class="fas fa-file-pdf"></i><span>PDF</span>
                </button>
            </div>
        </div>

        <!-- Mobile Filter Bar (<1200px) -->
        <div class="bs-filter-bar d-xl-none">
            <div class="bs-filter-summary" id="bsFilterSummaryTrigger">
                <i class="far fa-calendar-alt text-warning me-2"></i>
                <span class="bs-filter-summary-text">
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </span>
            </div>
            <button type="button" class="bs-filter-toggle" id="bsFilterToggle" aria-label="Filters">
                <i class="fas fa-sliders-h"></i>
            </button>
        </div>

        <!-- Desktop Page Header (>=1200px) -->
        <div class="page-header d-none d-xl-flex">
            <div class="page-title">
                <h4>Balance Sheet</h4>
            </div>
            <div class="page-btn">
                <form method="GET" action="{{ url()->current() }}" class="d-inline-block">
                    <div class="d-flex align-items-center justify-content-end" style="gap: 10px;">
                        <div class="d-flex align-items-center" style="gap: 5px;">
                            <label for="start_date" class="form-label mb-0" style="font-size: 13px; font-weight: bold; white-space: nowrap;">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="{{ $startDate }}" onchange="this.form.submit()">
                        </div>
                        <div class="d-flex align-items-center" style="gap: 5px;">
                            <label for="end_date" class="form-label mb-0" style="font-size: 13px; font-weight: bold; white-space: nowrap;">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{ $endDate }}" onchange="this.form.submit()">
                        </div>
                        <button type="button" onclick="downloadReport('pdf')" class="btn btn-danger btn-sm" title="Download PDF">PDF</button>
                        <button type="button" onclick="downloadReport('excel')" class="btn btn-success btn-sm" title="Download Excel">Excel</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Desktop Report Table (>=1200px) -->
                <div class="bs-report-wrap d-none d-xl-block">
                    <div class="bs-header">
                        <h3>{{ $companyName ?? 'Company Name' }}</h3>
                        <p>{{ $companyAddress ?? 'Address' }}</p>
                        <p>Contact : {{ $companyPhone ?? '' }}</p>
                        
                        <div class="bs-title">Balance Sheet</div>
                        <div class="bs-period">
                            From {{ \Carbon\Carbon::parse($startDate)->format('d-M-y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d-M-y') }}
                        </div>
                    </div>

                    <table class="bs-table">
                        <thead>
                            <tr>
                                <th class="table-half">
                                    L i a b i l i t i e s
                                    <span class="float-end fw-normal" style="float: right; font-size: 13px;">as at {{ date('d-M-y') }}</span>
                                </th>
                                <th class="table-half col-divider">
                                    A s s e t s
                                    <span class="float-end fw-normal" style="float: right; font-size: 13px;">as at {{ date('d-M-y') }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <!-- Liabilities Column -->
                                <td style="padding: 0;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        @foreach($liabilityGroups as $groupName => $accounts)
                                            @php 
                                                $groupTotal = array_sum($accounts);
                                            @endphp
                                            <tr>
                                                <td class="group-title">{{ $groupName }}</td>
                                                <td class="group-total">{{ money_fmt($groupTotal, $sym, $pos) }}</td>
                                            </tr>
                                            @foreach($accounts as $accName => $accAmt)
                                                <tr class="account-row">
                                                    <td class="account-name">{{ $accName }}</td>
                                                    <td class="account-amount">{{ money_fmt($accAmt, $sym, $pos) }}</td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </table>
                                </td>

                                <!-- Assets Column -->
                                <td class="col-divider" style="padding: 0;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        @foreach($assetGroups as $groupName => $accounts)
                                            @php 
                                                $groupTotal = array_sum($accounts);
                                            @endphp
                                            <tr>
                                                <td class="group-title">{{ $groupName }}</td>
                                                <td class="group-total">{{ money_fmt($groupTotal, $sym, $pos) }}</td>
                                            </tr>
                                            @foreach($accounts as $accName => $accAmt)
                                                <tr class="account-row">
                                                    <td class="account-name">{{ $accName }}</td>
                                                    <td class="account-amount">{{ money_fmt($accAmt, $sym, $pos) }}</td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </table>
                                </td>
                            </tr>
                            
                            <!-- Grand Total Row -->
                            <tr class="total-row">
                                <td style="padding: 0;">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td>T o t a l</td>
                                            <td class="text-right">{{ money_fmt($totals['liabilities_equity'] ?? 0, $sym, $pos) }}</td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="col-divider" style="padding: 0;">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td>T o t a l</td>
                                            <td class="text-right">{{ money_fmt($totals['assets'] ?? 0, $sym, $pos) }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile & Tablet Card Layout (<1200px) -->
                <div class="bs-m-statement d-xl-none">
                    <!-- Hero KPI Card -->
                    <div class="bs-hero-card">
                        <div class="bs-company-info">
                            <h5 class="bs-comp-name">{{ $companyName ?? 'Company Name' }}</h5>
                            @if(!empty($companyAddress))
                                <p class="bs-comp-sub">{{ $companyAddress }}</p>
                            @endif
                            @if(!empty($companyPhone))
                                <p class="bs-comp-sub"><i class="fas fa-phone-alt me-1"></i>{{ $companyPhone }}</p>
                            @endif
                            <div class="bs-date-badge">
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                            </div>
                        </div>

                        <div class="bs-kpi-grid">
                            <div class="bs-kpi-tile is-liab">
                                <span class="bs-kpi-label">Liabilities & Equity</span>
                                <span class="bs-kpi-val">{{ money_fmt($totals['liabilities_equity'] ?? 0, $sym, $pos) }}</span>
                            </div>
                            <div class="bs-kpi-tile is-assets">
                                <span class="bs-kpi-label">Total Assets</span>
                                <span class="bs-kpi-val">{{ money_fmt($totals['assets'] ?? 0, $sym, $pos) }}</span>
                            </div>
                        </div>

                        @php
                            $diff = abs(($totals['liabilities_equity'] ?? 0) - ($totals['assets'] ?? 0));
                            $isBalanced = $diff < 0.01;
                        @endphp
                        <div class="bs-balance-status {{ $isBalanced ? 'is-balanced' : 'is-unbalanced' }}">
                            @if($isBalanced)
                                <i class="fas fa-check-circle me-1"></i> Balance Sheet is Balanced
                            @else
                                <i class="fas fa-exclamation-triangle me-1"></i> Difference: {{ money_fmt($diff, $sym, $pos) }}
                            @endif
                        </div>
                    </div>

                    <!-- Breakdown Cards Grid (1 col on mobile, 2 col on tablet) -->
                    <div class="bs-m-cards">
                        <!-- Liabilities & Equity Section Card -->
                        <div class="bs-section-card">
                            <div class="bs-section-header liabilities">
                                <span><i class="fas fa-balance-scale-left me-1"></i> Liabilities & Equity</span>
                                <span>{{ money_fmt($totals['liabilities_equity'] ?? 0, $sym, $pos) }}</span>
                            </div>
                            <div class="bs-groups-body">
                                @forelse($liabilityGroups as $groupName => $accounts)
                                    @php $groupTotal = array_sum($accounts); @endphp
                                    <div class="bs-group-block">
                                        <div class="bs-group-header">
                                            <span class="bs-group-name">{{ $groupName }}</span>
                                            <span class="bs-group-sum text-primary">{{ money_fmt($groupTotal, $sym, $pos) }}</span>
                                        </div>
                                        @if(count($accounts) > 0)
                                            <div class="bs-account-list">
                                                @foreach($accounts as $accName => $accAmt)
                                                    <div class="bs-account-row">
                                                        <span class="bs-acc-name">{{ $accName }}</span>
                                                        <span class="bs-acc-amt">{{ money_fmt($accAmt, $sym, $pos) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="p-3 text-center text-muted">No liabilities recorded</div>
                                @endforelse
                            </div>
                            <div class="bs-section-total">
                                <span>Total Liabilities & Equity</span>
                                <span class="bs-total-val text-primary">{{ money_fmt($totals['liabilities_equity'] ?? 0, $sym, $pos) }}</span>
                            </div>
                        </div>

                        <!-- Assets Section Card -->
                        <div class="bs-section-card">
                            <div class="bs-section-header assets">
                                <span><i class="fas fa-balance-scale-right me-1"></i> Assets</span>
                                <span>{{ money_fmt($totals['assets'] ?? 0, $sym, $pos) }}</span>
                            </div>
                            <div class="bs-groups-body">
                                @forelse($assetGroups as $groupName => $accounts)
                                    @php $groupTotal = array_sum($accounts); @endphp
                                    <div class="bs-group-block">
                                        <div class="bs-group-header">
                                            <span class="bs-group-name">{{ $groupName }}</span>
                                            <span class="bs-group-sum text-success">{{ money_fmt($groupTotal, $sym, $pos) }}</span>
                                        </div>
                                        @if(count($accounts) > 0)
                                            <div class="bs-account-list">
                                                @foreach($accounts as $accName => $accAmt)
                                                    <div class="bs-account-row">
                                                        <span class="bs-acc-name">{{ $accName }}</span>
                                                        <span class="bs-acc-amt">{{ money_fmt($accAmt, $sym, $pos) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="p-3 text-center text-muted">No assets recorded</div>
                                @endforelse
                            </div>
                            <div class="bs-section-total">
                                <span>Total Assets</span>
                                <span class="bs-total-val text-success">{{ money_fmt($totals['assets'] ?? 0, $sym, $pos) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('js')
    <script>
        function downloadReport(type) {
            var startDate = document.getElementById('start_date') ? document.getElementById('start_date').value : document.getElementById('m_start_date').value;
            var endDate = document.getElementById('end_date') ? document.getElementById('end_date').value : document.getElementById('m_end_date').value;
            
            var url = "{{ url()->current() }}?start_date=" + startDate + "&end_date=" + endDate + "&export=" + type;
            var title = type === 'pdf' ? 'Generating PDF...' : 'Generating Excel...';
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({ 
                    title: title, 
                    html: 'Please wait',
                    allowOutsideClick: false, 
                    didOpen: () => Swal.showLoading() 
                });
            }

            fetch(url)
                .then(function(response) {
                    if (!response.ok) throw new Error('Export failed');
                    
                    var filename = type === 'pdf' ? 'Balance_Sheet.pdf' : 'Balance_Sheet.xlsx';
                    var disposition = response.headers.get('content-disposition');
                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        var matches = filenameRegex.exec(disposition);
                        if (matches != null && matches[1]) { 
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    }
                    
                    return response.blob().then(blob => ({ blob, filename }));
                })
                .then(function({ blob, filename }) {
                    if (typeof Swal !== 'undefined') Swal.close();
                    
                    var blobUrl = window.URL.createObjectURL(blob);
                    var link = document.createElement('a');
                    link.href = blobUrl;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(blobUrl);
                })
                .catch(function(error) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', 'Failed to generate file', 'error');
                    } else {
                        alert('Failed to generate file');
                    }
                });
        }

        $(document).ready(function() {
            // Mobile bottom sheet handlers
            const $filterSheet = $("#bsFilterSheet");
            const $filterBackdrop = $("#bsFilterBackdrop");

            function openFilterSheet() {
                $filterSheet.addClass("is-open");
                $filterBackdrop.addClass("is-open");
                $("body").addClass("bs-sheet-open bs-filter-open");
            }

            function closeFilterSheet() {
                $filterSheet.removeClass("is-open");
                $filterBackdrop.removeClass("is-open");
                $("body").removeClass("bs-sheet-open bs-filter-open");
            }

            $("#bsFilterToggle, #bsFilterSummaryTrigger").on("click", function(e) {
                e.preventDefault();
                openFilterSheet();
            });

            $("#bsFilterSheetClose, #bsFilterBackdrop").on("click", function(e) {
                e.preventDefault();
                closeFilterSheet();
            });

            function toDateInputFormat(d) {
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // Quick date range presets
            $(".bs-preset-pill").on("click", function(e) {
                e.preventDefault();
                $(".bs-preset-pill").removeClass("active");
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
            $("#bsFilterClear").on("click", function(e) {
                e.preventDefault();
                const today = new Date();
                const sDate = new Date(today.getFullYear(), today.getMonth(), 1);
                const eDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                $("#m_start_date").val(toDateInputFormat(sDate));
                $("#m_end_date").val(toDateInputFormat(eDate));
                $(".bs-preset-pill").removeClass("active");
                $('.bs-preset-pill[data-preset="this-month"]').addClass("active");
            });

            // Highlight active preset on load if matching
            (function() {
                const sVal = $("#m_start_date").val();
                const eVal = $("#m_end_date").val();
                const today = new Date();
                const tmStart = toDateInputFormat(new Date(today.getFullYear(), today.getMonth(), 1));
                const tmEnd = toDateInputFormat(new Date(today.getFullYear(), today.getMonth() + 1, 0));
                if (sVal === tmStart && eVal === tmEnd) {
                    $('.bs-preset-pill[data-preset="this-month"]').addClass("active");
                }
            })();
        });
    </script>
    @endpush
@endsection
