@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-10 px-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- LEFT: Product Images -->
         <div>
            @if($product->images->count())
                <div class="mb-4">
                    <div id="mainMedia">
                        @php $first = $product->images->first(); @endphp
                        @if($first->type === 'video')
                            <video src="{{ asset('storage/'.$first->path) }}" class="w-full h-96 object-cover rounded-lg shadow-md border" controls></video>
                        @else
                            <img src="{{ asset('storage/'.$first->path) }}" class="w-full h-96 object-cover rounded-lg shadow-md border" alt="Main Product Image">
                        @endif
                    </div>
                </div>

                
                <div class="flex flex-wrap gap-2">
                    @foreach($product->images as $index => $file)
                        <div class="w-20 h-20 border rounded overflow-hidden cursor-pointer hover:scale-105 transition" onclick="changeMedia('{{ asset('storage/'.$file->path) }}', '{{ $file->type }}')">
                            @if($file->type === 'video')
                                <video src="{{ asset('storage/'.$file->path) }}" class="w-full h-full object-cover" muted></video>
                            @else
                                <img src="{{ asset('storage/'.$file->path) }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No images or videos available for this product.</p>
            @endif
        </div>

        <!-- RIGHT: Product Details -->
        <div class="flex flex-col justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">{{ $product->title }}</h2>
                <p class="text-gray-600 mb-4">{{ $product->description }}</p>

                <!-- ⭐ Average Rating -->
                <div class="mb-3">
                    @php
                        $avgRating = round($product->reviews->avg('rating'), 1);
                    @endphp
                    <div class="flex items-center">
                        @for($i=1; $i<=5; $i++)
                            <i class="bi {{ $i <= $avgRating ? 'bi-star-fill text-yellow-400' : 'bi-star text-gray-400' }}"></i>
                        @endfor
                        <span class="ml-2 text-sm text-gray-600">({{ $avgRating }}/5 average)</span>
                    </div>
                </div>

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
                    @if($product->stock_qty < 1)
                        <p class="text-red-500 font-medium">Out of Stock</p>
                    @endif
                </div>

                <br>
                <h5 class="font-semibold text-gray-800 mb-2">Variants:</h5>
                @if($product->variants->count())
                    <ul class="list-disc pl-5">
                        @foreach($product->variants as $v)
                            <li>{{ $v->size ?? '-' }} / {{ $v->color ?? '-' }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-400 italic">No variants.</p>
                @endif
            </div>

            <!-- 🛍️ Buttons -->
           <div class="mt-8 space-y-4">

    @if($product->stock_qty > 0)
        @php
            $cart = session()->get('cart', []);
            $currentQty = isset($cart[$product->id]) ? $cart[$product->id]['quantity'] : 1;
        @endphp

        <!-- Quantity Selector -->
        <div class="flex items-center">
            <h6 class="mr-3 font-medium text-gray-700">Quantity In Your Cart</h6>
            <button type="button" id="decreaseQty" class="bg-gray-300 px-3 py-1 rounded-l">-</button>
            <input type="number" id="quantityInput" name="quantity"
                value="{{ $currentQty }}" min="1" max="{{ $product->stock_qty }}"
                class="w-16 text-center border-t border-b border-gray-300" readonly>
            <button type="button" id="increaseQty" class="bg-gray-300 px-3 py-1 rounded-r">+</button>
        </div>

        <!-- Add to Cart -->
        <form id="addToCartForm" action="{{ route('cart.add', $product->id) }}" method="POST" class="pt-3">
            @csrf
            <input type="hidden" name="quantity" id="selectedQuantity" value="{{ $currentQty }}">
            <button type="submit" id="addToCartBtn"
                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md font-medium transition w-full">
                🛒 Add to Cart
            </button>
        </form>

    @else
        <button class="bg-gray-400 text-white px-5 py-2 rounded-md w-full" disabled>🛒 Out of Stock</button>
    @endif

    <!-- Wishlist -->
    @auth
        <form action="{{ route('wishlist.add', $product->id) }}" method="POST">
            @csrf
            <button type="submit"
                class="border border-red-500 text-red-500 hover:bg-red-500 hover:text-white px-5 py-2 rounded-md font-medium transition w-full">
                💖 Add to Wishlist
            </button>
        </form>
    @else
        <a href="{{ route('login') }}"
            class="block text-center border border-red-500 text-red-500 hover:bg-red-500 hover:text-white px-5 py-2 rounded-md font-medium transition w-full">
            💖 Wishlist
        </a>
    @endauth
</div>

<!-- JS: Quantity validation -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const decreaseBtn = document.getElementById("decreaseQty");
        const increaseBtn = document.getElementById("increaseQty");
        const quantityInput = document.getElementById("quantityInput");
        const selectedQuantity = document.getElementById("selectedQuantity");
        const addToCartBtn = document.getElementById("addToCartBtn");
        const maxStock = parseInt(quantityInput.max);

        function updateButtonState() {
            const qty = parseInt(quantityInput.value);
            addToCartBtn.disabled = qty < 1 || qty > maxStock;
            addToCartBtn.classList.toggle("opacity-50", addToCartBtn.disabled);
            addToCartBtn.classList.toggle("cursor-not-allowed", addToCartBtn.disabled);
        }

        increaseBtn.addEventListener("click", () => {
            let qty = parseInt(quantityInput.value);
            if (qty < maxStock) {
                quantityInput.value = qty + 1;
                selectedQuantity.value = qty + 1;
            }
            updateButtonState();
        });

        decreaseBtn.addEventListener("click", () => {
            let qty = parseInt(quantityInput.value);
            if (qty > 1) {
                quantityInput.value = qty - 1;
                selectedQuantity.value = qty - 1;
            }
            updateButtonState();
        });

        updateButtonState();
    });
