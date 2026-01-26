<?php

namespace Modules\Admin\app\Http\Controllers\Auth;

use Modules\Admin\app\Http\Controllers\Controller;
use Modules\Admin\app\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('admin::auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $remember = $request->input('remember', false);

        // Attempt login with admin guard
        if (Auth::guard('admin')->attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            $remember
        )) {
            // Update last login timestamp
            /** @phpstan-ignore-next-line */
            auth('admin')->user()->recordLogin();

            return redirect()->route('admin.dashboard')
                ->with('success', __('admin.login_success'));
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', __('admin.login_failed'));
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', __('admin.logout_success'));
    }
}
