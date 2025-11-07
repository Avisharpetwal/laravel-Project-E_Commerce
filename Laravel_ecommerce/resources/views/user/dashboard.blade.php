

@extends('layouts.app')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

@section('content')
<div class="container py-4">
    <div class="row">
        {{-- 🗂 Category Sidebar + Price Filter --}}
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                    <span>Categories</span>
                    <a href="{{ route('products.list') }}" class="text-white text-decoration-none small">Reset</a>
                </div>

                <div class="card-body">
                    {{-- Category List --}}
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item">
                            <a href="{{ route('products.list') }}" 
                               class="text-decoration-none fw-semibold {{ request('category') ? 'text-dark' : 'text-primary' }}">
                                All Categories
                            </a>
                        </li>

                        @foreach ($categories as $cat)
                            <li class="list-group-item">
                                <a href="{{ route('products.list', ['category' => $cat->id]) }}" 
                                   class="text-decoration-none fw-semibold {{ request('category') == $cat->id ? 'text-primary' : 'text-dark' }}">
                                    <i class="bi bi-folder-fill me-1 text-warning"></i> {{ $cat->name }}
                                </a>

                                @if ($cat->children->count())
                                    <ul class="list-unstyled ps-3 mt-2">
                                        @foreach ($cat->children as $child)
                                            <li>
                                                <a href="{{ route('products.list', ['category' => $child->id]) }}" 
                                                   class="text-decoration-none small {{ request('category') == $child->id ? 'text-primary fw-bold' : 'text-secondary' }}">
                                                    <i class="bi bi-chevron-right small"></i> {{ $child->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    {{--  Price Filter --}}
                    <form action="{{ route('products.list') }}" method="GET">
                        @if (request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif

                        <div class="mb-2">
                            <label class="form-label fw-semibold">Min Price (₹)</label>
                            <input type="number" name="min_price" class="form-control form-control-sm" 
                                   value="{{ request('min_price') }}" placeholder="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Max Price (₹)</label>
                            <input type="number" name="max_price" class="form-control form-control-sm" 
                                   value="{{ request('max_price') }}" placeholder="10000">
                        </div>
                        <button class="btn btn-sm btn-primary w-100"><i class="bi bi-funnel"></i> Apply</button>
                    </form>
                </div>
            </div>
        </div>

        {{--  Product Grid --}}
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">
                    @if (request('category'))
                        {{ optional(\App\Models\Category::find(request('category')))->name ?? 'Products' }}
                    @else
                        Latest Products
                    @endif
                </h4>

                {{--  Search --}}
                <form method="GET" class="d-flex align-items-center">
                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Search...">
                    <button class="btn btn-outline-primary btn-sm"><i class="bi bi-search"></i></button>
                </form>
            </div>

            <div class="row">
                @forelse ($products as $product)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="position-relative">
                                @if ($product->images->first())
                                    <img src="{{ asset('storage/' . $product->images->first()->path) }}" 
                                         class="card-img-top rounded-top" style="height:220px; object-fit:cover;">
                                @else
                                    <img src="https://via.placeholder.com/300x200" 
                                         class="card-img-top" style="height:220px; object-fit:cover;">
                                @endif

                                @if ($product->discount > 0)
                                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                        -{{ $product->discount }}%
                                    </span>
                                @endif
                            </div>

                            <div class="card-body text-center">
                                <h6 class="card-title fw-bold">{{ Str::limit($product->title, 25) }}</h6>

                                @if ($product->discount > 0)
                                    <p class="text-danger fw-bold mb-0">
                                        ₹{{ $product->price - ($product->price * $product->discount / 100) }}
                                    </p>
                                    <small class="text-muted text-decoration-line-through">
                                        ₹{{ $product->price }}
                                    </small>
                                @else
                                    <p class="fw-bold">₹{{ $product->price }}</p>
                                @endif
                            </div>

                            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                    View
                                </a>

                                <div>
                                    @auth
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-heart"></i></button>
                                        <button class="btn btn-sm btn-outline-success"><i class="bi bi-cart"></i></button>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-heart"></i></a>
                                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-success"><i class="bi bi-cart"></i></a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">No products found.</p>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

