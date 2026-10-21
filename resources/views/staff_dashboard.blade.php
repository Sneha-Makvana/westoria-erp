@extends('layout.app')

@section('title', 'Staff Dashboard')

@section('content')
@php
    $canViewFollowUps = app('hasPermission')(30, 'view') || app('hasPermission')(30, 'add');
    $canViewMeetings = app('hasPermission')(31, 'view') || app('hasPermission')(31, 'add');
@endphp
<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.25; }
    }

    .dash-widget{
        margin-bottom:0px !important;
    }
    .staff-stat-card {
        border-radius: 16px;
        padding: 24px 20px;
        background-color: #ffffff;
        color: #1b2850;
        position: relative;
        overflow: hidden;
        min-height: 110px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        border: 1px solid #e9edf5;
        margin-bottom: 20px;
    }

    .staff-stat-card .stat-number {
        font-size: 2.2rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 6px;
    }

    .staff-stat-card .stat-label {
        font-size: 1rem;
        font-weight: 600;
        opacity: .95;
        margin-bottom: 2px;
    }

    .staff-stat-card .stat-sub {
        font-size: .78rem;
        color: #64748b;
    }

    .staff-stat-card .stat-icon {
        position: absolute;
        right: 18px;
        bottom: 16px;
        font-size: 1.8rem;
        opacity: 0.15;
    }

    .card-attendance .stat-icon {
        color: #ff6b35;
    }

    .card-leave .stat-icon {
        color: #e74c3c;
    }

    .card-followup .stat-icon {
        color: #3498db;
    }

    .card-meeting .stat-icon {
        color: #2ecc71;
    }

    .staff-panel {
        border: 1px solid #e9edf5;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        height: 100%;
    }

    .staff-panel .card-body {
        padding: 18px;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1b2850;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid #edf1f7;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .staff-table-wrap {
        overflow-x: auto;
    }

    .staff-delivery-scroll {
        max-height: 285px;
        overflow-y: auto;
        overflow-x: auto;
        padding-right: 4px;
    }

    .staff-delivery-scroll .staff-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .staff-delivery-scroll .staff-table td {
        height: 47px;
    }

    .staff-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 620px;
    }

    .staff-table thead th {
        background: #fff;
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
        padding: 12px 10px;
        border: 0;
        border-bottom: 1px solid #e9edf5;
    }

    .staff-table td {
        font-size: 13px;
        color: #475569;
        vertical-align: middle;
        padding: 14px 10px;
        border: 0;
        border-bottom: 1px solid #eef2f7;
        white-space: nowrap;
    }

    .staff-table .wrap-cell {
        white-space: normal !important;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .staff-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .staff-table tbody tr:hover {
        background: #fafcff;
    }

    .staff-empty-state {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        color: #64748b;
        text-align: center;
    }

    .badge-pending {
        background: #fff3cd;
        color: #856404;
    }

    .badge-completed {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-rescheduled,
    .badge-scheduled {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-high {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-medium {
        background: #fff3cd;
        color: #856404;
    }

    .badge-low {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge,
    .priority-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .mobile-toggle-btn,
    .mobile-detail-row {
        display: none;
    }

    .today-box {
        border: 1px solid #e9edf5;
        border-radius: 14px;
        background: #fff;
        padding: 16px;
        height: 100%;
    }

    .today-box-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1b2850;
        margin-bottom: 14px;
    }

    .today-check-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px;
        background: #f8fafc;
    }

    .check-pill {
        display: inline-block;
        border-radius: 4px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
    }

    .check-pill.in {
        background: #16a34a;
    }

    .check-pill.out {
        background: #0ea5e9;
    }

    .hours-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
        color: #334155;
    }

    .hours-time {
        font-size: 12px;
        font-weight: 700;
        color: #fff;
        border-radius: 4px;
        padding: 4px 8px;
    }

    .hours-time.worked {
        background: #0891b2;
    }

    .hours-time.remaining {
        background: #ef4444;
    }

    .hours-bar {
        height: 8px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
        margin-bottom: 14px;
    }

    .hours-bar > span {
        display: block;
        height: 100%;
    }

    .hours-bar .worked-fill {
        background: #06b6d4;
    }

    .hours-bar .remaining-fill {
        background: #f87171;
    }

    .status-chip {
        display: inline-block;
        background: #16a34a;
        color: #fff;
        border-radius: 4px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 700;
    }

    @media (max-width: 575px) {
        .staff-stat-card .stat-number {
            font-size: 1.7rem;
        }

        .staff-stat-card {
            min-height: 95px;
            padding: 18px 16px;
        }

        .staff-panel .card-body {
            padding: 14px;
        }

        .staff-table {
            min-width: 100%;
        }

        .staff-table thead th:not(:first-child):not(:last-child),
        .staff-row-summary td:not(:first-child):not(:last-child) {
            display: none;
        }

        .staff-table thead th:first-child {
            width: 44px;
            padding: 12px 8px;
        }

        .staff-table thead th:last-child,
        .staff-row-summary td:last-child {
            display: table-cell;
            width: 52px;
            text-align: right;
            padding: 10px 8px;
        }

        .staff-row-summary td {
            padding: 12px 8px;
            white-space: normal;
        }

        .staff-row-summary td:first-child {
            width: 44px;
            color: #0f172a;
            font-weight: 600;
        }

        .mobile-toggle-btn {
            width: 28px;
            height: 28px;
            border: 0;
            border-radius: 50%;
            background: #ff9f43;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            line-height: 1;
            padding: 0;
            box-shadow: 0 6px 12px rgba(239, 68, 68, 0.18);
        }

        .mobile-toggle-btn .toggle-minus {
            display: none;
        }

        .mobile-toggle-btn[aria-expanded="true"] .toggle-plus {
            display: none;
        }

        .mobile-toggle-btn[aria-expanded="true"] .toggle-minus {
            display: inline;
        }

        .mobile-detail-row.is-open {
            display: table-row;
        }

        .mobile-detail-row td {
            padding: 0 0 12px;
            border-bottom: 1px solid #eef2f7;
        }

        .mobile-detail-item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 12px;
            border-bottom: 1px solid #eef2f7;
            font-size: 12px;
        }

        .mobile-detail-item:last-child {
            border-bottom: 0;
        }

        .mobile-detail-label {
            color: #0f172a;
            font-weight: 600;
        }

        .mobile-detail-value {
            color: #475569;
            text-align: right;
            word-break: break-word;
        }
    }

    /* Wrap product names in tables */
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
        white-space: normal !important;
        word-break: break-word !important;
        overflow-wrap: anywhere !important;
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
</style>


<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Welcome, {{ ucwords($user->name) }} 👋</h4>
        </div>
    </div>

    <div class="row dashboard-widgets-row">
        @if ($canViewSales && $showErpTotalSales)
            <div class="col-lg-4 col-sm-6 col-12 mb-3">
                <a href="{{ route('sales.list') }}">
                    <div class="dash-widget dash1">
                        <div class="dash-widgetimg">
                            <span><img src="{{ env('ImagePath') . 'admin/assets/img/icons/dash2.svg' }}" alt="img"></span>
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
        @if ($canViewPurchases && $showErpTotalPurchase)
            <div class="col-lg-4 col-sm-6 col-12 mb-3">
                <a href="{{ route('purchase.lists') }}">
                    <div class="dash-widget">
                        <div class="dash-widgetimg">
                            <span><img src="{{ env('ImagePath') . 'admin/assets/img/icons/dash1.svg' }}" alt="img"></span>
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
        @if ($canViewExpenses && $showErpTotalExpense)
            <div class="col-lg-4 col-sm-6 col-12 mb-3">
                <a href="{{ route('expense.list') }}">
                    <div class="dash-widget dash2">
                        <div class="dash-widgetimg">
                            <span><img src="{{ env('ImagePath') . 'admin/assets/img/icons/dash3.svg' }}" alt="img"></span>
                        </div>
                        <div class="dash-widgetcontent">
                            <h5>
                                @if ($currencyPosition === 'left')
                                    {{ $currencySymbol }}
                                @endif
                                <span class="counters" data-count="{{ $totalExpenseAmount }}">
                                    {{ number_format($totalExpenseAmount, 2) }}</span>
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
        </div>

        <div class="row">

            @if ($canViewSales && $showErpRecentSales)
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
                                        <span style="font-weight: 700; font-size: 13px; color: #111827;">Completed</span>
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
                                        <span style="font-weight: 700; font-size: 13px; color: #111827;">Partially
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
                                        <span style="font-weight: 700; font-size: 13px; color: #111827;">Pending</span>
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
                                    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background-color: #ffffff; cursor: pointer; transition: border-color 0.2s ease, box-shadow 0.2s ease;"
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
                            <div
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
            @if ($canViewPurchases && $showErpRecentPurchases)
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
                                        <span style="font-weight: 700; font-size: 13px; color: #111827;">Completed</span>
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
                                        <span style="font-weight: 700; font-size: 13px; color: #111827;">Partially
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
                                        <span style="font-weight: 700; font-size: 13px; color: #111827;">Pending</span>
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
                                    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background-color: #ffffff; cursor: pointer; transition: border-color 0.2s ease, box-shadow 0.2s ease;"
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
                            <div
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
        </div>

        <div class="row mt-4 mb-4">
            @if ($canViewSales && $showErpRecentSales)
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
                                                        <span
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

            @if ($canViewPurchases && $showErpRecentPurchases)
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
                                                        <span
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

            @if ($canViewSales && $showErpRecentRentals)
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
                                                <td>{{ $rent->product_name ?? '-' }}</td>
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

            @if ($canViewSales && $showErpSalesChartCard)
                <div class="col-md-4 col-12 d-flex">
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

            @if ($canViewPurchases && $showErpPurchaseChartCard)
                <div class="col-md-4 col-12 d-flex">
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

            @if (!empty($showErpProductsDelivery) && app('hasPermission')(39, 'view'))
                <div class="col-md-4 col-sm-12 col-12 d-flex">
                    <div class="card flex-fill"
                        style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <div class="card-header d-flex justify-content-between align-items-center"
                            style="background-color: #e87b1e; padding: 12px 16px; border-bottom: none;">
                            <h4 class="card-title mb-0" style="color: #ffffff; font-size: 16px; font-weight: 500;">
                                Products Delivery</h4>
                            <a href="{{ route('sales.products_delivery') }}" class="btn btn-sm"
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
        </div>

    <div class="row mb-4">
        @if ($canViewAttendance)
        <div class="col-6 col-md-3">
            <div class="staff-stat-card card-attendance">
                <div>
                    <div class="stat-number">
                        {{ fmod((float) $totalPresent, 1.0) === 0.0 ? number_format($totalPresent, 0) : number_format($totalPresent, 1) }}
                    </div>
                    <div class="stat-label">Total Attendance</div>
                    <div class="stat-sub">{{ $monthName }}</div>
                </div>
                <i class="fas fa-calendar-check stat-icon"></i>
            </div>
        </div>
        @endif

        @if ($canViewLeaves)
        <div class="col-6 col-md-3">
            <div class="staff-stat-card card-leave">
                <div>
                    <div class="stat-number">{{ $totalLeave }}</div>
                    <div class="stat-label">Total Leave</div>
                    <div class="stat-sub">{{ $monthName }}</div>
                </div>
                <i class="fas fa-user-times stat-icon"></i>
            </div>
        </div>
        @endif

        @if ($canViewFollowUps)
            <div class="col-6 col-md-3">
                <div class="staff-stat-card card-followup">
                    <div>
                        <div class="stat-number">{{ $totalFollowUps }}</div>
                        <div class="stat-label">Total Follow-ups</div>
                        <div class="stat-sub">Assigned to me</div>
                    </div>
                    <i class="fas fa-phone-alt stat-icon"></i>
                </div>
            </div>
        @endif

        @if ($canViewMeetings)
            <div class="col-6 col-md-3">
                <div class="staff-stat-card card-meeting">
                    <div>
                        <div class="stat-number">{{ $totalMeetings }}</div>
                        <div class="stat-label">Total Meetings</div>
                        <div class="stat-sub">Assigned to me</div>
                    </div>
                    <i class="fas fa-handshake stat-icon"></i>
                </div>
            </div>
        @endif
    </div>

    @php
        $workedParts = array_map('intval', explode(':', $hoursWorkedFormatted ?? '00:00:00'));
        $workedSecondsValue = (($workedParts[0] ?? 0) * 3600) + (($workedParts[1] ?? 0) * 60) + ($workedParts[2] ?? 0);
        $standardSeconds = 8 * 3600;
        $workedPercent = min(100, round(($workedSecondsValue / $standardSeconds) * 100, 2));
        $remainingPercent = max(0, 100 - $workedPercent);
        $workStatusClass = $todayStatus === 'Checked In' ? 'success' : ($todayStatus === 'Checked Out' ? 'primary' : 'secondary');
    @endphp

    <div class="row g-4 mb-4">
        @if ($canViewAttendance)
        <div class="col-12 col-xl-6">
            <div class="today-box">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="today-box-title mb-0">Today's Check-In / Check-Out</div>
                </div>

                @if(isset($todaySessions) && $todaySessions->isNotEmpty())
                    @foreach($todaySessions as $session)
                        @php
                            $inTime = $session->check_in_time ? \Carbon\Carbon::parse($todayStr . ' ' . $session->check_in_time)->format('h:i A') : 'Pending';
                            $outTime = $session->check_out_time ? \Carbon\Carbon::parse($todayStr . ' ' . $session->check_out_time)->format('h:i A') : 'Working...';
                        @endphp
                        <div class="today-check-card mt-2">
                            <div class="text-muted mb-2">{{ \Carbon\Carbon::parse($todayStr)->format('d M Y') }}</div>
                            <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                                <span class="check-pill in">
                                    Check-in: {{ $inTime }}
                                </span>
                                <span class="check-pill out">
                                    Check-out: {{ $outTime }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="today-check-card mt-2" style="border: 1px dashed #e2e8f0; text-align: center; padding: 24px 16px;">
                        <i class="fas fa-fingerprint" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 10px; display: block;"></i>
                        <div style="color: #94a3b8; font-size: 13px; font-weight: 500;">No check-in recorded for today</div>
                        <div style="color: #cbd5e1; font-size: 12px; margin-top: 4px;">
                            {{ \Carbon\Carbon::parse($todayStr)->format('d M Y') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="today-box">
                <div class="today-box-title">Today's Hours</div>

                <div class="hours-line">
                    <span>Hours Worked</span>
                    <span id="hoursWorkedBadge" class="hours-time worked">{{ $hoursWorkedFormatted }}</span>
                </div>
                <div class="hours-bar">
                    <span id="workedProgressBar" class="worked-fill" style="width: {{ $workedPercent }}%;"></span>
                </div>

                <div class="hours-line">
                    <span>Remaining Hours</span>
                    <span id="remainingHoursBadge" class="hours-time remaining">{{ $remainingHoursFormatted }}</span>
                </div>
                <div class="hours-bar">
                    <span id="remainingProgressBar" class="remaining-fill" style="width: {{ $remainingPercent }}%;"></span>
                </div>

                <div class="hours-line mt-3 mb-0">
                    <span>Status</span>
                    <span class="status-chip bg-{{ $workStatusClass }}">{{ $todayStatus }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>

    @push('js')
    <script>
    /* ── Staff Dashboard: Today's Hours Live Timer ──────────────────────────
       Timer base = totalWorkedSeconds calculated by PHP server from DB records.
       Client clock drift/timezone issues are eliminated by only tracking
       elapsed time since page load.
    ──────────────────────────────────────────────────────────────────────── */
    (function () {
        const isCheckedIn      = {{ $todayStatus === 'Checked In' ? 'true' : 'false' }};
        const standardSec      = {{ (int) $standardSeconds }};
        const initialWorkedSec = {{ (int) $totalWorkedSeconds }};
        const checkInUnix      = {{ $todayCheckInUnix ?? 'null' }};

        // No check-in today → nothing to animate
        if (!checkInUnix) {
            console.log('[StaffDashboard] No check-in today — timer not started.');
            return;
        }

        const workedBadge    = document.getElementById('hoursWorkedBadge');
        const remainingBadge = document.getElementById('remainingHoursBadge');
        const workedBar      = document.getElementById('workedProgressBar');
        const remainingBar   = document.getElementById('remainingProgressBar');
        if (!workedBadge || !remainingBadge) return;

        function pad(n) { return String(n).padStart(2, '0'); }
        function secsToHMS(s) {
            s = Math.max(0, Math.floor(s));
            const h = Math.floor(s / 3600);
            const m = Math.floor((s % 3600) / 60);
            const sec = s % 60;
            return pad(h) + ':' + pad(m) + ':' + pad(sec);
        }

        function updateUI(workedSecs) {
            const remaining = Math.max(0, standardSec - workedSecs);
            const workedPct = Math.min(100, (workedSecs / standardSec) * 100);
            const remainPct = Math.min(100, (remaining  / standardSec) * 100);
            workedBadge.textContent    = secsToHMS(workedSecs);
            remainingBadge.textContent = secsToHMS(remaining);
            if (workedBar)    workedBar.style.width    = workedPct + '%';
            if (remainingBar) remainingBar.style.width  = remainPct + '%';
        }

        if (!isCheckedIn) {
            // ── CHECKED OUT: final static value — no timer ───────────────────
            console.log('[StaffDashboard] CHECKED OUT — final worked seconds:', initialWorkedSec, '=', secsToHMS(initialWorkedSec));
            updateUI(initialWorkedSec);
        } else {
            // ── STILL WORKING: live timer ────────────────────────────────────
            // Elapsed = time passed since the page was opened
            const pageLoadTime = Date.now();
            function tick() {
                const elapsedSinceLoad = Math.floor((Date.now() - pageLoadTime) / 1000);
                const currentWorked = initialWorkedSec + elapsedSinceLoad;
                updateUI(currentWorked);
            }
            tick();                    // run immediately on page load
            setInterval(tick, 1000);   // then update every second
            console.log('[StaffDashboard] LIVE TIMER started. Base Server Seconds:', initialWorkedSec);
        }
    })();
    </script>
    @endpush

    <div class="row g-4 mb-4">
        @if ($canViewFollowUps)
            <div class="col-12 col-xl-6">
                <div class="card staff-panel">
                    <div class="card-body">
                        <div class="section-title">
                            <i class="fas fa-phone-alt text-primary"></i> My Follow-ups
                        </div>

                    @if ($recentFollowUps->isEmpty())
                        <div class="staff-empty-state">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            No follow-ups assigned to you yet.
                        </div>
                    @else
                        <div class="staff-table-wrap">
                            <table class="table staff-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer Name</th>
                                        <th>Purpose</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Date &amp; Time</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentFollowUps as $i => $fu)
                                        <tr class="staff-row-summary">
                                            <td>{{ $i + 1 }}</td>
                                            <td class="wrap-cell">{{ $fu->subject_name ?? '—' }}</td>
                                            <td class="wrap-cell">{{ $fu->purpose }}</td>
                                            <td>
                                                <span class="priority-badge badge-{{ strtolower($fu->priority) }}">
                                                    {{ $fu->priority }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge badge-{{ strtolower($fu->status) }}">
                                                    {{ $fu->status }}
                                                </span>
                                            </td>
                                            <td class="wrap-cell">{{ $fu->follow_up_datetime ? $fu->follow_up_datetime->format('d M Y, h:i A') : '—' }}</td>
                                            <td>
                                                <button type="button" class="mobile-toggle-btn" aria-expanded="false">
                                                    <span class="toggle-plus">+</span>
                                                    <span class="toggle-minus">-</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="mobile-detail-row">
                                            <td colspan="7">
                                                <div class="mobile-detail-card">
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Customer Name</span>
                                                        <span class="mobile-detail-value">{{ $fu->subject_name ?? '—' }}</span>
                                                    </div>
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Purpose</span>
                                                        <span class="mobile-detail-value">{{ $fu->purpose }}</span>
                                                    </div>
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Priority</span>
                                                        <span class="mobile-detail-value">
                                                            <span class="priority-badge badge-{{ strtolower($fu->priority) }}">
                                                                {{ $fu->priority }}
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Status</span>
                                                        <span class="mobile-detail-value">
                                                            <span class="status-badge badge-{{ strtolower($fu->status) }}">
                                                                {{ $fu->status }}
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Date &amp; Time</span>
                                                        <span class="mobile-detail-value">{{ $fu->follow_up_datetime ? $fu->follow_up_datetime->format('d M Y, h:i A') : '—' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($totalFollowUps > 10)
                            <div class="text-end mt-3">
                                <a href="{{ route('followup.list') }}" class="btn btn-sm btn-outline-primary">
                                    View All Follow-ups <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        @endif
                    @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($canViewMeetings)
            <div class="col-12 col-xl-6">
                <div class="card staff-panel">
                    <div class="card-body">
                        <div class="section-title">
                            <i class="fas fa-handshake text-success"></i> My Meetings
                        </div>

                    @if ($recentMeetings->isEmpty())
                        <div class="staff-empty-state">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            No meetings assigned to you yet.
                        </div>
                    @else
                        <div class="staff-table-wrap">
                            <table class="table staff-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Customer Name</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Scheduled On</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentMeetings as $i => $meeting)
                                        <tr class="staff-row-summary">
                                            <td>{{ $i + 1 }}</td>
                                            <td class="wrap-cell">{{ $meeting->meeting_title }}</td>
                                            <td class="wrap-cell">{{ $meeting->customer->name ?? '—' }}</td>
                                            <td class="wrap-cell">{{ $meeting->meeting_type }}</td>
                                            <td>
                                                <span class="status-badge badge-{{ strtolower($meeting->status) }}">
                                                    {{ $meeting->status }}
                                                </span>
                                            </td>
                                            <td>{{ $meeting->scheduled_on ? $meeting->scheduled_on->format('d M Y, h:i A') : '—' }}</td>
                                            <td>
                                                <button type="button" class="mobile-toggle-btn" aria-expanded="false">
                                                    <span class="toggle-plus">+</span>
                                                    <span class="toggle-minus">-</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="mobile-detail-row">
                                            <td colspan="7">
                                                <div class="mobile-detail-card">
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Title</span>
                                                        <span class="mobile-detail-value">{{ $meeting->meeting_title }}</span>
                                                    </div>
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Customer Name</span>
                                                        <span class="mobile-detail-value">{{ $meeting->customer->name ?? '—' }}</span>
                                                    </div>
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Type</span>
                                                        <span class="mobile-detail-value">{{ $meeting->meeting_type }}</span>
                                                    </div>
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Status</span>
                                                        <span class="mobile-detail-value">
                                                            <span class="status-badge badge-{{ strtolower($meeting->status) }}">
                                                                {{ $meeting->status }}
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div class="mobile-detail-item">
                                                        <span class="mobile-detail-label">Scheduled On</span>
                                                        <span class="mobile-detail-value">{{ $meeting->scheduled_on ? $meeting->scheduled_on->format('d M Y, h:i A') : '—' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($totalMeetings > 10)
                            <div class="text-end mt-3">
                                <a href="{{ route('meeting.list') }}" class="btn btn-sm btn-outline-success">
                                    View All Meetings <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        @endif
                    @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Products Delivery moved to the chart row --}}
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.mobile-toggle-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const summaryRow = button.closest('tr');
                const detailRow = summaryRow ? summaryRow.nextElementSibling : null;

                if (!detailRow || !detailRow.classList.contains('mobile-detail-row')) {
                    return;
                }

                const isOpen = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                detailRow.classList.toggle('is-open', !isOpen);
            });
        });
    });

    document.addEventListener('change', function (event) {
        const select = event.target.closest('.pending-delivery-status');

        if (!select) {
            return;
        }

        const previousStatus = select.dataset.currentStatus;
        const newStatus = select.value;
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const url = select.dataset.url;

        if (!url) {
            select.value = previousStatus;
            alert('Delivery status update URL is missing.');
            return;
        }

        select.disabled = true;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ status: newStatus }),
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    if (!response.ok) {
                        throw data;
                    }

                    return data;
                });
            })
            .then(function (response) {
                if (response.status) {
                    select.dataset.currentStatus = newStatus;
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        text: response.message || 'Delivery status updated successfully.',
                        timer: 1400,
                        showConfirmButton: false
                    });
                    return;
                }

                select.value = previousStatus;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Unable to update delivery status.'
                });
            })
            .catch(function (error) {
                select.value = previousStatus;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error?.message || 'Unable to update delivery status.'
                });
            })
            .finally(function () {
                select.disabled = false;
            });
    });
