<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class UserLevelsSeeder extends Seeder
{
    public function run()
    {
        $levels = [
            ['name' => 'Bronze', 'min_reviews' => 0, 'bonus_percent' => 5, 'benefits' => json_encode(['bonus_points' => '5%'])],
            ['name' => 'Silver', 'min_reviews' => 10, 'bonus_percent' => 10, 'benefits' => json_encode(['bonus_points' => '10%'])],
            ['name' => 'Gold', 'min_reviews' => 25, 'bonus_percent' => 15, 'benefits' => json_encode(['bonus_points' => '15%'])],
        ];

        foreach ($levels as $l) {
            DB::table('user_levels')->insert([
                'name' => $l['name'],
                'min_reviews' => $l['min_reviews'],
                'bonus_percent' => $l['bonus_percent'],
                'benefits' => $l['benefits'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
