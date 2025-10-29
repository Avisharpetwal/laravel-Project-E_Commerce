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
                <img src="{{ asset('storage/'.$product->images->first()->path) }}" class="img-fluid rounded">
                <div class="mt-2 d-flex gap-2 flex-wrap">
                    @foreach($product->images as $img)
                        <div style="width:70px;height:70px;overflow:hidden">
                            <img src="{{ asset('storage/'.$img->path) }}" style="width:100%;height:100%;object-fit:cover">
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
@endsection
