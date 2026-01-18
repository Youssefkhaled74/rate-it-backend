<?php

namespace App\Modules\User\Onboarding\Contracts;

use Illuminate\Support\Collection;

interface OnboardingServiceInterface
{
    public function getActiveScreens(int $limit = 3): Collection;
}
