<?php

namespace App\Modules\Admin\Rbac\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionsController extends Controller
{
    public function index()
    {
        $perms = DB::table('permissions')->get();
        return response()->json(['success' => true, 'message' => 'Permissions list', 'data' => $perms, 'meta' => null]);
    }
}
