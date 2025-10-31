@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Edit Coupon</h2>

    <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="code" class="form-label">Coupon Code</label>
            <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $coupon->code) }}" required>
            @error('code') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="discount_amount" class="form-label">Discount Amount</label>
            <input type="number" step="0.01" name="discount_amount" id="discount_amount" class="form-control" value="{{ old('discount_amount', $coupon->discount_amount) }}" required>
            @error('discount_amount') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="minimum_value" class="form-label">Minimum Cart Value</label>
            <input type="number" step="0.01" name="minimum_value" id="minimum_value" class="form-control" value="{{ old('minimum_value', $coupon->minimum_value) }}" required>
            @error('minimum_value') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="expiry_date" class="form-label">Expiry Date</label>
            <input type="date" name="expiry_date" id="expiry_date" class="form-control" value="{{ old('expiry_date', $coupon->expiry_date->format('Y-m-d')) }}" required>
            @error('expiry_date') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-success">Update Coupon</button>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
