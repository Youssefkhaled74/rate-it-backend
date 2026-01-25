<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * VendorPermissionWithScoping: Permission check + role/brand scoping
 * 
 * Parameters:
 *   permission:VENDOR_ADMIN  - Only VENDOR_ADMIN role can access
 *   permission:BRANCH_STAFF  - Only BRANCH_STAFF role can access
 *   permission:ANY           - Any vendor role can access (if they have permission)
 * 
 * Example:
 *   Route::middleware([VendorAuthenticate::class, VendorPermissionWithScoping::class.':vendor.vouchers.verify:BRANCH_STAFF'])
 */
class VendorPermissionWithScoping
{
    public function handle(Request $request, Closure $next, $permission = null, $requiredRole = null)
    {
        $vendor = $request->get('vendor');
        if (! $vendor) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated', 'data' => null, 'meta' => null], 401);
        }

        // Check role if specified
        if ($requiredRole && $requiredRole !== 'ANY') {
            if ($vendor->role !== $requiredRole) {
                return response()->json(['success' => false, 'message' => 'Forbidden', 'data' => null, 'meta' => null], 403);
            }
        }

        // Check permission if specified
        if ($permission) {
            $roles = DB::table('model_has_roles')
                ->where('model_type', \App\Models\VendorUser::class)
                ->where('model_id', $vendor->id)
                ->pluck('role_id')
                ->toArray();
            
            if (empty($roles)) {
                return response()->json(['success' => false, 'message' => 'Forbidden', 'data' => null, 'meta' => null], 403);
            }

            $perm = DB::table('permissions')->where('name', $permission)->first();
            if (! $perm) {
                return response()->json(['success' => false, 'message' => 'Forbidden', 'data' => null, 'meta' => null], 403);
            }

            $has = DB::table('role_has_permissions')
                ->whereIn('role_id', $roles)
                ->where('permission_id', $perm->id)
                ->exists();
            
            if (! $has) {
                return response()->json(['success' => false, 'message' => 'Forbidden', 'data' => null, 'meta' => null], 403);
            }
        }

        return $next($request);
    }
}
