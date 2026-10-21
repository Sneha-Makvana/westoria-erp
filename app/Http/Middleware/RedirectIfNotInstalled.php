<?php

namespace App\Http\Middleware;

use App\Services\Setup\InstallerService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotInstalled
{
    public function __construct(private readonly InstallerService $installer) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->installer->isInstalled()) {
            return $next($request);
        }

        foreach (config('install.except_paths', []) as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Application not installed. Open /setup to continue.',
                'setup_url' => url('/setup'),
            ], 503);
        }

        return redirect()->route('setup.index');
    }
}
