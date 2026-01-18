<?php

namespace App\Modules\User\Lookups\Services;

use App\Modules\User\Lookups\Models\Gender;
use App\Modules\User\Lookups\Models\Nationality;
use App\Modules\User\Lookups\Resources\GenderResource;
use App\Modules\User\Lookups\Resources\NationalityResource;

class LookupsService
{
    public function getGenders(): \Illuminate\Support\Collection
    {
        $locale = app()->getLocale();
        $genders = Gender::where('is_active', true)
            ->orderBy('name_' . ($locale === 'ar' ? 'ar' : 'en'))
            ->get();

        return GenderResource::collection($genders)->collection;
    }

    public function getNationalities(): \Illuminate\Support\Collection
    {
        $locale = app()->getLocale();
        $nationalities = Nationality::where('is_active', true)
            ->orderBy('name_' . ($locale === 'ar' ? 'ar' : 'en'))
            ->get();

        return NationalityResource::collection($nationalities)->collection;
    }

    public function getAllLookups(): array
    {
        return [
            'genders' => $this->getGenders(),
            'nationalities' => $this->getNationalities(),
        ];
    }
}
