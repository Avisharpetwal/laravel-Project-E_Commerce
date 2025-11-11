<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductImageTest extends TestCase
{
    use RefreshDatabase;

    
    public function test_it_has_fillable_attributes()
    {
        $productImage = new ProductImage();

        $this->assertEquals(
            ['product_id', 'path', 'type', 'is_featured'],
            $productImage->getFillable()
        );
    }

    
    public function test_it_belongs_to_a_product()
    {
        // Create a product
        $product = Product::factory()->create();

        // Create a product image linked to that product
        $image = ProductImage::factory()->create([
            'product_id' => $product->id,
        ]);

        // Assert relationship works
        $this->assertInstanceOf(Product::class, $image->product);
        $this->assertEquals($product->id, $image->product->id);
    }
}
