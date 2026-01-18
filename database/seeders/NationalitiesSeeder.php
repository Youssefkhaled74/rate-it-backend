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
            ['iso_code' => 'EG', 'name_en' => 'Egyptian', 'name_ar' => 'مصري', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['iso_code' => 'SA', 'name_en' => 'Saudi', 'name_ar' => 'سعودي', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['iso_code' => 'AE', 'name_en' => 'Emirati', 'name_ar' => 'إماراتي', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['iso_code' => 'KW', 'name_en' => 'Kuwaiti', 'name_ar' => 'كويتي', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['iso_code' => 'QA', 'name_en' => 'Qatari', 'name_ar' => 'قطري', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['iso_code' => 'JO', 'name_en' => 'Jordanian', 'name_ar' => 'أردني', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($rows as $row) {
            DB::table('nationalities')->updateOrInsert(['iso_code' => $row['iso_code']], $row);
        }
    }
}
