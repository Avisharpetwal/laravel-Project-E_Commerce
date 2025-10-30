@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif


@extends('layouts.app')

@section('content')

<div class="container py-5">
    <h1 class="mb-4">Your Shopping Cart</h1>

    @if(session('cart') && count(session('cart')) > 0)
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Price (₹)</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $id => $item)
                    <tr>
                        <td>
                            @if($item['image'])
                                <img src="{{ asset('storage/'.$item['image']) }}" width="60">
                            @endif
                        </td>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['price'] }}</td>
                        <td>
                            <form action="{{ route('cart.update', $id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" style="width:60px;">
                                <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                            </form>
                        </td>
                        <td>₹{{ $item['price'] * $item['quantity'] }}</td>
                        <td>
                            <form action="{{ route('cart.remove', $id) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            <h5>Subtotal: ₹{{ $subtotal }}</h5>
            <h6>Tax (10%): ₹{{ $tax }}</h6>
            <h4 class="fw-bold">Total: ₹{{ $total }}</h4>
        </div>
    @else
        <h1>Your cart is empty!</h1>
    @endif
</div>

<div class="mt-4">
    <a href="{{ route('checkout.form') }}" class="btn btn-success">Proceed to Checkout</a>
</div>
@endsection
