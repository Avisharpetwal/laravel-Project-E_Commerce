@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Checkout</h2>

    <form action="{{ route('checkout.place') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name', auth()->user()->name) }}">
        </div>

        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label>Payment Method</label>
            <select name="payment_method" class="form-control" required>
                <option value="COD">Cash on Delivery</option>
                <option value="Card">Credit/Debit Card</option>
                <option value="UPI">UPI</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Notes (Optional)</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
        </div>

        <h4>Order Summary</h4>
        <p>Subtotal: ₹{{ $subtotal }}</p>
        <p>Tax (10%): ₹{{ $tax }}</p>

        @if(isset($discount) && $discount > 0)
            <p>Discount: -₹{{ $discount}}</p>
        @endif

        @php
            $finalTotal = $subtotal + $tax - ($discount ?? 0);
        @endphp

        <h5>Total: ₹{{ $finalTotal }}</h5>

        <button type="submit" class="btn btn-success mt-3">Place Order</button>
    </form>
</div>
@endsection
