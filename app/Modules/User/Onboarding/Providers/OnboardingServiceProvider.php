<?php

namespace App\Modules\User\Onboarding\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\User\Onboarding\Contracts\OnboardingServiceInterface;
use App\Modules\User\Onboarding\Services\OnboardingService;
use App\Modules\User\Onboarding\Repositories\OnboardingScreenRepository;

class OnboardingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OnboardingServiceInterface::class, function ($app) {
            return new OnboardingService(new OnboardingScreenRepository());
        });

        // Bind repository if needed elsewhere
        $this->app->singleton(OnboardingScreenRepository::class, function ($app) {
            return new OnboardingScreenRepository();
        });
    }

    public function boot(): void
    {
        // noop
    }
}
