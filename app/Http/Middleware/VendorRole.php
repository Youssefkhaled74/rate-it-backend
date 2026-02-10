<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class VendorRole
{
    public function handle($request, Closure $next, string $role)
    {
        $vendor = Auth::guard('vendor_web')->user();
        if (! $vendor || $vendor->role !== $role) {
            return redirect()->route('vendor.home')->with('error', __('vendor.forbidden'));
        }

        return $next($request);
    }
}
