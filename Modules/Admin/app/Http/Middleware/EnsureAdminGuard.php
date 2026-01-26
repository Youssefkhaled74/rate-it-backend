<?php

namespace Modules\Admin\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via admin guard
        if (!auth('admin')->check()) {
            return redirect()->route('admin.login');
        }

        // Check if admin is active
        if (auth('admin')->user()->status !== 'active') {
            auth('admin')->logout();
            return redirect()->route('admin.login')
                ->with('error', __('admin.account_inactive'));
        }

        return $next($request);
    }
}
