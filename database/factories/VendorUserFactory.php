<?php

namespace Database\Factories;

use App\Models\VendorUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VendorUserFactory extends Factory
{
    protected $model = VendorUser::class;

    public function definition()
    {
        return [
            'id' => (string) Str::uuid(),
            'brand_id' => null,
            'branch_id' => null,
            'name' => $this->faker->name,
            'phone' => $this->faker->unique()->numerify('05########'),
            'email' => $this->faker->optional()->unique()->safeEmail,
            'password_hash' => bcrypt('secret'),
            'role' => 'VENDOR_ADMIN',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
