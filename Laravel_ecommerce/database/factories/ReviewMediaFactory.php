<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\ReviewMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewMediaFactory extends Factory
{
    protected $model = ReviewMedia::class;

    public function definition(): array
    {
        return [
            'review_id' => Review::factory(),
            'path' => $this->faker->url(),
            'type' => $this->faker->randomElement(['image', 'video']),
        ];
    }
}
