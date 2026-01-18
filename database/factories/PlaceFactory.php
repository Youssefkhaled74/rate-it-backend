<?php

namespace Database\Factories;

use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\Factory;
 

class PlaceFactory extends Factory
{
    protected $model = Place::class;

    public function definition()
    {
        return [
            // 'id' => (string) Str::uuid(), // Removed id generation
            'brand_id' => null,
            'subcategory_id' => null,
            'name' => $this->faker->company . ' ' . $this->faker->word,
            'description' => $this->faker->optional()->sentence,
            'is_featured' => $this->faker->boolean(10),
            'city' => $this->faker->city,
            'area' => $this->faker->streetName,
            'meta' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
