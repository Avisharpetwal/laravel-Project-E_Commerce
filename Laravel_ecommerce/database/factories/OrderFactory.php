<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = \App\Models\Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'payment_method' => $this->faker->randomElement(['COD', 'Online']),
            'total' => $this->faker->numberBetween(500, 5000),
            'status' => 'Pending',
            'notes' => $this->faker->sentence(),
        ];
    }
}
