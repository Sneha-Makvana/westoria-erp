<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Models\Setting;
use App\Services\StaffPermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Backup when config is already loaded (env() is empty after config:cache).
        if (config('database.connections.mysql.host') === '127.0.0.1' && ! in_array(PHP_OS_FAMILY, ['Windows', 'Darwin'], true)) {
            config(['database.connections.mysql.host' => 'localhost']);
        }

        // After config:cache, env() is empty in providers — use config (filled from .env at cache time).
        $imagePath = config('app.image_path');
        if (! is_string($imagePath) || $imagePath === '') {
            $raw = config('app.url', 'http://localhost');
            $imagePath = rtrim((string) $raw, '/').'/';
            config(['app.image_path' => $imagePath]);
        }

        // Legacy Blade env('ImagePath') — must match config when .env is not read at runtime.
        $_ENV['ImagePath'] = $imagePath;
        $_ENV['IMAGE_PATH'] = $imagePath;
        putenv('ImagePath='.$imagePath);
        putenv('IMAGE_PATH='.$imagePath);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        try {
            if (!Schema::hasColumn('products', 'barcode_details')) {
                Schema::table('products', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->json('barcode_details')->nullable();
                });
            }
            if (!Schema::hasColumn('products', 'selling_prices')) {
                Schema::table('products', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->json('selling_prices')->nullable();
                });
            }
        } catch (\Exception $e) {
            // Silently fail if DB is not ready
        }

        View::share('imagePath', config('app.image_path'));
        // Guard against fresh-database installs where `settings` table doesn't exist yet.
        // Without this, `php artisan migrate` crashes before it can even create the table.
        try {
            $appSetting = Setting::first() ?? new Setting([
                'name'              => 'Fablead Developer & Technolab',
                'email'             => 'info@gmail.com',
                'phone'             => 1234567890,
                'address'           => 'Adajan Surat',
                'logo'              => 'admin/assets/img/logo-image.jpg',
                'currency_symbol'   => '₹',
                'currency_position' => 'left',
            ]);
        } catch (\Exception $e) {
            $appSetting = new Setting([
                'name'              => 'Fablead Developer & Technolab',
                'email'             => 'info@gmail.com',
                'phone'             => 1234567890,
                'address'           => 'Adajan Surat',
                'logo'              => 'admin/assets/img/logo-image.jpg',
                'currency_symbol'   => '₹',
                'currency_position' => 'left',
            ]);
        }
        View::share('appSetting', $appSetting);

        $planExpiryNotice = [
            'show' => false,
            'is_expired' => false,
            'plan_id' => null,
            'plan_name' => null,
            'end_date' => null,
            'days_left' => null,
            'days_left_text' => null,
            'message' => null,
            'contact_name' => $appSetting->name ?? 'Support',
            'contact_email' => $appSetting->email ?? '',
            'contact_phone' => $appSetting->phone ?? '',
            'today' => Carbon::now('Asia/Kolkata')->toDateString(),
        ];

        $user = Auth::user();
        if ($user) {
            $user->loadMissing('plan');
            $plan = $user->plan;

            if ($plan && ! empty($plan->end_date)) {
                $now = Carbon::now('Asia/Kolkata');
                $endDate = Carbon::parse($plan->end_date, 'Asia/Kolkata');
                $endOfDay = $endDate->copy()->endOfDay();
                $daysLeft = (int) $now->copy()->startOfDay()->diffInDays($endDate->copy()->startOfDay(), false);
                $warningDays = 30;
                $isExpired = $now->greaterThan($endOfDay);
                $shouldWarn = $isExpired || $daysLeft <= $warningDays;

                if ($shouldWarn) {
                    if ($isExpired) {
                        $message = 'Your plan has expired.';
                        $daysLeftText = 'Expired';
                    } elseif ($daysLeft === 0) {
                        $message = 'Your plan expires today.';
                        $daysLeftText = 'Today';
                    } elseif ($daysLeft === 1) {
                        $message = 'Your plan expires tomorrow.';
                        $daysLeftText = '1 day left';
                    } else {
                        $message = "Your plan will expire in {$daysLeft} days.";
                        $daysLeftText = "{$daysLeft} days left";
                    }

                    $planExpiryNotice = [
                        'show' => true,
                        'is_expired' => $isExpired,
                        'plan_id' => $plan->id ?? null,
                        'plan_name' => $plan->name ?? null,
                        'end_date' => $endDate->format('d/m/Y'),
                        'days_left' => $daysLeft,
                        'days_left_text' => $daysLeftText,
                        'message' => $message,
                        'contact_name' => $appSetting->name ?? 'Support',
                        'contact_email' => $appSetting->email ?? '',
                        'contact_phone' => $appSetting->phone ?? '',
                        'today' => $now->toDateString(),
                    ];
                }
            }
        }

        View::share('planExpiryNotice', $planExpiryNotice);

        // Do not enable Passport::hashClientSecrets() unless PASSPORT_PERSONAL_ACCESS_CLIENT_ID
        // and PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET are set in .env (see README).

        if (!File::exists(public_path('storage'))) {
            try {
                Artisan::call('storage:link');      
            } catch (\Exception $e) {
                // Optional: Log error or ignore silently
            }
        }
        app()->singleton('hasPermission', function () {
            return static function ($moduleId, $action) {
                return StaffPermission::check((int) $moduleId, (string) $action);
            };
        });

        View::composer('*', function ($view) {
            $user = Auth::user();
            $view->with('staffPermissionFullAccess', StaffPermission::hasFullAccess($user));
            $view->with('staffPermissionMap', StaffPermission::allModuleFlags($user));
        });
    }
}
