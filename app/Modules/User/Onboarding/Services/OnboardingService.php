<?php

namespace App\Modules\User\Onboarding\Services;

use App\Modules\User\Onboarding\Contracts\OnboardingServiceInterface;
use App\Modules\User\Onboarding\Repositories\OnboardingScreenRepository;
use Illuminate\Support\Collection;

class OnboardingService implements OnboardingServiceInterface
{
    protected OnboardingScreenRepository $repo;

    public function __construct(OnboardingScreenRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getActiveScreens(int $limit = 3): Collection
    {
        return $this->repo->getActive($limit);
    }
}
