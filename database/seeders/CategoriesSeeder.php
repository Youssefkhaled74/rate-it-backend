<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name_en' => 'Food', 'name_ar' => 'مأكولات', 'subcategories' => ['Restaurants','Cafes','Fast Food']],
            ['name_en' => 'Shopping', 'name_ar' => 'تسوق', 'subcategories' => ['Malls','Clothing','Electronics']],
            ['name_en' => 'Services', 'name_ar' => 'خدمات', 'subcategories' => ['Health','Beauty','Automotive']],
        ];

        foreach ($categories as $cat) {
            $category = DB::table('categories')->updateOrInsert(
                ['name_en' => $cat['name_en']],
                ['name_ar' => $cat['name_ar'], 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]
            );

            $catRow = DB::table('categories')->where('name_en', $cat['name_en'])->first();

            foreach ($cat['subcategories'] as $sub) {
                DB::table('subcategories')->updateOrInsert(
                    ['category_id' => $catRow->id, 'name_en' => $sub],
                    ['name_ar' => null, 'updated_at' => now(), 'created_at' => now()]
                );
            }
        }
    }
}
