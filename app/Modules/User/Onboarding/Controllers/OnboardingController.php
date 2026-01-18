<?php

namespace App\Modules\User\Onboarding\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Onboarding\Models\OnboardingScreen;

class OnboardingController extends BaseApiController
{
    public function index()
    {
        $screens = OnboardingScreen::query()
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->limit(3)
            ->get(['id','title','body','sort_order']);

        return $this->success($screens, null, null);
    }
}
