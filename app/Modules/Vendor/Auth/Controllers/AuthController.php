<?php

namespace App\Modules\Vendor\Auth\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\Vendor\Auth\Requests\LoginRequest;
use App\Modules\Vendor\Auth\Services\AuthService;
use App\Modules\Vendor\Auth\Resources\VendorUserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseApiController
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * POST /api/v1/vendor/auth/login
     * 
     * Login vendor user with phone and password
     */
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        
        try {
            $vendor = $this->authService->login($data['phone'], $data['password']);
            $token = $this->authService->createTokenForVendor($vendor);

            return $this->success(
                [
                    'vendor' => new VendorUserResource($vendor),
                    'token' => $token,
                ],
                'auth.vendor_login_success'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 401);
        }
    }

    /**
     * GET /api/v1/vendor/auth/me
     * 
     * Get current authenticated vendor user
     */
    public function me(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        
        if (! $vendor) {
            return $this->error('auth.unauthenticated', null, 401);
        }

        return $this->success(
            new VendorUserResource($vendor),
            'auth.vendor_profile'
        );
    }

    /**
     * POST /api/v1/vendor/auth/logout
     * 
     * Logout vendor user (revoke current token)
     */
    public function logout(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        
        if (! $vendor) {
            return $this->error('auth.unauthenticated', null, 401);
        }

        $this->authService->logout($vendor);

        return $this->success(null, 'auth.vendor_logout_success');
    }
}
