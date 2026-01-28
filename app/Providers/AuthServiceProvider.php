<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Admin;
use App\Policies\AdminPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Admin::class => AdminPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Development-friendly global bypass for admin authorization.
        // Allows any ability when:
        // - the authenticated user is an Admin with a SUPER_ADMIN role, OR
        // - the environment flag ADMIN_BYPASS_AUTH is true and the authenticated user is an Admin.
        // This only affects Admin model instances and therefore normal application users are not impacted.
        Gate::before(function ($user, $ability) {
            if (empty($user)) {
                return null;
            }

            // Only consider Admin model instances for this bypass
            if (! ($user instanceof \App\Models\Admin)) {
                return null;
            }

            // ENV bypass for development/testing. Set ADMIN_BYPASS_AUTH=true in .env to enable.
            if (env('ADMIN_BYPASS_AUTH', false)) {
                return true;
            }

            // Role-based SUPER_ADMIN bypass. Checks roles relation if available, or the simple `role` field.
            try {
                if (method_exists($user, 'roles')) {
                    if ($user->roles()->where('name', 'SUPER_ADMIN')->exists()) {
                        return true;
                    }
                }
            } catch (\Throwable $e) {
                // ignore DB issues while determining roles
            }

            if (strtoupper($user->role ?? '') === 'SUPER_ADMIN') {
                return true;
            }

            return null; // fallback to normal policy checks
        });
    }
}
