<?php

namespace App\Modules\User\Lookups\Services;

use App\Models\Area;
use App\Models\City;
use App\Modules\User\Lookups\Models\Gender;
use App\Modules\User\Lookups\Models\Nationality;
use App\Modules\User\Lookups\Resources\AreaResource;
use App\Modules\User\Lookups\Resources\CityResource;
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

    public function getCities(): \Illuminate\Support\Collection
    {
        $locale = app()->getLocale();
        $cities = City::where('is_active', true)
            ->orderBy('name_' . ($locale === 'ar' ? 'ar' : 'en'))
            ->get();

        return CityResource::collection($cities)->collection;
    }

    public function getAreas(?int $cityId = null): \Illuminate\Support\Collection
    {
        $locale = app()->getLocale();
        $query = Area::where('is_active', true);
        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        $areas = $query->orderBy('name_' . ($locale === 'ar' ? 'ar' : 'en'))->get();

        return AreaResource::collection($areas)->collection;
    }

    public function getAllLookups(): array
    {
        return [
            'genders' => $this->getGenders(),
            'nationalities' => $this->getNationalities(),
            'cities' => $this->getCities(),
            'areas' => $this->getAreas(),
        ];
    }
}
