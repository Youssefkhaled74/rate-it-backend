<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\User\Onboarding\Contracts\OnboardingServiceInterface;
use App\Modules\User\Onboarding\Services\OnboardingService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind module service interfaces to implementations
        $this->app->bind(OnboardingServiceInterface::class, OnboardingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
