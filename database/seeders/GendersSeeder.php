<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GendersSeeder extends Seeder
{
    public function run()
    {
        $now = now();
        $rows = [
            ['code' => 'male', 'name_en' => 'Male', 'name_ar' => 'ذكر', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'female', 'name_en' => 'Female', 'name_ar' => 'أنثى', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($rows as $row) {
            DB::table('genders')->updateOrInsert(['code' => $row['code']], $row);
        }
    }
}
