<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    //All Products
    public function index()
    {
        $products = Product::with('category','subcategory','images','variants')->latest()->paginate(12);
        return view('admin.products.index', compact('products'));
    }

    //Add A Product
    public function create()
    {
        
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return view('admin.products.create', compact('categories'));
    }
  
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|string|max:255',
            'description'=>'nullable|string',
            'price'=>'required|numeric|min:0',
            'discount'=>'nullable|numeric|min:0',
            'sku'=>'nullable|string|unique:products,sku',
            'stock_qty'=>'required|integer|min:0',
            'category_id'=>'nullable|exists:categories,id',
            'subcategory_id'=>'nullable|exists:categories,id',
            'images.*'=>'nullable|image|max:5120', // 5MB
            'sizes'=>'nullable|string',
            'colors'=>'nullable|string',
        ]);

        $product = Product::create([
            'title'=>$request->title,
            'description'=>$request->description,
            'price'=>$request->price,
            'discount'=>$request->discount ?? 0,
            'sku'=>$request->sku ?? 'SKU-'.Str::upper(Str::random(6)),
            'stock_qty'=>$request->stock_qty,
            'category_id'=>$request->category_id,
            'subcategory_id'=>$request->subcategory_id,
        ]);

        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $idx => $image) {
                $path = $image->store('products','public');
                $product->images()->create([
                    'path'=>$path,
                    'is_featured'=>($idx==0)
                ]);
            }
        }

       
        $sizes = array_filter(array_map('trim', explode(',', $request->sizes ?? '')));
        $colors = array_filter(array_map('trim', explode(',', $request->colors ?? '')));

        if ($sizes || $colors) {
            $combinations = [];

            if ($sizes && $colors) {
                foreach ($sizes as $s) {
                    foreach ($colors as $c) {
                        $combinations[] = ['size'=>$s,'color'=>$c];
                    }
                }
            } elseif ($sizes) {
                foreach ($sizes as $s) {
                    $combinations[]=['size'=>$s,'color'=>null];
                }
            } else {
                foreach ($colors as $c) {
                    $combinations[]=['size'=>null,'color'   =>$c];
                }
            }

            foreach ($combinations as $comb) {
                ProductVariant::create([
                    'product_id'=>$product->id,
                    'size'=>$comb['size'],
                    'color'=>$comb['color'],
                    'sku'=>'V-'.$product->id.'-'.Str::upper(Str::random(5)),
                    'stock_qty'=> $request->variant_stock ?? 0,
                    'price'=> $request->variant_price ?? $product->price,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success','Product created.');
    }

   
    public function show($id)
    {
    $product = \App\Models\Product::with('images', 'variants', 'category', 'subcategory')->findOrFail($id);

    
    if (auth()->check() && auth()->user()->role === 'admin') {
        return view('admin.products.show', compact('product'));
    }

    
    return view('user.product_show', compact('product'));
    }



    public function edit(Product $product)
    {
    $categories = Category::whereNull('parent_id')->get();
    $subcategories = Category::whereNotNull('parent_id')->get();
    return view('admin.products.create', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title'=>'required|string|max:255',
            'description'=>'nullable|string',
            'price'=>'required|numeric|min:0',
            'discount'=>'nullable|numeric|min:0',
            'sku'=>"nullable|string|unique:products,sku,{$product->id}",
            'stock_qty'=>'required|integer|min:0',
            'category_id'=>'nullable|exists:categories,id',
            'subcategory_id'=>'nullable|exists:categories,id',
            'images.*'=>'nullable|image|max:5120',
            'sizes'=>'nullable|string',
            'colors'=>'nullable|string',
        ]);

        $product->update([
            'title'=>$request->title,
            'description'=>$request->description,
            'price'=>$request->price,
            'discount'=>$request->discount ?? 0,
            'sku'=>$request->sku,
            'stock_qty'=>$request->stock_qty,
            'category_id'=>$request->category_id,
            'subcategory_id'=>$request->subcategory_id,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products','public');
                $product->images()->create(['path'=>$path,'is_featured'=>false]);
            }
        }

        if ($request->filled('sizes') || $request->filled('colors')) {
            $product->variants()->delete();

            $sizes = array_filter(array_map('trim', explode(',', $request->sizes ?? '')));
            $colors = array_filter(array_map('trim', explode(',', $request->colors ?? '')));

            $combinations = [];
            if ($sizes || $colors) {
                if ($sizes && $colors) {
                    foreach ($sizes as $s) {
                        foreach ($colors as $c) {
                            $combinations[] = ['size'=>$s,'color'=>$c];
                        }
                    }
                } elseif ($sizes) {
                    foreach ($sizes as $s) {
                        $combinations[]=['size'=>$s,'color'=>null];
                    }
                } else {
                    foreach ($colors as $c) {
                        $combinations[]=['size'=>null,'color'=>$c];
                    }
                }

                foreach ($combinations as $comb) {
                    ProductVariant::create([
                        'product_id'=>$product->id,
                        'size'=>$comb['size'],
                        'color'=>$comb['color'],
                        'sku'=>'V-'.$product->id.'-'.Str::upper(Str::random(5)),
                        'stock_qty'=> $request->variant_stock ?? 0,
                        'price'=> $request->variant_price ?? $product->price,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success','Product updated.');
    }

 public function Userdashboard(Request $request)
{
    $query = Product::with(['images', 'category', 'subcategory']);

    $categoryIds = [];
    if ($request->filled('category')) {
        $categoryId = $request->category;

        $categoryIds = Category::where('id', $categoryId)
            ->orWhere('parent_id', $categoryId)
            ->pluck('id');

        $query->where(function ($sub) use ($categoryIds) {
            $sub->whereIn('category_id', $categoryIds)
                ->orWhereIn('subcategory_id', $categoryIds);
        });
    }

    if ($request->filled('min_price') && $request->filled('max_price')) {
        $min = $request->min_price;
        $max = $request->max_price;

        $query->whereBetween('price', [$min, $max]);
    }

    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }

    $products = $query->latest()->paginate(9);

    $categories = Category::with('children')->whereNull('parent_id')->get();

    return view('user.dashboard', compact('products', 'categories'));
}



    
}
