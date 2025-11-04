<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderConfirmedNotification;

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

        $discount = 0;
    if (session('coupon')) {
        $discount = session('coupon')['discount_amount'];
        $total -= $discount;
    }

        return view('checkout', compact('cart', 'subtotal', 'tax', 'total','discount'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:10',
            'address' => 'required|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string'
        ],[
        'name.required' => 'This field is required.',
        'phone.required' => 'This field is required.',
        'phone.digits' => 'Phone number must be 10 digits.',
        'address.required' => 'This field is required.',
        'payment_method.required' => 'This field is required.',
    ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $tax = $subtotal * 0.1;
       

    $discount = 0;
    $coupon_code = null;
    if (session('coupon')) {
        $coupon = session('coupon');
        if ($subtotal >= $coupon['minimum_value']) {
        $discount = session('coupon')['discount_amount'];
        $coupon_code = session('coupon')['code'];
        
    }
}
 $total = $subtotal + $tax - $discount;
        // 🔹 Create Order
        $order = Order::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'total' => $total,
            'discount_amount' => $discount,         
            'coupon_code' => $coupon_code,
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


    $admins = User::where('role', 'admin')->get();
    foreach($admins as $admin){
    $admin->notify(new NewOrderNotification($order));
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
    public function confirmOrder(Order $order)
{
    // Only Pending order ->Confirm
    if ($order->status !== 'Pending') {
        return redirect()->back()->with('error', 'Only pending orders can be confirmed.');
    }

    // Order To Confirm
    $order->update(['status' => 'Confirmed']);

    //  notification To User
    $order->user->notify(new OrderConfirmedNotification($order));

    return redirect()->back()->with('success', 'Order confirmed successfully! User has been notified.');
}



}
