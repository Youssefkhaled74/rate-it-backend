<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomeBannersSeeder extends Seeder
{
    public function run()
    {
        $now = now();
        $rows = [
            [
                'title_en' => 'Black Friday Sale',
                'title_ar' => 'تخفيضات الجمعة السوداء',
                'body_en' => 'Up to 50% off on selected items',
                'body_ar' => 'خصم يصل إلى 50% على منتجات مختارة',
                'image' => 'uploads/banners/black-friday.jpg',
                'action_type' => 'external_url',
                'action_value' => 'https://example.com/black-friday',
                'sort_order' => 1,
                'starts_at' => $now->subDays(10),
                'ends_at' => $now->addDays(20),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title_en' => 'New Arrivals',
                'title_ar' => 'الوافدون الجدد',
                'body_en' => 'Check out the latest products',
                'body_ar' => 'تفقد أحدث المنتجات',
                'image' => 'uploads/banners/new-arrivals.jpg',
                'action_type' => 'category',
                'action_value' => '1',
                'sort_order' => 2,
                'starts_at' => null,
                'ends_at' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title_en' => 'Top Brands',
                'title_ar' => 'أفضل العلامات التجارية',
                'body_en' => 'Shop from popular brands',
                'body_ar' => 'تسوق من العلامات التجارية الشهيرة',
                'image' => 'uploads/banners/top-brands.jpg',
                'action_type' => 'brand',
                'action_value' => '1',
                'sort_order' => 3,
                'starts_at' => null,
                'ends_at' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Upsert by image path to avoid duplicates
        DB::table('home_banners')->upsert($rows, ['image'], ['title_en','title_ar','body_en','body_ar','action_type','action_value','sort_order','starts_at','ends_at','is_active','updated_at']);
    }
}
