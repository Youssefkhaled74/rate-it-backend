<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
            $existing = DB::table('categories')->where('name_en', $cat['name_en'])->first();
            if ($existing) {
                DB::table('categories')->where('id', $existing->id)->update([
                    'name_ar' => $cat['name_ar'],
                    'is_active' => true,
                    'updated_at' => now(),
                ]);
                $catId = $existing->id;
            } else {
                $catId = DB::table('categories')->insertGetId([
                    'name_en' => $cat['name_en'],
                    'name_ar' => $cat['name_ar'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($cat['subcategories'] as $sub) {
                $existingSub = DB::table('subcategories')->where('category_id', $catId)->where('name_en', $sub)->first();
                if ($existingSub) {
                    DB::table('subcategories')->where('id', $existingSub->id)->update([
                        'name_ar' => null,
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('subcategories')->insert([
                        'category_id' => $catId,
                        'name_en' => $sub,
                        'name_ar' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
