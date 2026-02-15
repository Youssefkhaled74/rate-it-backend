<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition()
    {
        return [
            // 'id' => (string) Str::uuid(),
            'user_id' => User::factory(),
            'place_id' => null,
            'branch_id' => null,
            'overall_rating' => $this->faker->randomFloat(1,1,5),
            'comment' => $this->faker->optional()->sentence,
            'status' => 'ACTIVE',
            'review_score' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
