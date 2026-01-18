<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ReviewsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $places = DB::table('places')->whereIn('name_en', ['Dr. Ahmed Sayed', 'Burger King'])->get();
        if ($places->isEmpty()) {
            return;
        }

        foreach ($places as $place) {
            $branchId = DB::table('branches')->where('place_id', $place->id)->value('id');
            if (! $branchId) {
                continue;
            }

            $sampleReviews = [
                [
                    'user_id' => 1,
                    'place_id' => $place->id,
                    'branch_id' => $branchId,
                    'overall_rating' => 5.0,
                    'comment' => 'Very experienced surgeon. Made me feel comfortable throughout the entire process.',
                    'created_at' => $now->copy()->subDays(30),
                    'updated_at' => $now->copy()->subDays(30),
                ],
                [
                    'user_id' => 2,
                    'place_id' => $place->id,
                    'branch_id' => $branchId,
                    'overall_rating' => 4.5,
                    'comment' => 'Great service and friendly staff.',
                    'created_at' => $now->copy()->subDays(10),
                    'updated_at' => $now->copy()->subDays(10),
                ],
                [
                    'user_id' => 3,
                    'place_id' => $place->id,
                    'branch_id' => $branchId,
                    'overall_rating' => 4.8,
                    'comment' => 'Professional and efficient.',
                    'created_at' => $now->copy()->subDays(5),
                    'updated_at' => $now->copy()->subDays(5),
                ],
            ];

            foreach ($sampleReviews as $r) {
                DB::table('reviews')->insertOrIgnore($r);
            }
        }
    }
}
