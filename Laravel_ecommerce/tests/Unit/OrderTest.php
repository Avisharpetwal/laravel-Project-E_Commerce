<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    
    public function test_it_has_fillable_attributes()
    {
        $order = new Order();

        $this->assertEquals([
            'user_id', 'name', 'phone', 'address', 'payment_method', 'total', 'status', 'notes'
        ], $order->getFillable());
    }

    
    public function test_it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $order->user);
        $this->assertEquals($user->id, $order->user->id);
    }

   
    public function test_it_has_many_order_items()
    {
        $order = Order::factory()->create();
        $items = OrderItem::factory()->count(3)->create(['order_id' => $order->id]);

        $this->assertInstanceOf(OrderItem::class, $order->items->first());
        $this->assertCount(3, $order->items);
    }

    
    public function test_order_items_and_items_relations_are_same()
    {
        $order = Order::factory()->create();
        OrderItem::factory()->create(['order_id' => $order->id]);

        $this->assertEquals($order->items->count(), $order->orderItems->count());
    }

    
    public function test_it_returns_orders_index_view_from_index_method()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $order = Order::factory()->create(['user_id' => $user->id]);

        $orderModel = new Order();
        $response = $orderModel->index();

        $this->assertEquals('orders.index', $response->name());
        $this->assertArrayHasKey('orders', $response->getData());
    }
}
