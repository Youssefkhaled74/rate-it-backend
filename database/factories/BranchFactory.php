<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition()
    {
        return [
            'id' => (string) Str::uuid(),
            'place_id' => null,
            'name' => $this->faker->company . ' Branch',
            'address' => $this->faker->address,
            'lat' => $this->faker->optional()->latitude,
            'lng' => $this->faker->optional()->longitude,
            'working_hours' => null,
            'qr_code_value' => Str::upper(Str::random(12)),
            'qr_generated_at' => now(),
            'review_cooldown_days' => $this->faker->numberBetween(0,7),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
