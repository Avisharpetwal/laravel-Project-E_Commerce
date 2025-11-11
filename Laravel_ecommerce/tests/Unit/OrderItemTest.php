<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderItemTest extends TestCase
{
    use RefreshDatabase;

    
    public function test_it_has_fillable_attributes()
    {
        $orderItem = new OrderItem();

        $this->assertEquals([
            'order_id', 'product_id', 'quantity', 'price'
        ], $orderItem->getFillable());
    }

    
    public function test_it_belongs_to_an_order()
    {
        $order = Order::factory()->create();
        $item = OrderItem::factory()->create(['order_id' => $order->id]);

        $this->assertInstanceOf(Order::class, $item->order);
        $this->assertEquals($order->id, $item->order->id);
    }

    
    public function test_it_belongs_to_a_product()
    {
        $product = Product::factory()->create();
        $item = OrderItem::factory()->create(['product_id' => $product->id]);

        $this->assertInstanceOf(Product::class, $item->product);
        $this->assertEquals($product->id, $item->product->id);
    }
}
