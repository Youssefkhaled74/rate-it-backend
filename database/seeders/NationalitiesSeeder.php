<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NationalitiesSeeder extends Seeder
{
    public function run()
    {
        $now = now();
        $rows = [
            ['country_code' => 'EG', 'name_en' => 'Egyptian', 'name_ar' => 'مصري', 'flag_style' => 'shiny', 'flag_size' => 64, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['country_code' => 'SA', 'name_en' => 'Saudi', 'name_ar' => 'سعودي', 'flag_style' => 'shiny', 'flag_size' => 64, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['country_code' => 'AE', 'name_en' => 'Emirati', 'name_ar' => 'إماراتي', 'flag_style' => 'shiny', 'flag_size' => 64, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['country_code' => 'KW', 'name_en' => 'Kuwaiti', 'name_ar' => 'كويتي', 'flag_style' => 'shiny', 'flag_size' => 64, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['country_code' => 'QA', 'name_en' => 'Qatari', 'name_ar' => 'قطري', 'flag_style' => 'shiny', 'flag_size' => 64, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['country_code' => 'JO', 'name_en' => 'Jordanian', 'name_ar' => 'أردني', 'flag_style' => 'shiny', 'flag_size' => 64, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];

        // Upsert by country_code to avoid duplicates and be idempotent
        DB::table('nationalities')->upsert($rows, ['country_code'], ['name_en', 'name_ar', 'flag_style', 'flag_size', 'is_active', 'updated_at']);
    }
}
