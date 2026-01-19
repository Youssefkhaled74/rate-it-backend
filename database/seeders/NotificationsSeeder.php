<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserNotification;
use Carbon\Carbon;

class NotificationsSeeder extends Seeder
{
    public function run()
    {
        // Create sample notifications for user_id = 1
        $userId = 1;

        UserNotification::insert([
            [
                'user_id' => $userId,
                'title' => 'Welcome to Rate-It',
                'body' => 'Thanks for joining Rate-It! Start by rating your first place.',
                'type' => 'system',
                'data' => json_encode(['campaign' => 'welcome']),
                'is_read' => false,
                'created_at' => Carbon::now()->subDays(2)->toDateTimeString(),
                'updated_at' => Carbon::now()->subDays(2)->toDateTimeString(),
            ],
            [
                'user_id' => $userId,
                'title' => 'You earned points',
                'body' => 'You earned 50 points for your recent review.',
                'type' => 'reward',
                'data' => json_encode(['points' => 50]),
                'is_read' => false,
                'created_at' => Carbon::now()->subDay()->toDateTimeString(),
                'updated_at' => Carbon::now()->subDay()->toDateTimeString(),
            ],
        ]);
    }
}
