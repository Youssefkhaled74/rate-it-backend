<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $admin = $request->get('admin');
        if (! $admin) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated', 'data' => null, 'meta' => null], 401);
        }

        // resolve admin roles
        $roles = DB::table('model_has_roles')->where('model_type', \App\Models\Admin::class)->where('model_id', $admin->id)->pluck('role_id')->toArray();
        if (empty($roles)) {
            return response()->json(['success' => false, 'message' => 'Forbidden', 'data' => null, 'meta' => null], 403);
        }

        // check permission mapping
        $perm = DB::table('permissions')->where('name', $permission)->first();
        if (! $perm) {
            return response()->json(['success' => false, 'message' => 'Forbidden', 'data' => null, 'meta' => null], 403);
        }

        $has = DB::table('role_has_permissions')->whereIn('role_id', $roles)->where('permission_id', $perm->id)->exists();
        if (! $has) {
            return response()->json(['success' => false, 'message' => 'Forbidden', 'data' => null, 'meta' => null], 403);
        }

        return $next($request);
    }
}
