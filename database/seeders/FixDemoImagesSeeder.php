<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixDemoImagesSeeder extends Seeder
{
    public function run()
    {
        $logo = 'assets/images/category-icon-placeholder.png';
        $cover = 'assets/images/category-placeholder.png';

        DB::table('brands')
            ->where(function ($q) {
                $q->where('logo', 'like', 'http%')
                  ->orWhere('logo', 'like', '%?text=%')
                  ->orWhere('logo_url', 'like', 'http%')
                  ->orWhere('logo_url', 'like', '%?text=%')
                  ->orWhere('cover_image', 'like', 'http%')
                  ->orWhere('cover_image', 'like', '%?text=%');
            })
            ->update([
                'logo' => $logo,
                'logo_url' => null,
                'cover_image' => $cover,
                'updated_at' => now(),
            ]);
    }
}
