@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">My Wishlist</h2>

    @if($wishlist->count())
        <div class="row g-3">
            @foreach($wishlist as $item)
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        @if($item->product->images->count())
                            <img src="{{ asset('storage/'.$item->product->images->first()->path) }}" class="card-img-top" style="height:200px; object-fit:cover;">
                        @endif
                        <div class="card-body text-center">
                            <h6>{{ $item->product->title }}</h6>
                            <form action="{{ route('wishlist.remove', $item->product->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-danger mt-2">Remove</button>
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
