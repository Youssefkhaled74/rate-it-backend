<?php

namespace Database\Factories;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    public function definition()
    {
        return [
            'id' => (string) Str::uuid(),
            'user_id' => null,
            'brand_id' => null,
            'code' => strtoupper(Str::random(10)),
            'points_used' => $this->faker->numberBetween(50,500),
            'value_amount' => $this->faker->optional()->randomFloat(2,5,200),
            'status' => 'VALID',
            'issued_at' => now(),
            'expires_at' => $this->faker->dateTimeBetween('+1 days', '+30 days'),
            'used_at' => null,
            'used_branch_id' => null,
            'verified_by_vendor_user_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
