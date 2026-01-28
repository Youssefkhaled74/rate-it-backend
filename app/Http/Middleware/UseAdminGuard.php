<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UseAdminGuard
{
    /**
     * Force the application to use the admin guard for the current request.
     */
    public function handle($request, Closure $next)
    {
        // Ensure Laravel resolves the authenticated user from the admin guard
        Auth::shouldUse('admin_web');

        return $next($request);
    }
}
