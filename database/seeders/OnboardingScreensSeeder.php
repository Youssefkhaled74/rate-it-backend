<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\User\Onboarding\Models\OnboardingScreen;

class OnboardingScreensSeeder extends Seeder
{
    public function run(): void
    {
        $screens = [
            [
                'sort_order' => 1,
                'title' => 'Now... Rate the World from Your Phone',
                'body' => ' You have the chance to rate every service and product - your voice reaches the world',
            ],
            [
                'sort_order' => 2,
                'title' => 'First 100% Specialized Rating App',
                'body' => ' The 21st century platform for authentic ratings - earn points with every share',
            ],
            [
                'sort_order' => 3,
                'title' => 'Your Participation Makes the Difference',
                'body' => ' Your opinion matters to us and helps the world - enjoy real golden rewards',
            ],
        ];

        foreach ($screens as $s) {
            OnboardingScreen::updateOrCreate(
                ['sort_order' => $s['sort_order']],
                array_merge($s, ['is_active' => true])
            );
        }
    }
}
