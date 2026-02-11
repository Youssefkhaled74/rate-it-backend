<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExistingUsersReviewsSeeder extends Seeder
{
    public function run(): void
    {
        $branchIds = DB::table('branches')
            ->where('is_active', 1)
            ->pluck('id')
            ->values()
            ->all();

        if (empty($branchIds)) {
            $this->command?->warn('No active branches found. Seeder skipped.');
            return;
        }

        $comments = [
            'Excellent service and helpful team.',
            'Good experience overall.',
            'Average experience, could be better.',
            'Clean place and fast response.',
            'Not satisfied with the service.',
            'Great quality and friendly staff.',
            'Reasonable price for the value.',
            'Will visit again.',
        ];

        $now = now();
        $batch = [];
        $created = 0;

        DB::table('users')
            ->select('id')
            ->orderBy('id')
            ->chunk(300, function ($users) use (&$batch, &$created, $branchIds, $comments, $now) {
                foreach ($users as $user) {
                    $reviewsCount = random_int(1, 3);

                    for ($i = 0; $i < $reviewsCount; $i++) {
                        $overall = random_int(10, 50) / 10;
                        $createdAt = Carbon::now()->subDays(random_int(0, 180))->subMinutes(random_int(0, 1440));

                        $batch[] = [
                            'user_id' => $user->id,
                            'place_id' => null,
                            'branch_id' => $branchIds[array_rand($branchIds)],
                            'overall_rating' => $overall,
                            'comment' => $comments[array_rand($comments)],
                            'status' => 'ACTIVE',
                            'is_hidden' => false,
                            'is_featured' => false,
                            'review_score' => $overall,
                            'created_at' => $createdAt,
                            'updated_at' => $now,
                        ];

                        if (count($batch) >= 1000) {
                            DB::table('reviews')->insert($batch);
                            $created += count($batch);
                            $batch = [];
                        }
                    }
                }
            });

        if (!empty($batch)) {
            DB::table('reviews')->insert($batch);
            $created += count($batch);
        }

        $this->command?->info("ExistingUsersReviewsSeeder inserted {$created} reviews.");
    }
}

