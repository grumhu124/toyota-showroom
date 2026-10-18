<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Separate endpoint keys avoid one form consuming another form's quota.
        RateLimiter::for('public-form', fn (Request $request) => Limit::perMinutes(10, 3)
            ->by($request->ip().'|'.$request->path()));
        // LoginRequest counts failures only; do not attach a request-count throttle.
    }
}
