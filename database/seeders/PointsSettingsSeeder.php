<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PointsSetting;

class PointsSettingsSeeder extends Seeder
{
    public function run()
    {
        // idempotent: if an active setting exists, do nothing
        $exists = PointsSetting::where('is_active', true)->first();
        if ($exists) {
            return;
        }

        PointsSetting::create([
            'points_per_review' => 15,
            'invite_points_per_friend' => 50,
            'invitee_bonus_points' => 0,
            'point_value_money' => 0.10,
            'currency' => 'EGP',
            'is_active' => true,
        ]);
    }
}
