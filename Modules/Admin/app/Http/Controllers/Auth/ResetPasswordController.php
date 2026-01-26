<?php

namespace Modules\Admin\app\Http\Controllers\Auth;

use Modules\Admin\app\Http\Controllers\Controller;
use Modules\Admin\app\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /**
     * Show the reset password form.
     */
    public function showResetPasswordForm($token)
    {
        return view('admin::auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle password reset.
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => bcrypt($password)])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('admin.login')
                ->with('success', __('admin.password_reset_success'));
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', __('admin.password_reset_failed'));
    }
}
