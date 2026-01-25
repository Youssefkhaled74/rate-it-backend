<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VendorAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        // Try to authenticate using the vendor guard (Sanctum)
        if (! Auth::guard('vendor')->check()) {
            return response()->json([
                'success' => false,
                'message' => __('auth.unauthenticated'),
                'data' => null,
                'meta' => null,
            ], 401);
        }

        $vendor = Auth::guard('vendor')->user();

        if (! $vendor || ! $vendor->is_active) {
            return response()->json([
                'success' => false,
                'message' => __('auth.unauthenticated'),
                'data' => null,
                'meta' => null,
            ], 401);
        }

        // attach vendor to request for downstream consumers
        $request->attributes->set('vendor', $vendor);

        return $next($request);
    }
}
