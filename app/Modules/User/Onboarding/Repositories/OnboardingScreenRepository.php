<?php

namespace App\Modules\User\Onboarding\Repositories;

use App\Models\Onboarding;
use Illuminate\Support\Collection;

class OnboardingScreenRepository
{
    public function getActive(int $limit = 3): Collection
    {
        return Onboarding::query()
            ->where('is_active', true)
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();
    }
}
