<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'review_id' => Review::factory(),
            'path' => $this->faker->imageUrl(),
        ];
    }
}