</script>

        </div>
    </div>

    <!-- 📝 Reviews Section -->
    <div  id="reviewSection" class="mt-10 border-t pt-6">
        <h3 class="text-xl font-bold mb-4">Customer Reviews</h3>
        @if($product->reviews->count())
            @foreach($product->reviews as $review)
                <div class="border-b pb-3 mb-3">
                    <div class="flex items-center mb-1">
                        <strong>{{ $review->user->name }}</strong>
                        <span class="ml-3 text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                    <div>
                        @for($i=1;$i<=5;$i++)
                            <i class="bi {{ $i <= $review->rating ? 'bi-star-fill text-yellow-400' : 'bi-star text-gray-300' }}"></i>
                        @endfor
                    </div>
                    <p class="mt-2 text-gray-700">{{ $review->comment }}</p>
                    @if($review->images->count())
                        <div class="flex gap-2 mt-2 flex-wrap">
                            @foreach($review->images as $img)
                                <img src="{{ asset('storage/'.$img->path) }}" class="w-40 rounded">
                            @endforeach
                        </div>
                    @endif
                    @if($review->video_path)
                        <video src="{{ asset('storage/'.$review->video_path) }}" controls class="w-60 mt-2 rounded"></video>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-gray-500">No reviews yet. Be the first to review this product!</p>
        @endif

        <!-- Add Review Form -->
        @auth
            <div class="mt-6">
                <h4 class="text-lg font-semibold mb-3">Write a Review</h4>
                <form id="reviewForm" action="{{ route('reviews.store', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Rating -->
                    <label class="block mb-1 font-medium">Rating:</label>
                    <div class="flex mb-3 space-x-1">
                        @for($i=1;$i<=5;$i++)
                            <label>
                                <input type="radio" name="rating" value="{{ $i }}" required class="hidden">
                                <i class="bi bi-star text-2xl text-gray-400 hover:text-yellow-400 cursor-pointer"></i>
                            </label>
                        @endfor
                    </div>

                    <!-- Comment -->
                    <textarea name="comment" rows="3" class="w-full border rounded-md p-2 mb-3" placeholder="Write your review..." required></textarea>

                    <!-- Image Upload -->
                    <label class="block mb-2 font-medium">Upload Image:</label>
                    <input type="file" name="images[]" accept="image/*" id="imageInput" class="mb-3" multiple>
                    <div id="imagePreviewContainer" class="flex gap-2 flex-wrap mb-3"></div>

                    
                    <!-- Drag & Drop + Image/Video Upload -->
                   <label class="block mb-2 font-medium">Upload Video:</label>
                   <input type="file" name="video" id="dropVideoInput" accept="video/mp4,video/webm" class="hidden">
                   <div id="dropVideoArea" class="border border-dashed border-gray-400 rounded p-4 text-center cursor-pointer mb-2">
                    Drag & Drop your video here or click to upload
                    </div>
                    <p id="videoError" class="text-red-500 text-sm mb-3 hidden"></p>
                    <video id="videoPreview" class="w-60 rounded border mb-2" controls></video>


                    <!-- Video Recording -->
                    <label class="block mb-2 font-medium">Record Video Review:</label>
                    <video id="preview" class="w-60 rounded border mb-2" autoplay muted></video>
                    <input type="hidden" name="video_data" id="videoData">
                    <div>
                        <button type="button" id="startBtn" class="bg-blue-500 text-white px-3 py-1 rounded">Start Recording</button>
                        <button type="button" id="stopBtn" class="bg-red-500 text-white px-3 py-1 rounded ml-2" disabled>Stop</button>
                    </div>

                    <button type="submit" class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                        Submit Review
                    </button>
                </form> 
            </div>
        @else
            <p class="mt-4 text-gray-500">
                <a href="{{ route('login') }}" class="text-blue-600 underline">Login</a> to post a review.
            </p>
        @endauth
    </div>
