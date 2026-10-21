@php
    $user = auth()->user();
    $settings = $settings ?? \App\Models\Setting::first();
    $isStaff = $user && $user->role === 'staff';

    $showErpSection =
        app('hasPermission')(1, 'view') || app('hasPermission')(1, 'add') ||
        app('hasPermission')(2, 'view') || app('hasPermission')(2, 'add') ||
        app('hasPermission')(3, 'view') || app('hasPermission')(3, 'add') ||
        app('hasPermission')(10, 'view') || app('hasPermission')(10, 'add') ||
        app('hasPermission')(40, 'view') || app('hasPermission')(40, 'add') ||
        app('hasPermission')(17, 'view') || app('hasPermission')(41, 'view') ||
        (app('hasPermission')(2, 'add') && app('hasPermission')(2, 'edit')) ||
        (app('hasPermission')(3, 'add') && app('hasPermission')(3, 'edit'));

    $showCrmSection =
        app('hasPermission')(9, 'view') || app('hasPermission')(9, 'add') ||
        app('hasPermission')(32, 'view') || app('hasPermission')(32, 'add') ||
        app('hasPermission')(30, 'view') || app('hasPermission')(30, 'add') ||
        app('hasPermission')(31, 'view') || app('hasPermission')(31, 'add') ||
        app('hasPermission')(33, 'view') || app('hasPermission')(33, 'add');

    $showAccountingSection =
        app('hasPermission')(42, 'view') || app('hasPermission')(43, 'view') ||
        app('hasPermission')(44, 'view') || app('hasPermission')(45, 'view') ||
        app('hasPermission')(46, 'view') || app('hasPermission')(46, 'add') ||
        app('hasPermission')(47, 'view') || app('hasPermission')(48, 'view') ||
        app('hasPermission')(5, 'view') || app('hasPermission')(5, 'add') ||
        app('hasPermission')(20, 'view') ||
        app('hasPermission')(34, 'view') || app('hasPermission')(35, 'view') ||
        app('hasPermission')(36, 'view') || app('hasPermission')(37, 'view') ||
        app('hasPermission')(38, 'view');
@endphp

