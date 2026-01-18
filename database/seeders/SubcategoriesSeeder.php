<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SubcategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // find Medical & Healthcare category id
        $medical = DB::table('categories')->where('name_en', 'Medical & Healthcare')->first();
        if (! $medical) {
            return;
        }

        $rows = [
            [
                'category_id' => $medical->id,
                'name_en' => 'Clinics',
                'name_ar' => 'عيادات',
                'image' => 'uploads/subcategories/clinics.jpg',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => $medical->id,
                'name_en' => 'Hospitals',
                'name_ar' => 'مستشفيات',
                'image' => 'uploads/subcategories/hospitals.jpg',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => $medical->id,
                'name_en' => 'Laboratories',
                'name_ar' => 'مختبرات',
                'image' => 'uploads/subcategories/laboratories.jpg',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => $medical->id,
                'name_en' => 'Pharmacies',
                'name_ar' => 'صيدليات',
                'image' => 'uploads/subcategories/pharmacies.jpg',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category_id' => $medical->id,
                'name_en' => 'Physical Therapy',
                'name_ar' => 'العلاج الطبيعي',
                'image' => 'uploads/subcategories/physical_therapy.jpg',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('subcategories')->upsert(
            $rows,
            ['category_id', 'name_en'],
            ['name_ar', 'image', 'is_active', 'updated_at']
        );
    }
}
