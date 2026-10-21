<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="Fablead Inventory-Billing Software">
    <meta name="keywords"
        content="inventory management, billing system, invoice generator, purchase orders, inventory control, POS system, admin dashboard">
    <meta name="author" content="Fablead Developers Technolab">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ image_path('admin/assets/img/favicon-192x192.webp') }}">

    <link rel="stylesheet" href="{{ image_path('admin/assets/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ image_path('admin/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ image_path('admin/assets/plugins/fontawesome/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ image_path('admin/assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"/>
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
    </style>
    <style>
        :root {
            --auth-primary: #F7933A;
            --auth-primary-dark: #ea7417;
            --auth-secondary: #1F2937;
            --auth-muted: #667085;
            --auth-border: #d9dee8;
            --auth-bg: #f7f8fb;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body.account-page {
            height: 100%;
        }

        body.account-page {
            position: relative;
            display: flex;
            flex-direction: column;
            margin: 0;
            height: 100vh;
            height: 100dvh;
            padding: clamp(10px, 2.4vh, 24px) 20px clamp(8px, 1.7vh, 16px);
            overflow: hidden;
            color: var(--auth-secondary);
            font-family: Inter, "Segoe UI", Roboto, Arial, sans-serif;
            background:
                radial-gradient(circle at 0% 68%, rgba(247, 147, 58, .10) 0 0, transparent 168px),
                radial-gradient(circle at 100% 78%, rgba(247, 147, 58, .12) 0 0, transparent 198px),
                linear-gradient(135deg, #ffffff 0%, #f6f8fb 46%, #ffffff 100%);
        }

        body.account-page::before,
        body.account-page::after {
            content: "";
            position: fixed;
            pointer-events: none;
            z-index: 0;
            opacity: .24;
        }

        body.account-page::before {
            top: -88px;
            left: -82px;
            width: 360px;
            height: 255px;
            border-radius: 0 0 100% 0;
            background: repeating-radial-gradient(ellipse at 0% 0%, transparent 0 13px, rgba(247, 147, 58, .55) 14px 15px, transparent 16px 24px);
        }

        body.account-page::after {
            right: -92px;
            bottom: -78px;
            width: 380px;
            height: 260px;
            border-radius: 100% 0 0 0;
            background: repeating-radial-gradient(ellipse at 100% 100%, transparent 0 13px, rgba(247, 147, 58, .52) 14px 15px, transparent 16px 24px);
        }

        .main-wrapper {
            position: relative;
            z-index: 1;
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .account-content,
        .login-wrapper {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .login-wrapper .login-content {
            width: min(100%, 640px);
        }

        .login-userset {
            position: relative;
            width: 100%;
            max-height: calc(100vh - 58px);
            max-height: calc(100dvh - 58px);
            padding: clamp(18px, 3vh, 25px) clamp(28px, 7vw, 50px);
            border: 0;
            border-radius: 22px;
            background: rgba(255, 255, 255, .92);
            /* box-shadow: 0 24px 60px rgba(31, 41, 55, .13); */
            backdrop-filter: blur(16px);
        }

        .login-userset::before {
            content: "";
            position: absolute;
            top: -1px;
            left: 50%;
            width: 92px;
            height: 4px;
            border-radius: 0 0 999px 999px;
            transform: translateX(-50%);
            background: var(--auth-primary);
        }

        .login-logo {
            display: flex;
            justify-content: center;
            margin: 0 0 clamp(10px, 1.9vh, 18px);
            text-align: center;
        }

        .login-logo .logo-img {
            display: block;
            width: 190px;
            max-width: 70%;
            max-height: 90px;
            object-fit: contain;
            margin: 0 auto !important;
        }

        .login-userheading {
            margin-bottom: clamp(16px, 2.8vh, 24px);
            text-align: center;
        }

        .login-userheading h3 {
            margin: 0 0 clamp(5px, 1vh, 8px);
            color: var(--auth-secondary);
            font-size: clamp(24px, 3.8vh, 30px);
            line-height: 1.18;
            font-weight: 800;
            letter-spacing: 0;
        }

        .login-userheading h3 span {
            color: var(--auth-primary);
        }

        .login-userheading h4 {
            margin: 0;
            color: var(--auth-muted);
            font-size: clamp(13px, 1.9vh, 15px);
            line-height: 1.55;
            font-weight: 400;
        }

        .form-login {
            margin-bottom: clamp(12px, 2vh, 17px);
        }

        .form-login label {
            display: block;
            margin-bottom: clamp(5px, 1vh, 8px);
            color: #111827;
            font-size: 14px;
            font-weight: 600;
        }

        .form-addons,
        .pass-group {
            position: relative;
            display: flex;
            align-items: center;
            min-height: clamp(42px, 6.3vh, 48px);
            border: 1px solid var(--auth-border);
            border-radius: 9px;
            background: #fff;
            overflow: hidden;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }

        .form-addons:focus-within,
        .pass-group:focus-within {
            border-color: rgba(247, 147, 58, .78);
            box-shadow: 0 0 0 4px rgba(247, 147, 58, .14);
        }

        .form-addons::before,
        .pass-group::before {
            content: "";
            align-self: stretch;
            width: 56px;
            flex: 0 0 56px;
            border-right: 0;
            background: #fff;
        }

        .form-addons::after,
        .pass-group::after {
            position: absolute;
            left: 19px;
            z-index: 2;
            color: var(--auth-primary);
            font-family: "Font Awesome 5 Free";
            font-size: 16px;
            font-weight: 900;
            line-height: 1;
        }

        .form-addons::after {
            content: "\f0e0";
        }

        .pass-group::after {
            content: "\f023";
        }

        .form-addons input,
        .pass-group input {
            width: 100%;
            height: clamp(42px, 6.3vh, 48px);
            min-width: 0;
            padding: 0 18px 0 8px;
            border: 0;
            outline: 0;
            color: var(--auth-secondary);
            font-size: 15px;
            background: transparent;
        }

        .form-addons input::placeholder,
        .pass-group input::placeholder {
            color: #7a8597;
        }

        .form-addons img {
            display: none;
        }

        .pass-group .toggle-password {
            position: static;
            flex: 0 0 48px;
            color: #8a94a6;
            cursor: pointer;
            text-align: center;
            transition: color .2s ease;
        }

        .pass-group .toggle-password:hover {
            color: var(--auth-primary);
        }

        .signin-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-top: -1px;
            margin-bottom: clamp(14px, 2.5vh, 21px);
        }

        .remember-login {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin: 0;
            color: var(--auth-secondary);
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            cursor: pointer;
        }

        .remember-login input {
            width: 16px;
            height: 16px;
            margin: 0;
            accent-color: var(--auth-primary);
            cursor: pointer;
        }

        .forgot-link {
            color: var(--auth-primary-dark);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            transition: color .2s ease;
        }

        .forgot-link:hover,
        .forgot-link:focus {
            color: var(--auth-primary);
            text-decoration: none;
        }

        .btn-login {
            width: 100%;
            min-height: clamp(42px, 6.5vh, 48px);
            border: 0;
            border-radius: 30px;
            color: #fff;
            background: linear-gradient(135deg, #ff9b45 0%, var(--auth-primary) 54%, #f47d23 100%);
            box-shadow: 0 13px 26px rgba(247, 147, 58, .30);
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0;
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
        }

        .btn-login:hover,
        .btn-login:focus {
            color: #fff;
            transform: translateY(-1px);
            filter: saturate(1.04);
            box-shadow: 0 16px 30px rgba(247, 147, 58, .36);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .signin-arrow {
            margin-left: 12px;
            font-weight: 500;
        }

        .login-divider {
            display: flex;
            align-items: center;
            gap: 24px;
            margin: clamp(15px, 2.5vh, 22px) 0 clamp(10px, 1.8vh, 14px);
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
        }

        .login-divider::before,
        .login-divider::after {
            content: "";
            height: 1px;
            flex: 1;
            background: #e2e7ef;
        }

        #faceLoginBtn {
            width: 100% !important;
            min-height: clamp(42px, 6.3vh, 48px);
            border: 1px solid #e2e7ef !important;
            border-radius: 999px !important;
            background: #fff !important;
            color: var(--auth-secondary) !important;
            box-shadow: 0 8px 22px rgba(31, 41, 55, .07);
            font-size: 14px !important;
            font-weight: 700 !important;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease, color .2s ease;
        }

        #faceLoginBtn i {
            color: var(--auth-primary);
            font-size: 15px !important;
        }

        #faceLoginBtn:hover,
        #faceLoginBtn:focus {
            border-color: rgba(247, 147, 58, .55) !important;
            color: var(--auth-primary-dark) !important;
            transform: translateY(-1px);
            box-shadow: 0 12px 26px rgba(31, 41, 55, .10);
        }

        #loginMessage,
        #faceLoginMessage {
            text-align: center;
            font-size: 13px;
            font-weight: 500;
        }

        .login-footer {
            position: relative;
            z-index: 1;
            flex: 0 0 auto;
            padding-top: clamp(6px, 1.4vh, 12px);
            text-align: center;
            color: #667085;
        }

        .login-footer h1 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
        }

        .login-footer a {
            color: inherit;
            text-decoration: none;
        }

        .login-footer h1:not(.login-footer-title) {
            display: none;
        }


        .login-wrapper .login-content .form-login input {
            border:none !important;
        }
        @media screen and (max-width: 768px) {
            body.account-page {
                padding: 12px 14px 10px;
            }

            .main-wrapper {
                min-height: 0;
            }

            .login-wrapper .login-content {
                width: 100% !important;
            }

            .login-userset {
                padding: 18px 22px;
                border-radius: 20px;
            }

            .login-logo .logo-img {
                width: 150px;
                max-width: 68%;
            }

            .login-userheading h3 {
                font-size: 28px;
            }

            .signin-options {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px;
            }
        }

        @media screen and (max-width: 420px) {
            .login-userset {
                padding: 16px 16px;
            }

            .login-userheading h3 {
                font-size: 26px;
            }

            .login-userheading h4 {
                font-size: 14px;
            }
        }

        @media screen and (max-height: 680px) {
            body.account-page {
                padding-top: 8px;
                padding-bottom: 6px;
            }

            .login-userset {
                padding-top: 16px;
                padding-bottom: 16px;
                border-radius: 18px;
            }

            .login-logo {
                margin-bottom: 8px;
            }

            .login-logo .logo-img {
                width: 140px;
                max-width: 75%;
                max-height: 75px;
            }

            .login-userheading {
                margin-bottom: 12px;
            }

            .login-userheading h3 {
                font-size: 23px;
                margin-bottom: 4px;
            }

            .login-userheading h4 {
                font-size: 12px;
            }

            .form-login {
                margin-bottom: 10px;
            }

            .form-addons,
            .pass-group {
                min-height: 40px;
            }

            .form-addons input,
            .pass-group input {
                height: 40px;
            }

            .signin-options {
                margin-bottom: 12px;
            }

            .btn-login,
            #faceLoginBtn {
                min-height: 40px;
            }

            .login-divider {
                margin: 12px 0 8px;
            }

            .login-footer h1 {
                font-size: 12px;
            }
        }
    </style>
