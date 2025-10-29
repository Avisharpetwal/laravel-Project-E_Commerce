<?php

// namespace App\Http\Controllers\User;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\Product;
// use App\Models\Category;

// class ProductBrowseController extends Controller
// {
//     // User Dashboard (Browse Products)
//     public function index(Request $request)
//     {
//         $query = Product::with('category', 'images');

//         // Search
//         if ($request->filled('search')) {
//             $query->where('title', 'like', '%' . $request->search . '%');
//         }

//         // Category Filter
//         if ($request->filled('category') && $request->category !== 'all') {
//             $query->where('category_id', $request->category);
//         }

//         // Price Range Filter
//         if ($request->filled('min_price') && $request->filled('max_price')) {
//             $query->whereBetween('price', [$request->min_price, $request->max_price]);
//         }

//         $products = $query->latest()->paginate(8);
//         $categories = Category::whereNull('parent_id')->get();

//         return view('user.dashboard', compact('products', 'categories'));
//     }

//     // Product Detail Page
//     public function show($id)
//     {
//         $product = Product::with('images', 'variants', 'category')->findOrFail($id);
//         return view('user.product_show', compact('product'));
//     }
// }
