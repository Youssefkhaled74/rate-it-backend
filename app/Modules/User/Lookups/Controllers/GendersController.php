<?php

namespace App\Modules\User\Lookups\Controllers;

use App\Support\Api\BaseApiController;
use App\Modules\User\Lookups\Models\Gender;
use Illuminate\Http\Request;

class GendersController extends BaseApiController
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $genders = Gender::where('is_active', true)
            ->orderBy('name_' . ($locale === 'ar' ? 'ar' : 'en'))
            ->get()
            ->map(function ($g) use ($locale) {
                return [
                    'id' => $g->id,
                    'code' => $g->code,
                    'name' => $locale === 'ar' ? $g->name_ar : $g->name_en,
                ];
            });

        return $this->success($genders, 'lookups.genders');
    }
}
