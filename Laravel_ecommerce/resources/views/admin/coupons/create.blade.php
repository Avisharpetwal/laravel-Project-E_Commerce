@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Create New Coupon</h2>

    <form action="{{ route('admin.coupons.store') }}" method="POST" class="bg-light p-4 rounded shadow-sm">
        @csrf

        <div class="mb-3">
            <label class="form-label">Coupon Code</label>
            <input type="text" name="code" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Discount Amount (₹)</label>
            <input type="number" name="discount_amount" class="form-control" step="0.01" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Expiry Date</label>
            <input type="date" name="expiry_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Minimum Order Value (₹)</label>
            <input type="number" name="minimum_value" class="form-control" step="0.01" required>
        </div>

        <button type="submit" class="btn btn-success px-4">
            <i class="bi bi-save me-1"></i> Save Coupon
        </button>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
@endsection
