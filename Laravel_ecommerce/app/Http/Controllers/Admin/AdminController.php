<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

//Yeh aaj new kiya hai 
use App\Models\Order;
use App\Models\OrderItem;
use DB;

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
        // ✅ Total Users & Admins
        $users = User::where('role', 'user')->count();
        $admins = User::where('role', 'admin')->count();

        // ✅ Orders & Sales
        $totalOrders = Order::count();
        $totalSales = Order::where('status', 'Delivered')->sum('total');

        // ✅ Top 5 Selling Products
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with('product')
            ->get();

        return view('admin.dashboard', compact(
            'users',
            'admins',
            'totalOrders',
            'totalSales',
            'topProducts'
        ));
    }

    public function users()
    {
        $users = User::where('role', 'user')->get();
        return view('admin.users', compact('users'));
    }

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

    

    
    
}
