<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    public function setUp(): void
    {
        parent::setUp();

        // Admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_all_orders()
    {
        Order::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.manage.orders'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.manage_orders');
        $response->assertViewHas('orders');
    }

   #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_single_order()
    {
        $order = Order::factory()->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.order.show', $order->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.order_show');
        $response->assertViewHas('order', $order);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_update_order_status()
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
}
