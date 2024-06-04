<?php

namespace App\Providers;

use App\Models\DailyDeal;
use App\Models\GeneralSetting;
use App\Policies\DealPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Cashier::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(DailyDeal::class, DealPolicy::class);

        $settings = GeneralSetting::bootConfig();
        Inertia::share('general_settings', $settings);
    }
}
