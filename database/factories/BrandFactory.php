<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;


class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'logo_url' => $this->faker->imageUrl(200,200),
            'points_expiry_days' => $this->faker->optional()->numberBetween(30,365),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
