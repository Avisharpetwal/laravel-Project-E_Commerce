<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = \App\Models\Product::class;

    public function definition(): array
    {
        return [
            'title' => $title = $this->faker->sentence(3),
            // 'slug' => Str::slug($title) . '-' . time(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->numberBetween(100, 1000),
            'discount' => $this->faker->numberBetween(0, 50),
            'sku' => $this->faker->unique()->bothify('SKU-###??'),
            'stock_qty' => $this->faker->numberBetween(1, 100),
            'category_id' => Category::factory(),
            'subcategory_id' => Category::factory(),
        ];
    }
}
