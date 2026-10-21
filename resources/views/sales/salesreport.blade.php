@extends('layout.app')

@section('title', 'Sales Report')

@section('content')
    @php
        $showSalesReportChart = \App\Services\StaffDepartmentScope::canShowReportChart(\App\Services\StaffDepartmentScope::REPORT_SALES);
    @endphp
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .dataTables_info {
            float: left;
            padding-right: 15px;
            font-size: 12px;
            color: #5e5873;
            font-weight: 600;
        }

        .dataTables_filter {
            display: none !important;
        }

        /* Custom Pagination Styling */
        .pagination .page-item .page-link {
            background-color: #5d6d7e;
            /* Dark gray for other pages */
            color: #fff;
            border: none;
            margin: 0 3px;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: bold;
        }

        /* Previous and Next buttons */
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
            /* Orange for active page */
            color: #fff;
            border: none;
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



        #DataTables_Table_0_info {
            float: left;
        }

        .table-scroll-top {
            display: none;
        }

        .table-scroll-top div {
            height: 1px;
        }

        /* Dropdown fixes for GST menu: closed by default, vertical list, proper alignment */
        .wordset .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            left: auto;
            flex-direction: column;
            z-index: 2000;
        }

        .wordset .dropdown-menu.show {
            display: block;
        }

        /* Dark Mode Overrides for Sales Chart */
        :root[data-theme="dark"] .sales-chart-body {
            background: #0f172a !important;
        }

        /* Product name color in table */
        .report-product-name {
            color: #2d3748;
        }

        :root[data-theme="dark"] .report-product-name {
            color: #ffffffff !important;
        }

        .wordset .dropdown-menu li {
            display: block;
            float: none;
        }

        .wordset .dropdown-menu .dropdown-item {
            display: block;
            /* white-space: nowrap; */
        }

        .total_expense {
            font-weight: 600;
            color: #1b2850;
            border: 1px solid #1b2850;
        }

        /* Mobile toggle button styles */
        .mobile-toggle-btn-table {
            background: #ff9f43;
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            min-width: 32px;
            min-height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s ease;
            padding: 0;
            margin: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .mobile-toggle-btn-table:hover {
            background: #ff8c2e;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .mobile-toggle-btn-table.minus {
            background: #dc3545;
        }

        .mobile-toggle-btn-table.minus:hover {
            background: #c82333;
        }

        /* Ensure Details column is centered */
        .datanew th.text-center,
        .datanew td.text-center {
            text-align: center !important;
        }

        .order-details-row {
            display: none;
        }

        .order-details-row.show {
            display: table-row !important;
        }

        /* .order-details-content {
            padding: 15px;
            background: #f8f9fa;
            border-top: 2px solid #ff9f43;
        } */

        .order-details-content .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .order-details-content .detail-item:last-child {
            border-bottom: none;
        }

        .order-details-content .detail-label {
            font-weight: 600;
            color: #495057;
        }

        .order-details-content .detail-value {
            color: #212529;
        }

        /* Product Name cell - allow wrapping */
        .datanew td.productimgname {
            white-space: normal !important;
        }

        .datanew td.productimgname>div {
            flex-wrap: wrap;
        }

        .datanew td.productimgname a {
            /* flex-wrap: wrap; */
            min-width: 0;
            /* allow shrinking */
        }

        .datanew td.productimgname span {
            white-space: normal !important;
            word-wrap: break-word;
            min-width: 0;
            flex: 1 1 auto;
        }


        /* Hide mobile toggle button and expandable details rows on desktop */
        /* @media screen and (min-width: 1025px) {
            .mobile-toggle-btn-table,
            .order-details-row {
                display: none !important;
            }
        } */
        @media screen and (min-width: 1025px) {
            .mobile-toggle-btn-table,
            .order-details-row {
                display: none !important;
            }

            .datanew thead th:nth-child(14), /* Details column hide on desktop */
            .datanew tbody td:nth-child(14) {
                display: none !important;
            }
        }

        /* Responsive table behavior for mobile/tablet */
        @media screen and (max-width: 1024px) {
             /* Hide all columns except: checkbox, Order Number, Details */
            .datanew thead th:nth-child(2),  /* Sr. No. */
            .datanew thead th:nth-child(4),  /* Product Name */
            .datanew thead th:nth-child(5),  /* Customer Name */
            .datanew thead th:nth-child(6),  /* Order Date */
            .datanew thead th:nth-child(7),  /* Customer Address */
            .datanew thead th:nth-child(8),  /* Category */
            .datanew thead th:nth-child(9),  /* Taxes */
            .datanew thead th:nth-child(10), /* Amount */
            .datanew thead th:nth-child(11), /* Qty */
            .datanew thead th:nth-child(12), /* Unit */
            .datanew thead th:nth-child(13), /* Action */
            .datanew tbody td:nth-child(2),
            .datanew tbody td:nth-child(4),
            .datanew tbody td:nth-child(5),
            .datanew tbody td:nth-child(6),
            .datanew tbody td:nth-child(7),
            .datanew tbody td:nth-child(8),
            .datanew tbody td:nth-child(9),
            .datanew tbody td:nth-child(10),
            .datanew tbody td:nth-child(11),
            .datanew tbody td:nth-child(12),
            .datanew tbody td:nth-child(13) {
                display: none !important;
            }

            /* Keep Details column visible */
            .datanew thead th:nth-child(14),
            .datanew tbody td:nth-child(14) {
                display: table-cell !important;
                text-align: center;
            }

            .table-responsive {
                overflow-x: visible !important;
            }
        }


        @media screen and (max-width: 767.98px) {

            .page-header {
                display: flex;
                justify-content: space-between;
                /* 🔥 main fix */
                align-items: center;
                flex-direction: row;
                flex-wrap: nowrap;
                gap: 10px;
            }

            .page-title h4 {
                margin: 0;
                /* font-size: 18px; */
                white-space: nowrap;
            }

            .total-expense-summary {
                width: auto;
                /* ❌ remove 100% */
                margin-top: 0;
                /* ❌ remove spacing */
            }

            .total-expense-summary h5 {
                margin: 0 !important;
                width: auto;
                /* ❌ remove 70% */
                text-align: right;
                /* align amount right */
                white-space: nowrap;
            }
        }

        /* Responsive chart card */
        @media screen and (max-width: 767.98px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start !important;
            }

            #sales-filter-summary {
                margin-top: 10px;
                width: 100%;
                white-space: normal !important;
            }

            #salesChartWrapper {
                -webkit-overflow-scrolling: touch;
            }

            #salesChartInner {
                height: 280px !important;
            }

            #salesProductChart {
                height: 280px !important;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 1024px) {
            #salesChartInner {
                height: 310px !important;
            }

            #salesProductChart {
                height: 310px !important;
            }
        }

        /* Responsive filters */
        @media screen and (max-width: 767.98px) {
            .table-top .search-set {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                justify-content: flex-start !important;
                width: 100%;
            }

            .search-set .form-select-sm {
                flex: 1;
                min-width: 120px;
            }

            .wordset {
                width: 100%;
                margin-top: 10px !important;
            }

            .wordset ul {
                width: 100%;
            }

            .wordset ul li {
                width: 100%;
            }

            .wordset ul li a button {
                width: 100%;
            }
        }

        @media screen and (min-width: 576px) and (max-width: 767.98px) {
            .search-set {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }
        }

        @media screen and (max-width: 767px) {
            .table-scroll-top {
                display: none !important;
            }

            .table-responsive {
                overflow-x: visible !important;
                overflow-y: visible !important;
            }

            .datanew {
                width: 100% !important;
                table-layout: auto !important;
            }

            .dataTables_length {
                margin-left: .8rem !important;
                margin-bottom: 1rem !important;
            }

            .dataTables_filter {
                text-align: left !important;
            }
        }

        .table tbody tr td {
            white-space: normal !important;
        }
        #generate-pdf {
            border-radius: 6px;
            padding: 7px 18px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            font-weight: 600;
        }

        #generate-pdf i {
            font-size: 14px;
        }
        /* ══ Mobile/Tablet UI (product-list style) ══ */
.sr-m-head,
.sr-filter-toggle,
.sr-filter-backdrop,
.sr-filter-sheet,
.sr-card-list { display: none; }

