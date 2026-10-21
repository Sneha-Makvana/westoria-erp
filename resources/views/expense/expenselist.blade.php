@extends('layout.app')

@section('title', 'Expense List')

@section('content')
    <style>
        .total_expense {
            font-weight: 600;
            color: #1b2850;
        }

        .form-control {
            color: #595b5d !important;
        }

        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }

        .dataTables_wrapper .row:first-child {
            display: none !important;
        }

        .dataTables_wrapper {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        .search-input {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .search-input input {
            padding-left: 36px !important;
            border-radius: 8px;
            height: 38px !important;
            border: 1px solid #d0d5dd !important;
            width: 100%;
        }

        .btn-searchset {
            position: absolute;
            left: 10px;
            z-index: 10;
            padding: 0;
            top: 9px !important;
        }

        /* Pagination custom styles */
        .pagination .page-item .page-link {
            color: #1b2850;
            background-color: #5d6d7e;
            border: none;
            margin: 0 3px;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: bold;
        }

        .pagination .page-item.active .page-link {
            background-color: #ff9f43 !important;
            border-color: #ff9f43 !important;
            color: #fff !important;
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

        .pagination .page-item.disabled .page-link {
            background-color: #fff !important;
            color: #dee2e6 !important;
            border: 1px solid #dee2e6 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
        }

        /* Toggle button styles */
        .expense-toggle-btn-table {
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
            font-size: 17px;
            font-weight: bold;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(255, 159, 67, 0.35);
        }

        .expense-toggle-btn-table:hover {
            background: #e68a35;
            color: #fff;
        }

        .expense-toggle-btn-table.minus {
            background: #dc3545;
            box-shadow: 0 1px 3px rgba(220, 53, 69, 0.35);
        }

        .expense-toggle-btn-table.minus:hover {
            background: #bb2d3b;
        }

        .btn-icon-mobile-expense {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #1b2850;
            background: #fff;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            padding: 0;
            transition: all 0.2s ease;
        }

        .btn-icon-mobile-expense:hover {
            background: #ff9f43;
            border-color: #ff9f43;
            color: #fff;
            transform: scale(1.05);
        }

        .btn-icon-mobile-expense img {
            width: 16px;
            height: 16px;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
            border-radius: 8px !important;
            border: 1px solid #d0d5dd !important;
            display: flex !important;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 10px !important;
            color: #334155 !important;
            font-size: 13px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            right: 8px !important;
        }

        #filter-date {
            height: 38px !important;
            border: 1px solid #d0d5dd !important;
            border-radius: 8px !important;
            font-size: 13px;
            color: #334155 !important;
            padding: 0 10px;
        }

        .form-label {
            font-size: 12px !important;
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
        }

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
            margin: 0;
            font-size: 26px;
            color: #2c3e50;
            font-weight: 600;
        }

        .download-loader-box p {
            margin: 8px 0 16px;
            font-size: 18px;
            color: #64748b;
        }

        /* Mobile Tools (Excel, PDF) */
        .exp-m-tools {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .exp-m-tool {
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
        }

        .exp-m-tool.is-green { background: #e7f6ec; color: #198754; }
        .exp-m-tool.is-red { background: #fdecee; color: #dc3545; }

        /* Mobile Search & Filter Button */
        .exp-filter-toggle-btn {
            position: relative;
            width: 40px;
            height: 38px;
            border: none;
            border-radius: 8px;
            background: #ff9f43;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(255, 159, 67, 0.35);
            cursor: pointer;
        }

        .exp-filter-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #1b2850;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
        }

        /* Mobile & Tablet Expense Card Styles (< 1200px) - Compact Size */
        #mobile-expense-cards {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin-bottom: 12px;
        }

        .expense-m-card {
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

        .expense-m-card:hover {
            box-shadow: 0 3px 10px rgba(16, 24, 40, 0.08);
            border-color: #cbd5e1;
        }

        .expense-m-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 6px;
            margin-bottom: 6px;
        }

        .expense-m-title-group {
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
            flex: 1 1 auto;
        }

        .expense-m-sr {
            font-size: 10.5px;
            font-weight: 700;
            color: #ff9f43;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 5px;
            padding: 1.5px 6px;
            flex-shrink: 0;
            line-height: 1.2;
        }

        .expense-m-name {
            margin: 0;
            font-size: 13px;
            font-weight: 700;
            color: #1b2850;
            line-height: 1.3;
            word-break: break-word;
        }

        .expense-m-amount {
            font-size: 13.5px;
            font-weight: 700;
            color: #ea580c;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .expense-m-meta-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 4px 6px;
            margin-bottom: 5px;
        }

        .expense-m-meta-date {
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
        }

        .expense-m-chip {
            font-size: 10px;
            font-weight: 600;
            border-radius: 5px;
            padding: 1px 6px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            line-height: 1.3;
        }

        .expense-m-chip.type {
            background: #f1f5f9;
            color: #475569;
        }

        .expense-m-chip.mode {
            background: #eff6ff;
            color: #2563eb;
        }

        .expense-m-desc {
            font-size: 11px;
            color: #475569;
            background: #f8fafc;
            border-radius: 6px;
            padding: 5px 8px;
            margin-top: 3px;
            margin-bottom: 6px;
            line-height: 1.3;
            word-break: break-word;
        }

        .expense-m-desc .desc-label {
            font-weight: 600;
            color: #64748b;
        }

        .expense-m-card-footer {
            display: flex;
            align-items: center;
            gap: 6px;
            padding-top: 6px;
            border-top: 1px dashed #e2e8f0;
            margin-top: auto;
        }

        .expense-m-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            height: 25px;
            padding: 1px 8px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #334155;
            cursor: pointer;
            line-height: 1.2;
            transition: all 0.2s ease;
        }

        .expense-m-action-btn:hover {
            background: #f8fafc;
            color: #1b2850;
        }

        .expense-m-action-btn.edit {
            color: #ff9f43;
            border-color: #fed7aa;
            background: #fff7ed;
        }

        .expense-m-action-btn.pdf {
            color: #dc3545;
            border-color: #fecdd3;
            background: #fff1f2;
        }

        .expense-m-action-btn.delete {
            color: #64748b;
            border-color: #e2e8f0;
        }

        .expense-m-action-btn img {
            width: 11px;
            height: 11px;
        }

        .expense-empty-card {
            grid-column: 1 / -1;
            background: #fff;
            border-radius: 10px;
            border: 1px dashed #cbd5e1;
            padding: 24px 16px;
            text-align: center;
            margin: 8px 0;
        }

        /* Tablet 2-column grid (640px to 1199.98px: iPad Mini, iPad Air, etc.) */
        @media screen and (min-width: 640px) and (max-width: 1199.98px) {
            #mobile-expense-cards {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 10px !important;
            }

            .expense-m-card {
                margin-bottom: 0 !important;
            }
        }

        /* Desktop view (>= 1200px) */
        @media screen and (min-width: 1200px) {
            .desktop-export-buttons {
                display: flex !important;
            }

            .exp-m-tools,
            .exp-m-search-bar {
                display: none !important;
            }

            .datanew th,
            .datanew td {
                padding: 10px 10px;
                font-size: 13.5px;
            }
        }

        /* Mobile Tool Pills (Excel, PDF) */
        .exp-m-tools {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 10px;
        }

        .exp-m-tool {
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

        .exp-m-tool i {
            font-size: 13px;
        }

        .exp-m-tool.is-green {
            background: #e7f6ec;
            color: #198754;
        }

        .exp-m-tool.is-red {
            background: #fdecee;
            color: #dc3545;
        }

        /* Mobile Filter Trigger Bar */
        .exp-filter-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border-radius: 12px;
            padding: 8px 10px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
        }

        .exp-filter-summary {
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

        .exp-filter-summary-text {
            font-size: 12.5px;
            color: #475467;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .exp-filter-toggle {
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

        .exp-filter-toggle.has-active {
            background: #e8334a;
        }

        .exp-filter-count {
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
        .exp-filter-backdrop {
            display: block;
            position: fixed;
            inset: 0;
            z-index: 1040;
            background: rgba(16, 24, 40, .45);
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
        }

        .exp-filter-sheet {
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

        body.exp-filter-open .exp-filter-backdrop {
            opacity: 1;
            pointer-events: auto;
        }

        body.exp-filter-open .exp-filter-sheet {
            transform: translateY(0);
        }

        body.exp-filter-open {
            overflow: hidden;
        }

        body.exp-filter-open .mobile-bottom-nav,
        body.exp-filter-open .floating-calculator-wrapper {
            display: none !important;
        }

        .exp-filter-sheet-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 18px;
            border-bottom: 1px solid #eef0f4;
            font-size: 16px;
            font-weight: 600;
            color: #1b2850;
        }

        .exp-filter-sheet-close {
            width: 32px;
            height: 32px;
            padding: 0;
            border: 0;
            background: transparent;
            color: #64748b;
            font-size: 20px;
            cursor: pointer;
        }

        .exp-filter-sheet-body {
            flex: 1 1 auto;
            overflow-y: auto;
            padding: 14px 18px;
        }

        .exp-filter-sheet-body #expenseFilterForm .row {
            margin: 0 !important;
        }

        .exp-filter-sheet-body #expenseFilterForm .col-12,
        .exp-filter-sheet-body #expenseFilterForm .filter-col {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
            padding: 0 !important;
            margin-bottom: 12px !important;
        }

        .exp-filter-sheet-body .form-group {
            margin-bottom: 0 !important;
        }

        .exp-filter-sheet-body label {
            display: block;
            margin-bottom: 6px !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #344054 !important;
        }

        .exp-filter-sheet-body .select2-container {
            width: 100% !important;
        }

        .exp-filter-sheet-body .select2-container .select2-selection--single {
            height: 42px !important;
            border-radius: 8px !important;
            border-color: #d0d5dd !important;
        }

        .exp-filter-sheet-body .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 42px !important;
            font-size: 14px !important;
            padding-left: 12px !important;
        }

        .exp-filter-sheet-body .select2-container .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 8px !important;
        }

        .exp-filter-sheet-body #filter-date,
        .exp-filter-sheet-body #expense-search-input {
            height: 42px !important;
            border-radius: 8px !important;
            border-color: #d0d5dd !important;
            font-size: 14px !important;
        }

        .exp-filter-sheet-foot {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            padding: 14px 18px calc(14px + env(safe-area-inset-bottom));
            border-top: 1px solid #eef0f4;
            background: #fff;
        }

        .exp-filter-clear,
        .exp-filter-apply {
            height: 44px;
            border: 0;
            border-radius: 22px;
            font-size: 14.5px;
            font-weight: 600;
            cursor: pointer;
        }

        .exp-filter-clear {
            background: #f1f5f9;
            color: #475467;
        }

        .exp-filter-apply {
            background: #ff9f43;
            color: #fff;
        }

        body.exp-filter-open .select2-container--open .select2-dropdown {
            z-index: 1060 !important;
        }

        body.exp-filter-open .bootstrap-datetimepicker-widget {
            z-index: 1065 !important;
        }

        /* Tablets & Mobile (< 1200px: iPhone, iPad Mini, iPad Air, iPad Pro) */
        @media screen and (max-width: 1199.98px) {
            .content {
                padding: 12px 12px 95px !important;
                max-width: 100vw;
            }

            .exp-m-head {
                margin-bottom: 10px;
            }

            .exp-m-head-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-bottom: 10px;
                background: #fff;
                border-radius: 12px;
                padding: 12px 14px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            }

            .exp-m-title {
                font-size: 16px;
                font-weight: 700;
                color: #1b2850;
            }

            .exp-m-head-actions .btn {
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

            .total-expense-summary {
                width: 100% !important;
                margin-bottom: 10px !important;
            }

            .total_expense {
                width: 100% !important;
                margin: 0 !important;
                padding: 10px 14px !important;
                background: #fff !important;
                border: 1px solid #ffedd5 !important;
                border-left: 4px solid #ff9f43 !important;
                border-radius: 10px !important;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .06);
                font-size: 13.5px !important;
                font-weight: 600 !important;
                color: #1e293b !important;
                display: flex !important;
                align-items: center;
                justify-content: space-between;
            }

            .total_expense .text-warning {
                color: #ea580c !important;
                font-weight: 700 !important;
                font-size: 15px !important;
            }

            .card {
                border-radius: 12px !important;
                border: 1px solid #edf2f7;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .06);
                margin-bottom: 14px;
            }

            .card-body {
                padding: 14px !important;
            }
        }

        /* Tablets (768px - 1199.98px: iPad Mini, iPad Air, iPad Pro) */
        @media screen and (min-width: 768px) and (max-width: 1199.98px) {
            .content {
                padding: 16px 16px 80px !important;
            }

            .exp-filter-sheet {
                max-width: 580px;
                left: 50% !important;
                right: auto !important;
                transform: translate(-50%, 105%);
                border-radius: 16px 16px 0 0;
                width: 100%;
            }

            body.exp-filter-open .exp-filter-sheet {
                transform: translate(-50%, 0) !important;
            }

            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                border-radius: 8px;
            }

            #expense-list-table {
                min-width: 850px;
                table-layout: auto !important;
            }

            .datanew th,
            .datanew td {
                padding: 9px 8px;
                font-size: 13px;
                white-space: nowrap !important;
            }
        }

        /* Mobile phones (< 768px: iPhone, Android) */
        @media screen and (max-width: 767.98px) {
            .exp-filter-sheet {
                left: 0;
                right: 0;
                width: 100%;
                transform: translateY(105%);
            }

            body.exp-filter-open .exp-filter-sheet {
                transform: translateY(0);
            }

            .pagination-controls {
                gap: 10px;
            }

            .pagination-controls .d-flex {
                justify-content: space-between;
                width: 100%;
            }
        }

        /* Dark Mode Overrides */
        :root[data-theme="dark"] .exp-m-head-top,
        :root[data-theme="dark"] .exp-filter-bar,
        :root[data-theme="dark"] .exp-filter-sheet,
        :root[data-theme="dark"] .total_expense {
            background: #1e293b !important;
            border-color: #334155 !important;
        }

        :root[data-theme="dark"] .exp-m-title,
        :root[data-theme="dark"] .exp-filter-sheet-head,
        :root[data-theme="dark"] .total_expense {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .exp-filter-summary {
            background: #0f172a !important;
            border-color: #334155 !important;
        }

        :root[data-theme="dark"] .exp-filter-summary-text,
        :root[data-theme="dark"] .exp-filter-sheet-body label {
            color: #94a3b8 !important;
        }

        :root[data-theme="dark"] .exp-filter-sheet-head,
        :root[data-theme="dark"] .exp-filter-sheet-foot {
            border-color: #334155 !important;
            background: #1e293b !important;
        }

        :root[data-theme="dark"] .exp-m-tool.is-green { background: #064e3b; color: #6ee7b7; }
        :root[data-theme="dark"] .exp-m-tool.is-red { background: #7f1d1d; color: #fca5a5; }
    </style>
    @if (session('error'))
        <div class="alert alert-danger" id="error-message">
            {{ session('error') }}
        </div>

        <style>
            .fade-out {
                opacity: 1;
                transition: opacity 0.5s ease-out;
            }

            .fade-out.hidden {
                opacity: 0;
            }
        </style>

        <script>
            setTimeout(function() {
                let alert = document.getElementById('error-message');
                if (alert) {
                    alert.classList.add('hidden'); // Triggers the fade-out transition
                    // Remove the element from DOM after fadeout (optional)
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500); // match the CSS transition duration (0.5s)
                }
            }, 4000);
        </script>
    @endif
    <!-- Mobile Filter Backdrop & Bottom Sheet (<1200px) -->
    <div class="exp-filter-backdrop d-xl-none" id="expFilterBackdrop"></div>
    <div class="exp-filter-sheet d-xl-none" id="expFilterSheet" role="dialog" aria-modal="true" aria-labelledby="expFilterSheetTitle">
        <div class="exp-filter-sheet-head">
            <span id="expFilterSheetTitle"><i class="fas fa-sliders-h me-2 text-warning"></i>Filter Expenses</span>
            <button type="button" class="exp-filter-sheet-close" id="expFilterSheetClose" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="exp-filter-sheet-body" id="expFilterSheetBody"></div>
        <div class="exp-filter-sheet-foot">
            <button type="button" class="exp-filter-clear" id="expFilterClear">Reset</button>
            <button type="button" class="exp-filter-apply" id="expFilterApply">Apply Filters</button>
        </div>
    </div>

    <div class="content">
        <!-- Mobile Header (<1200px) -->
        <div class="exp-m-head d-xl-none">
            <div class="exp-m-head-top">
                <div class="exp-m-title">All Expenses</div>
                <div class="exp-m-head-actions">
                    @if (app('hasPermission')(5, 'add'))
                        <a href="{{ route('expense.add') }}" class="btn btn-sm btn-added">
                            <i class="fas fa-plus me-1"></i> New Expense
                        </a>
                    @endif
                </div>
            </div>
            <div class="exp-m-tools">
                <button type="button" class="exp-m-tool is-green" data-exp-proxy="#viewExpenseExcelBtn">
                    <i class="fas fa-file-excel"></i><span>Excel</span>
                </button>
                <button type="button" class="exp-m-tool is-red" data-exp-proxy="#viewExpensePdfBtn">
                    <i class="fas fa-file-pdf"></i><span>PDF</span>
                </button>
            </div>
        </div>

        <!-- Total Expense Amount -->
        <div class="total-expense-summary mb-2 d-flex justify-content-end">
            <h5 id="total-expense-amount" class="mb-0 px-3 py-1 rounded bg-light total_expense w-100">
                <!-- Gets updated by JS -->
            </h5>
        </div>

        <!-- Mobile Filter Bar (<1200px) -->
        <div class="exp-filter-bar d-xl-none">
            <div class="exp-filter-summary" id="expFilterSummaryTrigger">
                <i class="fas fa-filter text-muted me-1"></i>
                <span class="exp-filter-summary-text" id="expFilterSummaryText">Tap to filter by type, month, year, date...</span>
            </div>
            <button type="button" class="exp-filter-toggle" id="expFilterToggle" aria-label="Filters">
                <i class="fas fa-sliders-h"></i>
                <span class="exp-filter-count d-none" id="expFilterCount">0</span>
            </button>
        </div>

        <!-- Desktop Header (>=1200px) -->
        <div class="page-header d-none d-xl-flex justify-content-between align-items-center">
            <div class="page-title">
                <h4>All Expenses</h4>
            </div>

            <div class="header-actions d-flex align-items-center gap-2">
                @if (app('hasPermission')(5, 'add'))
                    <a href="{{ route('expense.add') }}" class="btn btn-added d-flex align-items-center btn-sm">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-2"
                            alt="img">New Expense
                    </a>
                @endif

                <!-- Desktop Export Buttons -->
                <div class="desktop-export-buttons d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-success exportExcel" id="viewExpenseExcelBtn">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                    <button type="button" class="btn btn-sm btn-danger exportPdf" id="viewExpensePdfBtn">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Form Container for Desktop (>=1200px) -->
                <div id="desktopFormContainer" class="d-none d-xl-block mb-3">
                    <form id="expenseFilterForm">
                        <div class="row g-2 align-items-end filter-row">
                            <!-- Search Input -->
                            <div class="col-12 col-xl-3 filter-col">
                                <div class="form-group mb-0">
                                    <label for="expense-search-input" class="form-label d-xl-none">Search</label>
                                    <div class="search-input">
                                        <a class="btn btn-searchset">
                                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}"
                                                alt="img" style="margin-bottom: 2px;">
                                        </a>
                                        <input type="text" id="expense-search-input" class="form-control"
                                            placeholder="Search..." style="height: 38px;">
                                    </div>
                                </div>
                            </div>

                            <!-- Expense Type -->
                            <div class="col-12 col-xl-3 filter-col">
                                <div class="form-group mb-0">
                                    <label for="filter-expense-type" class="form-label">Expense Type</label>
                                    <select id="filter-expense-type" class="form-control form-control-sm select2-filter"
                                        data-placeholder="Select Expense Type">
                                        <option value="">All Expense Type</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Month -->
                            <div class="col-12 col-xl-2 filter-col">
                                <div class="form-group mb-0">
                                    <label for="filter-month" class="form-label">Month</label>
                                    <select id="filter-month" class="form-control form-control-sm select2-filter"
                                        data-placeholder="Select Month">
                                        <option value="">All Month</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}">
                                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <!-- Year -->
                            <div class="col-12 col-xl-2 filter-col">
                                <div class="form-group mb-0">
                                    <label for="filter-year" class="form-label">Year</label>
                                    <select id="filter-year" class="form-control form-control-sm select2-filter"
                                        data-placeholder="Select Year">
                                        <option value="">All Year</option>
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Date -->
                            <div class="col-12 col-xl-2 filter-col">
                                <div class="form-group mb-0">
                                    <label for="filter-date" class="form-label">Date</label>
                                    <input type="text" id="filter-date" placeholder="Choose Date"
                                        class="datetimepicker form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Desktop Table (>= 1200px) -->
                <div class="table-responsive d-none d-xl-block">
                    <table class="table datanew" id="expense-list-table">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Expense name</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Payment Mode</th>
                                <th>Expense For</th>
                                <th>Expense Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="expense-table-body">
                            <!-- Dynamic rows here -->
                        </tbody>
                    </table>
                </div>

                <!-- Responsive Cards Container (< 1200px: iPad Mini, Tablets & Mobile) -->
                <div id="mobile-expense-cards" class="d-xl-none">
                    <!-- Dynamic cards here -->
                </div>
                <div
                    class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
                        <select id="expense-per-page-select" class="form-select form-select-sm"
                            style="width: auto; border: 1px solid #ddd;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="ms-3" style="font-size: 14px; color: #555;">
                            <span id="expense-pagination-from">0</span> - <span id="expense-pagination-to">0</span> of
                            <span id="expense-pagination-total">0</span> items
                        </span>
                    </div>
                    <nav aria-label="Expense pagination">
                        <ul class="pagination pagination-sm mb-0" id="expense-pagination-numbers"></ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>

    <div id="downloadLoaderOverlay" class="download-loader-overlay d-none" aria-live="polite" aria-busy="true">
        <div class="download-loader-box">
            <h4 id="downloadLoaderTitle">Generating PDF...</h4>
            <p>Please wait</p>
            <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
        </div>
    </div>
