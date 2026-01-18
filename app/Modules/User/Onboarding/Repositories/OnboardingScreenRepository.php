<?php

namespace App\Modules\User\Onboarding\Repositories;

use App\Modules\User\Onboarding\Models\OnboardingScreen;
use Illuminate\Support\Collection;

class OnboardingScreenRepository
{
    public function getActive(int $limit = 3): Collection
    {
        return OnboardingScreen::query()
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->limit($limit)
            ->get();
    }
}
