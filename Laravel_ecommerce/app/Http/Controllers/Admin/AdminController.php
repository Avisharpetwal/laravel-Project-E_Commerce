<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::count();
        $admins = User::where('role', 'admin')->count();
        return view('admin.dashboard', compact('users', 'admins'));
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

        return redirect()->back();
    }


    
}
