@extends('layout.app')

@section('title', 'Customer List')

@section('content')
    <style>
        .sorting_1 {
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
        }

        .customer-name {
            display: block;
            margin-left: 8px;
            font-size: 14px;
            line-height: 1.3;
            word-break: break-word;
            overflow-wrap: anywhere;
            white-space: normal;
            max-width: 100%;
        }

        .customer-cell {
            display: flex;
            align-items: center;
            min-width: 0;
            /* IMPORTANT for wrapping */
        }

        /* Prevent image shrinking */
        .customer-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* Allow text to shrink and wrap */
        .customer-name-wrapper {
            flex: 1;
            min-width: 0;
        }

        /* Pagination Styling */
        .pagination .page-item .page-link {
            background-color: #5d6d7e;
            color: #fff;
            border: none;
              margin: 0 3px;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: bold;
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

        @media screen and (max-width: 768px) {
            .table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch !important;
            }

            .search-set {
                margin-right: 1rem !important;
            }
        }

        /* Desktop: show all columns normally */
        @media (min-width: 769px) {

            table.datanew thead th,
            table.datanew tbody td {
                display: table-cell !important;
            }

            /* Hide the Details toggle column on desktop */
            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(10) {
                display: none !important;
            }
        }

        /* Mobile: hide non-essential columns, show Details toggle */
        @media (max-width: 768px) {
            table.datanew {
                width: 100% !important;
                table-layout: fixed;
            }

            table.datanew thead th:nth-child(n+1),
            table.datanew tbody td:nth-child(n+1) {
                display: none !important;
            }

            /* Show only Customer Name and Details columns on mobile */
            table.datanew thead th:nth-child(2),
            table.datanew tbody td:nth-child(2) {
                display: table-cell !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(10) {
                display: table-cell !important;
                text-align: center;
                vertical-align: top !important;
                width: 56px !important;
                min-width: 56px !important;
                max-width: 56px !important;
                padding: 12px 6px !important;
            }

            .toggle-details {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 44px;
                height: 44px;
                margin-left: auto;
            }

            .toggle-details i {
                font-size: 24px;
            }

            /* Style for customer name wrapping */
            table.datanew tbody td:nth-child(2) {
                display: table-cell !important;
                width: calc(100% - 56px) !important;
                max-width: calc(100vw - 96px) !important;
                vertical-align: top !important;
            }

            table.datanew tbody td:nth-child(2) > div {
                width: 100%;
            }

            table.datanew tbody td:nth-child(2) a {
                display: flex !important;
                align-items: center !important;
                text-align: left !important;
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                word-wrap: break-word !important;
                word-break: break-word !important;
                overflow-wrap: break-word !important;
                white-space: normal !important;
                line-height: 1.3 !important;
            }

            /* .customer-name {
                    display: inline-block !important;
                    /* max-width: calc(100% - 60px) !important; */
            /* margin-left: 8px !important;
                    font-size: 14px !important;
                    word-break: break-word !important;
                    hyphens: auto !important;
                    -webkit-hyphens: auto !important;
                    -ms-hyphens: auto !important;
                } */

            .customer-name {
                display: block;
                margin-left: 8px;
                font-size: 14px;
                line-height: 1.3;
                word-break: break-word;
                overflow-wrap: anywhere;
                white-space: normal;
                max-width: 100%;
            }

            .customer-cell {
                display: flex;
                align-items: center;
                min-width: 0;
                /* IMPORTANT for wrapping */
            }

            /* Prevent image shrinking */
            .customer-image {
                width: 40px;
                height: 40px;
                object-fit: cover;
                border-radius: 50%;
                flex-shrink: 0;
            }

            /* Allow text to shrink and wrap */
            .customer-name-wrapper {
                flex: 1;
                min-width: 0;
            }

            /* Limit to 2 lines with ellipsis */
            .customer-name.truncated {
                display: -webkit-box !important;
                -webkit-line-clamp: 2 !important;
                -webkit-box-orient: vertical !important;
                overflow: hidden !important;
                /* text-overflow: ellipsis !important; */
            }
        }

        /* Tablet specific fixes */
        @media screen and (width: 768px) {
            .table-container {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }

            table.datanew thead th.details-column,
            table.datanew tbody td:nth-child(10) {
                display: table-cell !important;
                width: 56px !important;
                min-width: 56px !important;
                max-width: 56px !important;
                vertical-align: top !important;
            }

            .toggle-details {
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                width: 44px;
                height: 44px;
                z-index: 10 !important;
            }

            .toggle-details i {
                font-size: 20px !important;
                width: 24px !important;
                height: 24px !important;
                line-height: 24px !important;
            }
        }

        @media (min-width: 769px) and (max-width: 991.98px) {
            table.datanew tbody td:nth-child(2) {
                max-width: 190px;
                word-break: break-word;
                overflow-wrap: anywhere;
                white-space: normal;
            }
        }

        /* Fade out animation for error messages */
        .fade-out {
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }

        .fade-out.hidden {
            opacity: 0;
        }

        @media (max-width: 767.98px) {
            .table-top .search-set {
                display: flex;
                justify-content: flex-start !important;
                width: 100%;
            }

            .table-top .search-input {
                margin-left: 0 !important;
            }

            p {
                margin-bottom: 5px !important;
            }
        }

        /* Customer specific styling */
        .customer-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 10px;
        }

        /* Collapsible details styling */
    

        .detail-item {
            display: flex;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .detail-label {
            font-weight: 600;
            min-width: 100px;
            color: #495057;
        }

        .detail-value {
            color: #212529;
            flex: 1;
        }

        .detail-value.email-value {
            word-break: break-word;
            overflow-wrap: anywhere;
            white-space: normal;
        }

        .mobile-actions {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
        }

        .mobile-actions a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            /* background-color: #f8f9fa; */
            transition: all 0.3s ease;
        }

        .mobile-actions a:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
        }

        .mobile-actions svg {
            width: 18px;
            height: 18px;
        }

        .delete-customer svg path {
            fill: #dc3545 !important;
        }

        .listing-action-dropdown {
            display: inline-flex;
            justify-content: center;
            width: 100%;
        }

        .listing-action-dropdown .action-menu-toggle {
            align-items: center;
            background: #fff;
            border: 1px solid #d7dde8;
            border-radius: 4px;
            color: #1b2850;
            display: inline-flex;
            height: 28px;
            justify-content: center;
            padding: 0;
            width: 34px;
        }

        .listing-action-menu {
            border: 1px solid #e8ebed;
            border-radius: 6px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
            min-width: 168px;
            padding: 6px;
            z-index: 999999999999;
        }

        .listing-action-menu-floating {
            position: fixed !important;
            z-index: 999999999999 !important;
        }

        .listing-action-menu .dropdown-item {
            align-items: center;
            border-radius: 4px;
            color: #344054;
            display: flex;
            font-size: 12px;
            gap: 8px;
            padding: 7px 8px;
            width: 100%;
        }

        .listing-action-menu .dropdown-item i {
            color: #667085;
            font-size: 13px;
            text-align: center;
            width: 15px;
        }

        .listing-action-menu .dropdown-item.text-danger,
        .listing-action-menu .dropdown-item.text-danger i {
            color: #ea5455 !important;
        }

        .listing-action-menu .dropdown-item:hover {
            background: #f4f6f8;
            color: #1b2850;
        }
         /* ✅ Hide default DataTables search box completely */
        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }
         /* Add styles for search input and pagination (from category list) */
        .search-input {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-input input {
            padding-left: 35px !important;
            border-radius: 5px;
        }

        .btn-searchset {
            position: absolute;
            left: 10px;
            z-index: 10;
            padding: 0;
            top: 7px !important;
        }
         /* Custom Pagination Styling */
        .pagination .page-item .page-link {
            background-color: #5d6d7e;
            color: #fff;
            border: none;
            margin: 0 4px;
            padding: 6px 15px;
            border-radius: 6px;
            font-weight: bold;
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

        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-container table thead th {
            white-space: nowrap;
        }

        /* Phone layout (Sales style cards) */
        @media (max-width: 767.98px) {
            .cu-page.content {
                padding: 6px 6px 90px !important;
            }
            .cu-page > .page-header,
            .cu-page .table-container {
                display: none !important;
            }
            .cu-m-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-bottom: 8px;
                padding: 12px 10px;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            }
            .cu-m-title {
                margin-left: 2px;
                font-size: 15px;
                font-weight: 600;
                color: #1b2850;
                white-space: nowrap;
            }
            .cu-m-actions {
                display: flex;
                gap: 6px;
                flex-shrink: 0;
            }
            .cu-m-actions .btn {
                display: inline-flex;
                align-items: center;
                height: 30px;
                margin: 0 !important;
                padding: 0 10px !important;
                border-radius: 8px !important;
                font-size: 12px !important;
                font-weight: 600;
                white-space: nowrap;
                color: #fff !important;
            }
            .cu-m-import {
                background: #ff9f43 !important;
                border: 1px solid #ff9f43 !important;
            }
            .cu-m-new {
                background: #1b2850 !important;
                border: 1px solid #1b2850 !important;
            }

            .cu-page > .card {
                background: transparent !important;
                border: 0 !important;
                box-shadow: none !important;
                margin: 0 !important;
            }
            .cu-page > .card > .card-body {
                padding: 0 !important;
            }
            .cu-page .card-body > .mb-2 {
                margin-bottom: 8px !important;
                padding: 8px 10px;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            }
            .cu-page .search-set {
                margin: 0 !important;
            }
            .cu-page .search-input {
                width: 100% !important;
                margin: 0 !important;
            }
            .cu-page .search-input input {
                width: 100% !important;
                height: 40px !important;
                border-radius: 10px !important;
                font-size: 13px;
            }
            .cu-page .btn-searchset {
                top: 50% !important;
                transform: translateY(-50%);
            }

            .cu-card-list {
                display: flex !important;
                flex-direction: column;
                gap: 8px;
            }
            .cu-card {
                display: grid;
                grid-template-columns: 44px minmax(0, 1fr) auto;
                align-items: center;
                column-gap: 10px;
                padding: 12px;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
            }
            .cu-avatar {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 44px;
                height: 44px;
                border-radius: 50%;
                overflow: hidden;
                background: #fff3e6;
                color: #e07b14;
                font-size: 15px;
                font-weight: 700;
                text-decoration: none;
            }
            .cu-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .cu-main {
                min-width: 0;
            }
            .cu-name {
                display: block;
                overflow: hidden;
                color: #1b2850 !important;
                font-size: 14px;
                font-weight: 700;
                text-transform: uppercase;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .cu-sub {
                margin-top: 2px;
                overflow: hidden;
                color: #8a92a6;
                font-size: 12px;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .cu-acts {
                display: flex;
                align-items: center;
                gap: 2px;
            }
            .cu-call {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: #e7f6ec;
                color: #198754 !important;
                font-size: 13px;
            }
            .cu-acts .listing-action-dropdown {
                width: auto;
            }
            .cu-acts .action-menu-toggle {
                width: 32px !important;
                height: 32px !important;
                border: 0 !important;
                background: transparent !important;
                color: #8b93a7 !important;
                font-size: 17px;
                box-shadow: none !important;
            }
            .cu-acts .action-menu-toggle i {
                transform: rotate(90deg);
            }
            .cu-acts > .text-muted {
                display: none;
            }
            .cu-foot {
                grid-column: 1 / -1;
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1.3fr);
                gap: 8px;
                margin-top: 10px;
                padding-top: 10px;
                border-top: 1px dashed #e5e7eb;
            }
            .cu-stat {
                min-width: 0;
            }
            .cu-stat span {
                display: block;
                color: #8a92a6;
                font-size: 11px;
            }
            .cu-stat strong {
                display: block;
                overflow: hidden;
                color: #1b2850;
                font-size: 12.5px;
                font-weight: 700;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .cu-empty {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 8px;
                padding: 32px 12px;
                background: #fff;
                border-radius: 12px;
                color: #8a92a6;
                font-size: 13px;
            }
            .cu-empty i {
                font-size: 28px;
                color: #d0d5dd;
            }

            .cu-page .pagination-controls {
                margin-top: 10px !important;
                padding: 10px;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(16, 24, 40, .08);
                gap: 8px;
            }
            .cu-page .pagination-controls > .d-flex {
                flex-wrap: wrap;
                justify-content: center;
                margin-bottom: 0 !important;
                font-size: 12px;
            }

            :root[data-theme="dark"] .cu-m-head,
            :root[data-theme="dark"] .cu-page .card-body > .mb-2,
            :root[data-theme="dark"] .cu-card,
            :root[data-theme="dark"] .cu-empty,
            :root[data-theme="dark"] .cu-page .pagination-controls {
                background: #1e293b;
            }
            :root[data-theme="dark"] .cu-m-title,
            :root[data-theme="dark"] .cu-name,
            :root[data-theme="dark"] .cu-stat strong {
                color: #f8fafc !important;
            }
        }

    </style>

    <div class="content cu-page">
        <div class="cu-m-head d-md-none">
            <div class="cu-m-title">All Customers</div>
            @if (app('hasPermission')(9, 'add'))
                <div class="cu-m-actions">
                    <a href="{{ route('customer.import') }}" class="btn btn-sm cu-m-import">
                        <i class="fas fa-file-import me-1"></i> Import
                    </a>
                    <a href="{{ route('customer.add') }}" class="btn btn-sm cu-m-new">
                        <i class="fas fa-plus me-1"></i> New Customer
                    </a>
                </div>
            @endif
        </div>

        <div class="page-header">
            <div class="page-title">
                <h4>All Customers</h4>
            </div>
            <div class="page-btn d-flex gap-2">
                @if (app('hasPermission')(9, 'add'))
                    <a href="{{ route('customer.import') }}" class="btn btn-added btn-sm">
                        <i class="fa-solid fa-file-import me-1"></i> Import
                    </a>
                    <a href="{{ route('customer.add') }}" class="btn btn-added btn-sm">
                        <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-1" alt="img">New
                        Customer
                    </a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="mb-2">
                    <div class="search-set d-flex justify-content-md-start justify-content-start w-100">
                        <div class="search-path"></div>
                        <div class="search-input">
                            <a class="btn btn-searchset">
                                <img src="{{ env('ImagePath') . 'admin/assets/img/icons/search-white.svg' }}"
                                    alt="img">
                            </a>
                              <input type="text" id="search-input" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                </div>

                {{-- <div class="table-scroll-top">
                    <div></div>
                </div> --}}
                {{-- <div class="table-responsive">
                    <table class="table datanew"> --}}

                <div class="table-container">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>Customer Code</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>GST Number</th>
                                <th>PAN Number</th>
                                <th>Country</th>
                                <th>City</th>
                                <th>Action</th>
                                <th class="details-column">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <div class="cu-card-list d-md-none" id="customerCardList"></div>

                <!-- Pagination Controls (same as staff list) -->
<div class="pagination-controls d-flex flex-column flex-md-row justify-content-between align-items-center mt-3" style="display: none;">
    <div class="d-flex align-items-center mb-3 mb-md-0">
        <span class="me-2" style="font-size: 14px; color: #555;">Show per page :</span>
        <select id="per-page-select" class="form-select form-select-sm" style="width: auto; border: 1px solid #ddd;">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <span class="ms-3" style="font-size: 14px; color: #555;">
            <span id="pagination-from">0</span> - <span id="pagination-to">0</span> of <span id="pagination-total">0</span> items
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
@endsection

@push('js')
    <script>
        const userRole = "{{ auth()->user()->role }}";
    </script>

    {{-- <script>
        $(document).ready(function() {
            var authToken = localStorage.getItem("authToken");
            var table = $('.datanew').DataTable(); // Initialize DataTable
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            let url = "/api/getAllCustomer";
            if (selectedSubAdminId) {
                url += `?selectedSubAdminId=${selectedSubAdminId}`;
            }

            fetchCustomers(); // Call function to load data

            function fetchCustomers() {
                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function(response) {
                        if (response.status) {
                            let customers = response.data;
                            let tableBody = [];

                            // Function to capitalize first letter of each word
                            function capitalizeWords(str) {
                                if (!str || str.trim() === '') return 'N/A';
                                return str.replace(/\b\w/g, function(char) {
                                    return char.toUpperCase();
                                });
                            }

                            customers.forEach((customer) => {
                                let customerName = capitalizeWords(customer.name);

                                // Details toggle for mobile
                                let detailsToggle = `
                                    <a href="#details-${customer.id}" class="toggle-details" data-bs-toggle="collapse">
                                        <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
                                    </a>
                                `;

                                // Image URL
                                let imageUrl = customer.profile_image ?
                                    '{{ env('ImagePath') . '/storage/' }}' + customer
                                    .profile_image :
                                    '{{ env('ImagePath') . 'admin/assets/img/customer/customer5.jpg' }}';

                                // Prepare delete button conditionally
                                let deleteBtn = '';
                                if (
                                    userRole !== 'sales-manager' &&
                                    userRole !== 'purchase-manager' &&
                                    userRole !== 'inventory-manager'
                                ) {
                                    @if (app('hasPermission')(9, 'delete'))
                                        deleteBtn = `
                                            <a class="me-2 confirm-text delete-customer" data-id="${customer.id}" href="javascript:void(0);">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 4H15V6H9V4ZM8 8H17V20H7V8H8Z" fill="#092C4C"></path>
                                                    <path d="M9 10H11V18H9V10ZM13 10H15V18H13V10Z" fill="#092C4C"></path>
                                                </svg>
                                            </a>`;
                                    @endif
                                }

                                // Prepare action buttons
                                let actionButtons = `
                                    @if (app('hasPermission')(9, 'view'))
                                        <a class="me-2" href="/customer-view/${customer.id}">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"></path>
                                                <path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"></path>
                                            </svg>
                                        </a>
                                    @endif
                                    @if (app('hasPermission')(9, 'edit'))
                                        <a class="me-2" href="/edit-customer/${customer.id}">
                                            <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"></path>
                                            </svg>
                                        </a>
                                    @endif
                                    ${deleteBtn}
                                `;

                                // Mobile actions for collapsible section
                                let mobileActions = `
                                    <div class="mobile-actions">
                                        @if (app('hasPermission')(9, 'view'))
                                            <a href="/customer-view/${customer.id}">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"></path>
                                                    <path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"></path>
                                                </svg>
                                            </a>
                                        @endif
                                        @if (app('hasPermission')(9, 'edit'))
                                            <a href="/edit-customer/${customer.id}">
                                                <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"></path>
                                                </svg>
                                            </a>
                                        @endif
                                        ${deleteBtn}
                                    </div>
                                `;

                                tableBody.push([
                                    // Column 1: Customer Name with image AND collapsible details
                                    `<div>
                                        <div style="display: flex; align-items: center;">
                                            <a href="javascript:void(0);" class="d-flex align-items-center">
                                                <img src="${imageUrl}"
                                                    alt="customer"
                                                    class="customer-image">
                                                <span class="customer-name truncated" style="">${customerName}</span>
                                            </a>
                                        </div>

                                        <!-- Collapsible Details (visible only on mobile) -->
                                        <div class="collapse mt-2 d-lg-none" id="details-${customer.id}">
                                            <div class="collapse-details">
                                                 <div class="detail-item">
                                                    <span class="detail-label">Email:</span>
                                                    <span class="detail-value">${customer.email || 'N/A'}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Phone:</span>
                                                    <span class="detail-value">${customer.phone || 'N/A'}</span>
                                                </div>

                                                <div class="detail-item">
                                                    <span class="detail-label">Country:</span>
                                                    <span class="detail-value">${customer.country || 'N/A'}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">City:</span>
                                                    <span class="detail-value">${customer.city || 'N/A'}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">GST:</span>
                                                    <span class="detail-value">${customer.gst_number || 'N/A'}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">PAN:</span>
                                                    <span class="detail-value">${customer.pan_number || 'N/A'}</span>
                                                </div>

                                                ${mobileActions}
                                            </div>
                                        </div>
                                    </div>`,

                                    // Column 5: Email
                                    customer.email || 'N/A',
                                    // Column 2: Phone
                                    customer.phone || 'N/A',

                                    // Column 3: GST Number
                                    customer.gst_number || 'N/A',

                                    // Column 4: PAN Number
                                    customer.pan_number || 'N/A',

                                    // Column 6: Country
                                    customer.country || 'N/A',

                                    // Column 7: City
                                    customer.city || 'N/A',

                                    // Column 8: Action Buttons (hidden on mobile)
                                    actionButtons,

                                    // Column 9: Details Toggle (only for mobile)
                                    detailsToggle
                                ]);
                            });

                            table.clear().rows.add(tableBody).draw();

                            // Sync top scrollbar
                            const topScroll = document.querySelector('.table-scroll-top');
                            const tableResponsive = document.querySelector('.table-responsive');
                            const tableElement = document.querySelector('.datanew');

                            if (topScroll && tableResponsive && tableElement) {
                                const topInnerDiv = topScroll.querySelector('div');
                                topInnerDiv.style.width = tableElement.scrollWidth + 'px';

                                topScroll.onscroll = () => {
                                    tableResponsive.scrollLeft = topScroll.scrollLeft;
                                };
                                tableResponsive.onscroll = () => {
                                    topScroll.scrollLeft = tableResponsive.scrollLeft;
                                };
                            }
                        } else {
                            table.clear().draw();
                            $(".datanew tbody").html(
                            '<tr><td colspan="10">No customers found</td></tr>');
                        }
                    },
                    error: function(xhr) {
                        // console.log("Error:", xhr);
                        table.clear().draw();
                        $(".datanew tbody").html(
                            '<tr><td colspan="10">Error loading customer data</td></tr>');
                    },
                });
            }

            // Toggle details icon
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

            // Delete customer function
            $(document).on('click', '.delete-customer', function() {
                var customerId = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ff9f43",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/deleteCustomer/${customerId}`,
                            type: 'POST',
                            headers: {
                                "Authorization": "Bearer " + authToken,
                            },
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: response.message,
                                        icon: "success",
                                        confirmButtonColor: "#ff9f43",
                                        confirmButtonText: "OK"
                                    }).then(() => {
                                        fetchCustomers(); // Refresh the table
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: response.message,
                                        icon: "error",
                                        confirmButtonColor: "#ff9f43",
                                        confirmButtonText: "OK"
                                    });
                                }
                            },
                            error: function(xhr) {
                                let message = "Something went wrong!";
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    message = xhr.responseJSON.error;
                                }

                                Swal.fire({
                                    title: "Error!",
                                    text: message,
                                    icon: "error",
                                    confirmButtonColor: "#ff9f43",
                                    confirmButtonText: "OK"
                                });
                            }
                        });
                    }
                });
            });

            // Reset details when switching to desktop
            $(window).on('resize', function() {
                if ($(window).width() > 768) {
                    // Close all collapsed sections
                    $('.collapse').removeClass('show');

                    // Reset all toggle icons to plus
                    $('.toggle-details i')
                        .removeClass('fa-minus-circle')
                        .addClass('fa-plus-circle')
                        .css('color', '#ff9f43');
                }
            });
        });
    </script> --}}
    <script>
