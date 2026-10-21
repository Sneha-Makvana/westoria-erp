@extends('layout.app')

@section('title', 'Sales Pool Report')

@section('content')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />
    
    <style>
        /* Container & Layout */
        .sales-pool-container {
            padding: 20px;
            background-color: #f8f9fa;
            min-height: calc(100vh - 100px);
        }

        /* Header */
        .sales-pool-header {
            margin-bottom: 30px;
        }

        .sales-pool-header h2 {
            font-size: 28px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .sales-pool-header p {
            font-size: 14px;
            color: #7f8c8d;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .filter-row {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-group input[type="text"],
        .filter-group input[type="date"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background-color: #fff;
        }

        .filter-group input[type="text"]:focus,
        .filter-group input[type="date"]:focus {
            border-color: #ff9f43;
            box-shadow: 0 0 0 3px rgba(255, 159, 67, 0.1);
            outline: none;
        }

        .filter-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background-color: #fff;
            color: #2c3e50;
            cursor: pointer;
        }

        .filter-group select:focus {
            border-color: #ff9f43;
            box-shadow: 0 0 0 3px rgba(255, 159, 67, 0.1);
            outline: none;
        }

        .btn-apply {
            padding: 10px 28px;
            background-color: #ff9f43;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }

        .btn-apply:hover {
            background-color: #e68a35;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 159, 67, 0.3);
        }

        .btn-apply:active {
            transform: translateY(0);
        }

        /* Summary Cards */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #ff9f43;
            transition: all 0.3s;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .summary-card-label {
            font-size: 12px;
            font-weight: 600;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .summary-card-value {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            word-break: break-word;
        }

        .summary-card-subtitle {
            font-size: 12px;
            color: #95a5a6;
            margin-top: 8px;
        }

        /* Tabs */
        .tabs-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .tabs-header {
            display: flex;
            border-bottom: 2px solid #ecf0f1;
            background-color: #f8f9fa;
        }

        .tab-button {
            flex: 1;
            padding: 16px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #7f8c8d;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
        }

        .tab-button:hover {
            background-color: #ecf0f1;
            color: #ff9f43;
        }

        .tab-button.active {
            color: #ff9f43;
            border-bottom-color: #ff9f43;
        }

        .tabs-content {
            padding: 30px;
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .data-table thead {
            background-color: #f8f9fa;
            border-bottom: 2px solid #ecf0f1;
        }

        .data-table thead th {
            padding: 14px 12px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        .data-table tbody tr {
            border-bottom: 1px solid #ecf0f1;
            transition: background-color 0.2s;
        }

        .data-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .data-table tbody td {
            padding: 12px;
            color: #2c3e50;
        }

        .data-table .rank {
            font-weight: 700;
            color: #ff9f43;
            min-width: 30px;
        }

        .data-table .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #95a5a6;
        }

        .data-table .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #bdc3c7;
            display: block;
        }

        /* Loading State */
        .loading {
            display: none;
            text-align: center;
            padding: 40px;
        }

        .loading.active {
            display: block;
        }

        .loading i {
            font-size: 36px;
            color: #ff9f43;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Expandable row styles for mobile view */
        .expandable-details-row {
            display: none;
        }

        .expandable-details-row.show {
            display: table-row !important;
        }

        .expandable-details-row td {
            display: table-cell !important;
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            background: #f8f9fa !important;
            border-top: none !important;
            text-align: left !important;
        }

        .expandable-details-content {
            padding: 15px;
            background: #f8f9fa;
            border-top: 2px solid #e0e0e0;
        }

        .expandable-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #ecf0f1;
            flex-wrap: wrap;
            gap: 8px;
        }

        .expandable-detail-row:last-child {
            border-bottom: none;
        }

        .expandable-detail-label {
            font-weight: 600;
            color: #595b5d;
            font-size: 13px;
            flex: 0 1 auto;
            min-width: 100px;
        }

        .expandable-detail-value {
            color: #1b2850;
            font-size: 13px;
            text-align: right;
            flex: 1 1 auto;
            word-break: break-word;
            font-weight: 600;
        }

        .toggle-details-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            appearance: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-details-btn:hover i {
            transform: scale(1.1);
        }

        .toggle-details-btn i {
            font-size: 20px;
            transition: all 0.3s;
        }

        /* Responsive */
        @media (max-width: 1199px) {
            .filter-row {
                flex-direction: column;
            }

            .filter-group {
                min-width: 100%;
            }

            .summary-cards {
                grid-template-columns: 1fr;
            }

            .tab-button {
                font-size: 12px;
                padding: 12px 16px;
            }

            .tabs-content {
                padding: 15px;
            }

            .data-table {
                font-size: 12px;
                table-layout: fixed;
            }

            .data-table thead th,
            .data-table tbody tr:not(.expandable-details-row) td {
                padding: 10px 8px;
            }

            /* Hot Selling Items mobile layout */
            #itemsTable th:nth-child(n+3):not(:last-child),
            #itemsTable tbody tr:not(.expandable-details-row) td:nth-child(n+3):not(:last-child) {
                display: none;
            }

            #itemsTable th:nth-child(1),
            #itemsTable tbody tr:not(.expandable-details-row) td:nth-child(1) {
                width: 15%;
                text-align: left;
            }

            #itemsTable th:nth-child(2),
            #itemsTable tbody tr:not(.expandable-details-row) td:nth-child(2) {
                width: calc(85% - 40px);
                text-align: left;
                word-break: break-word;
                overflow-wrap: break-word;
            }

            /* Hot Customers mobile layout */
            #customersTable th:nth-child(n+3):not(:last-child),
            #customersTable tbody tr:not(.expandable-details-row) td:nth-child(n+3):not(:last-child) {
                display: none;
            }

            #customersTable th:nth-child(1),
            #customersTable tbody tr:not(.expandable-details-row) td:nth-child(1) {
                width: 15%;
                text-align: left;
            }

            #customersTable th:nth-child(2),
            #customersTable tbody tr:not(.expandable-details-row) td:nth-child(2) {
                width: calc(85% - 40px);
                text-align: left;
                word-break: break-word;
                overflow-wrap: break-word;
            }

            /* Details column for both tables on mobile */
            #itemsTable th:last-child,
            #itemsTable tbody tr:not(.expandable-details-row) td:last-child,
            #customersTable th:last-child,
            #customersTable tbody tr:not(.expandable-details-row) td:last-child {
                display: table-cell !important;
                width: 40px !important;
                text-align: right !important;
                vertical-align: middle;
            }

            #itemsTable th:last-child,
            #customersTable th:last-child {
                background-color: #f8f9fa;
                border-bottom: 2px solid #ecf0f1;
            }
        }

        /* Desktop view - hide Details column completely */
        @media (min-width: 1200px) {
            #itemsTable th:last-child,
            #itemsTable tbody tr:not(.expandable-details-row) td:last-child,
            #customersTable th:last-child,
            #customersTable tbody tr:not(.expandable-details-row) td:last-child {
                display: none !important;
            }

            #itemsTable th:nth-child(1),
            #itemsTable tbody tr:not(.expandable-details-row) td:nth-child(1),
            #itemsTable th:nth-child(2),
            #itemsTable tbody tr:not(.expandable-details-row) td:nth-child(2),
            #itemsTable th:nth-child(3),
            #itemsTable tbody tr:not(.expandable-details-row) td:nth-child(3),
            #itemsTable th:nth-child(4),
            #itemsTable tbody tr:not(.expandable-details-row) td:nth-child(4),
            #itemsTable th:nth-child(5),
            #itemsTable tbody tr:not(.expandable-details-row) td:nth-child(5),
            #itemsTable th:nth-child(6),
            #itemsTable tbody tr:not(.expandable-details-row) td:nth-child(6),
            #itemsTable th:nth-child(7),
            #itemsTable tbody tr:not(.expandable-details-row) td:nth-child(7),
            #customersTable th:nth-child(1),
            #customersTable tbody tr:not(.expandable-details-row) td:nth-child(1),
            #customersTable th:nth-child(2),
            #customersTable tbody tr:not(.expandable-details-row) td:nth-child(2),
            #customersTable th:nth-child(3),
            #customersTable tbody tr:not(.expandable-details-row) td:nth-child(3),
            #customersTable th:nth-child(4),
            #customersTable tbody tr:not(.expandable-details-row) td:nth-child(4),
            #customersTable th:nth-child(5),
            #customersTable tbody tr:not(.expandable-details-row) td:nth-child(5),
            #customersTable th:nth-child(6),
            #customersTable tbody tr:not(.expandable-details-row) td:nth-child(6) {
                display: table-cell !important;
            }
        }

        @media (max-width: 768px) {
            .data-table {
                font-size: 11px;
            }

            .data-table thead th,
            .data-table tbody tr:not(.expandable-details-row) td {
                padding: 8px 6px;
            }

            .toggle-details-btn {
                width: 28px;
                height: 28px;
                font-size: 16px;
            }

            #itemsTable th:nth-child(1),
            #itemsTable tbody tr:not(.expandable-details-row) td:nth-child(1) {
                width: 12%;
            }

            #itemsTable th:nth-child(2),
            #itemsTable tbody tr:not(.expandable-details-row) td:nth-child(2) {
                width: calc(88% - 36px);
            }

            #customersTable th:nth-child(1),
            #customersTable tbody tr:not(.expandable-details-row) td:nth-child(1) {
                width: 12%;
            }

            #customersTable th:nth-child(2),
            #customersTable tbody tr:not(.expandable-details-row) td:nth-child(2) {
                width: calc(88% - 36px);
            }

            #itemsTable th:last-child,
            #itemsTable tbody tr:not(.expandable-details-row) td:last-child,
            #customersTable th:last-child,
            #customersTable tbody tr:not(.expandable-details-row) td:last-child {
                width: 36px !important;
                text-align: right !important;
            }
        }

        /* No data placeholder */
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }

        .no-data i {
            font-size: 64px;
            color: #bdc3c7;
            margin-bottom: 20px;
            display: block;
        }

        .no-data p {
            font-size: 14px;
        }
        /* ═════ New Mobile/Tablet UI ═════ */
        .sp-m-only,
        .sp-filter-backdrop,
        .sp-filter-sheet { display: none; }

        @media (max-width: 1024.98px),
            (width: 1032px) and (height: 1376px),
            (width: 1280px) and (height: 800px) {

            .sales-pool-container.sp-page { padding: 10px 10px 80px !important; background: transparent; min-height: 0; }
            .sp-desktop-only { display: none !important; }
            .sp-m-only { display: block; }

            /* Header */
            .sp-m-head {
                background: #fff; border-radius: 12px; padding: 12px;
                margin-bottom: 10px; box-shadow: 0 1px 3px rgba(16,24,40,.08);
            }
            .sp-m-title { font-size: 16px; font-weight: 700; color: #1b2850; }
            .sp-m-sub { font-size: 12px; color: #8b93a7; margin-top: 2px; }

            /* Filter bar */
            .sp-filter-bar {
                display: flex; align-items: center; gap: 8px; background: #fff;
                border-radius: 12px; padding: 8px 10px; margin-bottom: 10px;
                box-shadow: 0 1px 3px rgba(16,24,40,.08);
            }
            .sp-filter-summary {
                flex: 1 1 auto; min-width: 0; display: flex; align-items: center; gap: 6px;
                padding: 8px 10px; background: #f8fafc; border: 1px solid #e2e8f0;
                border-radius: 8px; cursor: pointer;
            }
            .sp-filter-summary-text {
                font-size: 12.5px; font-weight: 600; color: #475467;
                white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            }
            .sp-filter-toggle {
                flex: 0 0 38px; width: 38px; height: 38px; border: 0; border-radius: 50%;
                background: #f05a6a; color: #fff; font-size: 15px;
                display: inline-flex; align-items: center; justify-content: center;
                box-shadow: 0 2px 6px rgba(240,90,106,.35);
            }

            /* Bottom sheet */
            .sp-filter-backdrop {
                display: block; position: fixed; inset: 0; z-index: 1055;
                background: rgba(16,24,40,.45); opacity: 0; pointer-events: none;
                transition: opacity .2s ease;
            }
            .sp-filter-sheet {
                display: flex; flex-direction: column; position: fixed; left: 0; right: 0; bottom: 0;
                z-index: 1060; max-height: 85vh; background: #fff;
                border-radius: 16px 16px 0 0; box-shadow: 0 -8px 24px rgba(16,24,40,.18);
                transform: translateY(105%); transition: transform .25s ease;
            }
            body.sp-filter-open { overflow: hidden !important; }
            body.sp-filter-open .sp-filter-backdrop { opacity: 1; pointer-events: auto; }
            body.sp-filter-open .sp-filter-sheet { transform: translateY(0); }
            body.sp-filter-open .mobile-bottom-nav,
            body.sp-filter-open .floating-calculator-wrapper { display: none !important; }

            .sp-filter-sheet-head {
                display: flex; align-items: center; justify-content: space-between;
                padding: 16px 18px; border-bottom: 1px solid #eef0f4;
                font-size: 16px; font-weight: 600; color: #1b2850;
            }
            .sp-filter-sheet-close { border: 0; background: transparent; color: #64748b; font-size: 20px; }
            .sp-filter-sheet-body { flex: 1 1 auto; overflow-y: auto; padding: 14px 18px; }
            .sp-filter-sheet-body .form-control,
            .sp-filter-sheet-body .form-select { height: 40px; border-radius: 8px; }
            .sp-filter-sheet-foot {
                display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
                padding: 14px 18px calc(14px + env(safe-area-inset-bottom));
                border-top: 1px solid #eef0f4;
            }
            .sp-filter-clear, .sp-filter-apply {
                height: 44px; border: 0; border-radius: 22px; font-size: 14.5px; font-weight: 600;
            }
            .sp-filter-clear { background: #f1f5f9; color: #475467; }
            .sp-filter-apply { background: #ff9f43; color: #fff; }

            /* Summary 2x2 */
            .sp-m-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px; }
            .sp-stat {
                background: #fff; border-radius: 12px; padding: 12px;
                border-left: 3px solid #ff9f43; box-shadow: 0 1px 3px rgba(16,24,40,.08);
                min-width: 0;
            }
            .sp-stat-label { font-size: 10.5px; font-weight: 700; color: #8b93a7; text-transform: uppercase; }
            .sp-stat-value { font-size: 16px; font-weight: 800; color: #1b2850; margin-top: 3px; word-break: break-word; }
            .sp-stat-sub { font-size: 11px; color: #8b93a7; margin-top: 2px; }

            /* Tabs */
            .sp-m-tabs {
                display: grid; grid-template-columns: 1fr 1fr; gap: 8px;
                background: #fff; border-radius: 12px; padding: 6px; margin-bottom: 10px;
                box-shadow: 0 1px 3px rgba(16,24,40,.08);
            }
            .sp-m-tab {
                height: 36px; border: 0; border-radius: 8px; background: #f1f2f6;
                color: #475467; font-size: 12px; font-weight: 700;
            }
            .sp-m-tab.active { background: #ff9f43; color: #fff; }
            .sp-m-panel { display: none; }
            .sp-m-panel.active { display: block; }

            /* Cards */
            .sp-card-list { display: flex; flex-direction: column; gap: 8px; }
            .sp-card {
                padding: 12px; background: #fff; border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16,24,40,.08);
            }
            .sp-card-top { display: flex; align-items: flex-start; gap: 8px; }
            .sp-rank {
                flex: 0 0 auto; font-size: 10px; font-weight: 700; padding: 2px 9px;
                border-radius: 12px; background: #fff3e6; color: #e07b14;
            }
            .sp-card-name {
                flex: 1 1 auto; min-width: 0; font-size: 14px; font-weight: 600;
                color: #1b2850; line-height: 1.3; overflow-wrap: anywhere;
            }
            .sp-card-amount { font-size: 14px; font-weight: 700; color: #27ae60; white-space: nowrap; }
            .sp-card-sub { margin-top: 3px; font-size: 11px; color: #8b93a7; }
            .sp-card-foot {
                display: flex; flex-wrap: wrap; gap: 8px 18px; margin-top: 8px;
                padding-top: 8px; border-top: 1px dashed #eceef3;
            }
            .sp-stat-mini span { display: block; font-size: 11px; color: #8b93a7; }
            .sp-stat-mini strong { display: block; font-size: 13px; color: #1b2850; }
            .sp-empty {
                padding: 32px 12px; text-align: center; background: #fff;
                border-radius: 12px; color: #8a92a6; font-size: 13px;
            }

            /* Dark mode */
            :root[data-theme="dark"] .sp-m-head,
            :root[data-theme="dark"] .sp-filter-bar,
            :root[data-theme="dark"] .sp-filter-sheet,
            :root[data-theme="dark"] .sp-stat,
            :root[data-theme="dark"] .sp-m-tabs,
            :root[data-theme="dark"] .sp-card,
            :root[data-theme="dark"] .sp-empty { background: #1e293b !important; }
            :root[data-theme="dark"] .sp-m-title,
            :root[data-theme="dark"] .sp-stat-value,
            :root[data-theme="dark"] .sp-card-name,
            :root[data-theme="dark"] .sp-stat-mini strong,
            :root[data-theme="dark"] .sp-filter-sheet-head { color: #f8fafc !important; }
            :root[data-theme="dark"] .sp-filter-summary { background: #0f172a !important; border-color: #334155 !important; }
            :root[data-theme="dark"] .sp-m-tab { background: #0f172a; color: #94a3b8; }
            :root[data-theme="dark"] .sp-m-tab.active { background: #ff9f43; color: #fff; }
        }

        /* Tablet: cards 2 per row */
        @media (min-width: 768px) and (max-width: 1024.98px),
            (width: 1032px) and (height: 1376px),
            (width: 1280px) and (height: 800px) {
            .sp-m-stats { grid-template-columns: repeat(4, 1fr); }
            .sp-card-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; align-items: start; }
            .sp-card-list .sp-empty { grid-column: 1 / -1; }
            .sp-filter-sheet { max-width: 520px; left: 50%; right: auto; width: 100%; margin-left: -260px; }
        }
    </style>

    <div class="sales-pool-container sp-page">
        {{-- ══ Mobile/Tablet UI ══ --}}
<div class="sp-m-head sp-m-only">
    <div class="sp-m-title">Sales Pool Report</div>
    <div class="sp-m-sub">Top-selling items and customers</div>
</div>

<div class="sp-filter-bar sp-m-only">
    <div class="sp-filter-summary" id="spFilterSummaryTrigger">
        <i class="far fa-calendar-alt text-warning me-2"></i>
        <span class="sp-filter-summary-text" id="spFilterSummaryText">Select period</span>
    </div>
    <button type="button" class="sp-filter-toggle" id="spFilterToggle" aria-label="Filters">
        <i class="fas fa-sliders-h"></i>
    </button>
</div>

<div class="sp-filter-backdrop" id="spFilterBackdrop"></div>
<div class="sp-filter-sheet" id="spFilterSheet" role="dialog" aria-modal="true">
    <div class="sp-filter-sheet-head">
        <span><i class="fas fa-sliders-h me-2 text-warning"></i>Filter</span>
        <button type="button" class="sp-filter-sheet-close" id="spFilterSheetClose" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="sp-filter-sheet-body">
        <div class="mb-3">
            <label class="form-label fw-bold">From Date</label>
            <input type="date" id="m_fromDate" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">To Date</label>
            <input type="date" id="m_toDate" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Customer</label>
            <select id="m_customerFilter" class="form-select">
                <option value="0">All Customers</option>
            </select>
        </div>
    </div>
    <div class="sp-filter-sheet-foot">
        <button type="button" class="sp-filter-clear" id="spFilterClear">Reset</button>
        <button type="button" class="sp-filter-apply" id="spFilterApply">Apply Filter</button>
    </div>
</div>

<div class="sp-m-stats sp-m-only">
    <div class="sp-stat">
        <div class="sp-stat-label">Total Bills</div>
        <div class="sp-stat-value" id="sm-bills">-</div>
    </div>
    <div class="sp-stat">
        <div class="sp-stat-label">Qty Sold</div>
        <div class="sp-stat-value" id="sm-qty">-</div>
    </div>
    <div class="sp-stat">
        <div class="sp-stat-label">Total Sales</div>
        <div class="sp-stat-value" id="sm-sales">-</div>
    </div>
    <div class="sp-stat">
        <div class="sp-stat-label">Top Customer</div>
        <div class="sp-stat-value" id="sm-top" style="font-size:14px;">-</div>
        <div class="sp-stat-sub" id="sm-top-amt">-</div>
    </div>
</div>

<div class="sp-m-tabs sp-m-only">
    <button type="button" class="sp-m-tab active" data-sptab="items"><i class="fas fa-fire me-1"></i>Hot Items</button>
    <button type="button" class="sp-m-tab" data-sptab="customers"><i class="fas fa-star me-1"></i>Hot Customers</button>
</div>

<div class="sp-m-panel active sp-m-only" id="spPanelItems">
    <div class="sp-card-list" id="spItemsCards"></div>
</div>
<div class="sp-m-panel sp-m-only" id="spPanelCustomers">
    <div class="sp-card-list" id="spCustomersCards"></div>
</div>
        <!-- Header -->
        <div class="sales-pool-header sp-desktop-only">
            <h2>Sales Pool Report</h2>
            <p>Analyze your top-selling items and customers within a date range</p>
        </div>

        <!-- Filter Section -->
        <div class="filter-section sp-desktop-only">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="fromDate">From Date</label>
                    <input type="date" id="fromDate" name="from_date" 
                           value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                </div>
                <div class="filter-group">
                    <label for="toDate">To Date</label>
                    <input type="date" id="toDate" name="to_date" 
                           value="{{ date('Y-m-d') }}">
                </div>
                <!-- <div class="filter-group">
                    <label for="staffFilter">Staff</label>
                    <select id="staffFilter" name="staff_id" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; background-color: #fff;">
                        <option value="0">All Staff</option>
                    </select>
                </div> -->
                <div class="filter-group">
                    <label for="customerFilter">Customer</label>
                    <select id="customerFilter" name="customer_id" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; background-color: #fff;">
                        <option value="0">All Customers</option>
                    </select>
                </div>
                <div class="filter-group" style="flex: 0 0 auto;">
                    <button class="btn-apply" id="applyFilters">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards sp-desktop-only">
            <div class="summary-card">
                <div class="summary-card-label">Total Bills</div>
                <div class="summary-card-value" id="totalBills">-</div>
                <div class="summary-card-subtitle">Confirmed sales orders</div>
            </div>
            <div class="summary-card">
                <div class="summary-card-label">Total Qty Sold</div>
                <div class="summary-card-value" id="totalQtySold">-</div>
                <div class="summary-card-subtitle">Units sold</div>
            </div>
            <div class="summary-card">
                <div class="summary-card-label">Total Sales</div>
                <div class="summary-card-value" id="totalSales">-</div>
                <div class="summary-card-subtitle">Total bill value</div>
            </div>
            <div class="summary-card">
                <div class="summary-card-label">Top Customer</div>
                <div class="summary-card-value" id="topCustomer" style="font-size: 18px;">-</div>
                <div class="summary-card-subtitle" id="topCustomerAmount">-</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs-container sp-desktop-only">
            <div class="tabs-header">
                <button class="tab-button active" data-tab="hotItems">
                    <i class="fas fa-fire"></i> Hot Selling Items
                </button>
                <button class="tab-button" data-tab="hotCustomers">
                    <i class="fas fa-star"></i> Hot Customers
                </button>
            </div>

            <div class="tabs-content">
                <!-- Hot Selling Items Tab -->
                <div class="tab-panel active" id="hotItems">
                    <div class="loading" id="loadingItems">
                        <i class="fas fa-circle-notch"></i>
                        <p>Loading data...</p>
                    </div>
                    <table class="data-table" id="itemsTable" style="display:none;">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>SKU</th>
                                <th style="text-align: right;">Qty Sold</th>
                                <th style="text-align: right;">Amount</th>
                                <th style="text-align: center;">Bills</th>
                                <th style="text-align: right; display: none;">Details</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                        </tbody>
                    </table>
                    <div class="no-data" id="noDataItems" style="display:none;">
                        <i class="fas fa-inbox"></i>
                        <p>No selling data available for the selected date range.</p>
                    </div>
                </div>

                <!-- Hot Customers Tab -->
                <div class="tab-panel" id="hotCustomers">
                    <div class="loading" id="loadingCustomers">
                        <i class="fas fa-circle-notch"></i>
                        <p>Loading data...</p>
                    </div>
                    <table class="data-table" id="customersTable" style="display:none;">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Customer Name</th>
                                <th style="text-align: center;">Total Bills</th>
                                <th style="text-align: right;">Qty Purchased</th>
                                <th style="text-align: right;">Total Amount Spent</th>
                                <th>Top Purchased Item</th>
                                <th style="text-align: right; display: none;">Details</th>
                            </tr>
                        </thead>
                        <tbody id="customersTableBody">
                        </tbody>
                    </table>
                    <div class="no-data" id="noDataCustomers" style="display:none;">
                        <i class="fas fa-inbox"></i>
                        <p>No customer data available for the selected date range.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.js"></script>

    <script>
        const ROUTES = {
            summary: '{{ route("sales-pool-report.summary") }}',
            hotItems: '{{ route("sales-pool-report.items") }}',
            hotCustomers: '{{ route("sales-pool-report.customers") }}',
            customers: '{{ route("sales-pool-report.get-customers") }}',
            staffMembers: '{{ route("sales-pool-report.get-staff") }}'
        };

        let currencySymbol = '₹';
        let currencyPosition = 'left';

        // Initialize
        $(document).ready(function() {
            // Load dropdown data
            loadStaffMembers();
            loadCustomers();

            // Load initial data
            loadAllData();

            // Tab switching
            $('.tab-button').click(function() {
                const tabName = $(this).data('tab');
                switchTab(tabName);
            });

            // Apply filters
            $('#applyFilters').click(function() {
                loadAllData();
            });
        });

        function loadStaffMembers() {
            $.ajax({
                url: ROUTES.staffMembers,
                type: 'GET',
                success: function(response) {
                    if (response.status && response.data.length > 0) {
                        const $select = $('#staffFilter');
                        response.data.forEach(staff => {
                            $select.append(`<option value="${staff.id}">${escapeHtml(staff.name)}</option>`);
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Staff members load error:', xhr);
                }
            });
        }

        function loadCustomers() {
            $.ajax({
                url: ROUTES.customers,
                type: 'GET',
                success: function(response) {
                    if (response.status && response.data.length > 0) {
                        const $select = $('#customerFilter');
                        response.data.forEach(customer => {
                            $select.append(`<option value="${customer.id}">${escapeHtml(customer.name)}</option>`);
                            $('#m_customerFilter').append(`<option value="${customer.id}">${escapeHtml(customer.name)}</option>`);
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Customers load error:', xhr);
                }
            });
        }

        function switchTab(tabName) {
            // Update button states
            $('.tab-button').removeClass('active');
            $('.tab-button[data-tab="' + tabName + '"]').addClass('active');

            // Update panel visibility
            $('.tab-panel').removeClass('active');
            $('#' + tabName).addClass('active');
        }

        function getDateRange() {
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();

            if (!fromDate || !toDate) {
                alert('Please select both From Date and To Date.');
                return null;
            }

            return {
                from_date: fromDate,
                to_date: toDate,
                staff_id: $('#staffFilter').val() || '0',
                customer_id: $('#customerFilter').val() || '0'
            };
        }

        function loadAllData() {
            const dateRange = getDateRange();
            if (!dateRange) return;
            updateSpFilterBar();

            loadSummary(dateRange);
            loadHotSellingItems(dateRange);
            loadHotCustomers(dateRange);
        }

        function loadSummary(dateRange) {
            $.ajax({
                url: ROUTES.summary,
                type: 'GET',
                data: dateRange,
                success: function(response) {
                    if (response.status) {
                        const data = response.data;
                        currencySymbol = response.currency_symbol;
                        currencyPosition = response.currency_position;

                        $('#totalBills').text(formatNumber(data.total_bills));
                        $('#totalQtySold').text(formatNumber(data.total_qty_sold));
                        $('#totalSales').text(formatCurrency(data.total_sales));
                        $('#topCustomer').text(data.top_customer);
                        $('#topCustomerAmount').text(formatCurrency(data.top_customer_amount));
                        $('#sm-bills').text(formatNumber(data.total_bills));
                        $('#sm-qty').text(formatNumber(data.total_qty_sold));
                        $('#sm-sales').text(formatCurrency(data.total_sales));
                        $('#sm-top').text(data.top_customer);
                        $('#sm-top-amt').text(formatCurrency(data.top_customer_amount));
                    }
                },
                error: function(xhr) {
                    console.error('Summary load error:', xhr);
                }
            });
        }

        function loadHotSellingItems(dateRange) {
            const $loading = $('#loadingItems');
            const $table = $('#itemsTable');
            const $tbody = $('#itemsTableBody');
            const $noData = $('#noDataItems');

            $loading.addClass('active');
            $table.hide();
            $noData.hide();
            $tbody.html('');

            $.ajax({
                url: ROUTES.hotItems,
                type: 'GET',
                data: dateRange,
                success: function(response) {
                    $loading.removeClass('active');

                    if (response.status && response.data.length > 0) {
                        currencySymbol = response.currency_symbol;
                        currencyPosition = response.currency_position;
                         renderItemCards(response.data);

                        response.data.forEach((item, index) => {
                            const rowId = `item-row-${index}`;
                            const detailsRowId = `item-details-${index}`;
                            
                            const row = `
                                <tr id="${rowId}">
                                    <td class="rank">#${item.rank}</td>
                                    <td>${escapeHtml(item.product_name)}</td>
                                    <td style="display: none;">${escapeHtml(item.category)}</td>
                                    <td style="display: none;">${escapeHtml(item.sku)}</td>
                                    <td style="text-align: right; display: none;">${formatNumber(item.qty_sold)}</td>
                                    <td style="text-align: right; display: none;">${formatCurrency(item.amount)}</td>
                                    <td style="text-align: center; display: none;"><span style="background: #ff9f43; color: white; padding: 4px 8px; border-radius: 3px; display: inline-block;">${item.bills}</span></td>
                                    <td style="text-align: right;">
                                        <button class="toggle-details-btn" data-target="${detailsRowId}" type="button">
                                            <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="expandable-details-row" id="${detailsRowId}">
                                    <td colspan="8">
                                        <div class="expandable-details-content">
                                            <div class="expandable-detail-row">
                                                <span class="expandable-detail-label">Category:</span>
                                                <span class="expandable-detail-value">${escapeHtml(item.category)}</span>
                                            </div>
                                            <div class="expandable-detail-row">
                                                <span class="expandable-detail-label">SKU:</span>
                                                <span class="expandable-detail-value">${escapeHtml(item.sku)}</span>
                                            </div>
                                            <div class="expandable-detail-row">
                                                <span class="expandable-detail-label">Qty Sold:</span>
                                                <span class="expandable-detail-value">${formatNumber(item.qty_sold)}</span>
                                            </div>
                                            <div class="expandable-detail-row">
                                                <span class="expandable-detail-label">Amount:</span>
                                                <span class="expandable-detail-value">${formatCurrency(item.amount)}</span>
                                            </div>
                                            <div class="expandable-detail-row">
                                                <span class="expandable-detail-label">Bills:</span>
                                                <span class="expandable-detail-value"><span style="background: #ff9f43; color: white; padding: 4px 8px; border-radius: 3px; display: inline-block;">${item.bills}</span></span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            $tbody.append(row);
                        });

                        $table.show();
                    } else {
                        $noData.show();
                        renderItemCards([]);
                    }
                },
                error: function(xhr) {
                    $loading.removeClass('active');
                    $noData.show();
                    renderItemCards([]);
                    console.error('Hot items load error:', xhr);
                }
            });
        }

        function loadHotCustomers(dateRange) {
            const $loading = $('#loadingCustomers');
            const $table = $('#customersTable');
            const $tbody = $('#customersTableBody');
            const $noData = $('#noDataCustomers');

            $loading.addClass('active');
            $table.hide();
            $noData.hide();
            $tbody.html('');

            $.ajax({
                url: ROUTES.hotCustomers,
                type: 'GET',
                data: dateRange,
                success: function(response) {
                    $loading.removeClass('active');

                    if (response.status && response.data.length > 0) {
                        currencySymbol = response.currency_symbol;
                        currencyPosition = response.currency_position;
                        renderCustomerCards(response.data);

                        response.data.forEach((customer, index) => {
                            const rowId = `customer-row-${index}`;
                            const detailsRowId = `customer-details-${index}`;
                            
                            const row = `
                                <tr id="${rowId}">
                                    <td class="rank">#${customer.rank}</td>
                                    <td>${escapeHtml(customer.customer_name)}</td>
                                    <td style="text-align: center; display: none;"><span style="background: #3498db; color: white; padding: 4px 8px; border-radius: 3px; display: inline-block;">${customer.total_bills}</span></td>
                                    <td style="text-align: right; display: none;">${formatNumber(customer.total_qty_purchased)}</td>
                                    <td style="text-align: right; font-weight: 600; color: #27ae60; display: none;">${formatCurrency(customer.total_amount_spent)}</td>
                                    <td style="display: none;">${escapeHtml(customer.top_purchased_item)}</td>
                                    <td style="text-align: right;">
                                        <button class="toggle-details-btn" data-target="${detailsRowId}" type="button">
                                            <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="expandable-details-row" id="${detailsRowId}">
                                    <td colspan="7">
                                        <div class="expandable-details-content">
                                            <div class="expandable-detail-row">
                                                <span class="expandable-detail-label">Total Bills:</span>
                                                <span class="expandable-detail-value"><span style="background: #3498db; color: white; padding: 4px 8px; border-radius: 3px; display: inline-block;">${customer.total_bills}</span></span>
                                            </div>
                                            <div class="expandable-detail-row">
                                                <span class="expandable-detail-label">Qty Purchased:</span>
                                                <span class="expandable-detail-value">${formatNumber(customer.total_qty_purchased)}</span>
                                            </div>
                                            <div class="expandable-detail-row">
                                                <span class="expandable-detail-label">Total Amount:</span>
                                                <span class="expandable-detail-value" style="color: #27ae60;">${formatCurrency(customer.total_amount_spent)}</span>
                                            </div>
                                            <div class="expandable-detail-row">
                                                <span class="expandable-detail-label">Top Item:</span>
                                                <span class="expandable-detail-value">${escapeHtml(customer.top_purchased_item)}</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            $tbody.append(row);
                        });

                        $table.show();
                    } else {
                        $noData.show();
                        renderCustomerCards([]);
                    }
                },
                error: function(xhr) {
                    $loading.removeClass('active');
                    $noData.show();
                    renderCustomerCards([]);
                    console.error('Hot customers load error:', xhr);
                }
            });
        }

        function formatCurrency(amount) {
            const formatted = parseFloat(amount).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            if (currencyPosition === 'right') {
                return formatted + ' ' + currencySymbol;
            } else {
                return currencySymbol + ' ' + formatted;
            }
        }

        function formatNumber(num) {
            return parseInt(num).toLocaleString('en-IN');
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Event delegation for toggle details button click
        $(document).on('click', '.toggle-details-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $btn = $(this);
            const targetId = $btn.attr('data-target');
            const $detailsRow = $(`#${targetId}`);
            const $icon = $btn.find('i');
            
            if ($detailsRow.length === 0) return;
            
            $detailsRow.toggleClass('show');
            
            if ($detailsRow.hasClass('show')) {
                // Expanded - show minus circle in red
                $icon.removeClass('fa-plus-circle')
                    .addClass('fa-minus-circle')
                    .css('color', '#dc3545');
            } else {
                // Collapsed - show plus circle in orange
                $icon.removeClass('fa-minus-circle')
                    .addClass('fa-plus-circle')
                    .css('color', '#ff9f43');
            }
        });
        /* ═════ New Mobile/Tablet UI ═════ */
function spDateLabel(s) {
    if (!s) return '';
    const d = new Date(s);
    const m = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return `${String(d.getDate()).padStart(2,'0')} ${m[d.getMonth()]} ${d.getFullYear()}`;
}

function updateSpFilterBar() {
    const f = $('#fromDate').val(), t = $('#toDate').val();
    const cust = $('#customerFilter').val() !== '0' ? ' • ' + $('#customerFilter option:selected').text() : '';
    $('#spFilterSummaryText').text(`${spDateLabel(f)} - ${spDateLabel(t)}${cust}`);
}

function renderItemCards(list) {
    if (!list || !list.length) {
        $('#spItemsCards').html('<div class="sp-empty">No selling data for the selected range.</div>');
        return;
    }
    $('#spItemsCards').html(list.map(item => `
        <div class="sp-card">
            <div class="sp-card-top">
                <span class="sp-rank">#${item.rank}</span>
                <span class="sp-card-name">${escapeHtml(item.product_name)}</span>
                <span class="sp-card-amount">${formatCurrency(item.amount)}</span>
            </div>
            <div class="sp-card-sub">${escapeHtml(item.category)} • SKU: ${escapeHtml(item.sku)}</div>
            <div class="sp-card-foot">
                <div class="sp-stat-mini"><span>Qty Sold</span><strong>${formatNumber(item.qty_sold)}</strong></div>
                <div class="sp-stat-mini"><span>Bills</span><strong>${item.bills}</strong></div>
            </div>
        </div>`).join(''));
}

function renderCustomerCards(list) {
    if (!list || !list.length) {
        $('#spCustomersCards').html('<div class="sp-empty">No customer data for the selected range.</div>');
        return;
    }
    $('#spCustomersCards').html(list.map(c => `
        <div class="sp-card">
            <div class="sp-card-top">
                <span class="sp-rank">#${c.rank}</span>
                <span class="sp-card-name">${escapeHtml(c.customer_name)}</span>
                <span class="sp-card-amount">${formatCurrency(c.total_amount_spent)}</span>
            </div>
            <div class="sp-card-sub">Top item: ${escapeHtml(c.top_purchased_item)}</div>
            <div class="sp-card-foot">
                <div class="sp-stat-mini"><span>Bills</span><strong>${c.total_bills}</strong></div>
                <div class="sp-stat-mini"><span>Qty</span><strong>${formatNumber(c.total_qty_purchased)}</strong></div>
            </div>
        </div>`).join(''));
}

/* Tabs */
$(document).on('click', '.sp-m-tab', function () {
    const tab = $(this).data('sptab');
    $('.sp-m-tab').removeClass('active');
    $(this).addClass('active');
    $('.sp-m-panel').removeClass('active');
    $(tab === 'items' ? '#spPanelItems' : '#spPanelCustomers').addClass('active');
});

/* Filter sheet */
function openSpSheet() {
    $('#m_fromDate').val($('#fromDate').val());
    $('#m_toDate').val($('#toDate').val());
    $('#m_customerFilter').val($('#customerFilter').val() || '0');
    $('body').addClass('sp-filter-open');
}
function closeSpSheet() { $('body').removeClass('sp-filter-open'); }

$('#spFilterToggle, #spFilterSummaryTrigger').on('click', function (e) { e.preventDefault(); openSpSheet(); });
$('#spFilterSheetClose, #spFilterBackdrop').on('click', function (e) { e.preventDefault(); closeSpSheet(); });

$('#spFilterClear').on('click', function () {
    const t = new Date(), s = new Date(); s.setDate(s.getDate() - 30);
    const f = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
    $('#m_fromDate').val(f(s));
    $('#m_toDate').val(f(t));
    $('#m_customerFilter').val('0');
});

$('#spFilterApply').on('click', function () {
    $('#fromDate').val($('#m_fromDate').val());
    $('#toDate').val($('#m_toDate').val());
    $('#customerFilter').val($('#m_customerFilter').val());
    closeSpSheet();
    loadAllData();
});
    </script>
@endsection
