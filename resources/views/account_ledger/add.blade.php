@extends('layout.app')
@section('title', 'Account Ledger')
@section('content')
   <style>
        /* Mobile & Tablet devices (< 1200px: phones, iPad Mini, iPad Air, iPad Pro) */
        @media screen and (max-width: 1199.98px) {
            .content {
                padding: 10px 10px 80px !important;
            }

            .al-desktop-header,
            .al-desktop-tools,
            #desktopFormContainer {
                display: none !important;
            }

            /* Mobile Header */
            .al-m-head {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
                padding: 12px;
                margin-bottom: 10px;
            }

            .al-m-head-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-bottom: 10px;
            }

            .al-m-title {
                font-size: 16px;
                font-weight: 700;
                color: #1b2850;
            }

            .al-m-head-actions .btn {
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

            /* Mobile Tool Pills (Excel, PDF) */
            .al-m-tools {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .al-m-tool {
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

            .al-m-tool i {
                font-size: 13px;
            }

            .al-m-tool.is-green {
                background: #e7f6ec;
                color: #198754;
            }

            .al-m-tool.is-red {
                background: #fdecee;
                color: #dc3545;
            }

            /* Mobile Payment Totals Chips */
            .al-m-totals-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 6px;
                margin-bottom: 10px;
            }

            .al-m-total-chip {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                padding: 6px 4px;
                text-align: center;
                box-shadow: 0 1px 2px rgba(16, 24, 40, .05);
            }

            .al-m-tot-label {
                display: block;
                font-size: 10.5px;
                font-weight: 600;
                color: #64748b;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .al-m-tot-val {
                display: block;
                font-size: 13px;
                font-weight: 700;
                margin-top: 2px;
            }

            .al-m-total-chip.is-emi .al-m-tot-val { color: #2563eb; }
            .al-m-total-chip.is-cash .al-m-tot-val { color: #16a34a; }
            .al-m-total-chip.is-online .al-m-tot-val { color: #0284c7; }

            /* Mobile Filter Trigger Bar */
            .al-filter-bar {
                display: flex;
                align-items: center;
                gap: 8px;
                background: #fff;
                border-radius: 12px;
                padding: 8px 10px;
                margin-bottom: 10px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            }

            .al-filter-summary {
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

            .al-filter-summary-text {
                font-size: 12.5px;
                color: #475467;
                font-weight: 500;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .al-filter-toggle {
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

            .al-filter-toggle.has-active {
                background: #e8334a;
            }

            .al-filter-count {
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

            /* Bottom Sheet Filter */
            .al-filter-backdrop {
                display: block;
                position: fixed;
                inset: 0;
                z-index: 1040;
                background: rgba(16, 24, 40, .45);
                opacity: 0;
                pointer-events: none;
                transition: opacity .2s ease;
            }

            .al-filter-sheet {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 1045;
                display: flex;
                flex-direction: column;
                max-height: 85vh;
                background: #fff;
                border-radius: 16px 16px 0 0;
                box-shadow: 0 -8px 24px rgba(16, 24, 40, .18);
                transform: translateY(105%);
                transition: transform .25s ease;
            }

            body.al-filter-open .al-filter-backdrop {
                opacity: 1;
                pointer-events: auto;
            }

            body.al-filter-open .al-filter-sheet {
                transform: translateY(0);
            }

            body.al-filter-open {
                overflow: hidden;
            }

            body.al-filter-open .mobile-bottom-nav,
            body.al-filter-open .floating-calculator-wrapper {
                display: none !important;
            }

            .al-filter-sheet-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 16px 18px;
                border-bottom: 1px solid #eef0f4;
                font-size: 16px;
                font-weight: 600;
                color: #1b2850;
            }

            .al-filter-sheet-close {
                width: 32px;
                height: 32px;
                padding: 0;
                border: 0;
                background: transparent;
                color: #64748b;
                font-size: 20px;
                cursor: pointer;
            }

            .al-filter-sheet-body {
                flex: 1 1 auto;
                overflow-y: auto;
                padding: 14px 18px;
            }

            .al-filter-sheet-body #appointmentForm .row {
                margin: 0 !important;
            }

            .al-filter-sheet-body #appointmentForm .col-6,
            .al-filter-sheet-body #appointmentForm .col-sm-6,
            .al-filter-sheet-body #appointmentForm .col-lg-3 {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
                padding: 0 !important;
            }

            .al-filter-sheet-body .form-group {
                margin-bottom: 14px !important;
            }

            .al-filter-sheet-body label {
                display: block;
                margin-bottom: 6px !important;
                font-size: 13.5px !important;
                font-weight: 600 !important;
                color: #344054 !important;
            }

            .al-filter-sheet-body .select2-container {
                width: 100% !important;
            }

            .al-filter-sheet-body .select2-container .select2-selection--single {
                height: 42px !important;
                border-radius: 8px !important;
                border-color: #d0d5dd !important;
            }

            .al-filter-sheet-body .select2-container .select2-selection--single .select2-selection__rendered {
                line-height: 42px !important;
                font-size: 14px !important;
                padding-left: 12px !important;
            }

            .al-filter-sheet-body .select2-container .select2-selection--single .select2-selection__arrow {
                height: 40px !important;
                right: 8px !important;
            }

            .al-filter-sheet-foot {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                padding: 14px 18px calc(14px + env(safe-area-inset-bottom));
                border-top: 1px solid #eef0f4;
            }

            .al-filter-clear,
            .al-filter-apply {
                height: 44px;
                border: 0;
                border-radius: 22px;
                font-size: 14.5px;
                font-weight: 600;
                cursor: pointer;
            }

            .al-filter-clear {
                background: #f1f5f9;
                color: #475467;
            }

            .al-filter-apply {
                background: #ff9f43;
                color: #fff;
            }

            body.al-filter-open .select2-container--open .select2-dropdown {
                z-index: 1060 !important;
            }

            /* Mobile Payment Card Styles */
            .al-m-card {
                background: #fff;
                border: 1px solid #eef0f4;
                border-radius: 12px;
                padding: 12px 14px;
                margin-bottom: 10px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .05);
            }

            .al-m-card-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 6px;
            }

            .al-m-card-bill {
                font-size: 14px;
                font-weight: 700;
                color: #1b2850;
            }

            .al-m-card-date {
                font-size: 11.5px;
                color: #64748b;
                font-weight: 500;
            }

            .al-m-card-party {
                font-size: 13.5px;
                font-weight: 600;
                color: #334155;
                margin-bottom: 8px;
                word-break: break-word;
            }

            .al-m-card-stats {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                background: #f8fafc;
                border-radius: 8px;
                padding: 8px 10px;
                margin-bottom: 10px;
            }

            .al-m-stat-item {
                display: flex;
                flex-direction: column;
            }

            .al-m-stat-label {
                font-size: 10.5px;
                color: #64748b;
                margin-bottom: 2px;
                text-transform: uppercase;
                font-weight: 600;
            }

            .al-m-stat-value {
                font-size: 13.5px;
                font-weight: 700;
                color: #1b2850;
            }

            .al-m-stat-value.is-paid {
                color: #16a34a;
            }

            .al-m-stat-value.is-pending {
                color: #dc2626;
            }

            .al-m-card-foot {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding-top: 8px;
                border-top: 1px dashed #e2e8f0;
            }

            .al-m-method-badge {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 3px 8px;
                border-radius: 6px;
                font-size: 11.5px;
                font-weight: 600;
                background: #f1f5f9;
                color: #475467;
            }

            .al-m-method-badge.cash { background: #fef3c7; color: #b45309; }
            .al-m-method-badge.online { background: #e0f2fe; color: #0369a1; }
            .al-m-method-badge.emi { background: #fee2e2; color: #b91c1c; }

            /* Empty state prompt */
            .al-empty-prompt {
                background: #fff;
                border-radius: 12px;
                padding: 24px 16px;
                text-align: center;
                border: 1px dashed #cbd5e1;
                margin: 10px 0;
            }

            .al-empty-prompt i {
                font-size: 36px;
                color: #ff9f43;
                margin-bottom: 10px;
                display: block;
            }

            .al-empty-prompt h6 {
                font-size: 15px;
                font-weight: 600;
                color: #1b2850;
                margin-bottom: 4px;
            }

            .al-empty-prompt p {
                font-size: 12.5px;
                color: #64748b;
                margin-bottom: 12px;
            }

            /* Card container styling on mobile */
            .card {
                border-radius: 12px !important;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .05) !important;
            }

            .card-body {
                padding: 12px !important;
            }

            /* Dark Mode Overrides */
            :root[data-theme="dark"] .al-m-head,
            :root[data-theme="dark"] .al-filter-bar,
            :root[data-theme="dark"] .al-filter-sheet,
            :root[data-theme="dark"] .al-m-card,
            :root[data-theme="dark"] .al-empty-prompt,
            :root[data-theme="dark"] .al-m-total-chip {
                background: #1e293b !important;
                border-color: #334155 !important;
            }

            :root[data-theme="dark"] .al-m-title,
            :root[data-theme="dark"] .al-m-card-bill,
            :root[data-theme="dark"] .al-filter-sheet-head,
            :root[data-theme="dark"] .al-empty-prompt h6,
            :root[data-theme="dark"] .al-m-stat-value {
                color: #f1f5f9 !important;
            }

            :root[data-theme="dark"] .al-m-card-party {
                color: #cbd5e1 !important;
            }

            :root[data-theme="dark"] .al-m-card-stats,
            :root[data-theme="dark"] .al-filter-summary {
                background: #0f172a !important;
                border-color: #334155 !important;
            }

            :root[data-theme="dark"] .al-filter-summary-text,
            :root[data-theme="dark"] .al-filter-sheet-body label,
            :root[data-theme="dark"] .al-m-tot-label {
                color: #94a3b8 !important;
            }

            :root[data-theme="dark"] .al-filter-sheet-head,
            :root[data-theme="dark"] .al-filter-sheet-foot {
                border-color: #334155 !important;
            }

            :root[data-theme="dark"] .al-m-tool.is-green { background: #064e3b; color: #6ee7b7; }
            :root[data-theme="dark"] .al-m-tool.is-red { background: #7f1d1d; color: #fca5a5; }
        }

        /* Tablet optimizations for iPad Mini, iPad Air, iPad Pro (768px - 1199.98px) */
        @media screen and (min-width: 768px) and (max-width: 1199.98px) {
            .content {
                padding: 16px 16px 80px !important;
            }

            .al-filter-sheet {
                max-width: 580px;
                left: 50% !important;
                right: auto !important;
                transform: translate(-50%, 105%);
                border-radius: 16px 16px 0 0;
                width: 100%;
            }

            body.al-filter-open .al-filter-sheet {
                transform: translate(-50%, 0) !important;
            }

            .al-m-cards {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .al-m-card {
                margin-bottom: 0;
            }

            .al-m-totals-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 12px;
            }

            .al-m-total-chip {
                padding: 10px 8px;
            }

            .al-m-tot-val {
                font-size: 16px;
            }
        }

        /* Medium devices (tablets, 768px and up to 1024px) */
        @media screen and (min-width: 768px) and (max-width: 1024px) {
            .table {
                font-size: 13px;
            }

            .table th,
            .table td {
                padding: 8px 6px;
            }

            /* Hide Details column on tablets (3rd column) */
            #paid-payments-table thead th:nth-child(3),
            #paid-payments-table tbody td:nth-child(3),
            #pending-payments-table thead th:nth-child(3),
            #pending-payments-table tbody td:nth-child(3) {
                display: none;
            }

            /* Hide expandable rows on tablets */
            .ledger-details-row {
                display: none !important;
            }
        }

        /* Large devices (desktops, 1025px and up) */
        @media screen and (min-width: 1025px) {
            .table {
                font-size: 14px;
            }

            .table th,
            .table td {
                padding: 12px 10px;
            }

            /* Hide Details column on desktop (3rd column) */
            #paid-payments-table thead th:nth-child(3),
            #paid-payments-table tbody td:nth-child(3),
            #pending-payments-table thead th:nth-child(3),
            #pending-payments-table tbody td:nth-child(3) {
                display: none;
            }

            /* Hide expandable rows on larger screens */
            .ledger-details-row {
                display: none !important;
            }
        }

        /* Expandable row details - available for all screen sizes */
        .ledger-details-row {
            display: none;
        }

        .ledger-details-row.show {
            display: table-row;
        }

        /* Expandable content styles */
        .ledger-details-content {
            padding: 15px;
            background: #fff;
            border-top: 2px solid #e0e0e0;
        }

        .ledger-details-list {
            margin-bottom: 15px;
        }

        .ledger-detail-row-simple {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .ledger-detail-row-simple:last-of-type {
            border-bottom: none;
        }

        .ledger-detail-label-simple {
            font-weight: 600;
            color: #595b5d;
            font-size: 14px;
        }

        .ledger-detail-value-simple {
            color: #1b2850;
            font-size: 14px;
            text-align: right;
        }

        /* Toggle button styles */
        .ledger-toggle-btn-table {
            background: #ff9f43;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .ledger-toggle-btn-table:hover {
            background: #ff8c2e;
        }

        .ledger-toggle-btn-table.minus {
            background: #dc3545;
        }

        .ledger-toggle-btn-table.minus:hover {
            background: #c82333;
        }

        #paid-payments-table tbody td:nth-child(3),
        #pending-payments-table tbody td:nth-child(3),
        .ledger-detail-value-simple {
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .ledger-mobile-party-name {
            display: none;
            margin-top: 4px;
            font-size: 14px;
            /* font-weight: 600; */
            color: #0c0c0c;
            line-height: 1.35;
            white-space: normal;
            word-break: break-word;
        }

        @media screen and (max-width: 1199.98px) {
            .ledger-mobile-party-name {
                display: block;
            }
        }

        .ledger-products-btn {
            border: 1px solid #ff9f43;
            background: #fff7ef;
            color: #1b2850;
            border-radius: 4px;
            padding: 4px 10px;
            font-weight: 600;
        }

        /* Dark Mode Override for View Products button */
        :root[data-theme="dark"] .ledger-products-btn {
            background: #0f172a !important;
            color: #d1d5db !important;
            border-color: #ff9f43 !important;
        }

        .ledger-total-box {
            border: 1px solid #1b2850;
            border-radius: 5px;
            padding: 4px 10px;
            display: inline-block;
        }
        .ledger-total-label {
            font-weight: bold;
            font-size: 15px;
            color: #1b2850;
        }

        /* Dark Mode Override for Total Labels */
        :root[data-theme="dark"] .ledger-total-box {
            border-color: #334155 !important;
        }
        :root[data-theme="dark"] .ledger-total-label {
            color: #d1d5db !important;
        }
    </style>
    <!-- Mobile Filter Backdrop & Bottom Sheet (<1200px) -->
    <div class="al-filter-backdrop d-xl-none" id="alFilterBackdrop"></div>
    <div class="al-filter-sheet d-xl-none" id="alFilterSheet" role="dialog" aria-modal="true" aria-labelledby="alFilterSheetTitle">
        <div class="al-filter-sheet-head">
            <span id="alFilterSheetTitle"><i class="fas fa-sliders-h me-2 text-warning"></i>Filter Ledger</span>
            <button type="button" class="al-filter-sheet-close" id="alFilterSheetClose" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="al-filter-sheet-body" id="alFilterSheetBody"></div>
        <div class="al-filter-sheet-foot">
            <button type="button" class="al-filter-clear" id="alFilterClear">Reset</button>
            <button type="button" class="al-filter-apply" id="alFilterApply">Apply Filters</button>
        </div>
    </div>

    <div class="content">
        <!-- Mobile Header (<1200px) -->
        <div class="al-m-head d-xl-none">
            <div class="al-m-head-top">
                <div class="al-m-title">Account Ledger</div>
                <div class="al-m-head-actions">
                    <a href="{{ route('auth.dashboard') }}" class="btn btn-sm btn-added">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="al-m-tools">
                <button type="button" class="al-m-tool is-green" data-al-proxy="#viewCsvBtn">
                    <i class="fas fa-file-excel"></i><span>Excel</span>
                </button>
                <button type="button" class="al-m-tool is-red" data-al-proxy="#viewPdfBtn">
                    <i class="fas fa-file-pdf"></i><span>PDF</span>
                </button>
            </div>
        </div>

        <!-- Mobile Totals Grid (<1200px) -->
        <div id="mobile-payment-mode-totals" class="al-m-totals-grid d-xl-none" style="display: none !important;">
            <div class="al-m-total-chip is-emi">
                <span class="al-m-tot-label">Total EMI</span>
                <span class="al-m-tot-val" id="m-total-emi-paid">₹0.00</span>
            </div>
            <div class="al-m-total-chip is-cash">
                <span class="al-m-tot-label">Total Cash</span>
                <span class="al-m-tot-val" id="m-total-cash-paid">₹0.00</span>
            </div>
            <div class="al-m-total-chip is-online">
                <span class="al-m-tot-label">Total Online</span>
                <span class="al-m-tot-val" id="m-total-online-paid">₹0.00</span>
            </div>
        </div>

        <!-- Mobile Filter Bar (<1200px) -->
        <div class="al-filter-bar d-xl-none">
            <div class="al-filter-summary" id="alFilterSummaryTrigger">
                <i class="fas fa-filter text-muted me-1"></i>
                <span class="al-filter-summary-text" id="alFilterSummaryText">Tap to filter by party, month, year...</span>
            </div>
            <button type="button" class="al-filter-toggle" id="alFilterToggle" aria-label="Filters">
                <i class="fas fa-sliders-h"></i>
                <span class="al-filter-count d-none" id="alFilterCount">0</span>
            </button>
        </div>

        <!-- Desktop Header (>=1200px) -->
        <div class="page-header al-desktop-header d-none d-xl-flex">
            <div class="page-title">
                <h4>Account Ledger</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('auth.dashboard') }}" class="btn btn-added">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Desktop PDF & Excel buttons and Payment Totals (>=1200px) -->
                <div class="row g-2 justify-content-between align-items-center mb-3 al-desktop-tools d-none d-xl-flex">
                    <div class="col-12 col-md-8">
                        <div id="payment-mode-totals" class="d-flex flex-wrap gap-2" style="display: none !important;">
                            <div class="border rounded p-2 bg-light text-center flex-grow-1">
                                <span class="d-block text-muted" style="font-size: 12px; font-weight: 600;">Total EMI Paid</span>
                                <span class="d-block text-primary fw-bold" id="total-emi-paid" style="font-size: 16px;">₹0.00</span>
                            </div>
                            <div class="border rounded p-2 bg-light text-center flex-grow-1">
                                <span class="d-block text-muted" style="font-size: 12px; font-weight: 600;">Total Cash Paid</span>
                                <span class="d-block text-success fw-bold" id="total-cash-paid" style="font-size: 16px;">₹0.00</span>
                            </div>
                            <div class="border rounded p-2 bg-light text-center flex-grow-1">
                                <span class="d-block text-muted" style="font-size: 12px; font-weight: 600;">Total Online Paid</span>
                                <span class="d-block text-info fw-bold" id="total-online-paid" style="font-size: 16px;">₹0.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="d-flex justify-content-md-end gap-2">
                            <button type="button" class="btn btn-sm text-white btn-danger flex-fill flex-md-grow-0" id="viewPdfBtn">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button type="button" class="btn btn-sm text-white btn-success flex-fill flex-md-grow-0" id="viewCsvBtn">
                                <i class="fas fa-file-csv"></i> Excel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Form Container for Desktop (>=1200px) -->
                <div id="desktopFormContainer" class="d-none d-xl-block">
                    <form id="appointmentForm">
                        <div class="row">
                            <div class="col-6 col-sm-6 col-lg-3">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select name="type" id="type" class="form-control select2 Name">
                                        <option value="">Select Type</option>
                                        <option value="customer">Customer</option>
                                        <option value="vendor">Vendor</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Customer Dropdown -->
                            <div class="col-6 col-sm-6 col-lg-3" id="customerDropdown" style="display: none;">
                                <div class="form-group">
                                    <label>Customer Name</label>
                                    <select name="customer_id" id="customer_id" class="form-control select2 Name">
                                        <option value="">Select Customer</option>
                                        <option value="all">All Customers</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id ?? 'null' }}" data-phone="{{ $customer->phone ?? '' }}">
                                                {{ $customer->name }}{{ $customer->phone ? ' - ' . $customer->phone : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-customer_id"></span>
                                </div>
                            </div>

                            <!-- Vendor Dropdown -->
                            <div class="col-6 col-sm-6 col-lg-3" id="vendorDropdown" style="display: none;">
                                <div class="form-group">
                                    <label>Vendor Name</label>
                                    <select name="vendor_id" id="vendor_id" class="form-control select2 Name">
                                        <option value="">Select Vendor</option>
                                        <option value="all">All Vendors</option>
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id ?? 'null' }}">
                                                {{ $vendor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-vendor_id"></span>
                                </div>
                            </div>

                            <div class="col-6 col-sm-6 col-lg-3">
                                <div class="form-group">
                                    <label>Month</label>
                                    <select name="month" id="month" class="form-control select2 Name">
                                        <option value="">Select Month</option>
                                        <option value="all">All Months</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}">
                                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                    <span class="text-danger error-month"></span>
                                </div>
                            </div>

                            <div class="col-6 col-sm-6 col-lg-3">
                                <div class="form-group">
                                    <label>Year</label>
                                    <select name="year" id="year" class="form-control select2 Name">
                                        <option value="">Select Year</option>
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-year"></span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div id="paymentDetails">
                    <div class="al-empty-prompt d-xl-none">
                        <i class="fas fa-filter"></i>
                        <h6>No Filter Selected</h6>
                        <p>Select Customer or Vendor from filters to view payment ledger records.</p>
                        <button type="button" class="btn btn-sm btn-primary al-open-sheet-btn">
                            <i class="fas fa-sliders-h me-1"></i> Choose Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ledgerProductsModal" tabindex="-1" aria-labelledby="ledgerProductsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ledgerProductsModalLabel">Bill Products</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body" id="ledgerProductsModalBody"></div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // Global variables
        var ledgerDataMap = {};

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }

        // Helper function to build expandable row content for Paid Payments
        function buildPaidPaymentExpandableRowContent(payment, order, productNames, paymentMethod, currency,
            currencyPosition) {
            const formatCurrency = (amount) => currencyPosition === 'left' ? `${currency}${amount}` :
            `${amount}${currency}`;

            return `
                <td colspan="8" class="ledger-details-content">
                    <div class="ledger-details-list">
                        <div class="ledger-detail-row-simple">
                            <span class="ledger-detail-label-simple">Product(s):</span>
                            <span class="ledger-detail-value-simple">${productNames}</span>
                        </div>
                        <div class="ledger-detail-row-simple">
                            <span class="ledger-detail-label-simple">Total Amount:</span>
                            <span class="ledger-detail-value-simple">${formatCurrency(order.total_amount || order.grand_total || 0)}</span>
                        </div>
                        <div class="ledger-detail-row-simple">
                            <span class="ledger-detail-label-simple">Paid Amount:</span>
                            <span class="ledger-detail-value-simple" style="font-weight: bold; color: #28a745;">${formatCurrency(payment.total_amount || order.amount_total || 0)}</span>
                        </div>
                        <div class="ledger-detail-row-simple">
                            <span class="ledger-detail-label-simple">Payment Date:</span>
                            <span class="ledger-detail-value-simple">${formatDate(payment.payment_date || order.created_at)}</span>
                        </div>
                        <div class="ledger-detail-row-simple">
                            <span class="ledger-detail-label-simple">Payment Method:</span>
                            <span class="ledger-detail-value-simple">${paymentMethod}</span>
                        </div>
                    </div>
                </td>
            `;
        }

        // Helper function to build expandable row content for Pending Payments
        function buildPendingPaymentExpandableRowContent(payment, order, productNames, currency, currencyPosition) {
            const formatCurrency = (amount) => currencyPosition === 'left' ? `${currency}${amount}` :
            `${amount}${currency}`;

            return `
                <td colspan="6" class="ledger-details-content">
                    <div class="ledger-details-list">
                        <div class="ledger-detail-row-simple">
                            <span class="ledger-detail-label-simple">Product(s):</span>
                            <span class="ledger-detail-value-simple">${productNames}</span>
                        </div>
                        <div class="ledger-detail-row-simple">
                            <span class="ledger-detail-label-simple">Total Amount:</span>
                            <span class="ledger-detail-value-simple">${formatCurrency(order.total_amount || order.grand_total || 0)}</span>
                        </div>
                        <div class="ledger-detail-row-simple">
                            <span class="ledger-detail-label-simple">Remaining Amount:</span>
                            <span class="ledger-detail-value-simple" style="font-weight: bold; color: #dc3545;">${formatCurrency(payment.amount_total || order.total_amount || 0)}</span>
                        </div>
                    </div>
                </td>
            `;
        }

        // Toggle function for Paid Payments table rows
        window.togglePaidPaymentRowDetails = function(paymentIndex) {
            const btn = $(`.ledger-toggle-btn-table[data-paid-index="${paymentIndex}"]`);
            if (btn.length === 0) return;

            const row = btn.closest('tr');
            let detailsRow = row.next(`tr.ledger-details-row[data-paid-index="${paymentIndex}"]`);
            const icon = btn.find('.toggle-icon');

            if (detailsRow.length === 0) {
                const paymentData = ledgerDataMap.paidPayments && ledgerDataMap.paidPayments[paymentIndex];
                if (paymentData) {
                    detailsRow = $('<tr>')
                        .addClass('ledger-details-row')
                        .attr('data-paid-index', paymentIndex)
                        .html(buildPaidPaymentExpandableRowContent(
                            paymentData.payment,
                            paymentData.order,
                            paymentData.productNames,
                            paymentData.paymentMethod,
                            paymentData.currency,
                            paymentData.currencyPosition
                        ));
                    row.after(detailsRow);
                } else {
                    return;
                }
            }

            if (detailsRow.hasClass('show')) {
                detailsRow.removeClass('show');
                btn.removeClass('minus');
                icon.text('+');
            } else {
                detailsRow.addClass('show');
                btn.addClass('minus');
                icon.text('−');
            }
        };

        // Toggle function for Pending Payments table rows
        window.togglePendingPaymentRowDetails = function(paymentIndex) {
            const btn = $(`.ledger-toggle-btn-table[data-pending-index="${paymentIndex}"]`);
            if (btn.length === 0) return;

            const row = btn.closest('tr');
            let detailsRow = row.next(`tr.ledger-details-row[data-pending-index="${paymentIndex}"]`);
            const icon = btn.find('.toggle-icon');

            if (detailsRow.length === 0) {
                const paymentData = ledgerDataMap.pendingPayments && ledgerDataMap.pendingPayments[paymentIndex];
                if (paymentData) {
                    detailsRow = $('<tr>')
                        .addClass('ledger-details-row')
                        .attr('data-pending-index', paymentIndex)
                        .html(buildPendingPaymentExpandableRowContent(
                            paymentData.payment,
                            paymentData.order,
                            paymentData.productNames,
                            paymentData.currency,
                            paymentData.currencyPosition
                        ));
                    row.after(detailsRow);
                } else {
                    return;
                }
            }

            if (detailsRow.hasClass('show')) {
                detailsRow.removeClass('show');
                btn.removeClass('minus');
                icon.text('+');
            } else {
                detailsRow.addClass('show');
                btn.addClass('minus');
                icon.text('−');
            }
        };

        // Helper function to get product names (will be defined in displayPaymentDetails scope)
        function getProductNames(order) {
            if (order.items && order.items.length > 0) {
                return order.items.map(i => i.product?.name || '-').join(', ');
            } else if (order.products_with_names && order.products_with_names.length > 0) {
                return order.products_with_names.map(p => p.product_name || '-').join(', ');
            }
            return '-';
        }

        // Helper function to capitalize
        function capitalize(str) {
            if (!str) return '-';
            return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function getLedgerPartyName(type, order) {
            if (!order) return '';

            if (type === 'customer') {
                return order.user?.name || order.customer_name || '';
            }

            if (type === 'vendor') {
                return order.vendor?.name || order.vendor_name || '';
            }

            return '';
        }

        window.openLedgerProductsModal = function(modalKey) {
            const data = ledgerDataMap.productLists && ledgerDataMap.productLists[modalKey];
            if (!data) return;

            const formatCurrencyValue = (amount) => {
                if (amount === null || amount === undefined || amount === '') return '-';
                const parsedAmount = parseFloat(amount || 0);
                const formatted = parsedAmount.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                return data.currencyPosition === 'left'
                    ? `${data.currency}${formatted}`
                    : `${formatted}${data.currency}`;
            };

            const rows = (data.products || []).length
                ? data.products.map((product, index) => `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${escapeHtml(product.name || '-')}</td>
                        <td>${escapeHtml(product.quantity ?? '-')}</td>
                        <td>${formatCurrencyValue(product.price)}</td>
                        <td>${formatCurrencyValue(product.total)}</td>
                    </tr>
                `).join('')
                : '<tr><td colspan="5" class="text-center text-muted">No products found.</td></tr>';

            $('#ledgerProductsModalLabel').text(`Products - ${data.billNo || '-'}`);
            $('#ledgerProductsModalBody').html(`
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
            `);
            $('#ledgerProductsModal').modal('show');
        };

        $(document).ready(function() {
            $('#type').on('change', function() {
                if (this.value === "customer") {
                    $('#customerDropdown').show();
                    $('#vendorDropdown').hide();
                } else if (this.value === "vendor") {
                    $('#vendorDropdown').show();
                    $('#customerDropdown').hide();
                } else {
                    $('#customerDropdown').hide();
                    $('#vendorDropdown').hide();
                }
            });
        });
        $(document).ready(function() {
            // localStorage.getItem('selectedSubAdminId');
            // console.log(localStorage.getItem("selectedSubAdminId"));


            $('.Name').not('#customer_id').select2({
                placeholder: 'Select an option',
                width: '100%'
            });

            $('#customer_id').select2({
                placeholder: 'Select an option',
                width: '100%',
                matcher: function(params, data) {
                    if ($.trim(params.term) === '') {
                        return data;
                    }

                    if (typeof data.text === 'undefined') {
                        return null;
                    }

                    const term = params.term.toLowerCase();
                    const text = (data.text || '').toLowerCase();
                    const phone = ($(data.element).data('phone') || '').toString().toLowerCase();
                    const normalizedTerm = term.replace(/[^0-9a-z]/g, '');
                    const normalizedPhone = phone.replace(/[^0-9]/g, '');

                    if (text.indexOf(term) > -1 || phone.indexOf(term) > -1) {
                        return data;
                    }

                    if (normalizedTerm && normalizedPhone.indexOf(normalizedTerm) > -1) {
                        return data;
                    }

                    return null;
                }
            });

            // Trigger fetch when any filter is changed
            $('#type, #customer_id, #vendor_id, #month, #year').on('change', fetchPayments);
            let debounceTimer;

            // $('#vehicle_number').on('input', function() {
            //     clearTimeout(debounceTimer);
            //     debounceTimer = setTimeout(fetchPayments, 500); // Debounce typing
            // });

            $('#customer_id, #vendor_id').on('change', function() {
                const selectedOption = $(this).find(':selected');
                const subbranchId = selectedOption.data('subbranch-id');

                if (subbranchId) {
                    // console.log("Selected Sub Branch ID:", subbranchId);

                    // Optional: store it in localStorage for API calls
                    localStorage.setItem("selectedSubBranchId", subbranchId);
                } else {
                    // console.log("No subbranch ID found.");
                    localStorage.removeItem("selectedSubBranchId");
                }
            });

            function fetchPayments() {
                var authToken = localStorage.getItem("authToken");
                var type = $('#type').val(); // customer | vendor
                var selectedId = type === 'customer' ? $('#customer_id').val() : $('#vendor_id').val();
                var month = $('#month').val();
                var year = $('#year').val();
                var selectedId = type === 'customer' ? $('#customer_id').val() : $('#vendor_id').val();
                // if (!selectedId) {
                //     $('#paymentDetails').html('<div class="text-danger">Please select an option.</div>');
                //     return;
                // }
                if (!selectedId) {
                    let message = 'Please select an option.';

                    if (type === 'customer') {
                        message = 'Please select customer name.';
                    } else if (type === 'vendor') {
                        message = 'Please select vendor name.';
                    }

                    $('#paymentDetails').html(`
                        <div class="d-none d-xl-block text-danger">${message}</div>
                        <div class="al-empty-prompt d-xl-none">
                            <i class="fas fa-filter"></i>
                            <h6>${message}</h6>
                            <p>Choose customer or vendor from filters to view payment ledger records.</p>
                            <button type="button" class="btn btn-sm btn-primary al-open-sheet-btn">
                                <i class="fas fa-sliders-h me-1"></i> Choose Filter
                            </button>
                        </div>
                    `);
                    return;
                }

                $.ajax({
                    url: '/api/get-payment-details',
                    type: 'GET',
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    data: {
                        type: type, // send this!
                        customer_id: type === 'customer' ? $('#customer_id').val() : null,
                        vendor_id: type === 'vendor' ? $('#vendor_id').val() : null,
                        month: month,
                        year: year,
                        // vehicle_number: vehicleNumber,
                        selectedSubAdminId: localStorage.getItem("selectedSubAdminId")
                    },
                    success: function(response) {
                        displayPaymentDetails(response);
                    },
                    error: function(xhr) {
                        $('#paymentDetails').html(
                            '<div class="text-danger">Failed to fetch payment details.</div>');
                    }
                });
            }

            function displayPaymentDetails(data) {
                const currency = data.settings.currency_symbol || '₹';
                const currencyPosition = data.settings.currency_position || 'left';

                function formatCurrency(amount) {
                    amount = parseFloat(amount || 0);

                    // Indian number format with commas
                    const formatted = amount.toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    return currencyPosition === 'left' ? `${currency}${formatted}` : `${formatted}${currency}`;
                }

                function formatDate(dateString) {
                    if (!dateString) return '-';
                    const date = new Date(dateString);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    return `${day}-${month}-${year}`;
                }

                function capitalize(str) {
                    if (!str) return '-';
                    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
                }

                // ✅ Common product name extractor
                function getProductNames(order) {
                    if (order.items && order.items.length > 0) {
                        return order.items.map(i => i.product?.name || '-').join(', ');
                    } else if (order.products_with_names && order.products_with_names.length > 0) {
                        return order.products_with_names.map(p => p.product_name || '-').join(', ');
                    }
                    return '-';
                }

                function getProductItems(order) {
                    if (order.items && order.items.length > 0) {
                        return order.items.map(item => ({
                            name: item.product?.name || '-',
                            quantity: item.quantity ?? '-',
                            price: item.price ?? null,
                            total: item.total_amount ?? null,
                        }));
                    }

                    if (order.products_with_names && order.products_with_names.length > 0) {
                        return order.products_with_names.map(item => ({
                            name: item.product_name || '-',
                            quantity: item.quantity ?? '-',
                            price: item.price ?? null,
                            total: item.total ?? null,
                        }));
                    }

                    return [];
                }

                function getBillKey(payment, index) {
                    const order = payment.order || payment;
                    return [
                        order.id || order.purchase_invoice_id || order.invoice_number || order.order_number || index,
                        order.invoice_number || order.order_number || '',
                    ].join('|');
                }

                function groupPaymentsByBill(payments, status) {
                    const grouped = {};

                    (payments || []).forEach((payment, index) => {
                        const order = payment.order || payment;
                        const key = getBillKey(payment, index);

                        if (!grouped[key]) {
                            grouped[key] = {
                                payment: { ...payment },
                                order: order,
                                paidTotal: 0,
                                pendingTotal: 0,
                                paymentMethods: [],
                                paymentDates: [],
                            };
                        }

                        if (status === 'paid') {
                            grouped[key].paidTotal += parseFloat(payment.total_amount || order.amount_total || 0);
                            if (payment.payment_method) grouped[key].paymentMethods.push(payment.payment_method);
                            if (payment.payment_date) grouped[key].paymentDates.push(payment.payment_date);
                        } else {
                            grouped[key].pendingTotal = parseFloat(payment.amount_total || order.remaining_amount || order.total_amount || 0);
                        }
                    });

                    return Object.values(grouped).map(group => {
                        if (status === 'paid') {
                            group.payment.total_amount = group.paidTotal;
                            group.payment.payment_method = [...new Set(group.paymentMethods)].join(', ');
                            group.payment.payment_date = group.paymentDates[group.paymentDates.length - 1] || group.payment.payment_date;
                        } else {
                            group.payment.amount_total = group.pendingTotal;
                        }

                        return group;
                    });
                }

                function buildProductsButton(modalKey, count) {
                    return `
                        <button type="button" class="ledger-products-btn" onclick="openLedgerProductsModal('${modalKey}')">
                            <i class="fas fa-boxes me-1"></i> Products (${count})
                        </button>
                    `;
                }

                let html = '';
                let type = $('#type').val();
                let billNoLabel = (type === 'customer') ? 'Order Number' : 'Bill No.';

                // Store payment data for expandable rows
                ledgerDataMap.paidPayments = {};
                ledgerDataMap.pendingPayments = {};
                ledgerDataMap.productLists = {};

                // Calculate payment mode totals
                let totalEmiPaid = 0;
                let totalCashPaid = 0;
                let totalOnlinePaid = 0;
                let hasPayments = false;

                (data.paidPayments || []).forEach(function(payment) {
                    hasPayments = true;
                    const order = payment.order || payment;
                    let amt = parseFloat(payment.total_amount || order.amount_total || 0);
                    let method = (payment.payment_method || '').toLowerCase().trim();
                    
                    if (method.includes('emi')) {
                        totalEmiPaid += amt;
                    } else if (method.includes('cash')) {
                        totalCashPaid += amt;
                    } else if (method.includes('online')) {
                        totalOnlinePaid += amt;
                    }
                });

                if (hasPayments) {
                    $('#total-emi-paid').text(formatCurrency(totalEmiPaid));
                    $('#total-cash-paid').text(formatCurrency(totalCashPaid));
                    $('#total-online-paid').text(formatCurrency(totalOnlinePaid));
                    $('#payment-mode-totals').attr('style', 'display: flex !important;');

                    $('#m-total-emi-paid').text(formatCurrency(totalEmiPaid));
                    $('#m-total-cash-paid').text(formatCurrency(totalCashPaid));
                    $('#m-total-online-paid').text(formatCurrency(totalOnlinePaid));
                    $('#mobile-payment-mode-totals').attr('style', 'display: grid !important;');
                } else {
                    $('#payment-mode-totals').attr('style', 'display: none !important;');
                    $('#mobile-payment-mode-totals').attr('style', 'display: none !important;');
                }

                // === Paid Payments ===
                let desktopPaidRows = '';
                let mobilePaidCards = '';
                let totalPaid = 0;
                const paidPaymentGroups = groupPaymentsByBill(data.paidPayments, 'paid');
                if (paidPaymentGroups.length > 0) {
                    paidPaymentGroups.forEach(function(group, index) {
                        const payment = group.payment;
                        const order = group.order;
                        const productNames = getProductNames(order);
                        const productItems = getProductItems(order);
                        const modalKey = `paid-${index}`;
                        ledgerDataMap.productLists[modalKey] = {
                            billNo: order.order_number || order.invoice_number || '-',
                            products: productItems,
                            currency: currency,
                            currencyPosition: currencyPosition
                        };
                        const paymentMethod = capitalize(payment.payment_method || order.payment_status || '-');
                        const partyName = getLedgerPartyName(type, order);
                        const billNumber = order.order_number || order.invoice_number || '-';
                        const billDate = formatDate(type === 'customer' ? order.created_at : order.purchase_date);
                        const paymentDate = formatDate(payment.payment_date || order.created_at);
                        const totalAmtFormatted = formatCurrency(order.total_amount || order.grand_total || 0);
                        const paidAmtFormatted = formatCurrency(payment.total_amount || order.amount_total || 0);

                        // Store payment data for expandable row (desktop)
                        ledgerDataMap.paidPayments[index] = {
                            payment: payment,
                            order: order,
                            productNames: productNames,
                            paymentMethod: paymentMethod,
                            currency: currency,
                            currencyPosition: currencyPosition
                        };

                        // Toggle button for Details column (desktop)
                        const detailsToggle = `
                            <button class="ledger-toggle-btn-table" onclick="togglePaidPaymentRowDetails('${index}')" data-paid-index="${index}">
                                <span class="toggle-icon">+</span>
                            </button>
                        `;

                        desktopPaidRows += `
            <tr>
                <td>
                    <div class="ledger-mobile-party-name">${escapeHtml(partyName)}</div>
                    <div>${billNumber}</div>
                </td>
                <td>${billDate}</td>
                <td>${detailsToggle}</td>
                <td>${buildProductsButton(modalKey, productItems.length)}</td>
                <td>${totalAmtFormatted}</td>
                <td>${paidAmtFormatted}</td>
                <td>${paymentDate}</td>
                <td>${paymentMethod}</td>
            </tr>`;

                        // Mobile Card
                        const methodLower = (paymentMethod || '').toLowerCase();
                        const methodClass = methodLower.includes('cash') ? 'cash' :
                                           (methodLower.includes('online') ? 'online' :
                                           (methodLower.includes('emi') ? 'emi' : ''));
                        mobilePaidCards += `
            <div class="al-m-card">
                <div class="al-m-card-head">
                    <span class="al-m-card-bill">#${escapeHtml(billNumber)}</span>
                    <span class="al-m-card-date"><i class="far fa-calendar-alt me-1"></i>${billDate}</span>
                </div>
                ${partyName ? `<div class="al-m-card-party"><i class="far fa-user me-1 text-muted"></i>${escapeHtml(partyName)}</div>` : ''}
                <div class="al-m-card-stats">
                    <div class="al-m-stat-item">
                        <span class="al-m-stat-label">Total Amount</span>
                        <span class="al-m-stat-value">${totalAmtFormatted}</span>
                    </div>
                    <div class="al-m-stat-item">
                        <span class="al-m-stat-label">Paid Amount</span>
                        <span class="al-m-stat-value is-paid">${paidAmtFormatted}</span>
                    </div>
                </div>
                <div class="al-m-card-foot">
                    ${buildProductsButton(modalKey, productItems.length)}
                    <span class="al-m-method-badge ${methodClass}">
                        <i class="fas fa-check-circle text-success me-1"></i>${escapeHtml(paymentMethod)}
                    </span>
                </div>
            </div>`;

                        totalPaid += parseFloat(payment.total_amount || order.amount_total || 0);
                    });
                } else {
                    desktopPaidRows = `<tr><td colspan="8" class="text-center text-muted">No record available</td></tr>`;
                    mobilePaidCards = `<div class="p-3 text-center text-muted">No record available</div>`;
                }

                html += `
    <div class="card shadow-sm mb-4">
        <div class="card-header text-dark d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Paid Payments</h5>
            <span class="badge bg-success">${paidPaymentGroups.length} Bills</span>
        </div>
        <div class="card-body p-0">
            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-xl-block">
                <table class="table table-bordered table-striped mb-0" id="paid-payments-table">
                    <thead class="thead-light">
                        <tr>
                            <th>${billNoLabel}</th>
                            <th>Date</th>
                            <th class="text-center">Details</th>
                            <th>Product(s)</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Payment Date</th>
                            <th>Payment Method</th>
                        </tr>
                    </thead>
                    <tbody>${desktopPaidRows}</tbody>
                </table>
            </div>
            <!-- Mobile Cards View -->
            <div class="al-m-cards d-xl-none p-2">
                ${mobilePaidCards}
            </div>
            <div class="d-flex justify-content-end pe-3 mt-2 mb-2">
                <div class="ledger-total-box">
                    <span class="ledger-total-label">Total Paid: </span>
                    <span style="font-weight: bold; font-size: 15px; color: orange;">
                        ${formatCurrency(totalPaid.toFixed(2))}
                    </span>
                </div>
            </div>
        </div>
    </div>`;

                // === Pending Payments ===
                let desktopPendingRows = '';
                let mobilePendingCards = '';
                let totalPending = 0;
                const pendingPaymentGroups = groupPaymentsByBill(data.pendingPayments, 'pending');
                if (pendingPaymentGroups.length > 0) {
                    pendingPaymentGroups.forEach(function(group, index) {
                        const payment = group.payment;
                        const order = group.order;
                        const productNames = getProductNames(order);
                        const productItems = getProductItems(order);
                        const modalKey = `pending-${index}`;
                        ledgerDataMap.productLists[modalKey] = {
                            billNo: order.order_number || order.invoice_number || payment.invoice_id || '-',
                            products: productItems,
                            currency: currency,
                            currencyPosition: currencyPosition
                        };
                        const partyName = getLedgerPartyName(type, order);
                        const billNumber = order.order_number || order.invoice_number || payment.invoice_id || '-';
                        const billDate = formatDate(type === 'customer' ? order.created_at : order.purchase_date);
                        const totalAmtFormatted = formatCurrency(order.total_amount || order.grand_total || 0);
                        const remainingAmtFormatted = formatCurrency(payment.amount_total || order.total_amount || 0);

                        // Store payment data for expandable row
                        ledgerDataMap.pendingPayments[index] = {
                            payment: payment,
                            order: order,
                            productNames: productNames,
                            currency: currency,
                            currencyPosition: currencyPosition
                        };

                        // Toggle button for Details column (desktop)
                        const detailsToggle = `
                            <button class="ledger-toggle-btn-table" onclick="togglePendingPaymentRowDetails('${index}')" data-pending-index="${index}">
                                <span class="toggle-icon">+</span>
                            </button>
                        `;

                        desktopPendingRows += `
            <tr>
                <td>
                    <div class="ledger-mobile-party-name">${escapeHtml(partyName)}</div>
                    <div>${billNumber}</div>
                </td>
                <td>${billDate}</td>
                <td>${detailsToggle}</td>
                <td>${buildProductsButton(modalKey, productItems.length)}</td>
                <td>${totalAmtFormatted}</td>
                <td>${remainingAmtFormatted}</td>
            </tr>`;

                        mobilePendingCards += `
            <div class="al-m-card">
                <div class="al-m-card-head">
                    <span class="al-m-card-bill">#${escapeHtml(billNumber)}</span>
                    <span class="al-m-card-date"><i class="far fa-calendar-alt me-1"></i>${billDate}</span>
                </div>
                ${partyName ? `<div class="al-m-card-party"><i class="far fa-user me-1 text-muted"></i>${escapeHtml(partyName)}</div>` : ''}
                <div class="al-m-card-stats">
                    <div class="al-m-stat-item">
                        <span class="al-m-stat-label">Total Amount</span>
                        <span class="al-m-stat-value">${totalAmtFormatted}</span>
                    </div>
                    <div class="al-m-stat-item">
                        <span class="al-m-stat-label">Remaining Amount</span>
                        <span class="al-m-stat-value is-pending">${remainingAmtFormatted}</span>
                    </div>
                </div>
                <div class="al-m-card-foot">
                    ${buildProductsButton(modalKey, productItems.length)}
                    <span class="al-m-method-badge emi">
                        <i class="fas fa-clock text-danger me-1"></i>Pending
                    </span>
                </div>
            </div>`;

                        totalPending += parseFloat(payment.amount_total || order.amount_total || 0);
                    });
                } else {
                    desktopPendingRows = `<tr><td colspan="6" class="text-center text-muted">No record available</td></tr>`;
                    mobilePendingCards = `<div class="p-3 text-center text-muted">No record available</div>`;
                }

                html += `
    <div class="card shadow-sm mb-4">
        <div class="card-header text-dark d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Pending Payments</h5>
            <span class="badge bg-danger">${pendingPaymentGroups.length} Bills</span>
        </div>
        <div class="card-body p-0">
            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-xl-block">
                <table class="table table-bordered table-striped mb-0" id="pending-payments-table">
                    <thead class="thead-light">
                        <tr>
                            <th>${billNoLabel}</th>
                            <th>Date</th>
                            <th class="text-center">Details</th>
                            <th>Product(s)</th>
                            <th>Total Amount</th>
                            <th>Remaining Amount</th>
                        </tr>
                    </thead>
                    <tbody>${desktopPendingRows}</tbody>
                </table>
            </div>
            <!-- Mobile Cards View -->
            <div class="al-m-cards d-xl-none p-2">
                ${mobilePendingCards}
            </div>
            <div class="d-flex justify-content-end pe-3 mt-2 mb-2">
                <div class="ledger-total-box">
                    <span class="ledger-total-label">Total Pending: </span>
                    <span style="font-weight: bold; font-size: 15px; color: orange;">
                        ${formatCurrency(totalPending.toFixed(2))}
                    </span>
                </div>
            </div>
        </div>
    </div>`;

                $('#paymentDetails').html(html);
            }



            $('#viewCsvBtn').on('click', function() {
                const type = $('#type').val();
                const customer_id = $('#customer_id').val();
                const vendor_id = $('#vendor_id').val();
                const month = $('#month').val();
                const year = $('#year').val();
                // const vehicleNumber = $('#vehicle_number').val().trim();

                if (!type) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Type',
                        text: 'Please select a type before exporting.',
                    });
                    return;
                }

                if (type === 'customer') {
                    const selectedValue = $('#customer_id').val();
                    if (!selectedValue) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing Customer',
                            text: 'Please select a customer before exporting.',
                        });
                        return;
                    }
                    userId = selectedValue === "all" ? "all" : selectedValue.split('|')[0];
                } else if (type === 'vendor') {
                    const selectedValue = $('#vendor_id').val();
                    if (!selectedValue) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing Vendor',
                            text: 'Please select a vendor before exporting.',
                        });
                        return;
                    }
                    userId = selectedValue === "all" ? "all" : selectedValue.split('|')[0];
                }

                // const parts = selectedValue.split('|');
                // const userId = parts[0] !== 'null' ? parts[0] : '';
                // const customerName = parts[1] || '';

                const authToken = localStorage.getItem("authToken");
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                // console.log(selectedSubAdminId);

                Swal.fire({
                    title: 'Generating Excel...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '/api/export-account-ledger',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + authToken
                    },
                    data: {
                        type: type,
                        customer_id: customer_id,
                        vendor_id: vendor_id,
                        month: month,
                        year: year,
                        selectedSubAdminId: selectedSubAdminId,
                    },
                    success: function(res) {
                        Swal.close(); // ✅ close the loader
                        if (res.status) {
                            const a = document.createElement('a');
                            a.href = res.file_url;
                            a.download = res.file_name;
                            document.body.appendChild(a);
                            a.click();
                            a.remove();
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function(err) {
                        Swal.close();
                        Swal.fire('Error', 'Failed to generate Excel file.', 'error');
                        console.error(err);
                    }
                });

            });


            $('#viewPdfBtn').on('click', function() {
                const type = $('#type').val();
                const customer_id = $('#customer_id').val();
                const vendor_id = $('#vendor_id').val();
                const month = $('#month').val();
                const year = $('#year').val();
                const authToken = localStorage.getItem("authToken");
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

                if (!type) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Type',
                        text: 'Please select a type before exporting.'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Generating PDF...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: '/api/account-ledger/download-pdf',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + authToken,
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        type: type,
                        customer_id: customer_id,
                        vendor_id: vendor_id,
                        month: month,
                        year: year,
                        selectedSubAdminId: selectedSubAdminId,
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.status && response.file_url) {
                            // ✅ Create temporary link to trigger download
                            const link = document.createElement('a');
                            link.href = response.file_url;
                            link.download = response.file_name || 'account_ledger.pdf';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Download Failed',
                                text: 'File URL not found in response.'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Download Failed',
                            text: 'Failed to generate PDF.'
                        });
                    }
                });
            });

            // Resize handler for responsive behavior
            let resizeTimer;
            let lastWidth = $(window).width();

            function forceLedgerCSSRecalculation() {
                const temp = document.createElement('div');
                temp.style.width = '1px';
                temp.style.height = '1px';
                temp.style.position = 'absolute';
                temp.style.visibility = 'hidden';
                document.body.appendChild(temp);
                void temp.offsetWidth;
                void temp.offsetHeight;
                document.body.removeChild(temp);

                void window.innerWidth;
                void window.innerHeight;
                void document.documentElement.offsetWidth;
                void document.documentElement.offsetHeight;
            }

            function handleLedgerResize() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const currentWidth = $(window).width();
                    lastWidth = currentWidth;

                    // Force CSS recalculation
                    forceLedgerCSSRecalculation();

                    const paidTable = document.getElementById('paid-payments-table');
                    const pendingTable = document.getElementById('pending-payments-table');
                    const tableResponsive = document.querySelectorAll('.table-responsive');

                    [paidTable, pendingTable, ...tableResponsive].forEach(function(el) {
                        if (el) {
                            void el.offsetHeight;
                            void el.offsetWidth;
                            el.style.display = 'none';
                            void el.offsetHeight;
                            el.style.display = '';
                        }
                    });

                    forceLedgerCSSRecalculation();
                }, 50);
            }

            // Window resize handler
            $(window).off('resize.ledger').on('resize.ledger', handleLedgerResize);

            if (window.ledgerResizeHandler) {
                window.removeEventListener('resize', window.ledgerResizeHandler);
            }
            window.ledgerResizeHandler = handleLedgerResize;
            window.addEventListener('resize', window.ledgerResizeHandler, {
                passive: true
            });

            // Orientation change handler
            $(window).off('orientationchange.ledger').on('orientationchange.ledger', function() {
                setTimeout(function() {
                    lastWidth = $(window).width();
                    handleLedgerResize();
                }, 300);
            });

            window.addEventListener('orientationchange', function() {
                setTimeout(function() {
                    lastWidth = $(window).width();
                    handleLedgerResize();
                }, 500);
            });

            // MatchMedia listeners for breakpoint changes
            const queries = [
                window.matchMedia('(max-width: 575.98px)'),
                window.matchMedia('(min-width: 576px) and (max-width: 767.98px)'),
                window.matchMedia('(min-width: 768px) and (max-width: 1024px)'),
                window.matchMedia('(min-width: 1025px)')
            ];

            queries.forEach(function(query) {
                if (query.addEventListener) {
                    query.addEventListener('change', function() {
                        setTimeout(handleLedgerResize, 100);
                    });
                } else if (query.addListener) {
                    query.addListener(function() {
                        setTimeout(handleLedgerResize, 100);
                    });
                }
            });

            // Initial width set and call
            lastWidth = $(window).width();

            $(window).on('load', function() {
                setTimeout(function() {
                    lastWidth = $(window).width();
                    handleLedgerResize();
                }, 500);
            });

            // Mobile bottom sheet and filter placement
            (function initMobileFilterSheet() {
                const sheetBody = document.getElementById('alFilterSheetBody');
                const desktopContainer = document.getElementById('desktopFormContainer');
                const form = document.getElementById('appointmentForm');
                const filterQuery = window.matchMedia('(max-width: 1199.98px)');

                function placeFilterFields() {
                    if (!sheetBody || !desktopContainer || !form) return;
                    if (filterQuery.matches) {
                        if (!sheetBody.contains(form)) {
                            sheetBody.appendChild(form);
                        }
                    } else {
                        if (!desktopContainer.contains(form)) {
                            desktopContainer.appendChild(form);
                            closeLedgerSheet();
                        }
                    }
                }

                function openLedgerSheet() {
                    placeFilterFields();
                    document.body.classList.add('al-filter-open');
                    $('#alFilterToggle').attr('aria-expanded', 'true');
                }

                function closeLedgerSheet() {
                    document.body.classList.remove('al-filter-open');
                    $('#alFilterToggle').attr('aria-expanded', 'false');
                }

                function updateFilterSummary() {
                    const type = $('#type').val();
                    const customerId = $('#customer_id').val();
                    const vendorId = $('#vendor_id').val();
                    const month = $('#month').val();
                    const year = $('#year').val();

                    let count = 0;
                    let parts = [];

                    if (type) {
                        count++;
                        if (type === 'customer') {
                            if (customerId && customerId !== '') {
                                count++;
                                const text = $('#customer_id option:selected').text().trim();
                                parts.push(customerId === 'all' ? 'All Customers' : text.split(' - ')[0]);
                            } else {
                                parts.push('Customer');
                            }
                        } else if (type === 'vendor') {
                            if (vendorId && vendorId !== '') {
                                count++;
                                const text = $('#vendor_id option:selected').text().trim();
                                parts.push(vendorId === 'all' ? 'All Vendors' : text);
                            } else {
                                parts.push('Vendor');
                            }
                        }
                    }

                    if (month && month !== '') {
                        count++;
                        const monthText = $('#month option:selected').text().trim();
                        parts.push(monthText);
                    }

                    if (year && year !== '') {
                        count++;
                        parts.push(year);
                    }

                    const countEl = $('#alFilterCount');
                    const toggleEl = $('#alFilterToggle');
                    if (count > 0) {
                        countEl.text(count).removeClass('d-none');
                        toggleEl.addClass('has-active');
                    } else {
                        countEl.addClass('d-none');
                        toggleEl.removeClass('has-active');
                    }

                    const summaryText = parts.length > 0 ? parts.join(' • ') : 'Tap to filter by party, month, year...';
                    $('#alFilterSummaryText').text(summaryText);
                }

                $(document).on('click', '#alFilterToggle, #alFilterSummaryTrigger, .al-open-sheet-btn', function() {
                    openLedgerSheet();
                });

                $(document).on('click', '#alFilterSheetClose, #alFilterBackdrop, #alFilterApply', function() {
                    closeLedgerSheet();
                });

                $('#alFilterClear').on('click', function() {
                    $('#type').val('').trigger('change.select2');
                    $('#customer_id').val('').trigger('change.select2');
                    $('#vendor_id').val('').trigger('change.select2');
                    $('#month').val('').trigger('change.select2');
                    $('#year').val(new Date().getFullYear()).trigger('change.select2');
                    $('#customerDropdown').hide();
                    $('#vendorDropdown').hide();
                    updateFilterSummary();
                    fetchPayments();
                });

                $(document).on('click', '.al-m-tool[data-al-proxy]', function() {
                    const target = $(this).data('al-proxy');
                    if (target) $(target).trigger('click');
                });

                $('#type, #customer_id, #vendor_id, #month, #year').on('change', updateFilterSummary);

                window.addEventListener('resize', placeFilterFields);
                if (filterQuery.addEventListener) {
                    filterQuery.addEventListener('change', placeFilterFields);
                } else if (filterQuery.addListener) {
                    filterQuery.addListener(placeFilterFields);
                }
                placeFilterFields();
                updateFilterSummary();
            })();

            window.handleLedgerResize = handleLedgerResize;

        });
    </script>
@endpush
