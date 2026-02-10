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
            'name_en' => $this->faker->company,
            'name_ar' => $this->faker->word,
            'logo_url' => $this->faker->imageUrl(200,200),
            'logo' => $this->faker->imageUrl(200,200),
            'cover_image' => $this->faker->imageUrl(400,200),
            'description_en' => $this->faker->paragraph,
            'description_ar' => $this->faker->paragraph,
            'points_expiry_days' => $this->faker->optional()->numberBetween(30,365),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