</div>

<script>
function changeImage(src) {
    document.getElementById('mainImage').src = src;
}

// Star selection color
document.querySelectorAll('input[name="rating"]').forEach((input, idx) => {
    input.addEventListener('change', () => {
        document.querySelectorAll('.bi-star').forEach((star, i) => {
            star.classList.toggle('text-yellow-400', i <= idx);
            star.classList.toggle('text-gray-400', i > idx);
        });
    });
});

// Preview uploaded images
const imgInput = document.getElementById('imageInput');
const imgContainer = document.getElementById('imagePreviewContainer');

imgInput?.addEventListener('change', e => {
    imgContainer.innerHTML = '';
    Array.from(e.target.files).forEach(file => {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.classList.add('w-40', 'rounded');
        imgContainer.appendChild(img);
    });
});

// WebRTC Video Record
let mediaRecorder, recordedChunks = [], stream;
const startBtn = document.getElementById('startBtn');
const stopBtn = document.getElementById('stopBtn');
const preview = document.getElementById('preview');
const videoData = document.getElementById('videoData');

startBtn?.addEventListener('click', async () => {
    stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    preview.srcObject = stream;
    recordedChunks = [];
    mediaRecorder = new MediaRecorder(stream);

    mediaRecorder.ondataavailable = e => recordedChunks.push(e.data);
    mediaRecorder.onstop = () => {
        const blob = new Blob(recordedChunks, { type: 'video/webm' });
        const reader = new FileReader();
        reader.onloadend = () => { videoData.value = reader.result; };
        reader.readAsDataURL(blob);

        preview.srcObject = null;
        preview.src = URL.createObjectURL(blob);
        stream.getTracks().forEach(t => t.stop());
    };

    mediaRecorder.start();
    startBtn.disabled = true;
    stopBtn.disabled = false;
});

stopBtn?.addEventListener('click', () => {
    mediaRecorder.stop();
    startBtn.disabled = false;
    stopBtn.disabled = true;
});

function changeMedia(src, type){
    const main = document.getElementById('mainMedia');
    main.innerHTML = '';
    if(type === 'video'){
        const vid = document.createElement('video');
        vid.src = src;
        vid.controls = true;
        vid.autoplay = true;
        vid.style.width = '100%';
        vid.style.height = '24rem';
        vid.style.objectFit = 'cover';
        vid.classList.add('rounded-lg', 'shadow-md', 'border');
        main.appendChild(vid);
    } else {
        const img = document.createElement('img');
        img.src = src;
        img.style.width = '100%';
        img.style.height = '24rem';
        img.style.objectFit = 'cover';
        img.classList.add('rounded-lg', 'shadow-md', 'border');
        main.appendChild(img);
    }
}


// Quantity Increment-Decrement
const qtyInput = document.getElementById('quantityInput');
const selectedQuantity = document.getElementById('selectedQuantity');
const incBtn = document.getElementById('increaseQty');
const decBtn = document.getElementById('decreaseQty');

incBtn?.addEventListener('click', () => {
    let current = parseInt(qtyInput.value);
    const max = parseInt(qtyInput.max);
    if (current < max) {
        qtyInput.value = current + 1;
        selectedQuantity.value = qtyInput.value;
    }
});

decBtn?.addEventListener('click', () => {
    let current = parseInt(qtyInput.value);
    if (current > 1) {
        qtyInput.value = current - 1;
        selectedQuantity.value = qtyInput.value;
    }
});

// Drag & Drop Video Upload
const dropArea = document.getElementById('dropVideoArea');
const dropInput = document.getElementById('dropVideoInput');
const videoPreview = document.getElementById('videoPreview');
const videoError = document.getElementById('videoError');

dropArea.addEventListener('click', () => dropInput.click());

// Handle file selection
dropInput.addEventListener('change', handleVideoFile);
dropArea.addEventListener('dragover', e => e.preventDefault());
dropArea.addEventListener('drop', e => {
    e.preventDefault();
    dropInput.files = e.dataTransfer.files;
    handleVideoFile();
});

function handleVideoFile() {
    videoError.classList.add('hidden');
    videoPreview.src = '';

    const file = dropInput.files[0];
    if (!file) return;

    // Check type
    if (!['video/mp4', 'video/webm'].includes(file.type)) {
        videoError.textContent = 'Only MP4 or WebM videos are allowed.';
        videoError.classList.remove('hidden');
        dropInput.value = '';
        return;
    }

    // Check size (50 MB)
    const maxSize = 50 * 1024 * 1024; // 50MB in bytes
    if (file.size > maxSize) {
        videoError.textContent = 'Video size must be less than 50 MB.';
        videoError.classList.remove('hidden');
        dropInput.value = '';
        return;
    }

    // Preview
    const url = URL.createObjectURL(file);
    videoPreview.src = url;
}

</script>
@endsection
