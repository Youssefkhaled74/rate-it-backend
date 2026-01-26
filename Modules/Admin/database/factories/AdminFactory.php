<?php

namespace Modules\Admin\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Admin\app\Models\Admin;

class AdminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Admin::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'password' => bcrypt('password123'),
            'is_super' => false,
            'status' => 'active',
            'email_verified_at' => now(),
        ];
    }

    /**
     * Indicate that the model should be a super admin.
     */
    public function superAdmin(): self
    {
        return $this->state([
            'is_super' => true,
        ]);
    }

    /**
     * Indicate that the model should be inactive.
     */
    public function inactive(): self
    {
        return $this->state([
            'status' => 'inactive',
        ]);
    }
}
