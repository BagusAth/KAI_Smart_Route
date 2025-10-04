<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
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
        View::share('behaviorGuardConfig', [
            'enabled' => (bool) config('services.behavior_guard.enabled'),
            'endpoint' => config('services.behavior_guard.endpoint'),
            'storage_key' => config('services.behavior_guard.storage_key'),
        ]);
    }
}
