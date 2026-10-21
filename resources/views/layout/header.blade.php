@php
    $settings = App\Models\Setting::first();
    $admin = App\Models\User::where('role', 'admin')->first();
    $erpOptions = collect([
        [
            'name' => 'Select ERP',
            'url' => 'https://erp-demo.fableadtech.com/',
        ],
        [
            'name' => 'Garage ERP',
            'url' => 'https://garage-erp.fableadtech.com/',
        ],
         [
            'name' => 'Manufacturer ERP',
            'url' => 'http://mfgdemo-erp.fableadtech.in/',
        ],
        [
            'name' => 'Hotel ERP',
            'url' => 'https://hotel-erp.fableadtech.com/',
        ],
    ])
        ->unique('url')
        ->values();
@endphp

{{-- <style>
    .select2-container--default .select2-selection--single .select2-selection__clear {
        display: none;
    }

    .web_button {
        width: 25px;
        height: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
    }

    .bar-icon span {
        display: block;
        height: 3px;
        width: 100%;
        background-color: #333;
        margin: 3px 0;
        transition: all 0.3s ease;
        pointer-events: none;
    }

    .search-view {
        width: 465px;
    }

    @media (max-width: 991px) {
        .web_button {
            display: none;
        }
    }

    /* iPad landscape specific fixes */
    @media (min-width: 992px) and (max-width: 1024px) {
        .web_button {
            display: flex !important;
            z-index: 1000;
            position: relative;
        }

        #toggle_btn1 {
            pointer-events: auto !important;
            cursor: pointer !important;
        }
    }

    /* iPad Mini / iPad Air portrait header alignment */
    @media (min-width: 768px) and (max-width: 991px) {
        .header {
            position: relative;
            display: flex;
            align-items: center;
            min-height: 60px;
            padding: 0 14px;
        }

        /* Header is in-flow (not fixed) — drop the fixed-header spacer */
        .page-wrapper {
            padding-top: 8px !important;
        }

        .page-wrapper .content {
            padding-top: 0 !important;
        }

        .header-left {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: auto !important;
            margin: 0;
            padding: 0;
            z-index: 2;
        }

        .header-left .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .header-left .logo .logo-view {
            max-width: 130px !important;
            margin: 0 auto !important;
        }

        #mobile_btn {
            display: flex !important;
            margin-right: auto;
            z-index: 3;
        }

        #toggle_btn1 {
            display: none !important;
        }

        .nav.user-menu {
            margin-left: auto;
            align-items: center;
            z-index: 3;
        }

        .nav.user-menu .user-img img {
            width: 34px;
            height: 34px;
            object-fit: cover;
            border-radius: 50%;
        }
    }

    /* iPad Pro specific (1024px) */
    @media screen and (width: 1024px) {
        .web_button {
            display: flex !important;
        }

        .mobile_btn {
            display: none !important;
        }
    }

    @media (max-width: 983px) and (min-width: 575px) {
        .logo-view {
            float: left;
            margin-left: 3rem;
        }

        .search-view {
            width: 366px;
        }
    }

    div#subBranchContainer {
        width: 200px !important;
    }

    div#erpContainer {
        width: 260px !important;
    }

    /* Mobile Bottom Navigation */
    .mobile-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: #ff9f43;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        display: none;
        justify-content: space-around;
        align-items: center;
        padding: 8px 0;
        z-index: 1050;
    }

    .mobile-bottom-nav .nav-item {
        text-align: center;
        color: #fff;
        text-decoration: none;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: color 0.3s;
    }

    .mobile-bottom-nav .nav-item i {
        font-size: 18px;
        margin-bottom: 4px;
    }

    .mobile-bottom-nav .nav-item span {
        font-size: 11px;
        font-weight: 500;
    }

    .mobile-bottom-nav .nav-item.active {
        color: #1b2850;
    }

    .notification-wrapper {
        position: relative;
    }

    /* Bell */
    .notification-bell {
        font-size: 20px;
        color: #1b2850;
        transition: all 0.3s ease;
    }

    .notification-bell:hover {
        color: #ff9f43;
    }

    /* Badge */
    .notification-badge {
        position: absolute;
        top: -4px;
        right: -0px;
        background: #ff3b30;
        color: #fff;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 50px;
        min-width: 18px;
        text-align: center;
        font-weight: bold;
        animation: pulse 2s infinite;
    }

    /* Dropdown */
    .notification-dropdown {
        position: absolute;
        top: 45px;
        right: 0;
        width: 350px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        display: none;
        overflow: hidden;
        z-index: 9999;
        border: 1px solid #eaeaea;
    }

    /* Header */
    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        font-weight: 600;
        border-bottom: 1px solid #eaeaea;
        background: #fff;
    }

    .notification-header a {
        font-size: 12px;
        color: #ff9f43;
        text-decoration: none;
        font-weight: 500;
    }

    .notification-header a:hover {
        text-decoration: underline;
    }

    /* Body */
    .notification-body {
        max-height: 460px;
        overflow-y: auto;
        background: #fff;
    }

    /* Empty State */
    .empty-notification {
        padding: 40px 20px;
        text-align: center;
        color: #8c8c8c;
        font-size: 14px;
    }

    .empty-notification i {
        font-size: 40px;
        margin-bottom: 10px;
        display: block;
        color: #ddd;
    }

    /* Notification Item */
    .notification-item {
        padding: 12px 15px;
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: 0.2s;
        position: relative;
    }

    .notification-item:hover {
        background: #fef9f0;
    }

    .notification-item.unread-notification {
        background: #fff9f0;
        border-left: 3px solid #ff9f43;
    }

    .notification-dot {
        width: 8px;
        height: 8px;
        background-color: #ff3b30;
        border-radius: 50%;
        position: absolute;
        top: 15px;
        right: 15px;
    }

    .notification-title {
        font-size: 14px;
        font-weight: 600;
        color: #1b2850;
        display: block;
        margin-bottom: 4px;
    }

    .notification-message {
        font-size: 12px;
        color: #6c757d;
        display: block;
        margin-bottom: 6px;
        line-height: 1.4;
    }

    .notification-time {
        font-size: 11px;
        color: #adb5bd;
    }

    .notification-time i {
        font-size: 10px;
        margin-right: 3px;
    }

    .notification-footer {
        padding: 10px;
        border-top: 1px solid #eaeaea;
        text-align: center;
        background: #fff;
        position: sticky;
        bottom: 0;
    }

    .notification-footer button {
        color: #ff9f43;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
    }

    .notification-footer button:hover {
        text-decoration: underline;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
    }

    @media (max-width: 991px) {
        .mobile-bottom-nav {
            display: flex;
        }

        #erpContainer {
            display: none !important;
        }

        body {
            padding-bottom: 60px !important;
        }

        .sidebar {
            bottom: 60px !important;
        }

        .sidebar-inner,
        .slimScrollDiv {
            height: calc(100vh - 120px) !important;
        }

        .tab-view {
            display: flex !important;
            position: absolute;
            right: 52px;
            top: 50%;
            transform: translateY(-50%);
            margin: 0 !important;
            z-index: 4;
        }

        .tab-view > :not(#subBranchContainer) {
            display: none !important;
        }

        #subBranchContainer {
            display: block !important;
            width: 120px !important;
            margin: 0 !important;
        }

        #subBranchContainer #subBrandSelect,
        #subBranchContainer .select2-container {
            width: 163px !important;
        }

        .notification-dropdown {
            width: 320px;
            right: -10px;
        }
    }

    @media (max-width: 575px) {
        .nav.user-menu {
            display: flex !important;
            align-items: center;
            margin-left: auto;
            padding-right: 60px;
        }

        .nav.user-menu > :not(.tab-view) {
            display: none !important;
        }

        .header {
            min-height: 60px;
            padding: 0;
        }

        .header .header-left {
            position: absolute !important;
            left: 56px !important;
            right: auto !important;
            width: auto !important;
            height: 60px;
            padding: 0 6px !important;
            border-right: 0 !important;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            z-index: 3;
        }

        .header .header-left .logo {
            width: auto !important;
            margin: 0 !important;
            text-align: left !important;
            display: inline-flex;
            align-items: center;
        }

        .header .header-left .logo .logo-view {
            max-width: 86px !important;
            margin: 0 !important;
        }

        .tab-view {
            right: 64px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 6;
        }

        #subBranchContainer {
            width: 110px !important;
        }

       #subBranchContainer #subBrandSelect, #subBranchContainer .select2-container {
            width: 138px !important;
            font-size: 12px;
            color: #212529;
            margin-top: 58px;
            margin-left: 77px;
        }
    }
</style> --}}


<style>
    .select2-container--default .select2-selection--single .select2-selection__clear {
        display: none;
    }

    .web_button {
        width: 25px;
        height: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
    }

    .bar-icon span {
        display: block;
        height: 3px;
        width: 100%;
        background-color: #333;
        margin: 3px 0;
        transition: all 0.3s ease;
        pointer-events: none;
    }

    .search-view {
        width: 465px;
    }

    .header .header-left .logo img {
        width: 120px !important;
    }

    .header .main-drop > .dropdown-toggle.userset {
        align-items: center;
        display: inline-flex;
        padding: 0;
    }

    .header .user-img {
        align-items: center;
        display: inline-flex;
        justify-content: center;
        line-height: 0;
        vertical-align: middle;
    }

    .header .user-img img {
        border-radius: 50%;
        display: block;
        height: 38px;
        margin-top: 0 !important;
        object-fit: cover;
        width: 38px;
    }

    .header .user-img .status {
        bottom: 0;
        right: 0;
    }

    @media (max-width: 991px) {
        .web_button {
            display: none;
        }
    }
    @media (max-width: 991px) and (min-width: 768px) {
    .header .header-left .logo img {
        left: 120px !important;
        position: relative;
    }
}

    /* iPad landscape specific fixes */
    @media (min-width: 992px) and (max-width: 1040px) {
        .web_button {
            display: flex !important;
            z-index: 1000;
            position: relative;
        }

        #toggle_btn1 {
            pointer-events: auto !important;
            cursor: pointer !important;
        }
    }

    /* iPad Mini / iPad Air portrait header alignment */
    @media (min-width: 768px) and (max-width: 991px) {
        .header {
            position: relative;
            display: flex;
            align-items: center;
            min-height: 60px;
            padding: 0 14px;
        }

        /* Header is in-flow (not fixed) — drop the fixed-header spacer */
        .page-wrapper {
            padding-top: 8px !important;
        }

        .page-wrapper .content {
            padding-top: 0 !important;
        }

        .header-left {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: auto !important;
            margin: 0;
            padding: 0;
            z-index: 2;
        }

        .header-left .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .header-left .logo .logo-view {
            max-width: 130px !important;
            margin: 0 auto !important;
        }

        #mobile_btn {
            display: flex !important;
            margin-right: auto;
            z-index: 3;
        }

        #toggle_btn1 {
            display: none !important;
        }

        .nav.user-menu {
            margin-left: auto;
            align-items: center;
            z-index: 3;
        }

        .nav.user-menu .user-img img {
            width: 34px;
            height: 34px;
            margin-top: 0 !important;
            object-fit: cover;
            border-radius: 50%;
        }
    }

    /* iPad Pro specific (1024px and 1032px) */
    @media screen and (width: 1024px), screen and (width: 1032px), screen and (min-width: 992px) and (max-width: 1040px) {
        .web_button {
            display: flex !important;
        }

        .mobile_btn {
            display: none !important;
        }

        .user-menu.nav>li>a {
            padding: 0 0px !important;
        }

        .header .user-img img {
            height: 34px;
            margin-top: 0 !important;
            width: 34px;
        }
    }

    /* iPad Pro landscape/tablet header layout */
    @media (min-width: 992px) and (max-width: 1040px) {
        :root {
            --ipad-pro-sidebar-width: 260px;
            --ipad-pro-mini-sidebar-width: 78px;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1040;
            display: flex;
            align-items: center;
            gap: 0;
            height: 64px !important;
            min-height: 64px !important;
            max-height: 64px !important;
            padding: 0;
            background: #fff;
            overflow: visible;
        }

        .header-left {
            width: var(--ipad-pro-sidebar-width) !important;
            min-width: var(--ipad-pro-sidebar-width);
            max-width: var(--ipad-pro-sidebar-width);
            flex: 0 0 var(--ipad-pro-sidebar-width);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 14px;
            box-sizing: border-box;
            border-right: 1px solid #eef1f6;
            height: 64px !important;
        }

        .header-left .logo {
            display: flex;
            align-items: center;
            margin: 0;
            flex: 1 1 auto;
            min-width: 0;
        }

        .header-left .logo .logo-view {
            max-width: 110px !important;
            width: auto;
        }

        #toggle_btn1 {
            flex: 0 0 28px;
            margin-left: 0;
            display: flex !important;
            align-items: center;
            justify-content: center;
            cursor: pointer !important;
            pointer-events: auto !important;
        }

        .mobile_btn {
            display: none !important;
        }

        .hide-on-ipad-pro,
        .header-hr-dropdown {
            display: none !important;
        }

        .nav.user-menu {
            flex: 1 1 auto;
            width: calc(100% - var(--ipad-pro-sidebar-width));
            min-width: 0;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end;
            flex-wrap: nowrap !important;
            gap: 8px;
            margin: 0;
            padding: 0 16px 0 20px;
            box-sizing: border-box;
            height: 64px !important;
        }

        .user-menu.nav>li>a {
            height: 64px !important;
            line-height: 64px !important;
            padding: 0 6px !important;
            display: flex !important;
            align-items: center !important;
        }

        .header .user-img img {
            width: 34px !important;
            height: 34px !important;
            margin-top: 0 !important;
        }

        .header-search-container {
            flex: 1 1 auto;
            min-width: 0;
            display: flex !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
            gap: 12px;
        }

        .header-search-container .notification-wrapper:first-of-type {
            margin-left: auto !important;
        }

        div#subBranchContainer {
            width: 145px !important;
            min-width: 125px;
            max-width: 155px;
            flex: 0 0 145px;
        }

        div#subBranchContainer .select2-container {
            width: 100% !important;
            min-width: 0 !important;
        }

        .header-search {
            flex: 0 1 240px;
            width: 240px !important;
            min-width: 180px;
            max-width: 280px;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .header-search input.form-control {
            width: 100%;
            min-width: 0;
            height: 38px;
            padding-left: 36px !important;
            font-size: 13px !important;
            border-radius: 6px !important;
        }

        .header-search img {
            left: 10px !important;
        }

        .notification-wrapper,
        .header-search-container>.me-3,
        .header-search-container>.dropdown,
        .header-search-container>div:last-child {
            flex-shrink: 0;
        }

        .header .btn.btn-sm,
        .header .dropdown-toggle.btn.btn-sm {
            height: 36px !important;
            padding: 0 10px;
            font-size: 12px;
            white-space: nowrap;
        }

        .page-wrapper,
        .content {
            padding-top: 74px;
        }

        .sidebar {
            top: 64px;
            height: calc(100vh - 64px);
        }

        .sidebar-inner,
        .slimScrollDiv {
            height: calc(100vh - 64px) !important;
        }

        body.mini-sidebar .header {
            left: 0;
        }

        body.mini-sidebar .header-left {
            width: var(--ipad-pro-mini-sidebar-width) !important;
            min-width: var(--ipad-pro-mini-sidebar-width);
            max-width: var(--ipad-pro-mini-sidebar-width);
            flex: 0 0 var(--ipad-pro-mini-sidebar-width);
            justify-content: center;
            gap: 0;
            padding-left: 0;
            padding-right: 0;
        }

        body.mini-sidebar .header-left .logo,
        body.mini-sidebar .header-left .logo-small {
            display: none !important;
        }

        body.mini-sidebar #toggle_btn1 {
            flex: 0 0 28px;
            margin: 0 auto;
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        body.mini-sidebar .nav.user-menu {
            width: calc(100% - var(--ipad-pro-mini-sidebar-width));
        }
    }

    /* Small desktop / responsive landscape header fix */
    @media (min-width: 1041px) and (max-width: 1199.98px) {
        :root {
            --responsive-header-sidebar-width: 190px;
            --responsive-header-mini-width: 78px;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1040;
            display: flex;
            align-items: center;
            gap: 0;
            height: 64px !important;
            min-height: 64px !important;
            max-height: 64px !important;
            padding: 0;
            background: #fff;
            overflow: visible;
        }

        .header-left {
            width: var(--responsive-header-sidebar-width) !important;
            min-width: var(--responsive-header-sidebar-width);
            max-width: var(--responsive-header-sidebar-width);
            flex: 0 0 var(--responsive-header-sidebar-width);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 0 14px;
            box-sizing: border-box;
            border-right: 1px solid #eef1f6;
        }

        .header-left .logo {
            display: flex;
            align-items: center;
            margin: 0;
            flex: 1 1 auto;
            min-width: 0;
        }

        .header-left .logo .logo-view {
            max-width: 105px !important;
            width: auto;
        }

        #toggle_btn1 {
            flex: 0 0 28px;
            margin-left: 0;
            display: flex !important;
        }

        .nav.user-menu {
            flex: 1 1 auto;
            width: calc(100% - var(--responsive-header-sidebar-width));
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: nowrap !important;
            gap: 8px;
            margin: 0;
            padding: 0 12px;
            box-sizing: border-box;
            height: 64px !important;
        }

        .header-search-container {
            flex: 1 1 auto;
            min-width: 0;
            display: flex !important;
            align-items: center;
            flex-wrap: nowrap !important;
            gap: 8px;
        }

        div#subBranchContainer {
            width: 140px !important;
            min-width: 140px;
            flex: 0 0 140px;
        }

        .header-search {
            flex: 1 1 180px;
            min-width: 150px;
            margin-right: 0 !important;
        }

        .header-search input.form-control {
            width: 100%;
            min-width: 0;
        }

        .notification-wrapper,
        .header-search-container>.me-3,
        .header-search-container>.dropdown,
        .header-search-container>div:last-child {
            flex-shrink: 0;
        }

        .header .btn.btn-sm,
        .header .dropdown-toggle.btn.btn-sm {
            height: 36px !important;
            padding: 0 8px;
            font-size: 11px;
            white-space: nowrap;
        }

        .page-wrapper,
        .content {
            padding-top: 74px;
        }

        .sidebar {
            top: 64px;
            height: calc(100vh - 64px);
        }

        .sidebar-inner,
        .slimScrollDiv {
            height: calc(100vh - 64px) !important;
        }

        body.mini-sidebar .header-left {
            width: var(--responsive-header-mini-width) !important;
            min-width: var(--responsive-header-mini-width);
            max-width: var(--responsive-header-mini-width);
            flex: 0 0 var(--responsive-header-mini-width);
            justify-content: center;
            gap: 0;
            padding-left: 0;
            padding-right: 0;
        }

        body.mini-sidebar .header-left .logo,
        body.mini-sidebar .header-left .logo-small {
            display: none !important;
        }

        body.mini-sidebar #toggle_btn1 {
            flex: 0 0 28px;
            margin: 0 auto;
            align-items: center;
            justify-content: center;
        }

        body.mini-sidebar .nav.user-menu {
            width: calc(100% - var(--responsive-header-mini-width));
        }
    }

    @media (max-width: 983px) and (min-width: 575px) {
        .logo-view {
            float: left;
            margin-left: 3rem;
        }

        .search-view {
            width: 366px;
        }
    }

    div#subBranchContainer {
        width: 130px !important;
    }

    /* Mobile Bottom Navigation */
    .mobile-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: #ff9f43;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        display: none;
        justify-content: space-around;
        align-items: center;
        padding: 8px 0;
        z-index: 1050;
    }

    .mobile-bottom-nav .nav-item {
        text-align: center;
        color: #fff;
        text-decoration: none;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: color 0.3s;
    }

    .mobile-bottom-nav .nav-item i {
        font-size: 18px;
        margin-bottom: 4px;
    }

    .mobile-bottom-nav .nav-item span {
        font-size: 11px;
        font-weight: 500;
    }

    .mobile-bottom-nav .nav-item.active {
        color: #1b2850;
    }

    .notification-wrapper {
        position: relative;
    }

    /* Bell */
    .notification-bell {
        font-size: 20px;
        color: #1b2850;
        transition: all 0.3s ease;
    }

    .notification-bell:hover {
        color: #ff9f43;
    }

    /* Badge */
    .notification-badge {
        position: absolute;
        top: -4px;
        right: -0px;
        background: #ff3b30;
        color: #fff;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 50px;
        min-width: 18px;
        text-align: center;
        font-weight: bold;
        animation: pulse 2s infinite;
    }

    /* Dropdown */
    .notification-dropdown {
        position: absolute;
        top: 45px;
        right: 0;
        width: 350px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        display: none;
        overflow: hidden;
        z-index: 9999;
        border: 1px solid #eaeaea;
    }

    /* Header */
    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        font-weight: 600;
        border-bottom: 1px solid #eaeaea;
        background: #fff;
    }

    .notification-header a {
        font-size: 12px;
        color: #ff9f43;
        text-decoration: none;
        font-weight: 500;
    }

    .notification-header a:hover {
        text-decoration: underline;
    }

    /* Body */
    .notification-body {
        max-height: 460px;
        overflow-y: auto;
        background: #fff;
    }

    /* Empty State */
    .empty-notification {
        padding: 40px 20px;
        text-align: center;
        color: #8c8c8c;
        font-size: 14px;
    }

    .empty-notification i {
        font-size: 40px;
        margin-bottom: 10px;
        display: block;
        color: #ddd;
    }

    /* Notification Item */
    .notification-item {
        padding: 12px 15px;
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: 0.2s;
        position: relative;
    }

    .notification-item:hover {
        background: #fef9f0;
    }

    .notification-item.unread-notification {
        background: #fff9f0;
        border-left: 3px solid #ff9f43;
    }

    .notification-dot {
        width: 8px;
        height: 8px;
        background-color: #ff3b30;
        border-radius: 50%;
        position: absolute;
        top: 15px;
        right: 15px;
    }

    .notification-title {
        font-size: 14px;
        font-weight: 600;
        color: #1b2850;
        display: block;
        margin-bottom: 4px;
    }

    .notification-message {
        font-size: 12px;
        color: #6c757d;
        display: block;
        margin-bottom: 6px;
        line-height: 1.4;
    }

    .notification-time {
        font-size: 11px;
        color: #adb5bd;
    }

    .notification-time i {
        font-size: 10px;
        margin-right: 3px;
    }

    .notification-footer {
        padding: 10px;
        border-top: 1px solid #eaeaea;
        text-align: center;
        background: #fff;
        position: sticky;
        bottom: 0;
    }

    .notification-footer button {
        color: #ff9f43;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
    }

    .notification-footer button:hover {
        text-decoration: underline;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    @media (max-width: 991px) {
        .mobile-bottom-nav {
            display: flex;
        }

        body {
            padding-bottom: 60px !important;
        }

        .sidebar {
            bottom: 60px !important;
        }

        .sidebar-inner,
        .slimScrollDiv {
            height: calc(100vh - 120px) !important;
        }

        /* .tab-view {
            display: none !important;
        } */

        .tab-view {
            display: flex !important;
            position: absolute;
            right: 165px !important;
            top: 50%;
            transform: translateY(-50%);
            margin: 0 !important;
            z-index: 4;
            gap: 12px !important;
        }

        .tab-view> :not(#erpContainer):not(#subBranchContainer) {
            display: none !important;
        }

        #subBranchContainer {
            display: block !important;
            width: 120px !important;
            margin: 0 !important;
        }

        #subBranchContainer #subBrandSelect,
        #subBranchContainer .select2-container {
            width: 163px !important;
        }

        .notification-dropdown {
            width: 320px;
            right: -10px;
        }
    }

    @media (max-width: 575px) {
        .nav.user-menu {
            display: flex !important;
            align-items: center;
            margin-left: auto;
            padding-right: 60px;
        }

        .nav.user-menu> :not(.tab-view) {
            display: none !important;
        }

        .header {
            min-height: 60px;
            padding: 0;
            display: flex;
            align-items: center;
            position: relative;
        }

        /* Hamburger stays on the left */
        #mobile_btn {
            position: relative;
            z-index: 5;
            flex-shrink: 0;
        }

        /* Logo is centered in the remaining space */
        .header .header-left {
            position: absolute !important;
            left: 55px !important;
            right: 110px !important;
            width: auto !important;
            height: 60px;
            padding: 0 6px !important;
            border-right: 0 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3;
        }

        .header .header-left .logo {
            width: auto !important;
            margin: 0 !important;
            text-align: center !important;
            display: inline-flex;
            align-items: center;
            /* justify-content: center; */
        }

        .header .header-left .logo .logo-view {
            max-width: 130px !important;
            width: auto !important;
            margin: 0 !important;
        }

        .tab-view {
            right: 80px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 6;
        }

        #erpContainer {
            display: none !important;
        }

        #subBranchContainer {
            width: 110px !important;
        }



        #subBranchContainer #subBrandSelect,
        #subBranchContainer .select2-container {
            width: 111px !important;
            font-size: 12px;
            color: #212529;
            margin-top: 0 !important;
        }

        #subBranchContainer #subBrandSelect,
        #subBranchContainer .select2-container {
            /* margin-left: 65px; */
        }

        /* Mobile Check In/Out button — right side, clear of the logo */
        .mobile-attendance-btn {
            position: absolute !important;
            right: 75px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
        }
    }

    /* SweetAlert2 mobile-friendly popup for long error messages */
    .swal-mobile-friendly {
        max-width: min(420px, calc(100vw - 2rem)) !important;
        width: auto !important;
        padding: 1.5rem 1rem !important;
        font-size: 14px !important;
        word-break: break-word !important;
    }

    @media (max-width: 575px) {
        .swal2-popup {
            max-width: calc(100vw - 2rem) !important;
            font-size: 13px !important;
            padding: 1.25rem 1rem !important;
        }

        .swal2-popup .swal2-title {
            font-size: 16px !important;
        }

        .swal2-popup .swal2-html-container {
            font-size: 13px !important;
            word-break: break-word !important;
            overflow-wrap: anywhere !important;
        }

        /* Toast on mobile — shrink and clip to viewport */
        .swal2-container.swal2-top .swal2-popup.swal2-toast {
            max-width: calc(100vw - 2rem) !important;
            font-size: 12px !important;
        }

        .swal2-container.swal2-top .swal2-popup.swal2-toast .swal2-title {
            font-size: 13px !important;
            white-space: normal !important;
            word-break: break-word !important;
        }
    }
