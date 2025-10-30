<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + $tax;

        return view('cart.index', compact('cart', 'subtotal', 'tax', 'total'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $discounted = $product->discount > 0 
                ? $product->price - ($product->price * $product->discount / 100)
                : $product->price;

            $cart[$id] = [
                'name' => $product->title,
                'price' => $discounted,
                'quantity' => 1,
                'image' => $product->images->first()->path ?? null,
            ];
        }

        session()->put('cart', $cart);
        return back()->with('success', 'Product added to cart!');
    }

   public function update(Request $request, $id)
{
    $cart = session()->get('cart', []);
    $product = Product::findOrFail($id);

    if (isset($cart[$id])) {
        $newQty = (int) $request->quantity;

        // Validate quantity
        if ($newQty < 1) {
            return back()->with('error', 'Quantity must be at least 1.');
        }

        // Check stock limit
        if ($newQty > $product->stock_qty) {
            $cart[$id]['quantity'] = $product->stock_qty;
            session()->put('cart', $cart);
            return back()->with('error', "Only {$product->stock_qty} items left in stock.");
        }

        // Update quantity if within range
        $cart[$id]['quantity'] = $newQty;
        session()->put('cart', $cart);

        return back()->with('success', 'Cart updated successfully!');
    }

    return back()->with('error', 'Product not found in cart.');
}

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return back()->with('success', 'Product removed from cart!');
    }
}
