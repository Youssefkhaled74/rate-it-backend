<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\User\Onboarding\Contracts\OnboardingServiceInterface;
use App\Modules\User\Onboarding\Services\OnboardingService;
use App\Modules\User\Auth\Services\AuthService;
use App\Modules\User\Auth\Services\OtpService;
use App\Modules\User\Auth\Services\PasswordResetService;
use App\Modules\User\Auth\Repositories\OtpCodeRepository;
use App\Modules\User\Auth\Repositories\PasswordResetRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Admin Module
        $this->app->register(\App\Providers\AdminModuleServiceProvider::class);

        // Bind module service interfaces to implementations
        $this->app->bind(OnboardingServiceInterface::class, OnboardingService::class);

        // Auth bindings
        $this->app->singleton(OtpCodeRepository::class, OtpCodeRepository::class);
        $this->app->singleton(PasswordResetRepository::class, PasswordResetRepository::class);
        $this->app->bind(OtpService::class, function($app){ return new OtpService($app->make(OtpCodeRepository::class)); });
        $this->app->bind(PasswordResetService::class, function($app){ return new PasswordResetService($app->make(PasswordResetRepository::class)); });
        $this->app->bind(AuthService::class, AuthService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
