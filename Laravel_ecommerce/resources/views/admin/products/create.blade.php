@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h3>{{ isset($product) ? 'Edit Product' : 'Add Product' }}</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ isset($product) ? route('admin.products.update',$product->id) : route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($product))
            @method('PUT')
        @endif

        <div class="mb-3">
            <label class="form-label fw-bold">Title</label>
            <input name="title" value="{{ old('title', $product->title ?? '') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Description</label>
            <textarea name="description" rows="4" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold">Original Price (₹)</label>
                <input name="price" value="{{ old('price', $product->price ?? '') }}" type="number" step="0.01" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Discount (%)</label>
                <input name="discount" value="{{ old('discount', $product->discount ?? 0) }}" type="number" min="0" max="100" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">SKU</label>
                <input name="sku" value="{{ old('sku', $product->sku ?? '') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Stock Quantity</label>
                <input name="stock_qty" value="{{ old('stock_qty', $product->stock_qty ?? 0) }}" type="number" class="form-control" required>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Category</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (old('category_id', $product->category_id ?? '') == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @foreach($cat->children as $sub)
                            <option value="{{ $sub->id }}" {{ (old('category_id', $product->category_id ?? '') == $sub->id || old('subcategory_id', $product->subcategory_id ?? '') == $sub->id) ? 'selected' : '' }}>
                                &nbsp;&nbsp;— {{ $sub->name }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Subcategory (optional)</label>
                <select name="subcategory_id" class="form-select">
                    <option value="">-- Select Subcategory --</option>
                    @foreach($categories as $cat)
                        @foreach($cat->children as $sub)
                            <option value="{{ $sub->id }}" {{ (old('subcategory_id', $product->subcategory_id ?? '') == $sub->id) ? 'selected' : '' }}>
                                {{ $cat->name }} / {{ $sub->name }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3 mt-3">
            <label class="form-label fw-bold">Product Images (Multiple)</label>
            <input type="file" name="images[]" accept="image/*" multiple class="form-control">
            @if(isset($product) && $product->images->count())
                <div class="mt-2 d-flex gap-2 flex-wrap">
                    @foreach($product->images as $img)
                        <div style="width:80px; height:80px; overflow:hidden; border-radius:6px;">
                            <img src="{{ asset('storage/'.$img->path) }}" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <hr>

        <h5 class="fw-bold">Product Variants</h5>
        <p class="text-muted small mb-2">Enter sizes and colors as comma separated lists (e.g. S,M,L)</p>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Sizes</label>
                <input name="sizes" value="{{ old('sizes') }}" class="form-control" placeholder="S,M,L">
            </div>
            <div class="col-md-6">
                <label class="form-label">Colors</label>
                <input name="colors" value="{{ old('colors') }}" class="form-control" placeholder="Red,Blue,Green">
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary">{{ isset($product) ? 'Update Product' : 'Create Product' }}</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
