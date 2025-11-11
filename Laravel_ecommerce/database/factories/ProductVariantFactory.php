<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            'color' => $this->faker->safeColorName(),
            'sku' => strtoupper($this->faker->bothify('SKU-###??')),
            'stock_qty' => $this->faker->numberBetween(1, 100),
            'price' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
