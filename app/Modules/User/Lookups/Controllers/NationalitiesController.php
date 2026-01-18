<?php

namespace App\Modules\User\Lookups\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Lookups\Models\Nationality;
use Illuminate\Http\Request;

class NationalitiesController extends BaseApiController
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $items = Nationality::where('is_active', true)
            ->orderBy('name_' . ($locale === 'ar' ? 'ar' : 'en'))
            ->get()
            ->map(function ($n) use ($locale) {
                return [
                    'id' => $n->id,
                    'iso_code' => $n->iso_code,
                    'name' => $locale === 'ar' ? $n->name_ar : $n->name_en,
                ];
            });

        return $this->success($items, 'lookups.nationalities');
    }
}
