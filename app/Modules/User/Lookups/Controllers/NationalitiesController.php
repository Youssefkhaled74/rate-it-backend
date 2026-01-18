<?php

namespace App\Modules\User\Lookups\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Lookups\Models\Nationality;
use App\Modules\User\Lookups\Resources\NationalityResource;
use Illuminate\Http\Request;

class NationalitiesController extends BaseApiController
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $items = Nationality::where('is_active', true)
            ->orderBy('name_' . ($locale === 'ar' ? 'ar' : 'en'))
            ->get();

        return $this->success(NationalityResource::collection($items), 'lookups.nationalities');
    }
}
