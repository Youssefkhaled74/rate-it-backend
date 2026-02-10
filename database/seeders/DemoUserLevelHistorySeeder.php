<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoUserLevelHistorySeeder extends Seeder
{
    public function run()
    {
        $users = DB::table('users')->pluck('id')->toArray();
        $levels = DB::table('user_levels')->orderBy('min_reviews')->pluck('id')->toArray();

        if (empty($users) || empty($levels)) {
            return;
        }

        $rows = [];
        $now = now();
        $sampleUsers = array_slice($users, 0, min(200, count($users)));

        foreach ($sampleUsers as $userId) {
            $levelId = $levels[array_rand($levels)];
            $rows[] = [
                'user_id' => $userId,
                'level_id' => $levelId,
                'achieved_at' => $now->copy()->subDays(rand(1, 120)),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('user_level_history')->upsert($rows, ['user_id', 'level_id']);
    }
}
