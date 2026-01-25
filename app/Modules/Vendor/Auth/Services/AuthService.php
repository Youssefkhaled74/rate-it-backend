<?php

namespace App\Modules\Vendor\Auth\Services;

use App\Models\VendorUser;
use App\Support\Exceptions\ApiException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Authenticate vendor user by phone and password
     * 
     * @throws ApiException
     */
    public function login(string $phone, string $password): VendorUser
    {
        $vendor = VendorUser::where('phone', $phone)
            ->whereNotNull('password_hash')
            ->first();

        if (! $vendor) {
            throw new ApiException(__('auth.vendor_invalid_credentials'), 401);
        }

        if (! $vendor->is_active) {
            throw new ApiException(__('auth.vendor_inactive'), 401);
        }

        if (! $vendor->verifyPassword($password)) {
            throw new ApiException(__('auth.vendor_invalid_credentials'), 401);
        }

        return $vendor;
    }

    /**
     * Create API token for vendor user
     */
    public function createTokenForVendor(VendorUser $vendor, string $tokenName = 'vendor-token'): string
    {
        return $vendor->createToken($tokenName)->plainTextToken;
    }

    /**
     * Revoke all tokens for vendor user (logout)
     */
    public function logout(VendorUser $vendor): void
    {
        $vendor->tokens()->delete();
    }
}
