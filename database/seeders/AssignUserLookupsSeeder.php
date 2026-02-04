<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\City;
use App\Models\User;
use App\Modules\User\Lookups\Models\Gender;
use App\Modules\User\Lookups\Models\Nationality;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class AssignUserLookupsSeeder extends Seeder
{
    public function run(): void
    {
        $genderIds = Gender::query()->pluck('id')->all();
        $nationalityIds = Nationality::query()->pluck('id')->all();
        $cities = City::query()->select(['id'])->get();
        $cityIds = $cities->pluck('id')->all();

        $areas = Area::query()->select(['id', 'city_id'])->get();
        $areaIdsByCity = $areas->groupBy('city_id')->map(function ($group) {
            return $group->pluck('id')->all();
        });
        $areaCityMap = $areas->pluck('city_id', 'id')->all();

        User::query()
            ->select(['id', 'gender_id', 'nationality_id', 'city_id', 'area_id'])
            ->chunkById(500, function ($users) use ($genderIds, $nationalityIds, $cityIds, $areaIdsByCity, $areaCityMap) {
                foreach ($users as $user) {
                    $updates = [];

                    if (empty($user->gender_id) && !empty($genderIds)) {
                        $updates['gender_id'] = Arr::random($genderIds);
                    }

                    if (empty($user->nationality_id) && !empty($nationalityIds)) {
                        $updates['nationality_id'] = Arr::random($nationalityIds);
                    }

                    // If city missing but area exists, infer city from area
                    if (empty($user->city_id) && !empty($user->area_id) && isset($areaCityMap[$user->area_id])) {
                        $updates['city_id'] = $areaCityMap[$user->area_id];
                    }

                    // If city still missing, assign a random city
                    $cityId = $updates['city_id'] ?? $user->city_id;
                    if (empty($cityId) && !empty($cityIds)) {
                        $cityId = Arr::random($cityIds);
                        $updates['city_id'] = $cityId;
                    }

                    // If area missing and we have areas for the chosen city, assign random area
                    if (empty($user->area_id) && !empty($cityId) && isset($areaIdsByCity[$cityId]) && !empty($areaIdsByCity[$cityId])) {
                        $updates['area_id'] = Arr::random($areaIdsByCity[$cityId]);
                    }

                    if (!empty($updates)) {
                        User::query()->whereKey($user->id)->update($updates);
                    }
                }
            });
    }
}
