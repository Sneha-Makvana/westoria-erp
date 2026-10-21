<?php

namespace App\Services\Setup;

use App\Services\License\DomainNormalizer;
use App\Services\License\LicenseManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PDO;
use RuntimeException;

class InstallerService
{
    public function __construct(
        private readonly EnvWriter $env
    ) {}

    public function isInstalled(): bool
    {
        return File::exists(config('install.installed_file'));
    }

    public function markInstalled(): void
    {
        $dir = dirname(config('install.installed_file'));
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put(config('install.installed_file'), json_encode([
            'installed_at' => now()->toIso8601String(),
            'url' => config('app.url'),
        ], JSON_PRETTY_PRINT));
    }

    /**
     * @return array<string, mixed>
     */
    public function requirements(): array
    {
        $writable = [
            'storage' => is_writable(storage_path()),
            'storage/app' => is_writable(storage_path('app')),
            'storage/logs' => is_writable(storage_path('logs')),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
        ];

        $extensions = [
            'pdo' => extension_loaded('pdo'),
            'pdo_mysql' => extension_loaded('pdo_mysql'),
            'openssl' => extension_loaded('openssl'),
            'mbstring' => extension_loaded('mbstring'),
            'tokenizer' => extension_loaded('tokenizer'),
            'json' => extension_loaded('json'),
            'curl' => extension_loaded('curl'),
        ];

        $phpOk = version_compare(PHP_VERSION, '8.1.0', '>=');
        $envOk = $this->env->exists()
            ? is_writable($this->env->path())
            : (is_writable(base_path()) && is_readable(base_path('.env.example')));

        return [
            'php_version' => PHP_VERSION,
            'php_ok' => $phpOk,
            'env_writable' => $envOk,
            'writable' => $writable,
            'extensions' => $extensions,
            'passed' => $phpOk && $envOk && ! in_array(false, $writable, true) && ! in_array(false, $extensions, true),
        ];
    }

    /**
     * @param  array<string, string>  $config
     */
    public function testDatabase(array $config): void
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['port'] ?: '3306',
            $config['database']
        );

        try {
            new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (\Throwable $e) {
            throw new RuntimeException('Database connection failed: '.$e->getMessage());
        }
    }

    /**
     * @param  array<string, string>  $data
     * @return array<string, string>
     */
    public function normalizeAppUrl(array $data): array
    {
        $url = rtrim(trim($data['app_url'] ?? ''), '/');
        if ($url === '') {
            throw new RuntimeException('Application URL is required.');
        }

        $data['app_url'] = $url;
        $data['image_path'] = rtrim($data['image_path'] ?? $url, '/').'/';

        return $data;
    }

    /**
     * Full installation (database, passport, license, lock file).
     *
     * @param  array<string, string>  $data
     */
    public function install(array $data): void
    {
        if ($this->isInstalled()) {
            throw new RuntimeException('Application is already installed.');
        }

        $data = $this->normalizeAppUrl($data);
        $this->testDatabase([
            'host' => $data['db_host'],
            'port' => $data['db_port'],
            'database' => $data['db_database'],
            'username' => $data['db_username'],
            'password' => $data['db_password'] ?? '',
        ]);

        $licenseServer = rtrim($data['license_server_url'] ?? '', '/');

        $this->env->write([
            'APP_NAME' => $data['app_name'] ?? 'Fablead ERP',
            'APP_ENV' => $data['app_env'] ?? 'production',
            'APP_DEBUG' => ($data['app_debug'] ?? 'false') === 'true' ? 'true' : 'false',
            'APP_URL' => $data['app_url'],
            'ImagePath' => $data['image_path'],
            'DB_HOST' => $data['db_host'],
            'DB_PORT' => $data['db_port'] ?? '3306',
            'DB_DATABASE' => $data['db_database'],
            'DB_USERNAME' => $data['db_username'],
            'DB_PASSWORD' => $data['db_password'] ?? '',
            'LICENSE_MODE' => $data['license_mode'] ?? 'remote',
            'LICENSE_SERVER_URL' => $licenseServer,
            'LICENSE_PRODUCT_CODE' => $data['license_product_code'] ?? 'FABLEAD_ERP',
            'INSTALLED' => 'true',
        ]);

        $this->reloadEnv($data);

        if (! config('app.key')) {
            Artisan::call('key:generate', ['--force' => true]);
            $this->reloadEnv($data);
        }

        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]);
        Artisan::call('passport:install', ['--force' => true]);

        if (! File::exists(public_path('storage'))) {
            try {
                Artisan::call('storage:link');
            } catch (\Throwable) {
                // Non-fatal on some hosts.
            }
        }

        $this->ensureStorageWebAccessible();

        if ($licenseEnabled) {
            config([
                'license.mode' => $data['license_mode'] ?? 'remote',
                'license.server_url' => rtrim($data['license_server_url'] ?? '', '/'),
                'license.enabled' => true,
            ]);
            $this->activateLicense($data);
        }

        $this->markInstalled();

        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
    }

    /**
     * Uploaded files are served via public/storage → storage/app/public.
     * storage/app must be traversable (755) or the web server cannot follow the symlink.
     */
    public function ensureStorageWebAccessible(): void
    {
        foreach ([storage_path('app'), storage_path('app/public')] as $dir) {
            if (File::isDirectory($dir)) {
                @chmod($dir, 0755);
            }
        }
    }

    /**
     * @param  array<string, string>  $data
     */
    private function activateLicense(array $data): void
    {
        $purchase = trim($data['purchase_code'] ?? '');
        $buyer = trim($data['buyer'] ?? '');

        if ($purchase === '' || $buyer === '') {
            throw new RuntimeException('Purchase code and Envato username are required.');
        }

        /** @var LicenseManager $licenses */
        $licenses = app(LicenseManager::class);

        if (config('license.mode') === 'offline') {
            $key = trim($data['license_key'] ?? '');
            if ($key === '') {
                throw new RuntimeException('License key is required in offline mode.');
            }
            $licenses->activateOffline($key);
        } else {
            $domain = DomainNormalizer::normalize(
                parse_url($data['app_url'], PHP_URL_HOST) ?: DomainNormalizer::fromRequest()
            );
            $licenses->activateRemote($purchase, $buyer, $domain);
        }
    }

    /**
     * @param  array<string, string>  $data
     */
    private function reloadEnv(array $data): void
    {
        Artisan::call('config:clear');

        putenv('APP_URL='.$data['app_url']);
        $_ENV['APP_URL'] = $data['app_url'];
        $_ENV['ImagePath'] = $data['image_path'];
        putenv('ImagePath='.$data['image_path']);
        putenv('LICENSE_SERVER_URL='.$data['license_server_url'] ?? '');
        putenv('LICENSE_MODE='.$data['license_mode'] ?? 'remote');

        config([
            'app.url' => $data['app_url'],
            'app.image_path' => $data['image_path'],
            'database.connections.mysql.host' => $data['db_host'],
            'database.connections.mysql.port' => $data['db_port'] ?? '3306',
            'database.connections.mysql.database' => $data['db_database'],
            'database.connections.mysql.username' => $data['db_username'],
            'database.connections.mysql.password' => $data['db_password'] ?? '',
        ]);

        DB::purge('mysql');
    }
}
