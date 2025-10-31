<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    // List all orders (with search & filter)
    public function index(Request $request)
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

    // Show single order details
    public function show(Order $order)
    {
        $order->load('user', 'orderItems.product.images');
        return view('admin.order_show', compact('order'));
    }

    // Update order status
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:Pending,Shipped,Delivered,Cancelled']);
        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }
}
