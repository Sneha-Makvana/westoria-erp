@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $role = auth()->user()->role ?? '';
    @endphp
    {{-- <style>
        .color_box {
            display: block;
            width: 100%;
            text-decoration: none;
            color: inherit;
        }

        /* Dashboard widget styles remain the same */
        @media screen and (min-width: 768px) and (max-width: 1400px) {
            .dash-count .dash-imgs svg {
                width: 40px !important;
                height: 36px !important;
            }

            .dash-widgetcontent h6 {
                font-size: 10px !important;
            }

            .dash-counts h5 {
                font-size: 10.5px !important;
            }

            .dash-count {
                min-height: 90px !important;
                margin: 0 0 15px !important;
                padding: 17px;
            }
        }

        @media screen and (max-width: 767px) {
            .dash-widget {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 15px !important;
                text-align: center;
                padding: 12px 20px !important;
            }

            .col-4 {
                display: flex;
                justify-content: center;
            }

            .dash-widgetcontent {
                margin-left: 0 !important;
                width: 68px !important;
            }

            .dash-widgetcontent h5 {
                margin-top: .5rem !important;
                font-size: 13px !important;
            }

            .dash-widgetcontent h6 {
                font-size: 10px !important;
            }

            .dash-count {
                min-height: 90px !important;
                margin: 0 0 15px !important;
                padding: 15px;
            }

            .dash-counts h5 {
                font-size: 10.5px !important;
            }

            .dash-count .dash-imgs svg {
                width: 37px !important;
                height: 37px !important;
            }

            .dash-count h4 {
                font-size: 19px !important;
            }

            .table-responsive {
                font-size: 12px !important;
            }

            /* NEW: Mobile table styles for sales/purchases */
            .dataview table thead th:nth-child(n+3),
            .dataview table tbody td:nth-child(n+3) {
                display: none !important;
            }

            .dataview table thead th.details-column,
            .dataview table tbody td:nth-child(2) {
                display: table-cell !important;
                text-align: center;
                vertical-align: middle;
                width: 50px;
            }

            .dataview .toggle-details i {
                font-size: 18px;
                transition: transform 0.3s ease;
            }

            /* Order ID column styling */
            .dataview table tbody td:first-child {
                display: flex !important;
                align-items: center !important;
                max-width: calc(100vw - 100px) !important;
            }

            .dataview .order-id {
                display: inline-block !important;
                max-width: calc(100% - 50px) !important;
                margin-left: 8px !important;
                font-size: 14px !important;
                word-break: break-word !important;
            }
        }

        /* Desktop: hide details toggle column */
        @media (min-width: 769px) {

            .dataview table thead th.details-column,
            .dataview table tbody td:nth-child(2) {
                display: none !important;
            }
        }

        /* Mobile collapse styles */
        .mobile-details-collapse {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-top: 10px;
            padding: 12px;
            background-color: #f8f9fa;
        }

        .mobile-details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #dee2e6;
        }

        .mobile-details-label {
            font-weight: 600;
            color: #495057;
        }

    .mobile-details-value {
        color: #212529;
        text-align: right;
    }

    .dataview .product-link {
        display: flex;
        align-items: flex-start;
        max-width: 250px;
        text-decoration: none;
        color: inherit;
    }

    .dataview .product-name {
        flex: 1;
        min-width: 0;
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
        line-height: 1.35;
    }

    .mobile-product-info {
        display: flex;
        align-items: flex-start;
        justify-content: flex-end;
        gap: 8px;
        width: 100%;
    }

    .mobile-product-info .product-name {
        text-align: right;
    }
    </style> --}}
    <style>
        .color_box {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: calc(100% - 15px);
            margin-bottom: 15px;
            text-decoration: none;
            color: inherit;
        }

        /* Dashboard table text wrapping fixes */
        .card-body .table td span {
            display: inline-block !important;
            max-width: 150px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            vertical-align: middle;
        }

        .card-body .table td div {
            max-width: 140px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        /* Transform solid color dashboard boxes to white cards with colored text/icons */
        .color_box .dash-count {
            background: #ffffff !important;
            border: 1px solid #f0f0f0 !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-grow: 1 !important;
            height: 100% !important;
            margin-bottom: 0 !important;
        }

        .color_box .dash-counts h4 {
            color: #1f2937 !important;
            font-weight: 700;
        }

        .color_box .dash-counts h5 {
            color: #667085 !important;
            font-weight: 600;
        }

        .color_box .dash-imgs {
            background-color: transparent !important;
        }

        /* Assign icon colors based on their original box classes */
        .color_box.box1 .dash-imgs>i,
        .color_box.box1 .dash-imgs>svg {
            stroke: #28c76f !important;
            color: #28c76f !important;
        }

        /* Green */
        .color_box.box2 .dash-imgs>i,
        .color_box.box2 .dash-imgs>svg {
            stroke: #1e1e2d !important;
            color: #1e1e2d !important;
        }

        /* Dark mode: make purchase invoice / total rental icons white */
        :root[data-theme="dark"] .color_box.box2 .dash-imgs>i,
        :root[data-theme="dark"] .color_box.box2 .dash-imgs>svg {
            stroke: #ffffff !important;
            color: #ffffff !important;
        }

        /* Dark Blue */
        .color_box.box3 .dash-imgs>i,
        .color_box.box3 .dash-imgs>svg {
            stroke: #ff9f43 !important;
            color: #ff9f43 !important;
        }

        /* Orange */
        .color_box.box4 .dash-imgs>i,
        .color_box.box4 .dash-imgs>svg {
            stroke: #00cfe8 !important;
            color: #00cfe8 !important;
        }

        /* Light Blue */

        /* Make icons smaller to match the reference design */
        .color_box .dash-imgs>i {
            font-size: 24px !important;
        }

        .color_box .dash-imgs>svg {
            width: 24px !important;
            height: 24px !important;
        }

        /* Dashboard widget styles */
        .dash-widget {
            transition: all 0.3s ease;
            border-radius: 8px !important;
        }

        .dash-widget:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Tablet view (768px - 1400px) */
        @media screen and (min-width: 768px) and (max-width: 1400px) {
            .dash-widget {
                padding: 15px !important;
            }

            .dash-widgetimg span img {
                width: 40px !important;
                height: 36px !important;
            }

            .dash-widgetcontent h6 {
                font-size: 10px !important;
                line-height: 1.2 !important;
            }

            .dash-widgetcontent h5 {
                font-size: 10.5px !important;
                margin-bottom: 5px !important;
            }

            .dash-widget {
                min-height: 90px !important;
                margin: 0 0 15px !important;
            }

            /* Ensure proper alignment in tablet */
            .col-lg-4.col-sm-6.col-4 {
                padding-left: 7.5px !important;
                padding-right: 7.5px !important;
            }
        }

        /* Mobile view (max-width: 767px) */
        @media screen and (max-width: 767px) {

            .dash-count {
                min-height: 90px !important;
                margin: 0 0 15px !important;
                padding: 15px;
            }

            .dash-counts h5 {
                font-size: 10.5px !important;
            }

            .dash-count .dash-imgs svg {
                width: 37px !important;
                height: 37px !important;
            }

            .dash-count h4 {
                font-size: 19px !important;
            }

            /* Dashboard widgets container */
            .dashboard-widgets-row {
                margin-left: -5px !important;
                margin-right: -5px !important;
                display: flex !important;
                flex-wrap: wrap !important;
            }

            /* First row (Purchase, Expense, Sales) - 3 in one row */
            .dashboard-widgets-row>.col-lg-4.col-sm-6.col-4:first-child,
            .dashboard-widgets-row>.col-lg-4.col-sm-6.col-4:nth-child(2),
            .dashboard-widgets-row>.col-lg-4.col-sm-6.col-4:nth-child(3) {
                width: 33.333% !important;
                /* Equal width for 3 widgets */
                padding-left: 5px !important;
                padding-right: 5px !important;
                margin-bottom: 10px !important;
                float: left !important;
            }

            /* Remaining widgets (Invoice counts, Vendors, Customers) - 2 per row */
            .dashboard-widgets-row>.col-lg-4.col-sm-6.col-4:nth-child(4),
            .dashboard-widgets-row>.col-lg-4.col-sm-6.col-4:nth-child(5),
            .dashboard-widgets-row>.col-lg-4.col-sm-6.col-4:nth-child(6),
            .dashboard-widgets-row>.col-lg-4.col-sm-6.col-4:nth-child(7) {
                width: 50% !important;
                padding-left: 5px !important;
                padding-right: 5px !important;
                margin-bottom: 10px !important;
                float: left !important;
            }

            /* Clear floats */
            .dashboard-widgets-row::after {
                content: "";
                display: table;
                clear: both;
            }

            /* Dashboard widget styling */
            .dash-widget {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                padding: 12px 5px !important;
                margin-bottom: 0 !important;
                height: 110px !important;
                /* Slightly shorter for 3 in a row */
                justify-content: center !important;
            }

            .dash-widgetimg {
                margin-bottom: 6px !important;
            }

            .dash-widgetimg span img {
                width: 30px !important;
                height: 30px !important;
            }

            .dash-widgetcontent {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 0 3px !important;
            }

            .dash-widgetcontent h5 {
                margin-top: 0.2rem !important;
                margin-bottom: 0.2rem !important;
                font-size: 12px !important;
                line-height: 1.2 !important;
                word-break: break-word !important;
            }

            .dash-widgetcontent h6 {
                font-size: 10px !important;
                line-height: 1.2 !important;
                margin-bottom: 0 !important;
                color: #6c757d !important;
            }

            /* Currency symbol and amount styling */
            .dash-widgetcontent h5 .counters {
                font-size: 12px !important;
                font-weight: 600 !important;
            }

            /* Adjust for first row (financial amounts) */
            .dashboard-widgets-row>.col-lg-4.col-sm-6.col-4:first-child .dash-widgetcontent h5,
            .dashboard-widgets-row>.col-lg-4.col-sm-6.col-4:nth-child(2) .dash-widgetcontent h5,
            .dashboard-widgets-row>.col-lg-4.col-sm-6.col-4:nth-child(3) .dash-widgetcontent h5 {
                font-size: 11px !important;
                /* Slightly smaller for amounts */
            }

            .dashboard-widgets-row>.col-lg-4.col-sm-6.col-4:first-child .dash-widgetcontent h6,
            .dashboard-widgets-row>.col-lg-4.col-sm-6.col-4:nth-child(2) .dash-widgetcontent h6,
            .dashboard-widgets-row>.col-lg-4.col-sm-6.col-4:nth-child(3) .dash-widgetcontent h6 {
                font-size: 9px !important;
                /* Slightly smaller labels */
            }

            /* Adjust for count widgets (4th onwards) */
            .dashboard-widgets-row>.col-lg-4.col-sm-6.col-4:nth-child(n+4) .dash-widgetcontent h5 {
                font-size: 14px !important;
                /* Larger for counts */
            }

            .dashboard-widgets-row>.col-lg-4.col-sm-6.col-4:nth-child(n+4) .dash-widgetcontent h6 {
                font-size: 11px !important;
            }

            .dashboard-widgets-row>.col-lg-4.col-sm-6.col-4:nth-child(n+4) .dash-widget {
                height: 100px !important;
                /* Slightly shorter for count widgets */
            }

            /* Table responsive styles */
            .table-responsive {
                font-size: 12px !important;
            }

            /* Mobile table styles for sales/purchases */
            .dataview table thead th:nth-child(n+3),
            .dataview table tbody td:nth-child(n+3) {
                display: none !important;
            }

            .dataview table thead th.details-column,
            .dataview table tbody td:nth-child(2) {
                display: table-cell !important;
                text-align: center;
                vertical-align: top !important;
                width: 42px;
                padding-top: 14px !important;
                padding-left: 0 !important;
                padding-right: 12px !important;
            }

            .dataview table tbody td.details-column {
                vertical-align: top !important;
            }

            .dataview .toggle-details {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 100%;
            }

            .dataview .toggle-details i {
                font-size: 18px;
                transition: transform 0.3s ease;
            }

            /* Order ID column styling */
            .dataview table tbody td:first-child {
                display: flex !important;
                align-items: flex-start !important;
                max-width: calc(100vw - 100px) !important;
            }

            .dataview table tbody td:first-child>div {
                width: 100%;
            }

            .dataview .order-id {
                display: inline-block !important;
                max-width: calc(100% - 50px) !important;
                margin-left: 8px !important;
                font-size: 14px !important;
                word-break: break-word !important;
            }

            .dataview .mobile-details-collapse {
                margin-right: -50px;
                padding: 10px 10px 6px;
            }

            .dataview .mobile-details-row {
                display: grid;
                grid-template-columns: minmax(78px, auto) minmax(0, 1fr);
                align-items: start;
                column-gap: 10px;
                row-gap: 6px;
                margin-bottom: 6px;
                padding-bottom: 6px;
            }

            .dataview .mobile-details-label {
                margin: 0;
                white-space: nowrap;
            }

            .dataview .mobile-details-value {
                display: block;
                width: 100%;
                min-width: 0;
                text-align: left;
            }

            .dataview .mobile-product-info {
                justify-content: space-between;
                align-items: flex-start;
                width: 100%;
            }

            .dataview .mobile-product-info .product-name {
                flex: 1;
                min-width: 0;
                text-align: left;
                padding-right: 8px;
            }
        }

        /* Desktop: hide details toggle column, reset flex on first cell */
        @media (min-width: 768px) {

            .dataview table thead th.details-column,
            .dataview table tbody td:nth-child(2) {
                display: none !important;
            }

            /* Undo the mobile flex override on the first td */
            .dataview table tbody td:first-child {
                display: table-cell !important;
                align-items: unset !important;
                max-width: unset !important;
            }

            /* Desktop widget spacing */
            .col-lg-4.col-sm-6.col-4 {
                /* margin-bottom: 25px !important; */
            }
        }

        @media screen and (min-width: 768px) and (max-width: 1180px) {
            .content>.dashboard-widgets-row:first-child>div.col-lg-4.col-sm-6.col-4 {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
                padding-left: 8px !important;
                padding-right: 8px !important;
                margin-bottom: 12px !important;
            }

            .content>.dashboard-widgets-row:first-child>div.col-lg-4.col-sm-6.col-4 .dash-widget {
                min-height: 86px;
                margin: 0 !important;
            }

            .content>.dashboard-widgets-row:first-child>div.d-flex {
                flex: 0 0 50%;
                max-width: 50%;
                padding-left: 8px !important;
                padding-right: 8px !important;
                margin-bottom: 12px !important;
            }

            .content>.dashboard-widgets-row:first-child>div.d-flex .color_box {
                width: 100%;
                height: 100%;
            }

            .content>.dashboard-widgets-row:first-child>div.d-flex .dash-count {
                display: flex;
                align-items: center;
                justify-content: space-between;
                min-height: 86px;
                height: 100%;
                margin: 0 !important;
                padding: 14px 16px;
            }

            .content>.dashboard-widgets-row:first-child>div.d-flex .dash-counts h4 {
                margin-bottom: 4px;
            }

            .content>.dashboard-widgets-row:first-child>div.d-flex .dash-counts h5 {
                margin-bottom: 0;
                line-height: 1.25;
            }
        }

        /* ── iPad Mini / Air (768px – 1024px): charts side-by-side col-6 ── */
        @media screen and (min-width: 768px) and (max-width: 1024px) {

            /* CRM: col-lg-5 + col-lg-7 → 50% each */
            .col-lg-7.col-sm-12.col-12.d-flex,
            .col-lg-5.col-sm-12.col-12.d-flex {
                flex: 0 0 50% !important;
                max-width: 50% !important;
            }

            /* ERP Sales & Purchases charts */
            div.col-sm-12.col-12.d-flex:has(#saleschart),
            div.col-sm-12.col-12.d-flex:has(#purchasechart),
            /* HR charts */
            div.col-sm-12.col-12.d-flex:has(#hrAttendanceChart),
            div.col-sm-12.col-12.d-flex:has(#hrSalaryChart),
            /* CRM charts */
            div.col-sm-12.col-12.d-flex:has(#crmPipelineChart),
            div.col-sm-12.col-12.d-flex:has(#crmActivityChart) {
                flex: 0 0 50% !important;
                max-width: 50% !important;
            }

            /* Full width for Products Delivery table */
            div.col-sm-12.col-12.d-flex:has(.pending-delivery-scroll) {
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }

            /* Keep the Sales / Purchases header compact on iPad widths */
            .card-header.d-flex.justify-content-between.align-items-center {
                gap: 8px;
            }

            .card-header.d-flex.justify-content-between.align-items-center .card-title {
                flex: 1 1 auto;
                min-width: 0;
            }

            .chart-select-container {
                flex: 0 0 112px;
                max-width: 112px;
            }

            .chart-select-container .select2-container {
                width: 100% !important;
                min-width: 0 !important;
            }

            .chart-select-container .select2-container--default .select2-selection--single {
                width: 100% !important;
            }

            .chart-select-container .select2-container--default .select2-selection--single .select2-selection__rendered {
                font-size: 12px !important;
                padding-left: 8px !important;
                padding-right: 20px !important;
            }
        }

        /* Mobile collapse styles */
        /* .mobile-details-collapse {
                border: 1px solid #dee2e6;
                border-radius: 8px;
                margin-top: 10px;
                padding: 12px;
                background-color: #f8f9fa;
            } */

        .mobile-details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #dee2e6;
        }

        .mobile-details-label {
            font-weight: 600;
            color: #495057;
        }

        .mobile-details-value {
            color: #212529;
            text-align: right;
        }

        .dataview .product-link {
            display: flex;
            align-items: flex-start;
            max-width: 250px;
            text-decoration: none;
            color: inherit;
        }

        .dataview .product-name {
            flex: 1;
            min-width: 70px;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
            line-height: 1.35;
        }

        .mobile-product-info {
            display: flex;
            align-items: flex-start;
            justify-content: flex-end;
            gap: 8px;
            width: 100%;
        }

        .mobile-product-info .product-name {
            text-align: right;
        }

        /* Style for chart select2 dropdowns */
        .chart-select-container .select2-container--default .select2-selection--single {
            height: 31px !important;
            border: 1px solid #ced4da !important;
            border-radius: 4px !important;
        }

        .chart-select-container .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 29px !important;
            font-size: 13px !important;
            padding-left: 10px !important;
            padding-right: 25px !important;
        }

        .chart-select-container .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 29px !important;
            width: 10px !important;
        }

        /* Clearfix for mobile grid */
        @media screen and (max-width: 767px) {
            .dashboard-widgets-row::after {
                content: "";
                display: table;
                clear: both;
            }
        }

        .crm-section,
        .hr-section {
            position: relative;
            z-index: 1;
        }

        .crm-section::before,
        .hr-section::before {
            bottom: -10px;
            content: "";
            left: -4px;
            pointer-events: none;
            position: absolute;
            right: -4px;
            top: -10px;
            z-index: -1;
        }

        .crm-section::before {
            background: #f3f8ff;
        }

        .hr-section::before {
            background: #f6fbf6;
        }

        .crm-section .card {
            border: 1px solid #dbeafe;
            box-shadow: none;
        }

        .hr-section .card {
            border: 1px solid #d9f0df;
            box-shadow: none;
        }

        .pending-delivery-scroll {
            max-height: 250px;
            overflow-x: auto;
            overflow-y: auto;
        }

        .custom-table-scroll {
            max-height: 250px;
            overflow-y: auto;
            overflow-x: auto;
        }

        .custom-table-scroll thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: #fff;
        }

        .custom-table-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-table-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-table-scroll::-webkit-scrollbar-thumb {
            background: #ff9f43;
            border-radius: 10px;
        }

        .custom-table-scroll::-webkit-scrollbar-thumb:hover {
            background: #e68a35;
        }

        .pending-delivery-scroll table {
            min-width: 0;
            table-layout: fixed;
            width: 100%;
        }

        .pending-delivery-scroll thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: #fff;
        }

        .pending-delivery-scroll td,
        .pending-delivery-scroll th {
            vertical-align: middle;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pending-delivery-scroll th:nth-child(1),
        .pending-delivery-scroll td:nth-child(1) {
            width: 38%;
        }

        .pending-delivery-scroll th:nth-child(2),
        .pending-delivery-scroll td:nth-child(2) {
            width: 26%;
        }

        .pending-delivery-scroll th:nth-child(3),
        .pending-delivery-scroll td:nth-child(3) {
            width: 36%;
            overflow: visible;
        }

        .pending-delivery-status {
            max-width: 100%;
            min-width: 0;
            width: 100%;
            height: 30px;
            border: 1px solid #d7dde8;
            border-radius: 6px;
            background-color: #fff;
            color: #1f2937;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 20px 4px 8px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .pending-delivery-status:focus {
            border-color: #ff9f43;
            box-shadow: 0 0 0 3px rgba(255, 159, 67, 0.16);
            outline: none;
        }

        .pending-delivery-status.is-saving {
            opacity: 0.7;
        }

        .crm-metric {
            min-height: 120px;
        }

        .hr-metric {
            min-height: 118px;
            height: 80%;
        }

        .crm-metric-label,
        .hr-metric-label {
            color: #667085;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .crm-metric-value,
        .hr-metric-value {
            color: #1f2937;
            font-size: 28px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 8px;
        }

        .crm-metric-note,
        .hr-metric-note {
            color: #667085;
            font-size: 12px;
            line-height: 1.35;
            margin: 0;
        }

        .crm-mini-badge,
        .hr-mini-badge {
            border-radius: 4px;
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 7px;
            max-width: 100%;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .crm-section .table,
        .hr-section .table {
            table-layout: fixed;
            width: 100%;
        }

        .crm-section .table th,
        .crm-section .table td,
        .hr-section .table th,
        .hr-section .table td {
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
            vertical-align: top;
        }

        .crm-section .table a,
        .crm-section .table .text-muted,
        .crm-section .table .mobile-details-label,
        .crm-section .table .mobile-details-value,
        .hr-section .table a,
        .hr-section .table .text-muted,
        .hr-section .table .mobile-details-label,
        .hr-section .table .mobile-details-value {
            max-width: 100%;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .crm-section .table .details-column,
        .hr-section .table .details-column {
            width: 42px;
            word-break: normal;
            overflow-wrap: normal;
        }

        .crm-chart,
        .hr-chart {
            min-height: 260px;
            width: 100%;
        }

        .crm-section:first-of-type,
        .hr-section:first-of-type {
            margin-top: 12px;
            padding-top: 18px;
        }

        .crm-section:first-of-type::before,
        .hr-section:first-of-type::before {
            border-radius: 8px 8px 0 0;
            top: 0;
        }

        .crm-section:nth-last-of-type(1)::before,
        .hr-section:nth-last-of-type(1)::before {
            border-radius: 0 0 8px 8px;
        }

        .pulse-zone {
            /* border-radius: 8px; */
            /* margin: 18px 0; */
            /* margin: 0px 0; */
            /* padding: 0px 14px 14px; */
        }

        /* .pulse-crm {
                background: #f3f8ff;
                padding: 12px 15px;
            } */

        /* .pulse-hr {
                background: #f6fbf6;
                padding: 12px 15px;
            } */

        /* .pulse-zone .row {
                margin-left: 0px;
                margin-right: 0px;
            } */

        .pulse-zone .crm-section,
        .pulse-zone .hr-section {
            padding-left: 15px;
            padding-right: 7px;
        }

        .pulse-zone .crm-section::before,
        .pulse-zone .hr-section::before {
            display: none;
        }

        /* Clickable table row hover */
        .table tbody tr[onclick] {
            transition: background-color 0.15s ease;
        }

        .table tbody tr[onclick]:hover {
            background-color: #fff8f2 !important;
        }

        /* =========================================
               DARK MODE TEXT COLOR FIXES FOR DASHBOARD
               ========================================= */
        :root[data-theme="dark"] .dash-widgetcontent h5 {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .dash-widgetcontent h6 {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .dash-counts h4 {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .dash-counts h5 {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .color_box .dash-counts h4 {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .color_box .dash-counts h5 {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .mobile-details-label {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .mobile-details-value {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .mobile-details-collapse {
            background-color: #334155 !important;
            border-color: #475569 !important;
        }

        :root[data-theme="dark"] .crm-metric-label,
        :root[data-theme="dark"] .hr-metric-label {
            color: #cbd5e1 !important;
        }

        :root[data-theme="dark"] .crm-metric-value,
        :root[data-theme="dark"] .hr-metric-value {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .crm-metric-note,
        :root[data-theme="dark"] .hr-metric-note {
            color: #cbd5e1 !important;
        }

        :root[data-theme="dark"] .dashboard-status-label {
            color: #e2e8f0 !important;
        }

        :root[data-theme="dark"] .dashboard-summary-entry {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25) !important;
        }

        :root[data-theme="dark"] .dashboard-summary-note {
            background-color: #111827 !important;
            color: #cbd5e1 !important;
        }

        :root[data-theme="dark"] .dashboard-summary-note p {
            color: #cbd5e1 !important;
        }

        :root[data-theme="dark"] .dashboard-summary-note i {
            color: #94a3b8 !important;
        }

        :root[data-theme="dark"] .dashboard-product-name {
            color: #f8fafc !important;
        }

        :root[data-theme="dark"] .card-title {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .dataview .product-link {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .dataview .product-name {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .pending-delivery-status {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
            border-color: #475569 !important;
        }

        :root[data-theme="dark"] .crm-section .card .card-header,
        :root[data-theme="dark"] .hr-section .card .card-header {
            background-color: #334155 !important;
            border-color: #475569 !important;
        }

        :root[data-theme="dark"] h5,
        :root[data-theme="dark"] h6 {
            color: #f1f5f9 !important;
        }

        /* Dark mode for ApexCharts text elements */
        :root[data-theme="dark"] .apexcharts-text {
            fill: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .apexcharts-xaxis-label {
            color: #f1f5f9 !important;
            fill: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .apexcharts-yaxis-label {
            color: #f1f5f9 !important;
            fill: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .apexcharts-legend-text {
            color: #f1f5f9 !important;
            fill: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .apexcharts-tooltip {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .apexcharts-tooltip-text {
            color: #f1f5f9 !important;
            fill: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .apexcharts-tooltip-title {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] text {
            fill: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .apexcharts-legend {
            color: #f1f5f9 !important;
        }

        /* Table header and body text in dark mode */
        :root[data-theme="dark"] .table th {
            color: #f1f5f9 !important;
            background-color: #334155 !important;
        }

        :root[data-theme="dark"] .table td {
            color: #f1f5f9 !important;
        }

        /* Chart title and labels */
        :root[data-theme="dark"] .card-header h6,
        :root[data-theme="dark"] .card-header .card-title {
            color: #f1f5f9 !important;
        }

        /* Specific for HR and CRM sections */
        :root[data-theme="dark"] .crm-section,
        :root[data-theme="dark"] .hr-section {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .crm-section .table tbody tr td,
        :root[data-theme="dark"] .hr-section .table tbody tr td {
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] .crm-section .table thead th,
        :root[data-theme="dark"] .hr-section .table thead th {
            background-color: #334155 !important;
            color: #f1f5f9 !important;
        }

        /* Phone dashboard */
        @media (max-width: 767.98px) {
            .db-page.content {
                padding: 8px 8px 90px !important;
            }
            .db-page > .db-quick-row {
                display: none !important;
            }

            .db-m-head {
                margin-bottom: 10px;
                padding: 14px 12px 12px;
                border-radius: 14px;
                background: linear-gradient(135deg, #ff9f43 0%, #e87b1e 100%);
                box-shadow: 0 6px 16px rgba(232, 123, 30, .25);
                color: #fff;
            }
            .db-m-hello {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                margin-bottom: 12px;
            }
            .db-m-greet {
                font-size: 17px;
                font-weight: 700;
                line-height: 1.2;
            }
            .db-m-date {
                margin-top: 2px;
                font-size: 12px;
                opacity: .9;
            }
            .db-m-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 40px;
                width: 40px;
                height: 40px;
                border-radius: 12px;
                background: rgba(255, 255, 255, .2);
                font-size: 17px;
            }
            .db-m-actions {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 6px;
            }
            .db-m-action {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 5px;
                min-width: 0;
                padding: 8px 2px;
                border: 0;
                border-radius: 10px;
                background: #fff;
                color: #1b2850 !important;
                font-size: 10.5px;
                font-weight: 600;
                line-height: 1.15;
                text-align: center;
                text-decoration: none;
            }
            .db-m-action span {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                font-size: 14px;
            }
            .db-m-action .is-green { background: #e7f6ec; color: #198754; }
            .db-m-action .is-orange { background: #fff3e6; color: #e07b14; }
            .db-m-action .is-blue { background: #e6f8fc; color: #0aa2c0; }
            .db-m-action .is-navy { background: #e9ecf5; color: #1b2850; }

            .db-page .dashboard-widgets-row .dash-widget {
                height: auto !important;
                min-height: 0 !important;
                align-items: flex-start !important;
                text-align: left !important;
                padding: 10px !important;
                border: 0 !important;
                border-radius: 12px !important;
                background: #fff !important;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08) !important;
            }
            .db-page .dashboard-widgets-row .dash-widget:hover {
                transform: none;
            }
            .db-page .dashboard-widgets-row .dash-widgetimg {
                margin-bottom: 8px !important;
            }
            .db-page .dashboard-widgets-row .dash-widgetimg span {
                width: 32px !important;
                height: 32px !important;
                line-height: 32px !important;
            }
            .db-page .dashboard-widgets-row .dash-widgetimg span img {
                width: 18px !important;
                height: 18px !important;
            }
            .db-page .dashboard-widgets-row .dash-widgetcontent {
                padding: 0 !important;
            }
            .db-page .dashboard-widgets-row .dash-widgetcontent h5,
            .db-page .dashboard-widgets-row .dash-widgetcontent h5 .counters {
                margin: 0 0 2px !important;
                color: #1b2850 !important;
                font-size: 12.5px !important;
                font-weight: 800 !important;
                white-space: nowrap;
            }
            .db-page .dashboard-widgets-row .dash-widgetcontent h5 {
                max-width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .db-page .dashboard-widgets-row .dash-widgetcontent h6 {
                font-size: 10px !important;
                color: #8a92a6 !important;
            }

            .db-page .dashboard-widgets-row > .col-6 {
                padding-left: 5px !important;
                padding-right: 5px !important;
                margin-bottom: 10px;
            }
            .db-page .color_box {
                height: 100%;
                margin-bottom: 0;
            }
            .db-page .color_box .dash-count {
                min-height: 0 !important;
                margin: 0 !important;
                padding: 12px !important;
                border: 0 !important;
                border-radius: 12px !important;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08) !important;
            }
            .db-page .color_box .dash-counts h4 {
                margin-bottom: 2px;
                font-size: 20px !important;
                font-weight: 800;
                color: #1b2850 !important;
            }
            .db-page .color_box .dash-counts h5 {
                margin: 0;
                font-size: 11.5px !important;
                color: #8a92a6 !important;
            }
            .db-page .color_box .dash-imgs {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 40px;
                width: 40px;
                height: 40px;
                border-radius: 12px;
            }
            .db-page .color_box .dash-imgs > i {
                font-size: 18px !important;
            }
            .db-page .color_box .dash-imgs > svg {
                width: 20px !important;
                height: 20px !important;
            }
            .db-page .color_box.box1 .dash-imgs { background-color: #e7f6ec !important; }
            .db-page .color_box.box2 .dash-imgs { background-color: #eef0f4 !important; }
            .db-page .color_box.box3 .dash-imgs { background-color: #fff3e6 !important; }
            .db-page .color_box.box4 .dash-imgs { background-color: #e6f8fc !important; }

            .db-page .card {
                border: 0 !important;
                border-radius: 14px !important;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08) !important;
                margin-bottom: 12px;
            }
            .db-page .card > .card-header[style*="#e87b1e"] {
                padding: 12px 14px !important;
                background: #fff !important;
                border-bottom: 1px solid #eef0f4 !important;
            }
            .db-page .card > .card-header[style*="#e87b1e"] .card-title {
                position: relative;
                padding-left: 10px;
                color: #1b2850 !important;
                font-size: 15px !important;
                font-weight: 700 !important;
            }
            .db-page .card > .card-header[style*="#e87b1e"] .card-title::before {
                content: "";
                position: absolute;
                left: 0;
                top: 2px;
                bottom: 2px;
                width: 3px;
                border-radius: 3px;
                background: #ff9f43;
            }
            .db-page .card > .card-header[style*="#e87b1e"] > .btn {
                padding: 4px 12px !important;
                border-radius: 999px !important;
                background: #fff3e6 !important;
                color: #e07b14 !important;
                font-size: 12px;
            }
            .db-page .card > .card-header[style*="#e87b1e"] select {
                border-radius: 8px;
                border-color: #e4e7ec;
            }
            .db-page .card > .card-body[style*="padding: 20px"] {
                padding: 12px !important;
            }
            .db-page .card > .card-body {
                border: 0 !important;
            }

            :root[data-theme="dark"] .db-page .dashboard-widgets-row .dash-widget,
            :root[data-theme="dark"] .db-page .card > .card-header[style*="#e87b1e"] {
                background: #1e293b !important;
            }
            :root[data-theme="dark"] .db-m-action {
                background: #1e293b;
                color: #f8fafc !important;
            }
            :root[data-theme="dark"] .db-page .dashboard-widgets-row .dash-widgetcontent h5,
            :root[data-theme="dark"] .db-page .dashboard-widgets-row .dash-widgetcontent h5 .counters,
            :root[data-theme="dark"] .db-page .color_box .dash-counts h4,
            :root[data-theme="dark"] .db-page .card > .card-header[style*="#e87b1e"] .card-title {
                color: #f8fafc !important;
            }
        }

        /* Dashboard data tables use compact row cards on phones and tablets. */
        @media (max-width: 1024.98px) {
            .db-page .table-responsive:not(.pending-delivery-scroll) {
                overflow-x: visible !important;
            }

            .db-page .table-responsive:not(.pending-delivery-scroll) > .table {
                border-collapse: separate !important;
                border-spacing: 0 8px !important;
                margin: -8px 0 0 !important;
            }

            .db-page .table-responsive:not(.pending-delivery-scroll) > .table thead {
                display: none !important;
            }

            .db-page .table-responsive:not(.pending-delivery-scroll) > .table tbody tr {
                display: grid !important;
                grid-template-columns: minmax(0, 1fr);
                align-items: center;
                margin: 0 0 8px !important;
                overflow: hidden;
                border: 1px solid #eef0f4 !important;
                border-radius: 10px;
                background: #fff !important;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .06);
            }

            .db-page .table-responsive:not(.pending-delivery-scroll) > .table tbody td {
                display: block !important;
                width: auto !important;
                max-width: none !important;
                padding: 10px 12px !important;
                border: 0 !important;
                background: transparent !important;
                text-align: left !important;
                white-space: normal !important;
                overflow: visible !important;
                text-overflow: clip !important;
            }

            .db-page .table-responsive:not(.pending-delivery-scroll) > .table tbody td:first-child {
                grid-column: 1;
                grid-row: 1;
                min-width: 0;
            }

            /* Keep the recent sale amount and date together at the card's top right. */
            .db-page .table-responsive > .table tbody tr:has(.dashboard-product-name) {
                grid-template-columns: minmax(0, 1fr) auto;
                grid-template-rows: auto auto auto;
            }

            .db-page .table-responsive > .table tbody tr:has(.dashboard-product-name) > td:nth-child(3),
            .db-page .table-responsive > .table tbody tr:has(.dashboard-product-name) > td:nth-child(4) {
                grid-column: 2;
                padding: 10px 12px 0 0 !important;
                white-space: nowrap !important;
            }

            .db-page .table-responsive > .table tbody tr:has(.dashboard-product-name) > td:nth-child(3) {
                grid-row: 1;
                text-align: right !important;
            }

            .db-page .table-responsive > .table tbody tr:has(.dashboard-product-name) > td:nth-child(4) {
                grid-row: 2;
                padding-top: 2px !important;
                text-align: right !important;
            }

            .db-page .table-responsive > .table tbody tr:has(.dashboard-product-name) > td:nth-child(2) {
                grid-column: 1 / -1;
                grid-row: 2 / span 2;
            }

            .db-page .table-responsive:not(.pending-delivery-scroll) > .table tbody td.details-column,
            .db-page .table-responsive:not(.pending-delivery-scroll) > .table thead th.details-column {
                display: none !important;
            }

            .db-page .table-responsive:not(.pending-delivery-scroll) > .table tbody td:not(:first-child):not(.details-column) {
                grid-column: 1 / -1;
                padding: 0 12px 9px !important;
            }

            .db-page .table-responsive:not(.pending-delivery-scroll) > .table tbody td:nth-child(2):not(.details-column) {
                grid-column: 1 / -1;
            }

            /* Two-line mobile records only for the four requested dashboard tables. */
            .db-page .hr-payroll-status-table tbody td:not(:first-child):not(.details-column),
            .db-page .crm-pipeline-quality-table tbody td:not(:first-child):not(.details-column),
            .db-page .crm-recent-leads-table tbody td:not(:first-child):not(.details-column),
            .db-page .crm-next-seven-days-table tbody td:not(:first-child):not(.details-column) {
                display: block !important;
                grid-column: 2 !important;
                padding: 10px 12px 0 0 !important;
                text-align: right !important;
            }

            .db-page .hr-payroll-status-table tbody tr,
            .db-page .crm-pipeline-quality-table tbody tr,
            .db-page .crm-recent-leads-table tbody tr,
            .db-page .crm-next-seven-days-table tbody tr {
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
                grid-template-rows: auto auto !important;
            }

            .db-page .hr-payroll-status-table tbody td:first-child,
            .db-page .crm-pipeline-quality-table tbody td:first-child,
            .db-page .crm-recent-leads-table tbody td:first-child,
            .db-page .crm-next-seven-days-table tbody td:first-child {
                grid-column: 1 !important;
                grid-row: 1 / span 2 !important;
            }

            .db-page .hr-payroll-status-table tbody td:not(:first-child):not(.details-column) ~ td:not(.details-column),
            .db-page .crm-pipeline-quality-table tbody td:not(:first-child):not(.details-column) ~ td:not(.details-column),
            .db-page .crm-recent-leads-table tbody td:not(:first-child):not(.details-column) ~ td:not(.details-column),
            .db-page .crm-next-seven-days-table tbody td:not(:first-child):not(.details-column) ~ td:not(.details-column) {
                padding-top: 2px !important;
            }

            .db-page .hr-payroll-status-table .mobile-details-collapse,
            .db-page .crm-pipeline-quality-table .mobile-details-collapse,
            .db-page .crm-recent-leads-table .mobile-details-collapse,
            .db-page .crm-next-seven-days-table .mobile-details-collapse {
                display: none !important;
            }

            .db-page .table-responsive:not(.pending-delivery-scroll) > .table tbody td[colspan] {
                grid-column: 1 / -1;
                padding: 14px 12px !important;
            }

            .db-page .table-responsive:not(.pending-delivery-scroll) .mobile-details-collapse {
                margin: 8px 0 0 !important;
                padding: 9px 10px 4px !important;
                border-radius: 8px;
                background: #f8fafc;
            }

            .db-page .table-responsive:not(.pending-delivery-scroll) .mobile-details-row {
                display: grid;
                grid-template-columns: minmax(75px, auto) minmax(0, 1fr);
                gap: 8px;
                padding-bottom: 6px;
                margin-bottom: 6px;
            }

            .db-page .table-responsive:not(.pending-delivery-scroll) .mobile-details-value {
                min-width: 0;
                text-align: right;
                overflow-wrap: anywhere;
            }

            .db-page .table-responsive:not(.pending-delivery-scroll) td > div[style*="display: flex"] {
                gap: 8px !important;
            }

            .db-page .table-responsive:not(.pending-delivery-scroll) .dashboard-product-name {
                min-width: 0;
                white-space: normal !important;
                overflow-wrap: anywhere;
            }

            :root[data-theme="dark"] .db-page .table-responsive:not(.pending-delivery-scroll) > .table tbody tr {
                background: #1e293b !important;
                border-color: #334155 !important;
            }

            :root[data-theme="dark"] .db-page .table-responsive:not(.pending-delivery-scroll) .mobile-details-collapse {
                background: #334155 !important;
            }
        }
    </style>
    <div class="content db-page">
        <div class="db-m-head d-md-none">
            <div class="db-m-hello">
                <div>
                    <div class="db-m-greet">Hello, {{ \Illuminate\Support\Str::limit(auth()->user()->name ?? 'there', 22) }}</div>
                    <div class="db-m-date">{{ now()->format('l, d M Y') }}</div>
                </div>
                <span class="db-m-icon"><i class="fas fa-chart-line"></i></span>
            </div>
            <div class="db-m-actions">
                @if (app('hasPermission')(2, 'add'))
                    <a href="{{ route('sales.add', ['sale_type' => 'sales']) }}" class="db-m-action">
                        <span class="is-green"><i class="fas fa-rupee-sign"></i></span>New Sale
                    </a>
                @endif
                @if (app('hasPermission')(1, 'add'))
                    <a href="{{ route('product.add') }}" class="db-m-action">
                        <span class="is-orange"><i class="fas fa-cube"></i></span>Add Product
                    </a>
                @endif
                @if (app('hasPermission')(9, 'add'))
                    <a href="{{ route('customer.add') }}" class="db-m-action">
                        <span class="is-blue"><i class="fas fa-user-plus"></i></span>Add Customer
                    </a>
                @endif
                <button type="button" class="db-m-action" data-bs-toggle="modal" data-bs-target="#quickProductModal">
                    <span class="is-navy"><i class="fa fa-search"></i></span>Quick Product
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-3 d-lg-none db-quick-row">
            <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#quickProductModal"
                style="background: #ff9f43; color: #fff; border: 1px solid #ff9f43; border-radius: 6px; padding: 0px 4px; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                <i class="fa fa-search"></i> Quick Product
            </button>
        </div>
        <div class="row dashboard-widgets-row">

            @if ($showErpDashboard && in_array($role, ['inventory-manager', 'admin', 'sub-admin']) && $showErpTotalSales)
                <div class="col-lg-4 col-sm-6 col-4">
                    <a href="{{ route('sales.list') }}">
                        <div class="dash-widget dash1">
                            <div class="dash-widgetimg">
                                <span><img src="{{ env('ImagePath') . 'admin/assets/img/icons/dash2.svg' }}"
                                        alt="img"></span>
                            </div>
                            <div class="dash-widgetcontent">
                                <h5>
                                    @if ($currencyPosition === 'left')
                                        {{ $currencySymbol }}
                                    @endif
                                    <span class="counters" data-count="{{ $totalSalesAmount }}">
                                        {{ number_format($totalSalesAmount, 2) }}</span>
                                    @if ($currencyPosition === 'right')
                                        {{ $currencySymbol }}
                                    @endif
                                </h5>
                                <h6>Total Sales Amount</h6>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if ($showErpDashboard && in_array($role, ['inventory-manager', 'admin', 'sub-admin']) && $showErpTotalPurchase)
                <div class="col-lg-4 col-sm-6 col-4">
                    <a href="{{ route('purchase.lists') }}">
                        <div class="dash-widget">
                            <div class="dash-widgetimg">
                                <span><img src="{{ env('ImagePath') . 'admin/assets/img/icons/dash1.svg' }}"
                                        alt="img"></span>
                            </div>
                            <div class="dash-widgetcontent">
                                <h5>
                                    @if ($currencyPosition === 'left')
                                        {{ $currencySymbol }}
                                    @endif
                                    <span class="counters" data-count="{{ $totalPurchaseAmount }}">
                                        {{ number_format($totalPurchaseAmount, 2) }}
                                    </span>
                                    @if ($currencyPosition === 'right')
                                        {{ $currencySymbol }}
                                    @endif
                                </h5>
                                <h6>Total Purchase Amount</h6>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if ($showErpDashboard && in_array($role, ['inventory-manager', 'admin', 'sub-admin']) && $showErpTotalExpense)
                <div class="col-lg-4 col-sm-6 col-4">
                    <a href="{{ route('expense.list') }}">
                        <div class="dash-widget dash2">
                            <div class="dash-widgetimg">
                                <span><img src="{{ env('ImagePath') . 'admin/assets/img/icons/dash3.svg' }}"
                                        alt="img"></span>
                            </div>
                            <div class="dash-widgetcontent">
                                <h5>
                                    @if ($currencyPosition === 'left')
                                        {{ $currencySymbol }}
                                    @endif
                                    <span class="counters"
                                        data-count="{{ $totalExpenseAmount }}">{{ number_format($totalExpenseAmount, 2) }}</span>
                                    @if ($currencyPosition === 'right')
                                        {{ $currencySymbol }}
                                    @endif
                                </h5>
                                <h6>Total Expense Amount</h6>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if (in_array($role, ['sales-manager']))
                <div @class([
                    'col-lg-3 col-sm-6 col-6 d-flex' => in_array($role, [
                        'inventory-manager',
                        'admin',
                        'sub-admin',
                    ]),
                    'col-lg-4 col-sm-6 col-6 d-flex' => $role === 'sales-manager',
                ])>
                    <a href="{{ route('sales.list') }}" class="color_box box4">
                        <div class="dash-count das1">
                            <div class="dash-counts">
                                <h4>
                                    @if ($currencyPosition === 'left')
                                        {{ $currencySymbol }}
                                    @endif
                                    <span class="counters"
                                        data-count="{{ $totalSalesAmount }}">{{ number_format($totalSalesAmount, 2) }}</span>
                                    @if ($currencyPosition === 'right')
                                        {{ $currencySymbol }}
                                    @endif
                                </h4>
                                <h5>Total Sales Amount</h5>
                            </div>
                            <div class="dash-imgs">
                                <i data-feather="user-check"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            {{-- @if (
                $showHrDashboardSection &&
                    in_array($role, ['inventory-manager', 'admin', 'sub-admin']) &&
                    ($showHrStaffStrength ||
                        $showHrActiveStaff ||
                        $showHrMonthlyAttendance ||
                        $showHrPersonalProgress ||
                        $showHrAttendancePattern ||
                        $showHrSalaryPayrollTrend ||
                        $showHrPayrollSnapshot ||
                        $showHrAttendanceWatch ||
                        $showHrPayrollStatus))
                <div class="col-12 hr-section mt-0 pt-0">
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                        <div>
                            <h4 class="mb-1">HR Workforce Overview</h4>
                            <p class="text-muted mb-0">Staff strength, attendance performance, payroll progress, and advance exposure.</p>
                        </div>
                        <a href="{{ route('staff.list') }}" class="btn btn-sm btn-primary">View Staff</a>
                    </div>
                </div>

                @if ($showHrStaffStrength)
                    <div class="col-lg-3 col-sm-6 col-6 d-flex hr-section">
                        <a href="{{ route('staff.list') }}" class="color_box w-100">
                            <div class="card flex-fill hr-metric" style="cursor:pointer;">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="hr-metric-label">Staff</div>
                                        <div class="hr-metric-value">{{ number_format($totalStaff, 0) }}</div>
                                        <p class="hr-metric-note">{{ number_format($activeStaff, 0) }} active,
                                            {{ number_format($newStaffThisMonth, 0) }} joined this month</p>
                                    </div>
                                    <div
                                        style="background-color: #fff3e8; border-radius: 8px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 12px;">
                                        <i data-feather="users"
                                            style="width: 20px; height: 20px; color: #e87b1e; stroke-width: 2;"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                @if ($showHrActiveStaff)
                    <div class="col-lg-3 col-sm-6 col-6 d-flex hr-section">
                        <a href="{{ route('attendence.summary') }}" class="color_box w-100">
                            <div class="card flex-fill hr-metric" style="cursor:pointer;">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="hr-metric-label">Today Attendance</div>
                                        <div class="hr-metric-value">{{ number_format($todayPresentStaff, 0) }}</div>
                                        <p class="hr-metric-note">{{ number_format($todayAbsentStaff, 0) }} absent,
                                            {{ number_format($todayUnmarkedStaff, 0) }} unmarked</p>
                                    </div>
                                    <div
                                        style="background-color: #e8f8f0; border-radius: 8px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 12px;">
                                        <i data-feather="check-circle"
                                            style="width: 20px; height: 20px; color: #10b981; stroke-width: 2;"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                @if ($showHrMonthlyAttendance)
                    <div class="col-lg-3 col-sm-6 col-6 d-flex hr-section">
                        <a href="{{ route('attendence.summary') }}" class="color_box w-100">
                            <div class="card flex-fill hr-metric" style="cursor:pointer;">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="hr-metric-label">Monthly Attendance</div>
                                        <div class="hr-metric-value">{{ $monthlyAttendanceRate }}%</div>
                                        <p class="hr-metric-note">Based on marked present, half-day, and absent entries</p>
                                    </div>
                                    <div
                                        style="background-color: #eef2ff; border-radius: 8px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 12px;">
                                        <i data-feather="bar-chart-2"
                                            style="width: 20px; height: 20px; color: #6366f1; stroke-width: 2;"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                @if ($showHrPersonalProgress)
                    <div class="col-lg-3 col-sm-6 col-6 d-flex hr-section">
                        <a href="{{ route('payroll.list') }}" class="color_box w-100">
                            <div class="card flex-fill hr-metric" style="cursor:pointer;">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="hr-metric-label">Payroll Progress</div>
                                        <div class="hr-metric-value">
                                            {{ number_format($salaryPaidCount, 0) }}/{{ number_format($totalStaff, 0) }}
                                        </div>
                                        <p class="hr-metric-note">{{ number_format($salaryPendingCount, 0) }} pending this
                                            month</p>
                                    </div>
                                    <div
                                        style="background-color: #fef3c7; border-radius: 8px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 12px;">
                                        <i data-feather="credit-card"
                                            style="width: 20px; height: 20px; color: #f59e0b; stroke-width: 2;"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif



                @if ($showHrPayrollSnapshot)
                    <div class="col-lg-4 col-sm-12 col-12 d-flex hr-section">
                        <div class="card flex-fill"
                            style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <div class="card-header d-flex justify-content-between align-items-center"
                                style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                                <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">
                                    Payroll</h4>
                                <a href="{{ route('payroll.list') }}" class="btn btn-sm"
                                    style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View
                                    All</a>
                            </div>
                            <div class="card-body"
                                style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px;">
                                <div class="table-responsive dataview custom-table-scroll hr-payroll-status-table">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Metric</th>
                                                <th class="details-column">Details</th>
                                                <th>Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div>
                                                        Paid this month
                                                        <div class="collapse mobile-details-collapse d-md-none"
                                                            id="payroll-snapshot-paid">
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Value:</span>
                                                                <span class="mobile-details-value">
                                                                    @if ($currencyPosition === 'left')
                                                                        {{ $currencySymbol }}
                                                                    @endif
                                                                    {{ number_format($salaryPaidAmount, 2) }}
                                                                    @if ($currencyPosition === 'right')
                                                                        {{ $currencySymbol }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="details-column">
                                                    <a href="#payroll-snapshot-paid" class="toggle-details"
                                                        data-bs-toggle="collapse">
                                                        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                    </a>
                                                </td>
                                                <td class="text-end d-none d-md-table-cell">
                                                    @if ($currencyPosition === 'left')
                                                        {{ $currencySymbol }}
                                                    @endif
                                                    {{ number_format($salaryPaidAmount, 2) }}
                                                    @if ($currencyPosition === 'right')
                                                        {{ $currencySymbol }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div>
                                                        Outstanding advances
                                                        <div class="collapse mobile-details-collapse d-md-none"
                                                            id="payroll-snapshot-advances">
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Value:</span>
                                                                <span class="mobile-details-value">
                                                                    @if ($currencyPosition === 'left')
                                                                        {{ $currencySymbol }}
                                                                    @endif
                                                                    {{ number_format($advanceOutstanding, 2) }}
                                                                    @if ($currencyPosition === 'right')
                                                                        {{ $currencySymbol }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="details-column">
                                                    <a href="#payroll-snapshot-advances" class="toggle-details"
                                                        data-bs-toggle="collapse">
                                                        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                    </a>
                                                </td>
                                                <td class="text-end d-none d-md-table-cell">
                                                    @if ($currencyPosition === 'left')
                                                        {{ $currencySymbol }}
                                                    @endif
                                                    {{ number_format($advanceOutstanding, 2) }}
                                                    @if ($currencyPosition === 'right')
                                                        {{ $currencySymbol }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div>
                                                        Salary pending staff
                                                        <div class="collapse mobile-details-collapse d-md-none"
                                                            id="payroll-snapshot-pending">
                                                            <div class="mobile-details-row">
                                                                <span class="mobile-details-label">Value:</span>
                                                                <span
                                                                    class="mobile-details-value">{{ number_format($salaryPendingCount, 0) }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="details-column">
                                                    <a href="#payroll-snapshot-pending" class="toggle-details"
                                                        data-bs-toggle="collapse">
                                                        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                    </a>
                                                </td>
                                                <td class="text-end d-none d-md-table-cell">
                                                    {{ number_format($salaryPendingCount, 0) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($showHrAttendanceWatch)
                    <div class="col-lg-4 col-sm-12 col-12 d-flex hr-section">
                        <div class="card flex-fill"
                            style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <div class="card-header d-flex justify-content-between align-items-center"
                                style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                                <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">
                                    Attendance</h4>
                                <a href="{{ route('attendence.calendar') }}" class="btn btn-sm"
                                    style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View
                                    All</a>
                            </div>
                            <div class="card-body"
                                style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px;">
                                <div class="table-responsive dataview custom-table-scroll">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Staff</th>
                                                <th class="details-column">Details</th>
                                                <th>Present</th>
                                                <th>Absent</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($attendanceWatch as $index => $staffRow)
                                                <tr>
                                                    <td>
                                                        <div>
                                                            {{ $staffRow->name }}
                                                            <div class="collapse mobile-details-collapse d-md-none"
                                                                id="attendance-details-{{ $staffRow->id ?? $index }}">
                                                                <div class="mobile-details-row">
                                                                    <span class="mobile-details-label">Present:</span>
                                                                    <span
                                                                        class="mobile-details-value">{{ number_format($staffRow->present_days, 0) }}</span>
                                                                </div>
                                                                <div class="mobile-details-row">
                                                                    <span class="mobile-details-label">Absent:</span>
                                                                    <span class="mobile-details-value">
                                                                        <span
                                                                            class="hr-mini-badge bg-light text-dark">{{ number_format($staffRow->absent_days, 0) }}</span>
                                                                        @if ($staffRow->half_days > 0)
                                                                            <div class="text-muted small">
                                                                                {{ number_format($staffRow->half_days, 0) }}
                                                                                half-day</div>
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="details-column">
                                                        <a href="#attendance-details-{{ $staffRow->id ?? $index }}"
                                                            class="toggle-details" data-bs-toggle="collapse">
                                                            <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                        </a>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        {{ number_format($staffRow->present_days, 0) }}</td>
                                                    <td class="d-none d-md-table-cell">
                                                        <span
                                                            class="hr-mini-badge bg-light text-dark">{{ number_format($staffRow->absent_days, 0) }}</span>
                                                        @if ($staffRow->half_days > 0)
                                                            <div class="text-muted small">
                                                                {{ number_format($staffRow->half_days, 0) }} half-day</div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No attendance data
                                                        found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($showHrPayrollStatus)
                    <div class="col-lg-4 col-sm-12 col-12 d-flex hr-section">
                        <div class="card flex-fill"
                            style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <div class="card-header d-flex justify-content-between align-items-center"
                                style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                                <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">
                                    Payroll Status</h4>
                                <a href="{{ route('payroll.list') }}" class="btn btn-sm"
                                    style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View
                                    All</a>
                            </div>
                            <div class="card-body"
                                style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px;">
                                <div class="table-responsive dataview custom-table-scroll hr-payroll-status-table">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Staff</th>
                                                <th class="details-column">Details</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($payrollStatusRows as $index => $payrollRow)
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <a
                                                                href="{{ route('staff.view', $payrollRow['id']) }}">{{ $payrollRow['name'] }}</a>
                                                            <div class="text-muted small">
                                                                {{ $payrollRow['phone'] ?? 'N/A' }}</div>
                                                            <div class="collapse mobile-details-collapse d-md-none"
                                                                id="payroll-status-details-{{ $payrollRow['id'] ?? $index }}">
                                                                <div class="mobile-details-row">
                                                                    <span class="mobile-details-label">Status:</span>
                                                                    <span class="mobile-details-value">
                                                                        <span
                                                                            class="hr-mini-badge bg-light text-dark">{{ $payrollRow['status'] }}</span>
                                                                    </span>
                                                                </div>
                                                                <div class="mobile-details-row">
                                                                    <span class="mobile-details-label">Amount:</span>
                                                                    <span class="mobile-details-value">
                                                                        @if ($currencyPosition === 'left')
                                                                            {{ $currencySymbol }}
                                                                        @endif
                                                                        {{ number_format($payrollRow['amount'], 2) }}
                                                                        @if ($currencyPosition === 'right')
                                                                            {{ $currencySymbol }}
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="details-column">
                                                        <a href="#payroll-status-details-{{ $payrollRow['id'] ?? $index }}"
                                                            class="toggle-details" data-bs-toggle="collapse">
                                                            <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                        </a>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        <span
                                                            class="hr-mini-badge bg-light text-dark">{{ $payrollRow['status'] }}</span>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        @if ($currencyPosition === 'left')
                                                            {{ $currencySymbol }}
                                                        @endif
                                                        {{ number_format($payrollRow['amount'], 2) }}
                                                        @if ($currencyPosition === 'right')
                                                            {{ $currencySymbol }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No staff records
                                                        found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($showHrAttendancePattern)
                    <div class="col-lg-7 col-sm-12 col-12 d-flex hr-section">
                        <div class="card flex-fill"
                            style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <div class="card-header d-flex justify-content-between align-items-center"
                                style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                                <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">7
                                    Day Attendance</h4>
                            </div>
                            <div class="card-body"
                                style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px;">
                                <div id="hrAttendanceChart" class="hr-chart"></div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($showHrSalaryPayrollTrend)
                    <div class="col-lg-5 col-sm-12 col-12 d-flex hr-section">
                        <div class="card flex-fill"
                            style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <div class="card-header d-flex justify-content-between align-items-center"
                                style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                                <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">
                                    Salary Payout</h4>
                            </div>
                            <div class="card-body"
                                style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px;">
                                <div id="hrSalaryChart" class="hr-chart"></div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif --}}

            @if (
                $showErpDashboard &&
                    in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']) &&
                    $showErpSalesInvoiceCount)
                <div class="col-lg col-sm-6 col-6 d-flex">
                    <a href="{{ route('sales.list') }}" class="color_box box1">
                        <div class="dash-count das3">
                            <div class="dash-counts">
                                <h4>{{ number_format($salesInvoiceCount, 0) }}</h4>
                                <h5>Sales Invoice</h5>
                            </div>
                            <div class="dash-imgs">
                                <i data-feather="file"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if (in_array($role, ['purchase-manager']))
                <div class="col-lg col-sm-6 col-6 d-flex">
                    <a href="{{ route('purchase.lists') }}" class="color_box box1">
                        <div class="dash-count das3">
                            <div class="dash-counts">
                                <h4>
                                    @if ($currencyPosition === 'left')
                                        {{ $currencySymbol }}
                                    @endif
                                    <span class="counters" data-count="{{ $totalPurchaseAmount }}">
                                        {{ number_format($totalPurchaseAmount, 2) }}
                                    </span>
                                    @if ($currencyPosition === 'right')
                                        {{ $currencySymbol }}
                                    @endif
                                </h4>
                                <h5>Total Purchase Amount</h5>
                            </div>
                            <div class="dash-imgs">
                                <i data-feather="file"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if (
                $showErpDashboard &&
                    in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin']) &&
                    $showErpPurchaseInvoiceCount)
                <div class="col-lg col-sm-6 col-6 d-flex">
                    <a href="{{ route('purchase.lists') }}" class="color_box box2">
                        <div class="dash-count das2">
                            <div class="dash-counts">
                                <h4>{{ number_format($purchaseInvoiceCount, 0) }}</h4>
                                <h5>Purchase Invoice</h5>
                            </div>
                            <div class="dash-imgs">
                                <i data-feather="file-text"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if (
                $showErpDashboard &&
                    in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin']) &&
                    $showErpRecentRentals)
                <div class="col-lg col-sm-6 col-6 d-flex">
                    <a href="{{ route('product.list') }}" class="color_box box2">
                        <div class="dash-count das2">
                            <div class="dash-counts">
                                <h4>{{ number_format($rentalProductsCount, 0) }}</h4>
                                <h5>Total Rental</h5>
                            </div>

                            <div class="dash-imgs">
                                <i class="fas fa-box-open"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if ($showErpDashboard && in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']))
                <div class="col-lg col-sm-6 col-6 d-flex">
                    <a href="{{ route('sales.list') }}" class="color_box box3">
                        <div class="dash-count das3">
                            <div class="dash-counts">
                                <h4>{{ number_format($totalQuotationsCount ?? 0, 0) }}</h4>
                                <h5>Total Quotations</h5>
                            </div>
                            <div class="dash-imgs">
                                <i data-feather="file-text"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if (
                $showErpDashboard &&
                    in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']) &&
                    $showErpCustomersCount)
                <div class="col-lg col-sm-6 col-6 d-flex">
                    <a href="{{ route('customer.list') }}" class="color_box box3">
                        <div class="dash-count">
                            <div class="dash-counts">
                                <h4>{{ number_format($customerCount, 0) }}</h4>
                                <h5>Customers</h5>
                            </div>
                            <div class="dash-imgs">
                                <i data-feather="users"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if (
                $showErpDashboard &&
                    in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin']) &&
                    $showErpVendorsCount)
                <div class="col-lg col-sm-6 col-6 d-flex">
                    <a href="{{ route('vendor.list') }}" class="color_box box4">
                        <div class="dash-count das1">
                            <div class="dash-counts">
                                <h4>{{ number_format($vendorCount, 0) }}</h4>
                                <h5>Vendors</h5>
                            </div>
                            <div class="dash-imgs">
                                <i data-feather="user-check"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

        </div>

        @php
            use Carbon\Carbon;
            $currentYear = Carbon::now()->year;
            $previousYear = $currentYear - 1;
            $showErpSalesChartCard =
                $showErpDashboard &&
                in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']) &&
                $showErpSalesChart;
            $showErpPurchaseChartCard =
                $showErpDashboard &&
                in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin']) &&
                $showErpPurchaseChart;
            $showErpProductsDeliveryCard =
                $showErpDashboard &&
                in_array($role, ['inventory-manager', 'sales-manager', 'purchase-manager', 'admin', 'sub-admin']) &&
                $showErpProductsDelivery;
            $erpMainSectionCount = collect([
                $showErpSalesChartCard,
                $showErpPurchaseChartCard,
                $showErpProductsDeliveryCard,
            ])
                ->filter()
                ->count();
            $erpMainSectionClass =
                $erpMainSectionCount === 1 ? 'col-lg-12' : ($erpMainSectionCount === 2 ? 'col-lg-6' : 'col-lg-4');
        @endphp

        <div class="row">

            @if (
                $showErpDashboard &&
                    in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']) &&
                    $showErpRecentSales)
                <div class="col-md-6">
                    <div class="card"
                        style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <div class="card-header d-flex justify-content-between align-items-center"
                            style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                            <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">Latest
                                Sales</h4>
                            <a href="{{ route('sales.list') }}" class="btn btn-sm"
                                style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View
                                All</a>
                        </div>
                        <div class="card-body"
                            style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding: 20px;">

                            <!-- Dark block: TOTAL SALES VOLUME -->
                            <a href="{{ route('sales.list') }}" style="text-decoration: none; display: block;">
                                <div style="background-color: #1f2937; border-radius: 8px; padding: 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; cursor: pointer; transition: opacity 0.2s ease;"
                                    onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">
                                    <div>
                                        <p
                                            style="color: #ea5455; font-size: 12px; font-weight: 600; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Total Sales Volume</p>
                                        <h3
                                            style="color: #ffffff; margin-bottom: 0; font-weight: 700; display: flex; align-items: center;">
                                            @if ($currencyPosition === 'left')
                                                {{ $currencySymbol }}
                                            @endif
                                            {{ number_format($totalSalesAmount, 2) }}
                                            @if ($currencyPosition === 'right')
                                                {{ $currencySymbol }}
                                            @endif
                                        </h3>
                                    </div>
                                    <div
                                        style="background-color: #e87b1e; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px rgba(232, 123, 30, 0.3);">
                                        <i data-feather="trending-up"
                                            style="color: white; width: 20px; height: 20px; stroke-width: 2.5;"></i>
                                    </div>
                                </div>
                            </a>

                            <!-- Two White Boxes: TOTAL ORDERS and THIS MONTH -->
                            <div class="row mb-4">
                                <div class="col-6">
                                    <a href="{{ route('sales.list') }}"
                                        style="text-decoration: none; display: block; height: 100%;">
                                        <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; height: 100%; cursor: pointer; transition: border-color 0.2s ease, box-shadow 0.2s ease; display: flex; align-items: center; justify-content: space-between;"
                                            onmouseover="this.style.borderColor='#e87b1e';this.style.boxShadow='0 2px 8px rgba(232,123,30,0.15)'"
                                            onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                                            <div>
                                                <p
                                                    style="color: #6b7280; font-size: 11px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">
                                                    Total Orders</p>
                                                <h4
                                                    style="color: #111827; margin-bottom: 0; font-weight: 700; font-size: 20px;">
                                                    {{ number_format($totalOrdersCount, 0) }}</h4>
                                            </div>
                                            <div
                                                style="background-color: #fff3e8; border-radius: 8px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 10px;">
                                                <i data-feather="shopping-bag"
                                                    style="width: 18px; height: 18px; color: #e87b1e; stroke-width: 2;"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('sales.list') }}"
                                        style="text-decoration: none; display: block; height: 100%;">
                                        <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; height: 100%; cursor: pointer; transition: border-color 0.2s ease, box-shadow 0.2s ease; display: flex; align-items: center; justify-content: space-between;"
                                            onmouseover="this.style.borderColor='#e87b1e';this.style.boxShadow='0 2px 8px rgba(232,123,30,0.15)'"
                                            onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                                            <div>
                                                <p
                                                    style="color: #6b7280; font-size: 11px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">
                                                    This Month</p>
                                                <h4
                                                    style="color: #111827; margin-bottom: 0; font-weight: 700; font-size: 20px;">
                                                    {{ number_format($ordersThisMonthCount, 0) }}</h4>
                                            </div>
                                            <div
                                                style="background-color: #e8f8f0; border-radius: 8px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 10px;">
                                                <i data-feather="calendar"
                                                    style="width: 18px; height: 18px; color: #10b981; stroke-width: 2;"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <!-- Progress Bars for Completed / Partially / Pending -->
                            @php
                                $completedPercent =
                                    $totalOrdersCount > 0
                                        ? round(($completedOrdersCount / $totalOrdersCount) * 100)
                                        : 0;
                                $partialPercent =
                                    $totalOrdersCount > 0 ? round(($partialOrdersCount / $totalOrdersCount) * 100) : 0;
                                $pendingPercent =
                                    $totalOrdersCount > 0 ? round(($pendingOrdersCount / $totalOrdersCount) * 100) : 0;
                            @endphp
                            <a href="{{ route('sales.list') }}"
                                style="text-decoration: none; display: block; color: inherit;">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="dashboard-status-label"
                                            style="font-weight: 700; font-size: 13px; color: #111827;">Completed</span>
                                        <span
                                            style="font-weight: 700; font-size: 13px; color: #10b981;">{{ $completedOrdersCount }}
                                            ({{ $completedPercent }}%)</span>
                                    </div>
                                    <div class="progress"
                                        style="height: 8px; border-radius: 4px; background-color: #e5e7eb;">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $completedPercent }}%; background-color: #10b981; border-radius: 4px;"
                                            aria-valuenow="{{ $completedPercent }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="dashboard-status-label"
                                            style="font-weight: 700; font-size: 13px; color: #111827;">Partially
                                            Paid</span>
                                        <span
                                            style="font-weight: 700; font-size: 13px; color: #3b82f6;">{{ $partialOrdersCount }}
                                            ({{ $partialPercent }}%)</span>
                                    </div>
                                    <div class="progress"
                                        style="height: 8px; border-radius: 4px; background-color: #e5e7eb;">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $partialPercent }}%; background-color: #3b82f6; border-radius: 4px;"
                                            aria-valuenow="{{ $partialPercent }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="dashboard-status-label"
                                            style="font-weight: 700; font-size: 13px; color: #111827;">Pending</span>
                                        <span
                                            style="font-weight: 700; font-size: 13px; color: #e87b1e;">{{ $pendingOrdersCount }}
                                            ({{ $pendingPercent }}%)</span>
                                    </div>
                                    <div class="progress"
                                        style="height: 8px; border-radius: 4px; background-color: #e5e7eb;">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $pendingPercent }}%; background-color: #e87b1e; border-radius: 4px;"
                                            aria-valuenow="{{ $pendingPercent }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </a>

                            <!-- Latest Sale Entry -->
                            @if ($latestSales->isNotEmpty())
                                @php
                                    $item = $latestSales->first();
                                    $statusColor = match (strtolower($item->payment_status ?? '')) {
                                        'completed', 'paid' => '#10b981',
                                        'partially' => '#3b82f6',
                                        default => '#e87b1e',
                                    };
                                @endphp
                                <a href="{{ route('sales.details', $item->order_id) }}"
                                    style="text-decoration: none; display: block;">
                                    <div class="dashboard-summary-entry"
                                        style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background-color: #ffffff; cursor: pointer; transition: border-color 0.2s ease, box-shadow 0.2s ease;"
                                        onmouseover="this.style.borderColor='#e87b1e';this.style.boxShadow='0 2px 8px rgba(232,123,30,0.15)'"
                                        onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                                        <div>
                                            <h5
                                                style="color: #111827; font-size: 14px; font-weight: 700; margin-bottom: 5px;">
                                                {{ $item->customer_name ?? 'N/A' }}</h5>
                                            <p
                                                style="color: #6b7280; font-size: 11px; font-weight: 500; margin-bottom: 0;">
                                                {{ $item->order_number ?? 'N/A' }} &bull;
                                                {{ \Carbon\Carbon::parse($item->order_date)->format('d M, Y') }}
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <h5
                                                style="color: #111827; font-size: 14px; font-weight: 700; margin-bottom: 5px;">
                                                @if ($currencyPosition === 'left')
                                                    {{ $currencySymbol }}
                                                @endif
                                                {{ number_format($item->total_amount, 2) }}
                                                @if ($currencyPosition === 'right')
                                                    {{ $currencySymbol }}
                                                @endif
                                            </h5>
                                            <p
                                                style="color: {{ $statusColor }}; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-bottom: 0; letter-spacing: 0.5px;">
                                                {{ $item->payment_status ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endif

                            <!-- Footer Note -->
                            <div class="dashboard-summary-note"
                                style="background-color: #f9fafb; border-radius: 8px; padding: 12px 15px; display: flex; align-items: center;">
                                <i data-feather="info"
                                    style="width: 14px; height: 14px; color: #6b7280; margin-right: 10px; stroke-width: 2.5;"></i>
                                <p style="color: #6b7280; font-size: 11px; font-weight: 500; margin-bottom: 0;">Overview is
                                    based on all visible sales and their statuses.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Latest Purchases Table - UPDATED -->
            @if (
                $showErpDashboard &&
                    in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin']) &&
                    $showErpRecentPurchases)
                <div class="col-md-6">
                    <div class="card mb-0"
                        style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <div class="card-header d-flex justify-content-between align-items-center"
                            style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                            <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">Latest
                                Purchases</h4>
                            <a href="{{ route('purchase.lists') }}" class="btn btn-sm"
                                style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View
                                All</a>
                        </div>
                        <div class="card-body"
                            style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding: 20px;">

                            @php
                                $purchaseCompletedPercent =
                                    $totalPurchasesCount > 0
                                        ? round(($completedPurchasesCount / $totalPurchasesCount) * 100)
                                        : 0;
                                $purchasePartialPercent =
                                    $totalPurchasesCount > 0
                                        ? round(($partialPurchasesCount / $totalPurchasesCount) * 100)
                                        : 0;
                                $purchasePendingPercent =
                                    $totalPurchasesCount > 0
                                        ? round(($pendingPurchasesCount / $totalPurchasesCount) * 100)
                                        : 0;
                            @endphp

                            <!-- Dark block: TOTAL PURCHASE VOLUME -->
                            <a href="{{ route('purchase.lists') }}" style="text-decoration: none; display: block;">
                                <div style="background-color: #1f2937; border-radius: 8px; padding: 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; cursor: pointer; transition: opacity 0.2s ease;"
                                    onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">
                                    <div>
                                        <p
                                            style="color: #ea5455; font-size: 12px; font-weight: 600; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Total Purchase Volume</p>
                                        <h3 style="color: #ffffff; margin-bottom: 0; font-weight: 700;">
                                            @if ($currencyPosition === 'left')
                                                {{ $currencySymbol }}
                                            @endif
                                            {{ number_format($totalPurchaseAmount, 2) }}
                                            @if ($currencyPosition === 'right')
                                                {{ $currencySymbol }}
                                            @endif
                                        </h3>
                                    </div>
                                    <div
                                        style="background-color: #e87b1e; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px rgba(232,123,30,0.3);">
                                        <i data-feather="shopping-cart"
                                            style="color: white; width: 20px; height: 20px; stroke-width: 2.5;"></i>
                                    </div>
                                </div>
                            </a>

                            <!-- Two White Boxes: TOTAL PURCHASES and THIS MONTH -->
                            <div class="row mb-4">
                                <div class="col-6">
                                    <a href="{{ route('purchase.lists') }}"
                                        style="text-decoration: none; display: block; height: 100%;">
                                        <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; height: 100%; cursor: pointer; transition: border-color 0.2s ease, box-shadow 0.2s ease; display: flex; align-items: center; justify-content: space-between;"
                                            onmouseover="this.style.borderColor='#e87b1e';this.style.boxShadow='0 2px 8px rgba(232,123,30,0.15)'"
                                            onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                                            <div>
                                                <p
                                                    style="color: #6b7280; font-size: 11px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">
                                                    Total Purchases</p>
                                                <h4
                                                    style="color: #111827; margin-bottom: 0; font-weight: 700; font-size: 20px;">
                                                    {{ number_format($totalPurchasesCount, 0) }}</h4>
                                            </div>
                                            <div
                                                style="background-color: #fff3e8; border-radius: 8px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 10px;">
                                                <i data-feather="package"
                                                    style="width: 18px; height: 18px; color: #e87b1e; stroke-width: 2;"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('purchase.lists') }}"
                                        style="text-decoration: none; display: block; height: 100%;">
                                        <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; height: 100%; cursor: pointer; transition: border-color 0.2s ease, box-shadow 0.2s ease; display: flex; align-items: center; justify-content: space-between;"
                                            onmouseover="this.style.borderColor='#e87b1e';this.style.boxShadow='0 2px 8px rgba(232,123,30,0.15)'"
                                            onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                                            <div>
                                                <p
                                                    style="color: #6b7280; font-size: 11px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">
                                                    This Month</p>
                                                <h4
                                                    style="color: #111827; margin-bottom: 0; font-weight: 700; font-size: 20px;">
                                                    {{ number_format($purchasesThisMonthCount, 0) }}</h4>
                                            </div>
                                            <div
                                                style="background-color: #eef2ff; border-radius: 8px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 10px;">
                                                <i data-feather="calendar"
                                                    style="width: 18px; height: 18px; color: #6366f1; stroke-width: 2;"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <!-- Progress Bars: Completed / Partially Paid / Pending -->
                            <a href="{{ route('purchase.lists') }}"
                                style="text-decoration: none; display: block; color: inherit;">
                                <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                        <span class="dashboard-status-label"
                                            style="font-weight: 700; font-size: 13px; color: #111827;">Completed</span>
                                        <span
                                            style="font-weight: 700; font-size: 13px; color: #10b981;">{{ $completedPurchasesCount }}
                                            ({{ $purchaseCompletedPercent }}%)</span>
                                    </div>
                                    <div class="progress"
                                        style="height: 8px; border-radius: 4px; background-color: #e5e7eb;">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $purchaseCompletedPercent }}%; background-color: #10b981; border-radius: 4px;"
                                            aria-valuenow="{{ $purchaseCompletedPercent }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="dashboard-status-label"
                                            style="font-weight: 700; font-size: 13px; color: #111827;">Partially
                                            Paid</span>
                                        <span
                                            style="font-weight: 700; font-size: 13px; color: #3b82f6;">{{ $partialPurchasesCount }}
                                            ({{ $purchasePartialPercent }}%)</span>
                                    </div>
                                    <div class="progress"
                                        style="height: 8px; border-radius: 4px; background-color: #e5e7eb;">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $purchasePartialPercent }}%; background-color: #3b82f6; border-radius: 4px;"
                                            aria-valuenow="{{ $purchasePartialPercent }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="dashboard-status-label"
                                            style="font-weight: 700; font-size: 13px; color: #111827;">Pending</span>
                                        <span
                                            style="font-weight: 700; font-size: 13px; color: #e87b1e;">{{ $pendingPurchasesCount }}
                                            ({{ $purchasePendingPercent }}%)</span>
                                    </div>
                                    <div class="progress"
                                        style="height: 8px; border-radius: 4px; background-color: #e5e7eb;">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $purchasePendingPercent }}%; background-color: #e87b1e; border-radius: 4px;"
                                            aria-valuenow="{{ $purchasePendingPercent }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </a>

                            <!-- Latest Purchase Entry -->
                            @if ($latestPurchases->isNotEmpty())
                                @php
                                    $latestPurchaseItem = $latestPurchases->first();
                                    $purchaseStatusColor = match (
                                        strtolower($latestPurchaseItem->purchase_status ?? '')
                                    ) {
                                        'completed' => '#10b981',
                                        'partially' => '#3b82f6',
                                        default => '#e87b1e',
                                    };
                                @endphp
                                <a href="{{ route('purchase.viewData', $latestPurchaseItem->invoice_id) }}"
                                    style="text-decoration: none; display: block;">
                                    <div class="dashboard-summary-entry"
                                        style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background-color: #ffffff; cursor: pointer; transition: border-color 0.2s ease, box-shadow 0.2s ease;"
                                        onmouseover="this.style.borderColor='#e87b1e';this.style.boxShadow='0 2px 8px rgba(232,123,30,0.15)'"
                                        onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                                        <div>
                                            <h5
                                                style="color: #111827; font-size: 14px; font-weight: 700; margin-bottom: 5px;">
                                                {{ $latestPurchaseItem->vendor_name ?? 'N/A' }}</h5>
                                            <p
                                                style="color: #6b7280; font-size: 11px; font-weight: 500; margin-bottom: 0;">
                                                {{ $latestPurchaseItem->bill_no ?? 'N/A' }} &bull;
                                                {{ $latestPurchaseItem->purchase_date ? \Carbon\Carbon::parse($latestPurchaseItem->purchase_date)->format('d M, Y') : 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <h5
                                                style="color: #111827; font-size: 14px; font-weight: 700; margin-bottom: 5px;">
                                                @if ($currencyPosition === 'left')
                                                    {{ $currencySymbol }}
                                                @endif
                                                {{ number_format($latestPurchaseItem->grand_total ?? 0, 2) }}
                                                @if ($currencyPosition === 'right')
                                                    {{ $currencySymbol }}
                                                @endif
                                            </h5>
                                        </div>
                                    </div>
                                </a>
                            @endif

                            <!-- Footer Note -->
                            <div class="dashboard-summary-note"
                                style="background-color: #f9fafb; border-radius: 8px; padding: 12px 15px; display: flex; align-items: center; margin-top: 10px;">
                                <i data-feather="info"
                                    style="width: 14px; height: 14px; color: #6b7280; margin-right: 10px; stroke-width: 2.5;"></i>
                                <p style="color: #6b7280; font-size: 11px; font-weight: 500; margin-bottom: 0;">Overview is
                                    based on all visible purchases and their current statuses.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Original Tables Restored Below Cards -->
            <div class="col-12">
                <div class="row mt-4">
                    @if (
                        $showErpDashboard &&
                            in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']) &&
                            $showErpRecentSales)
                        <div class="col-md-4">
                            <div class="card mb-4"
                                style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                                <div class="card-header d-flex justify-content-between align-items-center"
                                    style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                                    <h4 class="card-title mb-0"
                                        style="color: #ffffff; font-size: 16px; font-weight: 500;">Recent Sales</h4>
                                    <a href="{{ route('sales.list') }}" class="btn btn-sm"
                                        style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View
                                        All</a>
                                </div>
                                <div class="card-body p-0"
                                    style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px;">
                                    <div class="table-responsive">
                                        <table class="table mb-0" style="table-layout: auto;">
                                            <thead style="background-color: #f9fafb;">
                                                <tr>
                                                    <th
                                                        style="width: 15%; font-weight: 600; color: #374151; border-bottom: 1px solid #f0f0f0;">
                                                        Order ID</th>
                                                    <th
                                                        style="width: 40%; font-weight: 600; color: #374151; border-bottom: 1px solid #f0f0f0;">
                                                        Product Name</th>
                                                    <th
                                                        style="width: 20%; font-weight: 600; color: #374151; border-bottom: 1px solid #f0f0f0;">
                                                        Grand Total</th>
                                                    <th
                                                        style="width: 25%; font-weight: 600; color: #374151; border-bottom: 1px solid #f0f0f0;">
                                                        Sale Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($latestSales as $index => $item)
                                                    <tr style="cursor: pointer;"
                                                        onclick="window.location='{{ route('sales.details', $item->order_id) }}'">
                                                        <td style="color: #4b5563; vertical-align: middle;">
                                                            {{ $item->order_number ?? 'N/A' }}
                                                            <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">{{ $item->customer_name ?? 'N/A' }}</div>
                                                        </td>
                                                        <td style="vertical-align: middle;">
                                                            @php
                                                                $images = json_decode($item->images, true);
                                                                $imagePath =
                                                                    !empty($images) && isset($images[0])
                                                                        ? env('ImagePath') . 'storage/' . $images[0]
                                                                        : env('ImagePath') .
                                                                            'admin/assets/img/product/noimage.png';
                                                            @endphp
                                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                                <img src="{{ asset($imagePath) }}" alt="Product"
                                                                    style="width: 36px; height: 36px; object-fit: cover; border-radius: 6px; flex-shrink: 0; border: 1px solid #e5e7eb;">
                                                                <span class="dashboard-product-name"
                                                                    style="color: #374151; font-weight: 500;">{{ $item->product_name ?? 'N/A' }}</span>
                                                            </div>
                                                        </td>
                                                        <td
                                                            style="color: #111827; font-weight: 600; vertical-align: middle;">
                                                            @if ($currencyPosition === 'left')
                                                                {{ $currencySymbol }}
                                                            @endif
                                                            {{ number_format($item->total_amount, 2) }}
                                                            @if ($currencyPosition === 'right')
                                                                {{ $currencySymbol }}
                                                            @endif
                                                        </td>
                                                        <td style="color: #6b7280; vertical-align: middle;">
                                                            {{ \Carbon\Carbon::parse($item->order_date)->format('d F Y') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted"
                                                            style="padding: 20px;">No sales available.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (
                        $showErpDashboard &&
                            in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin']) &&
                            $showErpRecentPurchases)
                        <div class="col-md-4">
                            <div class="card mb-4"
                                style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                                <div class="card-header d-flex justify-content-between align-items-center"
                                    style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                                    <h4 class="card-title mb-0"
                                        style="color: #ffffff; font-size: 16px; font-weight: 500;">Recent Purchases</h4>
                                    <a href="{{ route('purchase.lists') }}" class="btn btn-sm"
                                        style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View
                                        All</a>
                                </div>
                                <div class="card-body p-0"
                                    style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px;">
                                    <div class="table-responsive">
                                        <table class="table mb-0" style="table-layout: auto;">
                                            <thead style="background-color: #f9fafb;">
                                                <tr>
                                                    <th
                                                        style="width: 15%; font-weight: 600; color: #374151; border-bottom: 1px solid #f0f0f0;">
                                                        Bill No</th>
                                                    <th
                                                        style="width: 40%; font-weight: 600; color: #374151; border-bottom: 1px solid #f0f0f0;">
                                                        Product Name</th>
                                                    <th
                                                        style="width: 20%; font-weight: 600; color: #374151; border-bottom: 1px solid #f0f0f0;">
                                                        Grand Total</th>
                                                    <th
                                                        style="width: 25%; font-weight: 600; color: #374151; border-bottom: 1px solid #f0f0f0;">
                                                        Purchase Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($latestPurchases as $index => $item)
                                                    <tr style="cursor: pointer;"
                                                        onclick="window.location='{{ route('purchase.viewData', $item->invoice_id) }}'">
                                                        <td style="color: #4b5563; vertical-align: middle;">
                                                            {{ $item->bill_no ?? 'N/A' }}
                                                            <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">{{ $item->vendor_name ?? 'N/A' }}</div>
                                                        </td>
                                                        <td style="vertical-align: middle;">
                                                            @php
                                                                $images = json_decode($item->images, true);
                                                                $imagePath =
                                                                    !empty($images) && isset($images[0])
                                                                        ? env('ImagePath') . 'storage/' . $images[0]
                                                                        : env('ImagePath') .
                                                                            'admin/assets/img/product/noimage.png';
                                                            @endphp
                                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                                <img src="{{ asset($imagePath) }}" alt="Product"
                                                                    style="width: 36px; height: 36px; object-fit: cover; border-radius: 6px; flex-shrink: 0; border: 1px solid #e5e7eb;">
                                                                <span class="dashboard-product-name"
                                                                    style="color: #374151; font-weight: 500;">{{ $item->product_name ?? 'N/A' }}</span>
                                                            </div>
                                                        </td>
                                                        <td
                                                            style="color: #111827; font-weight: 600; vertical-align: middle;">
                                                            @if ($currencyPosition === 'left')
                                                                {{ $currencySymbol }}
                                                            @endif
                                                            {{ number_format($item->grand_total ?? 0, 2) }}
                                                            @if ($currencyPosition === 'right')
                                                                {{ $currencySymbol }}
                                                            @endif
                                                        </td>
                                                        <td style="color: #6b7280; vertical-align: middle;">
                                                            {{ $item->purchase_date ? \Carbon\Carbon::parse($item->purchase_date)->format('d F Y') : 'N/A' }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted"
                                                            style="padding: 20px;">No purchase records found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (
                        $showErpDashboard &&
                            in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin']) &&
                            $showErpRecentRentals)
                        <div class="col-md-4">
                            <div class="card mb-4"
                                style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">

                                <div class="card-header d-flex justify-content-between align-items-center"
                                    style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">

                                    <h4 class="card-title mb-0" style="color:#fff;font-size:16px;font-weight:500;">
                                        Recent Rentals
                                    </h4>

                                    <a href="{{ route('sales.list') }}?type=rental" class="btn btn-sm"
                                        style="background:#fff;color:#e87b1e;border:none;border-radius:4px;font-weight:600;padding:4px 16px;">
                                        View All
                                    </a>
                                </div>

                                <div class="card-body p-0"
                                    style="border:1px solid #f0f0f0;border-top:none;border-radius:0 0 8px 8px;">

                                    <div class="table-responsive">

                                        <table class="table mb-0">

                                            <thead style="background:#f9fafb;">
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Product Name</th>
                                                    <th>Grand Total</th>
                                                    <th>Rental Date</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                @forelse($latestRentals as $rent)
                                                    <tr>
                                                        <td>
                                                            {{ $rent->order_number }}
                                                            <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">{{ $rent->customer_name ?? 'N/A' }}</div>
                                                        </td>

                                                        <td><span class="dashboard-product-name" style="color: #374151; font-weight: 500;">{{ $rent->product_name ?? '-' }}</span></td>

                                                        <td>
                                                            {{ $currencySymbol }}
                                                            {{ number_format($rent->total_amount, 2) }}
                                                        </td>

                                                        <td>
                                                            {{ \Carbon\Carbon::parse($rent->order_date)->format('d M Y') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">
                                                            No rental records found.
                                                        </td>
                                                    </tr>
                                                @endforelse

                                            </tbody>

                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if ($showErpSalesChartCard)
                <div class="{{ $erpMainSectionClass }} col-sm-12 col-12 d-flex">
                    <div class="card flex-fill"
                        style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <div class="card-header d-flex justify-content-between align-items-center"
                            style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                            <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">Sales
                            </h4>
                            <div class="chart-select-container">
                                <select class="form-control form-control-sm chart-select2" id="salesYearSelect"
                                    style="width: 130px;">
                                    <option value="month">This month ({{ date('F') }})</option>
                                    <option value="{{ $previousYear }}">Previous year ({{ $previousYear }})</option>
                                    <option value="{{ $currentYear }}" selected>This year ({{ $currentYear }})
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body chart-set"
                            style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px;">
                            <div class="h-250" style="width: 100%;" id="saleschart"></div>
                        </div>
                    </div>
                </div>
            @endif
            @if ($showErpPurchaseChartCard)
                <div class="{{ $erpMainSectionClass }} col-sm-12 col-12 d-flex">
                    <div class="card flex-fill"
                        style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <div class="card-header d-flex justify-content-between align-items-center"
                            style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                            <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">
                                Purchases</h4>
                            <div class="chart-select-container">
                                <select class="form-control form-control-sm chart-select2" id="purchaseYearSelect"
                                    style="width: 130px;">
                                    <option value="month">This month ({{ date('F') }})</option>
                                    <option value="{{ $previousYear }}">Previous year ({{ $previousYear }})</option>
                                    <option value="{{ $currentYear }}" selected>This year ({{ $currentYear }})
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body chart-set"
                            style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px;">
                            <div class="h-250" style="width: 100%;" id="purchasechart"></div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($showErpProductsDeliveryCard)
                <div class="{{ $erpMainSectionClass }} col-sm-12 col-12 d-flex">
                    <div class="card flex-fill"
                        style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <div class="card-header d-flex justify-content-between align-items-center"
                            style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                            <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">
                                Products Delivery</h4>
                            <a href="{{ route('sales.list') }}" class="btn btn-sm"
                                style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View
                                All</a>
                        </div>
                        <div class="card-body p-2"
                            style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px !important;">
                            <div class="table-responsive pending-delivery-scroll">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Order#</th>
                                            <th>Delivered By</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $deliveryStatuses = [
                                                'pending' => 'Pending',
                                                'delivered' => 'Delivered',
                                                'partially_delivered' => 'Partially Delivered',
                                                'cancelled' => 'Cancelled',
                                            ];
                                            $deliveryStatusMap = [
                                                'pending' => 'pending',
                                                'delivered' => 'delivered',
                                                'partial' => 'partially_delivered',
                                                'partially_delivered' => 'partially_delivered',
                                                'cancelled' => 'cancelled',
                                            ];
                                            $deliveries = $dashboardDeliveries ?? collect();
                                        @endphp

                                        @forelse ($deliveries as $delivery)
                                            <tr>
                                                <td>
                                                    <a
                                                        href="{{ route('sales.delivery', $delivery->order_id) }}">#{{ $delivery->order->order_number ?? $delivery->order_id }}</a>
                                                    <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">{{ $delivery->order->user->name ?? 'N/A' }}</div>
                                                </td>
                                                <td>{{ $delivery->deliveredBy->name ?? ($delivery->delivered_by ?? '--') }}
                                                </td>
                                                <td>
                                                    @php
                                                        $deliveryStatusKey = strtolower(
                                                            str_replace(
                                                                [' ', '-'],
                                                                '_',
                                                                trim((string) $delivery->status),
                                                            ),
                                                        );
                                                        $currentDeliveryStatus =
                                                            $deliveryStatusMap[$deliveryStatusKey] ?? 'pending';
                                                    @endphp
                                                    <select class="form-control form-control-sm pending-delivery-status"
                                                        @if ($delivery->has_delivery_record) data-url="{{ route('sales.delivery.status.update', $delivery->id) }}"
                                                        @else
                                                            data-url="{{ route('sales.order.delivery.status.update', $delivery->order_id) }}" @endif
                                                        data-current-status="{{ $currentDeliveryStatus }}">
                                                        @foreach ($deliveryStatuses as $statusValue => $statusLabel)
                                                            <option value="{{ $statusValue }}"
                                                                @selected($currentDeliveryStatus === $statusValue)>{{ $statusLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No deliveries found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif




            @if (
                $showCrmDashboardSection &&
                    in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']) &&
                    ($showCrmLeadPipeline ||
                        $showCrmConversion ||
                        $showCrmFollowupLoad ||
                        $showCrmMeetingMomentum ||
                        $showCrmLeadStatusMix ||
                        $showCrmActivityTrend ||
                        $showCrmPipelineQuality ||
                        $showCrmRecentLeads ||
                        $showCrmNext7Days))
                <div class="col-12 crm-section mt-0 pt-0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="mb-1">CRM Pipeline Overview</h4>
                            {{-- <p class="text-muted mb-0">Lead movement, customer conversion, and pending relationship work.</p> --}}
                        </div>
                        <a href="{{ route('lead.list') }}" class="btn btn-sm btn-primary">View Leads</a>
                    </div>
                </div>

                @if ($showCrmLeadPipeline)
                    <div class="col-lg-3 col-sm-6 col-6 d-flex crm-section">
                        <a href="{{ route('lead.list') }}" class="color_box w-100">
                            <div class="card flex-fill crm-metric" style="cursor:pointer;">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="crm-metric-label">Lead</div>
                                        <div class="crm-metric-value">{{ number_format($totalLeads, 0) }}</div>
                                        <p class="crm-metric-note">{{ number_format($newLeadsThisMonth, 0) }} new this
                                            month</p>
                                    </div>
                                    <div
                                        style="background-color: #fff3e8; border-radius: 8px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 12px;">
                                        <i data-feather="users"
                                            style="width: 20px; height: 20px; color: #e87b1e; stroke-width: 2;"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                @if ($showCrmConversion)
                    <div class="col-lg-3 col-sm-6 col-6 d-flex crm-section">
                        <a href="{{ route('lead.list') }}" class="color_box w-100">
                            <div class="card flex-fill crm-metric" style="cursor:pointer;">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="crm-metric-label">Conversion</div>
                                        <div class="crm-metric-value">{{ $crmConversionRate }}%</div>
                                        <p class="crm-metric-note">{{ number_format($convertedLeadCount, 0) }} leads
                                            became customers</p>
                                    </div>
                                    <div
                                        style="background-color: #e8f8f0; border-radius: 8px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 12px;">
                                        <i data-feather="trending-up"
                                            style="width: 20px; height: 20px; color: #10b981; stroke-width: 2;"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                @if ($showCrmFollowupLoad)
                    <div class="col-lg-3 col-sm-6 col-6 d-flex crm-section">
                        <a href="{{ route('followup.list') }}" class="color_box w-100">
                            <div class="card flex-fill crm-metric" style="cursor:pointer;">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="crm-metric-label">Follow-up</div>
                                        <div class="crm-metric-value">{{ number_format($pendingFollowUps, 0) }}</div>
                                        <p class="crm-metric-note">{{ number_format($overdueFollowUps, 0) }} overdue and
                                            need action</p>
                                    </div>
                                    <div
                                        style="background-color: #eef2ff; border-radius: 8px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 12px;">
                                        <i data-feather="phone-call"
                                            style="width: 20px; height: 20px; color: #6366f1; stroke-width: 2;"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                @if ($showCrmMeetingMomentum)
                    <div class="col-lg-3 col-sm-6 col-6 d-flex crm-section">
                        <a href="{{ route('meeting.list') }}" class="color_box w-100">
                            <div class="card flex-fill crm-metric" style="cursor:pointer;">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="crm-metric-label">Meeting</div>
                                        <div class="crm-metric-value">{{ number_format($meetingsThisWeek, 0) }}</div>
                                        <p class="crm-metric-note">{{ number_format($completedMeetingsThisMonth, 0) }}
                                            completed this month</p>
                                    </div>
                                    <div
                                        style="background-color: #fef3c7; border-radius: 8px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 12px;">
                                        <i data-feather="calendar"
                                            style="width: 20px; height: 20px; color: #f59e0b; stroke-width: 2;"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif


                @if ($showCrmPipelineQuality)
                    <div class="col-lg-4 col-sm-12 col-12 d-flex crm-section">
                        <div class="card flex-fill"
                            style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <div class="card-header d-flex justify-content-between align-items-center"
                                style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                                <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">
                                    Pipeline Quality</h4>
                                <a href="{{ route('lead.list') }}" class="btn btn-sm"
                                    style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View
                                    All</a>
                            </div>
                            <div class="card-body"
                                style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px;">
                                <div class="table-responsive dataview custom-table-scroll crm-pipeline-quality-table">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Status</th>
                                                <th class="details-column">Details</th>
                                                <th>Leads</th>
                                                <th>Share</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($leadStatusTable as $index => $statusRow)
                                                <tr>
                                                    <td>
                                                        <div>
                                                            {{ $statusRow['status'] }}
                                                            <div class="collapse mobile-details-collapse d-md-none"
                                                                id="pipeline-details-{{ $index }}">
                                                                <div class="mobile-details-row">
                                                                    <span class="mobile-details-label">Leads:</span>
                                                                    <span
                                                                        class="mobile-details-value">{{ number_format($statusRow['total'], 0) }}</span>
                                                                </div>
                                                                <div class="mobile-details-row">
                                                                    <span class="mobile-details-label">Share:</span>
                                                                    <span
                                                                        class="mobile-details-value">{{ $statusRow['share'] }}%</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="details-column">
                                                        <a href="#pipeline-details-{{ $index }}"
                                                            class="toggle-details" data-bs-toggle="collapse">
                                                            <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                        </a>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        {{ number_format($statusRow['total'], 0) }}</td>
                                                    <td class="d-none d-md-table-cell">{{ $statusRow['share'] }}%</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No lead data found
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($showCrmRecentLeads)
                    <div class="col-lg-4 col-sm-12 col-12 d-flex crm-section">
                        <div class="card flex-fill"
                            style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <div class="card-header d-flex justify-content-between align-items-center"
                                style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                                <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">
                                    Recent Leads</h4>
                                <a href="{{ route('lead.list') }}" class="btn btn-sm"
                                    style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View
                                    All</a>
                            </div>
                            <div class="card-body"
                                style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px;">
                                <div class="table-responsive dataview custom-table-scroll crm-recent-leads-table">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Lead</th>
                                                <th class="details-column">Details</th>
                                                <th>Status</th>
                                                <th>Owner</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($latestLeads as $index => $lead)
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <a
                                                                href="{{ route('lead.view', $lead->id) }}">{{ $lead->name }}</a>
                                                            <div class="text-muted small">
                                                                {{ $lead->company_name ?? $lead->phone }}</div>
                                                            <div class="collapse mobile-details-collapse d-md-none"
                                                                id="lead-details-{{ $lead->id ?? $index }}">
                                                                <div class="mobile-details-row">
                                                                    <span class="mobile-details-label">Status:</span>
                                                                    <span class="mobile-details-value">
                                                                        <span
                                                                            class="crm-mini-badge bg-light text-dark">{{ $lead->lead_status }}</span>
                                                                    </span>
                                                                </div>
                                                                <div class="mobile-details-row">
                                                                    <span class="mobile-details-label">Owner:</span>
                                                                    <span
                                                                        class="mobile-details-value">{{ $lead->assignedUser->name ?? 'Unassigned' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="details-column">
                                                        <a href="#lead-details-{{ $lead->id ?? $index }}"
                                                            class="toggle-details" data-bs-toggle="collapse">
                                                            <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                        </a>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        <span
                                                            class="crm-mini-badge bg-light text-dark">{{ $lead->lead_status }}</span>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        {{ $lead->assignedUser->name ?? 'Unassigned' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No recent leads
                                                        found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($showCrmNext7Days)
                    <div class="col-lg-4 col-sm-12 col-12 d-flex crm-section">
                        <div class="card flex-fill"
                            style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <div class="card-header d-flex justify-content-between align-items-center"
                                style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                                <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">
                                    Next 7 Days</h4>
                                <a href="{{ route('followup.list') }}" class="btn btn-sm"
                                    style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View
                                    All</a>
                            </div>
                            <div class="card-body"
                                style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px;">
                                <div class="table-responsive dataview custom-table-scroll crm-next-seven-days-table">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Work</th>
                                                <th class="details-column">Details</th>
                                                <th>Party</th>
                                                <th>Due</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($upcomingCrmActions as $index => $action)
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <span
                                                                class="crm-mini-badge bg-light text-dark">{{ $action['type'] }}</span>
                                                            <div class="mt-1">{{ $action['title'] }}</div>
                                                            <div class="collapse mobile-details-collapse d-md-none"
                                                                id="crm-action-details-{{ $index }}">
                                                                <div class="mobile-details-row">
                                                                    <span class="mobile-details-label">Party:</span>
                                                                    <span class="mobile-details-value">
                                                                        {{ $action['party'] }}
                                                                        <div class="text-muted small">
                                                                            {{ $action['owner'] }}</div>
                                                                    </span>
                                                                </div>
                                                                <div class="mobile-details-row">
                                                                    <span class="mobile-details-label">Due:</span>
                                                                    <span
                                                                        class="mobile-details-value">{{ $action['date'] ? $action['date']->format('d M, h:i A') : 'N/A' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="details-column">
                                                        <a href="#crm-action-details-{{ $index }}"
                                                            class="toggle-details" data-bs-toggle="collapse">
                                                            <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                                        </a>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        {{ $action['party'] }}
                                                        <div class="text-muted small">{{ $action['owner'] }}</div>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        {{ $action['date'] ? $action['date']->format('d M, h:i A') : 'N/A' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No upcoming CRM work
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                @if ($showCrmLeadStatusMix)
                    <div class="col-lg-5 col-sm-12 col-12 d-flex crm-section">
                        <div class="card flex-fill"
                            style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <div class="card-header d-flex justify-content-between align-items-center"
                                style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                                <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">
                                    Lead Status Mix</h4>
                                <a href="{{ route('lead.list') }}" class="btn btn-sm"
                                    style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View
                                    All</a>
                            </div>
                            <div class="card-body"
                                style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px;">
                                <div id="crmPipelineChart" class="crm-chart"></div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($showCrmActivityTrend)
                    <div class="col-lg-7 col-sm-12 col-12 d-flex crm-section">
                        <div class="card flex-fill"
                            style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            <div class="card-header d-flex justify-content-between align-items-center"
                                style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                                <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">CRM
                                    Activity Trend</h4>
                                <a href="{{ route('lead.list') }}" class="btn btn-sm"
                                    style="background-color: #ffffff; color: #e87b1e; border-radius: 4px; font-weight: 600; padding: 4px 16px; border: none;">View
                                    All</a>
                            </div>
                            <div class="card-body"
                                style="border: 1px solid #f0f0f0; border-top: none; border-radius: 0 0 8px 8px; padding-top: 15px;">
                                <div id="crmActivityChart" class="crm-chart"></div>
                            </div>
                        </div>
                    </div>
                @endif

            @endif

        </div>
    </div>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Toggle details icon animation
            $(document).on('click', '.toggle-details', function() {
                let icon = $(this).find('i');
                if (icon.hasClass('fa-plus-circle')) {
                    icon.removeClass('fa-plus-circle')
                        .addClass('fa-minus-circle')
                        .css('color', 'red');
                } else {
                    icon.removeClass('fa-minus-circle')
                        .addClass('fa-plus-circle')
                        .css('color', '#ff9f43');
                }
            });
        });
    </script>
    <script>
        const currentYear = {{ $currentYear }};
        const previousYear = {{ $previousYear }};
        const salesThisMonth = @json($salesChartThisMonth);
        const purchaseThisMonth = @json($purchaseChartThisMonth);

        const salesChartData = {
            [currentYear]: @json($salesChartthisyear),
            [previousYear]: @json($salesChartpreviousyear),
            'thisMonth': salesThisMonth
        };

        const purchaseChartData = {
            [currentYear]: @json($purchaseChartthisyear),
            [previousYear]: @json($purchaseChartpreviousyear),
            'thisMonth': purchaseThisMonth
        };

        const leadStatusLabels = @json($leadStatusLabels ?? []);
        const leadStatusCounts = @json($leadStatusCounts ?? []);
        const crmMonthlyLabels = @json($crmMonthlyLabels ?? []);
        const crmMonthlyLeads = @json($crmMonthlyLeads ?? []);
        const crmMonthlyFollowUps = @json($crmMonthlyFollowUps ?? []);
        const crmMonthlyMeetings = @json($crmMonthlyMeetings ?? []);
        const hrAttendanceLabels = @json($hrAttendanceLabels ?? []);
        const hrAttendancePresent = @json($hrAttendancePresent ?? []);
        const hrAttendanceAbsent = @json($hrAttendanceAbsent ?? []);
        const hrAttendanceHalfDay = @json($hrAttendanceHalfDay ?? []);
        const hrSalaryLabels = @json($hrSalaryLabels ?? []);
        const hrSalaryPaidAmounts = @json($hrSalaryPaidAmounts ?? []);
        const hrSalaryPaidCounts = @json($hrSalaryPaidCounts ?? []);

        // ✅ Updated chart options
        const options = {
            grid: {
                borderWidth: 1,
                borderColor: 'rgba(67, 87, 133, .09)',
                hoverable: true
            },
            xaxis: {
                ticks: [], // dynamically set below
                tickColor: 'rgba(67, 87, 133, .09)',
                font: {
                    color: '#8e9cad',
                    size: 9
                },
                autoscaleMargin: 0.02
            },
            yaxis: {
                tickColor: 'rgba(67, 87, 133, .09)',
                font: {
                    color: '#8e9cad',
                    size: 10
                },
                tickFormatter: function(val, axis) {
                    return val.toLocaleString();
                }
            },
            legend: {
                show: true,
                position: "nw"
            },
            tooltip: true,
            tooltipOpts: {
                content: function(label, xval, yval, flotItem) {
                    return label + ": " + yval.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },
                shifts: {
                    x: 10,
                    y: 20
                },
                defaultTheme: false
            }
        };

        // ✅ Fixed: Tick generator for day labels 1–31
        function getDayTicks(length) {
            return Array.from({
                length
            }, (_, i) => [i, (i + 1).toString()]);
        }

        // Optional: Only show every 2nd or 5th tick (use if labels are still crowded)
        // function getDayTicks(length) {
        //     return Array.from({ length }, (_, i) => [i, (i + 1).toString()])
        //         .filter(([, label]) => parseInt(label) % 2 === 1); // odd days only
        // }

        function renderChart(containerId, label, data, barColor, isMonthly = true) {
            const series = data.map((val, idx) => [idx, val]);

            options.xaxis.ticks = isMonthly ? [
                    [0, 'Jan'],
                    [1, 'Feb'],
                    [2, 'Mar'],
                    [3, 'Apr'],
                    [4, 'May'],
                    [5, 'Jun'],
                    [6, 'Jul'],
                    [7, 'Aug'],
                    [8, 'Sep'],
                    [9, 'Oct'],
                    [10, 'Nov'],
                    [11, 'Dec']
                ] :
                getDayTicks(data.length); // ✅ this ensures 1–31 labels

            $.plot(containerId, [{
                label,
                data: series,
                lines: {
                    show: true,
                    fill: true,
                    fillColor: {
                        colors: [{
                            opacity: 0.1
                        }, {
                            opacity: 0.4
                        }]
                    },
                    lineWidth: 2
                },
                points: {
                    show: true,
                    radius: 4,
                    fill: true,
                    fillColor: "#fff",
                    lineWidth: 2
                },
                color: barColor
            }], options);
        }

        // $(document).ready(function () {
        //     renderChart('#saleschart', 'Sales', salesChartData[currentYear], '#44c4fa');
        //     renderChart('#purchasechart', 'Purchases', purchaseChartData[currentYear], '#fa6c7c');

        //     $('#salesYearSelect').on('change', function () {
        //         const year = $(this).val();
        //         if (year === 'month') {
        //             renderChart('#saleschart', 'Sales (This Month)', salesChartData['thisMonth'], '#44c4fa', false);
        //         } else {
        //             renderChart('#saleschart', 'Sales', salesChartData[year], '#44c4fa');
        //         }
        //     });

        //     $('#purchaseYearSelect').on('change', function () {
        //         const year = $(this).val();
        //         if (year === 'month') {
        //             renderChart('#purchasechart', 'Purchases (This Month)', purchaseChartData['thisMonth'], '#fa6c7c', false);
        //         } else {
        //             renderChart('#purchasechart', 'Purchases', purchaseChartData[year], '#fa6c7c');
        //         }
        //     });
        // });

        $(document).ready(function() {
            $('.chart-select2').select2({
                minimumResultsForSearch: -1,
                width: '100%'
            });

            const $misplacedHrSections = $('.dashboard-widgets-row > .hr-section');
            if ($misplacedHrSections.length) {
                const $lastCrmSection = $('.crm-section').last();
                const $latestSalesSection = $('.card-title')
                    .filter(function() {
                        return $(this).text().trim() === 'Latest Sales';
                    })
                    .closest('.col-md-6');

                if ($lastCrmSection.length) {
                    $misplacedHrSections.insertAfter($lastCrmSection);
                } else if ($latestSalesSection.length) {
                    $misplacedHrSections.insertBefore($latestSalesSection);
                }
            }

            if ($('.crm-section').length && !$('.crm-zone').length) {
                $('.crm-section').wrapAll(
                    '<div class="col-12 pulse-zone crm-zone"><div class="row pulse-crm"></div></div>');
            }

            if ($('.hr-section').not('.dashboard-widgets-row > .hr-section').length && !$('.hr-zone').length) {
                $('.hr-section').not('.dashboard-widgets-row > .hr-section')
                    .wrapAll('<div class="col-12 pulse-zone hr-zone"><div class="row pulse-hr"></div></div>');
            }

            @if ($showCrmDashboardSection && in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']))
                if (document.querySelector('#crmPipelineChart')) {
                    new ApexCharts(document.querySelector('#crmPipelineChart'), {
                        chart: {
                            type: 'donut',
                            height: 260,
                            toolbar: {
                                show: false
                            }
                        },
                        labels: leadStatusLabels,
                        series: leadStatusCounts,
                        colors: ['#4361ee', '#10b981', '#ff9f43', '#ef4444', '#6b7280', '#06b6d4'],
                        legend: {
                            position: 'bottom',
                            fontSize: '12px'
                        },
                        dataLabels: {
                            enabled: false
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '68%'
                                }
                            }
                        },
                        noData: {
                            text: 'No lead data'
                        }
                    }).render();
                }

                if (document.querySelector('#crmActivityChart')) {
                    new ApexCharts(document.querySelector('#crmActivityChart'), {
                        chart: {
                            type: 'line',
                            height: 260,
                            toolbar: {
                                show: false
                            },
                            zoom: {
                                enabled: false
                            }
                        },
                        series: [{
                            name: 'Leads',
                            data: crmMonthlyLeads
                        }, {
                            name: 'Follow-ups',
                            data: crmMonthlyFollowUps
                        }, {
                            name: 'Meetings',
                            data: crmMonthlyMeetings
                        }],
                        colors: ['#4361ee', '#ff9f43', '#10b981'],
                        stroke: {
                            width: 3,
                            curve: 'smooth'
                        },
                        markers: {
                            size: 4
                        },
                        xaxis: {
                            categories: crmMonthlyLabels,
                            labels: {
                                style: {
                                    fontSize: '12px'
                                }
                            }
                        },
                        yaxis: {
                            min: 0,
                            forceNiceScale: true,
                            labels: {
                                formatter: function(value) {
                                    return Math.round(value);
                                }
                            }
                        },
                        grid: {
                            borderColor: '#edf0f5'
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right'
                        },
                        tooltip: {
                            y: {
                                formatter: function(value) {
                                    return Math.round(value);
                                }
                            }
                        },
                        noData: {
                            text: 'No CRM activity'
                        }
                    }).render();
                }
            @endif

            @if ($showHrDashboardSection && in_array($role, ['inventory-manager', 'admin', 'sub-admin']))
                if (document.querySelector('#hrAttendanceChart')) {
                    new ApexCharts(document.querySelector('#hrAttendanceChart'), {
                        chart: {
                            type: 'bar',
                            height: 260,
                            stacked: true,
                            toolbar: {
                                show: false
                            }
                        },
                        series: [{
                            name: 'Present',
                            data: hrAttendancePresent
                        }, {
                            name: 'Half-day',
                            data: hrAttendanceHalfDay
                        }, {
                            name: 'Absent',
                            data: hrAttendanceAbsent
                        }],
                        colors: ['#10b981', '#ff9f43', '#ef4444'],
                        plotOptions: {
                            bar: {
                                columnWidth: '42%',
                                borderRadius: 3
                            }
                        },
                        xaxis: {
                            categories: hrAttendanceLabels
                        },
                        yaxis: {
                            min: 0,
                            forceNiceScale: true,
                            labels: {
                                formatter: function(value) {
                                    return Math.round(value);
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right'
                        },
                        grid: {
                            borderColor: '#edf0f5'
                        },
                        noData: {
                            text: 'No attendance data'
                        }
                    }).render();
                }

                if (document.querySelector('#hrSalaryChart')) {
                    new ApexCharts(document.querySelector('#hrSalaryChart'), {
                        chart: {
                            height: 260,
                            type: 'line',
                            toolbar: {
                                show: false
                            },
                            zoom: {
                                enabled: false
                            }
                        },
                        series: [{
                            name: 'Payout',
                            type: 'column',
                            data: hrSalaryPaidAmounts
                        }, {
                            name: 'Paid Staff',
                            type: 'line',
                            data: hrSalaryPaidCounts
                        }],
                        colors: ['#4361ee', '#10b981'],
                        stroke: {
                            width: [0, 3],
                            curve: 'smooth'
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '45%',
                                borderRadius: 3
                            }
                        },
                        xaxis: {
                            categories: hrSalaryLabels
                        },
                        yaxis: [{
                            min: 0,
                            labels: {
                                formatter: function(value) {
                                    return Math.round(value).toLocaleString();
                                }
                            }
                        }, {
                            opposite: true,
                            min: 0,
                            forceNiceScale: true,
                            labels: {
                                formatter: function(value) {
                                    return Math.round(value);
                                }
                            }
                        }],
                        grid: {
                            borderColor: '#edf0f5'
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right'
                        },
                        noData: {
                            text: 'No salary data'
                        }
                    }).render();
                }
            @endif

            @if (in_array($role, ['inventory-manager', 'sales-manager', 'admin', 'sub-admin']))
                renderChart('#saleschart', 'Sales', salesChartData[currentYear], '#44c4fa');
                $('#salesYearSelect').on('change', function() {
                    const year = $(this).val();
                    if (year === 'month') {
                        renderChart('#saleschart', 'Sales (This Month)', salesChartData['thisMonth'],
                            '#44c4fa', false);
                    } else {
                        renderChart('#saleschart', 'Sales', salesChartData[year], '#44c4fa');
                    }
                });
            @endif

            @if (in_array($role, ['inventory-manager', 'purchase-manager', 'admin', 'sub-admin']))
                renderChart('#purchasechart', 'Purchases', purchaseChartData[currentYear], '#fa6c7c');
                $('#purchaseYearSelect').on('change', function() {
                    const year = $(this).val();
                    if (year === 'month') {
                        renderChart('#purchasechart', 'Purchases (This Month)', purchaseChartData[
                            'thisMonth'], '#fa6c7c', false);
                    } else {
                        renderChart('#purchasechart', 'Purchases', purchaseChartData[year], '#fa6c7c');
                    }
                });
            @endif

        });
    </script>
    <script>
        $(document).on('change', '.pending-delivery-status', function() {
            const $select = $(this);
            const previousStatus = $select.data('current-status');
            const newStatus = $select.val();

            $select.addClass('is-saving').prop('disabled', true);

            $.ajax({
                url: $select.data('url'),
                method: 'POST',
                data: {
                    status: newStatus
                },
                success: function(response) {
                    if (response.status) {
                        $select.data('current-status', newStatus);
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated',
                            text: response.message || 'Delivery status updated successfully.',
                            timer: 1400,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        $select.val(previousStatus);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Unable to update delivery status.'
                        });
                    }
                },
                error: function(xhr) {
                    $select.val(previousStatus);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Unable to update delivery status.'
                    });
                },
                complete: function() {
                    $select.removeClass('is-saving').prop('disabled', false);
                }
            });
        });

        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            let url = "{{ url('/api/dashboard-api') }}";

            if (selectedSubAdminId) {
                url += `?selectedSubAdminId=${selectedSubAdminId}`;
            }
            $.ajax({
                url: url,
                method: "GET",
                headers: {
                    "Authorization": "Bearer " + authToken,
                },
                dataType: "json",
                success: function(response) {
                    // console.log("Branch:", response.branch_id);
                    if (response.status) {
                        // console.log(response.data);

                        // Example: Update totals
                        $('#totalPurchase').text(parseFloat(response.data.totals.purchase)
                            .toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                        $('#totalSales').text(parseFloat(response.data.totals.sales).toLocaleString(
                            undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                        $('#totalExpense').text(parseFloat(response.data.totals.expense).toLocaleString(
                            undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                    }
                },
                error: function(xhr) {
                    // console.error(xhr.responseText);
                }
            });
            
            // Check for pending price updates and show modal
            const selectedSubAdminIdForUpdates = localStorage.getItem('selectedSubAdminId');
            let pricingUpdatesUrl = "{{ route('api.pricing-updates.data') }}";
            if (selectedSubAdminIdForUpdates) {
                pricingUpdatesUrl += "?selectedSubAdminId=" + selectedSubAdminIdForUpdates;
            }
            
            $.ajax({
                url: pricingUpdatesUrl,
                type: "GET",
                headers: {
                    "Authorization": "Bearer " + authToken
                },
                success: function(response) {
                    if(response.status && response.pending && response.pending.length > 0) {
                        let totalPending = response.pending.length;
                        let title = totalPending > 1 ? `Pending Price Updates (${totalPending})` : 'Price Update Request';
                        
                        let htmlContent = `<div style="max-height: 50vh; overflow-y: auto; overflow-x: hidden; padding-right: 5px;">`;
                        
                        response.pending.forEach((item) => {
                            let productName = item.target_product ? item.target_product.name : 'Unknown Product';
                            htmlContent += `
                                <div class="price-update-item border rounded p-3 mb-3 text-start position-relative shadow-sm" id="price-update-${item.id}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold mb-0 text-primary" style="font-size: 15px;">${productName}</h6>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-success px-3 btn-approve-price" data-id="${item.id}">Approve</button>
                                            <button class="btn btn-sm btn-danger px-3 btn-reject-price" data-id="${item.id}">Reject</button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm text-center mb-2" style="font-size: 13px;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Purchase</th>
                                                    <th>MRP</th>
                                                    <th>Selling</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold text-muted">Old</td>
                                                    <td>₹${item.old_purchase_price ?? '-'}</td>
                                                    <td>₹${item.old_mrp ?? '-'}</td>
                                                    <td>₹${item.old_price ?? '-'}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold text-success">New</td>
                                                    <td class="text-success fw-bold">₹${item.new_purchase_price ?? '-'}</td>
                                                    <td class="text-success fw-bold">₹${item.new_mrp ?? '-'}</td>
                                                    <td class="text-success fw-bold">₹${item.new_price ?? '-'}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            `;
                        });
                        
                        htmlContent += `</div>`;
                        
                        Swal.fire({
                            title: title,
                            html: htmlContent,
                            width: '600px',
                            showConfirmButton: false,
                            showCancelButton: false,
                            showCloseButton: true,
                            didOpen: () => {
                                const container = Swal.getHtmlContainer();
                                
                                $(container).on('click', '.btn-approve-price', function() {
                                    let btn = $(this);
                                    let id = btn.data('id');
                                    let card = $(`#price-update-${id}`);
                                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                                    card.find('.btn-reject-price').prop('disabled', true);
                                    
                                    $.post(`/pricing-updates/${id}/confirm`, { _token: "{{ csrf_token() }}" }, function(res) {
                                        if(res.status) {
                                            Swal.fire('Approved!', 'Price update confirmed.', 'success').then(() => location.reload());
                                        } else {
                                            Swal.fire('Error!', res.message, 'error');
                                            btn.prop('disabled', false).text('Approve');
                                            card.find('.btn-reject-price').prop('disabled', false);
                                        }
                                    });
                                });

                                $(container).on('click', '.btn-reject-price', function() {
                                    let btn = $(this);
                                    let id = btn.data('id');
                                    let card = $(`#price-update-${id}`);
                                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                                    card.find('.btn-approve-price').prop('disabled', true);
                                    
                                    $.post(`/pricing-updates/${id}/reject`, { _token: "{{ csrf_token() }}" }, function(res) {
                                        if(res.status) {
                                            Swal.fire('Rejected!', 'Price update rejected.', 'success').then(() => location.reload());
                                        } else {
                                            Swal.fire('Error!', res.message, 'error');
                                            btn.prop('disabled', false).text('Reject');
                                            card.find('.btn-approve-price').prop('disabled', false);
                                        }
                                    });
                                });
                            }
                        });
                    }
                }
            });
        });
        // });
    </script>
@endpush

@include('partials.plan-expiry-alert')
