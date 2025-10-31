@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">All Coupons</h2>

    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle me-1"></i> Create Coupon
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Discount (₹)</th>
                <th>Expiry Date</th>
                <th>Min Value (₹)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($coupons as $index => $coupon)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $coupon->code }}</td>
                    <td>{{ $coupon->discount_amount }}</td>
                    <td>{{ $coupon->expiry_date }}</td>
                    <td>{{ $coupon->minimum_value }}</td>
                    <td>
                        <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Delete this coupon?')" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">No coupons found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
