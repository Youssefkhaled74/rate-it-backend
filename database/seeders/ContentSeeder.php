<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run()
    {
        // Onboarding screens
        $screens = [
            ['title' => 'Welcome to Rate It', 'body' => 'Discover and rate places near you', 'sort_order' => 1],
            ['title' => 'Earn Points', 'body' => 'Write reviews and earn loyalty points', 'sort_order' => 2],
            ['title' => 'Redeem Vouchers', 'body' => 'Use points for discounts and vouchers', 'sort_order' => 3],
        ];

        foreach ($screens as $s) {
            DB::table('onboarding_screens')->updateOrInsert(
                ['title' => $s['title']],
                ['body' => $s['body'], 'sort_order' => $s['sort_order'], 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        // Banners
        $banners = [
            ['image_path' => '/images/banner1.jpg', 'link_url' => null, 'sort_order' => 1],
            ['image_path' => '/images/banner2.jpg', 'link_url' => null, 'sort_order' => 2],
        ];

        foreach ($banners as $b) {
            DB::table('banner_offers')->updateOrInsert(
                ['image_path' => $b['image_path']],
                ['link_url' => $b['link_url'], 'sort_order' => $b['sort_order'], 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