$(document).ready(function() {
    var authToken = localStorage.getItem("authToken");
    var table = $('.datanew').DataTable({
        destroy: true,
        paging: false,
        info: false,
        searching: false,
        dom: 't',
        ordering: false,
        // responsive: false
    });

    const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

    // Pagination state
    let currentPage = 1;
    let lastPage = 1;
    let perPage = 10;
    let searchQuery = '';

    // Initial fetch
    fetchCustomers(currentPage);

    // Search input handler
    $('#search-input').on('keyup', function() {
        searchQuery = $(this).val();
        fetchCustomers(1);
    });

    // Per-page change handler
    $('#per-page-select').on('change', function() {
        perPage = $(this).val();
        fetchCustomers(1);
    });

    // Handle page number clicks with ellipsis support
    $(document).on('click', '#pagination-numbers .page-link', function(e) {
        e.preventDefault();
        let page = $(this).data('page');
        let action = $(this).data('action');

        // Handle ellipsis clicks to load next/previous groups
        if (action === 'next-group') {
            // Load the page that starts the next group
            if (page && page <= lastPage) {
                fetchCustomers(page);
            }
            return;
        }

        if (action === 'prev-group') {
            // Load the previous group's starting page
            let prevStartPage = Math.max(1, page - 2);
            if (prevStartPage >= 1 && prevStartPage <= lastPage) {
                fetchCustomers(prevStartPage);
            }
            return;
        }

        // Regular page navigation
        if (page && page !== currentPage && page >= 1 && page <= lastPage) {
            fetchCustomers(page);
        }
    });

    function fetchCustomers(page = 1) {
        let url = `/api/getAllCustomer?page=${page}&per_page=${perPage}`;
        if (selectedSubAdminId) {
            url += `&selectedSubAdminId=${selectedSubAdminId}`;
        }
        if (searchQuery) {
            url += `&search=${encodeURIComponent(searchQuery)}`;
        }

        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            headers: {
                "Authorization": "Bearer " + authToken,
            },
            success: function(response) {
                if (response.status) {
                    let customers = response.data;
                    let pagination = response.pagination;

                    // Update state
                    currentPage = pagination.current_page;
                    lastPage = pagination.last_page;

                    // Update pagination UI
                    updatePaginationUI(pagination);

                    // Build table rows (same logic as before, but now with paginated data)
                    let tableBody = [];
                    let mobileCards = [];

                    function capitalizeWords(str) {
                        if (!str || str.trim() === '') return 'N/A';
                        return str.replace(/\b\w/g, function(char) {
                            return char.toUpperCase();
                        });
                    }

                    customers.forEach((customer) => {
                        let customerName = capitalizeWords(customer.name);
                        let imageUrl = customer.profile_image ?
                            '{{ env('ImagePath') . '/storage/' }}' + customer.profile_image :
                            '{{ env('ImagePath') . 'admin/assets/img/customer/customer5.jpg' }}';

                        // Delete button permission
                          let deleteBtn = '';
                                if (
                                    userRole !== 'sales-manager' &&
                                    userRole !== 'purchase-manager' &&
                                    userRole !== 'inventory-manager'
                                ) {
                                    @if (app('hasPermission')(9, 'delete'))
                                        deleteBtn = `
                                            <a class="me-2 confirm-text delete-customer" data-id="${customer.id}" href="javascript:void(0);">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5 20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V8H21V6H17V4C17 3.46957 16.7893 2.96086 16.4142 2.58579C16.0391 2.21071 15.5304 2 15 2H9C8.46957 2 7.96086 2.21071 7.58579 2.58579C7.21071 2.96086 7 3.46957 7 4V6H3V8H5V20ZM9 4H15V6H9V4ZM8 8H17V20H7V8H8Z" fill="#092C4C"></path>
                                                    <path d="M9 10H11V18H9V10ZM13 10H15V18H13V10Z" fill="#092C4C"></path>
                                                </svg>
                                            </a>`;
                                    @endif
                                }

                        let deleteDropdownItem = '';
                        if (
                            userRole !== 'sales-manager' &&
                            userRole !== 'purchase-manager' &&
                            userRole !== 'inventory-manager'
                        ) {
                            @if (app('hasPermission')(9, 'delete'))
                                deleteDropdownItem = `
                                    <a class="dropdown-item text-danger confirm-text delete-customer" data-id="${customer.id}" href="javascript:void(0);">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>`;
                            @endif
                        }

                        // Prepare action buttons
                                let actionItems = `
                                    @if (app('hasPermission')(9, 'view'))
                                        <a class="dropdown-item" href="/customer-view/${customer.id}">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                    @if (app('hasPermission')(9, 'edit'))
                                        <a class="dropdown-item" href="/edit-customer/${customer.id}">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                    ${deleteDropdownItem}
                                `;
                                let actionButtons = actionItems.trim() ? `
                                    <div class="dropdown listing-action-dropdown">
                                        <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end listing-action-menu">
                                            ${actionItems}
                                        </div>
                                    </div>
                                ` : '<span class="text-muted">N/A</span>';

                                // Mobile actions for collapsible section
                                let mobileActions = `
                                    <div class="mobile-actions">
                                        @if (app('hasPermission')(9, 'view'))
                                            <a href="/customer-view/${customer.id}">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 9C11.206 9.00524 10.4459 9.32299 9.88447 9.88447C9.32299 10.4459 9.00524 11.206 9 12C9 13.642 10.358 15 12 15C13.641 15 15 13.642 15 12C15 10.359 13.641 9 12 9Z" fill="#092C4C"></path>
                                                    <path d="M12 5C4.36704 5 2.07304 11.617 2.05204 11.684L1.94604 12L2.05105 12.316C2.07305 12.383 4.36704 19 12 19C19.633 19 21.927 12.383 21.948 12.316L22.054 12L21.949 11.684C21.927 11.617 19.633 5 12 5ZM12 17C6.64904 17 4.57604 13.154 4.07404 12C4.57804 10.842 6.65204 7 12 7C17.351 7 19.424 10.846 19.926 12C19.422 13.158 17.348 17 12 17Z" fill="#092C4C"></path>
                                                </svg>
                                            </a>
                                        @endif
                                        @if (app('hasPermission')(9, 'edit'))
                                            <a href="/edit-customer/${customer.id}">
                                                <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15.045 5.401C15.423 5.023 15.631 4.521 15.631 3.987C15.631 3.453 15.423 2.951 15.045 2.573L13.459 0.987001C13.081 0.609001 12.579 0.401001 12.045 0.401001C11.511 0.401001 11.009 0.609001 10.632 0.986001L0 11.585V16H4.413L15.045 5.401ZM12.045 2.401L13.632 3.986L12.042 5.57L10.456 3.985L12.045 2.401ZM2 14V12.415L9.04 5.397L10.626 6.983L3.587 14H2ZM0 18H16V20H0V18Z" fill="#092C4C"></path>
                                                </svg>
                                            </a>
                                        @endif
                                        ${deleteBtn}
                                    </div>
                                `;
                        let detailsToggle = `<a href="#details-${customer.id}" class="toggle-details" data-bs-toggle="collapse"><i class="fas fa-plus-circle" style="color: #ff9f43;"></i></a>`;

                        let firstColumn = `
                            <div>
                                <div style="display: flex; align-items: center;">
                                    <a href="/customer-view/${customer.id}" class="d-flex align-items-center">
                                        <img src="${imageUrl}" alt="customer" class="customer-image">
                                        <span class="customer-name truncated" style="">${customerName}</span>
                                    </a>
                                </div>
                                <div class="collapse mt-2 d-lg-none" id="details-${customer.id}">
                                    <div class="collapse-details">
                                        <div class="detail-item"><span class="detail-label">Code:</span><span class="detail-value email-value">${customer.customer_code || 'N/A'}</span></div>
                                        <div class="detail-item"><span class="detail-label">Email:</span><span class="detail-value email-value">${customer.email || 'N/A'}</span></div>
                                        <div class="detail-item"><span class="detail-label">Phone:</span><span class="detail-value">${customer.phone || 'N/A'}</span></div>
                                        <div class="detail-item"><span class="detail-label">Country:</span><span class="detail-value">${customer.country || 'N/A'}</span></div>
                                        <div class="detail-item"><span class="detail-label">City:</span><span class="detail-value">${customer.city || 'N/A'}</span></div>
                                        <div class="detail-item"><span class="detail-label">GST:</span><span class="detail-value">${customer.gst_number || 'N/A'}</span></div>
                                        <div class="detail-item"><span class="detail-label">PAN:</span><span class="detail-value">${customer.pan_number || 'N/A'}</span></div>
                                        ${mobileActions}
                                    </div>
                                </div>
                            </div>`;

                        mobileCards.push(buildCustomerCard(customer, customerName, actionButtons));

                        tableBody.push([
                            customer.customer_code || 'N/A',
                            firstColumn,
                            customer.email || 'N/A',
                            customer.phone || 'N/A',
                            customer.gst_number || 'N/A',
                            customer.pan_number || 'N/A',
                            customer.country || 'N/A',
                            customer.city || 'N/A',
                            actionButtons,
                            detailsToggle
                        ]);
                    });

                    table.clear().rows.add(tableBody).draw();
                    renderCustomerCards(mobileCards.join(''), 'No customers found');

                    // Sync top scrollbar (if you use it)
                    const topScroll = document.querySelector('.table-scroll-top');
                    const tableResponsive = document.querySelector('.table-responsive');
                    const tableElement = document.querySelector('.datanew');
                    if (topScroll && tableResponsive && tableElement) {
                        const topInnerDiv = topScroll.querySelector('div');
                        topInnerDiv.style.width = tableElement.scrollWidth + 'px';
                        topScroll.onscroll = () => tableResponsive.scrollLeft = topScroll.scrollLeft;
                        tableResponsive.onscroll = () => topScroll.scrollLeft = tableResponsive.scrollLeft;
                    }

                    $('.pagination-controls').show();
                } else {
                    table.clear().draw();
                    $(".datanew tbody").html('<tr><td colspan="10">No customers found</td></tr>');
                    renderCustomerCards('', 'No customers found');
                    $('.pagination-controls').hide();
                }
            },
            error: function(xhr) {
                console.error("Error fetching customers:", xhr);
                table.clear().draw();
                $(".datanew tbody").html('<tr><td colspan="10">Error loading customer data</td></tr>');
                renderCustomerCards('', 'Error loading customer data');
                $('.pagination-controls').hide();
            }
        });
    }

    function escCustomer(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function(ch) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
        });
    }

    function customerInitials(name) {
        const parts = String(name || '').trim().split(/\s+/).filter(Boolean);
        if (!parts.length) return '?';
        return ((parts[0][0] || '') + (parts.length > 1 ? parts[parts.length - 1][0] : '')).toUpperCase();
    }

    function buildCustomerCard(customer, customerName, actionButtons) {
        const viewUrl = `/customer-view/${customer.id}`;
        const initials = escCustomer(customerInitials(customer.name));
        const avatar = customer.profile_image
            ? `<img src="{{ env('ImagePath') . '/storage/' }}${escCustomer(customer.profile_image)}" alt="" loading="lazy" onerror="this.parentNode.innerHTML='<span>${initials}</span>'">`
            : `<span>${initials}</span>`;
        const phone = String(customer.phone || '').trim();
        const sub = [customer.customer_code, customer.city].filter(function(v) { return v && String(v).trim(); });
        const stat = function(label, value, extraClass) {
            const text = value && String(value).trim() ? escCustomer(value) : '—';
            return `<div class="cu-stat ${extraClass || ''}"><span>${label}</span><strong>${text}</strong></div>`;
        };

        return `
            <div class="cu-card">
                <a href="${viewUrl}" class="cu-avatar">${avatar}</a>
                <div class="cu-main">
                    <a href="${viewUrl}" class="cu-name">${escCustomer(customerName)}</a>
                    <div class="cu-sub">${sub.length ? sub.map(escCustomer).join(' &middot; ') : '&nbsp;'}</div>
                </div>
                <div class="cu-acts">
                    ${phone ? `<a href="tel:${escCustomer(phone)}" class="cu-call" title="Call"><i class="fas fa-phone-alt"></i></a>` : ''}
                    ${actionButtons}
                </div>
                <div class="cu-foot">
                    ${stat('Phone', phone)}
                    ${stat('GST', customer.gst_number)}
                    ${stat('Email', customer.email, 'cu-stat-wide')}
                </div>
            </div>`;
    }

    function renderCustomerCards(html, emptyText) {
        $('#customerCardList').html(html || `<div class="cu-empty"><i class="fas fa-users"></i><span>${escCustomer(emptyText)}</span></div>`);
    }

    function positionListingActionMenu(dropdownEl) {
        const $dropdown = $(dropdownEl);
        const $menu = $dropdown.data('floating-menu') || $dropdown.find('.listing-action-menu');
        const button = $dropdown.find('.action-menu-toggle')[0];

        if (!$menu.length || !button) {
            return;
        }

        const rect = button.getBoundingClientRect();
        const menuEl = $menu[0];
        const menuWidth = menuEl.offsetWidth || 168;
        const menuHeight = menuEl.offsetHeight || 220;
        const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
        const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
        const gap = 4;

        let left = rect.right - menuWidth;
        left = Math.max(8, Math.min(left, viewportWidth - menuWidth - 8));

        let top = rect.bottom + gap;
        if (top + menuHeight > viewportHeight - 8) {
            top = Math.max(8, rect.top - menuHeight - gap);
        }

        $menu.css({
            left: `${left}px`,
            position: 'fixed',
            top: `${top}px`,
            transform: 'none'
        });
    }

    $(document).on('show.bs.dropdown', '.listing-action-dropdown', function() {
        const $dropdown = $(this);
        const $menu = $dropdown.find('.listing-action-menu');

        if (!$menu.length) {
            return;
        }

        $dropdown.data('floating-menu', $menu);
        $dropdown.data('menu-parent', $menu.parent());
        $dropdown.data('menu-next', $menu.next());
        $menu.addClass('listing-action-menu-floating').appendTo('body');
    });

    $(document).on('shown.bs.dropdown', '.listing-action-dropdown', function() {
        positionListingActionMenu(this);
    });

    $(document).on('hidden.bs.dropdown', '.listing-action-dropdown', function() {
        const $dropdown = $(this);
        const $menu = $dropdown.data('floating-menu');
        const $parent = $dropdown.data('menu-parent');
        const $next = $dropdown.data('menu-next');

        if ($menu && $menu.length && $parent && $parent.length) {
            $menu.removeClass('listing-action-menu-floating').removeAttr('style');
            if ($next && $next.length && $.contains($parent[0], $next[0])) {
                $menu.insertBefore($next);
            } else {
                $parent.append($menu);
            }
        }

        $dropdown.removeData('floating-menu menu-parent menu-next');
    });

    $(window).on('scroll resize', function() {
        $('.listing-action-dropdown.show').each(function() {
            positionListingActionMenu(this);
        });
    });

    function updatePaginationUI(pagination) {
        let from = (pagination.current_page - 1) * pagination.per_page ;
        let to = pagination.current_page * pagination.per_page;
        if (to > pagination.total) to = pagination.total;
        if (pagination.total === 0) from = 0;

        $('#pagination-from').text(from);
        $('#pagination-to').text(to);
        $('#pagination-total').text(pagination.total);

        let paginationHtml = '';

        // Previous button
        paginationHtml += `
            <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page - 1}">Previous</a>
            </li>
        `;

        // Show only 3 page numbers at a time
        const visiblePageCount = 2;
        let startPage = Math.floor((pagination.current_page - 1) / visiblePageCount) * visiblePageCount + 1;
        let endPage = Math.min(pagination.last_page, startPage + visiblePageCount - 1);

        // Show previous ellipsis if there are pages before startPage
        if (startPage > 1) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0);" data-page="${startPage - 1}" data-action="prev-group">..</a>
                </li>
            `;
        }

        // Generate page numbers
        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `
                <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                </li>
            `;
        }

        // Show next ellipsis if there are more pages after endPage
        if (endPage < pagination.last_page) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0);" data-page="${endPage + 1}" data-action="next-group">..</a>
                </li>
            `;
        }

        // Next button
        paginationHtml += `
            <li class="page-item ${pagination.current_page === pagination.last_page || pagination.last_page === 0 ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0);" data-page="${pagination.current_page + 1}">Next</a>
            </li>
        `;

        $('#pagination-numbers').html(paginationHtml);
        $('.pagination-controls').show();
    }

    // Toggle details icon
    $(document).on('click', '.toggle-details', function() {
        let icon = $(this).find('i');
        if (icon.hasClass('fa-plus-circle')) {
            icon.removeClass('fa-plus-circle').addClass('fa-minus-circle').css('color', 'red');
        } else {
            icon.removeClass('fa-minus-circle').addClass('fa-plus-circle').css('color', '#ff9f43');
        }
    });

    // Delete customer (unchanged)
    $(document).on('click', '.delete-customer', function() {
        var customerId = $(this).data('id');
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ff9f43",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/deleteCustomer/${customerId}`,
                    type: 'POST',
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: "Deleted!",
                                text: response.message,
                                icon: "success",
                                confirmButtonColor: "#ff9f43",
                                confirmButtonText: "OK"
                            }).then(() => {
                                fetchCustomers(currentPage); // refresh current page
                            });
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: response.message,
                                icon: "error",
                                confirmButtonColor: "#ff9f43",
                                confirmButtonText: "OK"
                            });
                        }
                    },
                    error: function(xhr) {
                        let message = "Something went wrong!";
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            message = xhr.responseJSON.error;
                        }
                        Swal.fire({
                            title: "Error!",
                            text: message,
                            icon: "error",
                            confirmButtonColor: "#ff9f43",
                            confirmButtonText: "OK"
                        });
                    }
                });
            }
        });
    });

    // Reset details when switching to desktop
    $(window).on('resize', function() {
        if ($(window).width() > 768) {
            $('.collapse').removeClass('show');
            $('.toggle-details i')
                .removeClass('fa-minus-circle')
                .addClass('fa-plus-circle')
                .css('color', '#ff9f43');
        }
    });
});
</script>
@endpush
