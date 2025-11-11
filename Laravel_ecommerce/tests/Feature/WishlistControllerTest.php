<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class WishlistControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->product = Product::factory()->create();
    }

   
    public function test_authenticated_user_can_view_wishlist_page()
    {
        $this->actingAs($this->user);

        $response = $this->get('/wishlist');

        $response->assertStatus(200);
        $response->assertViewIs('wishlist.index');
    }

    
    public function test_authenticated_user_can_add_product_to_wishlist()
    {
        $this->actingAs($this->user);

        $response = $this->post("/wishlist/add/{$this->product->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Product added to wishlist!');

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
    }

    
    public function test_authenticated_user_can_remove_product_from_wishlist()
    {
        $wishlist = Wishlist::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->post("/wishlist/remove/{$this->product->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Product removed from wishlist!');

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
    }

    
    public function test_unauthenticated_user_cannot_access_wishlist_routes()
    {
        $response = $this->get('/wishlist');
        $response->assertRedirect('/login');

        $response = $this->post("/wishlist/add/{$this->product->id}");
        $response->assertRedirect('/login');

        $response = $this->post("/wishlist/remove/{$this->product->id}");
        $response->assertRedirect('/login');
    }
}
