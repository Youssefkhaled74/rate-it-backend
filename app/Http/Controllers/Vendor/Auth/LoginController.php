<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\LoginRequest;
use App\Models\VendorUser;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('vendor.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $vendor = VendorUser::where('phone', $data['phone'])->first();

        if (! $vendor || ! $vendor->verifyPassword($data['password'])) {
            return back()->withErrors(['phone' => __('auth.vendor_invalid_credentials')])->withInput();
        }
        if (! $vendor->is_active) {
            return back()->withErrors(['phone' => __('auth.vendor_inactive')])->withInput();
        }

        Auth::guard('vendor_web')->login($vendor);

        return redirect()->route('vendor.home');
    }

    public function logout()
    {
        Auth::guard('vendor_web')->logout();
        return redirect()->route('vendor.login');
    }
}
