@extends('layout.app')

@section('title', 'Cashbook')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
    <style>
        .form-control-sm {
            height: 32px;
            font-size: 13px;
        }

        .btn-sm {
            height: 32px;
            font-size: 14px;
        }

        /* Cashbook Top Tabs */
        .cashbook-top-tabs {
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

        .cashbook-top-tabs::-webkit-scrollbar {
            display: none;
        }

        .cashbook-top-tabs .nav-item {
            flex: 0 0 auto;
        }

        .cashbook-top-tabs .nav-link {
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

        .cashbook-top-tabs .nav-link.active {
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

        /* Mobile & Tablet Transaction Cards (< 1200px) - Compact Product Card Style */
        #mobile-cashbook-cards {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
            margin-bottom: 10px;
        }

        .transaction-m-card {
            background: #fff;
            border: 1px solid #eaecf0;
            border-radius: 8px;
            padding: 8px 10px;
            margin-bottom: 0;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .transaction-m-card:hover {
            box-shadow: 0 3px 8px rgba(16, 24, 40, 0.07);
            border-color: #cbd5e1;
        }

        .transaction-m-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            margin-bottom: 4px;
            padding-bottom: 4px;
            border-bottom: 1px dashed #edf2f7;
        }

        .transaction-m-ref-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 10.5px;
            font-weight: 700;
            color: #1e293b;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 1px 5px;
            line-height: 1.2;
        }

        .transaction-m-ref-badge .ref-type-label {
            color: #64748b;
            font-weight: 600;
            font-size: 10px;
        }

        .transaction-m-amount {
            font-size: 13px;
            font-weight: 700;
            color: #ea580c;
            white-space: nowrap;
        }

        .transaction-m-body {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .transaction-m-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            font-size: 11px;
            line-height: 1.25;
        }

        .transaction-m-label {
            color: #64748b;
            font-weight: 500;
            font-size: 10.5px;
            flex-shrink: 0;
        }

        .transaction-m-val {
            color: #1e293b;
            font-size: 11px;
            text-align: right;
            word-break: break-word;
        }

        .transaction-m-remarks {
            font-size: 10.5px;
            color: #475569;
            background: #f8fafc;
            border-radius: 4px;
            padding: 3px 6px;
            margin-top: 2px;
            line-height: 1.2;
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
            gap: 5px;
            margin-top: auto;
            padding-top: 4px;
            border-top: 1px dashed #edf2f7;
        }

        .transaction-m-btn-edit {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            height: 22px;
            padding: 0 7px;
            border-radius: 4px;
            font-size: 10.5px;
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
            height: 22px;
            padding: 0 7px;
            border-radius: 4px;
            font-size: 10.5px;
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
            font-size: 9px;
        }

        .transaction-empty-card {
            grid-column: 1 / -1;
            background: #fff;
            border-radius: 8px;
            border: 1px dashed #cbd5e1;
            padding: 20px 14px;
            text-align: center;
            margin: 6px 0;
        }

        /* Tablet 2-column card grid (640px to 1199.98px) */
        @media screen and (min-width: 640px) and (max-width: 1199.98px) {
            #mobile-cashbook-cards {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 8px !important;
            }

            .transaction-m-card {
                margin-bottom: 0 !important;
            }
        }

        /* Hide details toggle column everywhere since it is replaced by clean layout */
        .details-column,
        table.datatable-cashbook th.details-column,
        table.datatable-cashbook tbody td.details-control {
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
        .cb-m-head-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            background: #fff;
            border-radius: 12px;
            padding: 12px 14px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
        }

        .cb-m-title {
            font-size: 17px !important;
            font-weight: 700 !important;
            color: #1b2850 !important;
        }

        .cb-m-head-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cb-m-tools {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 10px;
        }

        .cb-m-tool {
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

        .cb-m-tool.is-green {
            background: #e7f6ec;
            color: #198754;
        }
        .cb-m-tool.is-green:hover {
            background: #198754;
            color: #fff;
        }

        .cb-m-tool.is-red {
            background: #fdecee;
            color: #dc3545;
        }
        .cb-m-tool.is-red:hover {
            background: #dc3545;
            color: #fff;
        }

        /* Mobile Total Balance Card */
        .cb-m-total-card {
            background: #fff;
            border: 1px solid #fed7aa;
            border-left: 4px solid #ff9f43;
            border-radius: 10px;
            padding: 10px 14px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .06);
        }

        .cb-m-total-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cb-m-total-label {
            font-size: 13px;
            font-weight: 600;
            color: #7c2d12;
        }

        .cb-m-total-val {
            font-size: 16px;
            font-weight: 700;
            color: #ea580c;
        }

        /* Mobile Filter Trigger Bar (<1200px) */
        .cb-filter-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border-radius: 12px;
            padding: 8px 10px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
        }

        .cb-filter-summary {
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

        .cb-filter-summary-text {
            font-size: 12.5px;
            color: #475467;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cb-filter-toggle {
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

        .cb-filter-toggle.has-active {
            background: #ea580c;
        }

        .cb-filter-count {
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
        .cb-filter-backdrop {
            display: block;
            position: fixed;
            inset: 0;
            z-index: 1040;
            background: rgba(16, 24, 40, .45);
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
        }

        .cb-filter-sheet {
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

        body.cb-filter-open .cb-filter-backdrop {
            opacity: 1;
            pointer-events: auto;
        }

        body.cb-filter-open .cb-filter-sheet {
            transform: translateY(0);
        }

        body.cb-filter-open {
            overflow: hidden;
        }

        body.cb-filter-open .mobile-bottom-nav,
        body.cb-filter-open .floating-calculator-wrapper {
            display: none !important;
        }

        .cb-filter-sheet-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 18px;
            border-bottom: 1px solid #eef0f4;
            font-size: 16px;
            font-weight: 600;
            color: #1b2850;
        }

        .cb-filter-sheet-close {
            width: 32px;
            height: 32px;
            padding: 0;
            border: 0;
            background: transparent;
            color: #64748b;
            font-size: 20px;
            cursor: pointer;
        }

        .cb-filter-sheet-body {
            flex: 1 1 auto;
            overflow-y: auto;
            padding: 14px 18px;
        }

        .cb-filter-sheet-body #cashbookFilterForm .row {
            margin: 0 !important;
        }

        .cb-filter-sheet-body #cashbookFilterForm .filter-col {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
            padding: 0 !important;
            margin-bottom: 12px !important;
        }

        .cb-filter-sheet-body .cashbook-total-col,
        .cb-filter-sheet-body .cashbook-export-col {
            display: none !important;
        }

        .cb-filter-sheet-body .filter-col-search {
            order: -1 !important;
            margin-top: 0 !important;
        }

        .cb-filter-sheet-body label {
            display: block;
            margin-bottom: 6px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #344054 !important;
        }

        .cb-filter-sheet-body .bb-input,
        .cb-filter-sheet-body .bb-search-input {
            height: 42px !important;
            border-radius: 8px !important;
            border-color: #d0d5dd !important;
            font-size: 14px !important;
            width: 100% !important;
        }

        .cb-filter-sheet-foot {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            padding: 14px 18px calc(14px + env(safe-area-inset-bottom));
            border-top: 1px solid #eef0f4;
            background: #fff;
        }

        .cb-filter-clear,
        .cb-filter-apply {
            height: 44px;
            border: 0;
            border-radius: 22px;
            font-size: 14.5px;
            font-weight: 600;
            cursor: pointer;
        }

        .cb-filter-clear {
            background: #f1f5f9;
            color: #475467;
        }

        .cb-filter-apply {
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

            #mobile-cashbook-cards {
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

            .btn-added {
                height: 32px;
                padding: 0 10px !important;
                font-size: 12px !important;
                border-radius: 8px !important;
            }
        }

        /* Tablets (768px - 1199.98px) sheet centering */
        @media screen and (min-width: 768px) and (max-width: 1199.98px) {
            .cb-filter-sheet {
                max-width: 580px;
                left: 50% !important;
                right: auto !important;
                transform: translate(-50%, 105%);
                border-radius: 16px 16px 0 0;
                width: 100%;
            }

            body.cb-filter-open .cb-filter-sheet {
                transform: translate(-50%, 0) !important;
            }
        }

        /* Dark Mode Overrides */
        :root[data-theme="dark"] .cb-m-head-top,
        :root[data-theme="dark"] .cb-filter-bar,
        :root[data-theme="dark"] .cb-filter-sheet,
        :root[data-theme="dark"] .cb-m-total-card,
        :root[data-theme="dark"] .transaction-m-card,
        :root[data-theme="dark"] .transaction-empty-card {
            background: #1e293b !important;
            border-color: #334155 !important;
        }

        :root[data-theme="dark"] .cb-m-title,
        :root[data-theme="dark"] .cb-filter-sheet-head,
        :root[data-theme="dark"] .cb-m-total-label,
        :root[data-theme="dark"] .transaction-m-val {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .cb-filter-summary,
        :root[data-theme="dark"] .transaction-m-ref-badge {
            background: #0f172a !important;
            border-color: #334155 !important;
        }

        :root[data-theme="dark"] .cb-filter-summary-text,
        :root[data-theme="dark"] .cb-filter-sheet-body label {
            color: #94a3b8 !important;
        }

        :root[data-theme="dark"] .cb-filter-sheet-head,
        :root[data-theme="dark"] .cb-filter-sheet-foot,
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

        :root[data-theme="dark"] .cb-m-tool.is-green { background: #064e3b; color: #6ee7b7; }
        :root[data-theme="dark"] .cb-m-tool.is-red { background: #7f1d1d; color: #fca5a5; }

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
@endpush

@section('content')
    <!-- Mobile Filter Backdrop & Bottom Sheet (<1200px) -->
    <div class="cb-filter-backdrop d-xl-none" id="cbFilterBackdrop"></div>
    <div class="cb-filter-sheet d-xl-none" id="cbFilterSheet" role="dialog" aria-modal="true" aria-labelledby="cbFilterSheetTitle">
        <div class="cb-filter-sheet-head">
            <span id="cbFilterSheetTitle"><i class="fas fa-sliders-h me-2 text-warning"></i>Filter Cashbook</span>
            <button type="button" class="cb-filter-sheet-close" id="cbFilterSheetClose" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cb-filter-sheet-body" id="cbFilterSheetBody"></div>
        <div class="cb-filter-sheet-foot">
            <button type="button" class="cb-filter-clear" id="cbFilterClear">Reset</button>
            <button type="button" class="cb-filter-apply" id="cbFilterApply">Apply Filters</button>
        </div>
    </div>

    <div class="content">
        <!-- Mobile Header (<1200px) -->
        <div class="cb-m-head d-xl-none mb-2">
            <div class="cb-m-head-top">
                <h4 class="cb-m-title mb-0">Cashbook</h4>
                <div class="cb-m-head-actions">
                    <a href="{{ route('auth.dashboard') }}" class="btn btn-sm btn-added">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="cb-m-tools mt-2">
                <button type="button" class="cb-m-tool is-green" data-cb-proxy="#customExportExcel">
                    <i class="fa fa-file-excel me-1"></i><span>Excel</span>
                </button>
                <button type="button" class="cb-m-tool is-red" data-cb-proxy="#customExportPdf">
                    <i class="fa fa-file-pdf me-1"></i><span>PDF</span>
                </button>
            </div>
        </div>

        <!-- Desktop Header (>=1200px) -->
        <div class="page-header d-none d-xl-flex">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100">
                <div class="page-title mb-0">
                    <h4 class="mb-0">Cashbook</h4>
                </div>
                <div class="page-btn">
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
                            <ul class="nav nav-tabs nav-tabs-solid cashbook-top-tabs" id="cashBookTabs">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#receipts" data-bs-toggle="tab" data-status="credit">Receipts</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#payments" data-bs-toggle="tab" data-status="debit">Payments</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#expenses" data-bs-toggle="tab" data-status="expense">Expenses</a>
                                </li>
                            </ul>
                        </div>

                        <!-- Mobile Total Balance Card (<1200px) -->
                        <div class="cb-m-total-card d-xl-none mb-2">
                            <div class="cb-m-total-inner">
                                <span class="cb-m-total-label">Total:</span>
                                <span class="cb-m-total-val" id="totalDisplayMobile">₹0.00</span>
                            </div>
                        </div>

                        <!-- Mobile Filter Bar (<1200px) -->
                        <div class="cb-filter-bar d-xl-none mb-3">
                            <div class="cb-filter-summary" id="cbFilterSummaryTrigger">
                                <i class="fas fa-filter text-muted me-1"></i>
                                <span class="cb-filter-summary-text" id="cbFilterSummaryText">Tap to filter by search, date, year...</span>
                            </div>
                            <button type="button" class="cb-filter-toggle" id="cbFilterToggle" aria-label="Filters">
                                <i class="fas fa-sliders-h"></i>
                                <span class="cb-filter-count d-none" id="cbFilterCount">0</span>
                            </button>
                        </div>

                        <!-- Desktop Filter Container (>=1200px) -->
                        <div id="desktopFilterContainer" class="d-none d-xl-block mb-3">
                            <form id="cashbookFilterForm">
                                <div class="row g-2 align-items-end cashbook-filter-row">
                                    <div class="col-lg-2 col-md-3 col-6 filter-col">
                                        <label for="from_date" class="form-label fw-bold mb-1" style="font-size:12px;">From Date</label>
                                        <input type="date" id="from_date" class="form-control form-control-sm bb-input">
                                    </div>

                                    <div class="col-lg-2 col-md-3 col-6 filter-col">
                                        <label for="to_date" class="form-label fw-bold mb-1" style="font-size:12px;">To Date</label>
                                        <input type="date" id="to_date" class="form-control form-control-sm bb-input">
                                    </div>

                                    <div class="col-lg-2 col-md-3 col-6 filter-col">
                                        <label for="year" class="form-label fw-bold mb-1" style="font-size:12px;">Year</label>
                                        <select id="year" class="form-control form-control-sm bb-input">
                                            <option value="">Select Year</option>
                                            @for ($i = date('Y'); $i >= 2020; $i--)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="d-none">
                                        <select id="status" class="form-control">
                                            <option value="credit">Credit</option>
                                            <option value="debit">Debit</option>
                                            <option value="expense">Expense</option>
                                        </select>
                                    </div>

                                    <div class="col-lg-3 col-md-3 col-6 cashbook-total-col">
                                        <label class="form-label fw-bold mb-1" style="font-size:12px;">Total</label>
                                        <div class="bb-total-box">
                                            <span class="bb-total-label">Total:</span>
                                            <span class="bb-total-val" id="totalDisplay">₹0.00</span>
                                            <input type="hidden" id="total" value="₹0.00">
                                        </div>
                                    </div>

                                    <!-- Excel & PDF -->
                                    <div class="col-lg-3 col-md-12 col-12 cashbook-export-col">
                                        <label class="form-label d-none d-lg-block opacity-0 mb-1" style="font-size:12px;">&nbsp;</label>
                                        <div class="bb-tools-grid">
                                            <button id="customExportExcel" type="button" class="bb-tool-btn is-excel">
                                                <i class="fas fa-file-excel me-1"></i> Excel
                                            </button>
                                            <button id="customExportPdf" type="button" class="bb-tool-btn is-pdf">
                                                <i class="fas fa-file-pdf me-1"></i> PDF
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Search Input --}}
                                <div class="row mt-2 filter-col-search">
                                    <div class="col-lg-4 col-md-6 col-12 filter-col">
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

                {{-- Table (Desktop & Tablets >=1200px) --}}
                <div class="table-responsive mt-3 d-none d-xl-block" style="overflow-x: auto;">
                    <table class="table datatable-cashbook" id="cashbookTable">
                        <thead>
                            <tr>
                                <th id="refColumnTitle">Order No</th>
                                <th>Date</th>
                                <th>Particulars</th>
                                <th>Amount</th>
                                <th class="details-column">Details</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                {{-- Mobile Cards Container (< 1200px) --}}
                <div class="d-xl-none mt-2" id="mobile-cashbook-cards">
                    {{-- Dynamically populated --}}
                </div>

                <!-- Pagination controls (initially hidden) -->
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
                            <!-- page numbers inserted here -->
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

    {{-- Edit Payment Modal --}}
    <div class="modal fade" id="cashbookEditModal" tabindex="-1" aria-labelledby="cashbookEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background:#ff9f43;">
                    <h5 class="modal-title text-white" id="cashbookEditLabel"><i class="fas fa-edit me-2"></i>Edit Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">x</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="cb_edit_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="date" id="cb_edit_date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount (₹)</label>
                        <input type="number" id="cb_edit_amount" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Remarks</label>
                        <textarea id="cb_edit_remarks" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="cb_save_edit_btn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

    <script>
        function indianCurrency(amount) {
            let num = Number(amount) || 0;
            return num.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
        window.indianCurrency = indianCurrency;

        function renderMobileCashbookCards(data, status) {
            const $container = $('#mobile-cashbook-cards');
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
            data.forEach((item) => {
                let amount = parseFloat(item.payment_amount) || 0;
                let remarks = item.remarks || '';
                let remarksEscaped = remarks.replace(/"/g, '&quot;');

                html += `
                    <div class="transaction-m-card">
                        <div class="transaction-m-card-header">
                            <div class="transaction-m-ref-badge">
                                <span class="ref-type-label">${refLabel}:</span>
                                <span class="ref-value">${item.order_number || '-'}</span>
                            </div>
                            <div class="transaction-m-amount">
                                ₹${indianCurrency(amount)}
                            </div>
                        </div>
                        <div class="transaction-m-body">
                            <div class="transaction-m-row">
                                <span class="transaction-m-label"><i class="far fa-calendar-alt me-1 text-muted"></i> Date:</span>
                                <span class="transaction-m-val">${item.payment_date || '-'}</span>
                            </div>
                            <div class="transaction-m-row">
                                <span class="transaction-m-label"><i class="far fa-user me-1 text-muted"></i> Particulars:</span>
                                <span class="transaction-m-val fw-semibold text-dark">${item.user_name || 'N/A'}</span>
                            </div>
                            ${remarks ? `
                            <div class="transaction-m-remarks">
                                <span class="remarks-label">Remarks:</span> ${remarks}
                            </div>` : ''}
                        </div>
                        <div class="transaction-m-footer">
                            <button class="btn btn-sm transaction-m-btn-edit cashbook-edit-btn"
                                data-id="${item.id}"
                                data-amount="${item.payment_amount}"
                                data-date="${item.payment_date}"
                                data-remarks="${remarksEscaped}"
                                title="Edit">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                            <button class="btn btn-sm transaction-m-btn-delete cashbook-delete-btn"
                                data-id="${item.id}"
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
        $(document).ready(function() {
            let authToken = localStorage.getItem("authToken");
            let selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            const $downloadLoader = $("#downloadLoaderOverlay");
            const $downloadLoaderText = $("#downloadLoaderText");
            const $exportButtons = $("#customExportExcel, #customExportPdf");

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

            // State variables
            let currentStatus = 'credit'; // from active tab
            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';

            // Helper to format Indian currency
            function indianCurrency(amount) {
                return Number(amount).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Initialize DataTable as a display-only table (no paging/search)
            let table = $('#cashbookTable').DataTable({
                destroy: true,
                ordering: false,
                searching: false,
                paging: false,
                info: false,
                lengthChange: false,
                autoWidth: false,
                dom: 't',
                columnDefs: [
                    { targets: 4, className: 'details-control', orderable: false },
                    { targets: 5, orderable: false, className: 'text-center' }
                ]
            });

            // Tab switching
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                currentStatus = $(e.target).data('status');
                $('#status').val(currentStatus).trigger('change');
                if (currentStatus === 'debit') {
                    $('#refColumnTitle').text('Bill No');
                } else if (currentStatus === 'expense') {
                    $('#refColumnTitle').text('Expense No');
                } else {
                    $('#refColumnTitle').text('Order No');
                }
                if (typeof window.updateCashBookFilterSummary === 'function') {
                    window.updateCashBookFilterSummary();
                }
                fetchCashbook(1); // reset to page 1
            });

            // Filter change events (date, year) -> reset to page 1
            $('#from_date, #to_date, #year').on('change', function() {
                if (typeof window.updateCashBookFilterSummary === 'function') {
                    window.updateCashBookFilterSummary();
                }
                if (!document.body.classList.contains('cb-filter-open')) {
                    fetchCashbook(1);
                }
            });

            // Search input with debounce
            let searchTimer;
            $('#search-input').on('keyup', function() {
                if (typeof window.updateCashBookFilterSummary === 'function') {
                    window.updateCashBookFilterSummary();
                }
                if (!document.body.classList.contains('cb-filter-open')) {
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(() => {
                        searchQuery = $(this).val();
                        fetchCashbook(1);
                    }, 500);
                }
            });

            // Per-page change
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchCashbook(1);
            });

            // Pagination click
            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchCashbook(page);
                }
            });

            // Export buttons (keep existing logic, but they will use current filters)
            // $('#customExportExcel').on('click', function() {
            //     // Build export URL with all filters
            //     let params = {
            //         from_date: $('#from_date').val(),
            //         to_date: $('#to_date').val(),
            //         year: $('#year').val(),
            //         status: currentStatus,
            //         search: searchQuery,
            //         selectedSubAdminId: selectedSubAdminId
            //     };
            //     let queryString = $.param(params);
            //     window.location.href = "{{ url('/api/export-cashbook-excel') }}?" + queryString;
            // });
            $('#customExportExcel').on('click', function() {
                if ($(this).prop("disabled")) {
                    return;
                }
                let params = {
                    from_date: $('#from_date').val(),
                    to_date: $('#to_date').val(),
                    year: $('#year').val(),
                    status: currentStatus,
                    search: searchQuery,
                    selectedSubAdminId: selectedSubAdminId
                };

                let queryString = $.param(params);
                let url = "{{ url('/api/export-cashbook-excel') }}?" + queryString;

                $.ajax({
                    url: url,
                    method: 'GET',
                    beforeSend: function() {
                        toggleDownloadLoader(true, "Generating Excel...");
                    },
                    headers: {
                        Authorization: "Bearer " + authToken
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(blob, status, xhr) {
                        // ✅ Get filename from response header or use default
                        let filename = 'cashbook_export.xls';
                        let contentDisposition = xhr.getResponseHeader('Content-Disposition');
                        if (contentDisposition) {
                            let match = contentDisposition.match(
                                /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                            if (match && match[1]) {
                                filename = match[1].replace(/['"]/g, '');
                            }
                        }
                        // ✅ Trigger download
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

            $('#customExportPdf').on('click', function() {
                if ($(this).prop("disabled")) {
                    return;
                }
                let data = {
                    from_date: $('#from_date').val(),
                    to_date: $('#to_date').val(),
                    year: $('#year').val(),
                    status: currentStatus,
                    search: searchQuery,
                    selectedSubAdminId: selectedSubAdminId
                };
                $.ajax({
                    url: "{{ url('/api/export-cashbook-pdf') }}",
                    type: "GET",
                    beforeSend: function() {
                        toggleDownloadLoader(true, "Generating PDF...");
                    },
                    data: data,
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        if (response.success || response.status) {
                            window.open(response.file_url, '_blank');
                        }
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

            // Initial load
            fetchCashbook(1);
            initMobileFilterSheet();

            // Mobile bottom sheet and filter placement
            function initMobileFilterSheet() {
                const sheetBody = document.getElementById('cbFilterSheetBody');
                const desktopContainer = document.getElementById('desktopFilterContainer');
                const form = document.getElementById('cashbookFilterForm');
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
                            closeCashBookSheet();
                        }
                    }
                }

                function openCashBookSheet() {
                    placeFilterFields();
                    document.body.classList.add('cb-filter-open');
                    $('#cbFilterToggle').attr('aria-expanded', 'true');
                }

                function closeCashBookSheet() {
                    document.body.classList.remove('cb-filter-open');
                    $('#cbFilterToggle').attr('aria-expanded', 'false');
                }

                window.closeCashBookSheet = closeCashBookSheet;

                function updateFilterSummary() {
                    const search = ($('#search-input').val() || '').trim();
                    const fromDate = $('#from_date').val();
                    const toDate = $('#to_date').val();
                    const year = $('#year').val();

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

                    const countEl = $('#cbFilterCount');
                    const toggleEl = $('#cbFilterToggle');
                    if (count > 0) {
                        countEl.text(count).removeClass('d-none');
                        toggleEl.addClass('has-active');
                    } else {
                        countEl.addClass('d-none');
                        toggleEl.removeClass('has-active');
                    }

                    const summaryText = parts.length > 0 ? parts.join(' • ') : 'Tap to filter by search, date, year...';
                    $('#cbFilterSummaryText').text(summaryText);
                }

                window.updateCashBookFilterSummary = updateFilterSummary;

                $(document).on('click', '#cbFilterToggle, #cbFilterSummaryTrigger', function() {
                    openCashBookSheet();
                });

                $(document).on('click', '#cbFilterSheetClose, #cbFilterBackdrop', function() {
                    closeCashBookSheet();
                });

                $('#cbFilterClear').on('click', function() {
                    $('#search-input').val('');
                    searchQuery = '';
                    $('#from_date').val('');
                    $('#to_date').val('');
                    $('#year').val('');
                    updateFilterSummary();
                    fetchCashbook(1);
                });

                $('#cbFilterApply').on('click', function() {
                    closeCashBookSheet();
                    searchQuery = ($('#search-input').val() || '').trim();
                    updateFilterSummary();
                    fetchCashbook(1);
                });

                $(document).on('click', '.cb-m-tool[data-cb-proxy]', function() {
                    const target = $(this).data('cb-proxy');
                    if (target) $(target).trigger('click');
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

            // Toggle details icon (for mobile)
            $(document).on('click', '.toggle-details', function() {
                let icon = $(this).find('i');
                if (icon.hasClass('fa-plus-circle')) {
                    icon.removeClass('fa-plus-circle').addClass('fa-minus-circle').css('color', 'red');
                } else {
                    icon.removeClass('fa-minus-circle').addClass('fa-plus-circle').css('color', '#ff9f43');
                }
            });

            // Main function to fetch data
            function fetchCashbook(page = 1) {
                let params = {
                    selectedSubAdminId: selectedSubAdminId,
                    from_date: $('#from_date').val(),
                    to_date: $('#to_date').val(),
                    year: $('#year').val(),
                    status: currentStatus,
                    search: searchQuery,
                    page: page,
                    per_page: perPage
                };

                $.ajax({
                    url: "{{ url('/api/cashbook/data') }}",
                    type: "GET",
                    data: params,
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        if (response.status && response.data && response.data.length > 0) {
                            // Update pagination info
                            currentPage = response.pagination.current_page;
                            lastPage = response.pagination.last_page;
                            updatePaginationUI(response.pagination);

                            // Build table rows for desktop/tablet table
                            let rows = [];
                            response.data.forEach((item, index) => {
                                let amount = parseFloat(item.payment_amount) || 0;
                                let remarksEscaped = (item.remarks || '').replace(/"/g, '&quot;');

                                rows.push([
                                    `<span class="fw-semibold">${item.order_number || '-'}</span>`,
                                    item.payment_date || '-',
                                    item.user_name || 'N/A',
                                    `₹${indianCurrency(amount)}`,
                                    '', // Details toggle column (hidden)
                                    `<div class="d-flex gap-1 justify-content-center">
                                        <button class="btn btn-sm btn-warning py-0 px-2 cashbook-edit-btn"
                                            data-id="${item.id}"
                                            data-amount="${item.payment_amount}"
                                            data-date="${item.payment_date}"
                                            data-remarks="${remarksEscaped}"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger py-0 px-2 cashbook-delete-btn"
                                            data-id="${item.id}"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>`
                                ]);
                            });

                            table.clear().rows.add(rows).draw();
                            renderMobileCashbookCards(response.data, currentStatus);

                            // Update total field
                            let formattedTotal = '₹' + indianCurrency(response.total_amount || 0);
                            $('#total').val(formattedTotal);
                            $('#totalDisplay, #totalDisplayMobile').text(formattedTotal);

                            // Show pagination controls
                            $('.pagination-controls').show();
                        } else {
                            table.clear().draw();
                            renderMobileCashbookCards([], currentStatus);
                            $('#total').val('₹0.00');
                            $('#totalDisplay, #totalDisplayMobile').text('₹0.00');
                            $('.pagination-controls').hide();
                        }
                    },
                    error: function(xhr) {
                        table.clear().draw();
                        renderMobileCashbookCards([], currentStatus);
                        $('#total').val('₹0.00');
                        $('#totalDisplay, #totalDisplayMobile').text('₹0.00');
                        $('.pagination-controls').hide();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load cashbook data',
                            confirmButtonColor: '#ff9f43'
                        });
                    }
                });
            }

            function updatePaginationUI(pagination) {
                $('#pagination-from').text(pagination.from || 0);
                $('#pagination-to').text(pagination.to || 0);
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
            $(document).on('click', '.cashbook-edit-btn', function () {
                let id      = $(this).data('id');
                let amount  = $(this).data('amount');
                let dateRaw = $(this).data('date');   // dd-mm-yyyy from API
                let remarks = $(this).data('remarks');

                // Convert dd-mm-yyyy → yyyy-mm-dd for date input
                let parts = String(dateRaw).split('-');
                let isoDate = parts.length === 3 ? `${parts[2]}-${parts[1]}-${parts[0]}` : dateRaw;

                $('#cb_edit_id').val(id);
                $('#cb_edit_amount').val(amount);
                $('#cb_edit_date').val(isoDate);
                $('#cb_edit_remarks').val(remarks);
                $('#cashbookEditModal').modal('show');
            });

            // ── Save edit ──
            $('#cb_save_edit_btn').on('click', function () {
                let id     = $('#cb_edit_id').val();
                let amount = $('#cb_edit_amount').val();
                let date   = $('#cb_edit_date').val();
                let remark = $('#cb_edit_remarks').val();

                if (!amount || !date) {
                    Swal.fire({ icon: 'warning', title: 'Required', text: 'Date and Amount are required.', confirmButtonColor: '#ff9f43' });
                    return;
                }

                $.ajax({
                    url: `{{ url('/api/payment-store') }}/${id}/update`,
                    method: 'POST',
                    headers: { Authorization: 'Bearer ' + authToken },
                    data: { payment_amount: amount, payment_date: date, remarks: remark, type: currentStatus },
                    success: function (res) {
                        if (res.status) {
                            $('#cashbookEditModal').modal('hide');
                            Swal.fire({ icon: 'success', title: 'Updated!', text: res.message, confirmButtonColor: '#ff9f43', timer: 1500, showConfirmButton: false });
                            fetchCashbook(currentPage);
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Update failed.', confirmButtonColor: '#ff9f43' });
                    }
                });
            });

            // ── Delete button click ──
            $(document).on('click', '.cashbook-delete-btn', function () {
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
                            url: `{{ url('/api/payment-store') }}/${id}/delete`,
                            method: 'POST',
                            headers: { Authorization: 'Bearer ' + authToken },
                            data: { type: currentStatus },
                            success: function (res) {
                                if (res.status) {
                                    Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, confirmButtonColor: '#ff9f43', timer: 1500, showConfirmButton: false });
                                    fetchCashbook(currentPage);
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Delete failed.', confirmButtonColor: '#ff9f43' });
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush

