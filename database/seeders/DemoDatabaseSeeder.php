<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoDatabaseSeeder extends Seeder
{
    public function run()
    {
        // run bootstrap first
        $this->call([ 
            AdminsSeeder::class,
            CategoriesSeeder::class,
            UserLevelsSeeder::class,
            NotificationTemplatesSeeder::class,
            ContentSeeder::class,
        ]);

        // demo data
        $this->call([
            DemoBrandsSeeder::class,
            DemoPlacesBranchesSeeder::class,
            DemoVendorUsersSeeder::class,
            DemoUsersSeeder::class,
            DemoSubscriptionsSeeder::class,
            DemoRatingCriteriaSeeder::class,
            DemoReviewsSeeder::class,
            DemoPointsSeeder::class,
            DemoVouchersSeeder::class,
            DemoNotificationsSeeder::class,
            DemoActivityLogsSeeder::class,
        ]);
    }
}