@media (max-width: 1024.98px), (width: 1032px) and (height: 1376px), (width: 1280px) and (height: 800px) {
    .content { padding: 6px 6px 90px !important; }
    .content > .page-header { display: none !important; }

    /* Header */
    .sr-m-head {
        display: block;
        margin-bottom: 8px;
        padding: 12px 10px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
    }
    .sr-m-head-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 10px;
    }
    .sr-m-title { margin-left: 2px; font-size: 15px; font-weight: 600; color: #1b2850; white-space: nowrap; }
    .sr-m-total { font-size: 13px; font-weight: 700; color: #ff9f43; white-space: nowrap; }
    .sr-m-tools { display: grid; grid-template-columns: 1fr; gap: 6px; }
    .sr-m-tool {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: 34px;
        border: 0;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }
    .sr-m-tool.is-red { background: #fdecee; color: #dc3545; }

    /* Chart card + original PDF wordset hide on mobile */
    .content .wordset { display: none !important; }

    /* Card wrapper transparent */
    .content > .card {
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        margin: 0 !important;
    }
    .content > .card > .card-body { padding: 0 !important; overflow: visible !important; }

    /* Filters box: only search row visible */
    #srFiltersBox { display: none !important; }
    .sr-search-wrap {
        margin: 0 0 8px !important;
        padding: 8px 10px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
    }
    .sr-search-wrap .search-set { flex-wrap: nowrap !important; }
    .sr-search-wrap .search-input { flex: 1 1 auto; min-width: 0; width: auto !important; }
    .sr-search-wrap .search-input input {
        height: 40px !important;
        width: 100% !important;
        border-radius: 10px !important;
        font-size: 13px;
    }
    .sr-search-wrap .btn-searchset { top: 50% !important; transform: translateY(-50%); }
    /* Product dropdown goes into sheet; hide here until moved */
    .sr-search-wrap .product-search { display: none; }

    /* Filter round button */
    .sr-filter-toggle {
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
    }
    .sr-filter-toggle.has-active { background: #e8334a; }
    .sr-filter-count {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 999px;
        background: #1b2850;
        color: #fff;
        font-size: 11px;
        line-height: 18px;
        text-align: center;
    }

    /* Bottom sheet */
    .sr-filter-backdrop {
        display: block;
        position: fixed;
        inset: 0;
        z-index: 1040;
        background: rgba(16, 24, 40, .45);
        opacity: 0;
        pointer-events: none;
        transition: opacity .2s ease;
    }
    .sr-filter-sheet {
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
    body.sr-filter-open .sr-filter-backdrop { opacity: 1; pointer-events: auto; }
    body.sr-filter-open .sr-filter-sheet { transform: translateY(0); }
    body.sr-filter-open { overflow: hidden; }
    body.sr-filter-open .mobile-bottom-nav,
    body.sr-filter-open .floating-calculator-wrapper { display: none !important; }

    .sr-filter-sheet-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 18px;
        border-bottom: 1px solid #eef0f4;
        font-size: 18px;
        font-weight: 600;
        color: #1b2850;
    }
    .sr-filter-sheet-close {
        width: 32px; height: 32px; padding: 0; border: 0;
        background: transparent; color: #2f6fd8; font-size: 20px;
    }
    .sr-filter-sheet-body { flex: 1 1 auto; overflow-y: auto; padding: 4px 18px; }
    .sr-filter-sheet-body .sr-filter-field {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 12px 0 !important;
        border-bottom: 1px solid #eef0f4;
    }
    .sr-filter-sheet-body .sr-filter-field > label {
        display: block;
        margin: 0 0 6px;
        font-size: 14px;
        color: #344054;
    }
    .sr-filter-sheet-body select,
    .sr-filter-sheet-body input {
        width: 100% !important;
        min-width: 0 !important;
        height: 40px !important;
        margin: 0 !important;
        border-radius: 8px !important;
        font-size: 14px;
    }
    .sr-filter-sheet-body .sr-filter-field > div { margin: 0 !important; width: 100%; }
    .sr-filter-sheet-foot {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        padding: 14px 18px calc(14px + env(safe-area-inset-bottom));
        border-top: 1px solid #eef0f4;
    }
    .sr-filter-clear, .sr-filter-apply {
        height: 46px; border: 0; border-radius: 24px; font-size: 15px; font-weight: 600;
    }
    .sr-filter-clear { background: #eef0f4; color: #475467; }
    .sr-filter-apply { background: #e8334a; color: #fff; }

    /* Table hidden, cards shown */
    .content .table-responsive { display: none !important; }
    .sr-card-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .sr-card {
        padding: 12px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
    }
    .sr-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; }
    .sr-card-name {
        color: #1b2850 !important;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        line-height: 1.3;
        min-width: 0;
        overflow-wrap: anywhere;
    }
    .sr-card-amount { font-size: 14px; font-weight: 700; color: #1b2850; white-space: nowrap; }
    .sr-card-sub {
        margin-top: 3px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 4px 8px;
        font-size: 11px;
        color: #8b93a7;
    }
    .sr-card-order {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 9px;
        border-radius: 12px;
        background: #fff3e6;
        color: #e07b14;
    }
    .sr-card-foot {
        display: flex;
        align-items: flex-end;
        gap: 16px;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px dashed #eceef3;
    }
    .sr-card-stat { min-width: 0; }
    .sr-card-stat span { display: block; font-size: 11px; color: #8b93a7; }
    .sr-card-stat strong {
        display: block;
        font-size: 13px;
        color: #1b2850;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .sr-card-view {
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        color: #8b93a7 !important;
        font-size: 16px;
    }
    .sr-empty {
        padding: 32px 12px;
        text-align: center;
        background: #fff;
        border-radius: 12px;
        color: #8a92a6;
        font-size: 13px;
    }

    /* Pagination white box */
    .content .pagination-controls {
        margin-top: 10px !important;
        padding: 10px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
    }

    :root[data-theme="dark"] .sr-m-head,
    :root[data-theme="dark"] .sr-search-wrap,
    :root[data-theme="dark"] .sr-filter-sheet,
    :root[data-theme="dark"] .sr-card,
    :root[data-theme="dark"] .sr-empty,
    :root[data-theme="dark"] .content .pagination-controls { background: #1e293b; }
    :root[data-theme="dark"] .sr-m-title,
    :root[data-theme="dark"] .sr-card-name,
    :root[data-theme="dark"] .sr-card-amount,
    :root[data-theme="dark"] .sr-card-stat strong,
    :root[data-theme="dark"] .sr-filter-sheet-head { color: #f8fafc !important; }
}
@media (max-width: 1024.98px), (width: 1032px) and (height: 1376px), (width: 1280px) and (height: 800px) {
    .sr-m-tools { grid-template-columns: 1fr 1fr !important; align-items: center; }
    .sr-m-selectall {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 34px;
        margin: 0;
        border-radius: 8px;
        background: #f1f2f6;
        color: #1b2850;
        font-size: 12px;
        font-weight: 600;
    }
    .sr-m-selectall input,
.sr-card-cb {
    -webkit-appearance: none;
    appearance: none;
    position: relative;
    flex-shrink: 0;
    width: 18px;
    height: 18px;
    margin: 0;
    border: 2px solid #ff9f43;
    border-radius: 4px;
    background: #fff;
    cursor: pointer;
}

.sr-m-selectall input:checked,
.sr-card-cb:checked {
    background: #ff9f43;
}

/* White tick */
.sr-m-selectall input:checked::after,
.sr-card-cb:checked::after {
    content: '';
    position: absolute;
    left: 4px;
    top: 0;
    width: 5px;
    height: 10px;
    border: solid #fff;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}
    .sr-card-check {
        display: inline-flex;
        align-items: center;
        margin: 0 8px 0 0;
        flex-shrink: 0;
    }
    .sr-card-checkmark { display: none; }
    .sr-card-top .sr-card-name { flex: 1 1 auto; }
}
.sr-m-actions { display: none; }

@media (max-width: 1024.98px), (width: 1032px) and (height: 1376px), (width: 1280px) and (height: 800px) {
    .sr-m-head-top { margin-bottom: 0 !important; }

    .sr-m-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin: 0 0 8px;
        padding: 8px 10px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
    }
    .sr-m-actions .sr-m-selectall,
    .sr-m-actions .sr-m-tool {
        width: 100%;
        height: 38px;
        margin: 0;
    }
    :root[data-theme="dark"] .sr-m-actions { background: #1e293b; }
}
/* Tablet: 2 cards per row (same as product list) */
@media screen and (min-width: 768px) and (max-width: 1024.98px),
       screen and (width: 1032px) and (height: 1376px),
       screen and (width: 1280px) and (height: 800px) {
    .sr-card-list {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        align-items: start;
        gap: 10px;
    }
    .sr-card-list .sr-empty {
        grid-column: 1 / -1;
    }
}

/* Desktop report filters: keep criteria on one row and search below. */
@media screen and (min-width: 1025px)
    and (not ((width: 1032px) and (height: 1376px)))
    and (not ((width: 1280px) and (height: 800px))) {
    #srFiltersBox {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
        column-gap: 12px;
        margin: 0 !important;
        padding: 14px 14px 8px;
        background: #fff;
        border: 1px solid #e5e9f0;
        border-bottom: 0;
        border-radius: 12px 12px 0 0;
    }

    #srFilterFields {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, .82fr)) minmax(0, 1.5fr) minmax(0, 1.2fr) repeat(3, minmax(0, 1fr));
        align-items: center;
        gap: 10px;
        width: 100%;
        min-width: 0;
        margin: 0 !important;
    }

    #srFilterFields:not(:has(#staff-filter)) {
        grid-template-columns: repeat(3, minmax(0, .82fr)) minmax(0, 1.5fr) minmax(0, 1.2fr) repeat(2, minmax(0, 1fr));
    }

    #srFilterFields > .sr-filter-field {
        min-width: 0;
        margin: 0 !important;
    }

    #srFilterFields > .sr-filter-field > label,
    .sr-search-wrap .product-search > label {
        display: none !important;
    }

    #srFilterFields select,
    #srFilterFields input {
        display: block;
        width: 100% !important;
        min-width: 0 !important;
        height: 40px !important;
        margin: 0 !important;
        font-size: 13px;
    }

    #srFilterFields > .sr-filter-field > div {
        width: 100% !important;
        min-width: 0 !important;
        margin: 0 !important;
    }

    #srFilterFields #date-range-filter {
        min-width: 0 !important;
        padding-left: 30px !important;
    }

    #srFiltersBox > .wordset {
        width: auto !important;
        margin: 0 !important;
        align-self: center;
    }

    #srFiltersBox > .wordset ul {
        display: flex;
        margin: 0;
        padding: 0;
    }

    #generate-pdf {
        justify-content: center;
        width: 90px;
        height: 40px;
        padding: 0 12px;
    }

    .sr-search-wrap {
        margin: 0 0 16px !important;
        padding: 10px 14px 14px;
        background: #fff;
        border: 1px solid #e5e9f0;
        border-top: 0;
        border-radius: 0 0 12px 12px;
    }

    .sr-search-wrap .search-set {
        display: grid !important;
        grid-template-columns: minmax(220px, 248px) minmax(190px, 220px);
        align-items: center;
        gap: 10px;
        width: 100%;
        margin: 0 !important;
    }

    .sr-search-wrap .search-path {
        display: none !important;
    }

    .sr-search-wrap .search-input {
        width: 100% !important;
        min-width: 0;
        margin: 0 !important;
    }

    .sr-search-wrap .search-input input,
    .sr-search-wrap .product-search select {
        width: 100% !important;
        height: 40px !important;
        margin: 0 !important;
        font-size: 13px;
    }

    .sr-search-wrap .product-search {
        width: 100%;
        min-width: 0;
        margin: 0 !important;
    }
}
    </style>
    <div class="content">
        {{-- ── Mobile header (phone/tablet only) ── --}}
