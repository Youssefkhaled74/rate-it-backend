<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        // Use the admin guard to authenticate the request
        $request->setUserResolver(function () {
            return Auth::guard('admin')->user();
        });

        if (! Auth::guard('admin')->check()) {
            return response()->json([
                'success' => false,
                'message' => __('auth.unauthenticated'),
                'data' => null,
                'meta' => null,
            ], 401);
        }

        $admin = Auth::guard('admin')->user();

        if (! $admin->is_active) {
            return response()->json([
                'success' => false,
                'message' => __('auth.unauthenticated'),
                'data' => null,
                'meta' => null,
            ], 401);
        }

        // attach admin to request for downstream consumers
        $request->attributes->set('admin', $admin);

        return $next($request);
    }
}
