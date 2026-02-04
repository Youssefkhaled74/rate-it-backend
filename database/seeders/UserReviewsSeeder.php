<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class UserReviewsSeeder extends Seeder
{
    public function run(): void
    {
        $branchIds = Branch::query()->pluck('id')->all();
        if (empty($branchIds)) {
            $this->command?->warn('UserReviewsSeeder: no branches found. Seed branches first.');
            return;
        }

        $comments = [
            'Great service and friendly staff.',
            'Good experience overall.',
            'Average experience, could be better.',
            'Not satisfied with the service.',
            'Excellent! Will visit again.',
        ];

        $ratings = [2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0];

        User::query()
            ->select(['id'])
            ->chunkById(500, function ($users) use ($branchIds, $comments, $ratings) {
                foreach ($users as $user) {
                    $hasReview = Review::query()->where('user_id', $user->id)->exists();
                    if ($hasReview) continue;

                    $branchId = Arr::random($branchIds);
                    $branch = Branch::query()->select(['id', 'place_id'])->find($branchId);
                    if (!$branch) continue;

                    Review::query()->create([
                        'user_id' => $user->id,
                        'branch_id' => $branch->id,
                        'place_id' => $branch->place_id,
                        'overall_rating' => Arr::random($ratings),
                        'comment' => Arr::random($comments),
                        'status' => 'ACTIVE',
                        'review_score' => null,
                        'created_at' => now()->subDays(rand(0, 60)),
                        'updated_at' => now(),
                    ]);
                }
            });
    }
}
