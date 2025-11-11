<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\TestDox('Admin Controller Feature Tests')]
class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_dashboard_is_accessible(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHasAll([
            'users', 'admins', 'totalOrders', 'totalSales',
            'topProducts', 'notifications', 'unreadCount',
            'weekSales', 'monthSales', 'todaySales'
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_users_page(): void
    {
        User::factory()->count(3)->create(['role' => 'user']);

        $response = $this->actingAs($this->admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertViewIs('admin.users');
        $response->assertViewHas('users');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_toggle_block_status_of_user(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_blocked' => false]);

        $response = $this->actingAs($this->admin)
                         ->post("/admin/users/{$user->id}/toggle");

        $response->assertRedirect();
        $this->assertTrue((bool)  $user->fresh()->is_blocked);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_manage_orders(): void
    {
        Order::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)->get('/admin/manage-orders');

        $response->assertStatus(200);
        $response->assertViewIs('admin.manage_orders');
        $response->assertViewHas('orders');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_update_order_status(): void
    {
        $order = Order::factory()->create(['status' => 'Pending']);

        $response = $this->actingAs($this->admin)
                         ->put("/admin/manage-orders/{$order->id}/update-status", [
                             'status' => 'Delivered'
                         ]);

        $response->assertRedirect();
        $this->assertEquals('Delivered', $order->fresh()->status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_order_details(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'subcategory_id' => $category->id
        ]);
        $order = Order::factory()->create(['user_id' => $user->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response = $this->actingAs($this->admin)
                 ->get(route('admin.order.show', $order->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.order_show');
        $response->assertViewHas('order');
    }
}
