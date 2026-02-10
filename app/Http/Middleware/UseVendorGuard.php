<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UseVendorGuard
{
    /**
     * Force the application to use the vendor web guard for the current request.
     */
    public function handle($request, Closure $next)
    {
        Auth::shouldUse('vendor_web');
        return $next($request);
    }
}
