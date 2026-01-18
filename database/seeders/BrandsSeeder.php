<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class BrandsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $rows = [
            [
                'name_en' => 'Burger King',
                'name_ar' => 'برجر كينج',
                'logo' => 'uploads/brands/burger_king_logo.png',
                'cover_image' => 'uploads/brands/burger_king_cover.jpg',
                'description_en' => 'Burger King Corporation is an American multinational chain of hamburger fast-food restaurants.',
                'description_ar' => 'برجر كينج هي سلسلة مطاعم همبرغر عالمية مشهورة.',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('brands')->upsert(
            $rows,
            ['name_en'],
            ['name_ar','logo','cover_image','description_en','description_ar','is_active','updated_at']
        );
    }
}
