<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

 
use App\Models\Order;
use App\Models\OrderItem;
use DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    // public function dashboard()
    // {
    //     $users = User::count();
    //     $admins = User::where('role', 'admin')->count();
    //     return view('admin.dashboard', compact('users', 'admins'));
    // }

    // public function users()
    // {
    //     $users = User::where('role', 'user')->get();
    //     return view('admin.users', compact('users'));
    // }


  public function dashboard()
    {
        //  Total Users & Admins
        $users = User::where('role', 'user')->count();
        $admins = User::where('role', 'admin')->count();

        //  Orders & Sales
        $totalOrders = Order::count();
        $totalSales = Order::where('status', 'Delivered')->sum('total');

        //Today Sales
        $todaySales = Order::where('status', 'Delivered')
        ->whereDate('updated_at', Carbon::today())
        ->sum('total');
        
        //Weekly Sales
        $weekStart = Carbon::now()->startOfWeek();
        $weekSales = Order::where('status', 'Delivered')
        ->whereBetween('created_at', [$weekStart, Carbon::now()])
        ->sum('total');


        //Monthly Sales
        $monthStart = Carbon::now()->startOfMonth();
        $monthSales = Order::where('status', 'Delivered')
        ->whereBetween('created_at', [$monthStart, Carbon::now()])
        ->sum('total');


        // ✅ Top 5 Selling Products
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with('product')
            ->get();
        
        // Notificatio For Admin
        $admin = auth()->user();
        $notifications = $admin->notifications()->latest()->take(5)->get();
        $unreadCount = $admin->unreadNotifications->count();

        return view('admin.dashboard', compact(
            'users',
            'admins',
            'totalOrders',
            'totalSales',
            'topProducts',
            'notifications',
            'unreadCount',
            'weekSales',
            'monthSales',
            'todaySales'
        ));
    }

    public function users()
    {
        $users = User::where('role', 'user')->get();
        return view('admin.users', compact('users'));
    }

    //Block Or UnBlock User
    public function toggleBlock(User $user)
    {
        $user->is_blocked = !$user->is_blocked;
        $user->save();

        return redirect()->back()->with('success', 'User status updated successfully.');
    }


    // public function toggleBlock(User $user)
    // {
    //     $user->is_blocked = !$user->is_blocked;
    //     $user->save();

    //     return redirect()->back();
    // }

    //Manage Orders
    public function manageOrders(Request $request)
    {
    $query = Order::with('user');

    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where('id', $search)
              ->orWhereHas('user', function ($q) use ($search) {
                  $q->where('name', 'like', "%$search%");
              });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->input('status'));
    }

    $orders = $query->orderByDesc('id')->get();

    return view('admin.manage_orders', compact('orders'));
    }

    //Update The Order Status
    public function updateOrderStatus(Request $request, $id)
    {
    $order = \App\Models\Order::findOrFail($id);
    $order->status = $request->status;
    $order->save();

    return back()->with('success', 'Order status updated successfully!');
    }

    //Show Orders
    public function show(Order $order)
    {
        $order->load('user', 'orderItems.product.images');
        return view('admin.order_show', compact('order'));
    }

    
    
}
