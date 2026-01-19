<?php

namespace App\Modules\User\Profile\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Profile\Requests\UpdateProfileRequest;
use App\Modules\User\Profile\Requests\SendPhoneOtpRequest;
use App\Modules\User\Profile\Requests\VerifyPhoneOtpRequest;
use App\Modules\User\Profile\Services\ProfileService;
use App\Modules\User\Profile\Resources\ProfileResource;
use Illuminate\Http\Request;

class ProfileController extends BaseApiController
{
    protected ProfileService $service;

    public function __construct(ProfileService $service)
    {
        $this->service = $service;
    }

    public function show(Request $request)
    {
        $user = $this->service->getProfile($request->user());
        return $this->success(new ProfileResource($user), 'profile.fetched');
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $data = $request->only(['full_name','email']);
        if ($request->hasFile('avatar')) {
            $data['avatar_file'] = $request->file('avatar');
        }

        $updated = $this->service->updateProfile($user, $data);
        return $this->success(new ProfileResource($updated), 'profile.updated');
    }

    public function sendPhoneOtp(SendPhoneOtpRequest $request)
    {
        $user = $request->user();
        $meta = $this->service->sendPhoneChangeOtp($user, $request->phone);
        return $this->success(null, 'profile.phone_otp_sent', $meta);
    }

    public function verifyPhoneOtp(VerifyPhoneOtpRequest $request)
    {
        $user = $request->user();
        $updated = $this->service->verifyPhoneChangeOtp($user, $request->phone, $request->otp);
        return $this->success(new ProfileResource($updated), 'profile.phone_verified');
    }
}
