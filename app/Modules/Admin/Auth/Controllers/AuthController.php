<?php

namespace App\Modules\Admin\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Auth\Requests\LoginRequest;
use App\Modules\Admin\Auth\Services\AuthService;
use App\Modules\Admin\Auth\Resources\AdminResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function login(LoginRequest $request)
    {
        $data = $this->service->login($request->input('email'), $request->input('password'));
        if (! $data) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials', 'data' => null, 'meta' => null], 401);
        }

        return response()->json(['success' => true, 'message' => 'Login successful', 'data' => ['admin' => new AdminResource($data['admin']), 'token' => $data['token']], 'meta' => null], 200);
    }

    public function me(Request $request)
    {
        $admin = $request->get('admin');
        return response()->json(['success' => true, 'message' => 'Admin retrieved', 'data' => new AdminResource($admin), 'meta' => null], 200);
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        $admin = $request->get('admin');
        $this->service->logout($admin, $token);
        return response()->json(['success' => true, 'message' => 'Logged out', 'data' => null, 'meta' => null], 200);
    }
}
