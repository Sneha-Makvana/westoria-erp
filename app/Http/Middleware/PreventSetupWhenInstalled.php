<?php

namespace App\Http\Middleware;

use App\Services\Setup\InstallerService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventSetupWhenInstalled
{
    public function __construct(private readonly InstallerService $installer) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->installer->isInstalled() && ! $request->boolean('reinstall')) {
            return redirect()->route('auth.signin')
                ->with('success', 'Application is already installed.');
        }

        return $next($request);
    }
}
