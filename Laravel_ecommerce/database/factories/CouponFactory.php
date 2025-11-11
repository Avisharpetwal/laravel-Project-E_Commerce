<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Coupon;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition()
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('COUPON???')),
            'discount_amount' => $this->faker->numberBetween(5, 50),
            'expiry_date' => $this->faker->dateTimeBetween('+1 days', '+1 year')->format('Y-m-d'),
            'minimum_value' => $this->faker->numberBetween(50, 500),
        ];
    }
}
