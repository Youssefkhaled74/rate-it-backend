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
            $existing = DB::table('onboarding_screens')->where('title', $s['title'])->first();
            if ($existing) {
                DB::table('onboarding_screens')->where('id', $existing->id)->update([
                    'body' => $s['body'],
                    'sort_order' => $s['sort_order'],
                    'is_active' => true,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('onboarding_screens')->insert([
                    'id' => (string) Str::uuid(),
                    'title' => $s['title'],
                    'body' => $s['body'],
                    'sort_order' => $s['sort_order'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Banners
        $banners = [
            ['image_path' => '/images/banner1.jpg', 'link_url' => null, 'sort_order' => 1],
            ['image_path' => '/images/banner2.jpg', 'link_url' => null, 'sort_order' => 2],
        ];

        foreach ($banners as $b) {
            $existing = DB::table('banner_offers')->where('image_path', $b['image_path'])->first();
            if ($existing) {
                DB::table('banner_offers')->where('id', $existing->id)->update([
                    'link_url' => $b['link_url'],
                    'sort_order' => $b['sort_order'],
                    'is_active' => true,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('banner_offers')->insert([
                    'id' => (string) Str::uuid(),
                    'image_path' => $b['image_path'],
                    'link_url' => $b['link_url'],
                    'sort_order' => $b['sort_order'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
