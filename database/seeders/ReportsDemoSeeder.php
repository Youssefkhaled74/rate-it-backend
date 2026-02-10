<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReportsDemoSeeder extends Seeder
{
    public function run()
    {
        // Base demo data (users, brands, places, branches, reviews, points, vouchers, subscriptions)
        $this->call([
            DemoDatabaseSeeder::class,
        ]);

        // Ensure plans + points settings exist
        $this->call([
            SubscriptionPlansSeeder::class,
            PointsSettingsSeeder::class,
        ]);

        // Add missing data needed for KPI reports
        $this->call([
            DemoSubscriptionTransactionsSeeder::class,
            DemoQrSessionsSeeder::class,
            DemoUserLevelHistorySeeder::class,
            FixDemoImagesSeeder::class,
        ]);
    }
}
