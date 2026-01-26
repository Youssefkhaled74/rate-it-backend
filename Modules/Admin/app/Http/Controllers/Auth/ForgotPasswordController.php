<?php

namespace Modules\Admin\app\Http\Controllers\Auth;

use Modules\Admin\app\Http\Controllers\Controller;
use Modules\Admin\app\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('admin::auth.forgot-password');
    }

    /**
     * Handle forgot password request.
     */
    public function sendResetLink(ForgotPasswordRequest $request)
    {
        $status = Password::broker('admins')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()
                ->with('success', __('admin.reset_link_sent'));
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', __('admin.reset_link_failed'));
    }
}
