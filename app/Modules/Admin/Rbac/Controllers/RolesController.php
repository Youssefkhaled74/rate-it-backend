<?php

namespace App\Modules\Admin\Rbac\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function index()
    {
        $roles = DB::table('roles')->get();
        return response()->json(['success' => true, 'message' => 'Roles list', 'data' => $roles, 'meta' => null]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles,name']);
        $id = DB::table('roles')->insertGetId(['name' => $request->input('name'), 'guard' => 'admin', 'created_at' => now(), 'updated_at' => now()]);
        $role = DB::table('roles')->where('id', $id)->first();
        return response()->json(['success' => true, 'message' => 'Role created', 'data' => $role, 'meta' => null], 201);
    }

    public function syncPermissions(Request $request, $role)
    {
        $request->validate(['permissions' => 'required|array']);
        $roleRow = DB::table('roles')->where('id', $role)->first();
        if (! $roleRow) {
            return response()->json(['success' => false, 'message' => 'Role not found', 'data' => null, 'meta' => null], 404);
        }
        DB::table('role_has_permissions')->where('role_id', $roleRow->id)->delete();
        foreach ($request->input('permissions') as $permName) {
            $perm = DB::table('permissions')->where('name', $permName)->first();
            if ($perm) {
                DB::table('role_has_permissions')->insert(['role_id' => $roleRow->id, 'permission_id' => $perm->id]);
            }
        }
        return response()->json(['success' => true, 'message' => 'Permissions synced', 'data' => null, 'meta' => null]);
    }
}
