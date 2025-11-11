<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $product;

    public function setUp(): void
    {
        parent::setUp();

        // Create a test user
        $this->user = User::factory()->create();

        // Create a test product
        $this->product = Product::factory()->create([
            'stock_qty' => 10,
            'price' => 100,
            'discount' => 0,
        ]);
    }

    public function test_user_can_add_product_to_cart()
    {
        $response = $this->actingAs($this->user)
                         ->post(route('cart.add', $this->product->id), [
                             'quantity' => 2
                         ]);

        $response->assertRedirect();
        $cart = session('cart');
        $this->assertNotEmpty($cart);
        $this->assertEquals(2, $cart[$this->product->id]['quantity']);
    }

    public function test_user_can_update_cart_quantity()
    {
        // First, add product
        $this->actingAs($this->user)
             ->post(route('cart.add', $this->product->id), [
                 'quantity' => 2
             ]);

        // Then, update quantity
        $response = $this->actingAs($this->user)
                         ->post(route('cart.update', $this->product->id), [
                             'quantity' => 5
                         ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals(5, session('cart')[$this->product->id]['quantity']);
        $this->assertTrue($data['success']);
        $this->assertEquals(500, $data['subtotal']); // 100*5
    }

    public function test_user_can_remove_product_from_cart()
    {
        // Add product first
        $this->actingAs($this->user)
             ->post(route('cart.add', $this->product->id), [
                 'quantity' => 2
             ]);

        $response = $this->actingAs($this->user)
                         ->post(route('cart.remove', $this->product->id));

        $response->assertRedirect();
        $this->assertEmpty(session('cart'));
    }

    public function test_user_can_apply_valid_coupon()
    {
        $coupon = Coupon::factory()->create([
            'code' => 'TEST10',
            'discount_amount' => 50,
            'minimum_value' => 100,
            'expiry_date' => Carbon::tomorrow(),
        ]);

        // Add product
        $this->actingAs($this->user)
             ->post(route('cart.add', $this->product->id), [
                 'quantity' => 2
             ]);

        $response = $this->actingAs($this->user)
                         ->post(route('cart.applyCoupon'), [
                             'coupon_code' => $coupon->code
                         ]);

        $response->assertRedirect();
        $this->assertEquals(50, session('coupon')['discount_amount']);
    }

    public function test_user_can_remove_coupon()
    {
        $coupon = Coupon::factory()->create([
            'code' => 'TEST10',
            'discount_amount' => 50,
            'minimum_value' => 100,
            'expiry_date' => Carbon::tomorrow(),
        ]);

        // Add coupon to session manually
        session(['coupon' => [
            'code' => $coupon->code,
            'discount_amount' => $coupon->discount_amount,
            'minimum_value' => $coupon->minimum_value
        ]]);

        $response = $this->actingAs($this->user)
                         ->post(route('cart.removeCoupon'));

        $response->assertRedirect();
        $this->assertNull(session('coupon'));
    }
}