</head>
@php
    use App\Models\Setting;
    $settings = Setting::first();
@endphp

<body class="account-page">

    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper">
                <div class="login-content">
                    <div class="login-userset">
                        <div class="login-logo">
                            <!-- <img src="{{ $settings && $settings->logo ? image_path('storage/' . $settings->logo) : 'https://fableadtechnolabs.com/static/media/250x150%20(1).b3f5a4db48c7770366ef.webp' }}"
                                alt="img" class="logo-img"> -->
                                <img src="{{ env('ImagePath') . 'admin/assets/img/Fablead_ERP_2048x1024.png' }}"
                                    alt="img" class="logo-img">
                        </div>
                        <div class="login-userheading">
                            <h3>Welcome <span>Back</span></h3>
                            <h4>Please sign in to your account to continue</h4>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success mb-3">{{ session('success') }}</div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                        @endif

                        <form id="loginForm">
                            <div class="form-login">
                                <label>Email</label>
                                <div class="form-addons">
                                    <input type="text" id="email" placeholder="Enter your email address">
                                    <img src="{{ image_path('admin/assets/img/icons/mail.svg') }}" alt="img">
                                </div>
                                <small id="emailError" class="text-danger"></small>
                            </div>

                            <div class="form-login">
                                <label>Password</label>
                                <div class="pass-group">
                                    <input type="password" id="password" class="pass-input"
                                        placeholder="Enter your password">
                                    <span class="fas toggle-password fa-eye-slash"></span>
                                </div>
                                <small id="passwordError" class="text-danger"></small>
                            </div>

                            <div class="signin-options">
                                <label class="remember-login">
                                    <input type="checkbox" id="remember">
                                    <span>Remember me</span>
                                </label>
                                <!-- <div class="alreadyuser">
                                    <a href="{{-- route('auth.forgetpassword') --}}" class="hover-a forgot-link">Forgot password?</a>
                                </div> -->
                            </div>

                            <div class="form-login">
                                <button type="submit" class="btn btn-login">Sign In <i class="fa fa-arrow-right-to-bracket"></i></button>
                            </div>

                            <div id="loginMessage" class="text-danger"></div>

                            {{-- ── Face Login ─────────────────────────────────────── --}}
                            {{-- <div class="form-login mt-2">
                                <div class="login-divider">
                                    <span>or continue with</span>
                                </div>
                                <button type="button" id="faceLoginBtn"
                                    style="width:100%;padding:13px 20px;border-radius:10px;border:2px solid #ff9f43;
                                           background:#fff;color:#f97316;font-weight:700;font-size:14px;
                                           cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;
                                           transition:all .2s ease;">
                                    <i class="fas fa-camera" style="font-size:15px;"></i> Login with Face
                                </button>
                                <div id="faceLoginMessage" class="text-danger mt-2" style="font-size:13px;"></div>
                            </div> --}}
                        </form>

                    </div>
                </div>
                <!-- <div class="login-img">
                    <img src="admin/assets/img/login.jpg" alt="img">
                </div> -->
            </div>
        </div>
    </div>
    <footer class="login-footer">
        <h1 class="login-footer-title">&copy; <?= date('Y') ?> <a href="https://www.fableadtech.com/" target="_blank">Copyright - Fablead Developers Technolab</a></h1>
        <h1 style="font-size: 14px; font-weight: 600; margin: 0;">© <?= date('Y') ?> <a href="https://www.fableadtech.com/" target="_blank" style="color: inherit; text-decoration: none;">Copyright - Fablead Developers Technolab</a></h1>
    </footer>

    <!-- face-api.js from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <script src="{{ image_path('admin/assets/js/jquery-3.6.0.min.js') }}"></script>

    <script src="{{ image_path('admin/assets/js/feather.min.js') }}"></script>

    <script src="{{ image_path('admin/assets/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ image_path('admin/assets/js/script.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- ── Face Recognition Modal & Script Partials ─────── --}}
    @include('partials.face-recognition-modal')
    @include('partials.face-recognition-script')

    <script>
        $(document).ready(function () {
            /* ── Normal email/password login ─────────────────────────── */
            $("#loginForm").submit(function (e) {
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");

        e.preventDefault();

        let email = $("#email").val().trim();
        let password = $("#password").val().trim();
        $("#emailError, #passwordError, #loginMessage").text("");

        if (!email) {
            $("#emailError").text("Email is required.");
            return;
        }
        if (!password) {
            $("#passwordError").text("Password is required.");
            return;
        }

        let $submitBtn = $("#loginForm button[type='submit']");
        let originalText = $submitBtn.html();
        $submitBtn.html('<span class="spinner-border spinner-border-sm"></span> Logging in...').prop("disabled", true);

        function performLogin(forceLogin = false) {
            $.ajax({
                url: "/",
                type: "POST",
                dataType: "json",
                data: {
                    email: email,
                    selectedSubAdminId: selectedSubAdminId,
                    password: password,
                    force: forceLogin ? 1 : 0,
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function (response) {
                    if (response.status && response.token) {
                        var selectedId;
                        if(response.user.branch_id != null){
                            selectedId = response.user.branch_id;
                        } else {
                            selectedId = response.user.id;
                        }

                        if (selectedId) {
                            localStorage.setItem('selectedSubAdminId', selectedId);
                            var storedSubAdminId = localStorage.getItem('selectedSubAdminId');

                            $.post('/set-subadmin-session', {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                subAdminId: selectedId
                            }, function() {});

                        } else if (selectedText === "Main Branch") {
                            $.post('/clear-subadmin-session', {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            }, function() {
                                localStorage.removeItem('selectedSubAdminId');
                            });
                        }
                        localStorage.setItem("authToken", response.token);
                        localStorage.setItem("token", response.token);
                        localStorage.setItem("selectedSubAdminId", selectedId);
                        window.location.href = response.redirect;
                    } else if (response.error) {
                        $("#loginMessage").text(response.error).css("color", "red");
                    }
                },
                error: function (xhr) {
                    let res = xhr.responseJSON;
                    let msg = res?.error || res?.message || "Login failed.";
                    if (res?.license_required) {
                        msg += ' Go to <a href="{{ url('/license') }}">/license</a> to activate.';
                    }
                    if (msg && msg.toLowerCase().includes('undefined variable')) {
                        msg = "Login failed. Please try again.";
                    }

                    if (res?.is_expired || (msg && msg.toLowerCase().includes('expired'))) {
                        showExpiredPlanModal(msg, res?.plan_name, res?.end_date);
                    } else {
                        $("#loginMessage").html(msg).css("color", "red");
                    }
                },
                complete: function () {
                    $submitBtn.html(originalText).prop("disabled", false);
                }
            });
        }

        performLogin(false);
    });

            /* ── Face Login ──────────────────────────────────────────── */
            const $faceBtn = $('#faceLoginBtn');
            const $faceMsgBox = $('#faceLoginMessage');

            $faceBtn.on('click', function () {
                $faceMsgBox.text('');
                let fr;
                try {
                    fr = window.OmsaiFaceRecognition.init();
                } catch (err) {
                    $faceMsgBox.text('Face recognition UI failed to initialise. Please refresh.');
                    return;
                }

                fr.open({
                    title:        'Login with Face',
                    subtitle:     'Look straight at the camera. We will log you in automatically.',
                    autoDetect:   true,
                    onCapture: async function (descriptor, modal) {
                        modal.setStatus('⏳ Verifying face...', 'info');

                        try {
                            const response = await $.ajax({
                                url:  '{{ route("auth.face-login") }}',
                                type: 'POST',
                                contentType: 'application/json',
                                data: JSON.stringify({
                                    face_descriptor: descriptor,
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                }),
                                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            });

                            if (response.status && response.redirect) {
                                modal.setStatus('✅ Face matched! Redirecting...', 'success');

                                // Store auth data same as password login
                                if (response.token) {
                                    localStorage.setItem('authToken', response.token);
                                    localStorage.setItem('token', response.token);
                                }
                                if (response.user) {
                                    const userId = response.user.branch_id ?? response.user.id;
                                    localStorage.setItem('selectedSubAdminId', userId);
                                    $.post('/set-subadmin-session', {
                                        _token: $('meta[name="csrf-token"]').attr('content'),
                                        subAdminId: userId
                                    }, function() {});
                                }

                                setTimeout(() => {
                                    modal.close();
                                    window.location.href = response.redirect;
                                }, 800);
                            } else {
                                throw new Error(response.error || 'Face login failed.');
                            }
                        } catch (err) {
                            const msg = err.responseJSON?.error || err.responseJSON?.message || err.message || 'Face login failed.';
                            throw new Error(msg);
                        }
                    },
                });
            });

            // Hover effects for face login button
            $faceBtn.on('mouseenter', function () {
                $(this).css({ 'background': 'linear-gradient(135deg,#ff9f43,#f97316)', 'color': '#fff' });
            }).on('mouseleave', function () {
                $(this).css({ 'background': '#fff', 'color': '#f97316' });
            });
        });
    </script>
    <!-- Expired Subscription Plan Modal -->
    <div class="modal fade" id="expiredPlanModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
            <div class="modal-content border-0" style="border-radius: 20px; padding: 28px 24px 24px; position: relative; box-shadow: 0 20px 50px rgba(0,0,0,0.25);">
                
                {{-- Close Button (X) --}}
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 16px; right: 20px; background: none; border: none; font-size: 22px; font-weight: 700; color: #ff5252; cursor: pointer; line-height: 1;">
                    &times;
                </button>

                <div class="modal-body p-0 text-center">
                    {{-- Red Circle Icon with Exclamation --}}
                    <div style="width: 58px; height: 58px; border-radius: 50%; background: #ff5252; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 30px; font-weight: 800; margin: 0 auto 16px; box-shadow: 0 8px 20px rgba(255, 82, 82, 0.35);">
                        !
                    </div>

                    {{-- Modal Header Title --}}
                    <h3 style="font-size: 22px; font-weight: 800; color: #1e293b; margin-bottom: 12px; font-family: 'Outfit', sans-serif;">
                        Subscription Plan Expired
                    </h3>

                    {{-- Message --}}
                    <p id="expiredModalMsg" style="font-size: 14px; color: #64748b; margin-bottom: 20px; line-height: 1.5;">
                        Your subscription plan has expired. Please renew your plan to continue using the system.
                    </p>

                    {{-- Details Box --}}
                    <div style="background: #f8fafc; border-left: 4px solid #ef4444; border-radius: 12px; padding: 14px 18px; margin-bottom: 22px; text-align: left;">
                        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 14px;">
                            <span style="color: #475569; font-weight: 500;">Plan:</span>
                            <strong id="expiredModalPlanName" style="color: #ef4444; font-weight: 700;">Starter</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 14px;">
                            <span style="color: #475569; font-weight: 500;">Expires On:</span>
                            <strong id="expiredModalEndDate" style="color: #ef4444; font-weight: 700;">N/A</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center" style="font-size: 14px;">
                            <span style="color: #475569; font-weight: 500;">Status:</span>
                            <span class="badge bg-danger">Expired</span>
                        </div>
                    </div>

                    {{-- Contact Information --}}
                    <div style="text-align: left; background: #fff5f5; border-radius: 12px; padding: 14px 16px; border: 1px dashed #fca5a5;">
                        <h5 style="color: #f97316; font-size: 15px; font-weight: 700; margin-bottom: 10px;">
                            Contact us to Renew:
                        </h5>
                        <p style="font-size: 14px; color: #334155; margin-bottom: 8px; font-weight: 600;">
                            <i class="fas fa-phone me-2" style="color: #f97316;"></i> +91 9824734531
                        </p>
                        <p style="font-size: 14px; color: #334155; margin-bottom: 0; font-weight: 600;">
                            <i class="fas fa-envelope me-2" style="color: #f97316;"></i> info@fableadtechnolabs.com
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function showExpiredPlanModal(message, planName, endDate) {
            if (message) {
                $("#expiredModalMsg").text(message);
            }
            if (planName) {
                $("#expiredModalPlanName").text(planName);
            }
            if (endDate) {
                $("#expiredModalEndDate").text(endDate);
            }
            var modalEl = document.getElementById('expiredPlanModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                var myModal = new bootstrap.Modal(modalEl, {
                    backdrop: 'static',
                    keyboard: true
                });
                myModal.show();
            }
        }
    </script>

    @if (session('error') && (str_contains(session('error'), 'expired') || str_contains(session('error'), 'plan')))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showExpiredPlanModal(@json(session('error')));
            });
        </script>
    @endif
</body>

</html>
