@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-start">
        <h3>{{ $product->title }}</h3>
        <div>
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-success btn-sm">Edit</a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
        @if($product->images->count())
        @php
        $first = $product->images->first();
        @endphp

        <div id="main-media">
            @if($first->type === 'video')
                <video src="{{ asset('storage/'.$first->path) }}" class="img-fluid rounded" controls style="max-height:400px; width:100%; object-fit:cover;"></video>
            @else
                <img src="{{ asset('storage/'.$first->path) }}" class="img-fluid rounded" style="max-height:400px; width:100%; object-fit:cover;">
            @endif
        </div>

                <div class="mt-2 d-flex gap-2 flex-wrap">
                    @foreach($product->images as $file)
                        <div style="width:70px;height:70px;overflow:hidden; cursor:pointer;">
                            @if($file->type === 'video')
                                <video src="{{ asset('storage/'.$file->path) }}" style="width:100%;height:100%;object-fit:cover;" muted></video>
                            @else
                                <img src="{{ asset('storage/'.$file->path) }}" style="width:100%;height:100%;object-fit:cover;">
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="col-md-6">
            @php
                $discounted = $product->price - ($product->price * ($product->discount / 100));
            @endphp
            <p><strong>Original Price:</strong> <span class="text-decoration-line-through text-muted">₹{{ $product->price }}</span></p>
            <p><strong>Discounted Price:</strong> <span class="text-success fw-bold">₹{{ number_format($discounted,2) }}</span> ({{ $product->discount }}%)</p>
            <p><strong>SKU:</strong> {{ $product->sku }}</p>
            <p><strong>Stock:</strong> {{ $product->stock_qty }}</p>
            <p><strong>Category:</strong> {{ $product->category?->name }} @if($product->subcategory) / {{ $product->subcategory->name }} @endif</p>

            <hr>
            <h5>Variants</h5>
            @if($product->variants->count())
                <ul>
                    @foreach($product->variants as $v)
                        <li>{{ $v->size ?? '-' }} / {{ $v->color ?? '-' }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">No variants.</p>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <p>{{ $product->description }}</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const thumbnails = document.querySelectorAll('.d-flex.flex-wrap div');
    const mainContainer = document.getElementById('main-media');

    thumbnails.forEach(th => {
        th.addEventListener('click', function() {
            const media = th.querySelector('img, video');
            const isVideo = media.tagName.toLowerCase() === 'video';
            const src = media.getAttribute('src');

            mainContainer.innerHTML = '';

            if(isVideo){
                const video = document.createElement('video');
                video.src = src;
                video.controls = true;
                video.autoplay = true;
                video.style.width = '100%';
                video.style.maxHeight = '400px';
                video.style.objectFit = 'cover';
                mainContainer.appendChild(video);
            } else {
                const img = document.createElement('img');
                img.src = src;
                img.style.width = '100%';
                img.style.maxHeight = '400px';
                img.style.objectFit = 'cover';
                img.classList.add('img-fluid', 'rounded');
                mainContainer.appendChild(img);
            }
        });
    });
});
</script>
@endsection
