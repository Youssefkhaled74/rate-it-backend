<?php

namespace App\Modules\User\Auth\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Auth\Requests\LoginRequest;
use App\Modules\User\Auth\Requests\RegisterRequest;
use App\Modules\User\Auth\Requests\SendOtpRequest;
use App\Modules\User\Auth\Requests\VerifyOtpRequest;
use App\Modules\User\Auth\Requests\ResetPasswordRequest;
use App\Modules\User\Auth\Services\AuthService;
use App\Modules\User\Auth\Services\OtpService;
use App\Modules\User\Auth\Services\PasswordResetService;
use App\Modules\User\Auth\Services\PhoneVerificationService;
use App\Modules\User\Auth\Requests\SendPhoneOtpRequest;
use App\Modules\User\Auth\Requests\VerifyPhoneOtpRequest;
use App\Modules\User\Auth\Resources\UserResource;
use Illuminate\Http\Request;

class AuthController extends BaseApiController
{
    protected AuthService $authService;
    protected OtpService $otpService;
    protected PasswordResetService $passwordResetService;
    protected PhoneVerificationService $phoneVerificationService;

    public function __construct(AuthService $authService, OtpService $otpService, PasswordResetService $passwordResetService, PhoneVerificationService $phoneVerificationService)
    {
        $this->authService = $authService;
        $this->otpService = $otpService;
        $this->passwordResetService = $passwordResetService;
        $this->phoneVerificationService = $phoneVerificationService;
    }

    public function sendPhoneOtp(SendPhoneOtpRequest $request)
    {
        $otp = $this->phoneVerificationService->sendOtp($request->phone);
        return $this->success(null, 'auth.phone_otp_sent', ['otp_code' => $otp]);
    }

    public function verifyPhoneOtp(VerifyPhoneOtpRequest $request)
    {
        // idempotent - if user is already verified return message
        $user = \App\Models\User::where('phone', $request->phone)->first();
        if ($user && $user->phone_verified_at) {
            return $this->success(null, 'auth.phone_already_verified');
        }

        $this->phoneVerificationService->verifyOtp($request->phone, $request->otp);

        $user = \App\Models\User::where('phone', $request->phone)->first();
        return $this->success(['user' => new \App\Modules\User\Auth\Resources\UserResource($user)], 'auth.phone_verified');
    }

    public function login(LoginRequest $request)
    {
        $user = $this->authService->login($request->phone, $request->password);
        $token = $this->authService->createTokenForUser($user);

        return $this->success(['user' => new UserResource($user), 'token' => $token], 'auth.login_success');
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());
        $token = $this->authService->createTokenForUser($user);

        return $this->success(['user' => new UserResource($user), 'token' => $token], 'auth.register_success');
    }

    public function sendOtp(SendOtpRequest $request)
    {
        $code = $this->otpService->sendOtp($request->phone);
        // For dev, include code in meta (will be logged). In production remove.
        return $this->success(null, 'auth.otp_sent', ['otp_code' => $code]);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $this->otpService->verifyOtp($request->phone, $request->otp);
        $resetToken = $this->passwordResetService->issueResetToken($request->phone);

        return $this->success(['reset_token' => $resetToken], 'auth.otp_verified');
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->passwordResetService->resetPassword($request->phone, $request->reset_token, $request->new_password);
        return $this->success(null, 'auth.password_changed');
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());
        return $this->success(null, 'auth.logout_success');
    }

    public function me(Request $request)
    {
        $user = $request->user()->loadMissing([
            'gender',
            'nationality',
            'city',
            'area.city',
            'notifications',
            'reviews.place',
            'reviews.branch',
            'reviews.photos',
            'reviews.answers.choice',
            'reviews.answers.criteria',
            'reviews.answers.photos',
        ]);

        return $this->success(new UserResource($user), 'auth.me');
    }
}
