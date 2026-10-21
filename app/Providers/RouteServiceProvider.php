<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $key = $request->user()?->id ?: $request->ip();

            if ($request->is(
                'api/importProducts',
                'api/previewImportProducts',
                'api/importPreviewRows/*',
                'api/importPreviewStatus/*',
                'api/processImportPreview/*',
                'api/sync-products-to-branches'
            )) {
                return Limit::perMinute(10000)->by($key);
            }

            return Limit::perMinute(60)->by($key);
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->group(base_path('routes/license.php'));

            // License server API + admin are not shipped in the buyer ERP build.
            if (config('license.admin_enabled')) {
                require base_path('routes/license-api.php');

                Route::middleware('web')
                    ->group(base_path('routes/license-admin.php'));
            }
        });
    }
}
