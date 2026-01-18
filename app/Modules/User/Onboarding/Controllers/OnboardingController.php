<?php

namespace App\Modules\User\Onboarding\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Onboarding\Models\OnboardingScreen;
use App\Modules\User\Onboarding\Resources\OnboardingScreenResource;

class OnboardingController extends BaseApiController
{
    public function index()
    {
        $screens = OnboardingScreen::query()
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->limit(3)
            ->get();

        return $this->success(OnboardingScreenResource::collection($screens), 'onboarding.list');
    }
}
