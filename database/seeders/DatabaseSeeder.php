<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Bootstrap / safe seeders (idempotent)
        $this->call([
            AdminsSeeder::class,
            CategoriesSeeder::class,
            UserLevelsSeeder::class,
            NotificationTemplatesSeeder::class,
            ContentSeeder::class,
            \Database\Seeders\OnboardingScreensSeeder::class,
            \Database\Seeders\GendersSeeder::class,
            \Database\Seeders\NationalitiesSeeder::class,
                \Database\Seeders\HomeBannersSeeder::class,
        ]);

        // Optionally run demo data only in local or when DB_SEED_DEMO=true
        if (app()->environment('local') || env('DB_SEED_DEMO', false)) {
            $this->call(\Database\Seeders\DemoDatabaseSeeder::class);

            // Demo helpers for reviews/questions
            $this->call([ 
                \Database\Seeders\DemoMedicalSubcategorySeeder::class,
                \Database\Seeders\AttachBranch1ToClinicsSeeder::class,
                \Database\Seeders\RatingCriteriaSeeder::class,
            ]);

            // Ensure branch 1 has criteria to match questions -> reviews flow
            $this->call([\Database\Seeders\EnsureBranch1CriteriaSeeder::class]);
        }
    }
}
