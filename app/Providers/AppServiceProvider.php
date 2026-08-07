<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Midtrans\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();

        Config::$serverKey = config('midtrans.serverKey');
        Config::$clientKey = config('midtrans.clientKey');
        Config::$isProduction = config('midtrans.isProduction');
        Config::$isSanitized = config('midtrans.isSanitized');
        Config::$is3ds = config('midtrans.is3ds');

        view()->composer('*', function ($view) {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $view->with('cafeSettings', [
                    'cafe_open_time' => \App\Models\Setting::get('cafe_open_time', '08:00'),
                    'cafe_close_time' => \App\Models\Setting::get('cafe_close_time', '22:00'),
                    'shift_duration_hours' => \App\Models\Setting::get('shift_duration_hours', '7'),
                    'before_close_notif_minutes' => \App\Models\Setting::get('before_close_notif_minutes', '15'),
                    'before_shift_notif_minutes' => \App\Models\Setting::get('before_shift_notif_minutes', '15'),
                    'order_limit_minutes' => \App\Models\Setting::get('order_limit_minutes', '10'),
                ]);
            }
        });
    }
}