@endsection
@push('js')
    {{-- <script>



        // Global variables
        var expenseDataMap = {};

        // Helper function to build expandable row content
        function buildExpenseExpandableRowContent(expense, res) {
            const symbol = res.currency_symbol || '₹';
            const position = res.currency_position || 'left';
            const amount = Number(expense.amount).toFixed(2);
            const displayAmount = position === 'right' ? amount + symbol : symbol + amount;

            // Build action buttons HTML
            let actionButtons = `
                <a href="/edit-expense/${expense.id}" class="btn-icon-mobile-expense" title="Edit">
                    <img src="{{ env('ImagePath') }}admin/assets/img/icons/edit.svg" alt="Edit">
                </a>`;

            if (!['sales-manager', 'purchase-manager', 'inventory-manager'].includes(window.userRole)) {
                actionButtons += `
                    <button type="button" class="btn-icon-mobile-expense delete-btn" data-id="${expense.id}" title="Delete">
                        <img src="{{ env('ImagePath') }}admin/assets/img/icons/delete.svg" alt="Delete">
                    </button>`;
            }

            return `
                <td colspan="7" class="expense-details-content">
                    <div class="expense-details-list">
                        <div class="expense-detail-row-simple">
                            <span class="expense-detail-label-simple">Date:</span>
                            <span class="expense-detail-value-simple">${moment(expense.expense_date).format('DD MMM YYYY')}</span>
                        </div>
                        <div class="expense-detail-row-simple">
                            <span class="expense-detail-label-simple">Amount:</span>
                            <span class="expense-detail-value-simple" style="font-weight: bold; color: #dc3545;">${displayAmount}</span>
                        </div>
                        <div class="expense-detail-row-simple">
                            <span class="expense-detail-label-simple">Expense For:</span>
                            <span class="expense-detail-value-simple" style="text-align: left; max-width: 60%;">${expense.description ?? 'N/A'}</span>
                        </div>
                        <div class="expense-detail-row-simple">
                            <span class="expense-detail-label-simple">Expense Type:</span>
                            <span class="expense-detail-value-simple">${expense.expense_type?.type ?? 'N/A'}</span>
                        </div>
                    </div>
                    ${actionButtons ? `<div class="expense-action-buttons-simple">${actionButtons}</div>` : ''}
                </td>
            `;
        }

        // Toggle function for expense rows
        window.toggleExpenseRowDetails = function(expenseId) {
            const btn = $(`.expense-toggle-btn-table[data-expense-id="${expenseId}"]`);
            if (btn.length === 0) return;

            const row = btn.closest('tr');
            let detailsRow = row.next(`tr.expense-details-row[data-expense-id="${expenseId}"]`);
            const icon = btn.find('.toggle-icon');

            if (detailsRow.length === 0) {
                const expenseData = expenseDataMap[expenseId];
                if (expenseData) {
                    detailsRow = $('<tr>')
                        .addClass('expense-details-row')
                        .attr('data-expense-id', expenseId)
                        .html(buildExpenseExpandableRowContent(expenseData.expense, expenseData.res));
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

        $(document).ready(function() {

            const authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            const userRole = "{{ auth()->user()->role }}";
            window.userRole = userRole; // Store globally for use in expandable row

            /* =======================
               DataTable Init (ONCE)
            ======================= */

            $('.select2-filter').each(function() {
            const $filter = $(this);
            const $dropdownParent = $filter.closest('.form-group').length
                ? $filter.closest('.form-group')
                : $filter.closest('.table-top');
            $filter.select2({
                width: '100%',
                placeholder: $filter.data('placeholder'),
                allowClear: true,
                dropdownParent: $dropdownParent
            });
        });

           $('#filter-year').select2({
                allowClear: true,
                width: '100%',
                dropdownParent: $('#filter-year').closest('.form-group')
            });
            $('#filter-month').select2({
                allowClear: true,
                width: '100%',
                dropdownParent: $('#filter-month').closest('.form-group')
            });

            let table;

            if ($.fn.DataTable.isDataTable('.datanew')) {
                table = $('.datanew').DataTable();
            } else {
                table = $('.datanew').DataTable({
                    autoWidth: false,
                    pageLength: 10,
                    ordering: false,
                    searching: true,
                    paging: true,
                    language: {
                        emptyTable: "No expenses found",
                        zeroRecords: "No expense record found"
                    }
                });
            }

            loadExpenseTypes();
            fetchExpenses();

            /* =======================
               FILTER EVENTS
            ======================= */

            // Date filter → clears other filters
            $('#filter-date').on('dp.change', function() {
                clearDropdownFilters();
                fetchExpenses(1);
            });

            // Dropdown filters → clear date
            // $('#filter-expense-type, #filter-month, #filter-year').on('change', function() {
            //     $('#filter-date').val('');
            //     fetchExpenses();
            // });
            $('.select2-filter').on('change', function () {
                if ($(this).val()) {
                    $('#filter-date').val('');
                }
                fetchExpenses();
            });

            /* =======================
               FETCH EXPENSES
            ======================= */
            function fetchExpenses() {

                const params = buildFilterParams();

                $.ajax({
                    url: '/api/expenses/list',
                    type: 'GET',
                    data: params,
                    headers: {
                        Authorization: "Bearer " + authToken
                    },

                    success: function(res) {
                        // Clear previous data map
                        expenseDataMap = {};

                        updateTotal(res);
                        table.clear();

                        if (!res.data?.length) {
                            table.draw();
                            return;
                        }

                        res.data.forEach(item => {
                            // Store expense data for expandable row
                            expenseDataMap[item.id] = {
                                expense: item,
                                res: res
                            };
                            table.row.add(buildRow(item, res));
                        });
                        table.draw();

                        // Remove expandable rows if on desktop
                        if ($(window).width() >= 768) {
                            $('.expense-details-row').remove();
                            $('.expense-toggle-btn-table').removeClass('minus').find('.toggle-icon').text('+');
                        }
                    },

                    error: () => alert('Failed to fetch expenses')
                });
            }

            /* =======================
               PARAM BUILDER
            ======================= */
            function buildFilterParams() {

                let params = {
                    selectedSubAdminId
                };

                const date = $('#filter-date').val();
                if (date) {
                    params.date = moment(date, 'DD-MM-YYYY').format('YYYY-MM-DD');
                    return params;
                }

                if ($('#filter-expense-type').val()) params.expense_type_id = $('#filter-expense-type').val();
                if ($('#filter-month').val()) params.month = $('#filter-month').val();
                if ($('#filter-year').val()) params.year = $('#filter-year').val();

                return params;
            }

            /* =======================
               ROW BUILDER
            ======================= */
            function buildRow(item, res) {

                const symbol = res.currency_symbol || '₹';
                const position = res.currency_position || 'left';
                // const amount = Number(item.amount).toFixed(2);
                // const displayAmount = position === 'right' ? amount + symbol : symbol + amount;
                const amount = Number(item.amount).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                const displayAmount = position === 'right' ? amount + symbol : symbol + amount;

                let actions = `
            <a href="/edit-expense/${item.id}">
                <img src="{{ env('ImagePath') }}admin/assets/img/icons/edit.svg">
            </a>`;

                if (!['sales-manager', 'purchase-manager', 'inventory-manager'].includes(userRole)) {
                    actions += `
                <a class="ms-2 delete-btn" data-id="${item.id}">
                    <img src="{{ env('ImagePath') }}admin/assets/img/icons/delete.svg">
                </a>`;
                }

                // Toggle button for Details column
                const detailsToggle = `
                    <button class="expense-toggle-btn-table" onclick="toggleExpenseRowDetails('${item.id}')" data-expense-id="${item.id}">
                        <span class="toggle-icon">+</span>
                    </button>
                `;

                return [
                    `<div class="expense-mobile-summary">
                        <span>${item.sr_no ?? item.id}</span>
                        <span class="expense-mobile-name">${item.expense_name ?? 'N/A'}</span>
                    </div>`,
                    detailsToggle,
                    moment(item.expense_date).format('DD MMM YYYY'),
                    displayAmount,
                    item.description ?? 'N/A',
                    item.expense_type?.type ?? 'N/A',
                    actions
                ];
            }

            /* =======================
               TOTAL UPDATE
            ======================= */
            function updateTotal(res) {
                // const total = Number(res.total_amount || 0).toFixed(2);

                const total = Number(res.total_amount || 0).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });                const symbol = res.currency_symbol || '₹';
                const display = res.currency_position === 'right' ?
                    total + symbol :
                    symbol + total;

                $('#total-expense-amount').html(
                    `Total Expenses: <span class="text-warning">${display}</span>`
                );
            }

            /* =======================
               LOAD EXPENSE TYPES
            ======================= */
            function loadExpenseTypes() {
                $.ajax({
                    url: '/api/expense-types',
                    data: {
                        selectedSubAdminId
                    },
                    headers: {
                        Authorization: "Bearer " + authToken
                    },
                    success: res => {

                        res.data.forEach(t =>
                            $('#filter-expense-type').append(
                                `<option value="${t.id}">${t.type}</option>`)
                        );
                    }
                });
            }

            // function clearDropdownFilters() {
            //     $('#filter-expense-type, #filter-month, #filter-year').val('');
            // }
            function clearDropdownFilters() {
                $('.select2-filter').val(null).trigger('change.select2');
            }

            /* =======================
               DELETE
            ======================= */
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: "Are you sure?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ff9f43"
                }).then(result => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: `/api/expenses/${id}`,
                        type: "POST",
                        headers: {
                            Authorization: "Bearer " + authToken,
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                        },
                        success: () => {
                            if (currentPage > 1 && table.rows().count() === 1) {
                                currentPage--;
                            }
                            fetchExpenses(currentPage);
                        }
                    });
                });
            });

            // Resize handler for responsive behavior
            let expenseResizeTimer;
            let lastExpenseWidth = $(window).width();

            function forceExpenseCSSRecalculation() {
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

            function handleExpenseResize() {
                clearTimeout(expenseResizeTimer);
                expenseResizeTimer = setTimeout(function() {
                    const currentWidth = $(window).width();
                    lastExpenseWidth = currentWidth;

                    // Remove all expandable rows if on desktop/tablet (>= 768px)
                    if (currentWidth >= 768) {
                        $('.expense-details-row').remove();
                        // Reset all toggle buttons to + state
                        $('.expense-toggle-btn-table').removeClass('minus').find('.toggle-icon').text('+');
                    }

                    // Force CSS recalculation
                    forceExpenseCSSRecalculation();

                    const expenseTable = document.getElementById('expense-list-table');
                    const tableResponsive = document.querySelectorAll('.table-responsive');

                    [expenseTable, ...tableResponsive].forEach(function(el) {
                        if (el) {
                            void el.offsetHeight;
                            void el.offsetWidth;
                            el.style.display = 'none';
                            void el.offsetHeight;
                            el.style.display = '';
                        }
                    });

                    // Adjust DataTable columns if table exists
                    if (table) {
                        table.columns.adjust().draw();
                    }

                    forceExpenseCSSRecalculation();
                }, 50);
            }

            // Window resize handler
            $(window).off('resize.expense').on('resize.expense', handleExpenseResize);

            if (window.expenseResizeHandler) {
                window.removeEventListener('resize', window.expenseResizeHandler);
            }
            window.expenseResizeHandler = handleExpenseResize;
            window.addEventListener('resize', window.expenseResizeHandler, { passive: true });

            // Orientation change handler
            $(window).off('orientationchange.expense').on('orientationchange.expense', function() {
                setTimeout(function() {
                    lastExpenseWidth = $(window).width();
                    handleExpenseResize();
                }, 300);
            });

            window.addEventListener('orientationchange', function() {
                setTimeout(function() {
                    lastExpenseWidth = $(window).width();
                    handleExpenseResize();
                }, 500);
            });

            // MatchMedia listeners for breakpoint changes
            const expenseQueries = [
                window.matchMedia('(max-width: 575.98px)'),
                window.matchMedia('(min-width: 576px) and (max-width: 767.98px)'),
                window.matchMedia('(min-width: 768px) and (max-width: 1024px)'),
                window.matchMedia('(min-width: 1025px)')
            ];

            expenseQueries.forEach(function(query) {
                if (query.addEventListener) {
                    query.addEventListener('change', function() {
                        setTimeout(handleExpenseResize, 100);
                    });
                } else if (query.addListener) {
                    query.addListener(function() {
                        setTimeout(handleExpenseResize, 100);
                    });
                }
            });

            // Initial width set and call
            lastExpenseWidth = $(window).width();

            $(window).on('load', function() {
                setTimeout(function() {
                    lastExpenseWidth = $(window).width();
                    handleExpenseResize();
                }, 500);
            });

            window.handleExpenseResize = handleExpenseResize;

        });
    </script> --}}
    <script>
        const canEditExpense = @json(app('hasPermission')(5, 'edit'));
        const canDeleteExpense = @json(app('hasPermission')(5, 'delete'));

        // Global variables
        var expenseDataMap = {};

        // Helper function to build expandable row content
        function buildExpenseExpandableRowContent(expense, res) {
            const symbol = res.currency_symbol || '₹';
            const position = res.currency_position || 'left';
            const amount = Number(expense.amount).toFixed(2);
            const displayAmount = position === 'right' ? amount + symbol : symbol + amount;

            // Build action buttons HTML
            let actionButtons = '';

            if (canEditExpense) {
                actionButtons += `
                <a href="/edit-expense/${expense.id}" class="btn-icon-mobile-expense" title="Edit">
                    <img src="{{ env('ImagePath') }}admin/assets/img/icons/edit.svg" alt="Edit">
                </a>`;
            }

            actionButtons += `
                <a href="/expense/pdf/${expense.id}" class="btn-icon-mobile-expense expense-pdf-download" title="Download PDF">
                    <img src="{{ env('ImagePath') }}admin/assets/img/icons/download.svg" alt="Download PDF">
                </a>`;

            if (canDeleteExpense && !['sales-manager', 'purchase-manager', 'inventory-manager'].includes(window.userRole)) {
                actionButtons += `
                <button type="button" class="btn-icon-mobile-expense delete-btn" data-id="${expense.id}" title="Delete">
                    <img src="{{ env('ImagePath') }}admin/assets/img/icons/delete.svg" alt="Delete">
                </button>`;
            }

            return `
            <td colspan="9" class="expense-details-content">
                <div class="expense-details-card">
                    <div class="expense-detail-row-simple">
                        <span class="expense-detail-label-simple">Sr No:</span>
                        <span class="expense-detail-value-simple">${expense.sr_no ?? expense.id}</span>
                    </div>
                    <div class="expense-detail-row-simple">
                        <span class="expense-detail-label-simple">Date:</span>
                        <span class="expense-detail-value-simple">${moment(expense.expense_date).format('DD MMM YYYY')}</span>
                    </div>
                    <div class="expense-detail-row-simple">
                        <span class="expense-detail-label-simple">Amount:</span>
                        <span class="expense-detail-value-simple" style="font-weight: 700; color: #ea580c;">${displayAmount}</span>
                    </div>
                    <div class="expense-detail-row-simple">
                        <span class="expense-detail-label-simple">Payment Mode:</span>
                        <span class="expense-detail-value-simple">${expense.payment_mode ?? 'N/A'}</span>
                    </div>
                    <div class="expense-detail-row-simple">
                        <span class="expense-detail-label-simple">Expense For:</span>
                        <span class="expense-detail-value-simple" style="text-align: right; max-width: 65%;">${expense.description ?? 'N/A'}</span>
                    </div>
                    <div class="expense-detail-row-simple">
                        <span class="expense-detail-label-simple">Expense Type:</span>
                        <span class="expense-detail-value-simple">${expense.expense_type?.type ?? 'N/A'}</span>
                    </div>
                    ${actionButtons ? `<div class="expense-action-buttons-simple">${actionButtons}</div>` : ''}
                </div>
            </td>
        `;
        }

        // Toggle function for expense rows
        window.toggleExpenseRowDetails = function(expenseId) {
            const btn = $(`.expense-toggle-btn-table[data-expense-id="${expenseId}"]`);
            if (btn.length === 0) return;

            const row = btn.closest('tr');
            let detailsRow = row.next(`tr.expense-details-row[data-expense-id="${expenseId}"]`);
            const icon = btn.find('.toggle-icon');

            if (detailsRow.length === 0) {
                const expenseData = expenseDataMap[expenseId];
                if (expenseData) {
                    detailsRow = $('<tr>')
                        .addClass('expense-details-row')
                        .attr('data-expense-id', expenseId)
                        .html(buildExpenseExpandableRowContent(expenseData.expense, expenseData.res));
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

        function initializeSelect2() {
            // console.log('Initializing Select2...');

            // Destroy existing Select2 instances if they exist
            if ($('#filter-expense-type').hasClass('select2-hidden-accessible')) {
                $('#filter-expense-type').select2('destroy');
            }
            if ($('#filter-month').hasClass('select2-hidden-accessible')) {
                $('#filter-month').select2('destroy');
            }
            if ($('#filter-year').hasClass('select2-hidden-accessible')) {
                $('#filter-year').select2('destroy');
            }

            // Initialize Select2 for all filter dropdowns - same style as invoice page
            $('#filter-expense-type').select2({
                placeholder: "All Expense Type",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#filter-expense-type').closest('.form-group')
            });

            $('#filter-month').select2({
                placeholder: "All Month",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#filter-month').closest('.form-group')
            });

            $('#filter-year').select2({
                placeholder: "All Year",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#filter-year').closest('.form-group')
            });

            // console.log('Select2 initialized successfully');
        }

        $(document).ready(function() {
            const authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            const userRole = "{{ auth()->user()->role }}";
            window.userRole = userRole; // Store globally for use in expandable row
            const $downloadLoader = $('#downloadLoaderOverlay');
            const $downloadLoaderTitle = $('#downloadLoaderTitle');
            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';

            let table;

            // Initialize DataTable
            if ($.fn.DataTable.isDataTable('.datanew')) {
                table = $('.datanew').DataTable();
            } else {
                table = $('.datanew').DataTable({
                    autoWidth: false,
                    pageLength: perPage,
                    ordering: false,
                    searching: false,
                    paging: false,
                    info: false,
                    dom: 't',
                    language: {
                        emptyTable: "No expenses found",
                        zeroRecords: "No expense record found"
                    }
                });
            }

            let searchDebounceTimer;
            $('#expense-search-input').on('input keyup', function(e) {
                searchQuery = $(this).val();
                if (window.updateExpenseFilterSummary) {
                    window.updateExpenseFilterSummary();
                }
                if (window.matchMedia('(min-width: 1200px)').matches) {
                    clearTimeout(searchDebounceTimer);
                    if (e.key === 'Enter') {
                        fetchExpenses(1);
                    } else {
                        searchDebounceTimer = setTimeout(function() {
                            fetchExpenses(1);
                        }, 400);
                    }
                }
            });

            $('#expenseFilterForm').on('submit', function(e) {
                e.preventDefault();
                if (window.closeExpenseSheet) {
                    window.closeExpenseSheet();
                }
                if (window.updateExpenseFilterSummary) {
                    window.updateExpenseFilterSummary();
                }
                fetchExpenses(1);
            });

            $('#expense-per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchExpenses(1);
            });

            // First load expense types, then initialize Select2
            loadExpenseTypes();
            fetchExpenses(currentPage);

            /* =======================
               FILTER EVENTS
            ======================= */

            // Date filter → clears other filters
            $('#filter-date').on('dp.change change', function() {
                clearDropdownFilters();
                if (window.updateExpenseFilterSummary) {
                    window.updateExpenseFilterSummary();
                }
                fetchExpenses(1);
            });

            // Dropdown filters → clear date
            $('.select2-filter').on('change', function() {
                if ($(this).val()) {
                    $('#filter-date').val('');
                }
                if (window.updateExpenseFilterSummary) {
                    window.updateExpenseFilterSummary();
                }
                fetchExpenses(1);
            });

            /* =======================
               FETCH EXPENSES
            ======================= */
            function fetchExpenses(page = 1) {
                currentPage = page;
                const params = buildFilterParams();

                $.ajax({
                    url: '/api/expenses/list',
                    type: 'GET',
                    data: params,
                    headers: {
                        Authorization: "Bearer " + authToken
                    },
                    success: function(res) {
                        expenseDataMap = {};
                        currentPage = res.pagination?.current_page || 1;
                        lastPage = res.pagination?.last_page || 1;

                        updateTotal(res);

                        // Populate desktop / tablet table
                        table.clear();
                        if (res.data && res.data.length > 0) {
                            res.data.forEach(item => {
                                table.row.add(buildDesktopRow(item, res));
                            });
                        }
                        table.draw();

                        // Populate mobile cards
                        renderMobileCards(res.data || [], res);

                        updatePaginationUI(res.pagination || {
                            current_page: 1,
                            last_page: 1,
                            per_page: perPage,
                            total: res.data?.length || 0
                        });
                    },
                    error: () => alert('Failed to fetch expenses')
                });
            }

            /* =======================
               PARAM BUILDER
            ======================= */
            function buildFilterParams() {
                let params = {
                    selectedSubAdminId,
                    page: currentPage,
                    per_page: perPage
                };

                const searchVal = $('#expense-search-input').val();
                const currentSearch = (searchVal !== undefined && searchVal !== null && searchVal.trim() !== '') ? searchVal.trim() : (searchQuery || '');
                if (currentSearch) params.search = currentSearch;

                const date = $('#filter-date').val();
                if (date) {
                    params.date = moment(date, 'DD-MM-YYYY').format('YYYY-MM-DD');
                    return params;
                }

                if ($('#filter-expense-type').val()) params.expense_type_id = $('#filter-expense-type').val();
                if ($('#filter-month').val()) params.month = $('#filter-month').val();
                if ($('#filter-year').val()) params.year = $('#filter-year').val();

                return params;
            }

            /* =======================
               ROW BUILDER (DESKTOP & TABLET)
            ======================= */
            function buildDesktopRow(item, res) {
                const symbol = res.currency_symbol || '₹';
                const position = res.currency_position || 'left';
                const amount = Number(item.amount).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                const displayAmount = position === 'right' ? amount + symbol : symbol + amount;

                let actions = '';

                if (canEditExpense) {
                    actions += `
                    <a href="/edit-expense/${item.id}">
                        <img src="{{ env('ImagePath') }}admin/assets/img/icons/edit.svg">
                    </a>`;
                }

                actions += `
                    <a class="ms-2 expense-pdf-download" href="/expense/pdf/${item.id}" title="Download PDF">
                        <img src="{{ env('ImagePath') }}admin/assets/img/icons/download.svg">
                    </a>`;

                if (canDeleteExpense && !['sales-manager', 'purchase-manager', 'inventory-manager'].includes(userRole)) {
                    actions += `
                    <a class="ms-2 delete-btn" data-id="${item.id}">
                        <img src="{{ env('ImagePath') }}admin/assets/img/icons/delete.svg">
                    </a>`;
                }

                return [
                    item.sr_no ?? item.id,
                    item.expense_name,
                    moment(item.expense_date).format('DD MMM YYYY'),
                    displayAmount,
                    item.payment_mode ?? 'N/A',
                    item.description ?? 'N/A',
                    item.expense_type?.type ?? 'N/A',
                    actions
                ];
            }

            /* =======================
               MOBILE CARDS RENDERER (< 768px)
            ======================= */
            function renderMobileCards(items, res) {
                const $container = $('#mobile-expense-cards');
                if (!items || items.length === 0) {
                    $container.html(`
                        <div class="expense-empty-card">
                            <i class="fas fa-receipt fa-2x text-muted mb-2"></i>
                            <h6 class="text-muted fw-bold mb-1">No expenses found</h6>
                            <p class="text-muted small mb-0">Try adjusting your filters or search term</p>
                        </div>
                    `);
                    return;
                }

                const symbol = res.currency_symbol || '₹';
                const position = res.currency_position || 'left';
                const imagePath = "{{ env('ImagePath') }}";

                let html = '';
                items.forEach(item => {
                    const amount = Number(item.amount).toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    const displayAmount = position === 'right' ? amount + symbol : symbol + amount;
                    const dateFormatted = item.expense_date ? moment(item.expense_date).format('DD MMM YYYY') : '-';

                    let actionButtons = '';
                    if (canEditExpense) {
                        actionButtons += `
                            <a href="/edit-expense/${item.id}" class="expense-m-action-btn edit" title="Edit">
                                <img src="${imagePath}admin/assets/img/icons/edit.svg" alt="Edit"> Edit
                            </a>
                        `;
                    }
                    actionButtons += `
                        <a href="/expense/pdf/${item.id}" class="expense-m-action-btn pdf expense-pdf-download" title="PDF">
                            <img src="${imagePath}admin/assets/img/icons/download.svg" alt="PDF"> PDF
                        </a>
                    `;
                    if (canDeleteExpense && !['sales-manager', 'purchase-manager', 'inventory-manager'].includes(userRole)) {
                        actionButtons += `
                            <button type="button" class="expense-m-action-btn delete delete-btn" data-id="${item.id}" title="Delete">
                                <img src="${imagePath}admin/assets/img/icons/delete.svg" alt="Delete"> Delete
                            </button>
                        `;
                    }

                    html += `
                        <div class="expense-m-card">
                            <div class="expense-m-card-header">
                                <div class="expense-m-title-group">
                                    <span class="expense-m-sr">#${item.sr_no ?? item.id}</span>
                                    <h6 class="expense-m-name">${item.expense_name ?? 'N/A'}</h6>
                                </div>
                                <span class="expense-m-amount">${displayAmount}</span>
                            </div>
                            <div class="expense-m-card-body p-0">
                                <div class="expense-m-meta-row">
                                    <span class="expense-m-meta-date">
                                        <i class="far fa-calendar-alt me-1"></i>${dateFormatted}
                                    </span>
                                    ${item.expense_type?.type ? `
                                        <span class="expense-m-chip type">
                                            <i class="fas fa-tag me-1"></i>${item.expense_type.type}
                                        </span>
                                    ` : ''}
                                    ${item.payment_mode ? `
                                        <span class="expense-m-chip mode">
                                            <i class="far fa-credit-card me-1"></i>${item.payment_mode}
                                        </span>
                                    ` : ''}
                                </div>
                                ${item.description ? `
                                    <div class="expense-m-desc">
                                        <span class="desc-label">Expense For:</span> ${item.description}
                                    </div>
                                ` : ''}
                            </div>
                            ${actionButtons ? `
                                <div class="expense-m-card-footer">
                                    ${actionButtons}
                                </div>
                            ` : ''}
                        </div>
                    `;
                });

                $container.html(html);
            }

            /* =======================
               TOTAL UPDATE
            ======================= */
            function updateTotal(res) {
                const total = Number(res.total_amount || 0).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                const symbol = res.currency_symbol || '₹';
                const display = res.currency_position === 'right' ?
                    total + symbol :
                    symbol + total;

                $('#total-expense-amount').html(
                    `Total Expenses: <span class="text-warning">${display}</span>`
                );
            }

            function updatePaginationUI(pagination) {
                let from = (pagination.current_page - 1) * pagination.per_page + 1;
                let to = pagination.current_page * pagination.per_page;

                if (to > pagination.total) {
                    to = pagination.total;
                }

                if (pagination.total === 0) {
                    from = 0;
                }

                $('#expense-pagination-from').text(from);
                $('#expense-pagination-to').text(to);
                $('#expense-pagination-total').text(pagination.total);

                let paginationHtml = '';
                const current = pagination.current_page || 1;
                const totalPages = pagination.last_page || 1;
                paginationHtml += `
                    <li class="page-item ${current === 1 ? 'disabled' : ''}">
                        <a class="page-link expense-page-link" href="javascript:void(0);" data-page="${current - 1}">Previous</a>
                    </li>
                `;

                const visiblePageCount = 2;
                let startPage = Math.floor((current - 1) / visiblePageCount) * visiblePageCount + 1;
                let endPage = Math.min(totalPages, startPage + visiblePageCount - 1);

                if (startPage > 1) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link expense-page-link" href="javascript:void(0);" data-page="${startPage - 1}" data-action="prev-group">..</a>
                        </li>
                    `;
                }

                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === current ? 'active' : ''}">
                            <a class="page-link expense-page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                        </li>
                    `;
                }

                if (endPage < totalPages) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link expense-page-link" href="javascript:void(0);" data-page="${endPage + 1}" data-action="next-group">..</a>
                        </li>
                    `;
                }

                paginationHtml += `
                    <li class="page-item ${current === totalPages || totalPages === 0 ? 'disabled' : ''}">
                        <a class="page-link expense-page-link" href="javascript:void(0);" data-page="${current + 1}">Next</a>
                    </li>
                `;

                $('#expense-pagination-numbers').html(paginationHtml);
                $('.pagination-controls').toggle(pagination.total > 0);
            }

            $(document).on('click', '.expense-page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    currentPage = page;
                    fetchExpenses(page);
                }
            });

            /* =======================
               LOAD EXPENSE TYPES - UPDATED
            ======================= */
            function loadStaticExpenseTypes() {
                if ($('#filter-expense-type option').length <= 1) {
                    $('#filter-expense-type').html('<option value="">All Expense Type</option>');
                }
                initializeSelect2();
            }

            function loadExpenseTypes() {
                // console.log('loadExpenseTypes started - calling API');

                $.ajax({
                    url: '/api/expense-types',
                    data: {
                        selectedSubAdminId
                    },
                    headers: {
                        Authorization: "Bearer " + authToken
                    },
                    success: function(res) {
                        // console.log('API call SUCCESSFUL - response received:', res);
                        // console.log('Response structure:', JSON.stringify(res).substring(0, 200) + '...');

                        // Clear existing options
                        $('#filter-expense-type').empty();

                        // Add the placeholder option
                        $('#filter-expense-type').append('<option value="">All Expense Type</option>');

                        // Check if res.data exists and is an array
                        if (res && res.data && Array.isArray(res.data)) {
                            // console.log('res.data is an array with length:', res.data.length);

                            if (res.data.length > 0) {
                                // console.log('Found', res.data.length, 'expense types');

                                res.data.forEach(t => {
                                    // console.log('Adding option:', t.id, t.type);
                                    $('#filter-expense-type').append(
                                        `<option value="${t.id}">${t.type}</option>`
                                    );
                                });

                                // console.log('Options added successfully from API');
                            } else {
                                // console.log('res.data array is empty - falling back to static');
                                loadStaticExpenseTypes();
                                return;
                            }
                        }
                        // Check if response has a different structure
                        else if (res && typeof res === 'object') {
                            // console.log('Response is an object but not in expected format:', Object.keys(res));
                            // Try to find data in other possible locations
                            if (res.expense_types && Array.isArray(res.expense_types)) {
                                // console.log('Found expense_types array instead of data');
                                res.expense_types.forEach(t => {
                                    $('#filter-expense-type').append(
                                        `<option value="${t.id}">${t.type}</option>`
                                    );
                                });
                            } else {
                                // console.log('Could not find data in expected format - falling back to static');
                                loadStaticExpenseTypes();
                                return;
                            }
                        } else {
                            // console.log('No valid data found in response - falling back to static');
                            loadStaticExpenseTypes();
                            return;
                        }

                        // Initialize Select2 AFTER options are loaded
                        initializeSelect2();
                    },
                    error: function(xhr, status, error) {
                        //     console.error('API call FAILED:', error);
                        //     console.log('Status:', status);
                        //     console.log('Response Text:', xhr.responseText);
                        //     console.log('Status Code:', xhr.status);
                        loadStaticExpenseTypes();
                    }
                });
            }

            function clearDropdownFilters() {
                $('#filter-expense-type').val(null).trigger('change');
                $('#filter-month').val(null).trigger('change');
                $('#filter-year').val(null).trigger('change');
            }

            /* =======================
               DELETE
            ======================= */
            $(document).on('click', '.delete-btn', function() {
                if (!canDeleteExpense) {
                    return;
                }

                const id = $(this).data('id');

                Swal.fire({
                    title: "Are you sure?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ff9f43"
                }).then(result => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: `/api/expenses/${id}`,
                        type: "POST",
                        headers: {
                            Authorization: "Bearer " + authToken,
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                        },
                        success: () => {
                            if (currentPage > 1 && table.rows().count() === 1) {
                                currentPage--;
                            }
                            fetchExpenses(currentPage);
                        }
                    });
                });
            });

            // Resize handler for responsive behavior
            let expenseResizeTimer;
            let lastExpenseWidth = $(window).width();

            function forceExpenseCSSRecalculation() {
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

            function handleExpenseResize() {
                clearTimeout(expenseResizeTimer);
                expenseResizeTimer = setTimeout(function() {
                    const currentWidth = $(window).width();
                    lastExpenseWidth = currentWidth;

                    // Remove all expandable rows if on desktop/tablet (>= 768px)
                    if (currentWidth >= 768) {
                        $('.expense-details-row').remove();
                        // Reset all toggle buttons to + state
                        $('.expense-toggle-btn-table').removeClass('minus').find('.toggle-icon').text('+');
                    }

                    // Force CSS recalculation
                    forceExpenseCSSRecalculation();

                    const expenseTable = document.getElementById('expense-list-table');
                    const tableResponsive = document.querySelectorAll('.table-responsive');

                    [expenseTable, ...tableResponsive].forEach(function(el) {
                        if (el) {
                            void el.offsetHeight;
                            void el.offsetWidth;
                            el.style.display = 'none';
                            void el.offsetHeight;
                            el.style.display = '';
                        }
                    });

                    // Adjust DataTable columns if table exists
                    if (table) {
                        table.columns.adjust().draw();
                    }

                    forceExpenseCSSRecalculation();
                }, 50);
            }

            // Window resize handler
            $(window).off('resize.expense').on('resize.expense', handleExpenseResize);

            if (window.expenseResizeHandler) {
                window.removeEventListener('resize', window.expenseResizeHandler);
            }
            window.expenseResizeHandler = handleExpenseResize;
            window.addEventListener('resize', window.expenseResizeHandler, {
                passive: true
            });

            // Orientation change handler
            $(window).off('orientationchange.expense').on('orientationchange.expense', function() {
                setTimeout(function() {
                    lastExpenseWidth = $(window).width();
                    handleExpenseResize();
                }, 300);
            });

            window.addEventListener('orientationchange', function() {
                setTimeout(function() {
                    lastExpenseWidth = $(window).width();
                    handleExpenseResize();
                }, 500);
            });

            // MatchMedia listeners for breakpoint changes
            const expenseQueries = [
                window.matchMedia('(max-width: 575.98px)'),
                window.matchMedia('(min-width: 576px) and (max-width: 767.98px)'),
                window.matchMedia('(min-width: 768px) and (max-width: 1024px)'),
                window.matchMedia('(min-width: 1025px)')
            ];

            expenseQueries.forEach(function(query) {
                if (query.addEventListener) {
                    query.addEventListener('change', function() {
                        setTimeout(handleExpenseResize, 100);
                    });
                } else if (query.addListener) {
                    query.addListener(function() {
                        setTimeout(handleExpenseResize, 100);
                    });
                }
            });

            // Initial width set and call
            lastExpenseWidth = $(window).width();

            $(window).on('load', function() {
                setTimeout(function() {
                    lastExpenseWidth = $(window).width();
                    handleExpenseResize();
                }, 500);
            });

            window.handleExpenseResize = handleExpenseResize;

            // Mobile bottom sheet and filter placement
            (function initMobileFilterSheet() {
                const sheetBody = document.getElementById('expFilterSheetBody');
                const desktopContainer = document.getElementById('desktopFormContainer');
                const form = document.getElementById('expenseFilterForm');
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
                            closeExpenseSheet();
                        }
                    }
                }

                function openExpenseSheet() {
                    placeFilterFields();
                    document.body.classList.add('exp-filter-open');
                    $('#expFilterToggle').attr('aria-expanded', 'true');
                }

                function closeExpenseSheet() {
                    document.body.classList.remove('exp-filter-open');
                    $('#expFilterToggle').attr('aria-expanded', 'false');
                }

                window.closeExpenseSheet = closeExpenseSheet;

                function updateFilterSummary() {
                    const search = $('#expense-search-input').val();
                    const type = $('#filter-expense-type').val();
                    const month = $('#filter-month').val();
                    const year = $('#filter-year').val();
                    const date = $('#filter-date').val();

                    let count = 0;
                    let parts = [];

                    if (search && search.trim() !== '') {
                        count++;
                        parts.push(`"${search.trim()}"`);
                    }

                    if (type && type !== '') {
                        count++;
                        const typeText = $('#filter-expense-type option:selected').text().trim();
                        parts.push(typeText !== 'All Expense Type' ? typeText : 'Type');
                    }

                    if (month && month !== '') {
                        count++;
                        const monthText = $('#filter-month option:selected').text().trim();
                        parts.push(monthText !== 'All Month' ? monthText : 'Month');
                    }

                    if (year && year !== '') {
                        count++;
                        parts.push(year);
                    }

                    if (date && date !== '') {
                        count++;
                        parts.push(date);
                    }

                    const countEl = $('#expFilterCount');
                    const toggleEl = $('#expFilterToggle');
                    if (count > 0) {
                        countEl.text(count).removeClass('d-none');
                        toggleEl.addClass('has-active');
                    } else {
                        countEl.addClass('d-none');
                        toggleEl.removeClass('has-active');
                    }

                    const summaryText = parts.length > 0 ? parts.join(' • ') : 'Tap to filter by type, month, year, date...';
                    $('#expFilterSummaryText').text(summaryText);
                }

                window.updateExpenseFilterSummary = updateFilterSummary;

                $(document).on('click', '#expFilterToggle, #expFilterSummaryTrigger, .exp-open-sheet-btn', function() {
                    openExpenseSheet();
                });

                $(document).on('click', '#expFilterSheetClose, #expFilterBackdrop', function() {
                    closeExpenseSheet();
                });

                $('#expFilterClear').on('click', function() {
                    $('#expense-search-input').val('');
                    searchQuery = '';
                    clearDropdownFilters();
                    $('#filter-date').val('');
                    updateFilterSummary();
                    fetchExpenses(1);
                });

                $('#expFilterApply').on('click', function() {
                    closeExpenseSheet();
                    updateFilterSummary();
                    fetchExpenses(1);
                });

                $(document).on('click', '.exp-m-tool[data-exp-proxy]', function() {
                    const target = $(this).data('exp-proxy');
                    if (target) $(target).trigger('click');
                });

                $('#filter-expense-type, #filter-month, #filter-year').on('change', updateFilterSummary);
                $('#filter-date').on('change dp.change', updateFilterSummary);
                $('#expense-search-input').on('input keyup', updateFilterSummary);

                window.addEventListener('resize', placeFilterFields);
                if (filterQuery.addEventListener) {
                    filterQuery.addEventListener('change', placeFilterFields);
                } else if (filterQuery.addListener) {
                    filterQuery.addListener(placeFilterFields);
                }
                placeFilterFields();
                updateFilterSummary();
            })();

            function toggleDownloadLoader(isLoading, title) {
                if (isLoading) {
                    $downloadLoaderTitle.text(title || 'Generating report...');
                    $downloadLoader.removeClass('d-none');
                } else {
                    $downloadLoader.addClass('d-none');
                }
            }

            function getFileNameFromDisposition(contentDisposition, fallbackName) {
                if (!contentDisposition) return fallbackName;

                const utf8Match = contentDisposition.match(/filename\*=UTF-8''([^;]+)/i);
                if (utf8Match && utf8Match[1]) {
                    return decodeURIComponent(utf8Match[1]);
                }

                const asciiMatch = contentDisposition.match(/filename=\"?([^\";]+)\"?/i);
                if (asciiMatch && asciiMatch[1]) {
                    return asciiMatch[1];
                }

                return fallbackName;
            }

            async function downloadExpenseReport(endpoint, type) {
                const params = buildFilterParams();
                const queryString = $.param(params);
                const url = endpoint + (queryString ? `?${queryString}` : '');
                const isPdf = type === 'pdf';
                const fallbackName = isPdf ? 'expense-report.pdf' : 'expense-report.xlsx';
                const loaderTitle = isPdf ? 'Generating PDF...' : 'Generating Excel...';

                toggleDownloadLoader(true, loaderTitle);

                try {
                    const response = await fetch(url, {
                        method: 'GET',
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        throw new Error('Download request failed');
                    }

                    const blob = await response.blob();
                    const contentType = response.headers.get('content-type') || '';
                    if (contentType.includes('application/json')) {
                        throw new Error('No export data available');
                    }
                    const contentDisposition = response.headers.get('content-disposition');
                    const fileName = getFileNameFromDisposition(contentDisposition, fallbackName);
                    const downloadUrl = window.URL.createObjectURL(blob);

                    const link = document.createElement('a');
                    link.href = downloadUrl;
                    link.download = fileName;
                    document.body.appendChild(link);
                    link.click();
                    link.remove();

                    window.URL.revokeObjectURL(downloadUrl);
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Download Failed',
                        text: 'Unable to generate the file right now. Please try again.',
                        confirmButtonColor: '#ff9f43'
                    });
                } finally {
                    toggleDownloadLoader(false);
                }
            }

            async function downloadSingleExpensePdf(url) {
                toggleDownloadLoader(true, 'Generating PDF...');

                try {
                    const response = await fetch(url, {
                        method: 'GET',
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        throw new Error('Download request failed');
                    }

                    const blob = await response.blob();
                    const contentType = response.headers.get('content-type') || '';
                    if (contentType.includes('application/json')) {
                        throw new Error('No PDF data available');
                    }
                    const contentDisposition = response.headers.get('content-disposition');
                    const fileName = getFileNameFromDisposition(contentDisposition, 'expense.pdf');
                    const downloadUrl = window.URL.createObjectURL(blob);

                    const link = document.createElement('a');
                    link.href = downloadUrl;
                    link.download = fileName;
                    document.body.appendChild(link);
                    link.click();
                    link.remove();

                    window.URL.revokeObjectURL(downloadUrl);
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Download Failed',
                        text: 'Unable to generate the PDF right now. Please try again.',
                        confirmButtonColor: '#ff9f43'
                    });
                } finally {
                    toggleDownloadLoader(false);
                }
            }

            // Row-level PDF download icons
            $(document).on('click', '.expense-pdf-download', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                if (!url) return;
                downloadSingleExpensePdf(url);
            });

            // Export Excel
            $(document).on('click', '.exportExcel', function(e) {
                e.preventDefault();
                downloadExpenseReport('/export-expense', 'excel');
            });

            // Export PDF
            $(document).on('click', '.exportPdf', function(e) {
                e.preventDefault();
                downloadExpenseReport('/export-expense-pdf', 'pdf');
            });
        });
    </script>
@endpush