</style>


@php
    use App\Models\FollowUp;
    use App\Models\Meeting;
    use App\Models\Delivery;
    use App\Models\Attendance;
    use App\Models\BankMaster;
    use App\Services\PendingEmiService;
    use Carbon\Carbon;

    $user = auth()->user();
    $logoRedirectRoute = $user && $user->role === 'staff' ? route('auth.staff-dashboard') : route('auth.dashboard');

    $selectedSubAdminId = session('selectedSubAdminId');
    $branchId = $user && $user->role === 'staff' && $user->branch_id ? $user->branch_id : ($user && $user->role === 'admin' ? ($selectedSubAdminId ?: $user->id) : ($user->branch_id ?? $user->id));
    $todayDate = Carbon::now('Asia/Kolkata')->toDateString();

    $todayMeetings = Meeting::with(['customer', 'assignedUser'])
        ->active()
        ->where('branch_id', $branchId)
        ->whereDate('scheduled_on', $todayDate)
        ->when($user && $user->role === 'staff', function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                    ->orWhere('assigned_to', $user->id);
            });
        })
        ->orderBy('scheduled_on')
        ->get();

    $todayFollowUps = FollowUp::with(['customer', 'lead', 'assignedUser'])
        ->active()
        ->where('branch_id', $branchId)
        ->whereDate('follow_up_datetime', $todayDate)
        ->when($user && $user->role === 'staff', function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                    ->orWhere('assigned_to', $user->id);
            });
        })
        ->orderBy('follow_up_datetime')
        ->get();

    $todayDeliveries = Delivery::with(['order.user.userDetail', 'deliveredBy'])
        ->whereHas('order', function ($query) use ($branchId) {
            $query->where('isDeleted', '!=', 1)
                ->where('branch_id', $branchId);
        })
        ->where(function ($query) use ($todayDate) {
            $query->whereDate('delivered_at', $todayDate)
                ->orWhereDate('created_at', $todayDate);
        })
        ->orderByDesc('id')
        ->get();

    $currentAlertMonth = Carbon::now('Asia/Kolkata')->format('Y-m');
    [$pendingEmiYear, $pendingEmiMonth] = PendingEmiService::parseMonth($currentAlertMonth);
    $pendingEmis = PendingEmiService::getPendingEmis((int) $branchId, $pendingEmiYear, $pendingEmiMonth);
    $branchSettings = \App\Models\Setting::where('branch_id', $branchId)->first();
    $alertCurrencySymbol = $branchSettings->currency_symbol ?? '₹';
    $alertCurrencyPosition = $branchSettings->currency_position ?? 'left';
    $alertBanks = BankMaster::where('isDeleted', 0)
        ->where('branch_id', $branchId)
        ->orderBy('bank_name')
        ->get(['id', 'bank_name']);

    $todayAlertCount = $todayMeetings->count() + $todayFollowUps->count() + $todayDeliveries->count() + count($pendingEmis);

    $todayAttendance = null;
    if ($user && $user->role === 'staff') {
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $todayDate)
            ->first();
    }

    $userPlan = null;
    if ($user && $user->role === 'admin' && !empty($user->plan_id)) {
        $userPlan = \App\Models\Plan::find($user->plan_id);
    }
@endphp

