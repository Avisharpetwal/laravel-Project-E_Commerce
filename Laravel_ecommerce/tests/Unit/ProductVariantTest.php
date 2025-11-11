<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductVariantTest extends TestCase
{
    use RefreshDatabase;

    
    public function test_it_has_fillable_attributes()
    {
        $variant = new ProductVariant();

        $this->assertEquals(
            ['product_id', 'size', 'color', 'sku', 'stock_qty', 'price'],
            $variant->getFillable()
        );
    }

    
    public function test_it_belongs_to_a_product()
    {
        // Create a product
        $product = Product::factory()->create();

        // Create a product variant linked to that product
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
        ]);

        // Assert the relationship
        $this->assertInstanceOf(Product::class, $variant->product);
        $this->assertEquals($product->id, $variant->product->id);
    }
}
