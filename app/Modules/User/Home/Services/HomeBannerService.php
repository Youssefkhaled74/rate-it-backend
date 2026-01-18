<?php

namespace App\Modules\User\Home\Services;

use App\Modules\User\Home\Models\HomeBanner;

class HomeBannerService
{
    public function listForHome()
    {
        return HomeBanner::activeNow()->get();
    }
}
