<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PlacesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Ensure brand exists
        $brand = DB::table('brands')->where('name_en', 'Burger King')->first();

        // Create a 'Dr. Ahmed Sayed' place (medical profile)
        $drPlace = [
            'brand_id' => null,
            'subcategory_id' => DB::table('subcategories')->where('name_en', 'Clinics')->value('id'),
            'name_en' => 'Dr. Ahmed Sayed',
            'name_ar' => 'د. أحمد سيد',
            'description_en' => 'Experienced general surgeon specializing in minimally invasive procedures with 10+ years of practice.',
            'description_ar' => 'جراح عام ذو خبرة متخصصة في الإجراءات قليلة التوغل مع أكثر من 10 سنوات من الممارسة.',
            'logo' => 'uploads/places/dr_ahmed_avatar.png',
            'cover_image' => 'uploads/places/dr_ahmed_cover.jpg',
            'city' => 'Cairo',
            'area' => 'Madinet Nasr',
            'is_featured' => false,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        DB::table('places')->upsert([
            $drPlace
        ], ['name_en'], ['name_ar','description_en','description_ar','logo','cover_image','city','area','is_featured','is_active','updated_at']);

        // Create a Burger King place
        if ($brand) {
            $bk = [
                'brand_id' => $brand->id,
                'subcategory_id' => null,
                'name_en' => 'Burger King',
                'name_ar' => 'برجر كينج',
                'description_en' => 'Burger King restaurant',
                'description_ar' => 'مطعم برجر كينج',
                'logo' => 'uploads/places/bk_avatar.png',
                'cover_image' => 'uploads/places/bk_cover.jpg',
                'city' => 'Cairo',
                'area' => 'Madinet Nasr',
                'is_featured' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            DB::table('places')->upsert([$bk], ['name_en','brand_id'], ['name_ar','description_en','description_ar','logo','cover_image','city','area','is_featured','is_active','updated_at']);
        }

        // Create branches for Dr. Ahmed place
        $placeId = DB::table('places')->where('name_en', 'Dr. Ahmed Sayed')->value('id');
        if ($placeId) {
            DB::table('branches')->upsert([
                [
                    'place_id' => $placeId,
                    'name' => null,
                    'address' => '13 Elaraby St, Madinet Nasr',
                    'lat' => null,
                    'lng' => null,
                    'qr_code_value' => 'dr_ahmed_branch_1',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            ], ['place_id','address'], ['name','lat','lng','qr_code_value','updated_at']);
        }

        // Create a branch for Burger King
        $bkPlaceId = DB::table('places')->where('name_en', 'Burger King')->value('id');
        if ($bkPlaceId) {
            DB::table('branches')->upsert([
                [
                    'place_id' => $bkPlaceId,
                    'name' => 'Burger King - Madinet Nasr',
                    'address' => '13 Elaraby St, Madinet Nasr',
                    'lat' => null,
                    'lng' => null,
                    'qr_code_value' => 'bk_branch_1',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            ], ['place_id','address'], ['name','lat','lng','qr_code_value','updated_at']);
        }
    }
}
