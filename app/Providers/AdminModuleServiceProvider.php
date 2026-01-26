<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Admin\app\Models\Admin;
use Modules\Admin\app\Policies\AdminPolicy;

class AdminModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any module services.
     */
    public function register(): void
    {
        $this->registerServices();
    }

    /**
     * Boot any module services.
     */
    public function boot(): void
    {
        $this->loadViews();
        $this->loadTranslations();
        $this->loadMigrations();
        $this->registerMiddleware();
        $this->registerPolicies();
        $this->registerRoutes();
        $this->publishAssets();
    }

    /**
     * Load module views.
     */
    protected function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'admin');
    }

    /**
     * Load module translations.
     */
    protected function loadTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'admin');
    }

    /**
     * Load module migrations.
     */
    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    /**
     * Register module middleware.
     */
    protected function registerMiddleware(): void
    {
        // Middleware is registered in app/Http/Kernel.php
    }

    /**
     * Register module policies.
     */
    protected function registerPolicies(): void
    {
        $this->app['auth']->policy(Admin::class, AdminPolicy::class);
    }

    /**
     * Register module routes.
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
    }

    /**
     * Publish assets and configuration.
     */
    protected function publishAssets(): void
    {
        $this->publishes([
            __DIR__ . '/config/admin.php' => config_path('admin.php'),
        ], 'admin-config');
    }

    /**
     * Register services.
     */
    protected function registerServices(): void
    {
        $this->app->singleton('admin.service', function ($app) {
            return new \Modules\Admin\app\Services\AdminService();
        });

        $this->app->singleton('profile.service', function ($app) {
            return new \Modules\Admin\app\Services\ProfileService();
        });

        $this->app->singleton('locale.service', function ($app) {
            return new \Modules\Admin\app\Services\LocaleService();
        });
    }
}
