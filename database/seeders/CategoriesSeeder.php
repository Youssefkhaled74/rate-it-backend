<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $rows = [
            [
                'name_en' => 'Medical & Healthcare',
                'name_ar' => 'الطب والرعاية الصحية',
                'logo' => 'uploads/categories/medical_healthcare.png',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Hospitals',
                'name_ar' => 'مستشفيات',
                'logo' => 'uploads/categories/hospitals.png',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Companies',
                'name_ar' => 'شركات',
                'logo' => 'uploads/categories/companies.png',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Auto Service',
                'name_ar' => 'خدمات سيارات',
                'logo' => 'uploads/categories/auto_service.png',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_en' => 'Restaurants',
                'name_ar' => 'مطاعم',
                'logo' => 'uploads/categories/restaurants.png',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('categories')->upsert(
            $rows,
            ['name_en'],
            ['name_ar', 'logo', 'is_active', 'updated_at']
        );
    }
}
