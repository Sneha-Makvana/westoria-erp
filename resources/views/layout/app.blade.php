@php
    $settings = App\Models\Setting::first();
    $user = auth()->user();
    $homeRoute = $user && $user->role === 'staff' ? 'auth.staff-dashboard' : 'auth.dashboard';
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0"> -->
    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Fablead Inventory-Billing Software">
    <meta name="keywords" content="inventory management, billing system, invoice generator, purchase orders, inventory control, POS system, admin dashboard">
    <meta name="author" content="Fablead Developers Technolab">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Default Title')</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ image_path('admin/assets/img/favicon-192x192.webp') }}">

    <link rel="stylesheet" href="{{ image_path('admin/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ image_path('admin/assets/plugins/owlcarousel/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ image_path('admin/assets/plugins/owlcarousel/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ image_path('admin/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ image_path('admin/assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ image_path('admin/assets/css/bootstrap-datetimepicker.min.css') }}">

    <link rel="stylesheet" href="{{ image_path('admin/assets/css/dataTables.bootstrap4.min.css') }}">

    <link rel="stylesheet" href="{{ image_path('admin/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ image_path('admin/assets/plugins/fontawesome/css/all.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* Globally apply Outfit font family to all primary text elements */
        body, h1, h2, h3, h4, h5, h6, p, a, button, input, select, textarea, label, table, th, td,
        .form-control, .btn, .nav-link, .dropdown-item, .card-title, .dash-label-std, .dash-val-std {
            font-family: 'Outfit', system-ui, -apple-system, sans-serif !important;
        }

        /* Ensure font-weights are applied correctly based on existing styles */
        strong, b {
            font-weight: 700 !important;
        }

        /* Hide number input spinners; prices should not change via scroll */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
            appearance: textfield;
        }

        /* Prevent unwanted horizontal viewport blowout on tablets & iPad */
        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }

        .main-wrapper {
            max-width: 100%;
            min-width: 0;
            overflow-x: hidden;
        }

        .page-wrapper {
            max-width: 100%;
            min-width: 0;
        }
    </style>
    @php
        $styleCssPath = public_path('admin/assets/css/style.css');
        $styleCssVersion = file_exists($styleCssPath) ? filemtime($styleCssPath) : time();
    @endphp
    <link rel="stylesheet" href="{{ image_path('admin/assets/css/style.css') }}?v={{ $styleCssVersion }}">
    <script>
        // Theme toggle is disabled: always light
        // const savedTheme = localStorage.getItem('theme') || 'light';
        const savedTheme = 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
    @stack('css')
</head>