<div class="sr-m-head">
    <div class="sr-m-head-top">
        <div class="sr-m-title">Sales Report</div>
        <div class="sr-m-total" id="srMobileTotal">Total: ₹0.00</div>
    </div>
    
</div>

{{-- ── Filter bottom sheet (phone/tablet only) ── --}}
<div class="sr-filter-backdrop" id="srFilterBackdrop"></div>
<div class="sr-filter-sheet" id="srFilterSheet" role="dialog" aria-modal="true" aria-labelledby="srFilterSheetTitle">
    <div class="sr-filter-sheet-head">
        <span id="srFilterSheetTitle">Filter By</span>
        <button type="button" class="sr-filter-sheet-close" id="srFilterSheetClose" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="sr-filter-sheet-body" id="srFilterSheetBody"></div>
    <div class="sr-filter-sheet-foot">
        <button type="button" class="sr-filter-clear" id="srFilterClear">Clear</button>
        <button type="button" class="sr-filter-apply" id="srFilterApply">Apply</button>
    </div>
</div>
        <div class="page-header">
            <div class="page-title">
                <h4>Sales Report</h4>

            </div>
            <div class="total-expense-summary">
                <h5 id="total-expense-amount" class="mb-0 px-3 py-1 mx-3 rounded bg-light total_expense">
                    <!-- Gets updated by JS -->Total:
                </h5>
            </div>

        </div>

        @if ($showSalesReportChart)
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap" style="background: linear-gradient(135deg, #1a1f3a 0%, #2d3561 100%); padding: 14px 20px;">
                <div class="d-flex align-items-center gap-2">
                    <div style="background: rgba(99,179,237,0.18); border-radius: 8px; padding: 6px 10px; display:inline-flex; align-items:center;">
                        <i class="fa-solid fa-chart-column" style="color:#63b3ed; font-size:16px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="color:#e2e8f0; font-size:15px; letter-spacing:0.3px;">Top Sold Products</h6>
                        <small style="color:#94a3b8; font-size:11px;">Ranked by quantity sold</small>
                    </div>
                </div>
                <!-- 🔹 Summary inline with title -->
                <div id="sales-filter-summary"
                    style="display:none;
                   background: rgba(255,255,255,0.1);
                   border: 1px solid rgba(255,255,255,0.2);
                   border-radius: 6px;
                   padding: 6px 12px;
                   font-size: 13px;
                   font-weight: 500;
                   color: #e2e8f0;
                   align-items: center;
                   justify-content: center;
                   gap: 6px;
                   white-space: nowrap;
                   transition: all 0.3s ease;">
                </div>
            </div>

            <div class="card-body sales-chart-body" style="background: #f8fafc; padding: 20px;">
                <div id="salesChartWrapper" style="overflow-x: auto; overflow-y: hidden; width: 100%;">
                    <div id="salesChartInner" style="min-width: 100%; height: 340px; position: relative;">
                        <canvas id="salesProductChart" style="height: 340px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-body" style="overflow:auto;">

                <div class="table-top" id="srFiltersBox">
                    <div class="search-set" id="srFilterFields">
                        <select id="date-filter" class="form-select form-select-sm">
                            <option value="">All Time</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month">This Month</option>
                            <option value="last_6_months">Last 6 Months</option>
                            <option value="this_year">This Year</option>
                            <option value="previous_year">Previous Year</option>
                        </select>

                        <select id="month-filter" class="form-select form-select-sm"
                            style="margin-right:8px; margin-left: 8px;">
                            <option value="">All Months</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>

                        <select id="year-filter" class="form-select form-select-sm" style="margin-right:8px;">
                            <option value="">All Years</option>
                            @for ($y = date('Y'); $y >= date('Y') - 10; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>

                        <div style="position: relative; margin-right:8px; display: flex; align-items: center;">
                            <i class="fa-regular fa-calendar" style="position: absolute; left: 10px; color: #6c757d; z-index: 10;"></i>
                            <input type="text" id="date-range-filter" class="form-control form-control-sm" placeholder="Date Range" style="padding-left: 30px; min-width: 190px;" autocomplete="off">
                        </div>

                        <select id="vendor-filter" class="form-select form-select-sm"
                            style="margin-right:8px; ">
                            <option value="">All Customers</option>
                            @isset($customers)
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                            data-search="{{ strtolower($customer->name . ' ' . ($customer->phone ?? '')) }}">
                                        {{ $customer->name }}{{ $customer->phone ? ' - ' . $customer->phone : '' }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>

                        <select id="category-filter" class="form-select form-select-sm"
                            style="margin-right:8px; ">
                            <option value="">All Categories</option>
                            @isset($categories)
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            @endisset
                        </select>

                        {{-- Brand filter --}}
                        <select id="brand-filter" class="form-select form-select-sm"
                            style="margin-right:8px; ">
                            <option value="">All Brands</option>
                            @isset($brands)
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            @endisset
                        </select>

                        @if (in_array(auth()->user()->role, ['admin', 'sub-admin']) && ($staffList ?? collect())->count())
                        <select id="staff-filter" class="form-select form-select-sm"
                            style="margin-right:8px; ">
                            <option value="">All Staff</option>
                            @foreach ($staffList as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                        @endif

                    </div>
                    <!-- <div class="wordset">
                        <ul>
                            <li>
                                <a id="generate-pdf" href="javascript:void(0);" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Download PDF">

                                    <button class="btn btn-primary btn-sm"><i class="fa-solid fa-file-pdf"></i>PDF</button>
                                </a>
                            </li>
                        </ul>
                    </div> -->
                    <div class="wordset">
                    <ul>
                        <li>
                            <button class="btn btn-primary btn-sm" id="generate-pdf" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Download PDF">
                                <i class="fa-solid fa-file-pdf"></i> PDF
                            </button>
                        </li>
                    </ul>
                </div>
                </div>
                <div class="mb-2 sr-search-wrap">
                    <div class="search-set d-flex justify-content-md-start justify-content-start w-100" style="gap: 10px; align-items: center;">
                        <div class="search-path"></div>
                        <div class="search-input">
                            <a class="btn btn-searchset">
                                {{-- <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}"
                                    alt="img"> --}}
                            </a>
                            <input type="text" id="search-input" class="form-control" placeholder="Search..." style="height:30px ">
                        </div>
                        <button type="button" id="srFilterToggle" class="sr-filter-toggle" aria-label="Filters" aria-expanded="false">
                            <i class="fas fa-filter"></i>
                            <span class="sr-filter-count d-none" id="srFilterCount">0</span>
                        </button>
                        <div class="product-search sr-filter-field">
                            <select id="product-name-filter" class="form-select" style="height:31px; border-radius: 4px; width:220px; padding-left:12px;">
                                <option value="">All Product</option>
                                @isset($products)
                                    @foreach ($products as $product)
                                        <option value="{{ $product->name }}">{{ $product->name }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                    </div>
                </div>
                <div class="sr-m-actions">
                <label class="sr-m-selectall">
                    <input type="checkbox" id="srSelectAllMobile">
                    <span>Select All</span>
                </label>
                <button type="button" class="sr-m-tool is-red" data-sr-proxy="#generate-pdf">
                    <i class="fa-solid fa-file-pdf"></i><span>PDF</span>
                </button>
            </div>
                {{-- <div class="table-scroll-top">
                    <div></div>
                </div> --}}
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>
                                    <label class="checkboxs">
                                        <input type="checkbox" id="select-all">
                                        <span class="checkmarks"></span>
                                    </label>
                                </th>
                                <th>Sr. No.</th>
                                <th>Order Number</th>
                                <th>Product Name</th>
                                <th>Customer Name</th>
                                <th>Order Date</th>
                                <th>Customer Address</th>
                                <th>Category</th>
                                <th>Taxes</th>
                                <th>Amount</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Action</th>
                                <th class="text-center">Details</th>
                            </tr>
                        </thead>
                        <tbody>


                        </tbody>
                    </table>
                </div>
                <div class="sr-card-list" id="srCardList"></div>
                <!-- Pagination Controls -->
                <div
                    class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
                        <select id="per-page-select" class="form-select form-select-sm"
                            style="width: auto; border: 1px solid #ddd;">
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
                            <!-- Page numbers will be populated by JS -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>


@endsection
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        // ── Mobile: PDF header button -> original button ──
$(document).on('click', '.sr-m-tool[data-sr-proxy]', function () {
    $($(this).data('sr-proxy')).trigger('click');
});

// ── Mobile: filter bottom sheet ──
(function initSalesFilterSheet() {
    const sheetBody  = document.getElementById('srFilterSheetBody');
    const fieldsBox  = document.getElementById('srFilterFields');
    const searchSet  = document.querySelector('.sr-search-wrap .search-set');
    const productBox = document.querySelector('.sr-search-wrap .product-search');
    const toggle     = document.getElementById('srFilterToggle');
    if (!sheetBody || !fieldsBox || !toggle) return;

    const labels = {
        'date-filter': 'Period', 'month-filter': 'Month', 'year-filter': 'Year',
        'date-range-filter': 'Date Range', 'vendor-filter': 'Customer',
        'category-filter': 'Category', 'brand-filter': 'Brand',
        'staff-filter': 'Staff', 'product-name-filter': 'Product'
    };

    // Har filter ne label wala wrapper banavo (ek var)
    function wrapFilters() {
        Array.from(fieldsBox.children).forEach(function (child) {
            if (child.classList.contains('sr-filter-field')) return;
            const ctrl = child.matches('select, input') ? child : child.querySelector('select, input');
            if (!ctrl) return;
            const wrap = document.createElement('div');
            wrap.className = 'sr-filter-field';
            const lab = document.createElement('label');
            lab.textContent = labels[ctrl.id] || '';
            fieldsBox.replaceChild(wrap, child);
            wrap.appendChild(lab);
            wrap.appendChild(child);
        });
        if (productBox && !productBox.querySelector('label')) {
            const lab = document.createElement('label');
            lab.textContent = labels['product-name-filter'];
            productBox.insertBefore(lab, productBox.firstChild);
        }
    }
    wrapFilters();

    const phoneQuery = window.matchMedia('(max-width: 1024.98px), (width: 1032px) and (height: 1376px), (width: 1280px) and (height: 800px)');

    function placeFilterFields() {
        if (phoneQuery.matches) {
            fieldsBox.querySelectorAll(':scope > .sr-filter-field').forEach(function (f) { sheetBody.appendChild(f); });
            if (productBox) sheetBody.appendChild(productBox);
        } else {
            sheetBody.querySelectorAll(':scope > .sr-filter-field').forEach(function (f) {
                if (f === productBox) { searchSet.appendChild(f); } else { fieldsBox.appendChild(f); }
            });
            closeSheet();
        }
    }
    function openSheet()  { placeFilterFields(); document.body.classList.add('sr-filter-open'); toggle.setAttribute('aria-expanded', 'true'); }
    function closeSheet() { document.body.classList.remove('sr-filter-open'); toggle.setAttribute('aria-expanded', 'false'); }

    const filterIds = ['#date-filter', '#month-filter', '#year-filter', '#date-range-filter',
                       '#vendor-filter', '#category-filter', '#brand-filter', '#staff-filter', '#product-name-filter'];
    function updateCount() {
        const n = filterIds.filter(function (sel) { return $(sel).length && !!$(sel).val(); }).length;
        $('#srFilterCount').text(n).toggleClass('d-none', n === 0);
        $(toggle).toggleClass('has-active', n > 0);
    }

    toggle.addEventListener('click', openSheet);
    ['srFilterSheetClose', 'srFilterApply', 'srFilterBackdrop'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('click', closeSheet);
    });
    $('#srFilterClear').on('click', function () {
        filterIds.forEach(function (sel) { if ($(sel).length) $(sel).val(''); });
        $('#date-range-filter').val('');
        updateCount();
        fetchOrderReport(1);
        loadSalesProductChart();
    });
    $(filterIds.join(',')).on('change apply.daterangepicker cancel.daterangepicker', updateCount);
    window.addEventListener('resize', placeFilterFields);

    placeFilterFields();
    updateCount();
})();
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            const ImagePath = "{{ env('ImagePath') }}";
            const salesReportApiUrl = "/api/order-report";

            // Pagination state
            let currentPage = 1;
            let lastPage = 1;
            let perPage = 10;
            let searchQuery = '';
            let isSelectingAllProducts = false;
            let isAllPagesSelected = false;
            let allSelectedIds = [];  

            // Global data map for expandable rows
            window.salesReportDataMap = {};

            function getSalesReportFilters(overrides = {}) {
                return {
                    filter: $('#date-filter').val(),
                    month: $('#month-filter').val(),
                    year: $('#year-filter').val(),
                    date_range: $('#date-range-filter').val(),
                    customer_id: $('#vendor-filter').val(),
                    category_id: $('#category-filter').val(),
                    brand_id: $('#brand-filter').val(),
                    product_name: $('#product-name-filter').val(),
                    staff_id: $('#staff-filter').length ? $('#staff-filter').val() : '',
                    search: searchQuery,
                    selectedSubAdminId: selectedSubAdminId,
                    ...overrides
                };
            }

            function fetchAllOrderReportItems(filters = {}) {
                const requestFilters = getSalesReportFilters({
                    per_page: 1000,
                    page: 1,
                    ...filters
                });
                const allItems = [];

                function fetchPage(page) {
                    requestFilters.page = page;

                    return $.ajax({
                        url: salesReportApiUrl,
                        type: "GET",
                        data: requestFilters,
                        headers: {
                            "Authorization": "Bearer " + authToken
                        }
                    }).then(function(response) {
                        if (!response.status) {
                            return response;
                        }

                        allItems.push(...(response.data || []));

                        const totalPages = response.pagination?.last_page || 1;
                        if (page < totalPages) {
                            return fetchPage(page + 1);
                        }

                        response.all_items = allItems;
                        return response;
                    });
                }

                return fetchPage(1);
            }

            function syncVisiblePageSelection() {
                $('input[name="item_ids[]"]').each(function() {
                    const id = String($(this).val());
                    $(this).prop('checked', allSelectedIds.includes(id));
                });

                const visibleCheckboxes = $('input[name="item_ids[]"]');
                const checkedVisibleCheckboxes = $('input[name="item_ids[]"]:checked');
                $('#select-all').prop(
                    'checked',
                    visibleCheckboxes.length > 0 && visibleCheckboxes.length === checkedVisibleCheckboxes.length
                );
                $('.sr-card-cb').each(function () {
                    $(this).prop('checked', allSelectedIds.includes(String($(this).val())));
                });
                $('#srSelectAllMobile').prop('checked', $('#select-all').prop('checked'));
            }

            // 🔹 Initial Load
            fetchOrderReport(currentPage);
            loadSalesProductChart();

            // 🔹 Handle search input
            $('#search-input').on('keyup', function() {
                searchQuery = $(this).val();
                fetchOrderReport(1);
                loadSalesProductChart();
            });

            $('#product-name-filter').on('change', function() {
                fetchOrderReport(1);
                loadSalesProductChart();
            });


            // 🔹 Trigger filters
            $('#date-filter, #month-filter, #year-filter, #vendor-filter, #category-filter, #brand-filter, #staff-filter').on('change',
                function() {
                    fetchOrderReport(1);
                    loadSalesProductChart();
                });

            // 🔹 Initialize Date Range Picker
            $('#date-range-filter').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'DD-MM-YYYY'
                }
            });

            $('#date-range-filter').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
                fetchOrderReport(1);
                loadSalesProductChart();
            });

            $('#date-range-filter').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                fetchOrderReport(1);
                loadSalesProductChart();
            });

            // Handle page number clicks
            $(document).on('click', '#pagination-numbers .page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                    fetchOrderReport(page);
                }
            });

            // Handle per-page change
            $('#per-page-select').on('change', function() {
                perPage = $(this).val();
                fetchOrderReport(1);
            });

            /** ----------------------------------------------
             * 📱 Mobile Expandable Row Functions
             * ---------------------------------------------- */
            // function buildSalesReportExpandableRowContent(item) {
            //     let taxesHtml = "N/A";
            //     if (item.taxes && item.taxes.length > 0) {
            //         taxesHtml = "";
            //         $.each(item.taxes, function(tIdx, tax) {
            //             taxesHtml += `<div>GST (${tax.rate}%): ₹${tax.amount}</div>`;
            //         });
            //     }
            //     return `
            //         <div class="detail-item">
            //             <span class="detail-label">Sr. No.:</span>
            //             <span class="detail-value">${window.salesReportDataMap ? '' : ''}</span>
            //         </div>
            //         <div class="detail-item">
            //             <span class="detail-label">Customer Name:</span>
            //             <span class="detail-value">${item.customer_name || 'N/A'}</span>
            //         </div>
            //         <div class="detail-item">
            //             <span class="detail-label">Order Date:</span>
            //             <span class="detail-value">${item.order_date || 'N/A'}</span>
            //         </div>
            //         <div class="order-details-content">
            //             <div class="detail-item">
            //                 <span class="detail-label">GST NO:</span>
            //                 <span class="detail-value">${item.customer_gst || 'N/A'}</span>
            //             </div>
            //             <div class="detail-item">
            //                 <span class="detail-label">Customer Address:</span>
            //                 <span class="detail-value">${item.customer_address || 'N/A'}</span>
            //             </div>
            //             <div class="detail-item">
            //                 <span class="detail-label">Category:</span>
            //                 <span class="detail-value">${item.category || 'N/A'}</span>
            //             </div>
            //             <div class="detail-item">
            //                 <span class="detail-label">Taxes:</span>
            //                 <span class="detail-value">${taxesHtml}</span>
            //             </div>
            //             <div class="detail-item">
            //                 <span class="detail-label">Amount:</span>
            //                 <span class="detail-value" style="color: #28a745; font-weight: 600;">${item.sold_amount || '₹0.00'}</span>
            //             </div>
            //             <div class="detail-item">
            //                 <span class="detail-label">Quantity:</span>
            //                 <span class="detail-value" style="color: #ff9f43; font-weight: 600;">${item.sold_qty || '0'}</span>
            //             </div>
            //         </div>
            //     `;
            // }
            function buildSalesReportExpandableRowContent(item) {
                let taxesHtml = "N/A";
                if (item.taxes && item.taxes.length > 0) {
                    taxesHtml = "";
                    $.each(item.taxes, function(tIdx, tax) {
                        taxesHtml += `<div>GST (${tax.rate}%): ₹${tax.amount}</div>`;
                    });
                }
                return `
                    <div class="order-details-content">
                        <div class="detail-item">
                            <span class="detail-label">Product Name:</span>
                            <span class="detail-value">${item.name || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Customer Name:</span>
                            <span class="detail-value">${item.customer_name || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Order Date:</span>
                            <span class="detail-value">${item.order_date || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">GST NO:</span>
                            <span class="detail-value">${item.customer_gst || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Customer Address:</span>
                            <span class="detail-value">${item.customer_address || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Category:</span>
                            <span class="detail-value">${item.category || 'N/A'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Taxes:</span>
                            <span class="detail-value">${taxesHtml}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Amount:</span>
                            <span class="detail-value" style="color: #28a745; font-weight: 600;">${item.sold_amount || '₹0.00'}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Quantity:</span>
                            <span class="detail-value" style="color: #ff9f43; font-weight: 600;">${item.sold_qty || '0'}</span>
                        </div>
                        <div class="detail-item" style="border-bottom:none; padding-top:12px;">
                            <a href="/sales-details/${item.order_id}" class="btn btn-sm" style="width:100%; background:#ff9f43; color:#fff; text-align:center; border-radius:6px; padding:8px; font-weight:600;">
                                <i class="fa-regular fa-eye"></i>&nbsp; View Details
                            </a>
                        </div>
                    </div>
                `;
            }

            window.toggleSalesReportRowDetails = function(productId) {
                // Only work on mobile/tablet (≤1024px)
                if ($(window).width() > 1024) {
                    return;
                }

                const row = $(`tr[data-product-id="${productId}"]`);
                if (row.length === 0) return;

                const detailsRow = row.next(`tr.order-details-row[data-product-details-id="${productId}"]`);
                const toggleBtn = row.find('.mobile-toggle-btn-table');

                if (detailsRow.length === 0) {
                    // Create expandable row if it doesn't exist
                    const item = window.salesReportDataMap[productId];
                    if (!item) return;

                    const expandableContent = buildSalesReportExpandableRowContent(item);
                    const newRow = $(`
                        <tr class="order-details-row show" data-product-details-id="${productId}">
                            <td colspan="11">${expandableContent}</td>
                        </tr>
                    `);
                    row.after(newRow);
                    toggleBtn.addClass('minus').html('-');
                } else {
                    // Toggle existing row
                    if (detailsRow.hasClass('show')) {
                        detailsRow.removeClass('show');
                        toggleBtn.removeClass('minus').html('+');
                    } else {
                        detailsRow.addClass('show');
                        toggleBtn.addClass('minus').html('-');
                    }
                }
            };

            window.addSalesReportExpandableRows = function(dt) {
                // Remove existing expandable rows
                $('.order-details-row').remove();

                // Only add expandable rows on mobile/tablet (≤1024px)
                if ($(window).width() <= 1024) {
                    dt.rows().every(function() {
                        const row = $(this.node());
                        const productId = row.attr('data-product-id');
                        if (productId && window.salesReportDataMap[productId]) {
                            const item = window.salesReportDataMap[productId];
                            const expandableContent = buildSalesReportExpandableRowContent(item);
                            const expandableRow = $(`
                                <tr class="order-details-row" data-product-details-id="${productId}">
                                    <td colspan="11">${expandableContent}</td>
                                </tr>
                            `);
                            row.after(expandableRow);
                        }
                    });
                } else {
                    // On desktop, reset all toggle buttons to +
                    $('.mobile-toggle-btn-table').removeClass('minus').html('+');
                }
            };

            // Resize handler with debouncing
            let resizeTimer;
            window.handleSalesReportResize = function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const currentWidth = $(window).width();

                    // Force browser reflow
                    const body = document.body;
                    body.style.display = 'none';
                    body.offsetHeight; // Trigger reflow
                    body.style.display = '';

                    // Get DataTable instance
                    const table = $('.datanew').DataTable();
                    if (table) {
                        // If switching to desktop (width > 1024px), remove all expandable rows
                        if (currentWidth > 1024) {
                            $('.order-details-row').remove();
                            $('.mobile-toggle-btn-table').removeClass('minus').html('+');
                        }

                        // Adjust columns
                        table.columns.adjust().draw(false);

                        // Re-add expandable rows if needed (only on mobile/tablet)
                        if (currentWidth <= 1024) {
                            setTimeout(function() {
                                window.addSalesReportExpandableRows(table);
                            }, 100);
                        }
                    }

                    // Resize chart if it exists
                    if (window.salesChart) {
                        window.salesChart.resize();
                    }
                }, 250);
            };

            // Add resize event listener
            $(window).on('resize', window.handleSalesReportResize);

            // Add matchMedia listeners for breakpoints
            const mediaQueries = [
                window.matchMedia('(max-width: 575.98px)'),
                window.matchMedia('(min-width: 576px) and (max-width: 767.98px)'),
                window.matchMedia('(min-width: 768px) and (max-width: 1024px)'),
                window.matchMedia('(min-width: 1025px)')
            ];

            mediaQueries.forEach(function(mq) {
                mq.addListener(function() {
                    window.handleSalesReportResize();
                });
            });

            /** ----------------------------------------------
             * 📋 Fetch Order Report Table
             * ---------------------------------------------- */
    //         function fetchOrderReport(page = 1) {
    //             const filters = {
    //                 page: page,                // ✅ important
    // per_page: perPage,         // ✅ important
    // search: searchQuery,
    //                 filter: $('#date-filter').val(),
    //                 month: $('#month-filter').val(),
    //                 year: $('#year-filter').val(),
    //                 customer_id: $('#vendor-filter').val(),
    //                 category_id: $('#category-filter').val(),
    //                 selectedSubAdminId: selectedSubAdminId
    //             };

    //             $.ajax({
    //                 url: "/api/order-report",
    //                 type: "GET",
    //                 data: filters,
    //                 headers: {
    //                     "Authorization": "Bearer " + authToken
    //                 },
    //                 success: function(response) {
    //                     // console.log(response);

    //                     if (!response.status) return alert("No orders found.");
    //                     updateSalesSummary(response);

    //                     // Clear previous data map
    //                     window.salesReportDataMap = {};

    //                     let tbody = "";
    //                     $.each(response.data, function(index, item) {
    //                         // Store item data for expandable rows
    //                         window.salesReportDataMap[item.id] = item;

    //                         tbody += `
    //                     <tr data-product-id="${item.id}">
    //                         <td>
    //                             <label class="checkboxs">
    //                                 <input type="checkbox" name="item_ids[]" value="${item.id}">
    //                                 <span class="checkmarks"></span>
    //                             </label>
    //                         </td>
    //                         <td class="productimgname">
    //                             <div style="display: flex; align-items: center; gap: 10px;">
    //                                 <a href="/product-view/${item.product_id}" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
    //                                     <img src="${ImagePath}${item.image}" alt="${item.name}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
    //                                     <span style="margin-left: 10px;">${item.name}</span>
    //                                 </a>
    //                             </div>
    //                         </td>
    //                         <td class="text-center">
    //                             <button class="mobile-toggle-btn-table" onclick="window.toggleSalesReportRowDetails('${item.id}')">+</button>
    //                         </td>
    //                         <td>${item.SKU}</td>
    //                         <td>${item.category}</td>
    //                         <td>${item.brand ?? 'N/A'}</td>
    //                         <td>${item.sold_amount}</td>
    //                         <td>${item.sold_qty}</td>
    //                     </tr>
    //                 `;
    //                     });

    //                     let table = $('.datanew').DataTable();
    //                     table.destroy();
    //                     $(".datanew tbody").html(tbody);
    //                     let dt = $('.datanew').DataTable({
    //                         responsive: true
    //                     });

    //                     // Add expandable rows after table draws
    //                     dt.on('draw', function() {
    //                         window.addSalesReportExpandableRows(dt);
    //                     });

    //                     // Initial add of expandable rows
    //                     setTimeout(function() {
    //                         window.addSalesReportExpandableRows(dt);
    //                         // Trigger resize handler to ensure proper layout
    //                         window.handleSalesReportResize();
    //                     }, 100);
    //                 },
    //                 error: function() {
    //                     alert("Failed to load order report.");
    //                 }
    //             });
    //         }

    function fetchOrderReport(page = 1) {

    const filters = getSalesReportFilters({
        page: page,
        per_page: perPage
    });

            $.ajax({
                url: salesReportApiUrl,
                type: "GET",
                data: filters,
        headers: {
            "Authorization": "Bearer " + authToken
        },
        success: function(response) {

            if (!response.status) return alert("No orders found.");

            updateSalesSummary(response);

            // ✅ UPDATE PAGINATION STATE
            currentPage = response.pagination.current_page;
            lastPage = response.pagination.last_page;

            // ✅ UPDATE PAGINATION TEXT
            $('#pagination-from').text(response.pagination.from || 0);
            $('#pagination-to').text(response.pagination.to || 0);
            $('#pagination-total').text(response.pagination.total);

            // ✅ BUILD PAGINATION BUTTONS
            let paginationHtml = "";
            let isMobile = $(window).width() < 576;

            // Previous Button
            paginationHtml += `
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0);" data-page="${currentPage - 1}">Previous</a>
                </li>
            `;

            if (lastPage <= (isMobile ? 3 : 5)) {
                for (let i = 1; i <= lastPage; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `;
                }
            } else {
                // First Page
                paginationHtml += `
                    <li class="page-item ${currentPage === 1 ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="1">1</a>
                    </li>
                `;

                if (currentPage > 2) {
                    paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }

                // Current Page (if not first or last)
                if (currentPage !== 1 && currentPage !== lastPage) {
                    paginationHtml += `
                        <li class="page-item active">
                            <a class="page-link" href="#" data-page="${currentPage}">${currentPage}</a>
                        </li>
                    `;
                }

                if (currentPage < lastPage - 1) {
                    paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }

                // Last Page
                paginationHtml += `
                    <li class="page-item ${currentPage === lastPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${lastPage}">${lastPage}</a>
                    </li>
                `;
            }

            // Next Button
            paginationHtml += `
                <li class="page-item ${currentPage === lastPage || lastPage === 0 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0);" data-page="${currentPage + 1}">Next</a>
                </li>
            `;

            $('#pagination-numbers').html(paginationHtml);

            // Clear previous data map
            window.salesReportDataMap = {};
            let cardsHtml = '';
            let tbody = "";
            let startSrNo = response.pagination.from || 1;
            $.each(response.data, function(index, item) {
                const itemId = String(item.id);
                const srNo = startSrNo + index;

                window.salesReportDataMap[itemId] = item;

                let taxesHtml = "N/A";
                if (item.taxes && item.taxes.length > 0) {
                    taxesHtml = "";
                    $.each(item.taxes, function(tIdx, tax) {
                        taxesHtml += `<div>GST (${tax.rate}%): ₹${tax.amount}</div>`;
                    });
                }
                cardsHtml += `
                <div class="sr-card">
                <div class="sr-card-top">
                    <label class="sr-card-check">
                        <input type="checkbox" class="sr-card-cb" value="${itemId}" ${allSelectedIds.includes(itemId) ? 'checked' : ''}>
                        <span class="sr-card-checkmark"></span>
                    </label>
                    <a href="/product-view/${item.product_id}" class="sr-card-name">${item.name || 'N/A'}</a>
                        <span class="sr-card-amount">${item.sold_amount || '₹0.00'}</span>
                    </div>
                    <div class="sr-card-sub">
                        <span class="sr-card-order">${item.order_number || ''}</span>
                        <span>${item.customer_name || 'N/A'}</span>
                    </div>
                    <div class="sr-card-foot">
                        <div class="sr-card-stat"><span>Qty</span><strong>${item.sold_qty || 0} ${item.unit || ''}</strong></div>
                        <div class="sr-card-stat"><span>Date</span><strong>${item.order_date || 'N/A'}</strong></div>
                        <div class="sr-card-stat"><span>Category</span><strong>${item.category || 'N/A'}</strong></div>
                        <a class="sr-card-view" href="/sales-details/${item.order_id}" title="View Details"><i class="fa-regular fa-eye"></i></a>
                    </div>
                </div>`;
                tbody += `
                <tr data-product-id="${itemId}">
                    <td>
                        <label class="checkboxs">
                            <input type="checkbox" name="item_ids[]" value="${itemId}" ${allSelectedIds.includes(itemId) ? 'checked' : ''}>
                            <span class="checkmarks"></span>
                        </label>
                    </td>
                    <td>${srNo}</td>
                    <td>${item.order_number}</td>
                    <td class="productimgname" >
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <a href="/product-view/${item.product_id}" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                                <span class="report-product-name" style="margin-left: 8px; font-size: 12.5px; font-weight: 600; line-height: 1.4; word-break: break-word; max-width: 240px; display: inline-block;">${item.name}</span>
                            </a>
                        </div>
                    </td>
                    <td>${item.customer_name}</td>
                    <td>${item.order_date || 'N/A'}</td>
                    <td style="white-space: normal; max-width: 200px;">${item.customer_address}</td>
                    <td>${item.category}</td>
                    <td>${taxesHtml}</td>
                    <td>${item.sold_amount}</td>
                    <td>${item.sold_qty}</td>
                    <td>${item.unit || 'N/A'}</td>
                    <td>
                        <a class="action-set" href="/sales-details/${item.order_id}" data-bs-toggle="tooltip" data-bs-placement="top" title="View Details">
                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/eye.svg' }}" alt="View">
                        </a>
                    </td>
                    <td class="text-center">
                        <button class="mobile-toggle-btn-table" onclick="window.toggleSalesReportRowDetails('${itemId}')">+</button>
                    </td>
                </tr>`;
            });

            // ✅ DESTROY OLD TABLE
            if ($.fn.DataTable.isDataTable('.datanew')) {
                $('.datanew').DataTable().destroy();
            }

            $(".datanew tbody").html(tbody);
            $('#srCardList').html(cardsHtml || '<div class="sr-empty">No sales found</div>');

            // ✅ RE-INIT WITHOUT PAGINATION
            let dt = $('.datanew').DataTable({
                responsive: false,
                dom: 't',
                paging: false,     // 🔥 IMPORTANT
                info: false,
                searching: false
            });

            dt.on('draw', function() {
                window.addSalesReportExpandableRows(dt);
            });

            setTimeout(function() {
                window.addSalesReportExpandableRows(dt);
                window.handleSalesReportResize();
                syncVisiblePageSelection();
                $('.dataTables_filter').remove();
            }, 100);
        },
        error: function() {
            alert("Failed to load order report.");
        }
    });
}
            // ── Card checkbox <-> shared selection ──
$(document).on('change', '.sr-card-cb', function () {
    const id = String($(this).val());
    if ($(this).is(':checked')) {
        if (!allSelectedIds.includes(id)) allSelectedIds.push(id);
    } else {
        allSelectedIds = allSelectedIds.filter(x => x !== id);
        isAllPagesSelected = false;
    }
    syncVisiblePageSelection();
});

$(document).on('change', '#srSelectAllMobile', function () {
    const isChecked = $(this).is(':checked');

    if (isChecked) {
        $('#select-all').prop('checked', true).trigger('change');
    } else {
        // Seedhu deselect: badha selection clear
        allSelectedIds = [];
        isAllPagesSelected = false;
        $('input[name="item_ids[]"]').prop('checked', false);
        $('.sr-card-cb').prop('checked', false);
        $('#select-all').prop('checked', false);
    }
});

            // 🔹 Handle Select-All checkbox
            $(document).on('change', '#select-all', function() {
                const isChecked = $(this).is(':checked');

                if (isChecked) {
                    if (isSelectingAllProducts) {
                        return;
                    }

                    isSelectingAllProducts = true;
                    $('#select-all').prop('disabled', true);

                    fetchAllOrderReportItems()
                        .done(function(response) {
                            if (!response.status) {
                                allSelectedIds = [];
                                isAllPagesSelected = false;
                                $('#select-all').prop('checked', false);
                                return;
                            }

                            allSelectedIds = (response.all_items || []).map(function(item) {
                                return String(item.id);
                            });
                            allSelectedIds = [...new Set(allSelectedIds)];
                            isAllPagesSelected = true;
                            syncVisiblePageSelection();
                        })
                        .fail(function() {
                            allSelectedIds = [];
                            isAllPagesSelected = false;
                            $('#select-all').prop('checked', false);
                        })
                        .always(function() {
                            isSelectingAllProducts = false;
                            $('#select-all').prop('disabled', false);
                        });
                } else {
                    allSelectedIds = [];
                    isAllPagesSelected = false;
                    $('input[name="item_ids[]"]').prop('checked', false);
                    $('.sr-card-cb').prop('checked', false);
                    $('#srSelectAllMobile').prop('checked', false);
                }
            });

            // 🔹 Handle individual checkbox changes
            $(document).on('change', 'input[name="item_ids[]"]', function() {
                const id = String($(this).val());

                if ($(this).is(':checked')) {
                    if (!allSelectedIds.includes(id)) allSelectedIds.push(id);
                } else {
                    allSelectedIds = allSelectedIds.filter(x => x !== id);
                    isAllPagesSelected = false;
                }

                syncVisiblePageSelection();
            });


            /** ----------------------------------------------
             * 📊 Load Sales Product Chart
             * ---------------------------------------------- */
            function loadSalesProductChart() {
                if (!$('#salesProductChart').length) {
                    return;
                }
                const chartContainer = $("#salesProductChart").parent();

                fetchAllOrderReportItems()
                    .done(function(response) {
                        $("#noSalesMessage").remove();
                        updateSalesSummary(response);
                        const chartItems = response.all_items || [];

                        if (!response.status || !chartItems.length) {
                            if (window.salesChart) window.salesChart.destroy();
                            chartContainer.append(`
                                <div id="noSalesMessage"
                                    style="
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                height: 100%;
                                                width: 100%;
                                                color: #6b7280;
                                                font-size: 16px;
                                                text-align: center;
                                            ">
                                    No sales found.
                                </div>
                            `);
                            return;
                        }

                        // ✅ Aggregate by product
                        const productTotals = {};
                        const productAmounts = {};
                        chartItems.forEach(item => {
                            const name = item.name || "Unknown";
                            const qty = parseFloat(item.sold_qty);
                            const amount = parseFloat(String(item.sold_amount).replace(/[₹,]/g, "")) ||
                                0;
                            productTotals[name] = (productTotals[name] || 0) + qty;
                            productAmounts[name] = (productAmounts[name] || 0) + amount;
                        });

                        const sortedEntries = Object.entries(productTotals)
                            .sort((a, b) => b[1] - a[1]);

                        const labels = sortedEntries.map(entry => entry[0]);
                        const values = sortedEntries.map(entry => entry[1]);

                        if (window.salesChart) window.salesChart.destroy();

                        // Professional 20-color palette that cycles
                        const colorPalette = [
                            '#4361ee','#3a86ff','#7209b7','#f72585','#4cc9f0',
                            '#06d6a0','#118ab2','#ffd166','#ef476f','#073b4c',
                            '#e9c46a','#f4a261','#e76f51','#264653','#2a9d8f',
                            '#457b9d','#1d3557','#a8dadc','#e63946','#52b788'
                        ];
                        const bgColors = labels.map((_, i) => colorPalette[i % colorPalette.length]);
                        const borderColors = bgColors.map(c => c);

                        // Dynamic width: min 60px per bar, at least full container width
                        const chartWrapper = document.getElementById('salesChartInner');
                        const minBarWidth = 64;
                        const dynamicWidth = Math.max(chartWrapper.parentElement.offsetWidth, labels.length * minBarWidth);
                        chartWrapper.style.width = dynamicWidth + 'px';

                        const ctx = document.getElementById("salesProductChart").getContext("2d");
                        window.salesChart = new Chart(ctx, {
                            type: "bar",
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: "Qty Sold",
                                    data: values,
                                    backgroundColor: bgColors.map(c => c + 'cc'),
                                    borderColor: borderColors,
                                    borderWidth: 1.5,
                                    borderRadius: 6,
                                    borderSkipped: false,
                                    barThickness: 38,
                                    maxBarThickness: 48
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: { duration: 700, easing: 'easeOutQuart' },
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: '#1a1f3a',
                                        titleColor: '#e2e8f0',
                                        bodyColor: '#94a3b8',
                                        padding: 12,
                                        cornerRadius: 8,
                                        callbacks: {
                                            title: function(context) {
                                                return context[0].label;
                                            },
                                            label: function(context) {
                                                const label = context.label;
                                                const qty = context.formattedValue;
                                                const amt = productAmounts[label] || 0;
                                                return [`  Qty: ${qty} pcs`, `  Revenue: ₹${amt.toLocaleString('en-IN', {minimumFractionDigits: 2})}` ];
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: { display: false },
                                        ticks: {
                                            color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#ffffff' : '#64748b',
                                            font: { size: 10, weight: '500' },
                                            maxRotation: 35,
                                            minRotation: 25,
                                            callback: function(val, idx) {
                                                const lbl = this.getLabelForValue(idx);
                                                return lbl.length > 18 ? lbl.substring(0, 16) + '…' : lbl;
                                            }
                                        },
                                        border: { color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#334155' : '#e2e8f0' }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1e293b' : '#f1f5f9', lineWidth: 1 },
                                        ticks: {
                                            color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#94a3b8' : '#64748b',
                                            font: { size: 11 },
                                            stepSize: 1
                                        },
                                        border: { dash: [4, 4], color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#334155' : '#e2e8f0' }
                                    }
                                }
                            }
                        });

                        // Resize chart inner when window resizes
                        $(window).off('resize.salesChart').on('resize.salesChart', function() {
                            if (window.salesChart) {
                                const wrapper = document.getElementById('salesChartInner');
                                const minW = Math.max(wrapper.parentElement.offsetWidth, labels.length * minBarWidth);
                                wrapper.style.width = minW + 'px';
                                window.salesChart.resize();
                            }
                        });
                    })
                    .fail(function(xhr) {
                        alert("Chart load failed: " + xhr.status + " " + xhr.statusText);
                        console.error("Chart load failed:", xhr);
                    });
            }

            $('#generate-pdf').on('click', function() {
                // Failsafe: Sync visually checked items on current page just in case the change event didn't fire
                $('input[name="item_ids[]"]').each(function() {
                    const id = String($(this).val());
                    if ($(this).is(':checked')) {
                        if (!allSelectedIds.includes(id)) allSelectedIds.push(id);
                    } else {
                        allSelectedIds = allSelectedIds.filter(x => x !== id);
                    }
                });

                if (allSelectedIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Selection',
                        text: 'Please select at least one product to generate the PDF.',
                        confirmButtonColor: '#ff9f43',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                const url = `/sales/report/${allSelectedIds.join(',')}`;
                window.open(url, '_blank');
            });

            $('#generate-excel').on('click', function() {
                // Failsafe: Sync visually checked items on current page
                $('input[name="item_ids[]"]').each(function() {
                    const id = String($(this).val());
                    if ($(this).is(':checked')) {
                        if (!allSelectedIds.includes(id)) allSelectedIds.push(id);
                    } else {
                        allSelectedIds = allSelectedIds.filter(x => x !== id);
                    }
                });

                if (allSelectedIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Selection',
                        text: 'Please select at least one product to export to Excel.',
                        confirmButtonColor: '#ff9f43',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                const url = `/sales/report/${allSelectedIds.join(',')}/export-excel`;
                window.location.href = url;
            });


            function updateSalesSummary(response) {
                const customerText = $('#vendor-filter option:selected').text();
                const categoryText = $('#category-filter option:selected').text();
                const monthText = $('#month-filter option:selected').text();
                const yearText = $('#year-filter option:selected').text();
                const dateText = $('#date-filter option:selected').text();

                const brandText = $('#brand-filter option:selected').text();
                const staffText = $('#staff-filter option:selected').text();

                const hasCustomer = $('#vendor-filter').val() !== "";
                const hasCategory = $('#category-filter').val() !== "";
                const hasBrand = $('#brand-filter').val() !== "";
                const hasStaff = $('#staff-filter').length && $('#staff-filter').val() !== "";
                const hasMonth = $('#month-filter').val() !== "";
                const hasYear = $('#year-filter').val() !== "";
                const hasDate = $('#date-filter').val() !== "";
                const hasDateRange = $('#date-range-filter').val() !== "";
                const dateRangeText = $('#date-range-filter').val();

                // ✅ Calculate total sold amount
                let totalAmount = parseFloat(String(response.total_sold_amount || 0).replace(/[₹,]/g, '')) || 0;

                // ✅ Update total on top
                $('#total-expense-amount').html(
                    `Total: <span style="color:#ff9f43;">₹${totalAmount.toLocaleString('en-IN', { minimumFractionDigits: 2 })}</span>`
                );
                $('#srMobileTotal').text('Total: ₹' + totalAmount.toLocaleString('en-IN', { minimumFractionDigits: 2 }));

                // Hide summary when no filters selected
                if (!hasCustomer && !hasCategory && !hasBrand && !hasStaff && !hasMonth && !hasYear && !hasDate && !hasDateRange) {
                    $('#sales-filter-summary').fadeOut(200);
                    return;
                }

                // Build readable parts
                let parts = [];
                if (hasCustomer) parts.push(`<strong>${customerText}</strong>`);
                if (hasStaff) parts.push(`by <strong>${staffText}</strong>`);
                if (hasCategory) parts.push(`in <strong>${categoryText}</strong>`);
                if (hasBrand) parts.push(`brand <strong>${brandText}</strong>`);
                if (hasMonth || hasYear) {
                    let timeText = [];
                    if (hasMonth) timeText.push(monthText);
                    if (hasYear) timeText.push(yearText);
                    parts.push(`at <strong>${timeText.join(' ')}</strong>`);
                }
                if (hasDate && dateText !== "All Time") parts.push(`(${dateText})`);
                if (hasDateRange) parts.push(`between <strong>${dateRangeText}</strong>`);

                const message = `Showing sales for ${parts.join(' ')}`;

                // ✅ Show summary beautifully
                $('#sales-filter-summary')
                    .html(`<span>${message}</span>`)
                    .hide()
                    .fadeIn(300);
            }


        });
    </script>
@endpush
