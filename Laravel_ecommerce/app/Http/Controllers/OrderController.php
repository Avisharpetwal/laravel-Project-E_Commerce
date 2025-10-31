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


public function cancel(Order $order)
{
    // Sirf apne order cancel kar sake
    if ($order->user_id !== auth()->id()) {
        abort(403, 'Unauthorized action.');
    }

    // Sirf pending cancel ho sakta hai
    if ($order->status !== 'Pending') {
        return redirect()->back()->with('error', 'Only pending orders can be cancelled.');
    }

    $order->update(['status' => 'Cancelled']);

    return redirect()->route('orders.show', $order->id)
                     ->with('success', 'Order has been cancelled successfully.');
}

public function adminIndex(Request $request)
    {
        $query = Order::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('id', $search)
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->get();

        return view('admin.manage_orders', compact('orders'));
    }

    // Show order details for admin
    public function adminShow($id)
    {
        $order = Order::with('user', 'orderItems.product.images')->findOrFail($id);
        return view('admin.order_show', compact('order'));
    }

    // Update order status by admin
    public function adminUpdateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:Pending,Shipped,Delivered,Cancelled']);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }




}
