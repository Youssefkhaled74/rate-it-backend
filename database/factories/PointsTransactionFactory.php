<?php

namespace Database\Factories;

use App\Models\PointsTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PointsTransactionFactory extends Factory
{
    protected $model = PointsTransaction::class;

    public function definition()
    {
        return [
            // 'id' => (string) Str::uuid(),
            'user_id' => null,
            'brand_id' => null,
            'type' => 'EARN_REVIEW',
            'points' => $this->faker->numberBetween(5,50),
            'reference_type' => 'REVIEW',
            'reference_id' => null,
            'expires_at' => $this->faker->optional()->dateTimeBetween('+30 days','+365 days'),
            'meta' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
