<?php

namespace Database\Factories;

use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\Factory;
 

class PlaceFactory extends Factory
{
    protected $model = Place::class;

    public function definition()
    {
        $nameEn = $this->faker->company . ' ' . $this->faker->word;

        return [
            // 'id' => (string) Str::uuid(), // Removed id generation
            'brand_id' => null,
            'subcategory_id' => null,
            'name' => $nameEn,
            'name_en' => $nameEn,
            'name_ar' => $this->faker->optional()->company,
            'description_en' => $this->faker->optional()->sentence,
            'description_ar' => $this->faker->optional()->sentence,
            'is_featured' => $this->faker->boolean(10),
            'is_active' => $this->faker->boolean(90),
            'city' => $this->faker->city,
            'area' => $this->faker->streetName,
            'meta' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
