<?php

namespace App\Modules\User\Onboarding\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Onboarding\Resources\OnboardingScreenResource;
use App\Modules\User\Onboarding\Contracts\OnboardingServiceInterface;

class OnboardingController extends BaseApiController
{
    protected OnboardingServiceInterface $service;

    public function __construct(OnboardingServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $screens = $this->service->getActiveScreens(3);

        return $this->success(OnboardingScreenResource::collection($screens), 'onboarding.list');
    }
}
