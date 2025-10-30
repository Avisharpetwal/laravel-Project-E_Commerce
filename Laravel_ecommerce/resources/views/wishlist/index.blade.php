
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">My Wishlist</h2>

    @if($wishlist->count())
        <div class="row g-3">
            @foreach($wishlist as $item)
                @php $product = $item->product; @endphp
                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        {{-- Product Image --}}
                        <a href="{{ route('products.show', $product->id) }}">
                            @if($product->images->count())
                                <img src="{{ asset('storage/'.$product->images->first()->path) }}" 
                                     class="card-img-top" 
                                     style="height:200px; object-fit:cover;" 
                                     alt="{{ $product->title }}">
                            @else
                                <img src="https://via.placeholder.com/300x200" 
                                     class="card-img-top" 
                                     style="height:200px; object-fit:cover;" 
                                     alt="No Image">
                            @endif
                        </a>

                        {{-- Product Info --}}
                        <div class="card-body text-center">
                            <a href="{{ route('products.show', $product->id) }}" 
                               class="text-decoration-none text-dark fw-semibold">
                                <h6 class="mb-2">{{ $product->title }}</h6>
                            </a>

                            {{-- Remove Button --}}
                            <form action="{{ route('wishlist.remove', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger mt-2">
                                    <i class="bi bi-trash"></i> Remove
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p>No products in wishlist!</p>
    @endif
</div>
@endsection
