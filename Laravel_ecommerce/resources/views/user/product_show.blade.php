@extends('layouts.app')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

@section('content')
<div class="container py-5">

    <div class="row g-4">
       
        <div class="col-md-6">
            @if($product->images->count())
                <div class="mb-3">
                    <img id="mainImage" 
                         src="{{ asset('storage/'.$product->images->first()->path) }}" 
                         class="img-fluid rounded shadow-sm border" 
                         style="height:400px; object-fit:cover;"
                         alt="Main Product Image">
                </div>

                <div class="d-flex flex-wrap gap-2">
                    @foreach($product->images as $index => $img)
                        <img src="{{ asset('storage/'.$img->path) }}"
                             class="img-thumbnail thumb-img"
                             style="width:90px; height:90px; object-fit:cover; cursor:pointer; transition:0.3s;"
                             onclick="changeImage('{{ asset('storage/'.$img->path) }}')"
                             data-bs-toggle="modal" data-bs-target="#imageModal{{ $index }}"
                             alt="Thumbnail">
                        
                        <!-- Modal for zoom view -->
                        <div class="modal fade" id="imageModal{{ $index }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content bg-dark border-0">
                                    <div class="modal-body text-center p-0">
                                        <img src="{{ asset('storage/'.$img->path) }}" class="img-fluid rounded">
                                    </div>
                                    <div class="modal-footer border-0 justify-content-center">
                                        <button class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">No images available for this product.</p>
            @endif
        </div>

        <!-- RIGHT: Product Details -->
        <div class="col-md-6">
            <h3 class="fw-bold">{{ $product->title }}</h3>
            <p class="text-muted">{{ $product->description }}</p>

            {{-- Price Section --}}
            @if ($product->discount > 0)
                <h4 class="text-danger fw-bold">
                    ₹{{ $product->price - ($product->price * $product->discount / 100) }}
                </h4>
                <p>
                    <span class="text-muted text-decoration-line-through">₹{{ $product->price }}</span>
                    <span class="text-success ms-2">(-{{ $product->discount }}%)</span>
                </p>
            @else
                <h4 class="fw-bold">₹{{ $product->price }}</h4>
            @endif

            {{-- Stock Section --}}
            <div class="mt-3">
                @if($product->stock_qty > 0)
                    <p class="text-success fw-semibold mb-1">
                        In Stock: {{ $product->stock_qty }} available
                    </p>
                @else
                    <p class="text-danger fw-semibold mb-1">Out of Stock</p>
                @endif
            </div>

            {{-- Actions --}}
            <div class="mt-4">
                @if($product->stock_qty > 0)
                    <button class="btn btn-success me-2">
                        <i class="bi bi-cart"></i> Add to Cart
                    </button>
                @else
                    <button class="btn btn-secondary me-2" disabled>
                        <i class="bi bi-cart-x"></i> Out of Stock
                    </button>
                @endif
                <button class="btn btn-outline-danger">
                    <i class="bi bi-heart"></i> Wishlist
                </button>
            </div>
        </div>
    </div>
</div>


<script>
function changeImage(src) {
    document.getElementById('mainImage').src = src;
}
</script>


<style>
.thumb-img:hover {
    transform: scale(1.05);
    border: 2px solid #28a745;
}
</style>
@endsection
