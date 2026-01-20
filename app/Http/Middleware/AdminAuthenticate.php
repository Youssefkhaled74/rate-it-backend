<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => __('auth.unauthenticated'),
                'data' => null,
                'meta' => null,
            ], 401);
        }

        $row = DB::table('personal_access_tokens')
            ->where('token', $token)
            ->where('tokenable_type', Admin::class)
            ->first();

        if (! $row) {
            return response()->json([
                'success' => false,
                'message' => __('auth.unauthenticated'),
                'data' => null,
                'meta' => null,
            ], 401);
        }

        $admin = Admin::find($row->tokenable_id);
        if (! $admin || ! $admin->is_active) {
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
