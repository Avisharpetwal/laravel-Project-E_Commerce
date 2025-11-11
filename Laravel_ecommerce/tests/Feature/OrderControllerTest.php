<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Test user
        $this->user = User::factory()->create();

        // Admin user
        $this->admin = User::factory()->create(['role' => 'admin']);

        // Test product
        $this->product = Product::factory()->create([
            'title' => 'Test Product',
            'price' => 100,
            'stock_qty' => 10,
        ]);
    }

    // ----------------------- User Tests -----------------------

    public function test_user_can_view_checkout_form()
    {
        session(['cart' => [
            $this->product->id => [
                'name' => $this->product->title,
                'price' => $this->product->price,
                'quantity' => 2,
                'image' => null,
            ]
        ]]);

        $response = $this->actingAs($this->user)->get(route('checkout.form'));

        $response->assertStatus(200);
        $response->assertViewIs('checkout');
        $response->assertViewHasAll(['cart', 'subtotal', 'tax', 'total']);
    }

    public function test_user_can_place_order()
    {
        session(['cart' => [
            $this->product->id => [
                'name' => $this->product->title,
                'price' => $this->product->price,
                'quantity' => 2,
                'image' => null,
            ]
        ]]);

        $response = $this->actingAs($this->user)->post(route('checkout.place'), [
            'name' => 'Test User',
            'phone' => '1234567890',
            'address' => 'Test Address',
            'payment_method' => 'COD',
        ]);

        $response->assertRedirect(route('orders.success'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total' => 220, // 100*2 + 10% tax
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        // Stock should reduce
        $this->assertEquals(8, $this->product->fresh()->stock_qty);
    }

    public function test_user_can_view_their_orders()
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('orders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('orders.index');
        $response->assertViewHas('orders', function ($orders) use ($order) {
            return $orders->contains($order);
        });
    }

    public function test_user_can_cancel_pending_order()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($this->user)->patch(route('orders.cancel', $order));

        $response->assertRedirect(route('orders.show', $order->id));
        $response->assertSessionHas('success', 'Order has been cancelled successfully.');
        $this->assertEquals('Cancelled', $order->fresh()->status);
    }

    public function test_user_cannot_cancel_non_pending_order()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'Shipped',
        ]);

        $response = $this->actingAs($this->user)->patch(route('orders.cancel', $order));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Only pending orders can be cancelled.');
        $this->assertEquals('Shipped', $order->fresh()->status);
    }

    // ----------------------- Admin Tests -----------------------

    public function test_admin_can_view_all_orders()
    {
        Order::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.manage.orders'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.manage_orders');
        $response->assertViewHas('orders');
    }

    public function test_admin_can_view_single_order()
    {
        $order = Order::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.order.show', $order->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.order_show');
        $response->assertViewHas('order', $order);
    }

    public function test_admin_can_update_order_status()
    {
        $order = Order::factory()->create(['status' => 'Pending']);

        $response = $this->actingAs($this->admin)
                         ->put(route('admin.update.order.status', $order->id), [
                             'status' => 'Shipped',
                         ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Shipped',
        ]);
    }

    // public function test_admin_can_confirm_order()
    // {
    //     $order = Order::factory()->create(['status' => 'Pending']);

    //     $response = $this->actingAs($this->admin)
    //                      ->patch(route('admin.orders.confirm', $order));

    //     $response->assertRedirect();
    //     $response->assertSessionHas('success');
    //     $this->assertEquals('Confirmed', $order->fresh()->status);
    // }
}
