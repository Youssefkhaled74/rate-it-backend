<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\City;
use Illuminate\Database\Seeder;

class AreasSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            'Cairo' => [
                ['name_en' => 'Nasr City', 'name_ar' => 'مدينة نصر'],
                ['name_en' => 'Heliopolis', 'name_ar' => 'مصر الجديدة'],
                ['name_en' => 'Maadi', 'name_ar' => 'المعادي'],
            ],
            'Giza' => [
                ['name_en' => 'Dokki', 'name_ar' => 'الدقي'],
                ['name_en' => 'Mohandessin', 'name_ar' => 'المهندسين'],
                ['name_en' => 'Haram', 'name_ar' => 'الهرم'],
            ],
            'Alexandria' => [
                ['name_en' => 'Smouha', 'name_ar' => 'سموحة'],
                ['name_en' => 'Stanley', 'name_ar' => 'ستانلي'],
                ['name_en' => 'Gleem', 'name_ar' => 'جليم'],
            ],
            'Luxor' => [
                ['name_en' => 'East Bank', 'name_ar' => 'البر الشرقي'],
                ['name_en' => 'West Bank', 'name_ar' => 'البر الغربي'],
            ],
        ];

        foreach ($areas as $cityNameEn => $cityAreas) {
            $city = City::firstOrCreate(
                ['name_en' => $cityNameEn],
                ['name_ar' => null, 'is_active' => true]
            );

            foreach ($cityAreas as $area) {
                Area::updateOrCreate(
                    ['city_id' => $city->id, 'name_en' => $area['name_en']],
                    ['name_ar' => $area['name_ar'], 'is_active' => true]
                );
            }
        }
    }
}
