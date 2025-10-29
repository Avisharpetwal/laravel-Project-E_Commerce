@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>All Products</h3>
        <a href="{{ route('admin.products.create') }}" class="btn btn-dark">+ Add Product</a>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        @foreach($products as $p)
        @php
            $discounted = $p->price - ($p->price * ($p->discount / 100));
        @endphp
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                @if($p->images->first())
                    <img src="{{ asset('storage/'.$p->images->first()->path) }}" class="card-img-top" style="height:200px; object-fit:cover">
                @endif
                <div class="card-body text-center">
                    <h5 class="card-title">{{ $p->title }}</h5>
                    <!-- <p class="card-text text-muted small">{{ Str::limit($p->description,80) }}</p> -->

                    <p>
                        <span class="text-decoration-line-through text-muted">₹{{ $p->price }}</span>
                        <span class="fw-bold text-success ms-2">₹{{ number_format($discounted,2) }}</span>
                        <small class="text-danger">(-{{ $p->discount }}%)</small>
                    </p>
                    <!-- <p class="mb-1"><strong>Stock:</strong> {{ $p->stock_qty }}</p> -->
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('admin.products.show', $p->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                    <div>
                        <a href="{{ route('admin.products.edit', $p->id) }}" class="btn btn-sm btn-success">Edit</a>
                        <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete product?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection
