<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    
    public function test_it_can_create_a_wishlist_item()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->assertDatabaseHas('wishlists', [
            'id' => $wishlist->id,
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    
    public function test_it_belongs_to_a_product()
    {
        $product = Product::factory()->create();
        $wishlist = Wishlist::factory()->create([
            'product_id' => $product->id,
        ]);

        $this->assertInstanceOf(Product::class, $wishlist->product);
        $this->assertEquals($product->id, $wishlist->product->id);
    }
}
