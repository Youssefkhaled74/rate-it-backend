<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition()
    {
        $status = $this->faker->randomElement(['FREE','ACTIVE','EXPIRED']);
        $started = $this->faker->dateTimeBetween('-1 years','now');
        $freeUntil = ($status === 'FREE') ? $this->faker->dateTimeBetween($started,'+6 months') : null;

        return [
            'id' => (string) Str::uuid(),
            'user_id' => null,
            'status' => $status,
            'started_at' => $started,
            'free_until' => $freeUntil,
            'paid_until' => ($status === 'ACTIVE') ? $this->faker->dateTimeBetween('now','+1 year') : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
