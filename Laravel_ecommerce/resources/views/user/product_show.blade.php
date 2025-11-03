@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto py-10 px-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- LEFT: Product Images -->
        <div>
            @if($product->images->count())
                <div class="mb-4">
                    <img id="mainImage"
                        src="{{ asset('storage/'.$product->images->first()->path) }}"
                        class="w-full h-96 object-cover rounded-lg shadow-md border"
                        alt="Main Product Image">
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($product->images as $index => $img)
                        <img src="{{ asset('storage/'.$img->path) }}"
                            class="w-20 h-20 object-cover rounded cursor-pointer border hover:scale-105 transition"
                            onclick="changeImage('{{ asset('storage/'.$img->path) }}')"
                            data-bs-toggle="modal" data-bs-target="#imageModal{{ $index }}"
                            alt="Thumbnail">

                        <!-- Modal for zoom -->
                        <div class="modal fade" id="imageModal{{ $index }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content bg-black border-0">
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
                <p class="text-gray-500">No images available for this product.</p>
            @endif
        </div>

        <!-- RIGHT: Product Details -->
        <div class="flex flex-col justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">{{ $product->title }}</h2>
                <p class="text-gray-600 mb-4">{{ $product->description }}</p>

                <!-- 💰 Price -->
                @if ($product->discount > 0)
                    <h3 class="text-red-600 text-2xl font-semibold mb-1">
                        ₹{{ $product->price - ($product->price * $product->discount / 100) }}
                    </h3>
                    <p>
                        <span class="line-through text-gray-400">₹{{ $product->price }}</span>
                        <span class="text-green-600 ml-2">-{{ $product->discount }}%</span>
                    </p>
                @else
                    <h3 class="text-2xl font-semibold mb-3">₹{{ $product->price }}</h3>
                @endif

                <!-- 📦 Stock Info -->
                <div class="mt-4">
                    @if($product->stock_qty > 0)
                        <p class="text-green-600 font-medium">
                            In Stock: {{ $product->stock_qty }} available
                        </p>
                    @else
                        <p class="text-red-500 font-medium">Out of Stock</p>
                    @endif
                </div>
                <br>
                <br>
                <h5 style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">Variants:</h5>

@if($product->variants->count())
    <ul style="list-style-type: disc; padding-left: 20px; margin: 0;">
        @foreach($product->variants as $v)
            <li style="margin-bottom: 4px; color: #333; font-size: 14px;">
                {{ $v->size ?? '-' }} / {{ $v->color ?? '-' }}
            </li>
        @endforeach
    </ul>
@else
    <p style="color: #999; font-size: 14px; font-style: italic; margin: 0;">No variants.</p>
@endif
            </div>

            <!-- 🛍️ Buttons -->
            <div class="mt-8 flex items-center space-x-4">
                @if($product->stock_qty > 0)
                    <!-- ✅ Add to Cart (Session Based) -->
                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md font-medium transition">
                            🛒 Add to Cart
                        </button>
                    </form>
                @else
                    <button class="bg-gray-400 text-white px-5 py-2 rounded-md" disabled>
                        🛒 Out of Stock
                    </button>
                @endif

                <!-- 💖 Wishlist -->
                @auth
                    <form action="{{ route('wishlist.add', $product->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="border border-red-500 text-red-500 hover:bg-red-500 hover:text-white px-5 py-2 rounded-md font-medium transition">
                            💖 Add to Wishlist
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" 
                       class="border border-red-500 text-red-500 hover:bg-red-500 hover:text-white px-5 py-2 rounded-md font-medium transition">
                        💖 Wishlist
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<script>
function changeImage(src) {
    document.getElementById('mainImage').src = src;
}
</script>
@endsection
