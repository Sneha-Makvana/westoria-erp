<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Setup — {{ $defaults['app_name'] }}</title>
    <link rel="stylesheet" href="{{ image_path('admin/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ image_path('admin/assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ image_path('admin/assets/css/style.css') }}">
    <style>
        .setup-wrap { max-width: 720px; margin: 2rem auto; padding: 0 1rem; }
        .step-panel { display: none; }
        .step-panel.active { display: block; }
        .step-nav .badge { width: 28px; height: 28px; line-height: 20px; }
        .req-fail { color: #dc3545; }
        .req-ok { color: #198754; }
    </style>
</head>

<body class="account-page">
    <div class="setup-wrap">
        <div class="login-userheading text-center mb-4">
            <h3>Fablead ERP — Installation Wizard</h3>
            <p class="text-muted">One-time setup for live server or local install</p>
        </div>

        <ul class="nav nav-pills step-nav justify-content-center mb-4 flex-wrap gap-2">
            <li class="nav-item"><span class="badge bg-primary rounded-pill" id="pill-1">1</span> Requirements</li>
            <li class="nav-item"><span class="badge bg-secondary rounded-pill" id="pill-2">2</span> Database</li>
            <li class="nav-item"><span class="badge bg-secondary rounded-pill" id="pill-3">3</span> Application</li>
            <li class="nav-item"><span class="badge bg-secondary rounded-pill" id="pill-4">4</span> License</li>
            <li class="nav-item"><span class="badge bg-secondary rounded-pill" id="pill-5">5</span> Install</li>
        </ul>

        <div id="setupAlert" class="alert d-none" role="alert"></div>

        <form id="setupForm">
            @csrf

            <div class="step-panel active" data-step="1">
                <div class="card p-4">
                    <h5>Server requirements</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="{{ $requirements['php_ok'] ? 'req-ok' : 'req-fail' }}">
                            <i class="fas fa-{{ $requirements['php_ok'] ? 'check' : 'times' }}"></i>
                            PHP {{ $requirements['php_version'] }} (8.1+ required)
                        </li>
                        <li class="{{ $requirements['env_writable'] ? 'req-ok' : 'req-fail' }}">
                            <i class="fas fa-{{ $requirements['env_writable'] ? 'check' : 'times' }}"></i>
                            `.env` file writable
                        </li>
                        @foreach ($requirements['writable'] as $path => $ok)
                            <li class="{{ $ok ? 'req-ok' : 'req-fail' }}">
                                <i class="fas fa-{{ $ok ? 'check' : 'times' }}"></i> {{ $path }} writable
                            </li>
                        @endforeach
                        @foreach ($requirements['extensions'] as $ext => $ok)
                            <li class="{{ $ok ? 'req-ok' : 'req-fail' }}">
                                <i class="fas fa-{{ $ok ? 'check' : 'times' }}"></i> PHP extension: {{ $ext }}
                            </li>
                        @endforeach
                    </ul>
                    @unless ($requirements['passed'])
                        <p class="text-danger mt-3 mb-0">Fix the items above before continuing.</p>
                    @endunless
                </div>
                <div class="text-end mt-3">
                    <button type="button" class="btn btn-primary btn-next" data-next="2"
                        {{ $requirements['passed'] ? '' : 'disabled' }}>Continue</button>
                </div>
            </div>

            <div class="step-panel" data-step="2">
                <div class="card p-4">
                    <h5>Database</h5>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Host</label>
                            <input type="text" name="db_host" class="form-control" value="{{ $defaults['db_host'] }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Port</label>
                            <input type="text" name="db_port" class="form-control" value="{{ $defaults['db_port'] }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Database name</label>
                            <input type="text" name="db_database" class="form-control" value="{{ $defaults['db_database'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="db_username" class="form-control" value="{{ $defaults['db_username'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="db_password" class="form-control">
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mt-3" id="btnTestDb">Test connection</button>
                    <span id="dbTestResult" class="ms-2 small"></span>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-secondary btn-prev" data-prev="1">Back</button>
                    <button type="button" class="btn btn-primary btn-next" data-next="3">Continue</button>
                </div>
            </div>

            <div class="step-panel" data-step="3">
                <div class="card p-4">
                    <h5>Application URL</h5>
                    <p class="text-muted small">Use your live domain on production (must match the URL buyers use in the browser).</p>
                    <div class="mb-3">
                        <label class="form-label">Application name</label>
                        <input type="text" name="app_name" class="form-control" value="{{ $defaults['app_name'] }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Application URL</label>
                        <input type="url" name="app_url" class="form-control" value="{{ $defaults['app_url'] }}" required>
                    </div>
                    <input type="hidden" name="app_env" value="production">
                    <input type="hidden" name="app_debug" value="false">
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-secondary btn-prev" data-prev="2">Back</button>
                    <button type="button" class="btn btn-primary btn-next" data-next="4">Continue</button>
                </div>
            </div>

            <div class="step-panel" data-step="4">
                <div class="card p-4">
                    <h5>License activation</h5>
                    <p class="text-muted small">Enter your CodeCanyon purchase code and Envato username. Your author provides the license server URL.</p>
                    <input type="hidden" name="license_enabled" value="1">
                    <input type="hidden" name="license_product_code" value="{{ $defaults['license_product_code'] }}">
                    <div class="mb-3">
                        <label class="form-label">License server URL</label>
                        <input type="url" name="license_server_url" class="form-control"
                            value="{{ $defaults['license_server_url'] }}"
                            placeholder="https://license.yourdomain.com" required>
                        <small class="text-muted">Provided by the seller — not your ERP domain.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">License mode</label>
                        <select name="license_mode" class="form-control" id="licenseMode">
                            <option value="remote" {{ $defaults['license_mode'] === 'remote' ? 'selected' : '' }}>Online (remote)</option>
                            <option value="offline" {{ $defaults['license_mode'] === 'offline' ? 'selected' : '' }}>Offline (license key)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Purchase code</label>
                        <input type="text" name="purchase_code" class="form-control" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Envato username</label>
                        <input type="text" name="buyer" class="form-control" placeholder="Your CodeCanyon username">
                    </div>
                    <div class="mb-3 d-none" id="offlineKeyWrap">
                        <label class="form-label">License key</label>
                        <textarea name="license_key" class="form-control" rows="3" placeholder="Paste offline license key"></textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-secondary btn-prev" data-prev="3">Back</button>
                    <button type="button" class="btn btn-primary btn-next" data-next="5">Continue</button>
                </div>
            </div>

            <div class="step-panel" data-step="5">
                <div class="card p-4 text-center">
                    <h5>Ready to install</h5>
                    <p class="text-muted">This will configure `.env`, run migrations, set up Passport, and activate your license.</p>
                    <p class="small text-muted">Default admin after install: <strong>admin@gmail.com</strong> / <strong>12345678</strong></p>
                    <button type="submit" class="btn btn-login btn-lg" id="btnInstall">
                        <i class="fas fa-download me-2"></i> Install now
                    </button>
                </div>
                <div class="text-start mt-3">
                    <button type="button" class="btn btn-secondary btn-prev" data-prev="4">Back</button>
                </div>
            </div>
        </form>
    </div>

    <script src="{{ image_path('admin/assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ image_path('admin/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        let currentStep = 1;

        function showStep(n) {
            currentStep = n;
            $('.step-panel').removeClass('active');
            $('.step-panel[data-step="' + n + '"]').addClass('active');
            for (let i = 1; i <= 5; i++) {
                $('#pill-' + i).removeClass('bg-primary bg-secondary').addClass(i <= n ? 'bg-primary' : 'bg-secondary');
            }
        }

        $('.btn-next').on('click', function () { showStep(parseInt($(this).data('next'), 10)); });
        $('.btn-prev').on('click', function () { showStep(parseInt($(this).data('prev'), 10)); });

        $('#licenseMode').on('change', function () {
            $('#offlineKeyWrap').toggleClass('d-none', $(this).val() !== 'offline');
        }).trigger('change');

        $('#btnTestDb').on('click', function () {
            const $btn = $(this).prop('disabled', true);
            $('#dbTestResult').text('Testing...').removeClass('text-danger text-success');
            $.post('{{ route('setup.test-database') }}', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                db_host: $('[name=db_host]').val(),
                db_port: $('[name=db_port]').val(),
                db_database: $('[name=db_database]').val(),
                db_username: $('[name=db_username]').val(),
                db_password: $('[name=db_password]').val(),
            }).done(function (r) {
                $('#dbTestResult').text(r.message).addClass('text-success');
            }).fail(function (xhr) {
                $('#dbTestResult').text(xhr.responseJSON?.message || 'Failed').addClass('text-danger');
            }).always(function () { $btn.prop('disabled', false); });
        });

        $('#setupForm').on('submit', function (e) {
            e.preventDefault();
            const $btn = $('#btnInstall').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Installing...');
            $('#setupAlert').addClass('d-none');
            $.ajax({
                url: '{{ route('setup.install') }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function (r) {
                    $('#setupAlert').removeClass('d-none alert-danger').addClass('alert-success').text(r.message);
                    setTimeout(function () { window.location.href = r.redirect; }, 1500);
                },
                error: function (xhr) {
                    $('#setupAlert').removeClass('d-none alert-success').addClass('alert-danger')
                        .text(xhr.responseJSON?.message || 'Installation failed.');
                    $btn.prop('disabled', false).html('<i class="fas fa-download me-2"></i> Install now');
                }
            });
        });
    </script>
</body>

</html>
