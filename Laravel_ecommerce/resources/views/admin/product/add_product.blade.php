@extends('layouts.admin')

@section('content')
<div class="container my-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
            <h3>Add New Product</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Title --}}
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Title</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Description</label>
                    <textarea name="description" id="description" rows="4" class="form-control"></textarea>
                </div>

                {{-- Price and Discount --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="price" class="form-label fw-bold">Price (₹)</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="discount" class="form-label fw-bold">Discount (%)</label>
                        <input type="number" step="0.01" name="discount" id="discount" class="form-control">
                    </div>
                </div>

                {{-- SKU and Stock --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="sku" class="form-label fw-bold">SKU</label>
                        <input type="text" name="sku" id="sku" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="stock_quantity" class="form-label fw-bold">Stock Quantity</label>
                        <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" required>
                    </div>
                </div>

                {{-- Category (with hierarchy) --}}
                <div class="mb-3">
                    <label for="category_id" class="form-label fw-bold">Select Category</label>
                    <select name="category_id" id="category_id" class="form-select">
                        <option value="">-- Select Category --</option>
                        @foreach ($categories as $category)
                            @include('admin.partials.category_option', ['category' => $category, 'prefix' => ''])
                        @endforeach
                    </select>
                </div>

                {{-- Variants --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="size" class="form-label fw-bold">Size</label>
                        <input type="text" name="size" id="size" class="form-control" placeholder="e.g. S, M, L">
                    </div>
                    <div class="col-md-6">
                        <label for="color" class="form-label fw-bold">Color</label>
                        <input type="text" name="color" id="color" class="form-control" placeholder="e.g. Red, Blue">
                    </div>
                </div>

                {{-- Multiple Images --}}
                <div class="mb-4">
                    <label for="images" class="form-label fw-bold">Upload Product Images</label>
                    <input type="file" name="images[]" id="images" multiple class="form-control">
                    <small class="text-muted">You can upload multiple images.</small>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success px-5 fw-bold">
                        <i class="bi bi-plus-circle me-1"></i> Add Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
