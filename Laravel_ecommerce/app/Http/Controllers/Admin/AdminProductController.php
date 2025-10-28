<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class AdminProductController extends Controller
{
    /**
     * Show Add Product Form
     */
    public function create()
    {
        $categories = Category::with('children')->get();
        return view('admin.product.add_product', compact('categories'));
    }

    /**
     * Store Product Data
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'sku' => 'required|string|unique:products,sku',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'variants' => 'nullable|array',
        ]);

        $product = new Product();
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->discount = $request->discount;
        $product->sku = $request->sku;
        $product->stock = $request->stock;
        $product->category_id = $request->category_id;
        $product->subcategory_id = $request->subcategory_id;
        $product->variants = json_encode($request->variants ?? []);
        $product->save();

        // Store images (Laravel File Storage)
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = $path;
            }
            $product->images = json_encode($images);
            $product->save();
        }

        return redirect()->route('admin.products.create')->with('success', 'Product added successfully!');
    }
    
}
