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
        // Register authorization policies
        // Use Gate::policy() in Laravel 11+ or update config/auth.php
        \Illuminate\Support\Facades\Gate::policy(Admin::class, AdminPolicy::class);
    }

    /**
     * Register module routes.
     */
    protected function registerRoutes(): void
    {
        // Register web routes (Blade dashboard)
        $this->registerWebRoutes();
        
        // Register API routes (AJAX endpoints)
        $this->registerApiRoutes();
    }

    /**
     * Register web routes (Blade dashboard pages).
     */
    private function registerWebRoutes(): void
    {
        \Illuminate\Support\Facades\Route::group([
            'middleware' => ['web'],
            'prefix' => 'admin',
            'as' => 'admin.',
        ], function () {
            $this->loadRoutesFrom(base_path('Modules/Admin/routes/web.php'));
        });
    }

    /**
     * Register API routes (AJAX/JSON endpoints).
     */
    private function registerApiRoutes(): void
    {
        \Illuminate\Support\Facades\Route::group([
            'middleware' => ['api'],
            'prefix' => 'admin/api',
            'as' => 'admin.api.',
        ], function () {
            $this->loadRoutesFrom(base_path('Modules/Admin/routes/api.php'));
        });
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
