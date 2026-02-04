<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name_en' => 'Cairo', 'name_ar' => 'القاهرة'],
            ['name_en' => 'Giza', 'name_ar' => 'الجيزة'],
            ['name_en' => 'Alexandria', 'name_ar' => 'الإسكندرية'],
            ['name_en' => 'Luxor', 'name_ar' => 'الأقصر'],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                ['name_en' => $city['name_en']],
                ['name_ar' => $city['name_ar'], 'is_active' => true]
            );
        }
    }
}
