<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Regular user
        $this->user = User::factory()->create([
            'role' => 'user',
        ]);
    }

    /** @test */
    public function admin_can_view_orders_list()
    {
        // Create some orders
        $orders = Order::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.manage.orders'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.manage_orders');
        $response->assertViewHas('orders');
    }

    /** @test */
    public function admin_can_view_single_order()
    {
        $order = Order::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.order.show', $order->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.order_show');
        $response->assertViewHas('order');
    }

    /** @test */
    public function admin_can_update_order_status()
    {
        $order = Order::factory()->create([
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($this->admin)
                         ->put(route('admin.update.order.status', $order->id), [
                             'status' => 'Shipped'
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Order status updated successfully!');
        $this->assertEquals('Shipped', $order->fresh()->status);
    }

    /** @test */
    public function non_admin_cannot_access_admin_order_routes()
    {
        $order = Order::factory()->create();

        $routes = [
            ['GET', route('admin.manage.orders')],
            ['GET', route('admin.order.show', $order->id)],
            ['PUT', route('admin.update.order.status', $order->id), ['status' => 'Shipped']],
        ];

        foreach ($routes as $route) {
            [$method, $url, $data] = array_pad($route, 3, []); // ensures $data is always set
            $response = $this->actingAs($this->user)->json($method, $url, $data);
            $response->assertStatus(302); // redirected by admin middleware
        }
    }
}