<body style="min-height: 100vh; display: flex; flex-direction: column;">

    <div id="global-loader">
        <div class="whirly-loader"></div>
    </div>
    <script>
        // Hide page loader ASAP — do not wait for footer plugins (charts, flot, etc.)
        (function () {
            var loader = document.getElementById('global-loader');
            if (!loader) return;
            var hidden = false;
            function hideGlobalLoader() {
                if (hidden || !loader) return;
                hidden = true;
                loader.classList.add('is-hidden');
                loader.style.opacity = '0';
                loader.style.pointerEvents = 'none';
                setTimeout(function () {
                    if (loader && loader.parentNode) {
                        loader.style.display = 'none';
                    }
                }, 180);
            }
            window.hideGlobalLoader = hideGlobalLoader;
            // Failsafe only — primary hide runs right after page content (before footer JS)
            setTimeout(hideGlobalLoader, 800);
        })();
    </script>

    <div class="main-wrapper" style="flex: 1;">
        @include('layout.header')
        @include('layout.sidebar')

        <!-- Main Content Section -->
        <div class="page-wrapper">
            @yield('content')
        </div>
    </div>
    <script>
        // Content is in the DOM — hide loader before heavy footer scripts download
        if (typeof window.hideGlobalLoader === 'function') {
            window.hideGlobalLoader();
        }
    </script>

    <!-- Footer with copyright -->
    <footer style="text-align: center; padding: 10px 0; background-color: #f4f4f4; height: 50px;">
        <h1 style="font-size: 14px; font-weight: 600;">© <?= date('Y') ?> <a href="https://www.fableadtech.com/" target="_blank" style="color: inherit; text-decoration: none;">Copyright - Fablead Developers Technolab</a></h1>
    </footer>
    @include('partials.calculator')
    @include('layout.footer')
    <div class="mobile-bottom-nav">
        <a href="{{ route('sales.list') }}" class="nav-item {{ request()->routeIs('sales.list') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="{{ route($homeRoute) }}" class="nav-item {{ request()->routeIs('auth.dashboard') || request()->routeIs('auth.staff-dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('sales.add', ['sale_type' => 'sales']) }}" class="nav-item {{ request()->routeIs('sales.add') ? 'active' : '' }}">
            <i class="fas fa-shopping-cart"></i>
            <span>Order</span>
        </a>
        <a href="{{ route('product.list') }}" class="nav-item {{ request()->routeIs('product.list') ? 'active' : '' }}">
            <i class="fas fa-cubes"></i>
            <span>Products</span>
        </a>
        <a href="{{ route('customer.list') }}" class="nav-item {{ request()->routeIs('customer.list') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Customer</span>
        </a>
    </div>

    @if (!empty($planExpiryNotice['show']))
        <div class="modal fade" id="planExpiryNoticeModal" tabindex="-1" aria-labelledby="planExpiryNoticeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-warning text-dark">
                        <div>
                            <h5 class="modal-title mb-1" id="planExpiryNoticeModalLabel">Plan Expiry Notice</h5>
                            <small class="d-block">Please renew before your access ends</small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert {{ !empty($planExpiryNotice['is_expired']) ? 'alert-danger' : 'alert-warning' }} mb-3">
                            <strong>{{ $planExpiryNotice['message'] }}</strong>
                            <div class="mt-1">
                                {{ !empty($planExpiryNotice['is_expired']) ? 'Your account is expired now. Please renew immediately.' : 'Renew early to avoid service interruption.' }}
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Plan Name</div>
                                    <div class="fw-semibold">{{ $planExpiryNotice['plan_name'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">End Date</div>
                                    <div class="fw-semibold">{{ $planExpiryNotice['end_date'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Status</div>
                                    <div class="fw-semibold">{{ $planExpiryNotice['days_left_text'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Support</div>
                                    <div class="fw-semibold">{{ $planExpiryNotice['contact_name'] ?? 'Support' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="fw-semibold mb-2">Contact Details</div>
                            <div class="d-flex flex-column gap-1">
                                @if (!empty($planExpiryNotice['contact_phone']))
                                    <a href="tel:{{ preg_replace('/\s+/', '', (string) $planExpiryNotice['contact_phone']) }}" class="text-decoration-none">
                                        <i class="fas fa-phone me-1"></i> +91 9824734531 
                                    </a>
                                @endif
                                @if (!empty($planExpiryNotice['contact_email']))
                                    <a href="mailto:{{ $planExpiryNotice['contact_email'] }}" class="text-decoration-none">
                                        <i class="fas fa-envelope me-1"></i>info@fableadtechnolabs.com
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        @if (!empty($planExpiryNotice['contact_phone']))
                            <a href="tel:{{ preg_replace('/\s+/', '', (string) $planExpiryNotice['contact_phone']) }}" class="btn btn-warning text-dark" id="planExpiryContactBtn">
                                Contact Now
                            </a>
                        @endif
                        @if (!empty($planExpiryNotice['contact_email']))
                            <a href="mailto:{{ $planExpiryNotice['contact_email'] }}" class="btn btn-outline-secondary">
                                Email Support
                            </a>
                        @endif
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
 
    @if (!empty($planExpiryNotice['show']))
        <script>
            (function() {
                var notice = @json($planExpiryNotice);
                var storageKey = 'plan-expiry-dismissed-' + (notice.plan_id || 'unknown') + '-' + (notice.end_date || 'unknown');
                var today = notice.today || new Date().toISOString().slice(0, 10);
                if (localStorage.getItem(storageKey) !== today) {
                    window.willShowPlanExpiry = true;
                }
            })();

            document.addEventListener('DOMContentLoaded', function() {
                var modalElement = document.getElementById('planExpiryNoticeModal');
                if (!modalElement || typeof bootstrap === 'undefined') {
                    return;
                }

                if (!window.willShowPlanExpiry) {
                    return;
                }

                var notice = @json($planExpiryNotice);
                var storageKey = 'plan-expiry-dismissed-' + (notice.plan_id || 'unknown') + '-' + (notice.end_date || 'unknown');
                var today = notice.today || new Date().toISOString().slice(0, 10);

                var noticeModal = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: true
                });

                var dismissNotice = function() {
                    localStorage.setItem(storageKey, today);
                };

                modalElement.addEventListener('hidden.bs.modal', dismissNotice, { once: true });

                var contactButton = document.getElementById('planExpiryContactBtn');
                if (contactButton) {
                    contactButton.addEventListener('click', dismissNotice, { once: true });
                }

                noticeModal.show();
            });
        </script>
    @endif

    <script>
        (function() {
            document.addEventListener('wheel', function(e) {
                var active = document.activeElement;
                if (active && active.tagName === 'INPUT' && active.type === 'number') {
                    e.preventDefault();
                }
            }, { passive: false });
        })();
    </script>

    @stack('js')
    @include('layout.partials.caps-lock')
</body>

</html>
