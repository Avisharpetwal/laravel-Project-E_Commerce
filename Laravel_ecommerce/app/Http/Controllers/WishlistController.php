<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = Wishlist::with('product.images')
            ->where('user_id', Auth::id())
            ->get();

        return view('wishlist.index', compact('wishlist'));
    }

    public function add($id)
    {
        $product = Product::findOrFail($id);

        Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
        ]);

        return back()->with('success', 'Product added to wishlist!');
    }

    public function remove($id)
    {
        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $id)
            ->delete();

        return back()->with('success', 'Product removed from wishlist!');
    }
}
