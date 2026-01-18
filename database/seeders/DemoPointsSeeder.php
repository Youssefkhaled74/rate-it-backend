<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoPointsSeeder extends Seeder
{
    public function run()
    {
        $reviews = DB::table('reviews')->pluck('id','user_id');
        $rows = DB::table('reviews')->get();

        foreach ($rows as $r) {
            DB::table('points_transactions')->insert([
                'id' => (string) Str::uuid(),
                'user_id' => $r->user_id,
                'brand_id' => DB::table('places')->where('id', $r->place_id)->value('brand_id'),
                'type' => 'EARN_REVIEW',
                'points' => rand(10,50),
                'reference_type' => 'REVIEW',
                'reference_id' => $r->id,
                'expires_at' => now()->addDays(rand(30,365)),
                'meta' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // add some expire and adjust transactions
        $users = DB::table('users')->inRandomOrder()->limit(50)->get();
        foreach ($users as $u) {
            DB::table('points_transactions')->insert([
                'id' => (string) Str::uuid(),
                'user_id' => $u->id,
                'brand_id' => null,
                'type' => 'ADJUST_ADMIN',
                'points' => rand(-50,100),
                'reference_type' => null,
                'reference_id' => null,
                'expires_at' => null,
                'meta' => json_encode(['reason' => 'promo_adjust']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
