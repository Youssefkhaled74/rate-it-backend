<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition()
    {
        return [
            'id' => (string) Str::uuid(),
            'name' => $this->faker->company,
            'logo_url' => $this->faker->imageUrl(200,200),
            'points_expiry_days' => $this->faker->optional()->numberBetween(30,365),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
