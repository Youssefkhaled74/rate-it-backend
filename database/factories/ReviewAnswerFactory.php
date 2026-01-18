<?php

namespace Database\Factories;

use App\Models\ReviewAnswer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReviewAnswerFactory extends Factory
{
    protected $model = ReviewAnswer::class;

    public function definition()
    {
        return [
            'review_id' => null,
            'criteria_id' => null,
            'rating_value' => null,
            'yes_no_value' => null,
            'choice_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
        // 'id' => (string) Str::uuid(),
}
