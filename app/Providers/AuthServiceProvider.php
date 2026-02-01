<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\User;
use App\Policies\AdminPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Admin::class => AdminPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        /**
         * Global bypass for Admin authorization:
         * - ALWAYS allow everything for SUPER_ADMIN admins.
         * - Optionally allow everything for any admin when ADMIN_BYPASS_AUTH=true (NON-PRODUCTION only).
         * - Does NOT affect normal "User" accounts because we only act on App\Models\Admin.
         */
        Gate::before(function ($user, string $ability) {

            // If not authenticated, do nothing
            if (! $user) {
                return null;
            }

            // Only apply to Admin model instances
            if (! ($user instanceof Admin)) {
                return null;
            }

            // ✅ 1) SUPER_ADMIN bypass (always)
            // Prefer role field if exists
            if (strtoupper((string) ($user->role ?? '')) === 'SUPER_ADMIN') {
                return true;
            }

            // If you have roles() relation, also support it (optional)
            try {
                if (method_exists($user, 'roles') && $user->roles()->where('name', 'SUPER_ADMIN')->exists()) {
                    return true;
                }
            } catch (\Throwable $e) {
                // Ignore DB/relationship issues and continue to normal checks
            }

            // ✅ 2) ENV bypass (non-production only)
            $envBypass = filter_var(env('ADMIN_BYPASS_AUTH', false), FILTER_VALIDATE_BOOLEAN);
            if ($envBypass && ! app()->environment('production')) {
                return true;
            }

            // Continue normal policy checks
            return null;
        });
    }
}
