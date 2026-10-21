<?php

namespace App\Http\Controllers;

use App\Services\Setup\InstallerService;
use Illuminate\Http\Request;
use RuntimeException;

class SetupController extends Controller
{
    public function __construct(private readonly InstallerService $installer) {}

    public function index()
    {
        $requirements = $this->installer->requirements();

        return view('setup.index', [
            'requirements' => $requirements,
            'defaults' => [
                'app_url' => rtrim(url('/'), '/'),
                'app_name' => config('app.name', 'Fablead ERP'),
                'db_host' => env('DB_HOST', '127.0.0.1'),
                'db_port' => env('DB_PORT', '3306'),
                'db_database' => env('DB_DATABASE', ''),
                'db_username' => env('DB_USERNAME', 'root'),
                'license_server_url' => env('LICENSE_SERVER_URL', ''),
                'license_mode' => env('LICENSE_MODE', 'remote'),
                'license_product_code' => env('LICENSE_PRODUCT_CODE', 'FABLEAD_ERP'),
            ],
        ]);
    }

    public function testDatabase(Request $request)
    {
        $validated = $request->validate([
            'db_host' => ['required', 'string'],
            'db_port' => ['nullable', 'string'],
            'db_database' => ['required', 'string'],
            'db_username' => ['required', 'string'],
            'db_password' => ['nullable', 'string'],
        ]);

        try {
            $this->installer->testDatabase([
                'host' => $validated['db_host'],
                'port' => $validated['db_port'] ?? '3306',
                'database' => $validated['db_database'],
                'username' => $validated['db_username'],
                'password' => $validated['db_password'] ?? '',
            ]);

            return response()->json(['success' => true, 'message' => 'Database connection successful.']);
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function install(Request $request)
    {
        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:120'],
            'app_url' => ['required', 'url'],
            'app_env' => ['nullable', 'in:local,production,staging'],
            'app_debug' => ['nullable', 'in:0,1,true,false'],
            'db_host' => ['required', 'string'],
            'db_port' => ['nullable', 'string'],
            'db_database' => ['required', 'string'],
            'db_username' => ['required', 'string'],
            'db_password' => ['nullable', 'string'],
            'license_enabled' => ['nullable', 'in:0,1,true,false'],
            'license_mode' => ['nullable', 'in:remote,offline'],
            'license_server_url' => ['nullable', 'string', 'max:255'],
            'purchase_code' => ['required_if:license_enabled,1,true', 'nullable', 'string', 'max:64'],
            'buyer' => ['required_if:license_enabled,1,true', 'nullable', 'string', 'max:120'],
            'license_key' => ['nullable', 'string'],
        ]);

        try {
            $this->installer->install($validated);

            return response()->json([
                'success' => true,
                'message' => 'Installation completed successfully.',
                'redirect' => route('auth.signin'),
            ]);
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Installation failed: '.$e->getMessage(),
            ], 500);
        }
    }
}
