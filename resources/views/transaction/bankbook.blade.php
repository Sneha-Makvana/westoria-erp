@extends('layout.app')

@section('title', 'Bank Book')

@section('content')

    <style>
        .form-control-sm {
            height: 32px;
            font-size: 13px;
        }

        .btn-sm {
            height: 32px;
            font-size: 14px;
        }

        /* Bank Book Top Tabs */
        .bankbook-top-tabs {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 6px;
            margin-bottom: 0;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 10px;
            border: none;
        }

        .bankbook-top-tabs::-webkit-scrollbar {
            display: none;
        }

        .bankbook-top-tabs .nav-item {
            flex: 0 0 auto;
        }

        .bankbook-top-tabs .nav-link {
            border-radius: 8px !important;
            padding: 7px 18px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #64748b !important;
            border: none !important;
            background: transparent !important;
            transition: all 0.2s ease;
            white-space: nowrap;
            text-align: center;
        }

        .bankbook-top-tabs .nav-link.active {
            background: #ff9f43 !important;
            color: #fff !important;
            box-shadow: 0 2px 6px rgba(255, 159, 67, 0.35) !important;
        }

        /* Filter Controls */
        .bb-input {
            height: 38px !important;
            border-radius: 8px !important;
            border: 1px solid #d0d5dd !important;
            font-size: 13px !important;
            padding: 0 10px !important;
            background: #fff;
        }

        .bb-total-box {
            height: 38px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-left: 4px solid #ff9f43;
            border-radius: 8px;
            padding: 0 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .bb-total-label {
            font-size: 12px;
            font-weight: 600;
            color: #7c2d12;
        }

        .bb-total-val {
            font-size: 15px;
            font-weight: 700;
            color: #ea580c;
        }

        .bb-tools-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .bb-tool-btn {
            height: 38px;
            border-radius: 8px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .bb-tool-btn.is-excel {
            background: #e7f6ec;
            color: #198754;
            border: 1px solid #c3e6cb;
        }
        .bb-tool-btn.is-excel:hover {
            background: #198754;
            color: #fff;
        }

        .bb-tool-btn.is-pdf {
            background: #fdecee;
            color: #dc3545;
            border: 1px solid #f5c6cb;
        }
        .bb-tool-btn.is-pdf:hover {
            background: #dc3545;
            color: #fff;
        }

        .bb-search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .bb-search-icon {
            position: absolute;
            left: 12px;
            color: #94a3b8;
            font-size: 13px;
            pointer-events: none;
        }

        .bb-search-input {
            height: 38px !important;
            border-radius: 8px !important;
            border: 1px solid #d0d5dd !important;
            padding-left: 34px !important;
            font-size: 13px !important;
            width: 100%;
        }

        /* Mobile & Tablet Transaction Cards (< 1200px) - Compact Size */
        #mobile-bankbook-cards {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin-bottom: 12px;
        }

        .transaction-m-card {
            background: #fff;
            border: 1px solid #eaecf0;
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 0;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.04);
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .transaction-m-card:hover {
            box-shadow: 0 3px 10px rgba(16, 24, 40, 0.08);
            border-color: #cbd5e1;
        }

        .transaction-m-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .transaction-m-ref-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 1.5px 6px;
            line-height: 1.2;
        }

        .transaction-m-ref-badge .ref-type-label {
            color: #64748b;
            font-weight: 600;
            font-size: 10.5px;
        }

        .transaction-m-amount {
            font-size: 13.5px;
            font-weight: 700;
            color: #ea580c;
            white-space: nowrap;
        }

        .transaction-m-body {
            display: flex;
            flex-direction: column;
            gap: 3.5px;
        }

        .transaction-m-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            font-size: 11.5px;
            line-height: 1.3;
        }

        .transaction-m-label {
            color: #64748b;
            font-weight: 500;
            font-size: 11px;
            flex-shrink: 0;
        }

        .transaction-m-val {
            color: #1e293b;
            font-size: 11.5px;
            text-align: right;
            word-break: break-word;
        }

        .transaction-m-badge-bank {
            font-size: 10.5px;
            font-weight: 600;
            background: #eff6ff;
            color: #1d4ed8;
            padding: 1px 6px;
            border-radius: 4px;
            border: 1px solid #bfdbfe;
            display: inline-block;
            line-height: 1.3;
        }

        .transaction-m-remarks {
            font-size: 11px;
            color: #475569;
            background: #f8fafc;
            border-radius: 5px;
            padding: 4px 8px;
            margin-top: 2px;
            line-height: 1.3;
            word-break: break-word;
            border-left: 2px solid #cbd5e1;
        }

        .transaction-m-remarks .remarks-label {
            font-weight: 600;
            color: #64748b;
        }

        .transaction-m-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
            margin-top: auto;
            padding-top: 6px;
            border-top: 1px dashed #e2e8f0;
        }

        .transaction-m-btn-edit {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            height: 25px;
            padding: 1px 8px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 600;
            background: #fff7ed;
            color: #ea580c;
            border: 1px solid #fed7aa;
            line-height: 1;
        }

        .transaction-m-btn-edit:hover {
            background: #ff9f43;
            color: #fff;
        }

        .transaction-m-btn-delete {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            height: 25px;
            padding: 1px 8px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 600;
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            line-height: 1;
        }

        .transaction-m-btn-delete:hover {
            background: #dc2626;
            color: #fff;
        }

        .transaction-m-btn-edit i,
        .transaction-m-btn-delete i {
            font-size: 10px;
        }

        .transaction-empty-card {
            grid-column: 1 / -1;
            background: #fff;
            border-radius: 10px;
            border: 1px dashed #cbd5e1;
            padding: 24px 16px;
            text-align: center;
            margin: 8px 0;
        }

        /* Tablet 2-column card grid (640px to 1199.98px) */
        @media screen and (min-width: 640px) and (max-width: 1199.98px) {
            #mobile-bankbook-cards {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 10px !important;
            }

            .transaction-m-card {
                margin-bottom: 0 !important;
            }
        }

        /* Hide details toggle column everywhere since it is replaced by clean layout */
        .details-column,
        table.datanew th.details-column,
        table.datanew tbody td:nth-child(6) {
            display: none !important;
        }

        /* Hide DataTables default controls */
        .dt-buttons,
        .dataTables_filter,
        .dataTables_length {
            display: none !important;
        }

        /* Pagination custom styles */
        .pagination .page-item .page-link {
            background-color: #5d6d7e;
            color: #fff;
            border: none;
            margin: 0 3px;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: bold;
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

        .pagination .page-item.disabled .page-link {
            background-color: #fff !important;
            color: #dee2e6 !important;
            border: 1px solid #dee2e6 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }

        /* Mobile Header & Tools (<1200px) */
        .bb-m-head-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            background: #fff;
            border-radius: 12px;
            padding: 12px 14px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
        }

        .bb-m-title {
            font-size: 17px !important;
            font-weight: 700 !important;
            color: #1b2850 !important;
        }

        .bb-m-head-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bb-m-tools {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 10px;
        }

        .bb-m-tool {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 36px;
            padding: 0 10px;
            border: 0;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .bb-m-tool.is-green {
            background: #e7f6ec;
            color: #198754;
        }
        .bb-m-tool.is-green:hover {
            background: #198754;
            color: #fff;
        }

        .bb-m-tool.is-red {
            background: #fdecee;
            color: #dc3545;
        }
        .bb-m-tool.is-red:hover {
            background: #dc3545;
            color: #fff;
        }

        /* Mobile Total Balance Card */
        .bb-m-total-card {
            background: #fff;
            border: 1px solid #fed7aa;
            border-left: 4px solid #ff9f43;
            border-radius: 10px;
            padding: 10px 14px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .06);
        }

        .bb-m-total-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .bb-m-total-label {
            font-size: 13px;
            font-weight: 600;
            color: #7c2d12;
        }

        .bb-m-total-val {
            font-size: 16px;
            font-weight: 700;
            color: #ea580c;
        }

        /* Mobile Filter Trigger Bar (<1200px) */
        .bb-filter-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border-radius: 12px;
            padding: 8px 10px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
        }

        .bb-filter-summary {
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

        .bb-filter-summary-text {
            font-size: 12.5px;
            color: #475467;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bb-filter-toggle {
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
            background: #ff9f43;
            color: #fff;
            font-size: 15px;
            box-shadow: 0 2px 6px rgba(255, 159, 67, .35);
            cursor: pointer;
        }

        .bb-filter-toggle.has-active {
            background: #ea580c;
        }

        .bb-filter-count {
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
        .bb-filter-backdrop {
            display: block;
            position: fixed;
            inset: 0;
            z-index: 1040;
            background: rgba(16, 24, 40, .45);
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
        }

        .bb-filter-sheet {
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

        body.bb-filter-open .bb-filter-backdrop {
            opacity: 1;
            pointer-events: auto;
        }

        body.bb-filter-open .bb-filter-sheet {
            transform: translateY(0);
        }

        body.bb-filter-open {
            overflow: hidden;
        }

        body.bb-filter-open .mobile-bottom-nav,
        body.bb-filter-open .floating-calculator-wrapper {
            display: none !important;
        }

        .bb-filter-sheet-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 18px;
            border-bottom: 1px solid #eef0f4;
            font-size: 16px;
            font-weight: 600;
            color: #1b2850;
        }

        .bb-filter-sheet-close {
            width: 32px;
            height: 32px;
            padding: 0;
            border: 0;
            background: transparent;
            color: #64748b;
            font-size: 20px;
            cursor: pointer;
        }

        .bb-filter-sheet-body {
            flex: 1 1 auto;
            overflow-y: auto;
            padding: 14px 18px;
        }

        .bb-filter-sheet-body #bankbookFilterForm .row {
            margin: 0 !important;
        }

        .bb-filter-sheet-body #bankbookFilterForm .filter-col {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
            padding: 0 !important;
            margin-bottom: 12px !important;
        }

        .bb-filter-sheet-body .bankbook-total-col,
        .bb-filter-sheet-body .bankbook-export-col {
            display: none !important;
        }

        .bb-filter-sheet-body .filter-col-search {
            order: -1 !important;
            margin-top: 0 !important;
        }

        .bb-filter-sheet-body label {
            display: block;
            margin-bottom: 6px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #344054 !important;
        }

        .bb-filter-sheet-body .bb-input,
        .bb-filter-sheet-body .bb-search-input {
            height: 42px !important;
            border-radius: 8px !important;
            border-color: #d0d5dd !important;
            font-size: 14px !important;
            width: 100% !important;
        }

        .bb-filter-sheet-foot {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            padding: 14px 18px calc(14px + env(safe-area-inset-bottom));
            border-top: 1px solid #eef0f4;
            background: #fff;
        }

        .bb-filter-clear,
        .bb-filter-apply {
            height: 44px;
            border: 0;
            border-radius: 22px;
            font-size: 14.5px;
            font-weight: 600;
            cursor: pointer;
        }

        .bb-filter-clear {
            background: #f1f5f9;
            color: #475467;
        }

        .bb-filter-apply {
            background: #ff9f43;
            color: #fff;
        }

        /* Tablets & Mobile (< 1200px) */
        @media screen and (max-width: 1199.98px) {
            .content {
                padding: 12px 12px 95px !important;
                max-width: 100vw;
            }

            .card {
                border-radius: 12px !important;
                border: 1px solid #edf2f7;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .06);
            }

            .card-body {
                padding: 14px !important;
            }

            .table-responsive {
                display: none !important;
            }

            #mobile-bankbook-cards {
                display: grid !important;
            }

            .pagination-controls {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 10px;
            }

            .pagination-controls > div {
                justify-content: space-between;
                width: 100%;
            }

            .pagination-controls nav .pagination {
                justify-content: center;
                flex-wrap: wrap;
            }

            .bankbook-header-action,
            .btn-added {
                height: 32px;
                padding: 0 10px !important;
                font-size: 12px !important;
                border-radius: 8px !important;
            }
        }

        /* Tablets (768px - 1199.98px) sheet centering */
        @media screen and (min-width: 768px) and (max-width: 1199.98px) {
            .bb-filter-sheet {
                max-width: 580px;
                left: 50% !important;
                right: auto !important;
                transform: translate(-50%, 105%);
                border-radius: 16px 16px 0 0;
                width: 100%;
            }

            body.bb-filter-open .bb-filter-sheet {
                transform: translate(-50%, 0) !important;
            }
        }

        /* Dark Mode Overrides */
        :root[data-theme="dark"] .bb-m-head-top,
        :root[data-theme="dark"] .bb-filter-bar,
        :root[data-theme="dark"] .bb-filter-sheet,
        :root[data-theme="dark"] .bb-m-total-card,
        :root[data-theme="dark"] .transaction-m-card,
        :root[data-theme="dark"] .transaction-empty-card {
            background: #1e293b !important;
            border-color: #334155 !important;
        }

        :root[data-theme="dark"] .bb-m-title,
        :root[data-theme="dark"] .bb-filter-sheet-head,
        :root[data-theme="dark"] .bb-m-total-label,
        :root[data-theme="dark"] .transaction-m-val {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .bb-filter-summary,
        :root[data-theme="dark"] .transaction-m-ref-badge {
            background: #0f172a !important;
            border-color: #334155 !important;
        }

        :root[data-theme="dark"] .bb-filter-summary-text,
        :root[data-theme="dark"] .bb-filter-sheet-body label {
            color: #94a3b8 !important;
        }

        :root[data-theme="dark"] .bb-filter-sheet-head,
        :root[data-theme="dark"] .bb-filter-sheet-foot,
        :root[data-theme="dark"] .transaction-m-card-header,
        :root[data-theme="dark"] .transaction-m-footer {
            border-color: #334155 !important;
            background: #1e293b !important;
        }

        :root[data-theme="dark"] .transaction-m-remarks {
            background: #0f172a !important;
            color: #cbd5e1 !important;
            border-color: #475569 !important;
        }

        :root[data-theme="dark"] .bb-m-tool.is-green { background: #064e3b; color: #6ee7b7; }
        :root[data-theme="dark"] .bb-m-tool.is-red { background: #7f1d1d; color: #fca5a5; }

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
            font-size: 30px;
            color: #2c3e50;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .download-loader-box h4 {
                font-size: 24px;
            }
        }
    </style>

    <!-- Mobile Filter Backdrop & Bottom Sheet (<1200px) -->
    <div class="bb-filter-backdrop d-xl-none" id="bbFilterBackdrop"></div>
    <div class="bb-filter-sheet d-xl-none" id="bbFilterSheet" role="dialog" aria-modal="true" aria-labelledby="bbFilterSheetTitle">
        <div class="bb-filter-sheet-head">
            <span id="bbFilterSheetTitle"><i class="fas fa-sliders-h me-2 text-warning"></i>Filter Bank Book</span>
            <button type="button" class="bb-filter-sheet-close" id="bbFilterSheetClose" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="bb-filter-sheet-body" id="bbFilterSheetBody"></div>
        <div class="bb-filter-sheet-foot">
            <button type="button" class="bb-filter-clear" id="bbFilterClear">Reset</button>
            <button type="button" class="bb-filter-apply" id="bbFilterApply">Apply Filters</button>
        </div>
    </div>

    <div class="content">
        <!-- Mobile Header (<1200px) -->
        <div class="bb-m-head d-xl-none mb-2">
            <div class="bb-m-head-top">
                <h4 class="bb-m-title mb-0">Bank Book</h4>
                <div class="bb-m-head-actions">
                    <button type="button" class="btn btn-sm btn-primary bankbook-header-action" id="openBankBookEntryModalMobile">
                        <i class="fa fa-plus me-1"></i> Bank Book
                    </button>
                    <a href="{{ route('auth.dashboard') }}" class="btn btn-sm btn-added">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="bb-m-tools mt-2">
                <button type="button" class="bb-m-tool is-green" data-bb-proxy="#exportExcel">
                    <i class="fa fa-file-excel me-1"></i><span>Excel</span>
                </button>
                <button type="button" class="bb-m-tool is-red" data-bb-proxy="#exportPdf">
                    <i class="fa fa-file-pdf me-1"></i><span>PDF</span>
                </button>
            </div>
        </div>

        <!-- Desktop Header (>=1200px) -->
        <div class="page-header d-none d-xl-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100">
                <div class="page-title mb-0">
                    <h4 class="mb-0">Bank Book</h4>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-primary btn-sm bankbook-header-action"
                        id="openBankBookEntryModal">
                        <i class="fa fa-plus me-1"></i> Bank Book
                    </button>
                    <a href="{{ route('auth.dashboard') }}" class="btn btn-added">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-top mb-3">
                    <div class="w-100">
                        {{-- Tabs --}}
                        <div class="mb-3">
                            <ul class="nav nav-tabs nav-tabs-solid bankbook-top-tabs" id="bankBookTabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-status="credit" href="javascript:void(0)">Receipts</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-status="debit" href="javascript:void(0)">Payments</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-status="expense" href="javascript:void(0)">Expenses</a>
                                </li>
                            </ul>
                        </div>

                        <!-- Mobile Total Balance Card (<1200px) -->
                        <div class="bb-m-total-card d-xl-none mb-2">
                            <div class="bb-m-total-inner">
                                <span class="bb-m-total-label">Total:</span>
                                <span class="bb-m-total-val">₹<span id="grandClosingBalanceMobile">0.00</span></span>
                            </div>
                        </div>

                        <!-- Mobile Filter Bar (<1200px) -->
                        <div class="bb-filter-bar d-xl-none mb-3">
                            <div class="bb-filter-summary" id="bbFilterSummaryTrigger">
                                <i class="fas fa-filter text-muted me-1"></i>
                                <span class="bb-filter-summary-text" id="bbFilterSummaryText">Tap to filter by search, date, year, bank...</span>
                            </div>
                            <button type="button" class="bb-filter-toggle" id="bbFilterToggle" aria-label="Filters">
                                <i class="fas fa-sliders-h"></i>
                                <span class="bb-filter-count d-none" id="bbFilterCount">0</span>
                            </button>
                        </div>

                        <!-- Desktop Filter Container (>=1200px) -->
                        <div id="desktopFilterContainer" class="d-none d-xl-block mb-3">
                            <form id="bankbookFilterForm">
                                <div class="row g-2 align-items-end bankbook-filter-row">
                                    <!-- Start Date -->
                                    <div class="col-xl-2 col-md-3 col-6 filter-col">
                                        <label for="fromDate" class="form-label fw-bold mb-1" style="font-size:12px;">From Date</label>
                                        <input type="date" id="fromDate" class="form-control form-control-sm bb-input">
                                    </div>

                                    <!-- End Date -->
                                    <div class="col-xl-2 col-md-3 col-6 filter-col">
                                        <label for="toDate" class="form-label fw-bold mb-1" style="font-size:12px;">To Date</label>
                                        <input type="date" id="toDate" class="form-control form-control-sm bb-input">
                                    </div>

                                    <!-- Year -->
                                    <div class="col-xl-2 col-md-3 col-6 filter-col">
                                        <label for="filterYear" class="form-label fw-bold mb-1" style="font-size:12px;">Year</label>
                                        <select id="filterYear" class="form-control form-control-sm bb-input">
                                            <option value="">-- Select Year --</option>
                                            @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <!-- Bank Filter -->
                                    <div class="col-xl-2 col-md-3 col-6 filter-col">
                                        <label for="bankFilter" class="form-label fw-bold mb-1" style="font-size:12px;">Bank</label>
                                        <select id="bankFilter" class="form-control form-control-sm bb-input">
                                            <option value="">-- All Banks --</option>
                                        </select>
                                    </div>

                                    <!-- Total -->
                                    <div class="col-xl-2 col-md-6 col-12 bankbook-total-col filter-col">
                                        <label class="form-label fw-bold mb-1" style="font-size:12px;">Total</label>
                                        <div class="bb-total-box">
                                            <span class="bb-total-label">Total:</span>
                                            <span class="bb-total-val">₹<span id="grandClosingBalance">0.00</span></span>
                                        </div>
                                    </div>

                                    <!-- Excel & PDF -->
                                    <div class="col-xl-2 col-md-6 col-12 bankbook-export-col filter-col">
                                        <label class="form-label d-none d-lg-block opacity-0 mb-1" style="font-size:12px;">&nbsp;</label>
                                        <div class="bb-tools-grid">
                                            <button type="button" class="bb-tool-btn is-excel" id="exportExcel">
                                                <i class="fa fa-file-excel me-1"></i> Excel
                                            </button>
                                            <button type="button" class="bb-tool-btn is-pdf" id="exportPdf">
                                                <i class="fa fa-file-pdf me-1"></i> PDF
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Search Input --}}
                                    <div class="col-xl-4 col-md-6 col-12 filter-col filter-col-search mt-2">
                                        <label for="search-input" class="form-label fw-bold mb-1 d-xl-none" style="font-size:12px;">Search</label>
                                        <div class="bb-search-wrapper">
                                            <i class="fas fa-search bb-search-icon"></i>
                                            <input type="text" id="search-input" class="form-control form-control-sm bb-search-input"
                                                placeholder="Search by name, phone, order no…">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                {{-- Table (Desktop only >=1200px) --}}
                <div class="table-responsive mt-3 d-none d-xl-block" style="overflow-x: auto;">
                    <table class="table datanew" id="bankBookTable">
                        <thead>
                            <tr>
                                <th id="refColumnTitle">Order No</th>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Bank Name</th>
                                <th>Amount</th>
                                <th class="details-column">Details</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                {{-- Mobile Cards Container (< 1200px) --}}
                <div class="d-xl-none mt-2" id="mobile-bankbook-cards">
                    {{-- Dynamically populated --}}
                </div>

                {{-- Pagination Controls --}}
                <div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3"
                    style="display: none;">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
                        <select id="per-page-select" class="form-select form-select-sm"
                            style="width: auto; border: 1px solid #ddd; border-radius: 6px;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="ms-3" style="font-size: 14px; color: #555;">
                            <span id="pagination-from">0</span> - <span id="pagination-to">0</span> of <span
                                id="pagination-total">0</span> items
                        </span>
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0" id="pagination-numbers">
                            {{-- Page numbers inserted here --}}
                        </ul>
                    </nav>
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

    {{-- Add Bank Book Entry Modal --}}
    <div class="modal fade" id="bankbookEntryModal" tabindex="-1" aria-labelledby="bankbookEntryLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background:#ff9f43;">
                    <h5 class="modal-title text-white" id="bankbookEntryLabel"><i class="fas fa-university me-2"></i>Add Bank Book Entry</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Type</label>
                        <select id="bb_entry_type" class="form-control">
                            <option value="credit">Receipts</option>
                            <option value="debit">Payments</option>
                            <option value="expense">Expenses</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Transaction ID</label>
                        <input type="text" id="bb_entry_transaction_id" class="form-control" placeholder="Transaction ID">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="date" id="bb_entry_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Bank Name</label>
                        <select id="bb_entry_bank_id" class="form-control">
                            <option value="">Select Bank</option>
                            @foreach ($banks as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount</label>
                        <input type="number" id="bb_entry_amount" class="form-control" step="0.01" min="0" placeholder="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="bb_save_entry_btn">Save Entry</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Payment Modal (Bankbook) --}}
    <div class="modal fade" id="bankbookEditModal" tabindex="-1" aria-labelledby="bankbookEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background:#ff9f43;">
                    <h5 class="modal-title text-white" id="bankbookEditLabel"><i class="fas fa-edit me-2"></i>Edit Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="bb_edit_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="date" id="bb_edit_date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount (₹)</label>
                        <input type="number" id="bb_edit_amount" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Remarks</label>
                        <textarea id="bb_edit_remarks" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="bb_save_edit_btn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

    <script>
        function renderMobileBankBookCards(data, status) {
            const $container = $('#mobile-bankbook-cards');
            if (!data || data.length === 0) {
                $container.html(`
                    <div class="transaction-empty-card">
                        <i class="fas fa-receipt fa-2x mb-2" style="color: #cbd5e1;"></i>
                        <p class="mb-0 text-muted fw-semibold" style="font-size: 13.5px;">No transactions found</p>
                    </div>
                `);
                return;
            }

            let refLabel = (status === 'debit') ? 'Bill No' : (status === 'expense' ? 'Expense No' : 'Order No');
            let html = '';
            data.forEach((row) => {
                let amount = parseFloat(row.payment_amount) || 0;
                let refNo = (row.status === 'debit' || row.status === 'expense') ? row.order_no : row.invoice_no;
                let remarks = row.remarks || '';
                let remarksEscaped = remarks.replace(/"/g, '&quot;');

                html += `
                    <div class="transaction-m-card">
                        <div class="transaction-m-card-header">
                            <div class="transaction-m-ref-badge">
                                <span class="ref-type-label">${refLabel}:</span>
                                <span class="ref-value">${refNo || '-'}</span>
                            </div>
                            <div class="transaction-m-amount">
                                ₹${formatINR(amount)}
                            </div>
                        </div>
                        <div class="transaction-m-body">
                            <div class="transaction-m-row">
                                <span class="transaction-m-label"><i class="far fa-calendar-alt me-1 text-muted"></i> Date:</span>
                                <span class="transaction-m-val">${row.payment_date || '-'}</span>
                            </div>
                            <div class="transaction-m-row">
                                <span class="transaction-m-label"><i class="far fa-user me-1 text-muted"></i> Particulars:</span>
                                <span class="transaction-m-val fw-semibold text-dark">${row.particulars || 'N/A'}</span>
                            </div>
                            ${row.bank_name ? `
                            <div class="transaction-m-row">
                                <span class="transaction-m-label"><i class="fas fa-university me-1 text-muted"></i> Bank:</span>
                                <span class="transaction-m-badge-bank">${row.bank_name}</span>
                            </div>` : ''}
                            ${remarks ? `
                            <div class="transaction-m-remarks">
                                <span class="remarks-label">Remarks:</span> ${remarks}
                            </div>` : ''}
                        </div>
                        <div class="transaction-m-footer">
                            <button class="btn btn-sm transaction-m-btn-edit bb-edit-btn"
                                data-id="${row.id}"
                                data-amount="${row.payment_amount}"
                                data-date="${row.payment_date}"
                                data-remarks="${remarksEscaped}"
                                title="Edit">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                            <button class="btn btn-sm transaction-m-btn-delete bb-delete-btn"
                                data-id="${row.id}"
                                title="Delete">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </div>
                    </div>
                `;
            });
            $container.html(html);
        }
    </script>
    <script>
        let bankBookTable;
        let currentStatus = 'credit';
        let currentPage = 1;
        let lastPage = 1;
        let perPage = 10;
        let searchQuery = '';

        function formatINR(amount) {
            return Number(amount).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        $(document).ready(function() {
            const $downloadLoader = $("#downloadLoaderOverlay");
            const $downloadLoaderText = $("#downloadLoaderText");
            const $exportButtons = $("#exportExcel, #exportPdf");

            function toggleDownloadLoader(isLoading, message) {
                if (isLoading) {
                    $downloadLoaderText.text(message || "Generating report...");
                    $downloadLoader.removeClass("d-none");
                    $exportButtons.prop("disabled", true).addClass("disabled").attr("aria-disabled", "true");
                } else {
                    $downloadLoader.addClass("d-none");
                    $exportButtons.prop("disabled", false).removeClass("disabled").removeAttr("aria-disabled");
                }
            }

            // Initialize DataTable as a display container only
            bankBookTable = $('#bankBookTable').DataTable({
                destroy: true,
                ordering: false,
                searching: false,
                paging: false,
                info: false,
                lengthChange: false,
                autoWidth: false,
                dom: 't',
                buttons: [], // buttons will be handled separately
                columnDefs: [
                    { targets: 5, orderable: false },
                    { targets: 6, orderable: false, className: 'text-center' }
                ]
            });

            loadBanks();
            loadBankBook(currentPage);
            initMobileFilterSheet();

            // Mobile bottom sheet and filter placement
            function initMobileFilterSheet() {
                const sheetBody = document.getElementById('bbFilterSheetBody');
                const desktopContainer = document.getElementById('desktopFilterContainer');
                const form = document.getElementById('bankbookFilterForm');
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
                            closeBankBookSheet();
                        }
                    }
                }

                function openBankBookSheet() {
                    placeFilterFields();
                    document.body.classList.add('bb-filter-open');
                    $('#bbFilterToggle').attr('aria-expanded', 'true');
                }

                function closeBankBookSheet() {
                    document.body.classList.remove('bb-filter-open');
                    $('#bbFilterToggle').attr('aria-expanded', 'false');
                }

                window.closeBankBookSheet = closeBankBookSheet;

                function updateFilterSummary() {
                    const search = ($('#search-input').val() || '').trim();
                    const fromDate = $('#fromDate').val();
                    const toDate = $('#toDate').val();
                    const year = $('#filterYear').val();
                    const bank = $('#bankFilter').val();
                    const bankText = $('#bankFilter option:selected').text().trim();

                    let count = 0;
                    let parts = [];

                    if (search) {
                        count++;
                        parts.push(`"${search}"`);
                    }
                    if (fromDate) {
                        count++;
                        parts.push(`From: ${fromDate}`);
                    }
                    if (toDate) {
                        count++;
                        parts.push(`To: ${toDate}`);
                    }
                    if (year) {
                        count++;
                        parts.push(year);
                    }
                    if (bank && bank !== '') {
                        count++;
                        parts.push(bankText !== '-- All Banks --' && bankText !== 'All Banks' ? bankText : 'Bank');
                    }

                    const countEl = $('#bbFilterCount');
                    const toggleEl = $('#bbFilterToggle');
                    if (count > 0) {
                        countEl.text(count).removeClass('d-none');
                        toggleEl.addClass('has-active');
                    } else {
                        countEl.addClass('d-none');
                        toggleEl.removeClass('has-active');
                    }

                    const summaryText = parts.length > 0 ? parts.join(' • ') : 'Tap to filter by search, date, year, bank...';
                    $('#bbFilterSummaryText').text(summaryText);
                }

                window.updateBankBookFilterSummary = updateFilterSummary;

                $(document).on('click', '#bbFilterToggle, #bbFilterSummaryTrigger, .bb-open-sheet-btn', function() {
                    openBankBookSheet();
                });

                $(document).on('click', '#bbFilterSheetClose, #bbFilterBackdrop', function() {
                    closeBankBookSheet();
                });

                $('#bbFilterClear').on('click', function() {
                    $('#search-input').val('');
                    searchQuery = '';
                    $('#fromDate').val('');
                    $('#toDate').val('');
                    $('#filterYear').val('');
                    $('#bankFilter').val('');
                    updateFilterSummary();
                    loadBankBook(1);
                });

                $('#bbFilterApply').on('click', function() {
                    closeBankBookSheet();
                    searchQuery = ($('#search-input').val() || '').trim();
                    updateFilterSummary();
                    loadBankBook(1);
                });

                $(document).on('click', '.bb-m-tool[data-bb-proxy]', function() {
                    const target = $(this).data('bb-proxy');
                    if (target) $(target).trigger('click');
                });

                $('#fromDate, #toDate, #filterYear, #bankFilter').on('change', function() {
                    updateFilterSummary();
                    if (!document.body.classList.contains('bb-filter-open')) {
                        loadBankBook(1);
                    }
                });

                $('#search-input').on('input keyup', function() {
                    updateFilterSummary();
                });

                window.addEventListener('resize', placeFilterFields);
                if (filterQuery.addEventListener) {
                    filterQuery.addEventListener('change', placeFilterFields);
                } else if (filterQuery.addListener) {
                    filterQuery.addListener(placeFilterFields);
                }

                placeFilterFields();
                updateFilterSummary();
            }

            $('#openBankBookEntryModal, #openBankBookEntryModalMobile').on('click', function() {
                const defaultEntryType = currentStatus === 'expense' ? 'expense' : (currentStatus === 'debit' ? 'debit' : 'credit');
                $('#bb_entry_type').val(defaultEntryType);
                $('#bb_entry_transaction_id').val('');
                $('#bb_entry_date').val('{{ date('Y-m-d') }}');
                $('#bb_entry_bank_id').val('');
                $('#bb_entry_amount').val('');
                $('#bankbookEntryModal').modal('show');
            });

            $('#bb_save_entry_btn').on('click', function() {
                const $button = $(this);
                const defaultText = $button.text();
                const entryType = $('#bb_entry_type').val() || 'credit';

                $button.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: "{{ url('/api/bankbook/manual-entry') }}",
                    method: 'POST',
                    headers: {
                        Authorization: "Bearer " + localStorage.getItem("authToken")
                    },
                    data: {
                        entry_type: entryType,
                        transaction_id: $('#bb_entry_transaction_id').val(),
                        payment_date: $('#bb_entry_date').val(),
                        bank_id: $('#bb_entry_bank_id').val(),
                        payment_amount: $('#bb_entry_amount').val(),
                        status: entryType === 'debit' ? 'debit' : 'credit',
                        selectedSubAdminId: localStorage.getItem("selectedSubAdminId")
                    },
                    success: function(res) {
                        if (res.status) {
                            $('#bankbookEntryModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved',
                                text: res.message || 'Bank book entry added successfully.',
                                confirmButtonColor: '#ff9f43',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            currentStatus = entryType;
                            $('#bankBookTabs .nav-link').removeClass('active');
                            $(`#bankBookTabs .nav-link[data-status="${entryType}"]`).addClass('active');
                            if (currentStatus === 'debit') {
                                $('#refColumnTitle').text('Bill No');
                            } else if (currentStatus === 'expense') {
                                $('#refColumnTitle').text('Expense No');
                            } else {
                                $('#refColumnTitle').text('Order No');
                            }
                            loadBankBook(1);
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Could not save bank book entry.',
                            confirmButtonColor: '#ff9f43'
                        });
                    },
                    complete: function() {
                        $button.prop('disabled', false).text(defaultText);
                    }
                });
            });

            // Search input handler
            let searchTimer;
            $('#search-input').on('keyup', function() {
                if (!document.body.classList.contains('bb-filter-open')) {
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(() => {
                        searchQuery = $(this).val();
                        loadBankBook(1);
                    }, 500);
                }
            });

            // Per‑page change handler
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                loadBankBook(1);
            });

            // Page click handler
            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    loadBankBook(page);
                }
            });

            // Tabs click
            $('#bankBookTabs .nav-link').on('click', function() {
                $('#bankBookTabs .nav-link').removeClass('active');
                $(this).addClass('active');
                currentStatus = $(this).data('status');
                if (currentStatus === 'debit') {
                    $('#refColumnTitle').text('Bill No');
                } else if (currentStatus === 'expense') {
                    $('#refColumnTitle').text('Expense No');
                } else {
                    $('#refColumnTitle').text('Order No');
                }
                loadBankBook(1);
            });

            $('#exportExcel').click(function() {
                if ($(this).prop("disabled")) {
                    return;
                }
                let params = {
                    from_date: $('#fromDate').val(),
                    to_date: $('#toDate').val(),
                    year: $('#filterYear').val(),
                    bank_id: $('#bankFilter').val(),
                    status: currentStatus,
                    search: searchQuery,
                    selectedSubAdminId: localStorage.getItem("selectedSubAdminId")
                    //  token: localStorage.getItem("authToken")
                };
                let queryString = $.param(params);
                let url = "{{ url('/api/export-bankbook-excel') }}?" + queryString;

                $.ajax({
                    url: url,
                    method: 'GET',
                    beforeSend: function() {
                        toggleDownloadLoader(true, "Generating Excel...");
                    },
                    headers: {
                        Authorization: "Bearer " + localStorage.getItem("authToken")
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(blob, status, xhr) {
                        let filename = 'bankbook_export.xml'; // default (changed from .xlsx)
                        let contentDisposition = xhr.getResponseHeader('Content-Disposition');
                        if (contentDisposition) {
                            let match = contentDisposition.match(
                                /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                            if (match && match[1]) {
                                filename = match[1].replace(/['"]/g, '');
                            }
                        }
                        let downloadUrl = window.URL.createObjectURL(blob);
                        let a = document.createElement('a');
                        a.href = downloadUrl;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(downloadUrl);
                        document.body.removeChild(a);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Export Failed',
                            text: xhr.responseJSON?.message ||
                                'Could not download file',
                            confirmButtonColor: '#ff9f43'
                        });
                    },
                    complete: function() {
                        toggleDownloadLoader(false);
                    }
                });
            });


            $('#exportPdf').click(function() {
                if ($(this).prop("disabled")) {
                    return;
                }
                let data = {
                    from_date: $('#fromDate').val(),
                    to_date: $('#toDate').val(),
                    year: $('#filterYear').val(),
                    bank_id: $('#bankFilter').val(),
                    status: currentStatus,
                    search: searchQuery,
                    selectedSubAdminId: localStorage.getItem("selectedSubAdminId")
                };
                $.ajax({
                    url: "{{ url('/api/export-bankbook-pdf') }}",
                    beforeSend: function() {
                        toggleDownloadLoader(true, "Generating PDF...");
                    },
                    headers: {
                        Authorization: "Bearer " + localStorage.getItem("authToken")
                    },
                    data: data,
                    success: function(res) {
                        if (res.success) window.open(res.file_url, '_blank');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Export Failed',
                            text: xhr.responseJSON?.message || 'Could not generate PDF',
                            confirmButtonColor: '#ff9f43'
                        });
                    },
                    complete: function() {
                        toggleDownloadLoader(false);
                    }
                });
            });

            // Toggle details icon
            $(document).on('click', '.toggle-details', function() {
                let icon = $(this).find('i');
                if (icon.hasClass('fa-plus-circle')) {
                    icon.removeClass('fa-plus-circle').addClass('fa-minus-circle').css('color', 'red');
                } else {
                    icon.removeClass('fa-minus-circle').addClass('fa-plus-circle').css('color', '#ff9f43');
                }
            });
        });

        function loadBanks() {
            $.ajax({
                url: "{{ url('/api/banks/data') }}",
                headers: {
                    Authorization: "Bearer " + localStorage.getItem("authToken")
                },
                data: {
                    selectedSubAdminId: localStorage.getItem("selectedSubAdminId")
                },
                success: function(res) {
                    let html = '<option value="">All Banks</option>';
                    if (res.status) {
                        res.data.forEach(bank => {
                            html += `<option value="${bank.id}">${bank.bank_name}</option>`;
                        });
                    }
                    $('#bankFilter').html(html);
                    if (typeof window.updateBankBookFilterSummary === 'function') {
                        window.updateBankBookFilterSummary();
                    }
                }
            });
        }

        function loadBankBook(page = 1) {
            bankBookTable.clear().draw();

            let params = {
                from_date: $('#fromDate').val(),
                to_date: $('#toDate').val(),
                year: $('#filterYear').val(),
                bank_id: $('#bankFilter').val(),
                status: currentStatus,
                search: searchQuery,
                page: page,
                per_page: perPage,
                selectedSubAdminId: localStorage.getItem("selectedSubAdminId")
            };

            $.ajax({
                url: "{{ url('/api/bankbook') }}",
                headers: {
                    Authorization: "Bearer " + localStorage.getItem("authToken")
                },
                data: params,
                success: function(res) {
                    if (!res.data || res.data.length === 0) {
                        bankBookTable.clear().draw();
                        renderMobileBankBookCards([], currentStatus);
                        $('#grandClosingBalance, #grandClosingBalanceMobile').text('0.00');
                        $('.pagination-controls').hide();
                        return;
                    }

                    // Update pagination info
                    currentPage = res.pagination.current_page;
                    lastPage = res.pagination.last_page;
                    updatePaginationUI(res.pagination);

                    // Build rows for desktop/tablet table
                    let tableBody = [];
                    res.data.forEach((row, index) => {
                        let amount = parseFloat(row.payment_amount) || 0;
                        let refNo = (row.status === 'debit' || row.status === 'expense') ? row.order_no : row.invoice_no;
                        let remarksEscaped = (row.remarks || '').replace(/"/g, '&quot;');

                        tableBody.push([
                            `<span class="fw-semibold">${refNo || '-'}</span>`,
                            row.payment_date || '-',
                            row.particulars || 'N/A',
                            row.bank_name || '-',
                            `₹${formatINR(amount)}`,
                            '', // details toggle column (hidden)
                            `<div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-sm btn-warning py-0 px-2 bb-edit-btn"
                                    data-id="${row.id}"
                                    data-amount="${row.payment_amount}"
                                    data-date="${row.payment_date}"
                                    data-remarks="${remarksEscaped}"
                                    title="Edit"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger py-0 px-2 bb-delete-btn"
                                    data-id="${row.id}"
                                    title="Delete"><i class="fas fa-trash"></i></button>
                            </div>`
                        ]);
                    });

                    bankBookTable.clear().rows.add(tableBody).draw();
                    renderMobileBankBookCards(res.data, currentStatus);
                    $('#grandClosingBalance, #grandClosingBalanceMobile').text(formatINR(res.current_tab_total || 0));
                    $('.pagination-controls').show();
                },
                error: function() {
                    bankBookTable.clear().draw();
                    renderMobileBankBookCards([], currentStatus);
                    $('#grandClosingBalance, #grandClosingBalanceMobile').text('0.00');
                    $('.pagination-controls').hide();
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Failed to load data!',
                        confirmButtonColor: '#ff9f43'
                    });
                }
            });
        }

        function updatePaginationUI(pagination) {
            let from = pagination.from || 0;
            let to = pagination.to || 0;
            $('#pagination-from').text(from);
            $('#pagination-to').text(to);
            $('#pagination-total').text(pagination.total);

            let paginationHtml = '';
            let currentPage = pagination.current_page || 1;
            let lastPage = pagination.last_page || 1;
            paginationHtml += `
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0);" data-page="${currentPage - 1}">Previous</a>
                </li>
            `;

            const visiblePageCount = 2;
            let startPage = Math.floor((currentPage - 1) / visiblePageCount) * visiblePageCount + 1;
            let endPage = Math.min(lastPage, startPage + visiblePageCount - 1);

            if (startPage > 1) {
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);" data-page="${startPage - 1}" data-action="prev-group">..</a>
                    </li>
                `;
            }

            for (let i = startPage; i <= endPage; i++) {
                paginationHtml += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                    </li>
                `;
            }

            if (endPage < lastPage) {
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);" data-page="${endPage + 1}" data-action="next-group">..</a>
                    </li>
                `;
            }

            paginationHtml += `
                <li class="page-item ${currentPage === lastPage || lastPage === 0 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0);" data-page="${currentPage + 1}">Next</a>
                </li>
            `;

            $('#pagination-numbers').html(paginationHtml);
        }

        // ── Edit button click ──
        $(document).on('click', '.bb-edit-btn', function () {
            let id      = $(this).data('id');
            let amount  = $(this).data('amount');
            let dateRaw = $(this).data('date');   // dd-mm-yyyy from API
            let remarks = $(this).data('remarks') || '';

            // Convert dd-mm-yyyy → yyyy-mm-dd for date input
            let parts   = String(dateRaw).split('-');
            let isoDate = parts.length === 3 ? `${parts[2]}-${parts[1]}-${parts[0]}` : dateRaw;

            $('#bb_edit_id').val(id);
            $('#bb_edit_amount').val(amount);
            $('#bb_edit_date').val(isoDate);
            $('#bb_edit_remarks').val(remarks);
            $('#bankbookEditModal').modal('show');
        });

        // ── Save edit ──
        $('#bb_save_edit_btn').on('click', function () {
            let id     = $('#bb_edit_id').val();
            let amount = $('#bb_edit_amount').val();
            let date   = $('#bb_edit_date').val();
            let remark = $('#bb_edit_remarks').val();

            if (!amount || !date) {
                Swal.fire({ icon: 'warning', title: 'Required', text: 'Date and Amount are required.', confirmButtonColor: '#ff9f43' });
                return;
            }

            $.ajax({
                url: `{{ url('/api/bankbook-payment') }}/${id}/update`,
                method: 'POST',
                headers: { Authorization: 'Bearer ' + localStorage.getItem('authToken') },
                data: { payment_amount: amount, payment_date: date, remarks: remark, type: currentStatus },
                success: function (res) {
                    if (res.status) {
                        $('#bankbookEditModal').modal('hide');
                        Swal.fire({ icon: 'success', title: 'Updated!', text: res.message, confirmButtonColor: '#ff9f43', timer: 1500, showConfirmButton: false });
                        loadBankBook(currentPage);
                    }
                },
                error: function (xhr) {
                    Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Update failed.', confirmButtonColor: '#ff9f43' });
                }
            });
        });

        // ── Delete button click ──
        $(document).on('click', '.bb-delete-btn', function () {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Delete Payment?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete'
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('/api/bankbook-payment') }}/${id}/delete`,
                        method: 'POST',
                        headers: { Authorization: 'Bearer ' + localStorage.getItem('authToken') },
                        data: { type: currentStatus },
                        success: function (res) {
                            if (res.status) {
                                Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, confirmButtonColor: '#ff9f43', timer: 1500, showConfirmButton: false });
                                loadBankBook(currentPage);
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Delete failed.', confirmButtonColor: '#ff9f43' });
                        }
                    });
                }
            });
        });

    </script>
@endpush