</script>
@push('js')
<script>
    const currentYear = {{ $currentYear ?? date('Y') }};
    const previousYear = {{ $previousYear ?? (date('Y') - 1) }};
    const salesThisMonth = @json($salesChartThisMonth ?? []);
    const purchaseThisMonth = @json($purchaseChartThisMonth ?? []);

    const salesChartData = {
        [currentYear]: @json($salesChartthisyear ?? []),
        [previousYear]: @json($salesChartpreviousyear ?? []),
        'thisMonth': salesThisMonth
    };

    const purchaseChartData = {
        [currentYear]: @json($purchaseChartthisyear ?? []),
        [previousYear]: @json($purchaseChartpreviousyear ?? []),
        'thisMonth': purchaseThisMonth
    };

    const options = {
        grid: {
            borderWidth: 1,
            borderColor: 'rgba(67, 87, 133, .09)',
            hoverable: true
        },
        xaxis: {
            ticks: [],
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

    function getDayTicks(length) {
        return Array.from({ length }, (_, i) => [i, (i + 1).toString()]);
    }

    function renderChart(containerId, label, data, barColor, isMonthly = true) {
        if ($(containerId).length === 0) return;

        const series = data.map((val, idx) => [idx, val]);

        options.xaxis.ticks = isMonthly ? [
                [0, 'Jan'], [1, 'Feb'], [2, 'Mar'], [3, 'Apr'],
                [4, 'May'], [5, 'Jun'], [6, 'Jul'], [7, 'Aug'],
                [8, 'Sep'], [9, 'Oct'], [10, 'Nov'], [11, 'Dec']
            ] : getDayTicks(data.length);

        $.plot(containerId, [{
            label: label,
            data: series,
            color: barColor
        }], {
            ...options,
            series: {
                shadowSize: 0,
                bars: {
                    show: true,
                    barWidth: isMonthly ? 0.5 : 0.6,
                    align: 'center',
                    fill: 1,
                    lineWidth: 0,
                    fillColor: { colors: [{ opacity: 1 }, { opacity: 1 }] }
                }
            }
        });

        if (isMonthly) {
            $('.flot-x-axis .flot-tick-label').each(function() {
                const labelText = $(this).text();
                if (['Jan', 'Apr', 'Jul', 'Oct'].includes(labelText)) {
                    $(this).css('color', '#ff9f43').css('font-weight', 'bold');
                } else if (['Feb', 'May', 'Aug', 'Nov'].includes(labelText)) {
                    $(this).css('color', '#10b981');
                } else {
                    $(this).css('color', '#3b82f6');
                }
            });
        }
    }

    $(document).ready(function() {
        if ($('.chart-select2').length > 0) {
            $('.chart-select2').select2({
                minimumResultsForSearch: -1,
                width: '100%'
            });
        }

        renderChart('#saleschart', 'Sales', salesChartData['thisMonth'], '#4361ee', false);
        renderChart('#purchasechart', 'Purchases', purchaseChartData['thisMonth'], '#10b981', false);

        $('#salesYearSelect').on('change', function() {
            const year = $(this).val();
            if (year === 'month') {
                renderChart('#saleschart', 'Sales (This Month)', salesChartData['thisMonth'], '#4361ee', false);
            } else {
                renderChart('#saleschart', 'Sales', salesChartData[year], '#4361ee');
            }
        });

        $('#purchaseYearSelect').on('change', function() {
            const year = $(this).val();
            if (year === 'month') {
                renderChart('#purchasechart', 'Purchases (This Month)', purchaseChartData['thisMonth'], '#10b981', false);
            } else {
                renderChart('#purchasechart', 'Purchases', purchaseChartData[year], '#10b981');
            }
        });
            }
        });
        
        // Check for pending price updates and show modal
        $.ajax({
            url: "{{ route('api.pricing-updates.data') }}",
            type: "GET",
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
</script>
@endpush
@endsection
