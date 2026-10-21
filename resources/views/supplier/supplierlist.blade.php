    @extends('layout.app')

    @section('title', 'Vendor List')

    @section('content')
        <style>
            .sorting_1 {
                display: flex !important;
                align-items: center !important;
                gap: 5px !important;
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

            /* Desktop: show all columns normally */
            @media (min-width: 768px) {

                table.datanew thead th,
                table.datanew tbody td {
                    display: table-cell !important;
                }

                /* Hide the Details toggle column on desktop */
                table.datanew thead th.details-column,
                table.datanew tbody td:nth-child(9) {
                    display: none !important;
                }

                table.datanew tbody td:first-child {
                    max-width: 320px;
                    /* control column width */
                    width: 320px;
                    vertical-align: middle;
                }

                /* wrapper flex alignment */
                .vendor-wrapper {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    width: 100%;
                    min-width: 0;
                }

                /* image fixed size */
                .vendor-image {
                    width: 40px;
                    height: 40px;
                    min-width: 40px;
                    object-fit: cover;
                    border-radius: 50%;
                }

                /* TEXT WRAP MAGIC */
                .vendor-name {
                    display: block;
                    flex: 1;
                    min-width: 0;
                    white-space: normal;
                    word-break: break-word;
                    overflow-wrap: anywhere;
                    line-height: 1.3;
                    max-width: 100%;
                }
            }

            /* Phone layout (Customer style cards) */
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
                .cu-acts .vendor-action-dropdown {
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
            }

            /* Vendor specific styling */
            .vendor-image {
                width: 40px;
                height: 40px;
                object-fit: cover;
                border-radius: 50%;
                margin-right: 10px;
            }

            /* Collapsible details styling */
            /* .collapse-details {
        margin-top: 10px;
        padding: 12px;
        background-color: #f8f9fa;
        border-radius: 6px;
        border-left: 3px solid #ff9f43;
        width: 210px;
        max-width: 100%;
    } */

    /* Each row */
    .detail-item {
        display: flex;
        flex-direction: row;
        margin-bottom: 8px;
        gap: 2px;
    }

    /* Label styling */
    .detail-label {
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
    }

    /* Value styling + WORD WRAP FIX */
    .detail-value {
        font-size: 13px;
        color: #212529;
        word-break: break-word;     /* main fix */
        overflow-wrap: break-word;  /* modern support */
        white-space: normal;        /* allow wrapping */
        line-height: 1.4;
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

            .mobile-actions img {
                width: 18px;
                height: 18px;
                object-fit: contain;
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

            .table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table-container table thead th {
                white-space: nowrap;
            }
            /* table.datanew td:nth-child(8) {
                align-items: center;
                gap: 8px;
            } */
            /* Action column fix */
            table.datanew td:nth-child(8),
            table.datanew th:nth-child(8) {
                width: 120px !important;      /* fixed width */
                min-width: 120px;
                max-width: 120px;
                text-align: center;
                vertical-align: middle;
            }

            /* Action buttons alignment */
            .action-buttons {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 10px;
                white-space: nowrap; /* prevent wrapping */
            }

            /* Icons size fix */
            .action-buttons img {
                width: 20px;
                height: 18px;
            }

            .vendor-action-dropdown {
                display: inline-flex;
                justify-content: center;
                width: 100%;
            }

            .vendor-action-dropdown .action-menu-toggle {
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

            .vendor-action-dropdown .action-menu-toggle:hover,
            .vendor-action-dropdown .action-menu-toggle:focus {
                background: #f8fafc;
                border-color: #b8c2d2;
                color: #1b2850;
            }

            .vendor-action-menu {
                border: 1px solid #e8ebed;
                border-radius: 6px;
                box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
                min-width: 168px;
                padding: 6px;
                z-index: 999999999999;
            }

            .vendor-action-menu-floating {
                position: fixed !important;
                z-index: 999999999999 !important;
            }

            .vendor-action-menu .dropdown-item {
                align-items: center;
                border-radius: 4px;
                color: #344054;
                display: flex;
                font-size: 12px;
                gap: 8px;
                padding: 7px 8px;
                width: 100%;
            }

            .vendor-action-menu .dropdown-item i {
                color: #667085;
                font-size: 13px;
                text-align: center;
                width: 15px;
            }

            .vendor-action-menu .dropdown-item.text-danger,
            .vendor-action-menu .dropdown-item.text-danger i {
                color: #ea5455 !important;
            }

            .vendor-action-menu .dropdown-item:hover {
                background: #f4f6f8;
                color: #1b2850;
            }

        </style>

        <div class="content cu-page">
            <div class="cu-m-head d-md-none">
                <div class="cu-m-title">All Vendors</div>
                <div class="cu-m-actions">
                    <a href="{{ route('vendor.import') }}" class="btn btn-sm cu-m-import">
                        <i class="fas fa-file-import me-1"></i> Import
                    </a>
                    @if (app('hasPermission')(10, 'add'))
                        <a href="{{ route('vendor.add') }}" class="btn btn-sm cu-m-new">
                            <i class="fas fa-plus me-1"></i> Add Vendor
                        </a>
                    @endif
                </div>
            </div>

            <div class="page-header">
                <div class="page-title">
                    <h4>All Vendors</h4>
                </div>
                <div class="page-btn d-flex">
                    <a href="{{ route('vendor.import') }}" class="btn  btn-added me-2"> <i class="fas fa-file-import me-2"></i>Import Vendors</a>
                    @if (app('hasPermission')(10, 'add'))
                        <a href="{{ route('vendor.add') }}" class="btn btn-added btn-sm">
                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/plus.svg' }}" class="me-1" alt="img">Add
                            Vendor
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

                    <div class="table-container">
                        <table class="table datanew">
                            <thead>
                                <tr>
                                    <th>Vendor Name</th>
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

                    <div class="cu-card-list d-md-none" id="vendorCardList"></div>

                    <!-- Pagination Controls -->
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

        <script>
            // $(document).ready(function() {
            //     var authToken = localStorage.getItem("authToken");
            //     var table = $('.datanew').DataTable(); // Initialize DataTable
            //     const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
            //     let url = "/api/getAllSupplier";
            //     if (selectedSubAdminId) {
            //         url += `?selectedSubAdminId=${selectedSubAdminId}`;
            //     }

            //     fetchVendors(); // Call function to load data

            //     function fetchVendors() {
            //         $.ajax({
            //             url: url,
            //             type: "GET",
            //             dataType: "json",
            //             headers: {
            //                 "Authorization": "Bearer " + authToken,
            //             },
            //             success: function(response) {
            //                 if (response.status) {
            //                     let vendors = response.data;
            //                     let tableBody = [];

            //                     // Function to capitalize first letter of each word
            //                     function capitalizeWords(str) {
            //                         if (!str || str.trim() === '') return 'N/A';
            //                         return str.replace(/\b\w/g, function(char) {
            //                             return char.toUpperCase();
            //                         });
            //                     }

            //                     vendors.forEach((vendor) => {
            //                         let vendorName = capitalizeWords(vendor.name);

            //                         // Details toggle for mobile
            //                         let detailsToggle = `
            //                         <a href="#details-${vendor.id}" class="toggle-details" data-bs-toggle="collapse">
            //                             <i class="fas fa-plus-circle" style="color: #ff9f43;"></i>
            //                         </a>
            //                     `;

            //                         // Image URL
            //                         let imageUrl = vendor.profile_image ?
            //                             '{{ env('ImagePath') . '/storage/' }}' + vendor
            //                             .profile_image :
            //                             '{{ env('ImagePath') . 'admin/assets/img/customer/customer5.jpg' }}';

            //                         // Prepare delete button conditionally
            //                         let deleteBtn = '';
            //                         if (
            //                             userRole !== 'sales-manager' &&
            //                             userRole !== 'purchase-manager' &&
            //                             userRole !== 'inventory-manager'
            //                         ) {
            //                             @if (app('hasPermission')(10, 'delete'))
            //                                 deleteBtn = `
            //                             <a class="me-2 delete-vendor" data-id="${vendor.id}" href="javascript:void(0);">
            //                                 <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}" alt="Delete">
            //                             </a>`;
            //                             @endif
            //                         }

            //                         // Prepare action buttons
            //                         let actionButtons = `
            //                         @if (app('hasPermission')(10, 'view'))
            //                             <a class="me-2" href="/vendor-view/${vendor.id}">
            //                                 <img src="{{ env('ImagePath') . 'admin/assets/img/icons/eye.svg' }}" alt="View">
            //                             </a>
            //                         @endif
            //                         @if (app('hasPermission')(10, 'edit'))
            //                             <a class="me-2" href="/edit-vendor/${vendor.id}">
            //                                 <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}" alt="Edit">
            //                             </a>
            //                         @endif
            //                         ${deleteBtn}
            //                     `;

            //                         // Mobile actions for collapsible section
            //                         let mobileActions = `
            //                         <div class="mobile-actions">
            //                             @if (app('hasPermission')(10, 'view'))
            //                                 <a href="/vendor-view/${vendor.id}">
            //                                     <img src="{{ env('ImagePath') . 'admin/assets/img/icons/eye.svg' }}" alt="View">
            //                                 </a>
            //                             @endif
            //                             @if (app('hasPermission')(10, 'edit'))
            //                                 <a href="/edit-vendor/${vendor.id}">
            //                                     <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}" alt="Edit">
            //                                 </a>
            //                             @endif
            //                             ${deleteBtn}
            //                         </div>
            //                     `;

            //                         tableBody.push([
            //                             // Column 1: Vendor Name with image AND collapsible details
            //                             `<div>
            //                             <div class="vendor-wrapper">
            //                                 <img src="${imageUrl}" class="vendor-image" alt="vendor">
            //                                 <span class="vendor-name" style="width: 75px;">${vendorName}</span>
            //                             </div>
            //                             <!-- Collapsible Details (visible only on mobile) -->
            //                             <div class="collapse mt-2 d-lg-none" id="details-${vendor.id}">
            //                                 <div class="collapse-details">
            //                                     <div class="detail-item">
            //                                         <span class="detail-label">Email:</span>
            //                                         <span class="detail-value">${vendor.email || 'N/A'}</span>
            //                                     </div>
            //                                     <div class="detail-item">
            //                                         <span class="detail-label">Phone:</span>
            //                                         <span class="detail-value">${vendor.phone || 'N/A'}</span>
            //                                     </div>

            //                                     <div class="detail-item">
            //                                         <span class="detail-label">Country:</span>
            //                                         <span class="detail-value">${vendor.country || 'N/A'}</span>
            //                                     </div>
            //                                     <div class="detail-item">
            //                                         <span class="detail-label">City:</span>
            //                                         <span class="detail-value">${vendor.city || 'N/A'}</span>
            //                                     </div>
            //                                     <div class="detail-item">
            //                                         <span class="detail-label">GST:</span>
            //                                         <span class="detail-value">${vendor.gst_number || 'N/A'}</span>
            //                                     </div>
            //                                     <div class="detail-item">
            //                                         <span class="detail-label">PAN:</span>
            //                                         <span class="detail-value">${vendor.pan_number || 'N/A'}</span>
            //                                     </div>

            //                                     ${mobileActions}
            //                                 </div>
            //                             </div>
            //                         </div>`,

            //                             // Column 5: Email
            //                             vendor.email || 'N/A',

            //                             // Column 2: Phone
            //                             vendor.phone || 'N/A',

            //                             // Column 3: GST Number
            //                             vendor.gst_number || 'N/A',

            //                             // Column 4: PAN Number
            //                             vendor.pan_number || 'N/A',

            //                             // Column 6: Country
            //                             vendor.country || 'N/A',

            //                             // Column 7: City
            //                             vendor.city || 'N/A',

            //                             // Column 8: Action Buttons (hidden on mobile)
            //                             actionButtons,

            //                             // Column 9: Details Toggle (only for mobile)
            //                             detailsToggle
            //                         ]);
            //                     });

            //                     table.clear().rows.add(tableBody).draw();

            //                     // Sync top scrollbar
            //                     const topScroll = document.querySelector('.table-scroll-top');
            //                     const tableResponsive = document.querySelector('.table-responsive');
            //                     const tableElement = document.querySelector('.datanew');

            //                     if (topScroll && tableResponsive && tableElement) {
            //                         const topInnerDiv = topScroll.querySelector('div');
            //                         topInnerDiv.style.width = tableElement.scrollWidth + 'px';

            //                         topScroll.onscroll = () => {
            //                             tableResponsive.scrollLeft = topScroll.scrollLeft;
            //                         };
            //                         tableResponsive.onscroll = () => {
            //                             topScroll.scrollLeft = tableResponsive.scrollLeft;
            //                         };
            //                     }
            //                 } else {
            //                     table.clear().draw();
            //                     $(".datanew tbody").html('<tr><td colspan="9">No vendors found</td></tr>');
            //                 }
            //             },
            //             error: function(xhr) {
            //                 // console.log("Error:", xhr);
            //                 table.clear().draw();
            //                 $(".datanew tbody").html(
            //                     '<tr><td colspan="9">Error loading vendor data</td></tr>');
            //             },
            //         });
            //     }

            //     // Toggle details icon
            //     $(document).on('click', '.toggle-details', function() {
            //         let icon = $(this).find('i');
            //         if (icon.hasClass('fa-plus-circle')) {
            //             icon.removeClass('fa-plus-circle')
            //                 .addClass('fa-minus-circle')
            //                 .css('color', 'red');
            //         } else {
            //             icon.removeClass('fa-minus-circle')
            //                 .addClass('fa-plus-circle')
            //                 .css('color', '#ff9f43');
            //         }
            //     });

            //     // Delete vendor function
            //     $(document).on('click', '.delete-vendor', function() {
            //         var vendorId = $(this).data('id');
            //         Swal.fire({
            //             title: "Are you sure?",
            //             text: "You won't be able to revert this!",
            //             icon: "warning",
            //             showCancelButton: true,
            //             confirmButtonColor: "#ff9f43",
            //             cancelButtonColor: "#6c757d",
            //             confirmButtonText: "Yes, delete it!"
            //         }).then((result) => {
            //             if (result.isConfirmed) {
            //                 $.ajax({
            //                     url: `/api/deleteSupplier/${vendorId}`,
            //                     type: 'POST',
            //                     headers: {
            //                         "Authorization": "Bearer " + authToken,
            //                     },
            //                     data: {
            //                         _token: $('meta[name="csrf-token"]').attr('content')
            //                     },
            //                     success: function(response) {
            //                         if (response.status) {
            //                             Swal.fire({
            //                                 title: "Deleted!",
            //                                 text: response.message,
            //                                 icon: "success",
            //                                 confirmButtonColor: "#ff9f43",
            //                                 confirmButtonText: "OK"
            //                             }).then(() => {
            //                                 fetchVendors(); // Refresh the table
            //                             });
            //                         } else {
            //                             Swal.fire({
            //                                 title: "Error!",
            //                                 text: response.message,
            //                                 icon: "error",
            //                                 confirmButtonColor: "#ff9f43",
            //                                 confirmButtonText: "OK"
            //                             });
            //                         }
            //                     },
            //                     error: function(xhr) {
            //                         let message = "Something went wrong!";
            //                         if (xhr.responseJSON && xhr.responseJSON.error) {
            //                             message = xhr.responseJSON.error;
            //                         }

            //                         Swal.fire({
            //                             title: "Error!",
            //                             text: message,
            //                             icon: "error",
            //                             confirmButtonColor: "#ff9f43",
            //                             confirmButtonText: "OK"
            //                         });
            //                     }
            //                 });
            //             }
            //         });
            //     });

            //     // Reset details when switching to desktop
            //     $(window).on('resize', function() {
            //         if ($(window).width() > 768) {
            //             // Close all collapsed sections
            //             $('.collapse').removeClass('show');

            //             // Reset all toggle icons to plus
            //             $('.toggle-details i')
            //                 .removeClass('fa-minus-circle')
            //                 .addClass('fa-plus-circle')
            //                 .css('color', '#ff9f43');
            //         }
            //     });
            // });
            $(document).ready(function() {
        var authToken = localStorage.getItem("authToken");
        var $table = $('.datanew');
        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().clear().destroy();
        }
        var table = $table.DataTable({
            paging: false,
            info: false,
            searching: false,
            dom: 't',
            ordering: false,
        });

        const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

        // Pagination state
        let currentPage = 1;
        let lastPage = 1;
        let perPage = 10;
        let searchQuery = '';

        // Initial fetch
        fetchVendors(currentPage);

        // Search input handler
        $('#search-input').on('keyup', function() {
            searchQuery = $(this).val();
            fetchVendors(1);
        });

        // Per-page change handler
        $('#per-page-select').on('change', function() {
            perPage = $(this).val();
            fetchVendors(1);
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
                    fetchVendors(page);
                }
                return;
            }

            if (action === 'prev-group') {
                // Load the previous group's starting page
                let prevStartPage = Math.max(1, page - 2);
                if (prevStartPage >= 1 && prevStartPage <= lastPage) {
                    fetchVendors(prevStartPage);
                }
                return;
            }

            // Regular page navigation
            if (page && page !== currentPage && page >= 1 && page <= lastPage) {
                fetchVendors(page);
            }
        });

        function fetchVendors(page = 1) {
            let url = `/api/getAllSupplier?page=${page}&per_page=${perPage}`;
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
                        let vendors = response.data;
                        let pagination = response.pagination;

                        // Update state
                        currentPage = pagination.current_page;
                        lastPage = pagination.last_page;

                        // Update pagination UI
                        updatePaginationUI(pagination);

                        // Build table rows
                        let tableBody = [];
                        let mobileCards = [];

                        function capitalizeWords(str) {
                            if (!str || str.trim() === '') return 'N/A';
                            return str.replace(/\b\w/g, function(char) {
                                return char.toUpperCase();
                            });
                        }

                        vendors.forEach((vendor) => {
                            let vendorName = capitalizeWords(vendor.name);
                            let imageUrl = vendor.profile_image ?
                                '{{ env('ImagePath') . '/storage/' }}' + vendor.profile_image :
                                '{{ env('ImagePath') . 'admin/assets/img/customer/customer5.jpg' }}';

                            let menuItems = '';
                            let deleteBtn = '';
                            let mobileDeleteBtn = '';
                            @if (app('hasPermission')(10, 'view'))
                                menuItems += `<a class="dropdown-item" href="/vendor-view/${vendor.id}">
                                    <i class="fas fa-eye"></i><span>View</span>
                                </a>`;
                            @endif
                            @if (app('hasPermission')(10, 'edit'))
                                menuItems += `<a class="dropdown-item" href="/edit-vendor/${vendor.id}">
                                    <i class="fas fa-edit"></i><span>Edit</span>
                                </a>`;
                            @endif
                            if (
                                userRole !== 'sales-manager' &&
                                userRole !== 'purchase-manager' &&
                                userRole !== 'inventory-manager'
                            ) {
                                @if (app('hasPermission')(10, 'delete'))
                                    deleteBtn = `<a class="dropdown-item text-danger delete-vendor" data-id="${vendor.id}" href="javascript:void(0);">
                                        <i class="fas fa-trash"></i><span>Delete</span>
                                    </a>`;
                                    mobileDeleteBtn = `
                                        <a href="javascript:void(0);" class="delete-vendor" data-id="${vendor.id}">
                                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/delete.svg' }}" alt="Delete">
                                        </a>`;
                                    menuItems += deleteBtn;
                                @endif
                            }
                            if (!menuItems) {
                                menuItems = '<span class="dropdown-item text-muted">No actions</span>';
                            }

                            let actionButtons = `
                                <div class="dropdown vendor-action-dropdown">
                                    <button class="btn action-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end vendor-action-menu">
                                        ${menuItems}
                                    </div>
                                </div>
                            `;

                            mobileCards.push(buildVendorCard(vendor, vendorName, actionButtons));

                            // Mobile actions for collapsible section
                            let mobileActions = `
                                <div class="mobile-actions">
                                    @if (app('hasPermission')(10, 'view'))
                                        <a href="/vendor-view/${vendor.id}">
                                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/eye.svg' }}" alt="View">
                                        </a>
                                    @endif
                                    @if (app('hasPermission')(10, 'edit'))
                                        <a href="/edit-vendor/${vendor.id}">
                                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/edit.svg' }}" alt="Edit">
                                        </a>
                                    @endif
                                    ${mobileDeleteBtn}
                                </div>
                            `;

                            let detailsToggle = `<a href="#details-${vendor.id}" class="toggle-details" data-bs-toggle="collapse"><i class="fas fa-plus-circle" style="color: #ff9f43;"></i></a>`;

                            let firstColumn = `
                                <div>
                                    <a href="/vendor-view/${vendor.id}" class="vendor-wrapper d-flex">
                                        <img src="${imageUrl}" class="vendor-image" alt="vendor">
                                        <span class="vendor-name">${vendorName}</span>
                                    </a>
                                    <div class="collapse mt-2 d-lg-none" id="details-${vendor.id}">
                                        <div class="collapse-details">
                                            <div class="detail-item"><span class="detail-label">Email:</span><span class="detail-value">${vendor.email || 'N/A'}</span></div>
                                            <div class="detail-item"><span class="detail-label">Phone:</span><span class="detail-value">${vendor.phone || 'N/A'}</span></div>
                                            <div class="detail-item"><span class="detail-label">Country:</span><span class="detail-value">${vendor.country || 'N/A'}</span></div>
                                            <div class="detail-item"><span class="detail-label">City:</span><span class="detail-value">${vendor.city || 'N/A'}</span></div>
                                            <div class="detail-item"><span class="detail-label">GST:</span><span class="detail-value">${vendor.gst_number || 'N/A'}</span></div>
                                            <div class="detail-item"><span class="detail-label">PAN:</span><span class="detail-value">${vendor.pan_number || 'N/A'}</span></div>
                                            ${mobileActions}
                                        </div>
                                    </div>
                                </div>`;

                            tableBody.push([
                                firstColumn,
                                vendor.email || 'N/A',
                                vendor.phone || 'N/A',
                                vendor.gst_number || 'N/A',
                                vendor.pan_number || 'N/A',
                                vendor.country || 'N/A',
                                vendor.city || 'N/A',
                                actionButtons,
                                detailsToggle
                            ]);
                        });

                        table.clear().rows.add(tableBody).draw();
                        renderVendorCards(mobileCards.join(''), 'No vendors found');

                        // Sync top scrollbar (if you use it)
                        const topScroll = document.querySelector('.table-scroll-top');
                        const tableContainer = document.querySelector('.table-container');
                        const tableElement = document.querySelector('.datanew');
                        if (topScroll && tableContainer && tableElement) {
                            const topInnerDiv = topScroll.querySelector('div');
                            topInnerDiv.style.width = tableElement.scrollWidth + 'px';
                            topScroll.onscroll = () => tableContainer.scrollLeft = topScroll.scrollLeft;
                            tableContainer.onscroll = () => topScroll.scrollLeft = tableContainer.scrollLeft;
                        }

                        $('.pagination-controls').show();
                    } else {
                        table.clear().draw();
                        $(".datanew tbody").html('<tr><td colspan="9">No vendors found</td></tr>');
                        renderVendorCards('', 'No vendors found');
                        $('.pagination-controls').hide();
                    }
                },
                error: function(xhr) {
                    console.error("Error fetching vendors:", xhr);
                    table.clear().draw();
                    $(".datanew tbody").html('<tr><td colspan="9">Error loading vendor data</td></tr>');
                    renderVendorCards('', 'Error loading vendor data');
                    $('.pagination-controls').hide();
                }
            });
        }

        function escVendor(value) {
            return String(value == null ? '' : value).replace(/[&<>"']/g, function(ch) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
            });
        }

        function vendorInitials(name) {
            const parts = String(name || '').trim().split(/\s+/).filter(Boolean);
            if (!parts.length) return '?';
            return ((parts[0][0] || '') + (parts.length > 1 ? parts[parts.length - 1][0] : '')).toUpperCase();
        }

        function buildVendorCard(vendor, vendorName, actionButtons) {
            const viewUrl = `/vendor-view/${vendor.id}`;
            const initials = escVendor(vendorInitials(vendor.name));
            const avatar = vendor.profile_image
                ? `<img src="{{ env('ImagePath') . '/storage/' }}${escVendor(vendor.profile_image)}" alt="" loading="lazy" onerror="this.parentNode.innerHTML='<span>${initials}</span>'">`
                : `<span>${initials}</span>`;
            const phone = String(vendor.phone || '').trim();
            const sub = [vendor.city, vendor.country].filter(function(v) { return v && String(v).trim(); });
            const stat = function(label, value, extraClass) {
                const text = value && String(value).trim() ? escVendor(value) : '—';
                return `<div class="cu-stat ${extraClass || ''}"><span>${label}</span><strong>${text}</strong></div>`;
            };

            return `
                <div class="cu-card">
                    <a href="${viewUrl}" class="cu-avatar">${avatar}</a>
                    <div class="cu-main">
                        <a href="${viewUrl}" class="cu-name">${escVendor(vendorName)}</a>
                        <div class="cu-sub">${sub.length ? sub.map(escVendor).join(' &middot; ') : '&nbsp;'}</div>
                    </div>
                    <div class="cu-acts">
                        {{-- Call button commented out:
                        ${phone ? `<a href="tel:${escVendor(phone)}" class="cu-call" title="Call"><i class="fas fa-phone-alt"></i></a>` : ''}
                        --}}
                        ${actionButtons}
                    </div>
                    <div class="cu-foot">
                        ${stat('Phone', phone)}
                        ${stat('GST', vendor.gst_number)}
                        ${stat('Email', vendor.email, 'cu-stat-wide')}
                    </div>
                </div>`;
        }

        function renderVendorCards(html, emptyText) {
            $('#vendorCardList').html(html || `<div class="cu-empty"><i class="fas fa-users"></i><span>${escVendor(emptyText)}</span></div>`);
        }

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

        function positionVendorActionMenu(dropdownEl) {
            const $dropdown = $(dropdownEl);
            const $menu = $dropdown.data('floating-menu') || $dropdown.find('.vendor-action-menu');
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

        $(document).on('show.bs.dropdown', '.vendor-action-dropdown', function() {
            const $dropdown = $(this);
            const $menu = $dropdown.find('.vendor-action-menu');

            if (!$menu.length) {
                return;
            }

            $dropdown.data('floating-menu', $menu);
            $dropdown.data('menu-parent', $menu.parent());
            $dropdown.data('menu-next', $menu.next());
            $menu.addClass('vendor-action-menu-floating').appendTo('body');
        });

        $(document).on('shown.bs.dropdown', '.vendor-action-dropdown', function() {
            positionVendorActionMenu(this);
        });

        $(document).on('hidden.bs.dropdown', '.vendor-action-dropdown', function() {
            const $dropdown = $(this);
            const $menu = $dropdown.data('floating-menu');
            const $parent = $dropdown.data('menu-parent');
            const $next = $dropdown.data('menu-next');

            if ($menu && $menu.length && $parent && $parent.length) {
                $menu.removeClass('vendor-action-menu-floating').removeAttr('style');
                if ($next && $next.length && $.contains($parent[0], $next[0])) {
                    $menu.insertBefore($next);
                } else {
                    $parent.append($menu);
                }
            }

            $dropdown.removeData('floating-menu menu-parent menu-next');
        });

        $(window).on('scroll resize', function() {
            $('.vendor-action-dropdown.show').each(function() {
                positionVendorActionMenu(this);
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

        // Delete vendor function (unchanged)
        $(document).on('click', '.delete-vendor', function() {
            var vendorId = $(this).data('id');
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
                        url: `/api/deleteSupplier/${vendorId}`,
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
                                    fetchVendors(currentPage); // refresh current page
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