<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <style>
                #sidebar-menu .sidebar-search-wrap {
                    display: flex;
                    align-items: center;
                    box-sizing: border-box;
                    width: 100%;
                    height: 40px;
                    margin: 0 0 18px;
                    padding: 0 14px;
                    background: #fff;
                    border: 1px solid #d1d5db;
                    border-radius: 6px;
                    box-shadow: none;
                }
                #sidebar-menu .sidebar-search-wrap:focus-within {
                    border-color: #9ca3af;
                }
                #sidebar-menu .sidebar-search-icon {
                    flex-shrink: 0;
                    margin-right: 10px;
                    color: #9ca3af;
                    font-size: 14px;
                    line-height: 1;
                }
                #sidebar-menu #sidebar-module-search {
                    flex: 1;
                    width: 100%;
                    min-width: 0;
                    height: 100%;
                    margin: 0;
                    padding: 0;
                    border: none !important;
                    outline: none !important;
                    background: transparent !important;
                    box-shadow: none !important;
                    font-size: 14px;
                    font-weight: 400;
                    color: #374151;
                    -webkit-appearance: none;
                    appearance: none;
                }
                #sidebar-menu #sidebar-module-search::placeholder {
                    color: #9ca3af;
                    opacity: 1;
                }
                #sidebar-menu #sidebar-module-search:focus {
                    border: none !important;
                    outline: none !important;
                    box-shadow: none !important;
                }
                .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a {
                    background: #f1dadaff !important;
                    color: #092c4c !important;
                    border-left: 3px solid #f82011ff !important;
                    border-radius: 8px !important;
                    font-weight: 600 !important;
                    padding: 10px 15px !important;
                    display: flex !important;
                    align-items: center !important;
                }
                .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a img {
                    filter: none !important;
                    margin-right: 0 !important;
                }
                .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a span {
                    font-weight: 600 !important;
                    color: #092c4c !important;
                }
                .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a.active {
                    background: #1b2850 !important;
                    color: #fff !important;
                }
                .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a.active span {
                    color: #fff !important;
                }
                .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a.active img {
                    filter: brightness(0) invert(1) !important;
                }
                .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a:hover:not(.active) {
                    background: rgba(241, 195, 195, 1) !important;
                }
                
                /* Dark Mode Fix for Dashboard Sidebar Item */
                html[data-theme="dark"] .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a,
                [data-layout-mode="dark"] .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a,
                .dark .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a,
                body.dark .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a {
                    background: transparent !important;
                    color: #ffffff !important;
                    border-left-color: #ff9f43 !important;
                }
                
                html[data-theme="dark"] .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a span,
                [data-layout-mode="dark"] .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a span,
                .dark .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a span,
                body.dark .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a span {
                    color: #ffffff !important;
                }
                
                html[data-theme="dark"] .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a img,
                [data-layout-mode="dark"] .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a img,
                .dark .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a img,
                body.dark .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a img {
                    filter: brightness(0) invert(1) !important;
                }
                
                html[data-theme="dark"] .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a:hover:not(.active),
                [data-layout-mode="dark"] .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a:hover:not(.active),
                .dark .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a:hover:not(.active),
                body.dark .sidebar .sidebar-menu>ul>li.sidebar-category-dashboard>a:hover:not(.active) {
                    background: rgba(255, 255, 255, 0.05) !important;
                }

                /* Responsive Sidebar Scrollbar Fix */
                @media (max-width: 991px) {
                    #sidebar.sidebar {
                        top: 60px !important;
                        bottom: 60px !important;
                        height: calc(100vh - 120px) !important;
                        height: calc(100dvh - 120px) !important;
                        max-height: calc(100vh - 120px) !important;
                        max-height: calc(100dvh - 120px) !important;
                        overflow: hidden !important;
                        display: flex !important;
                        flex-direction: column !important;
                    }

                    #sidebar.sidebar .slimScrollDiv {
                        width: 100% !important;
                        height: 100% !important;
                        max-height: 100% !important;
                        flex: 1 1 auto !important;
                        overflow: hidden !important;
                    }

                    #sidebar.sidebar .sidebar-inner,
                    #sidebar.sidebar .slimScrollDiv > .sidebar-inner {
                        width: 100% !important;
                        height: 100% !important;
                        max-height: 100% !important;
                        overflow-y: auto !important;
                        overflow-x: hidden !important;
                        -webkit-overflow-scrolling: touch !important;
                        overscroll-behavior-y: contain;
                    }

                    #sidebar.sidebar .slimScrollBar,
                    #sidebar.sidebar .slimScrollRail {
                        display: none !important;
                    }

                    #sidebar.sidebar .sidebar-inner::-webkit-scrollbar {
                        width: 5px;
                    }

                    #sidebar.sidebar .sidebar-inner::-webkit-scrollbar-track {
                        background: transparent;
                    }

                    #sidebar.sidebar .sidebar-inner::-webkit-scrollbar-thumb {
                        background: rgba(0, 0, 0, 0.2);
                        border-radius: 4px;
                    }

                    #sidebar.sidebar .sidebar-inner::-webkit-scrollbar-thumb:hover {
                        background: rgba(0, 0, 0, 0.35);
                    }

                    #sidebar.sidebar .sidebar-menu {
                        padding-bottom: 40px !important;
                    }
                }
            </style>
            <div class="sidebar-search-wrap">
                <i class="fas fa-search sidebar-search-icon"></i>
                <input type="text" id="sidebar-module-search" class="sidebar-search-input"
                    placeholder="Search..." autocomplete="off">
            </div>
            <ul class="sidebar-nav-list">
                {{-- Dashboard --}}
                @if ($isStaff || app('hasPermission')(0, 'view'))
                    <li class="sidebar-category sidebar-category-dashboard">
                        @if ($isStaff)
                            <a href="{{ route('auth.staff-dashboard') }}"
                               class="{{ request()->routeIs('auth.staff-dashboard') ? 'active' : '' }}">
                                <img src="{{ image_path('admin/assets/img/icons/dashboard.svg') }}" alt="img">
                                <span>Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('auth.dashboard') }}"
                               class="{{ request()->routeIs('auth.dashboard') ? 'active' : '' }}">
                                <img src="{{ image_path('admin/assets/img/icons/dashboard.svg') }}" alt="img">
                                <span>Dashboard</span>
                            </a>
                        @endif
                    </li>
                @endif

                {{-- ====== ERP ====== --}}
                @if ($showErpSection)
                    <li class="submenu sidebar-category sidebar-category-erp">
                        <a href="javascript:void(0);">
                            <i class="fas fa-industry"></i>
                            <span>ERP</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                    {{-- Products --}}
                    @if (app('hasPermission')(1, 'view') || app('hasPermission')(1, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <img src="{{ image_path('admin/assets/img/icons/product.svg') }}" alt="img">
                                <span>Products</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(1, 'view'))
                                    <li><a href="{{ route('product.list') }}" data-search="products all products list erp" class="{{ request()->routeIs('product.list') && !request()->boolean('deleted') ? 'active' : '' }}">All Products</a></li>
                                    <li><a href="{{ route('product.list', ['deleted' => 1]) }}" data-search="products deleted products trash restore erp" class="{{ request()->routeIs('product.list') && request()->boolean('deleted') ? 'active' : '' }}">Deleted Products</a></li>
                                @endif
                                @if (app('hasPermission')(1, 'add'))
                                    <li><a href="{{ route('product.add') }}" data-search="products new product add erp">New Product</a></li>
                                @endif
                                @if (app('hasPermission')(1, 'view'))
                                    <li><a href="{{ route('product.import') }}" data-search="products import erp">Import Products</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Catalog Setup --}}
                    @if (app('hasPermission')(6, 'view'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fas fa-tags"></i>
                                <span>Catalog Setup</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ route('category.list') }}" data-search="category categories catalog erp">All Categories</a></li>
                                <li><a href="{{ route('category.add') }}" data-search="category new catalog erp">New Category</a></li>
                                <li><a href="{{ route('brand.list') }}" data-search="brand brands catalog erp">All Brands</a></li>
                                <li><a href="{{ route('brand.add') }}" data-search="brand new catalog erp">New Brand</a></li>
                                <li><a href="{{ route('unit.list') }}" data-search="unit units catalog erp">All Units</a></li>
                                <li><a href="{{ route('type.list') }}" class="{{ request()->routeIs('type.list') ? 'active' : '' }}" data-search="type types master catalog erp">Type Master</a></li>
                                <li><a href="{{ route('material.list') }}" class="{{ request()->routeIs('material.list') ? 'active' : '' }}" data-search="material materials master catalog erp">Material Master</a></li>
                                <li><a href="{{ route('labour_item.all_labour_item') }}" data-search="labour items catalog erp">All Labour Items</a></li>
                            </ul>
                        </li>
                    @endif

                    {{-- Sales and Orders --}}
                    @if (app('hasPermission')(2, 'view') || app('hasPermission')(2, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <img src="{{ image_path('admin/assets/img/icons/sales1.svg') }}" alt="img">
                                <span>Sales & Bills</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(2, 'view'))
                                    <li><a href="{{ route('sales.list') }}">All Sales & Bills</a></li>
                                @endif
                                @if (app('hasPermission')(2, 'add'))
                                    <li><a href="{{ route('sales.add', ['sale_type' => 'sales']) }}" data-search="new sales bill add erp">New Sales</a></li>
                                    <li><a href="{{ route('sales.add', ['sale_type' => 'quotation']) }}" data-search="new quotation add erp">New Quotation</a></li>
                                    @if(optional(\App\Models\Setting::first())->rental)
                                    <li><a href="{{ route('sales.add', ['sale_type' => 'rental']) }}" data-search="new rental add erp">New Rental</a></li>
                                    @endif
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if (app('hasPermission')(39, 'view'))
                        <li class="sidebar-module sidebar-module-link">
                            <a href="{{ route('sales.products_delivery') }}" data-search="products delivery sales bills delivery orders">
                                <i class="fas fa-truck"></i>
                                <span>Products Delivery</span>
                            </a>
                        </li>
                    @endif

                    <li class="submenu sidebar-module">
                        <a href="javascript:void(0);">
                            <i class="fas fa-file-invoice"></i>
                            <span>Challan Bill</span> <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{ route('challan-bills.index') }}">All Challan Bill</a></li>
                            <li><a href="{{ route('challan-bills.create') }}">Add Challan Bill</a></li>
                        </ul>
                    </li>

                    {{-- Purchases --}}
                    @if (app('hasPermission')(3, 'view') || app('hasPermission')(3, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <img src="{{ image_path('admin/assets/img/icons/purchase1.svg') }}" alt="img">
                                <span>Purchases</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(3, 'view'))
                                    <li><a href="{{ route('purchase.lists') }}">All Purchases</a></li>
                                @endif
                                @if (app('hasPermission')(3, 'add'))
                                    <li><a href="{{ route('purchase.add') }}">New Purchase</a></li>
                                    <li><a href="{{ route('purchase.row_material.lists') }}">Row Material Purchases</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Staff --}}
                @if (app('hasPermission')(8, 'view'))
                    <li class="submenu sidebar-module">
                        <a href="javascript:void(0);">
                            <img src="{{ image_path('admin/assets/img/icons/users1.svg') }}" alt="img">
                            <span>Staff</span> <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{ route('staff.list') }}">All Staff</a></li>
                            <li><a href="{{ route('staff.add') }}">New Staff</a></li>
                        </ul>
                    </li>
                @endif
                    {{-- Vendors --}}
                    @if (app('hasPermission')(10, 'view') || app('hasPermission')(10, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-handshake"></i>
                                <span>Vendors</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(4, 'view'))
                                    <li><a href="{{ route('vendor.list') }}">All Vendors</a></li>
                                @endif
                                @if (app('hasPermission')(4, 'add'))
                                    <li><a href="{{ route('vendor.add') }}">New Vendor </a></li>
                                    <li><a href="{{ route('vendor.import') }}">Import Vendor </a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Manufacturing ERP --}}
                    @if (app('hasPermission')(49, 'view') || app('hasPermission')(49, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fas fa-cogs"></i>
                                <span>Manufacture Product</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(49, 'view'))
                                    <li><a href="{{ route('row_material.list') }}">All Materials</a></li>
                                @endif
                                @if (app('hasPermission')(49, 'view'))
                                    <li><a href="{{ route('material-inventory.list') }}" class="{{ request()->routeIs('material-inventory.list') ? 'active' : '' }}">Material Inventory</a></li>
                                @endif
                                @if (app('hasPermission')(49, 'view'))
                                    <li><a href="{{ route('inventory.bom.list') }}">Bill of Materials</a></li>
                                @endif
                                @if (app('hasPermission')(49, 'view'))
                                    <li><a href="{{ route('inventory.production.list') }}">Production</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if (app('hasPermission')(40, 'view') || app('hasPermission')(40, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-building-columns"></i>
                                <span>Financers</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(40, 'view'))
                                    <li><a href="{{ route('financer.list') }}">All Financers</a></li>
                                @endif
                                @if (app('hasPermission')(40, 'add'))
                                    <li><a href="{{ route('financer.import') }}">Import Financers</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if (app('hasPermission')(41, 'view'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-warehouse"></i>
                                <span>Manage Inventory</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ route('inventory.list') }}" class="{{ request()->routeIs('inventory.list') ? 'active' : '' }}">Stock Inventory</a></li>
                                <li><a href="{{ route('exchange-inventory.list') }}" class="{{ request()->routeIs('exchange-inventory.list') ? 'active' : '' }}">Exchange Inventory</a></li>
                            </ul>
                        </li>
                    @endif

                    {{-- Returns --}}
                    @if (
                        (app('hasPermission')(2, 'add') && app('hasPermission')(2, 'edit')) ||
                            (app('hasPermission')(3, 'add') && app('hasPermission')(3, 'edit')))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <img src="{{ image_path('admin/assets/img/icons/return1.svg') }}" alt="img">
                                <span>Returns</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (isset($settings) && $settings->rental)
                                    <li><a href="{{ route('rentalreturn.list') }}">Rental Return</a></li>
                                @endif
                                @if (app('hasPermission')(2, 'add') && app('hasPermission')(2, 'edit'))
                                    <li><a href="{{ route('salesreturn.list') }}">Sales Return</a></li>
                                    <li><a href="{{ route('paymentin.index') }}" class="{{ request()->routeIs('paymentin.*') ? 'active' : '' }}">Payment-In</a></li>
                                @endif
                                @if (app('hasPermission')(3, 'add') && app('hasPermission')(3, 'edit'))
                                    <li><a href="{{ route('purchasereturn.list') }}">Purchase Return</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                        </ul>
                    </li>
                @endif


                {{-- ====== CRM ====== --}}
                @if ($showCrmSection)
                    <li class="submenu sidebar-category sidebar-category-crm">
                        <a href="javascript:void(0);">
                            <i class="fas fa-user-friends"></i>
                            <span>CRM</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                    {{-- Customers --}}
                    @if (app('hasPermission')(9, 'view') || app('hasPermission')(9, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-users"></i>
                                <span>Customers</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(9, 'view'))
                                    <li><a href="{{ route('customer.list') }}">All Customers</a></li>
                                @endif
                                @if (app('hasPermission')(9, 'add'))
                                    <li><a href="{{ route('customer.add') }}">New Customer</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Manage Leads --}}
                    @if (app('hasPermission')(32, 'view') || app('hasPermission')(32, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-bullhorn"></i>
                                <span>Manage Leads</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(32, 'view'))
                                    <li><a href="{{ route('lead.list') }}">All Leads</a></li>
                                @endif
                                @if (app('hasPermission')(32, 'add'))
                                    <li><a href="{{ route('lead.add') }}">New Lead</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Follow Ups --}}
                    @if (app('hasPermission')(30, 'view') || app('hasPermission')(30, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-calendar-check"></i>
                                <span>Follow Ups</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(30, 'view'))
                                    <li><a href="{{ route('followup.list') }}">All Follow Ups</a></li>
                                @endif
                                @if (app('hasPermission')(30, 'add'))
                                    <li><a href="{{ route('followup.add') }}">New Follow Up</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Meetings --}}
                    @if (app('hasPermission')(31, 'view') || app('hasPermission')(31, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-handshake"></i>
                                <span>Meetings</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(31, 'view'))
                                    <li><a href="{{ route('meeting.list') }}">All Meetings</a></li>
                                @endif
                                @if (app('hasPermission')(31, 'add'))
                                    <li><a href="{{ route('meeting.add') }}">New Meeting</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Tickets --}}
                    @if (app('hasPermission')(33, 'view') || app('hasPermission')(33, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-ticket-alt"></i>
                                <span>Tickets</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(33, 'view'))
                                    <li><a href="{{ route('ticket.list') }}">All Tickets</a></li>
                                @endif
                                @if (app('hasPermission')(33, 'add'))
                                    <li><a href="{{ route('ticket.add') }}">New Ticket</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                        </ul>
                    </li>
                @endif
                 {{-- ====== ACCOUNTING ====== --}}
                @if ($showAccountingSection)
                    <li class="submenu sidebar-category sidebar-category-accounting">
                        <a href="javascript:void(0);">
                            <i class="fas fa-calculator"></i>
                            <span>Accounting</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                    {{-- Account Branch --}}
                    <!-- @if (app('hasPermission')(1, 'view') || app('hasPermission')(16, 'view'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-building"></i>
                                <span>Account Branch</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(1, 'view') || app('hasPermission')(16, 'view'))
                                    <li><a href="{{ route('account_branch.list') }}">All Account Branches</a></li>
                                @endif
                                @if (app('hasPermission')(1, 'add') || app('hasPermission')(16, 'add'))
                                    <li><a href="{{ route('account_branch.add') }}">New Account Branch</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif -->

                    @if (app('hasPermission')(42, 'view') || app('hasPermission')(43, 'view') || app('hasPermission')(44, 'view') || app('hasPermission')(45, 'view'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-calculator"></i>
                                <span>Manage Accounting</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(42, 'view'))
                                    <li><a href="{{ route('account_ledger.add') }}">Account Ledger</a></li>
                                @endif
                                @if (app('hasPermission')(43, 'view'))
                                    <li><a href="{{ route('income-statement.index') }}">Income Statement</a></li>
                                @endif
                                @if (app('hasPermission')(44, 'view'))
                                    <li><a href="{{ route('accounting.balance-sheet') }}">Balance Sheet</a></li>
                                @endif
                                @if (app('hasPermission')(45, 'view'))
                                    <li><a href="{{ route('banks.index') }}">Banks</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                      {{-- Receipt & Payment --}}
                       @if (app('hasPermission')(46, 'view') || app('hasPermission')(46, 'add'))
                                <li class="sidebar-module sidebar-module-link">
                                    <a href="{{ route('sales.receipt.index') }}">
                                        <i class="fa fa-receipt"></i>
                                        <span>Receipt &amp; Payment</span>
                                    </a>
                                </li>
                            @endif


                    {{-- Expenses --}}
                    @if (app('hasPermission')(5, 'view') || app('hasPermission')(5, 'add'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <img src="{{ image_path('admin/assets/img/icons/expense1.svg') }}" alt="img">
                                <span>Expenses</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(5, 'view'))
                                    <li><a href="{{ route('expense.list') }}">All Expenses</a></li>
                                @endif
                                @if (app('hasPermission')(5, 'add'))
                                    <li><a href="{{ route('expense.add') }}">New Expense</a></li>
                                @endif
                                @if (app('hasPermission')(5, 'view'))
                                    <li><a href="{{ route('expensetype.list') }}">All Expense Type</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                      {{-- Invoices --}}
                            <!-- @if (app('hasPermission')(4, 'view') || app('hasPermission')(4, 'add'))
                                <li class="submenu sidebar-module">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-clipboard"></i>
                                        <span>Invoices</span> <span class="menu-arrow"></span></a>
                                    <ul>
                                        @if (app('hasPermission')(4, 'view'))
                                            <li><a href="{{ route('custom_invoice.lists') }}">All Invoices</a></li>
                                        @endif
                                        @if (app('hasPermission')(4, 'add'))
                                            <li><a href="{{ route('custom_invoice.add') }}">New Invoice</a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif -->

                   

                    {{-- Cash & Bank --}}
                    @if (app('hasPermission')(47, 'view'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fas fa-book"></i>
                                <span>Cash & Bank</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(47, 'view'))
                                    <li><a href="{{ route('transaction.bankbook') }}">Bank Book</a></li>
                                @endif
                                @if (app('hasPermission')(47, 'view'))
                                    <li><a href="{{ route('transaction.cashbook') }}">Cash Book</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Credit/Debit Notes --}}
                    @if (app('hasPermission')(48, 'view'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fa fa-credit-card"></i>
                                <span>Credit/Debit Notes</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(48, 'view'))
                                    <li>
                                        <a href="{{ route('credit-notes-items.index') }}">
                                            Credit Notes
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('debit-notes-items.index') }}">
                                            Debit Notes
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('credit-notes.index') }}">
                                            Credit/Debit Notes Type
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- GST Reports --}}
                    @if (app('hasPermission')(20, 'view'))
                        <li class="submenu sidebar-module">
                            <a href="javascript:void(0);">
                                <i class="fas fa-chart-line"></i>
                                <span>GST Reports</span> <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                @if (app('hasPermission')(20, 'view'))
                                    <li><a href="{{ route('gst.sales_list') }}">GST Report</a></li>
                                    <li><a href="{{ route('gst.hsn_summary') }}">HSN Summary</a></li>
                                    @endif
                            </ul>
                        </li>
                    @endif
                        </ul>
                    </li>
                @endif
                
                {{-- ====== REPORTS ====== --}}
                @if (app('hasPermission')(34, 'view') || app('hasPermission')(35, 'view') || app('hasPermission')(36, 'view') || app('hasPermission')(37, 'view') || app('hasPermission')(38, 'view'))
                    <li class="submenu sidebar-category sidebar-category-reports">
                        <a href="javascript:void(0);">
                            <img src="{{ image_path('admin/assets/img/icons/time.svg') }}" alt="img">
                            <span>Reports</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            @if (app('hasPermission')(34, 'view'))
                                <li><a href="{{ route('sales.report') }}">Sales Report</a></li>
                            @endif
                            @if (app('hasPermission')(34, 'view'))
                                <li><a href="{{ route('sales-pool-report.index') }}">Sales Pool Report</a></li>
                            @endif
                            @if (app('hasPermission')(38, 'view') && !empty($settings) && $settings->tds_apply)
                                <li><a href="{{ route('tds.report') }}">TDS Report</a></li>
                            @endif
                            @if (app('hasPermission')(35, 'view'))
                                <li><a href="{{ route('purchase.report') }}">Purchase Report</a></li>
                            @endif
                            @if (app('hasPermission')(36, 'view'))
                                <li><a href="{{ route('expense.report') }}">Expenses Report</a></li>
                            @endif
                            @if (app('hasPermission')(37, 'view'))
                                <li><a href="{{ route('profit-loss-statement.index') }}">Profit &amp; Loss Statement</a></li>
                            @endif
                        </ul>
                    </li>
                @endif

               

                @if (auth()->user()->role === 'staff')
                {{-- ====== STAFF WORKSPACE SECTION ====== --}}
                <li class="submenu sidebar-category sidebar-category-workspace">
                    <a href="javascript:void(0);">
                        <i class="fas fa-briefcase"></i>
                        <span>My Workspace</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('attendence.calendar') }}">
                                <i class="fa fa-clock"></i><span> My Attendance</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('leave.request') }}">
                                <i class="fa fa-calendar-alt"></i><span> My Leaves</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('payroll.list') }}">
                                <i class="fa fa-money-check-dollar"></i><span> My Payroll</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- @if (in_array(auth()->user()->role, ['admin', 'hr', 'sub-admin']))
                ====== HR MODULE SECTION ======
                <li class="submenu sidebar-category sidebar-category-hr">
                    <a href="javascript:void(0);">
                        <i class="fas fa-id-badge"></i>
                        <span>HR</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                

                Attendance
                @if (app('hasPermission')(26, 'view') || app('hasPermission')(26, 'add'))
                    <li class="submenu sidebar-module">
                        <a href="javascript:void(0);">
                            <i class="fa fa-clock"></i>
                            <span>Attendance</span> <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            @if (app('hasPermission')(26, 'add') || app('hasPermission')(26, 'view'))
                                <li><a href="{{ route('attendence.calendar') }}">Manage Attendance</a></li>
                                <li><a href="{{ route('attendence.summary') }}">Attendance</a></li>
                            @endif
                        </ul>
                    </li>
                @endif

                Leaves
                @if (app('hasPermission')(28, 'view') || app('hasPermission')(28, 'add'))
                    <li class="submenu sidebar-module">
                        <a href="javascript:void(0);">
                            <i class="fa fa-calendar-alt"></i>
                            <span>Leaves</span> <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            @if (in_array(auth()->user()->role, ['admin', 'sub-admin', 'hr']))
                                @if (app('hasPermission')(28, 'view'))
                                    <li><a href="{{ route('leave.view') }}">All Leaves</a></li>
                                @endif
                            @endif
                            @if (app('hasPermission')(28, 'view'))
                                <li><a href="{{ route('leave.request') }}">Leave Request</a></li>
                            @endif
                            @if (app('hasPermission')(28, 'add'))
                                <li><a href="{{ route('leave.add') }}">Add Leave</a></li>
                            @endif
                        </ul>
                    </li>
                @endif

                Payroll
                @if (app('hasPermission')(29, 'view') || app('hasPermission')(29, 'add'))
                    <li class="submenu sidebar-module">
                        <a href="javascript:void(0);">
                            <i class="fa fa-money-check-dollar"></i>
                            <span>Payroll</span> <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            @if (in_array(auth()->user()->role, ['staff']))
                                <li><a href="{{ route('payroll.list') }}">View Payroll</a></li>
                            @endif
                            @if (in_array(auth()->user()->role, ['hr', 'admin']))
                                @if (app('hasPermission')(29, 'view'))
                                    <li><a href="{{ route('payroll.list') }}">Manage Payroll</a></li>
                                @endif
                                @if (app('hasPermission')(29, 'add'))
                                    <li><a href="{{ route('payroll.create') }}">Add Payroll</a></li>
                                @endif
                                @if (app('hasPermission')(29, 'view') || app('hasPermission')(29, 'add'))
                                    <li><a href="{{ route('payroll.salary-details') }}">Group Salary</a></li>
                                @endif
                                @if (app('hasPermission')(29, 'view'))
                                    <li><a href="{{ route('payroll.incentive') }}">Incentive Calculation</a></li>
                                @endif
                            @endif
                        </ul>
                    </li>
                @endif

                Advance Pay
                @if (auth()->user()->role === 'admin')
                    <li class="submenu sidebar-module">
                        <a href="javascript:void(0);">
                            <i class="fa fa-money-bill-wave"></i>
                            <span>Advance Pay</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            @if (app('hasPermission')(23, 'view'))
                                <li><a href="{{ route('advance_pay.index') }}">All Advance Pay</a></li>
                            @endif
                            @if (app('hasPermission')(23, 'add'))
                                <li><a href="{{ route('advance_pay.create') }}">New Advance Pay</a></li>
                            @endif
                        </ul>
                    </li>
                @endif

                My Branch — only visible when plan allows multiple branches
                @if (auth()->user()->role === 'admin' && canUseBranches())
                    <li class="submenu sidebar-module">
                        <a href="javascript:void(0);">
                            <i class="fa fa-code-branch"></i>
                            <span>My Branch</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{ route('subbranch.list') }}">All Branches</a></li>
                            <li><a href="{{ route('subbranch.add') }}">New Branch</a></li>
                        </ul>
                    </li>
                @endif
                    </ul>
                </li>
                @endif --}}

                {{-- Settings --}}
                @if (app('hasPermission')(14, 'view') || app('hasPermission')(15, 'view'))
                    <li class="submenu sidebar-category sidebar-category-settings">
                        <a href="javascript:void(0);">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span> <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            
                            @if (in_array(auth()->user()->role, ['admin', 'sub-admin']))
                                <li><a href="{{ route('plans.myplan') }}">My Plan Details</a></li>
                            @endif
                            @if (app('hasPermission')(14, 'view'))
                            <li><a href="{{ route('auth.change-password') }}">Change Password</a></li>
                            @endif
                            @if (app('hasPermission')(14, 'view'))
                                <li><a href="{{ route('setting.generalsettings') }}">Shop Settings</a></li>
                                <li><a href="{{ route('setting.customize-invoice') }}">Customize Invoice</a></li>
                                <li><a href="{{ route('setting.invoice-print') }}">Invoice Print</a></li>
                            @endif
                            @if (app('hasPermission')(14, 'view') && (!$settings || $settings->send_mail))
                                <li><a href="{{ route('setting.smtpsettings') }}">Smtp Settings</a></li>
                            @endif
                            @if (app('hasPermission')(14, 'view') && (!$settings || $settings->customer_whatsapp_message || $settings->admin_whatsapp_message))
                                <li><a href="{{ route('setting.facebookappconfiguration') }}">WhatsApp
                                        Configuration</a></li>
                            @endif
                            @if (app('hasPermission')(15, 'view'))
                                <li><a href="{{ route('auth.taxrates') }}">Tax Rates</a></li>
                            @endif
                            {{-- @if (app('hasPermission')(14, 'view'))
                                <li><a href="{{ route('setting.connecteddevices') }}">Connected Devices</a></li>
                            @endif --}}
                            {{-- @if (app('hasPermission')(14, 'view'))
                                <li><a href="{{ route('department.view') }}">Departments</a></li>
                                <li><a href="{{ route('designation.view') }}">Designations</a></li>
                                <li><a href="{{ route('leave-type.view') }}">Leave Types</a></li>
                                <li><a href="{{ route('holidays.index') }}">Manage Holidays</a></li>
                                <li><a href="{{ route('holidays.calendar') }}">Holiday Calendar</a></li>
                            @endif --}}
                            @if (app('hasPermission')(14, 'view'))
                                <li><a href="{{ route('table-truncate.index') }}">Table Truncate</a></li>
                            @endif
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>


</div>
