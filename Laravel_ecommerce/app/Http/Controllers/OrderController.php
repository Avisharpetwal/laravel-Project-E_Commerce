<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderController extends Controller
{
    public function checkoutForm()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $tax = $subtotal * 0.1;
        $total = $subtotal + $tax;

        return view('checkout', compact('cart', 'subtotal', 'tax', 'total'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $tax = $subtotal * 0.1;
        $total = $subtotal + $tax;

        // 🔹 Create Order
        $order = Order::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'total' => $total,
        ]);

        // 🔹 Add items + reduce stock
        foreach ($cart as $id => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            $product = Product::find($id);
            if ($product) {
                $product->stock_qty = max(0, $product->stock_qty - $item['quantity']);
                $product->save();
            }
        }

        // 🔹 Clear cart
        session()->forget('cart');

        return redirect()->route('orders.success')->with('success', 'Order placed successfully!');
    }

    public function orderSuccess()
    {
        return view('orders.success');
    }

    public function myOrders()
    {
        $orders = Order::where('user_id', auth()->id())->latest()->get();
        return view('orders.index', compact('orders'));
    }

    public function index()
{
    $orders = auth()->user()->orders()->with('orderItems.product.images')->latest()->get();
    return view('orders.index', compact('orders'));
}

public function show($id)
{
    $order = auth()->user()->orders()
        ->with('orderItems.product.images')
        ->findOrFail($id);

    return view('orders.show', compact('order'));
}


}
