<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class VendorWebAuthenticate
{
    public function handle($request, Closure $next)
    {
        if (! Auth::guard('vendor_web')->check()) {
            return redirect()->route('vendor.login');
        }

        $vendor = Auth::guard('vendor_web')->user();
        if (! $vendor || ! $vendor->is_active) {
            Auth::guard('vendor_web')->logout();
            return redirect()->route('vendor.login');
        }

        $request->attributes->set('vendor', $vendor);

        return $next($request);
    }
}