<div class="header">
    <div class="header-left active">
        <a href="{{ $logoRedirectRoute }}" class="logo text-start">
            <img src="{{ !empty($settings) && !empty($settings->logo) ? image_path('storage/' . $settings->logo) : 'https://fableadtechnolabs.com/static/media/250x150%20(1).b3f5a4db48c7770366ef.webp' }}"
                alt="" style="" class="logo-view">
        </a>
        <a href="{{ $logoRedirectRoute }}" class="logo-small">
            <img src="{{ !empty($settings) && !empty($settings->favicon) ? image_path('storage/' . $settings->favicon) : 'https://fableadtechnolabs.com/favicon-192x192.webp' }}"
                alt="">
        </a>

        <div id="toggle_btn1" class="web_button">
            <span class="bar-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </div>
    </div>

    <a id="mobile_btn" class="mobile_btn" href="#sidebar">
        <span class="bar-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>

    <ul class="nav user-menu">
        <div class="d-flex align-items-center header-search-container tab-view">
            @php
                $activePlan = auth()->check() ? auth()->user()->getActivePlan() : null;
                $canViewSalesBills = app('hasPermission')(2, 'view') || app('hasPermission')(2, 'add');
                $canAddSalesBills = app('hasPermission')(2, 'add');
                $canViewPurchases = app('hasPermission')(3, 'view') || app('hasPermission')(3, 'add');
                $canAddPurchases = app('hasPermission')(3, 'add');
            @endphp
            @if($activePlan && auth()->check() && auth()->user()->role !== 'staff')
                @php
                    $isExpiringSoon = false;
                    if ($activePlan->end_date) {
                        $daysLeft = \Carbon\Carbon::now('Asia/Kolkata')->diffInDays(\Carbon\Carbon::parse($activePlan->end_date), false);
                        if ($daysLeft >= 0 && $daysLeft <= 15) {
                            $isExpiringSoon = true;
                        }
                    }
                @endphp
                <a href="{{ route('plans.myplan') }}" class="text-decoration-none">
                    <div class="d-flex align-items-center border rounded px-2 py-1 me-2" style="border-color: var(--theme-color-18) !important; background-color: var(--theme-color-4); min-width: max-content; height: 38px;">
                        <i class="fas fa-crown me-2" style="color: #ff9f43; font-size: 16px;"></i>
                        <div class="d-flex flex-column me-2" style="line-height: 1.2;">
                            <span style="font-size: 12px; font-weight: 600; color: var(--theme-color-5);">{{ $activePlan->name ?? 'Plan' }}</span>
                            <span style="font-size: 10px; color: var(--theme-color-30);">Expires: {{ $activePlan->end_date ? \Carbon\Carbon::parse($activePlan->end_date)->format('d-m-Y') : 'N/A' }}</span>
                        </div>
                        @if($isExpiringSoon)
                            <span class="badge" style="background-color: #ff9f43; color: #fff; font-size: 9px; padding: 4px 6px; font-weight: 500; border-radius: 4px;">Expiring Soon</span>
                        @endif
                    </div>
                </a>
            @endif

            <!-- Search Field Container -->
            <div class="header-search d-flex align-items-center position-relative me-1" style="width: 173px; min-width: 173px;">
                <!-- Search Icon -->
                <img src="{{ image_path('admin/assets/img/icons/search.svg') }}" alt="Search"
                    style="position: absolute; left: 8px; width: 16px; height: 16px; z-index: 10; opacity: 0.6;">

                <!-- Input Field -->
                <input type="text" id="customerSearch" class="form-control form-control-sm rounded px-3 ps-4"
                    placeholder="Search..." autocomplete="off" style="height: 38px; font-size: 13px;">

                <!-- Search Results -->
                <div id="searchResults" class="list-group bg-white position-absolute rounded shadow mt-1 w-100"
                    style="z-index: 1050; max-height: 400px; overflow-y: auto; display: none; top: 100%; left: 0;">
                </div>
            </div>

            @if (in_array($user->role, ['admin']))
                <!-- <div class="me-1" id="erpContainer" style="width:115px;">
                    <div class="d-flex align-items-center">
                        <select id="erpSelect" class="form-select form-select-sm" data-current-erp="Select ERP" style="width: 300px;">
                            <option value="">Select ERP</option>
                            @foreach ($erpOptions as $erpOption)
                                @php
                                    $isCurrentErp = ($erpOption['name'] === 'Select ERP');
                                @endphp
                                <option value="{{ $erpOption['url'] }}" {{ $isCurrentErp ? 'selected' : '' }}>
                                    {{ $erpOption['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div> -->

                @if (canUseBranches())
                <div class="me-1" id="subBranchContainer" style="display: none;">
                    <div class="d-flex align-items-center">
                        <select id="subBrandSelect" class="form-select form-select-sm" style="width: 300px;">
                        </select>
                        <div id="currentSelection" class="ms-2 text-muted d-none" style="font-size: 12px;"></div>
                    </div>
                </div>
                @endif
            @endif


            {{-- HR Dropdown --}}
            @if (in_array(auth()->user()->role, ['admin', 'sub-admin', 'hr']))
            <div class="dropdown me-2 align-items-center hide-on-ipad-pro header-hr-dropdown">
                <button class="btn btn-sm dropdown-toggle text-white" style="background-color: #ff9f43; border-radius: 6px; height: 38px;" data-bs-toggle="dropdown">
                    <i class="fa fa-id-badge"></i> HR<i class="fa fa-chevron-down ms-1"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('attendence.calendar') }}">Attendance</a></li>
                    <li><a class="dropdown-item" href="{{ route('payroll.list') }}">Payroll</a></li>
                    <li><a class="dropdown-item" href="{{ route('staff.list') }}">Staff</a></li>
                    <li><a class="dropdown-item" href="{{ route('leave.request') }}">Leaves</a></li>
                </ul>
            </div>
            @endif

            @stack('header-actions')

            <!-- Quick Product Search Button -->
            <div class="me-1 hide-on-ipad-pro">
                <button
                    class="btn btn-sm d-flex align-items-center justify-content-center"
                    type="button" data-bs-toggle="modal" data-bs-target="#quickProductModal"
                    style="height: 38px; background-color: #ff9f43; color: white; border-radius: 6px; border: none; padding: 0 12px;">
                    <i class="fa fa-search me-1"></i> Quick Product
                </button>
            </div>

            <!-- New Bill Dropdown -->
            @if ($canViewSalesBills)
                <div class="dropdown me-1 hide-on-ipad-pro">
                    <button
                        class="btn btn-sm d-flex align-items-center justify-content-center header-new-order-button dropdown-toggle"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        style="height: 38px; background-color: #ff9f43; color: white; border-radius: 6px; border: none; padding: 0 12px;">
                        <i class="fa fa-plus me-1"></i> Bill
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="min-width: 200px; padding: 8px 0; border-radius: 8px;margin-top: 5px !important;">
                        @if (app('hasPermission')(2, 'view'))
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('sales.list') }}" style="font-size: 14px; font-weight: 500;">
                                <i class="fa fa-list text-dark me-2" style="font-size: 16px; width: 20px; text-align: center;"></i>
                                All Sales & Bills
                            </a>
                        </li>
                        @endif
                        @if ($canAddSalesBills)
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('sales.add', ['sale_type' => 'sales']) }}" style="font-size: 14px; font-weight: 500;">
                                <i class="fa fa-file-invoice text-primary me-2" style="font-size: 16px; width: 20px; text-align: center;"></i>
                                New Sales
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('sales.add', ['sale_type' => 'quotation']) }}" style="font-size: 14px; font-weight: 500;">
                                <i class="fa fa-file-alt text-success me-2" style="font-size: 16px; width: 20px; text-align: center;"></i>
                                New Quotation
                            </a>
                        </li>
                        @if(optional(\App\Models\Setting::first())->advance_receipt)
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('sales.add', ['sale_type' => 'advance_receipt']) }}" style="font-size: 14px; font-weight: 500;">
                                <i class="fa fa-receipt text-warning me-2" style="font-size: 16px; width: 20px; text-align: center;"></i>
                                New Advance Receipt
                            </a>
                        </li>
                        @endif
                        @if(optional(\App\Models\Setting::first())->rental)
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('sales.add', ['sale_type' => 'rental']) }}" style="font-size: 14px; font-weight: 500;">
                                <i class="fa fa-truck text-info me-2" style="font-size: 16px; width: 20px; text-align: center;"></i>
                                New Rental
                            </a>
                        </li>
                        @endif
                        @endif
                    </ul>
                </div>
            @endif

            @if ($canViewPurchases)
            <div class="me-1 hide-on-ipad-pro">
                <a href="{{ $canAddPurchases ? route('purchase.add') : route('purchase.lists') }}"
                    class="btn btn-sm d-flex align-items-center justify-content-center"
                    style="height: 38px; background-color: #ff9f43; color: white; border-radius: 6px; border: none;">
                    <i class="fa fa-plus me-1"></i> Purchase
                </a>
            </div>
            @endif
            {{-- Theme Toggle (light / dark) — disabled
            <li class="nav-item me-2" style="display:flex; align-items:center;">
                <a href="javascript:void(0);" id="global-theme-toggle" class="nav-link p-0" title="Toggle Theme">
                    <i class="fa-regular fa-moon" id="theme-toggle-icon" style="font-size: 20px; color: #1b2850;"></i>
                </a>
            </li>
            --}}

            @if ($user->role !== 'staff')
                <style>
                .notification-group-title { font-size: 11px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; padding: 8px 14px 6px; position: sticky; top: 0; z-index: 1; }
                .notification-group-login { color: #1d4f91; background: #e8f1fb; }
                .notification-group-products { color: #9a5b12; background: #fff1e0; }
                .notification-group-other { color: #4b5563; background: #f3f4f6; }
                .notification-item.group-login { border-left: 3px solid #3b82f6; }
                .notification-item.group-products { border-left: 3px solid #ff9f43; }
                .notification-item.group-other { border-left: 3px solid #6b7280; }
            </style>
            <li class="nav-item dropdown me-1 notification-wrapper">
                    <a href="javascript:void(0);" class="nav-link position-relative" data-bs-toggle="modal"
                        data-bs-target="#todayAlertsModal" title="Today Alerts">
                        <i class="fa-regular fa-clock" style="font-size: 18px;color: #1b2850;"></i>
                        @if ($todayAlertCount > 0)
                            <span class="notification-badge" style="top: -4px; right: -1px;">{{ $todayAlertCount > 99 ? '99+' : $todayAlertCount }}</span>
                        @endif
                    </a>
                </li>
            @endif

            <style>
                .notification-group-title { font-size: 11px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; padding: 8px 14px 6px; position: sticky; top: 0; z-index: 1; }
                .notification-group-login { color: #1d4f91; background: #e8f1fb; }
                .notification-group-products { color: #9a5b12; background: #fff1e0; }
                .notification-group-other { color: #4b5563; background: #f3f4f6; }
                .notification-item.group-login { border-left: 3px solid #3b82f6; }
                .notification-item.group-products { border-left: 3px solid #ff9f43; }
                .notification-item.group-other { border-left: 3px solid #6b7280; }
            </style>
            <li class="nav-item dropdown me-1 notification-wrapper">
                <a href="javascript:void(0);" class="nav-link position-relative notification-toggle"
                    data-notification-toggle title="Notifications">
                    <i class="fa fa-bell notification-bell"></i>
                    <span class="notification-badge notification-count d-none" data-notification-count>0</span>
                </a>

                <div class="notification-dropdown" data-notification-menu>
                    <div class="notification-header">
                        <span>Notifications</span>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('notifications.index') }}" class="text-decoration-none">View All</a>
                        </div>
                    </div>
                    <div class="notification-body" data-notification-list>
                        <div class="empty-notification">
                            <i class="fa fa-bell-slash"></i>
                            No notifications
                        </div>
                    </div>
                    <div class="notification-footer">
                        <a href="{{ route('notifications.index') }}" class="text-decoration-none">View all notifications
                            →</a>
                    </div>
                </div>
            </li>
        </div>


        @if ($user->role === 'staff')
        <li class="nav-item" id="headerAttendanceContainer"
            style="display:flex; align-items:center; gap:8px; margin-right:12px;">
            <button id="btnHeaderCheckIn" class="btn-check-in"
                style="display:flex; align-items:center; height:38px; background:#ff9f43; color:#fff;
                       border:none; border-radius:6px; padding:0 15px; font-weight:600;
                       font-size:13px; cursor:pointer; gap:6px; white-space:nowrap;">
                <i class="fa fa-sign-in-alt"></i>&nbsp;Check In
            </button>
            <button id="btnHeaderCheckOut" class="btn-check-out"
                style="display:none; align-items:center; height:38px; background:#ff9f43; color:#fff;
                       border:none; border-radius:6px; padding:0 15px; font-weight:600;
                       font-size:13px; cursor:pointer; gap:6px; white-space:nowrap;">
                <i class="fa fa-sign-out-alt"></i>&nbsp;Check Out
            </button>
        </li>
        @endif

        {{-- Plan badge hidden for now
        @if ($user->role === 'admin' && $userPlan)
        <li class="nav-item d-none d-lg-flex align-items-center me-1">
            <a href="{{ route('plans.planlist') }}"
               class="btn btn-sm"
               style="height: 36px; background-color: #1b2850; color: #fff; border-radius: 6px; border: none; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 5px; padding: 0 14px; white-space: nowrap; text-decoration: none;">
                <i class="fa fa-crown" style="font-size: 12px;"></i>
                {{ $userPlan->name }}
            </a>
        </li>
        @endif
        --}}

        <li class="nav-item dropdown has-arrow main-drop">
            <a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
                <span class="user-img">
                    <img src="{{ !empty($user->profile_image) ? image_path('storage/' . $user->profile_image) : image_path('admin/assets/img/customer/customer5.jpg') }}"
                        alt="">
                    <span class="status online"></span>
                </span>
            </a>
            <div class="dropdown-menu menu-drop-user">
                <div class="profilename">
                    <div class="profileset">
                        <span class="user-img">
                            <img src="{{ !empty($user->profile_image) ? image_path('storage/' . $user->profile_image) : image_path('admin/assets/img/customer/customer5.jpg') }}"
                                alt="">
                        </span>
                        <div class="profilesets">
                            <h6>{{ $user->name ?? 'User' }}</h6>
                            <h5>{{ ucfirst($user->role ?? 'user') }}</h5>
                        </div>
                    </div>
                    <hr class="m-0">
                    <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('auth.profile') }}" style="font-size: 14px; font-weight: 500;">
                        <i class="fa fa-user text-primary me-2" style="font-size: 16px; width: 20px; text-align: center;"></i> My Profile
                    </a>
                    <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/change-password') }}" style="font-size: 14px; font-weight: 500;">
                        <i class="fa fa-lock text-success me-2" style="font-size: 16px; width: 20px; text-align: center;"></i> Change Password
                    </a>
                    @if ($user->role === 'admin')
                        <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('setting.generalsettings') }}" style="font-size: 14px; font-weight: 500;">
                            <i class="fa fa-cog text-warning me-2" style="font-size: 16px; width: 20px; text-align: center;"></i> Settings
                        </a>
                        @if (canUseBranches())
                        <a class="dropdown-item new_branch d-flex align-items-center py-2" href="{{ route('subbranch.list') }}" style="font-size: 14px; font-weight: 500;">
                            <i class="fa fa-layer-group text-info me-2" style="font-size: 16px; width: 20px; text-align: center;"></i> My Branch
                        </a>
                        @endif
                    @endif
                    <hr class="m-0">
                    <a class="dropdown-item logout d-flex align-items-center py-2" href="{{ route('logout') }}" style="font-size: 14px; font-weight: 500;">
                        <i class="fa fa-sign-out-alt text-danger me-2" style="font-size: 16px; width: 20px; text-align: center;"></i> Logout
                    </a>
                </div>
            </div>
        </li>
    </ul>

    @if ($user->role === 'staff')
    <div class="mobile-attendance-btn d-lg-none">
        <button class="btn-check-in"
            style="display:flex; align-items:center; height:34px; background:#ff9f43; color:#fff;
                   border:none; border-radius:6px; padding:0 12px; font-weight:600;
                   font-size:12px; cursor:pointer; gap:4px; white-space:nowrap;">
            <i class="fa fa-sign-in-alt"></i>&nbsp;Check In
        </button>
        <button class="btn-check-out"
            style="display:none; align-items:center; height:34px; background:#ff9f43; color:#fff;
                   border:none; border-radius:6px; padding:0 12px; font-weight:600;
                   font-size:12px; cursor:pointer; gap:4px; white-space:nowrap;">
            <i class="fa fa-sign-out-alt"></i>&nbsp;Check Out
        </button>
    </div>
    @endif

    @if ($user->role !== 'staff')
    <div class="mobile-header-clock notification-wrapper">
        <a href="javascript:void(0);" class="nav-link position-relative" data-bs-toggle="modal"
            data-bs-target="#todayAlertsModal" title="Today Alerts">
            <i class="fa-regular fa-clock" style="font-size: 18px;color: #1b2850;"></i>
            @if ($todayAlertCount > 0)
                <span class="notification-badge" style="top: -4px; right: -1px;">{{ $todayAlertCount > 99 ? '99+' : $todayAlertCount }}</span>
            @endif
        </a>
    </div>
    @endif

    <div class="mobile-header-notification notification-wrapper">
        <a href="javascript:void(0);" class="nav-link position-relative notification-toggle"
            data-notification-toggle title="Notifications">
            <i class="fa fa-bell notification-bell"></i>
            <span class="notification-badge notification-count d-none" data-notification-count>0</span>
        </a>

        <div class="notification-dropdown" data-notification-menu>
            <div class="notification-header">
                <span>Notifications</span>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('notifications.index') }}" class="text-decoration-none">View All</a>
                </div>
            </div>
            <div class="notification-body" data-notification-list>
                <div class="empty-notification">
                    <i class="fa fa-bell-slash"></i>
                    No notifications
                </div>
            </div>
            <div class="notification-footer">
                <a href="{{ route('notifications.index') }}" class="text-decoration-none">View all notifications
                    →</a>
            </div>
        </div>
    </div>

    <div class="dropdown mobile-user-menu">
        <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
            aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{ route('auth.profile') }}">My Profile</a>
            <a class="dropdown-item" href="{{ url('/change-password') }}">Change Password</a>
            @if ($user->role === 'admin')
                <a class="dropdown-item" href="{{ route('setting.generalsettings') }}">Settings</a>
                @if (canUseBranches())
                <a class="dropdown-item new_branch" href="{{ route('subbranch.list') }}">My Branch</a>
                @endif
            @endif
            <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
        </div>
    </div>
</div>

@php
    $deliveryStatusOptions = [
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

    $defaultTodayAlertTab = 'meetings';
    if ($todayDeliveries->count() >= $todayMeetings->count()
        && $todayDeliveries->count() >= $todayFollowUps->count()
        && $todayDeliveries->count() > 0) {
        $defaultTodayAlertTab = 'deliveries';
    } elseif ($todayFollowUps->count() > $todayMeetings->count()) {
        $defaultTodayAlertTab = 'followups';
    }

    $showMeetingsTab = $defaultTodayAlertTab === 'meetings';
    $showFollowUpsTab = $defaultTodayAlertTab === 'followups';
    $showDeliveriesTab = $defaultTodayAlertTab === 'deliveries';
    $showPendingEmisTab = false;

    $formatAlertCurrency = function ($amount) use ($alertCurrencySymbol, $alertCurrencyPosition) {
        $formatted = number_format((float) $amount, 2);
        return $alertCurrencyPosition === 'right'
            ? $formatted . $alertCurrencySymbol
            : $alertCurrencySymbol . $formatted;
    };
@endphp

<style>
    .modal-dialog.today-alerts-dialog {
        width: min(760px, calc(100vw - 2rem)) !important;
        max-width: min(760px, calc(100vw - 2rem)) !important;
    }

    .today-alerts-toggle-col {
        display: none;
        width: 44px;
    }

    .today-alerts-row-toggle-inline {
        display: none;
    }

    @media (min-width: 768px) {
        .today-alerts-toggle-col,
        .today-alerts-row-toggle-inline,
        .today-alerts-details-row {
            display: none !important;
        }
    }

    .today-alerts-modal .modal-header,
    .today-alerts-modal .modal-footer {
        padding: 0.85rem 1rem;
    }

    :root[data-theme="dark"] .today-alerts-modal .modal-footer .btn.btn-secondary {
        background-color: #0f172a  !important;
        border-color: #0f172a!important;
        color: #fff !important;
    }

    :root[data-theme="dark"] .today-alerts-modal .modal-footer .btn.btn-secondary:hover,
    :root[data-theme="dark"] .today-alerts-modal .modal-footer .btn.btn-secondary:focus {
        background-color: #0f172a  !important;
        border-color: #0f172a!important;
        color: #fff !important;
        box-shadow: none !important;
    }

    .today-alerts-modal .modal-body {
        padding: 1rem;
    }

    .today-alerts-tabs.nav.nav-tabs {
        border-bottom: 1px solid #e9ecef;
        display: grid !important;
        flex-wrap: nowrap !important;
        gap: 0;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        list-style: none;
        margin-bottom: 1rem !important;
        overflow: visible;
        padding-left: 0;
        width: 100%;
    }

    .today-alerts-tabs .nav-item {
        float: none;
        margin-bottom: 0;
        min-width: 0;
        width: 100%;
    }

    .today-alerts-tabs .nav-link {
        border: 0;
        border-bottom: 2px solid transparent;
        border-radius: 0;
        color: #6c757d;
        display: block;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.2;
        margin-bottom: 0;
        padding: 0.55rem 0.15rem;
        text-align: center;
        white-space: nowrap;
        width: 100%;
    }

    .today-alerts-tabs .nav-link.active {
        color: #ff9f43;
        border-bottom-color: #ff9f43;
        background: transparent;
    }

    .today-alerts-count {
        background: rgba(255, 159, 67, 0.12);
        border-radius: 999px;
        color: #ff9f43;
        font-size: 11px;
        margin-left: 4px;
        padding: 0.05rem 0.35rem;
    }

    .today-alerts-modal .table {
        font-size: 13px;
        table-layout: fixed;
        width: 100%;
    }

    .today-alerts-modal .table th,
    .today-alerts-modal .table td {
        padding: 0.65rem;
        vertical-align: middle;
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .today-alerts-row-toggle {
        align-items: center;
        background: #fff3e7;
        border: 1px solid #ffd3ab;
        border-radius: 50%;
        color: #ff9f43;
        display: inline-flex;
        font-size: 18px;
        font-weight: 700;
        height: 28px;
        justify-content: center;
        line-height: 1;
        min-width: 28px;
        padding: 0;
        text-decoration: none;
        width: 28px;
    }

    .today-alerts-row-toggle:hover,
    .today-alerts-row-toggle:focus {
        background: #ff9f43;
        color: #fff;
    }

    .today-alerts-row-toggle .today-alerts-toggle-symbol {
        display: block;
        transform: translateY(-1px);
    }

    .today-alerts-details-row {
        display: none;
    }

    .today-alerts-details-row.is-open {
        display: table-row;
    }

    .today-alerts-details-row > td {
        background: #fffaf4;
        padding: 0 !important;
    }

    .today-alerts-details-card {
        display: grid;
        gap: 0.75rem;
        padding: 0.9rem 1rem 1rem;
    }

    .today-alerts-detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }

    .today-alerts-detail-label {
        color: #6c757d;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }

    .today-alerts-detail-value {
        color: #212529;
        font-size: 13px;
    }

    .today-alerts-detail-action a {
        align-items: center;
        color: #ff9f43;
        display: inline-flex;
        font-weight: 600;
        gap: 0.35rem;
        text-decoration: none;
    }

    .today-alert-delivery-status {
        min-width: 80px;
        font-size: 12px;
        padding: 4px 8px;
        height: auto;
    }

    .today-alert-delivery-status.is-saving {
        opacity: 0.65;
        pointer-events: none;
    }

    .today-alerts-address {
        max-width: 220px;
        white-space: normal;
        word-break: break-word;
    }

    .today-alerts-emi-toolbar {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: space-between;
        margin-bottom: 0.85rem;
    }

    .today-alerts-emi-toolbar label {
        color: #6c757d;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .today-alerts-emi-month-input {
        max-width: 180px;
    }

    .today-alerts-emi-pay-btn {
        font-size: 12px;
        padding: 0.25rem 0.55rem;
        white-space: nowrap;
    }

    .today-alerts-emi-pay-btn.is-disabled {
        opacity: 0.55;
        pointer-events: none;
    }

    @media (max-width: 767px) {
        .modal-dialog.today-alerts-dialog {
            max-width: calc(100% - 1rem) !important;
            margin: 0.5rem auto;
            width: calc(100% - 1rem) !important;
        }

        .today-alerts-tabs.nav.nav-tabs {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .today-alerts-tabs .nav-item {
            flex: 0 0 auto;
            width: auto;
        }

        .today-alerts-tabs .nav-link {
            font-size: 11px;
            padding: 0.55rem 0.45rem;
            width: auto;
        }

        .today-alerts-details-row.is-open {
            display: table-row !important;
        }

        .today-alerts-modal .table {
            min-width: 0;
        }

        .today-alerts-modal .table thead {
            display: none;
        }

        .today-alerts-summary-row td {
            border-left: 0;
            border-right: 0;
            padding: 0.85rem 0.65rem;
        }

        .today-alerts-summary-row .today-alerts-mobile-hide {
            display: none;
        }

        .today-alerts-primary-cell {
            width: 100%;
        }

        .today-alerts-primary-content {
            align-items: flex-start;
            display: flex;
            gap: 0.75rem;
            justify-content: space-between;
        }

        .today-alerts-primary-content > div {
            flex: 1 1 auto;
            min-width: 0;
        }

        .today-alerts-primary-title {
            color: #212529;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.35;
            word-break: break-word;
        }

        .today-alerts-primary-meta {
            color: #6c757d;
            display: block;
            font-size: 12px;
            line-height: 1.4;
            margin-top: 0.2rem;
        }

        .today-alerts-row-toggle {
            flex-shrink: 0;
            margin-left: auto;
            margin-top: 0.1rem;
        }

        .today-alerts-row-toggle-inline {
            display: inline-flex;
            order: 2;
        }
    }
</style>

<div class="modal fade" id="todayAlertsModal" tabindex="-1" aria-labelledby="todayAlertsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered today-alerts-dialog ">
        <div class="modal-content today-alerts-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="todayAlertsModalLabel">Today Alerts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs today-alerts-tabs mb-3" id="todayAlertsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $showMeetingsTab ? 'active' : '' }}" id="meetings-tab"
                            data-bs-toggle="tab" data-bs-target="#meetings-pane" type="button" role="tab"
                            aria-controls="meetings-pane" aria-selected="{{ $showMeetingsTab ? 'true' : 'false' }}">
                            Meetings
                            <span class="today-alerts-count">{{ $todayMeetings->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $showFollowUpsTab ? 'active' : '' }}" id="followups-tab"
                            data-bs-toggle="tab" data-bs-target="#followups-pane" type="button" role="tab"
                            aria-controls="followups-pane" aria-selected="{{ $showFollowUpsTab ? 'true' : 'false' }}">
                            Follow Ups
                            <span class="today-alerts-count">{{ $todayFollowUps->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $showDeliveriesTab ? 'active' : '' }}" id="deliveries-tab"
                            data-bs-toggle="tab" data-bs-target="#deliveries-pane" type="button" role="tab"
                            aria-controls="deliveries-pane" aria-selected="{{ $showDeliveriesTab ? 'true' : 'false' }}">
                            Deliveries
                            <span class="today-alerts-count">{{ $todayDeliveries->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $showPendingEmisTab ? 'active' : '' }}" id="pending-emis-tab"
                            data-bs-toggle="tab" data-bs-target="#pending-emis-pane" type="button" role="tab"
                            aria-controls="pending-emis-pane" aria-selected="{{ $showPendingEmisTab ? 'true' : 'false' }}">
                            Pending EMIs
                            <span class="today-alerts-count" id="pendingEmisTabCount">{{ count($pendingEmis) }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="todayAlertsTabContent">
                    <div class="tab-pane fade {{ $showMeetingsTab ? 'show active' : '' }}" id="meetings-pane"
                        role="tabpanel" aria-labelledby="meetings-tab">
                        @if ($todayMeetings->isEmpty())
                            <div class="alert alert-info mb-0">No meetings scheduled for today.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="today-alerts-toggle-col"></th>
                                            <th>Title</th>
                                            <th>Customer</th>
                                            <th>Type</th>
                                            <th>Schedule</th>
                                            <th>Assigned</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($todayMeetings as $meeting)
                                            <tr class="today-alerts-summary-row">
                                                <td class="today-alerts-toggle-col text-center">
                                                    <button type="button" class="today-alerts-row-toggle"
                                                        data-alert-toggle aria-expanded="false"
                                                        aria-controls="meeting-details-{{ $meeting->id }}">
                                                        <span class="today-alerts-toggle-symbol">+</span>
                                                    </button>
                                                </td>
                                                <td class="today-alerts-primary-cell">
                                                    <div class="today-alerts-primary-content">
                                                        <button type="button"
                                                            class="today-alerts-row-toggle today-alerts-row-toggle-inline"
                                                            data-alert-toggle aria-expanded="false"
                                                            aria-controls="meeting-details-{{ $meeting->id }}">
                                                            <span class="today-alerts-toggle-symbol">+</span>
                                                        </button>
                                                        <div>
                                                            <div class="today-alerts-primary-title">
                                                                {{ $meeting->meeting_title ?? 'Untitled meeting' }}
                                                            </div>
                                                            <span class="today-alerts-primary-meta d-md-none">
                                                                {{ $meeting->customer?->name ?? 'Customer not found' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="today-alerts-mobile-hide">
                                                    {{ $meeting->customer?->name ?? 'Customer not found' }}
                                                </td>
                                                <td class="today-alerts-mobile-hide">
                                                    {{ $meeting->meeting_type ?? 'Type missing' }}
                                                </td>
                                                <td class="today-alerts-mobile-hide">
                                                    {{ $meeting->formatted_scheduled_on ?? ($meeting->scheduled_on?->format('d-m-Y h:i A') ?? '') }}
                                                </td>
                                                <td class="today-alerts-mobile-hide">
                                                    {{ $meeting->assignedUser?->name ?? '-' }}
                                                </td>
                                                <td class="text-center today-alerts-mobile-hide">
                                                    <a href="/meeting-view/{{ $meeting->id }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr id="meeting-details-{{ $meeting->id }}" class="today-alerts-details-row">
                                                <td colspan="7">
                                                    <div class="today-alerts-details-card">
                                                        <div class="today-alerts-detail-item">
                                                            <span class="today-alerts-detail-label">Customer</span>
                                                            <span class="today-alerts-detail-value">{{ $meeting->customer?->name ?? 'Customer not found' }}</span>
                                                        </div>
                                                        <div class="today-alerts-detail-item">
                                                            <span class="today-alerts-detail-label">Type</span>
                                                            <span class="today-alerts-detail-value">{{ $meeting->meeting_type ?? 'Type missing' }}</span>
                                                        </div>
                                                        <div class="today-alerts-detail-item">
                                                            <span class="today-alerts-detail-label">Schedule</span>
                                                            <span class="today-alerts-detail-value">{{ $meeting->formatted_scheduled_on ?? ($meeting->scheduled_on?->format('d-m-Y h:i A') ?? '') }}</span>
                                                        </div>
                                                        <div class="today-alerts-detail-item">
                                                            <span class="today-alerts-detail-label">Assigned</span>
                                                            <span class="today-alerts-detail-value">{{ $meeting->assignedUser?->name ?? '-' }}</span>
                                                        </div>
                                                        <div class="today-alerts-detail-action">
                                                            <a href="/meeting-view/{{ $meeting->id }}">
                                                                <i class="fa fa-eye"></i>
                                                                View Details
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="tab-pane fade {{ $showFollowUpsTab ? 'show active' : '' }}" id="followups-pane"
                        role="tabpanel" aria-labelledby="followups-tab">
                        @if ($todayFollowUps->isEmpty())
                            <div class="alert alert-info mb-0">No follow ups scheduled for today.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="today-alerts-toggle-col"></th>
                                            <th>Purpose</th>
                                            <th>Customer</th>
                                            <th>Status</th>
                                            <th>Date & Time</th>
                                            <th>Assigned</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($todayFollowUps as $followUp)
                                            <tr class="today-alerts-summary-row">
                                                <td class="today-alerts-toggle-col text-center">
                                                    <button type="button" class="today-alerts-row-toggle"
                                                        data-alert-toggle aria-expanded="false"
                                                        aria-controls="followup-details-{{ $followUp->id }}">
                                                        <span class="today-alerts-toggle-symbol">+</span>
                                                    </button>
                                                </td>
                                                <td class="today-alerts-primary-cell">
                                                    <div class="today-alerts-primary-content">
                                                        <button type="button"
                                                            class="today-alerts-row-toggle today-alerts-row-toggle-inline"
                                                            data-alert-toggle aria-expanded="false"
                                                            aria-controls="followup-details-{{ $followUp->id }}">
                                                            <span class="today-alerts-toggle-symbol">+</span>
                                                        </button>
                                                        <div>
                                                            <div class="today-alerts-primary-title">
                                                                {{ $followUp->purpose ?? 'No purpose' }}
                                                            </div>
                                                            <span class="today-alerts-primary-meta d-md-none">
                                                                {{ $followUp->subject_name ?? 'Lead not found' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="today-alerts-mobile-hide">
                                                    {{ $followUp->subject_name ?? 'Lead not found' }}
                                                </td>
                                                <td class="today-alerts-mobile-hide">
                                                    {{ $followUp->status ?? 'Status unknown' }}
                                                </td>
                                                <td class="today-alerts-mobile-hide">
                                                    {{ $followUp->formatted_follow_up_datetime ?? ($followUp->follow_up_datetime?->format('d-m-Y h:i A') ?? '') }}
                                                </td>
                                                <td class="today-alerts-mobile-hide">
                                                    {{ $followUp->assignedUser?->name ?? '-' }}
                                                </td>
                                                <td class="text-center today-alerts-mobile-hide">
                                                    <a href="/follow-up-view/{{ $followUp->id }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr id="followup-details-{{ $followUp->id }}" class="today-alerts-details-row">
                                                <td colspan="7">
                                                    <div class="today-alerts-details-card">
                                                        <div class="today-alerts-detail-item">
                                                            <span class="today-alerts-detail-label">Customer</span>
                                                            <span class="today-alerts-detail-value">{{ $followUp->subject_name ?? 'Lead not found' }}</span>
                                                        </div>
                                                        <div class="today-alerts-detail-item">
                                                            <span class="today-alerts-detail-label">Status</span>
                                                            <span class="today-alerts-detail-value">{{ $followUp->status ?? 'Status unknown' }}</span>
                                                        </div>
                                                        <div class="today-alerts-detail-item">
                                                            <span class="today-alerts-detail-label">Date & Time</span>
                                                            <span class="today-alerts-detail-value">{{ $followUp->formatted_follow_up_datetime ?? ($followUp->follow_up_datetime?->format('d-m-Y h:i A') ?? '') }}</span>
                                                        </div>
                                                        <div class="today-alerts-detail-item">
                                                            <span class="today-alerts-detail-label">Assigned</span>
                                                            <span class="today-alerts-detail-value">{{ $followUp->assignedUser?->name ?? '-' }}</span>
                                                        </div>
                                                        <div class="today-alerts-detail-action">
                                                            <a href="/follow-up-view/{{ $followUp->id }}">
                                                                <i class="fa fa-eye"></i>
                                                                View Details
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="tab-pane fade {{ $showDeliveriesTab ? 'show active' : '' }}" id="deliveries-pane"
                        role="tabpanel" aria-labelledby="deliveries-tab">
                        @if ($todayDeliveries->isEmpty())
                            <div class="alert alert-info mb-0">No deliveries scheduled for today.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="today-alerts-toggle-col"></th>
                                            <th>Order#</th>
                                            <th>Customer</th>
                                            <th class="today-alerts-mobile-hide">Contact</th>
                                            <th class="today-alerts-mobile-hide">Address</th>
                                            <th>Status</th>
                                            <th class="text-center today-alerts-mobile-hide">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($todayDeliveries as $delivery)
                                            @php
                                                $customer = $delivery->order?->user;
                                                $customerDetail = $customer?->userDetail;
                                                $addressParts = array_filter([
                                                    $customerDetail?->address,
                                                    $customerDetail?->city,
                                                    $customerDetail?->country,
                                                ]);
                                                $customerAddress = $addressParts ? implode(', ', $addressParts) : 'N/A';
                                                $deliveryStatusKey = strtolower(str_replace([' ', '-'], '_', trim((string) $delivery->status)));
                                                $currentDeliveryStatus = $deliveryStatusMap[$deliveryStatusKey] ?? 'pending';
                                            @endphp
                                            <tr class="today-alerts-summary-row">
                                                <td class="today-alerts-toggle-col text-center">
                                                    <button type="button" class="today-alerts-row-toggle"
                                                        data-alert-toggle aria-expanded="false"
                                                        aria-controls="delivery-details-{{ $delivery->id }}">
                                                        <span class="today-alerts-toggle-symbol">+</span>
                                                    </button>
                                                </td>
                                                <td class="today-alerts-primary-cell">
                                                    <div class="today-alerts-primary-content">
                                                        <button type="button"
                                                            class="today-alerts-row-toggle today-alerts-row-toggle-inline"
                                                            data-alert-toggle aria-expanded="false"
                                                            aria-controls="delivery-details-{{ $delivery->id }}">
                                                            <span class="today-alerts-toggle-symbol">+</span>
                                                        </button>
                                                        <div>
                                                            <a href="{{ route('sales.delivery', $delivery->order_id) }}">
                                                                #{{ $delivery->order->order_number ?? $delivery->order_id }}
                                                            </a>
                                                            <span class="today-alerts-primary-meta d-md-none">
                                                                {{ $customer?->name ?? 'Customer not found' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="today-alerts-mobile-hide">
                                                    {{ $customer?->name ?? 'Customer not found' }}
                                                </td>
                                                <td class="today-alerts-mobile-hide">
                                                    {{ $customer?->phone ?? 'N/A' }}
                                                </td>
                                                <td class="today-alerts-mobile-hide today-alerts-address">
                                                    {{ $customerAddress }}
                                                </td>
                                                <td>
                                                    <select
                                                        class="form-control form-control-sm today-alert-delivery-status"
                                                        data-url="{{ route('sales.delivery.status.update', $delivery->id) }}"
                                                        data-current-status="{{ $currentDeliveryStatus }}"
                                                    >
                                                        @foreach ($deliveryStatusOptions as $statusValue => $statusLabel)
                                                            <option value="{{ $statusValue }}" @selected($currentDeliveryStatus === $statusValue)>{{ $statusLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="text-center today-alerts-mobile-hide">
                                                    <a href="{{ route('sales.delivery', $delivery->order_id) }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr id="delivery-details-{{ $delivery->id }}" class="today-alerts-details-row">
                                                <td colspan="7">
                                                    <div class="today-alerts-details-card">
                                                        <div class="today-alerts-detail-item">
                                                            <span class="today-alerts-detail-label">Customer</span>
                                                            <span class="today-alerts-detail-value">{{ $customer?->name ?? 'Customer not found' }}</span>
                                                        </div>
                                                        <div class="today-alerts-detail-item">
                                                            <span class="today-alerts-detail-label">Contact</span>
                                                            <span class="today-alerts-detail-value">{{ $customer?->phone ?? 'N/A' }}</span>
                                                        </div>
                                                        <div class="today-alerts-detail-item">
                                                            <span class="today-alerts-detail-label">Address</span>
                                                            <span class="today-alerts-detail-value">{{ $customerAddress }}</span>
                                                        </div>
                                                        <div class="today-alerts-detail-item">
                                                            <span class="today-alerts-detail-label">Delivered By</span>
                                                            <span class="today-alerts-detail-value">{{ $delivery->deliveredBy?->name ?? $delivery->delivered_by ?? '-' }}</span>
                                                        </div>
                                                        <div class="today-alerts-detail-action">
                                                            <a href="{{ route('sales.delivery', $delivery->order_id) }}">
                                                                <i class="fa fa-eye"></i>
                                                                View Delivery
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="tab-pane fade {{ $showPendingEmisTab ? 'show active' : '' }}" id="pending-emis-pane"
                        role="tabpanel" aria-labelledby="pending-emis-tab">
                        <div class="today-alerts-emi-toolbar">
                            <div>
                                <label for="pendingEmiMonthPicker">Select Month</label>
                                <input type="month" class="form-control form-control-sm today-alerts-emi-month-input"
                                    id="pendingEmiMonthPicker" value="{{ $currentAlertMonth }}">
                            </div>
                            <small class="text-muted" id="pendingEmiMonthSummary">
                                Showing pending EMIs for {{ Carbon::create($pendingEmiYear, $pendingEmiMonth, 1)->format('F Y') }}
                            </small>
                        </div>

                        <div id="pendingEmiLoading" class="text-center text-muted py-3 d-none">Loading pending EMIs...</div>

                        <div id="pendingEmiEmpty" class="alert alert-info mb-0 {{ count($pendingEmis) ? 'd-none' : '' }}">
                            No pending EMIs found for this month.
                        </div>

                        <div class="table-responsive {{ count($pendingEmis) ? '' : 'd-none' }}" id="pendingEmiTableWrap">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="today-alerts-toggle-col"></th>
                                        <th>Order#</th>
                                        <th>Customer</th>
                                        <th class="today-alerts-mobile-hide">Contact</th>
                                        <th>EMI Month</th>
                                        <th>EMI Amount</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="pendingEmiTableBody">
                                    @foreach ($pendingEmis as $pendingEmi)
                                        <tr class="today-alerts-summary-row">
                                            <td class="today-alerts-toggle-col text-center">
                                                <button type="button" class="today-alerts-row-toggle"
                                                    data-alert-toggle aria-expanded="false"
                                                    aria-controls="pending-emi-details-{{ $pendingEmi['order_id'] }}-{{ $pendingEmi['emi_month'] }}">
                                                    <span class="today-alerts-toggle-symbol">+</span>
                                                </button>
                                            </td>
                                            <td class="today-alerts-primary-cell">
                                                <div class="today-alerts-primary-content">
                                                    <button type="button"
                                                        class="today-alerts-row-toggle today-alerts-row-toggle-inline"
                                                        data-alert-toggle aria-expanded="false"
                                                        aria-controls="pending-emi-details-{{ $pendingEmi['order_id'] }}-{{ $pendingEmi['emi_month'] }}">
                                                        <span class="today-alerts-toggle-symbol">+</span>
                                                    </button>
                                                    <div>
                                                        <a href="{{ route('sales.details', $pendingEmi['order_id']) }}">
                                                            #{{ $pendingEmi['order_number'] ?? $pendingEmi['order_id'] }}
                                                        </a>
                                                        <span class="today-alerts-primary-meta d-md-none">
                                                            {{ $pendingEmi['customer_name'] }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="today-alerts-mobile-hide">{{ $pendingEmi['customer_name'] }}</td>
                                            <td class="today-alerts-mobile-hide">{{ $pendingEmi['customer_phone'] }}</td>
                                            <td>{{ $pendingEmi['emi_month_label'] }}</td>
                                            <td>{{ $formatAlertCurrency($pendingEmi['emi_amount']) }}</td>
                                            <td class="text-center">
                                                @if ($pendingEmi['can_pay'])
                                                    <button type="button"
                                                        class="btn btn-sm btn-primary today-alerts-emi-pay-btn today-alert-emi-pay"
                                                        data-order-id="{{ $pendingEmi['order_id'] }}"
                                                        data-order-number="{{ $pendingEmi['order_number'] ?? $pendingEmi['order_id'] }}"
                                                        data-customer-name="{{ $pendingEmi['customer_name'] }}"
                                                        data-emi-month="{{ $pendingEmi['emi_month'] }}"
                                                        data-emi-month-label="{{ $pendingEmi['emi_month_label'] }}"
                                                        data-emi-amount="{{ $pendingEmi['emi_amount'] }}">
                                                        Mark Paid
                                                    </button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-secondary today-alerts-emi-pay-btn is-disabled"
                                                        title="Pay earlier EMI months first"
                                                        disabled>
                                                        Pay Previous
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr id="pending-emi-details-{{ $pendingEmi['order_id'] }}-{{ $pendingEmi['emi_month'] }}"
                                            class="today-alerts-details-row">
                                            <td colspan="7">
                                                <div class="today-alerts-details-card">
                                                    <div class="today-alerts-detail-item">
                                                        <span class="today-alerts-detail-label">Customer</span>
                                                        <span class="today-alerts-detail-value">{{ $pendingEmi['customer_name'] }}</span>
                                                    </div>
                                                    <div class="today-alerts-detail-item">
                                                        <span class="today-alerts-detail-label">Contact</span>
                                                        <span class="today-alerts-detail-value">{{ $pendingEmi['customer_phone'] }}</span>
                                                    </div>
                                                    <div class="today-alerts-detail-item">
                                                        <span class="today-alerts-detail-label">Due Date</span>
                                                        <span class="today-alerts-detail-value">{{ $pendingEmi['due_date'] }}</span>
                                                    </div>
                                                    <div class="today-alerts-detail-item">
                                                        <span class="today-alerts-detail-label">EMI Month</span>
                                                        <span class="today-alerts-detail-value">{{ $pendingEmi['emi_month_label'] }}</span>
                                                    </div>
                                                    <div class="today-alerts-detail-action">
                                                        <a href="{{ route('sales.list') }}">
                                                            <i class="fa fa-eye"></i>
                                                            Open Order Page
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="todayAlertEmiPayModal" tabindex="-1" aria-labelledby="todayAlertEmiPayModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content today-alerts-modal">
            <form id="todayAlertEmiPayForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="todayAlertEmiPayModalLabel">Mark EMI Paid</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="todayAlertEmiOrderId">
                    <input type="hidden" name="payment_method" value="emi">
                    <input type="hidden" name="payment_type" value="emi">
                    <input type="hidden" name="emi_month" id="todayAlertEmiMonth">
                    <input type="hidden" name="emi_monthly_amount" id="todayAlertEmiAmountHidden">
                    <input type="hidden" name="payment_amount" id="todayAlertEmiPaymentAmount">
                    <input type="hidden" name="amount" id="todayAlertEmiAmountField">

                    <div class="border rounded bg-light p-2 mb-3">
                        <div><strong>Order:</strong> <span id="todayAlertEmiOrderNumber">-</span></div>
                        <div><strong>Customer:</strong> <span id="todayAlertEmiCustomerName">-</span></div>
                        <div><strong>EMI Month:</strong> <span id="todayAlertEmiMonthLabel">-</span></div>
                        <div><strong>Amount:</strong> <span id="todayAlertEmiAmountDisplay">-</span></div>
                    </div>

                    <div class="mb-3">
                        <label for="todayAlertEmiBankId" class="form-label">Select Bank</label>
                        <select name="bank_id" id="todayAlertEmiBankId" class="form-select" required>
                            <option value="">Select Bank</option>
                            @foreach ($alertBanks as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                            @endforeach
                        </select>
                        <div class="text-danger small" id="todayAlertEmiBankError"></div>
                    </div>

                    <div class="mb-0">
                        <label for="todayAlertEmiRemarks" class="form-label">Remarks</label>
                        <textarea name="remarks" id="todayAlertEmiRemarks" class="form-control" rows="2"
                            placeholder="Optional remarks"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="todayAlertEmiPaySubmit">Submit Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
    <script>
        const currentUserRole = "{{ auth()->user()->role }}";
        const currentUserId = "{{ auth()->user()->id }}";
    </script>

    @if (($showTodayAlertsModal ?? false) && request()->routeIs('auth.dashboard') && auth()->user()->role === 'admin' && $todayAlertCount > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modalElement = document.getElementById('todayAlertsModal');
                if (modalElement && typeof bootstrap !== 'undefined') {
                    const todayAlertsModal = new bootstrap.Modal(modalElement);
                    
                    const showTodayAlerts = function() {
                        todayAlertsModal.show();
                    };

                    if (window.willShowPlanExpiry) {
                        const expiryModal = document.getElementById('planExpiryNoticeModal');
                        if (expiryModal) {
                            expiryModal.addEventListener('hidden.bs.modal', function() {
                                if (window.willShowCustomPlanExpiry) {
                                    const customModal = document.getElementById('customPlanExpiryModal');
                                    if (customModal) {
                                        customModal.addEventListener('hidden.bs.modal', showTodayAlerts, {once: true});
                                        return;
                                    }
                                }
                                showTodayAlerts();
                            }, {once: true});
                            return;
                        }
                    }
                    
                    if (window.willShowCustomPlanExpiry) {
                        const customModal = document.getElementById('customPlanExpiryModal');
                        if (customModal) {
                            customModal.addEventListener('hidden.bs.modal', showTodayAlerts, {once: true});
                            return;
                        }
                    }
                    
                    showTodayAlerts();
                }
            });
        </script>
    @endif

    {{-- ==================== ATTENDANCE CHECK IN/OUT (same as omsai-ERP) ==================== --}}
    <script>
        (function() {
            // Guard: only run for staff role
            console.log('[Attendance] currentUserRole =', typeof currentUserRole !== 'undefined' ? currentUserRole : 'UNDEFINED');
            if (typeof currentUserRole === 'undefined' || currentUserRole !== 'staff') {
                console.log('[Attendance] Skipping — user is not staff.');
                return;
            }

            var btnIns  = document.querySelectorAll('.btn-check-in');
            var btnOuts = document.querySelectorAll('.btn-check-out');

            console.log('[Attendance] check-in buttons found:', btnIns.length, '| check-out buttons found:', btnOuts.length);

            if (btnIns.length === 0 || btnOuts.length === 0) return;

            var csrfMeta  = document.querySelector('meta[name="csrf-token"]');
            var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            // ── State helpers ──────────────────────────────────────────────
            function showCheckIn() {
                btnIns.forEach(function(btn) { btn.style.display = 'flex'; });
                btnOuts.forEach(function(btn) { btn.style.display = 'none'; });
                console.log('[Attendance] → Showing Check In');
            }
            function showCheckOut() {
                btnIns.forEach(function(btn) { btn.style.display = 'none'; });
                btnOuts.forEach(function(btn) { btn.style.display = 'flex'; });
                console.log('[Attendance] → Showing Check Out');
            }

            // ── Fetch today's status on page load ──────────────────────────
            function fetchStatus() {
                fetch("{{ route('staff.checkstatus', [], false) }}", {
                    method: 'GET',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                .then(function(r) {
                    console.log('[Attendance] status response HTTP:', r.status);
                    return r.ok ? r.json() : Promise.reject('HTTP ' + r.status);
                })
                .then(function(data) {
                    console.log('[Attendance] status data:', data);
                    if (data.status === 'checked_in') {
                        showCheckOut();
                    } else {
                        showCheckIn();
                    }
                })
                .catch(function(err) {
                    console.warn('[Attendance] status fetch failed:', err);
                    showCheckIn(); // safe fallback
                });
            }

            fetchStatus();

            // ── Check In click ─────────────────────────────────────────────
            btnIns.forEach(function(btn) {
                btn.addEventListener('click', function() {

                    // Disable all check-in buttons and show spinner
                    btnIns.forEach(function(b) {
                        b.disabled = true;
                        b.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;Getting GPS...';
                    });

                    function resetBtnIns() {
                        btnIns.forEach(function(b) {
                            b.disabled = false;
                            b.innerHTML = '<i class="fa fa-sign-in-alt"></i>&nbsp;Check In';
                        });
                    }

                    async function doCheckIn(latitude, longitude) {
                        btnIns.forEach(function(b) {
                            b.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;Checking In...';
                        });

                        var body = {};
                        if (latitude !== undefined && longitude !== undefined) {
                            body.check_in_latitude  = latitude;
                            body.check_in_longitude = longitude;

                            try {
                                btnIns.forEach(function(b) { b.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;Getting Address...'; });
                                let geoRes = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`);
                                let geoData = await geoRes.json();
                                if (geoData && geoData.display_name) {
                                    body.check_in_location_name = geoData.display_name;
                                }
                            } catch (e) {
                                console.warn('[CheckIn] Geocoding failed:', e);
                            }

                            btnIns.forEach(function(b) { b.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;Checking In...'; });
                        }

                        fetch("{{ route('staff.checkin', [], false) }}", {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify(body)
                        })
                        .then(async function(r) {
                            var text = await r.text();
                            try {
                                var data = JSON.parse(text);
                                return { ok: r.ok, status: r.status, data: data };
                            } catch (e) {
                                console.error('[CheckIn] Server returned non-JSON:', text);
                                throw new Error('Server returned an invalid response (HTTP ' + r.status + ')');
                            }
                        })
                        .then(function(res) {
                            var data = res.data;
                            if (res.ok || data.success) {
                                Swal.fire({ toast:true, position:'top', icon:'success', title: data.message || 'Checked In successfully.', showConfirmButton:false, timer:2000, timerProgressBar:true })
                                    .then(function() { window.location.reload(); });
                                showCheckOut();
                            } else {
                                var errMsg = data.error || 'Check In failed.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Check In Failed',
                                    html: '<span style="font-size:13px;line-height:1.5;display:block;word-break:break-word;">' + errMsg + '</span>',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#ff9f43',
                                    customClass: { popup: 'swal-mobile-friendly' }
                                });
                            }
                        })
                        .catch(function(err) {
                            console.error('[CheckIn] Request failed:', err);
                            Swal.fire({ toast:true, position:'top', icon:'error', title: err.message || 'Check In failed. Please try again.', showConfirmButton:false, timer:5000 });
                        })
                        .finally(resetBtnIns);
                    }

                    // Get GPS then check in
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                var lat      = position.coords.latitude;
                                var lon      = position.coords.longitude;
                                var accuracy = position.coords.accuracy;
                                console.log('[CheckIn] GPS: ' + lat + ', ' + lon + ' | Accuracy: ' + accuracy + 'm');
                                doCheckIn(lat, lon);
                            },
                            function(error) {
                                console.warn('[CheckIn] GPS error (code ' + error.code + '):', error.message);
                                if (error.code === 1) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Location Access Required',
                                        html: 'Please allow location access in your browser and try again.<br><br><small>On iPhone: Settings → Safari → Location → Allow</small>',
                                        confirmButtonText: 'OK'
                                    });
                                    resetBtnIns();
                                } else {
                                    // GPS timeout / unavailable — proceed without coords
                                    doCheckIn();
                                }
                            },
                            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                        );
                    } else {
                        doCheckIn();
                    }

                }); // end click listener
            }); // end btnIns.forEach

            // ── Check Out click ────────────────────────────────────────────
            btnOuts.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    btnOuts.forEach(function(b) {
                        b.disabled = true;
                        b.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;Getting GPS...';
                    });

                    async function doCheckOut(latitude, longitude) {
                        btnOuts.forEach(function(b) {
                            b.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;Checking Out...';
                        });

                        var body = {};
                        if (latitude !== undefined && longitude !== undefined) {
                            body.check_out_latitude  = latitude;
                            body.check_out_longitude = longitude;

                            try {
                                btnOuts.forEach(function(b) { b.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;Getting Address...'; });
                                let geoRes = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`);
                                let geoData = await geoRes.json();
                                if (geoData && geoData.display_name) {
                                    body.check_out_location_name = geoData.display_name;
                                }
                            } catch (e) {
                                console.warn('[CheckOut] Geocoding failed:', e);
                            }

                            btnOuts.forEach(function(b) { b.innerHTML = '<i class="fa fa-spinner fa-spin"></i>&nbsp;Checking Out...'; });
                        }

                        fetch("{{ route('staff.checkout', [], false) }}", {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify(body)
                        })
                        .then(async function(r) {
                            var text = await r.text();
                            try {
                                var data = JSON.parse(text);
                                return { ok: r.ok, status: r.status, data: data };
                            } catch (e) {
                                console.error('[CheckOut] Server returned non-JSON:', text);
                                throw new Error('Server returned an invalid response (HTTP ' + r.status + ')');
                            }
                        })
                        .then(function(res) {
                            var data = res.data;
                            if (res.ok || data.success) {
                                Swal.fire({ toast:true, position:'top', icon:'success', title: data.message || 'Checked Out successfully.', showConfirmButton:false, timer:2000, timerProgressBar:true })
                                    .then(function() { window.location.reload(); });
                                showCheckIn();
                            } else {
                                var errMsg = data.error || 'Check Out failed.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Check Out Failed',
                                    html: '<span style="font-size:13px;line-height:1.5;display:block;word-break:break-word;">' + errMsg + '</span>',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#ff9f43',
                                    customClass: { popup: 'swal-mobile-friendly' }
                                });
                            }
                        })
                        .catch(function(err) {
                            console.error('[CheckOut] Request failed:', err);
                            Swal.fire({ toast:true, position:'top', icon:'error', title: err.message || 'Check Out failed. Please try again.', showConfirmButton:false, timer:5000 });
                        })
                        .finally(function() {
                            btnOuts.forEach(function(b) {
                                b.disabled = false;
                                b.innerHTML = '<i class="fa fa-sign-out-alt"></i>&nbsp;Check Out';
                            });
                        });
                    }

                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                var lat = position.coords.latitude;
                                var lon = position.coords.longitude;
                                doCheckOut(lat, lon);
                            },
                            function(error) {
                                console.warn('[CheckOut] GPS error:', error.message);
                                if (error.code === 1) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Location Access Required',
                                        html: 'Please allow location access in your browser and try again.',
                                        confirmButtonText: 'OK'
                                    });
                                    btnOuts.forEach(function(b) {
                                        b.disabled = false;
                                        b.innerHTML = '<i class="fa fa-sign-out-alt"></i>&nbsp;Check Out';
                                    });
                                } else {
                                    doCheckOut();
                                }
                            },
                            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                        );
                    } else {
                        doCheckOut();
                    }
                });
            });

        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-alert-toggle]').forEach(function(toggleButton) {
                toggleButton.addEventListener('click', function() {
                    const detailsRow = document.getElementById(this.getAttribute('aria-controls'));
                    if (!detailsRow) {
                        return;
                    }

                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', String(!isExpanded));
                    detailsRow.classList.toggle('is-open', !isExpanded);

                    const symbol = this.querySelector('.today-alerts-toggle-symbol');
                    if (symbol) {
                        symbol.textContent = isExpanded ? '+' : '-';
                    }
                });
            });

            $(document).on('change', '.today-alert-delivery-status', function() {
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
                        } else {
                            $select.val(previousStatus);
                            alert(response.message || 'Unable to update delivery status.');
                        }
                    },
                    error: function(xhr) {
                        $select.val(previousStatus);
                        alert(xhr.responseJSON?.message || 'Unable to update delivery status.');
                    },
                    complete: function() {
                        $select.removeClass('is-saving').prop('disabled', false);
                    }
                });
            });

            const pendingEmiMonthPicker = document.getElementById('pendingEmiMonthPicker');
            const pendingEmiTableBody = document.getElementById('pendingEmiTableBody');
            const pendingEmiTableWrap = document.getElementById('pendingEmiTableWrap');
            const pendingEmiEmpty = document.getElementById('pendingEmiEmpty');
            const pendingEmiLoading = document.getElementById('pendingEmiLoading');
            const pendingEmiMonthSummary = document.getElementById('pendingEmiMonthSummary');
            const pendingEmiTabCount = document.getElementById('pendingEmisTabCount');
            const salesDetailsBaseUrl = @json(url('/sales-details'));
            const salesListUrl = @json(route('sales.list'));
            const alertCurrencySymbol = @json($alertCurrencySymbol);
            const alertCurrencyPosition = @json($alertCurrencyPosition);

            function formatAlertCurrency(amount, currencySymbol, currencyPosition) {
                const formatted = parseFloat(amount || 0).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                return currencyPosition === 'right'
                    ? `${formatted}${currencySymbol}`
                    : `${currencySymbol}${formatted}`;
            }

            function formatPendingEmiMonthLabel(monthValue) {
                if (!monthValue) {
                    return '';
                }

                const [year, month] = monthValue.split('-').map(Number);
                const date = new Date(year, month - 1, 1);
                return date.toLocaleDateString(undefined, { month: 'long', year: 'numeric' });
            }

            function renderPendingEmiRows(items, currencySymbol, currencyPosition) {
                if (!pendingEmiTableBody) {
                    return;
                }

                if (!items || !items.length) {
                    pendingEmiTableBody.innerHTML = '';
                    pendingEmiTableWrap?.classList.add('d-none');
                    pendingEmiEmpty?.classList.remove('d-none');
                    return;
                }

                pendingEmiEmpty?.classList.add('d-none');
                pendingEmiTableWrap?.classList.remove('d-none');

                pendingEmiTableBody.innerHTML = items.map(function(item) {
                    const detailsId = `pending-emi-details-${item.order_id}-${item.emi_month}`;
                    const orderLink = `${salesDetailsBaseUrl}/${item.order_id}`;
                    const actionButton = item.can_pay
                        ? `<button type="button"
                                class="btn btn-sm btn-primary today-alerts-emi-pay-btn today-alert-emi-pay"
                                data-order-id="${item.order_id}"
                                data-order-number="${item.order_number || item.order_id}"
                                data-customer-name="${item.customer_name || 'N/A'}"
                                data-emi-month="${item.emi_month}"
                                data-emi-month-label="${item.emi_month_label}"
                                data-emi-amount="${item.emi_amount}">
                                Mark Paid
                           </button>`
                        : `<button type="button"
                                class="btn btn-sm btn-outline-secondary today-alerts-emi-pay-btn is-disabled"
                                title="Pay earlier EMI months first"
                                disabled>
                                Pay Previous
                           </button>`;

                    return `
                        <tr class="today-alerts-summary-row">
                            <td class="today-alerts-toggle-col text-center">
                                <button type="button" class="today-alerts-row-toggle"
                                    data-alert-toggle aria-expanded="false"
                                    aria-controls="${detailsId}">
                                    <span class="today-alerts-toggle-symbol">+</span>
                                </button>
                            </td>
                            <td class="today-alerts-primary-cell">
                                <div class="today-alerts-primary-content">
                                    <button type="button"
                                        class="today-alerts-row-toggle today-alerts-row-toggle-inline"
                                        data-alert-toggle aria-expanded="false"
                                        aria-controls="${detailsId}">
                                        <span class="today-alerts-toggle-symbol">+</span>
                                    </button>
                                    <div>
                                        <a href="${orderLink}">#${item.order_number || item.order_id}</a>
                                        <span class="today-alerts-primary-meta d-md-none">${item.customer_name || 'N/A'}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="today-alerts-mobile-hide">${item.customer_name || 'N/A'}</td>
                            <td class="today-alerts-mobile-hide">${item.customer_phone || 'N/A'}</td>
                            <td>${item.emi_month_label}</td>
                            <td>${formatAlertCurrency(item.emi_amount, currencySymbol, currencyPosition)}</td>
                            <td class="text-center">${actionButton}</td>
                        </tr>
                        <tr id="${detailsId}" class="today-alerts-details-row">
                            <td colspan="7">
                                <div class="today-alerts-details-card">
                                    <div class="today-alerts-detail-item">
                                        <span class="today-alerts-detail-label">Customer</span>
                                        <span class="today-alerts-detail-value">${item.customer_name || 'N/A'}</span>
                                    </div>
                                    <div class="today-alerts-detail-item">
                                        <span class="today-alerts-detail-label">Contact</span>
                                        <span class="today-alerts-detail-value">${item.customer_phone || 'N/A'}</span>
                                    </div>
                                    <div class="today-alerts-detail-item">
                                        <span class="today-alerts-detail-label">Due Date</span>
                                        <span class="today-alerts-detail-value">${item.due_date || '-'}</span>
                                    </div>
                                    <div class="today-alerts-detail-item">
                                        <span class="today-alerts-detail-label">EMI Month</span>
                                        <span class="today-alerts-detail-value">${item.emi_month_label}</span>
                                    </div>
                                    <div class="today-alerts-detail-action">
                                        <a href="${salesListUrl}">
                                            <i class="fa fa-eye"></i>
                                            Open Order Page
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            function loadPendingEmis(monthValue) {
                if (!pendingEmiMonthPicker) {
                    return;
                }

                pendingEmiLoading?.classList.remove('d-none');

                $.ajax({
                    url: @json(route('sales.pending_emis')),
                    method: 'GET',
                    data: {
                        month: monthValue || pendingEmiMonthPicker.value
                    },
                    success: function(response) {
                        const currencySymbol = response.currency_symbol || alertCurrencySymbol;
                        const currencyPosition = response.currency_position || alertCurrencyPosition;
                        renderPendingEmiRows(response.data || [], currencySymbol, currencyPosition);

                        if (pendingEmiTabCount) {
                            pendingEmiTabCount.textContent = response.count || 0;
                        }

                        if (pendingEmiMonthSummary) {
                            pendingEmiMonthSummary.textContent = `Showing pending EMIs for ${formatPendingEmiMonthLabel(response.month || monthValue)}`;
                        }
                    },
                    error: function() {
                        if (pendingEmiTableBody) {
                            pendingEmiTableBody.innerHTML = '';
                        }
                        pendingEmiTableWrap?.classList.add('d-none');
                        pendingEmiEmpty?.classList.remove('d-none');
                        if (pendingEmiEmpty) {
                            pendingEmiEmpty.textContent = 'Unable to load pending EMIs. Please try again.';
                        }
                    },
                    complete: function() {
                        pendingEmiLoading?.classList.add('d-none');
                    }
                });
            }

            if (pendingEmiMonthPicker) {
                pendingEmiMonthPicker.addEventListener('change', function() {
                    loadPendingEmis(this.value);
                });
            }

            const todayAlertEmiPayModalElement = document.getElementById('todayAlertEmiPayModal');
            const todayAlertEmiPayModal = todayAlertEmiPayModalElement && typeof bootstrap !== 'undefined'
                ? new bootstrap.Modal(todayAlertEmiPayModalElement)
                : null;

            $(document).on('click', '.today-alert-emi-pay', function() {
                const $button = $(this);
                const emiAmount = parseFloat($button.data('emi-amount')) || 0;

                $('#todayAlertEmiOrderId').val($button.data('order-id'));
                $('#todayAlertEmiMonth').val($button.data('emi-month'));
                $('#todayAlertEmiOrderNumber').text('#' + ($button.data('order-number') || $button.data('order-id')));
                $('#todayAlertEmiCustomerName').text($button.data('customer-name') || 'N/A');
                $('#todayAlertEmiMonthLabel').text($button.data('emi-month-label') || '-');
                $('#todayAlertEmiAmountDisplay').text(formatAlertCurrency(emiAmount, alertCurrencySymbol, alertCurrencyPosition));
                $('#todayAlertEmiAmountHidden').val(emiAmount);
                $('#todayAlertEmiPaymentAmount').val(emiAmount);
                $('#todayAlertEmiAmountField').val(emiAmount);
                $('#todayAlertEmiBankId').val('');
                $('#todayAlertEmiRemarks').val('');
                $('#todayAlertEmiBankError').text('');

                if (todayAlertEmiPayModal) {
                    todayAlertEmiPayModal.show();
                }
            });

            $('#todayAlertEmiPayForm').on('submit', function(event) {
                event.preventDefault();

                if (!$('#todayAlertEmiBankId').val()) {
                    $('#todayAlertEmiBankError').text('Please select a bank.');
                    return;
                }

                const submitButton = $('#todayAlertEmiPaySubmit');
                const formData = new FormData(this);
                const authToken = localStorage.getItem('authToken') || '';

                submitButton.prop('disabled', true).text('Processing...');

                $.ajax({
                    url: '/api/sales/make-payment',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer ' + authToken
                    },
                    success: function() {
                        if (todayAlertEmiPayModal) {
                            todayAlertEmiPayModal.hide();
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'EMI payment submitted successfully.',
                                confirmButtonText: 'OK'
                            }).then(function() {
                                loadPendingEmis(pendingEmiMonthPicker ? pendingEmiMonthPicker.value : '');
                            });
                        } else {
                            alert('EMI payment submitted successfully.');
                            loadPendingEmis(pendingEmiMonthPicker ? pendingEmiMonthPicker.value : '');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Unable to submit EMI payment.';

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors)
                                .flat()
                                .join('\n');
                        } else if (xhr.responseJSON?.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage
                            });
                        } else {
                            alert(errorMessage);
                        }
                    },
                    complete: function() {
                        submitButton.prop('disabled', false).text('Submit Payment');
                    }
                });
            });
        });

        // // ==================== SEARCH FUNCTIONALITY ====================
        // document.addEventListener('DOMContentLoaded', function() {
        //     const searchInput = document.getElementById('customerSearch');
        //     const resultBox = document.getElementById('searchResults');

        //     if (searchInput) {
        //         searchInput.addEventListener('input', function() {
        //             const query = this.value.trim();

        //             if (query.length < 1) {
        //                 resultBox.style.display = 'none';
        //                 return;
        //             }

        //             fetch(`/search-users?query=${encodeURIComponent(query)}`)
        //                 .then(response => response.json())
        //                 .then(data => {
        //                     resultBox.innerHTML = '';
        //                     let hasResults = false;

        //                     // Customers section
        //                     if (['admin', 'sales-manager', 'inventory-manager'].includes(currentUserRole) && data.users?.length > 0) {
        //                         hasResults = true;
        //                         resultBox.innerHTML += `<div class="list-group-item fw-bold bg-light">Customers</div>`;
        //                         data.users.forEach(user => {
        //                             const profileUrl = `/customer-view/${user.id}`;
        //                             resultBox.innerHTML += `
    //                                 <a href="${profileUrl}" class="list-group-item list-group-item-action d-flex align-items-center">
    //                                     <img src="${user.profile_image}" alt="User Image" class="rounded-circle me-2" style="width:35px; height:35px; object-fit: cover;">
    //                                     <div>
    //                                         <strong>${escapeHtml(user.name) ?? 'N/A'}</strong><br>
    //                                         <small class="text-muted">${escapeHtml(user.email) ?? 'N/A'}</small>
    //                                     </div>
    //                                 </a>
    //                             `;
        //                         });
        //                     }

        //                     // Vendors section
        //                     if (['admin', 'purchase-manager', 'inventory-manager'].includes(currentUserRole) && data.vendors?.length > 0) {
        //                         hasResults = true;
        //                         resultBox.innerHTML += `<div class="list-group-item fw-bold bg-light mt-2">Vendors</div>`;
        //                         data.vendors.forEach(vendor => {
        //                             const vendorUrl = `/vendor-view/${vendor.id}`;
        //                             resultBox.innerHTML += `
    //                                 <a href="${vendorUrl}" class="list-group-item list-group-item-action d-flex align-items-center">
    //                                     <img src="${vendor.profile_image}" alt="Vendor Image" class="rounded-circle me-2" style="width:35px; height:35px; object-fit: cover;">
    //                                     <div>
    //                                         <strong>${escapeHtml(vendor.name) ?? 'N/A'}</strong><br>
    //                                         <small class="text-muted">${escapeHtml(vendor.email) ?? 'N/A'}</small>
    //                                     </div>
    //                                 </a>
    //                             `;
        //                         });
        //                     }

        //                     // Products section
        //                     if (['admin', 'purchase-manager', 'inventory-manager'].includes(currentUserRole) && data.products?.length > 0) {
        //                         hasResults = true;
        //                         resultBox.innerHTML += `<div class="list-group-item fw-bold bg-light mt-2">Products</div>`;
        //                         data.products.forEach(product => {
        //                             resultBox.innerHTML += `
    //                                 <a href="/product-view/${product.id}" class="list-group-item list-group-item-action d-flex align-items-center">
    //                                     <img src="${product.image}" alt="Product Image" class="rounded me-2" style="width:35px; height:35px; object-fit: cover;">
    //                                     <div>
    //                                         <strong>${escapeHtml(product.name) ?? 'N/A'}</strong><br>
    //                                         <small class="text-muted">Price: ₹${product.price ?? 'N/A'}</small>
    //                                     </div>
    //                                 </a>
    //                             `;
        //                         });
        //                     }

        //                     // Orders section
        //                     if (['admin', 'sales-manager', 'inventory-manager'].includes(currentUserRole) && data.orders?.length > 0) {
        //                         hasResults = true;
        //                         resultBox.innerHTML += `<div class="list-group-item fw-bold bg-light mt-2">Orders</div>`;
        //                         data.orders.forEach(order => {
        //                             resultBox.innerHTML += `
    //                                 <a href="/sales-details/${order.id}" class="list-group-item list-group-item-action">
    //                                     <div class="d-flex align-items-center mb-1">
    //                                         <img src="{{ image_path('admin/assets/img/icons/cart.svg') }}" width="20" height="20" class="me-2" alt="cart">
    //                                         <strong>Order #: ${order.order_number ?? 'N/A'}</strong>
    //                                     </div>
    //                                     <div>
    //                                         <small>Customer: ${escapeHtml(order.user_name) ?? 'N/A'}</small><br>
    //                                         <small>Total: ₹${order.total_amount ?? 'N/A'} | Status: ${order.payment_status ?? 'N/A'}</small>
    //                                     </div>
    //                                 </a>
    //                             `;
        //                         });
        //                     }

        //                     if (!hasResults) {
        //                         resultBox.innerHTML = '<div class="list-group-item text-center text-muted">No results found</div>';
        //                     }

        //                     resultBox.style.display = 'block';
        //                 })
        //                 .catch(error => {
        //                     console.error('Search error:', error);
        //                     resultBox.innerHTML = '<div class="list-group-item text-center text-danger">Error loading results</div>';
        //                     resultBox.style.display = 'block';
        //                 });
        //         });
        //     }

        //     // Hide dropdown on outside click
        //     document.addEventListener('click', function(e) {
        //         if (searchInput && resultBox && !searchInput.contains(e.target) && !resultBox.contains(e.target)) {
        //             resultBox.style.display = 'none';
        //         }
        //     });
        // });

        // // Helper function to escape HTML
        // function escapeHtml(text) {
        //     if (!text) return '';
        //     const div = document.createElement('div');
        //     div.textContent = text;
        //     return div.innerHTML;
        // }

        // // ==================== SIDEBAR TOGGLE FUNCTIONALITY ====================
        // $(document).on('mouseover', function(e) {
        //     const $toggleBtn = $('#toggle_btn1');
        //     const isTabletSize = $(window).width() >= 768 && $(window).width() <= 1024;
        //     const isButtonAvailable = $toggleBtn.length > 0 && (isTabletSize || $toggleBtn.is(':visible'));

        //     if ($('body').hasClass('mini-sidebar') && isButtonAvailable) {
        //         const isInsideSidebar = $(e.target).closest('.sidebar').length;
        //         if (isInsideSidebar) {
        //             $('body').addClass('expand-menu');
        //             $('.subdrop + ul').slideDown();
        //         } else {
        //             $('body').removeClass('expand-menu');
        //             $('.subdrop + ul').slideUp();
        //         }
        //     }
        // });

        // // Toggle button handler
        // $(document).on('click', '#toggle_btn1', function(e) {
        //     e.preventDefault();
        //     e.stopPropagation();

        //     if ($(this).data('processing')) {
        //         return false;
        //     }
        //     $(this).data('processing', true);

        //     const body = $('body');
        //     const $btn = $(this);

        //     if (body.hasClass('mini-sidebar')) {
        //         body.removeClass('mini-sidebar');
        //         $btn.addClass('active');
        //         $('.subdrop + ul').slideDown();
        //         localStorage.setItem('screenModeNightTokenState', 'night');
        //         setTimeout(function() {
        //             body.removeClass('mini-sidebar');
        //             $('.header-left').addClass('active');
        //         }, 100);
        //     } else {
        //         body.addClass('mini-sidebar');
        //         $btn.removeClass('active');
        //         $('.subdrop + ul').slideUp();
        //         localStorage.removeItem('screenModeNightTokenState');
        //         setTimeout(function() {
        //             body.addClass('mini-sidebar');
        //             $('.header-left').removeClass('active');
        //         }, 100);
        //     }

        //     setTimeout(() => {
        //         $btn.data('processing', false);
        //     }, 300);

        //     return false;
        // });

        // // ==================== BRANCH DROPDOWN FUNCTIONALITY ====================
        // (function initializeBranchDropdown() {
        //     const container = document.getElementById('subBranchContainer');
        //     const select = document.getElementById('subBrandSelect');
        //     if (container) {
        //         container.style.display = 'block';
        //     }
        //     if (select) {
        //         select.innerHTML = "";
        //         const mainOption = document.createElement('option');
        //         mainOption.value = "";
        //         mainOption.textContent = 'Main Branch';
        //         select.appendChild(mainOption);
        //         if (window.$ && $.fn && $.fn.select2) {
        //             $('#subBrandSelect').select2({
        //                 placeholder: 'Select a branch',
        //                 allowClear: true
        //             });
        //         }
        //     }
        // })();

        // fetch('/api/get_subadmin', {
        //     headers: {
        //         "Authorization": "Bearer " + localStorage.getItem("authToken"),
        //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //     }
        // })
        // .then(res => res.json())
        // .then(response => {
        //     if (!response) {
        //         console.warn('No response from /api/get_subadmin');
        //         return;
        //     }

        //     const select = document.getElementById("subBrandSelect");
        //     if (!select) return;

        //     select.innerHTML = "";
        //     document.getElementById('subBranchContainer').style.display = 'block';

        //     const mainOption = document.createElement("option");
        //     mainOption.value = "";
        //     mainOption.textContent = "Main Branch";
        //     select.appendChild(mainOption);

        //     (response.data || []).forEach(function(item) {
        //         const option = document.createElement("option");
        //         option.value = item.id;
        //         option.textContent = item.name;
        //         select.appendChild(option);
        //     });

        //     if (window.$ && $.fn && $.fn.select2) {
        //         $('#subBrandSelect').select2({
        //             placeholder: 'Select a branch',
        //             allowClear: true
        //         });
        //     }

        //     const savedId = localStorage.getItem('selectedSubAdminId');
        //     if (savedId) {
        //         const exists = (response.data || []).some(function(item) {
        //             return String(item.id) === String(savedId);
        //         });
        //         if (exists) {
        //             $('#subBrandSelect').val(savedId).trigger('change.select2');
        //         } else {
        //             $.post('/clear-subadmin-session', {
        //                 _token: $('meta[name="csrf-token"]').attr('content')
        //             }, function() {
        //                 localStorage.removeItem('selectedSubAdminId');
        //             });
        //         }
        //     }
        // })
        // .catch(function(error) {
        //     console.error('Failed to load sub-admin list:', error);
        // });

        // $('#subBrandSelect').on('change', function() {
        //     const selectedId = $(this).val();
        //     const selectedText = $(this).find('option:selected').text() || '';
        //     $('#currentSelection').text(selectedId ? ('Selected: ' + selectedText) : '');

        //     if (selectedId) {
        //         localStorage.setItem('selectedSubAdminId', selectedId);
        //         $.post('/set-subadmin-session', {
        //             _token: $('meta[name="csrf-token"]').attr('content'),
        //             subAdminId: selectedId
        //         }, function() {
        //             window.location.href = "{{ route('auth.dashboard') }}";
        //         });
        //     } else if (selectedText === "Main Branch") {
        //         $.post('/clear-subadmin-session', {
        //             _token: $('meta[name="csrf-token"]').attr('content')
        //         }, function() {
        //             localStorage.removeItem('selectedSubAdminId');
        //             window.location.href = '/dashboard';
        //         });
        //     }
        // });

        // // ==================== NOTIFICATION FUNCTIONALITY ====================
        // let notificationRefreshInterval = null;

        // document.addEventListener('DOMContentLoaded', function() {
        //     initializeNotifications();

        //     // Auto-refresh every 30 seconds
        //     if (notificationRefreshInterval) {
        //         clearInterval(notificationRefreshInterval);
        //     }
        //     notificationRefreshInterval = setInterval(() => {
        //         const menu = document.getElementById('notificationMenu');
        //         if (menu && menu.style.display === 'block') {
        //             loadNotifications();
        //         }
        //     }, 30000);
        // });

        // function initializeNotifications() {
        //     const notificationToggle = document.getElementById('notificationToggle');
        //     const notificationMenu = document.getElementById('notificationMenu');

        //     if (notificationToggle) {
        //         // Remove any existing event listeners
        //         const newToggle = notificationToggle.cloneNode(true);
        //         notificationToggle.parentNode.replaceChild(newToggle, notificationToggle);

        //         newToggle.addEventListener('click', function(e) {
        //             e.preventDefault();
        //             e.stopPropagation();

        //             if (notificationMenu.style.display === 'block') {
        //                 notificationMenu.style.display = 'none';
        //             } else {
        //                 loadNotifications();
        //                 notificationMenu.style.display = 'block';
        //             }
        //         });
        //     }

        //     // Close dropdown when clicking outside
        //     document.addEventListener('click', function(e) {
        //         if (notificationToggle && notificationMenu) {
        //             if (!notificationToggle.contains(e.target) && !notificationMenu.contains(e.target)) {
        //                 notificationMenu.style.display = 'none';
        //             }
        //         }
        //     });

        //     // Initial load
        //     loadNotifications();
        // }

        // function loadNotifications() {
        //     const token = document.querySelector('meta[name="csrf-token"]');
        //     if (!token) {
        //         console.error('CSRF token not found');
        //         return;
        //     }

        //     fetch('/notifications', {
        //         headers: {
        //             'X-CSRF-TOKEN': token.getAttribute('content'),
        //             'Content-Type': 'application/json',
        //             'Accept': 'application/json'
        //         },
        //         credentials: 'same-origin'
        //     })
        //     .then(response => {
        //         if (!response.ok) {
        //             throw new Error('Network response was not ok');
        //         }
        //         return response.json();
        //     })
        //     .then(response => {
        //         const list = document.getElementById('notificationList');
        //         const count = document.getElementById('notificationCount');
        //         const markAllBtn = document.getElementById('markAllReadBtn');

        //         if (!list) return;

        //         list.innerHTML = '';

        //         if (!response.status || !response.data || response.data.length === 0) {
        //             list.innerHTML = `
    //                 <div class="empty-notification">
    //                     <i class="fa fa-bell-slash"></i>
    //                     No notifications
    //                 </div>
    //             `;
        //             if (count) count.classList.add('d-none');
        //             if (markAllBtn) markAllBtn.style.display = 'none';
        //             return;
        //         }

        //         // Update count badge
        //         if (count) {
        //             const unreadCount = response.count || response.data.filter(n => !n.is_read).length;
        //             if (unreadCount > 0) {
        //                 count.innerText = unreadCount > 99 ? '99+' : unreadCount;
        //                 count.classList.remove('d-none');
        //             } else {
        //                 count.classList.add('d-none');
        //             }
        //         }

        //         // Show mark all button if there are unread notifications
        //         if (markAllBtn) {
        //             const hasUnread = response.data.some(n => !n.is_read);
        //             markAllBtn.style.display = hasUnread ? 'block' : 'none';
        //         }

        //         // Render notifications
        //         response.data.forEach(item => {
        //             const notificationItem = document.createElement('div');
        //             notificationItem.className = `notification-item ${!item.is_read ? 'unread-notification' : ''}`;
        //             notificationItem.setAttribute('data-id', item.id);
        //             notificationItem.onclick = function() {
        //                 handleNotificationClick(item.id, item.link);
        //             };

        //             notificationItem.innerHTML = `
    //                 <div class="d-flex justify-content-between align-items-start">
    //                     <div class="flex-grow-1">
    //                         <strong class="notification-title">${escapeHtml(item.title)}</strong>
    //                         <span class="notification-message">${escapeHtml(item.message)}</span>
    //                         <small class="notification-time">
    //                             <i class="fa fa-clock-o"></i>
    //                             ${formatDate(item.created_at)}
    //                         </small>
    //                     </div>
    //                     ${!item.is_read ? '<span class="notification-dot"></span>' : ''}
    //                 </div>
    //             `;

        //             list.appendChild(notificationItem);
        //         });
        //     })
        //     .catch(error => {
        //         console.error('Error loading notifications:', error);
        //         const list = document.getElementById('notificationList');
        //         if (list) {
        //             list.innerHTML = `
    //                 <div class="empty-notification text-danger">
    //                     <i class="fa fa-exclamation-circle"></i>
    //                     Failed to load notifications
    //                 </div>
    //             `;
        //         }
        //     });
        // }

        // function handleNotificationClick(id, link) {
        //     // Mark as read
        //     const token = document.querySelector('meta[name="csrf-token"]');
        //     if (!token) return;

        //     fetch(`/notifications/${id}/read`, {
        //         method: 'POST',
        //         headers: {
        //             'X-CSRF-TOKEN': token.getAttribute('content'),
        //             'Content-Type': 'application/json'
        //         },
        //         credentials: 'same-origin'
        //     })
        //     .then(() => {
        //         // Update the UI
        //         const notificationItem = document.querySelector(`.notification-item[data-id="${id}"]`);
        //         if (notificationItem) {
        //             notificationItem.classList.remove('unread-notification');
        //             const dot = notificationItem.querySelector('.notification-dot');
        //             if (dot) dot.remove();
        //         }

        //         // Update count
        //         updateNotificationCount();

        //         // Redirect if link exists
        //         if (link && link !== '#') {
        //             window.location.href = link;
        //         }
        //     })
        //     .catch(error => {
        //         console.error('Error marking notification as read:', error);
        //         // Still redirect even if marking fails
        //         if (link && link !== '#') {
        //             window.location.href = link;
        //         }
        //     });
        // }

        // function markAllNotificationsAsRead() {
        //     const token = document.querySelector('meta[name="csrf-token"]');
        //     if (!token) return;

        //     fetch('/notifications/mark-all-read', {
        //         method: 'POST',
        //         headers: {
        //             'X-CSRF-TOKEN': token.getAttribute('content'),
        //             'Content-Type': 'application/json'
        //         },
        //         credentials: 'same-origin'
        //     })
        //     .then(response => response.json())
        //     .then(() => {
        //         // Reload notifications to update UI
        //         loadNotifications();
        //     })
        //     .catch(error => {
        //         console.error('Error marking all as read:', error);
        //     });
        // }

        // function updateNotificationCount() {
        //     const token = document.querySelector('meta[name="csrf-token"]');
        //     if (!token) return;

        //     fetch('/notifications', {
        //         headers: {
        //             'X-CSRF-TOKEN': token.getAttribute('content'),
        //             'Content-Type': 'application/json'
        //         },
        //         credentials: 'same-origin'
        //     })
        //     .then(response => response.json())
        //     .then(response => {
        //         const count = document.getElementById('notificationCount');
        //         if (count) {
        //             const unreadCount = response.count || (response.data ? response.data.filter(n => !n.is_read).length : 0);
        //             if (unreadCount > 0) {
        //                 count.innerText = unreadCount > 99 ? '99+' : unreadCount;
        //                 count.classList.remove('d-none');
        //             } else {
        //                 count.classList.add('d-none');
        //             }
        //         }
        //     })
        //     .catch(error => {
        //         console.error('Error updating count:', error);
        //     });
        // }

        // function formatDate(dateString) {
        //     const date = new Date(dateString);
        //     const now = new Date();
        //     const diffMs = now - date;
        //     const diffMins = Math.floor(diffMs / 60000);
        //     const diffHours = Math.floor(diffMs / 3600000);
        //     const diffDays = Math.floor(diffMs / 86400000);

        //     if (diffMins < 1) return 'Just now';
        //     if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
        //     if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
        //     if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;

        //     return date.toLocaleDateString('en-IN', {
        //         day: 'numeric',
        //         month: 'short',
        //         year: 'numeric'
        //     });
        // }

        // ==================== SEARCH FUNCTIONALITY ====================
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('customerSearch');
            const resultBox = document.getElementById('searchResults');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();

                    if (query.length < 1) {
                        resultBox.style.display = 'none';
                        return;
                    }

                    fetch(`/search-users?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            resultBox.innerHTML = '';
                            let hasResults = false;

                            // ✅ Customers section
                            if (['admin', 'sales-manager', 'inventory-manager', 'staff'].includes(
                                    currentUserRole) && data.users?.length > 0) {
                                hasResults = true;
                                resultBox.innerHTML +=
                                    `<div class="list-group-item fw-bold bg-light">Customers</div>`;
                                data.users.forEach(user => {
                                    const profileUrl = `/customer-view/${user.id}`;
                                    resultBox.innerHTML += `
                                    <a href="${profileUrl}" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <img src="${user.profile_image}" alt="User Image" class="rounded-circle me-2" style="width:35px; height:35px; object-fit: cover;">
                                        <div>
                                            <strong>${escapeHtml(user.name) ?? 'N/A'}</strong>${user.phone ? ' - ' + escapeHtml(user.phone) : ''}<br>
                                            <small class="text-muted">${escapeHtml(user.email) ?? 'N/A'}</small>
                                        </div>
                                    </a>
                                `;
                                });
                            }

                            // ✅ Vendors section
                            if (['admin', 'purchase-manager', 'inventory-manager', 'staff'].includes(
                                    currentUserRole) && data.vendors?.length > 0) {
                                hasResults = true;
                                resultBox.innerHTML +=
                                    `<div class="list-group-item fw-bold bg-light mt-2">Vendors</div>`;
                                data.vendors.forEach(vendor => {
                                    const vendorUrl = `/vendor-view/${vendor.id}`;
                                    resultBox.innerHTML += `
                                    <a href="${vendorUrl}" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <img src="${vendor.profile_image}" alt="Vendor Image" class="rounded-circle me-2" style="width:35px; height:35px; object-fit: cover;">
                                        <div>
                                            <strong>${escapeHtml(vendor.name) ?? 'N/A'}</strong><br>
                                            <small class="text-muted">${escapeHtml(vendor.email) ?? 'N/A'}</small>
                                        </div>
                                    </a>
                                `;
                                });
                            }

                            // ✅ Products section
                            if (['admin', 'purchase-manager', 'inventory-manager', 'staff'].includes(
                                    currentUserRole) && data.products?.length > 0) {
                                hasResults = true;
                                resultBox.innerHTML +=
                                    `<div class="list-group-item fw-bold bg-light mt-2">Products</div>`;
                                data.products.forEach(product => {
                                    resultBox.innerHTML += `
                                    <a href="/product-view/${product.id}" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <img src="${product.image}" alt="Product Image" class="rounded me-2" style="width:35px; height:35px; object-fit: cover;">
                                        <div>
                                            <strong>${escapeHtml(product.name) ?? 'N/A'}</strong><br>
                                            <small class="text-muted">Price: ₹${product.price ?? 'N/A'}</small>
                                        </div>
                                    </a>
                                `;
                                });
                            }

                            // ✅ Orders section
                            if (['admin', 'sales-manager', 'inventory-manager', 'staff'].includes(
                                    currentUserRole) && data.orders?.length > 0) {
                                hasResults = true;
                                resultBox.innerHTML +=
                                    `<div class="list-group-item fw-bold bg-light mt-2">Orders</div>`;
                                data.orders.forEach(order => {
                                    resultBox.innerHTML += `
                                    <a href="/sales-details/${order.id}" class="list-group-item list-group-item-action">
                                        <div class="d-flex align-items-center mb-1">
                                            <img src="{{ image_path('admin/assets/img/icons/cart.svg') }}" width="20" height="20" class="me-2" alt="cart">
                                            <strong>Order #: ${order.order_number ?? 'N/A'}</strong>
                                        </div>
                                        <div>
                                            <small>Customer: ${escapeHtml(order.user_name) ?? 'N/A'}</small><br>
                                            <small>Total: ₹${order.total_amount ?? 'N/A'} | Status: ${order.payment_status ?? 'N/A'}</small>
                                        </div>
                                    </a>
                                `;
                                });
                            }

                            if (!hasResults) {
                                resultBox.innerHTML =
                                    '<div class="list-group-item text-center text-muted">No results found</div>';
                            }

                            resultBox.style.display = 'block';
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                            resultBox.innerHTML =
                                '<div class="list-group-item text-center text-danger">Error loading results</div>';
                            resultBox.style.display = 'block';
                        });
                });
            }

            // Hide dropdown on outside click
            document.addEventListener('click', function(e) {
                if (searchInput && resultBox && !searchInput.contains(e.target) && !resultBox.contains(e
                        .target)) {
                    resultBox.style.display = 'none';
                }
            });
        });

        // Helper function to escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // ==================== SIDEBAR TOGGLE FUNCTIONALITY ====================
        $(document).on('mouseover', function(e) {
            const $toggleBtn = $('#toggle_btn1');
            const isTabletSize = $(window).width() >= 768 && $(window).width() <= 1024;
            const isButtonAvailable = $toggleBtn.length > 0 && (isTabletSize || $toggleBtn.is(':visible'));

            if ($('body').hasClass('mini-sidebar') && isButtonAvailable) {
                const isInsideSidebar = $(e.target).closest('.sidebar').length;
                if (isInsideSidebar) {
                    $('body').addClass('expand-menu');
                    $('.subdrop + ul').slideDown();
                } else {
                    $('body').removeClass('expand-menu');
                    $('.subdrop + ul').slideUp();
                }
            }
        });

        // Toggle button handler
        $(document).on('click', '#toggle_btn1', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if ($(this).data('processing')) {
                return false;
            }
            $(this).data('processing', true);

            const body = $('body');
            const $btn = $(this);

            if (body.hasClass('mini-sidebar')) {
                body.removeClass('mini-sidebar');
                $btn.addClass('active');
                $('.subdrop + ul').slideDown();
                localStorage.setItem('screenModeNightTokenState', 'night');
                setTimeout(function() {
                    body.removeClass('mini-sidebar');
                    $('.header-left').addClass('active');
                }, 100);
            } else {
                body.addClass('mini-sidebar');
                $btn.removeClass('active');
                $('.subdrop + ul').slideUp();
                localStorage.removeItem('screenModeNightTokenState');
                setTimeout(function() {
                    body.addClass('mini-sidebar');
                    $('.header-left').removeClass('active');
                }, 100);
            }

            setTimeout(() => {
                $btn.data('processing', false);
            }, 300);

            return false;
        });

        // ==================== BRANCH DROPDOWN FUNCTIONALITY ====================
        function isBranchMobileView() {
            return window.matchMedia('(max-width: 991px)').matches;
        }

        function syncMainBranchOptionVisibility() {
            const select = document.getElementById('subBrandSelect');
            if (!select) return;

            const existingMainOption = Array.from(select.options).find(function(option) {
                return option.value === '' && option.textContent.trim() === 'Main Branch';
            });

            if (isBranchMobileView()) {
                if (existingMainOption) {
                    const wasSelected = existingMainOption.selected;
                    existingMainOption.remove();
                    if (wasSelected && select.options.length > 0) {
                        select.selectedIndex = 0;
                    }
                }
                return;
            }

            if (!existingMainOption) {
                const mainOption = document.createElement('option');
                mainOption.value = '';
                mainOption.textContent = 'Main Branch';
                select.insertBefore(mainOption, select.firstChild);
            }
        }

        function applyBranchDropdownUI() {
            if (!(window.$ && $.fn && $.fn.select2)) return;

            const $select = $('#subBrandSelect');
            if (!$select.length) return;

            // On mobile, keep native select for reliable rendering in tight header space.
            if (isBranchMobileView()) {
                if ($select.data('select2')) {
                    $select.select2('destroy');
                }
                $select.css('display', 'block');
                return;
            }

            // On desktop/tablet, keep existing Select2 behavior.
            if ($select.data('select2')) {
                $select.select2('destroy');
            }
            $select.select2({
                placeholder: 'Select a branch',
                allowClear: true,
                width: '100%'
            });
        }

        function applyErpDropdownUI() {
            if (!(window.$ && $.fn && $.fn.select2)) return;

            const $select = $('#erpSelect');
            if (!$select.length) return;

            if (isBranchMobileView()) {
                if ($select.data('select2')) {
                    $select.select2('destroy');
                }
                $select.css('display', 'block');
                return;
            }

            if ($select.data('select2')) {
                $select.select2('destroy');
            }
            $select.select2({
                placeholder: 'Select ERP',
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: Infinity
            });
        }

        (function initializeBranchDropdown() {
            const container = document.getElementById('subBranchContainer');
            const select = document.getElementById('subBrandSelect');
            if (container) {
                container.style.display = 'block';
            }
            if (select) {
                select.innerHTML = "";
                syncMainBranchOptionVisibility();
                applyBranchDropdownUI();
            }
        })();

        (function initializeErpDropdown() {
            const container = document.getElementById('erpContainer');
            const select = document.getElementById('erpSelect');
            if (!container || !select) return;

            container.style.display = 'block';
            applyErpDropdownUI();

            const currentOrigin = window.location.origin.replace(/\/$/, '');
            const matchedOption = Array.from(select.options).find(function(option) {
                return option.value && option.value.replace(/\/$/, '') === currentOrigin;
            });

            if (matchedOption) {
                select.value = matchedOption.value;
            }

            if (window.$) {
                $('#erpSelect').trigger('change.select2');
            }
        })();

        fetch('/api/get_subadmin', {
                headers: {
                    "Authorization": "Bearer " + localStorage.getItem("authToken"),
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            .then(res => res.json())
            .then(response => {
                if (!response) {
                    console.warn('No response from /api/get_subadmin');
                    return;
                }

                const select = document.getElementById("subBrandSelect");
                if (!select) return;

                select.innerHTML = "";
                document.getElementById('subBranchContainer').style.display = 'block';

                (response.data || []).forEach(function(item) {
                    const option = document.createElement("option");
                    option.value = item.id;
                    option.textContent = item.name;
                    select.appendChild(option);
                });

                syncMainBranchOptionVisibility();
                applyBranchDropdownUI();

                const savedId = localStorage.getItem('selectedSubAdminId');
                if (savedId) {
                    const exists = (response.data || []).some(function(item) {
                        return String(item.id) === String(savedId);
                    });
                    if (exists) {
                        const $select = $('#subBrandSelect');
                        $select.val(savedId);
                        if ($select.data('select2')) {
                            $select.trigger('change.select2');
                        }
                    } else {
                        $.post('/clear-subadmin-session', {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }, function() {
                            localStorage.removeItem('selectedSubAdminId');
                        });
                    }
                }
            })
            .catch(function(error) {
                console.error('Failed to load sub-admin list:', error);
            });

        let wasBranchMobileView = isBranchMobileView();
        window.addEventListener('resize', function() {
            const nowMobile = isBranchMobileView();
            if (nowMobile !== wasBranchMobileView) {
                wasBranchMobileView = nowMobile;
                syncMainBranchOptionVisibility();
                applyErpDropdownUI();
                applyBranchDropdownUI();
            }
        });

        $('#erpSelect').on('change', function() {
            const selectedUrl = ($(this).val() || '').trim();
            if (!selectedUrl) {
                return;
            }

            const selectedName = $(this).find('option:selected').text().trim();
            const currentErpName = $(this).data('current-erp');

            if (selectedName === currentErpName) {
                return;
            }

            const normalizedSelectedUrl = selectedUrl.replace(/\/$/, '');
            const normalizedCurrentUrl = window.location.origin.replace(/\/$/, '');

            if (normalizedSelectedUrl !== normalizedCurrentUrl) {
                window.location.href = selectedUrl;
            }
        });

        $('#subBrandSelect').on('change', function() {
            const selectedId = $(this).val();
            const selectedText = $(this).find('option:selected').text() || '';
            $('#currentSelection').text(selectedId ? ('Selected: ' + selectedText) : '');

            if (selectedId) {
                localStorage.setItem('selectedSubAdminId', selectedId);
                $.post('/set-subadmin-session', {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    subAdminId: selectedId
                }, function() {
                    window.location.href = "{{ route('auth.dashboard') }}";
                });
            } else if (selectedText === "Main Branch") {
                $.post('/clear-subadmin-session', {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, function() {
                    localStorage.removeItem('selectedSubAdminId');
                    window.location.href = '/dashboard';
                });
            }
        });


        // ==================== NOTIFICATION FUNCTIONALITY ====================
        let notificationRefreshInterval = null;
        let lastNotificationId = 0;
        let notificationLiveReady = false;
        let notificationAudioContext = null;

        document.addEventListener('click', function unlockNotificationAudio() {
            if (notificationAudioContext && notificationAudioContext.state === 'suspended') {
                notificationAudioContext.resume();
            }
        }, { once: true });

        document.addEventListener('DOMContentLoaded', function() {
            initializeNotifications();
            requestBrowserNotificationPermission();
            initializeLiveNotifications();

            if (notificationRefreshInterval) {
                clearInterval(notificationRefreshInterval);
            }
            notificationRefreshInterval = setInterval(pollLiveNotifications, 15000);
        });

        function requestBrowserNotificationPermission() {
            if (!('Notification' in window) || Notification.permission !== 'default') {
                return;
            }

            Notification.requestPermission().catch(() => {});
        }

        function playNotificationSound() {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) {
                    return;
                }

                if (!notificationAudioContext) {
                    notificationAudioContext = new AudioContext();
                }

                if (notificationAudioContext.state === 'suspended') {
                    notificationAudioContext.resume();
                }

                const ctx = notificationAudioContext;
                const now = ctx.currentTime;

                [880, 1174].forEach((frequency, index) => {
                    const oscillator = ctx.createOscillator();
                    const gain = ctx.createGain();
                    oscillator.type = 'sine';
                    oscillator.frequency.value = frequency;
                    gain.gain.setValueAtTime(0.0001, now + (index * 0.12));
                    gain.gain.exponentialRampToValueAtTime(0.18, now + (index * 0.12) + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + (index * 0.12) + 0.28);
                    oscillator.connect(gain);
                    gain.connect(ctx.destination);
                    oscillator.start(now + (index * 0.12));
                    oscillator.stop(now + (index * 0.12) + 0.3);
                });
            } catch (error) {
                console.warn('Notification sound failed:', error);
            }
        }

        function showBrowserNotification(item) {
            if (!('Notification' in window) || Notification.permission !== 'granted') {
                return;
            }

            try {
                const notification = new Notification(item.title || 'New Notification', {
                    body: item.message || '',
                    icon: "{{ image_path('admin/assets/img/icons/notification-bing.svg') }}",
                    tag: 'erp-notification-' + item.id,
                    requireInteraction: false,
                });

                notification.onclick = function() {
                    window.focus();
                    if (item.link && item.link !== '#') {
                        window.location.href = item.link;
                    }
                    notification.close();
                };
            } catch (error) {
                console.warn('Browser notification failed:', error);
            }
        }

        function showInPageNotificationToast(item) {
            let container = document.getElementById('erp-live-notification-toasts');
            if (!container) {
                container = document.createElement('div');
                container.id = 'erp-live-notification-toasts';
                container.className = 'erp-live-notification-toasts';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.className = 'erp-live-notification-toast';
            toast.innerHTML = `
                <div class="erp-live-notification-toast-icon"><i class="fa fa-bell"></i></div>
                <div class="erp-live-notification-toast-body">
                    <strong>${escapeHtml(item.title || 'Notification')}</strong>
                    <span>${escapeHtml(item.message || '')}</span>
                </div>
                <button type="button" class="erp-live-notification-toast-close" aria-label="Close">&times;</button>
            `;

            toast.addEventListener('click', function(e) {
                if (e.target.closest('.erp-live-notification-toast-close')) {
                    toast.remove();
                    return;
                }
                if (item.link && item.link !== '#') {
                    window.location.href = item.link;
                }
            });

            container.prepend(toast);

            setTimeout(() => {
                toast.classList.add('is-hiding');
                setTimeout(() => toast.remove(), 300);
            }, 8000);
        }

        function alertForNewNotifications(items) {
            if (!items || !items.length) {
                return;
            }

            items.forEach(item => {
                playNotificationSound();
                showBrowserNotification(item);
                showInPageNotificationToast(item);
            });

            document.querySelectorAll('.notification-bell').forEach(bell => {
                bell.classList.add('notification-bell-ring');
                setTimeout(() => bell.classList.remove('notification-bell-ring'), 1200);
            });
        }

        function initializeLiveNotifications() {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (!token) {
                return;
            }

            fetch('/notifications/live-updates', {
                    headers: {
                        'X-CSRF-TOKEN': token.getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(response => {
                    if (!response.status) {
                        return;
                    }

                    lastNotificationId = response.max_id || 0;
                    setNotificationCounts(response.unread_count || 0);
                    notificationLiveReady = true;
                })
                .catch(error => {
                    console.error('Notification baseline failed:', error);
                });
        }

        function pollLiveNotifications() {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (!token) {
                return;
            }

            fetch('/notifications/live-updates?after_id=' + lastNotificationId, {
                    headers: {
                        'X-CSRF-TOKEN': token.getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(response => {
                    if (!response.status) {
                        return;
                    }

                    setNotificationCounts(response.unread_count || 0);

                    if (typeof response.max_id === 'number') {
                        if (notificationLiveReady && response.new_notifications?.length) {
                            alertForNewNotifications(response.new_notifications);

                            const hasOpenMenu = getNotificationInstances().some(instance =>
                                instance.menu.style.display === 'block');
                            if (hasOpenMenu) {
                                loadNotifications();
                            }
                        }

                        lastNotificationId = response.max_id;
                        notificationLiveReady = true;
                    }
                })
                .catch(error => {
                    console.error('Live notification poll failed:', error);
                });
        }

        function getNotificationInstances() {
            return Array.from(document.querySelectorAll('.notification-wrapper')).map(wrapper => ({
                wrapper,
                toggle: wrapper.querySelector('[data-notification-toggle]'),
                menu: wrapper.querySelector('[data-notification-menu]'),
                list: wrapper.querySelector('[data-notification-list]'),
                count: wrapper.querySelector('[data-notification-count]')
            })).filter(instance => instance.toggle && instance.menu);
        }

        function closeNotificationMenus(exceptMenu = null) {
            getNotificationInstances().forEach(instance => {
                if (!exceptMenu || instance.menu !== exceptMenu) {
                    instance.menu.style.display = 'none';
                }
            });
        }

        function setNotificationCounts(unreadCount) {
            getNotificationInstances().forEach(instance => {
                if (!instance.count) return;

                if (unreadCount > 0) {
                    instance.count.innerText = unreadCount > 99 ? '99+' : unreadCount;
                    instance.count.classList.remove('d-none');
                } else {
                    instance.count.classList.add('d-none');
                }
            });
        }

        function renderEmptyNotifications(message, isError = false) {
            const iconClass = isError ? 'fa fa-exclamation-circle' : 'fa fa-bell-slash';
            const stateClass = isError ? 'empty-notification text-danger' : 'empty-notification';

            getNotificationInstances().forEach(instance => {
                if (!instance.list) return;

                instance.list.innerHTML = `
                <div class="${stateClass}">
                    <i class="${iconClass}"></i>
                    ${message}
                </div>
            `;
            });
        }

        function initializeNotifications() {
            const notificationInstances = getNotificationInstances();

            notificationInstances.forEach(instance => {
                const newToggle = instance.toggle.cloneNode(true);
                instance.toggle.parentNode.replaceChild(newToggle, instance.toggle);
                instance.toggle = newToggle;

                newToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const isOpen = instance.menu.style.display === 'block';
                    closeNotificationMenus();

                    if (!isOpen) {
                        loadNotifications();
                        instance.menu.style.display = 'block';
                    }
                });
            });

            document.addEventListener('click', function(e) {
                getNotificationInstances().forEach(instance => {
                    if (!instance.toggle.contains(e.target) && !instance.menu.contains(e.target)) {
                        instance.menu.style.display = 'none';
                    }
                });
            });

            loadNotifications();
        }

        function notificationGroupKey(type) {
            const value = String(type || '');
            if (value === 'login' || value === 'vendor_login') return 'login';
            if (value === 'product' || value.indexOf('product') === 0 || value === 'price_changed' || value === 'price_update_request') return 'products';
            return 'other';
        }

        function createNotificationItem(item) {
            const group = notificationGroupKey(item.type);
            const notificationItem = document.createElement('div');
            notificationItem.className = `notification-item group-${group} ${!item.is_read ? 'unread-notification' : ''}`;
            notificationItem.setAttribute('data-id', item.id);
            notificationItem.onclick = function(e) {
                if (e.target.closest('.notification-action')) {
                    return;
                }
                handleNotificationClick(item.id, item.link);
            };

            notificationItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <strong class="notification-title">${escapeHtml(item.title)}</strong>
                        <span class="notification-message">${escapeHtml(item.message)}</span>
                        <small class="notification-time">
                            <i class="fa fa-clock-o"></i>
                            ${formatDate(item.created_at)}
                        </small>
                    </div>
                    ${!item.is_read ? '<span class="notification-dot"></span>' : ''}
                </div>
            `;

            return notificationItem;
        }

        function loadNotifications() {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (!token) {
                console.error('CSRF token not found');
                return;
            }

            // Get selected subadmin ID from localStorage if exists
            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId') || '';

            fetch('/notifications?selectedSubAdminId=' + selectedSubAdminId, {
                    headers: {
                        'X-CSRF-TOKEN': token.getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(response => {
                    const markAllBtn = document.getElementById('markAllReadBtn');
                    const notificationInstances = getNotificationInstances();

                    if (notificationInstances.length === 0) return;

                    notificationInstances.forEach(instance => {
                        if (instance.list) {
                            instance.list.innerHTML = '';
                        }
                    });

                    if (!response.status || !response.data || response.data.length === 0) {
                        renderEmptyNotifications('No notifications');
                        setNotificationCounts(0);
                        if (markAllBtn) markAllBtn.style.display = 'none';
                        return;
                    }

                    // Update count badge
                    const unreadCount = response.count || response.data.filter(n => !n.is_read).length;
                    setNotificationCounts(unreadCount);

                    // Show mark all button if there are unread notifications
                    if (markAllBtn) {
                        const hasUnread = response.data.some(n => !n.is_read);
                        markAllBtn.style.display = hasUnread ? 'block' : 'none';
                    }

                    const groups = response.groups || {
                        login: response.data.filter(item => notificationGroupKey(item.type) === 'login'),
                        products: response.data.filter(item => notificationGroupKey(item.type) === 'products'),
                        other: response.data.filter(item => notificationGroupKey(item.type) === 'other')
                    };
                    const sections = [
                        { key: 'login', label: 'Login' },
                        { key: 'products', label: 'Products' },
                        { key: 'other', label: 'Other' }
                    ];

                    sections.forEach(section => {
                        const items = groups[section.key] || [];
                        if (!items.length) return;
                        notificationInstances.forEach(instance => {
                            if (!instance.list) return;
                            const heading = document.createElement('div');
                            heading.className = 'notification-group-title notification-group-' + section.key;
                            heading.textContent = section.label;
                            instance.list.appendChild(heading);
                            items.forEach(item => {
                                instance.list.appendChild(createNotificationItem(item));
                            });
                        });
                    });
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    renderEmptyNotifications('Failed to load notifications', true);
                });
        }

        function handleNotificationClick(id, link) {
            // Mark as read
            const token = document.querySelector('meta[name="csrf-token"]');
            if (!token) return;

            fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token.getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(() => {
                    // Update the UI
                    document.querySelectorAll(`.notification-item[data-id="${id}"]`).forEach(notificationItem => {
                        notificationItem.classList.remove('unread-notification');
                        const dot = notificationItem.querySelector('.notification-dot');
                        if (dot) dot.remove();
                    });

                    // Update count
                    updateNotificationCount();

                    // Redirect if link exists
                    if (link && link !== '#') {
                        window.location.href = link;
                    }
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                    // Still redirect even if marking fails
                    if (link && link !== '#') {
                        window.location.href = link;
                    }
                });
        }

        function markAllNotificationsAsRead() {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (!token) return;

            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId') || '';

            fetch('/notifications/mark-all-read?selectedSubAdminId=' + selectedSubAdminId, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token.getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(() => {
                    // Reload notifications to update UI
                    loadNotifications();
                })
                .catch(error => {
                    console.error('Error marking all as read:', error);
                });
        }

        function updateNotificationCount() {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (!token) return;

            const selectedSubAdminId = localStorage.getItem('selectedSubAdminId') || '';

            fetch('/notifications?selectedSubAdminId=' + selectedSubAdminId, {
                    headers: {
                        'X-CSRF-TOKEN': token.getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(response => {
                    setNotificationCounts(response.count || 0);
                })
                .catch(error => {
                    console.error('Error updating count:', error);
                });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
            if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
            if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;

            return date.toLocaleDateString('en-IN', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>

    <style>
        /* Additional styles for search results */
        .list-group-item-action {
            transition: all 0.2s ease;
        }

        .list-group-item-action:hover {
            background-color: #fef9f0 !important;
            transform: translateX(2px);
        }

        /* Notification animation */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .notification-dropdown {
            animation: slideDown 0.2s ease;
        }

        :root[data-theme="dark"] .notification-dropdown {
            background: #0f172a !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.45) !important;
        }

        :root[data-theme="dark"] .notification-header,
        :root[data-theme="dark"] .notification-body,
        :root[data-theme="dark"] .notification-footer {
            background: #0f172a !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        :root[data-theme="dark"] .notification-title,
        :root[data-theme="dark"] .notification-header,
        :root[data-theme="dark"] .notification-footer button {
            color: #f8fafc !important;
        }

        :root[data-theme="dark"] .notification-message {
            color: #cbd5e1 !important;
        }

        :root[data-theme="dark"] .notification-time {
            color: #94a3b8 !important;
        }

        :root[data-theme="dark"] .notification-item {
            background: #0f172a !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        }

        :root[data-theme="dark"] .notification-item:hover {
            background: rgba(255, 159, 67, 0.12) !important;
        }

        :root[data-theme="dark"] .notification-item.unread-notification {
            background: rgba(255, 159, 67, 0.08) !important;
        }

        :root[data-theme="dark"] .notification-header a,
        :root[data-theme="dark"] .notification-footer button {
            color: #ff9f43 !important;
        }

        /* Scrollbar styling */
        .notification-body::-webkit-scrollbar {
            width: 5px;
        }

        .notification-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .notification-body::-webkit-scrollbar-thumb {
            background: #ff9f43;
            border-radius: 5px;
        }

        .notification-body::-webkit-scrollbar-thumb:hover {
            background: #ff8c2e;
        }

        @keyframes notificationBellRing {
            0%, 100% { transform: rotate(0deg); }
            15% { transform: rotate(14deg); }
            30% { transform: rotate(-12deg); }
            45% { transform: rotate(10deg); }
            60% { transform: rotate(-8deg); }
            75% { transform: rotate(4deg); }
        }

        .notification-bell-ring {
            animation: notificationBellRing 0.9s ease-in-out;
            transform-origin: top center;
        }

        .erp-live-notification-toasts {
            position: fixed;
            top: 78px;
            right: 20px;
            z-index: 10050;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 360px;
            width: calc(100vw - 40px);
            pointer-events: none;
        }

        .erp-live-notification-toast {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: #fff;
            border: 1px solid #ffe0b8;
            border-left: 4px solid #ff9f43;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(27, 40, 80, 0.15);
            padding: 14px 16px;
            cursor: pointer;
            pointer-events: auto;
            animation: slideDown 0.25s ease;
        }

        .erp-live-notification-toast.is-hiding {
            opacity: 0;
            transform: translateX(12px);
            transition: all 0.3s ease;
        }

        .erp-live-notification-toast-icon {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #fff4e8;
            color: #ff9f43;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .erp-live-notification-toast-body {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }

        .erp-live-notification-toast-body strong {
            color: #1b2850;
            font-size: 14px;
            line-height: 1.3;
        }

        .erp-live-notification-toast-body span {
            color: #5b6670;
            font-size: 13px;
            line-height: 1.4;
        }

        .erp-live-notification-toast-close {
            border: none;
            background: transparent;
            color: #98a2b3;
            font-size: 20px;
            line-height: 1;
            padding: 0;
            margin-left: auto;
            flex-shrink: 0;
        }

        .mobile-header-notification {
            display: none;
            position: absolute;
            right: 84px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 5;
            margin: 0;
        }

        .mobile-header-notification .nav-link {
            padding: 8px 10px;
            /* margin: -15px !important; */
        }

        .mobile-header-clock {
            display: none;
            position: absolute;
            right: 124px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 5;
            margin: 0;
        }

        .mobile-header-clock .nav-link {
            padding: 8px 10px;
        }

        .nav-link{
            padding: .5rem .5rem;
        }

        @media (max-width: 1023px) {
            .mobile-header-notification {
                display: block;
            }
            .mobile-header-clock {
                display: block;
            }
        }

        @media (max-width: 575px) {
            .mobile-header-notification {
                right: 45px;
            }

            .mobile-header-notification .notification-dropdown {
                right: -34px;
                width: min(320px, calc(100vw - 24px));
            }

            .mobile-header-clock {
                right: 75px;
            }
        }

        /* Search input focus */
        #customerSearch:focus {
            border-color: #ff9f43;
            box-shadow: 0 0 0 0.2rem rgba(255, 159, 67, 0.25);
        }
    </style>
    <style>
        @media only screen and (max-width: 767px) {
            .select2-container {
                min-width: unset !important;
            }
        }

        /* iPhone SE */
        @media screen and (max-width: 340px) {
            .select2-container {
                width: 123px !important;
            }
        }

        @media screen and (min-width: 341px) and (max-width: 374px) {
            .select2-container {
                width: 140px !important;
            }
        }

        /* iPhone XR / iPhone 11 */
        @media screen and (min-width: 375px) and (max-width: 390px) {
            .select2-container {
                width: 145px !important;
            }
        }

        /* iPhone XR / iPhone 11 */
        @media screen and (min-width: 391px) and (max-width: 420px) {
            .select2-container {
                width: 165px !important;
            }
        }

        /* iPhone XR / iPhone 11 */
        @media screen and (min-width: 430px) and (max-width: 480px) {
            .select2-container {
                width: 175px !important;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 819px) {
            .select2-container {
                min-width: auto !important;
            }

            .select2-container {
                width: 340px !important;
            }
        }

        @media screen and (min-width: 820px) and (max-width: 1023px) {
            .select2-container {
                min-width: auto !important;
            }

            .select2-container {
                width: 368px !important;
            }

            .header-new-order-button {
                display: none !important;
            }

            #toggle_btn1 {
                display: none !important;
            }


            .header-search {
                display: none !important;
            }

            .logo-view {
                float: none;
            }
        }

        @media screen and (width: 768px) and (height: 1024px) {
            .header-new-order-button {
                display: none !important;
            }

            .header-search {
                display: none !important;
            }

            .logo-view {
                float: none;
            }

            #toggle_btn1 {
                display: none !important;
            }
        }


        @media screen and (width: 1024px) and (height: 1366px), screen and (width: 1032px) and (height: 1376px), screen and (width: 1032px) {
            .select2-container {
                min-width: auto !important;
            }

            .select2-container {
                width: 151px !important;
            }

            /* .header .header-left {
                width: 78px !important;
               } */
            /* .web_button{
                position: absolute;
                z-index: 9999;
               }


               #toggle_btn1  {
                display: none !important;
               }
               .header .mobile_btn {
                display: block !important;
               }
        */
            /* .header-search{
                display: none !important;
               }
               .header-search-container{
                display: none !important;
               } */
            .search-view {
                width: 285px !important;
            }

            .logo-view {
                float: none;
                /* display: none !important; */
            }

            /* .header-new-order-button{
                display: none !important;
               } */
            .mini-sidebar .header-left .logo-small {
                display: none !important;
            }

            .hide-on-ipad-pro,
            .header-hr-dropdown {
                display: none !important;
            }
        }

        @media screen and (width: 540px) and (height: 720px) {
            .select2-container {
                min-width: auto !important;
            }

            .select2-container {
                width: 225px !important;
            }
        }

        @media screen and (width: 1024px) and (height: 600px) {
            .select2-container {
                min-width: auto !important;
            }

            .select2-container {
                width: 155px !important;
            }

            /* .header .header-left {
                width: 78px !important;
               } */
            .web_button {
                position: absolute;
                z-index: 9999;
            }

            .search-view {
                width: 285px !important;
            }

            .logo-view {
                float: none;
                /* display: none !important; */
            }

            .mini-sidebar .header-left .logo-small {
                display: none !important;
            }

            div#toggle_btn1 {
                position: relative !important;
            }
        }

        @media screen and (width: 1280px) and (height: 800px) {
            .select2-container {
                min-width: auto !important;
            }

            .select2-container {
                width: 215px !important;
            }

            /* .header .header-left {
                width: 78px !important;
               } */

            .web_button {
                position: absolute;
                z-index: 9999;
            }
        }

        .search-view {
            width: 285px !important;
        }

        .logo-view {
            float: none;
            /* display: none !important; */
        }

        .mini-sidebar .header-left .logo-small {
            display: none !important;
        }

        div#toggle_btn1 {
            position: relative !important;
        }

        @media screen and (max-width: 767px) {

            .header .header-left {
                align-items: center !important;
                border-right: 0 !important;
                display: flex !important;
                height: 60px !important;
                justify-content: flex-start !important;
                left: 60px !important;
                padding: 0 !important;
                right: auto !important;
                width: 90px !important;
            }

            .header .header-left .logo {
                align-items: center !important;
                display: flex !important;
                justify-content: flex-start !important;
                margin: 0 !important;
                width: auto !important;
            }

            .header .header-left .logo .logo-view {
                margin: 0 !important;
                max-width: 86px !important;
                width: auto !important;
            }

            .header .header-left .logo-small,
            #toggle_btn1,
            #erpContainer {
                display: none !important;
            }

            #subBranchContainer #subBrandSelect,
            #subBranchContainer .select2-container {
                margin-left: 0 !important;
                margin-top: 0 !important;
            }

            .page-wrapper {
                padding-top: 15px !important;
            }

            .page-wrapper .content {
                padding-top: 0 !important;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 1040px) {
            #erpContainer {
                margin-right: 12px !important;
            }

            #subBranchContainer {
                margin-left: 12px !important;
            }

            #erpContainer #erpSelect,
            #subBranchContainer #subBrandSelect {
                width: 125px !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const themeToggleBtn = document.getElementById('global-theme-toggle');
            const themeIcon = document.getElementById('theme-toggle-icon');
            
            // Check current theme from localStorage
            let currentTheme = localStorage.getItem('theme') || 'light';
            
            function updateIcon(theme) {
                if (theme === 'dark') {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                    themeIcon.style.color = '#ffffffff'; // Sun color
                } else {
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                    themeIcon.style.color = '#1b2850'; // Moon color
                }
            }

            // Initial icon setup
            if(themeToggleBtn) {
                document.documentElement.setAttribute('data-theme', currentTheme);
                updateIcon(currentTheme);

                themeToggleBtn.addEventListener('click', function () {
                    currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-theme', currentTheme);
                    localStorage.setItem('theme', currentTheme);
                    updateIcon(currentTheme);
                });
            }
        });
    </script>
    <style>
        #global-theme-toggle {
            transition: transform 0.3s ease;
            display: flex !important;
            align-items: center;
            justify-content: center;
        }
        :root[data-theme="dark"] #global-theme-toggle {
            background: rgba(255, 159, 67, 0.1);
        }

        :root[data-theme="dark"] .header .select2-container--default .select2-selection--single .select2-selection__rendered,
        :root[data-theme="dark"] .header .select2-container--default .select2-selection--single .select2-selection__placeholder,
        :root[data-theme="dark"] .header .select2-container--default .select2-selection--single .select2-selection__arrow,
        :root[data-theme="dark"] .header .select2-container--default .select2-selection--single .select2-selection__arrow b,
        :root[data-theme="dark"] .header .select2-container--default .select2-search--dropdown .select2-search__field,
        :root[data-theme="dark"] .header .select2-container--default .select2-results__option {
            color: #f8fafc !important;
        }

        :root[data-theme="dark"] .header .select2-container--default .select2-selection--single,
        :root[data-theme="dark"] .header .select2-container--default .select2-dropdown,
        :root[data-theme="dark"] .header .select2-container--default .select2-search--dropdown .select2-search__field {
            background-color: #0f172a !important;
            border-color: rgba(255, 255, 255, 0.14) !important;
        }

        :root[data-theme="dark"] .select2-container--default .select2-dropdown,
        :root[data-theme="dark"] .select2-container--default .select2-results,
        :root[data-theme="dark"] .select2-container--default .select2-results__options {
            background-color: #0f172a !important;
            color: #f8fafc !important;
            border-color: rgba(255, 255, 255, 0.14) !important;
        }

        :root[data-theme="dark"] .select2-container--default .select2-results__option {
            color: #f8fafc !important;
        }

        :root[data-theme="dark"] .select2-container--default .select2-results__option--highlighted[aria-selected],
        :root[data-theme="dark"] .select2-container--default .select2-results__option--highlighted[data-selected] {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
        }

        :root[data-theme="dark"] .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: rgba(255, 159, 67, 0.14) !important;
            color: #ffffff !important;
        }

        :root[data-theme="dark"] .header .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #f8fafc transparent transparent transparent !important;
        }

        :root[data-theme="dark"] .header .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #f8fafc transparent !important;
        }

        :root[data-theme="dark"] .header .select2-container--default .select2-search--dropdown .select2-search__field::placeholder {
            color: #94a3b8 !important;
            opacity: 1;
        }

        :root[data-theme="dark"] .header .bar-icon span {
            background-color: #f8fafc !important;
        }

        :root[data-theme="dark"] .header .header-search input#customerSearch {
            color: #f8fafc !important;
            background-color: #0f172a !important;
            border-color: rgba(255, 255, 255, 0.14) !important;
        }

        :root[data-theme="dark"] .header .header-search input#customerSearch::placeholder {
            color: #94a3b8 !important;
            opacity: 1;
        }

        :root[data-theme="dark"] .header .header-search #searchResults {
            background-color: #0f172a !important;
            border: 1px solid rgba(255, 255, 255, 0.14) !important;
        }

        :root[data-theme="dark"] .header .header-search #searchResults .list-group-item,
        :root[data-theme="dark"] .header .header-search #searchResults .list-group-item *,
        :root[data-theme="dark"] .header .header-search #searchResults .dropdown-item,
        :root[data-theme="dark"] .header .header-search #searchResults .dropdown-item * {
            color: #f8fafc !important;
        }

        :root[data-theme="dark"] .header .header-search #searchResults .list-group-item:hover,
        :root[data-theme="dark"] .header .header-search #searchResults .dropdown-item:hover {
            background-color: rgba(255, 159, 67, 0.12) !important;
        }

        :root[data-theme="dark"] .header .header-search #searchResults img {
            filter: brightness(0) invert(1) !important;
        }
    </style>
@endpush

<!-- Quick Product Search Modal -->
<div class="modal fade" id="quickProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Product Search</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="quickProductTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="qp-image-tab" data-bs-toggle="tab" data-bs-target="#qp-image" type="button" role="tab">Image Search</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="qp-barcode-tab" data-bs-toggle="tab" data-bs-target="#qp-barcode" type="button" role="tab">Barcode Scan</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <!-- Image Search Tab -->
                    <div class="tab-pane fade show active" id="qp-image" role="tabpanel">
                        <div class="mb-3 text-center">
                            <div class="d-flex justify-content-center gap-3">
                                <label for="qpImageUpload" class="form-label border rounded p-3 w-50" style="border: 2px dashed #ccc; cursor: pointer;">
                                    <i class="fa fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <h6 class="text-muted" style="font-size: 13px;">Upload Image</h6>
                                    <input type="file" id="qpImageUpload" accept="image/*" class="d-none">
                                </label>
                                <label for="qpCameraCapture" class="form-label border rounded p-3 w-50" style="border: 2px dashed #ccc; cursor: pointer;">
                                    <i class="fa fa-camera fa-2x text-muted mb-2"></i>
                                    <h6 class="text-muted" style="font-size: 13px;">Take Picture</h6>
                                    <input type="file" id="qpCameraCapture" accept="image/*" capture="environment" class="d-none">
                                </label>
                            </div>
                            <img id="qpImagePreview" src="" alt="Preview" class="img-fluid mt-3 rounded d-none" style="max-height: 200px; margin: 0 auto;">
                        </div>
                        <button type="button" id="qpSearchImageBtn" class="btn btn-primary w-100" disabled>Search by Image</button>
                    </div>
                    <!-- Barcode Scan Tab -->
                    <div class="tab-pane fade" id="qp-barcode" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label">Scan via Handheld Scanner</label>
                            <input type="text" id="qpBarcodeScannerInput" class="form-control" placeholder="Scan or Type Barcode..." autocomplete="off">
                        </div>
                        <div class="text-center my-3"><strong>OR</strong></div>
                        <div class="text-center">
                            <label class="form-label">Scan via Camera</label>
                            <div id="qpBarcodeReader" style="width: 100%; border: 1px solid #ccc; min-height: 200px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const qpModal = document.getElementById('quickProductModal');
        const qpImageUpload = document.getElementById('qpImageUpload');
        const qpCameraCapture = document.getElementById('qpCameraCapture');
        const qpImagePreview = document.getElementById('qpImagePreview');
        const qpSearchImageBtn = document.getElementById('qpSearchImageBtn');
        const qpBarcodeScannerInput = document.getElementById('qpBarcodeScannerInput');
        
        let html5QrcodeScanner = null;
        let selectedImageFile = null;

        if (!qpModal) return;

        function handleImageSelection(e) {
            const file = e.target.files[0];
            if (file) {
                selectedImageFile = file;
                const reader = new FileReader();
                reader.onload = function(e) {
                    qpImagePreview.src = e.target.result;
                    qpImagePreview.classList.remove('d-none');
                    qpSearchImageBtn.disabled = false;
                }
                reader.readAsDataURL(file);
            }
        }

        if(qpImageUpload) qpImageUpload.addEventListener('change', handleImageSelection);
        if(qpCameraCapture) qpCameraCapture.addEventListener('change', handleImageSelection);

        qpSearchImageBtn.addEventListener('click', function() {
            if (!selectedImageFile) return;

            const formData = new FormData();
            formData.append('image', selectedImageFile);
            formData.append('_token', '{{ csrf_token() }}');

            qpSearchImageBtn.disabled = true;
            qpSearchImageBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Searching...';

            fetch('{{ route("product.quick-search.image") }}', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(response => {
                qpSearchImageBtn.disabled = false;
                qpSearchImageBtn.innerHTML = 'Search by Image';

                if (response.status === 200 && response.body.status === 'success') {
                    window.location.href = response.body.redirect_url;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Product Not Found',
                        text: response.body.message || 'No matching product was found.',
                        customClass: { popup: 'swal-mobile-friendly' }
                    });
                }
            })
            .catch(error => {
                qpSearchImageBtn.disabled = false;
                qpSearchImageBtn.innerHTML = 'Search by Image';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while searching.',
                    customClass: { popup: 'swal-mobile-friendly' }
                });
            });
        });

        // Barcode Scanning Functionality
        const lookupBarcode = (barcode) => {
            if (!barcode.trim()) return;

            Swal.fire({
                title: 'Searching...',
                text: 'Looking up barcode: ' + barcode,
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading() }
            });

            const formData = new FormData();
            formData.append('barcode', barcode);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("product.quick-search.barcode") }}', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(response => {
                if (response.status === 200 && response.body.status === 'success') {
                    Swal.close();
                    if(html5QrcodeScanner) {
                        html5QrcodeScanner.clear().catch(e => console.error(e));
                    }
                    window.location.href = response.body.redirect_url;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Product Not Found',
                        text: response.body.message || 'No product found with this barcode.',
                        customClass: { popup: 'swal-mobile-friendly' }
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred during barcode lookup.',
                    customClass: { popup: 'swal-mobile-friendly' }
                });
            });
        };

        // Handheld Scanner Input
        let barcodeInputTimer;
        qpBarcodeScannerInput.addEventListener('keyup', function(e) {
            clearTimeout(barcodeInputTimer);
            if (e.key === 'Enter') {
                lookupBarcode(this.value);
                this.value = '';
            } else {
                barcodeInputTimer = setTimeout(() => {
                    if (this.value.length >= 3) {
                        lookupBarcode(this.value);
                        this.value = '';
                    }
                }, 500);
            }
        });

        // Modal Events
        qpModal.addEventListener('shown.bs.modal', function () {
            if(document.getElementById('qp-barcode-tab').classList.contains('active')) {
                qpBarcodeScannerInput.focus();
                startCameraScanner();
            }
        });

        qpModal.addEventListener('hidden.bs.modal', function () {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear().catch(error => {
                    console.error("Failed to clear html5QrcodeScanner. ", error);
                });
                html5QrcodeScanner = null;
            }
            if(qpImageUpload) qpImageUpload.value = '';
            if(qpCameraCapture) qpCameraCapture.value = '';
            qpImagePreview.src = '';
            qpImagePreview.classList.add('d-none');
            qpSearchImageBtn.disabled = true;
            selectedImageFile = null;
            qpBarcodeScannerInput.value = '';
        });

        document.getElementById('qp-barcode-tab').addEventListener('shown.bs.tab', function () {
            qpBarcodeScannerInput.focus();
            startCameraScanner();
        });

        document.getElementById('qp-image-tab').addEventListener('shown.bs.tab', function () {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear().catch(error => {
                    console.error("Failed to clear html5QrcodeScanner. ", error);
                });
                html5QrcodeScanner = null;
            }
        });

        function startCameraScanner() {
            if (!html5QrcodeScanner) {
                html5QrcodeScanner = new Html5QrcodeScanner("qpBarcodeReader", { fps: 10, qrbox: 250 });
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
            }
            lookupBarcode(decodedText);
        }

        function onScanFailure(error) {
            // handle scan failure silently to keep scanning
        }
    });
</script>
