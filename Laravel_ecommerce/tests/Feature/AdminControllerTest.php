<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_dashboard_is_accessible(): void
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

    public function test_admin_can_view_users_page(): void
    {
        User::factory()->count(3)->create(['role' => 'user']);

        $response = $this->actingAs($this->admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertViewIs('admin.users');
        $response->assertViewHas('users');
    }

    public function test_admin_can_toggle_block_status_of_user(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_blocked' => false]);

        $response = $this->actingAs($this->admin)
                         ->post(route('admin.users.toggle', $user->id));

        $response->assertRedirect();
        $this->assertTrue((bool) $user->fresh()->is_blocked); 
    }

    public function test_admin_can_manage_orders(): void
    {
        Order::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.manage.orders'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.manage_orders');
        $response->assertViewHas('orders');
    }

    public function test_admin_can_update_order_status(): void
    {
        $order = Order::factory()->create(['status' => 'Pending']);

        $response = $this->actingAs($this->admin)
                         ->put(route('admin.update.order.status', $order->id), [
                             'status' => 'Delivered'
                         ]);

        $response->assertRedirect();
        $this->assertEquals('Delivered', $order->fresh()->status);
    }

    public function test_admin_can_view_order_details(): void
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

    public function test_non_admin_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $order = Order::factory()->create(['user_id' => $user->id]);

        $routes = [
            ['GET', route('admin.manage.orders'), null],
            ['PUT', route('admin.update.order.status', $order->id), ['status' => 'Delivered']],
            ['GET', route('admin.order.show', $order->id), null]
        ];

        foreach ($routes as [$method, $url, $data]) {
            $response = $this->actingAs($user)->json($method, $url, $data ?? []);
            $response->assertStatus(302); 
        }
    }
}
