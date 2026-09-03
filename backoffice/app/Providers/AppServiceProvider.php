<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // Enforce HTTPS scheme when running behind Cloudflare Tunnel / Reverse Proxy
        if (
            request()->isSecure() ||
            request()->server('HTTP_X_FORWARDED_PROTO') === 'https' ||
            request()->server('HTTP_X_FORWARDED_SSL') === 'on' ||
            str_starts_with(config('app.url'), 'https://')
        ) {
            URL::forceScheme('https');
        }
    }
}
