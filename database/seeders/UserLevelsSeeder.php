<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class UserLevelsSeeder extends Seeder
{
    public function run()
    {
        $levels = [
            ['name' => 'Bronze', 'min_reviews' => 0, 'benefits' => json_encode(['discount' => '5%'])],
            ['name' => 'Silver', 'min_reviews' => 10, 'benefits' => json_encode(['discount' => '10%'])],
            ['name' => 'Gold', 'min_reviews' => 25, 'benefits' => json_encode(['discount' => '15%'])],
        ];

        foreach ($levels as $l) {
            DB::table('user_levels')->insert([
                'name' => $l['name'],
                'min_reviews' => $l['min_reviews'],
                'benefits' => $l['benefits'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
