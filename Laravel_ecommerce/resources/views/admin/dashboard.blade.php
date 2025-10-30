@extends('layouts.app')

{{-- Bootstrap CSS & Icons --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

@section('content')
<div class="container py-5">

    <!-- Header -->
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-white bg-dark p-3 rounded shadow-sm">Admin Dashboard</h1>
        <p class="text-muted mt-3 fs-5">
            Welcome back, <strong>{{ Auth::user()->name }}</strong> 👋
        </p>
    </div>

    <!-- Stats Section -->
    <div class="row g-4 justify-content-center">

        <div class="col-md-3">
            <div class="card shadow border-0 h-100 text-center bg-light hover-card">
                <div class="card-body">
                    <div class="text-primary mb-2"><i class="bi bi-people-fill fs-1"></i></div>
                    <h5 class="card-title text-secondary fw-semibold">Total Users</h5>
                    <h2 class="fw-bold text-primary">{{ $users }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow border-0 h-100 text-center bg-light hover-card">
                <div class="card-body">
                    <div class="text-success mb-2"><i class="bi bi-person-badge-fill fs-1"></i></div>
                    <h5 class="card-title text-secondary fw-semibold">Total Admins</h5>
                    <h2 class="fw-bold text-success">{{ $admins }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow border-0 h-100 text-center bg-light hover-card">
                <div class="card-body">
                    <div class="text-warning mb-2"><i class="bi bi-cart-check-fill fs-1"></i></div>
                    <h5 class="card-title text-secondary fw-semibold">Total Orders</h5>
                    <h2 class="fw-bold text-warning">{{ $totalOrders }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow border-0 h-100 text-center bg-light hover-card">
                <div class="card-body">
                    <div class="text-danger mb-2"><i class="bi bi-currency-rupee fs-1"></i></div>
                    <h5 class="card-title text-secondary fw-semibold">Total Sales</h5>
                    <h2 class="fw-bold text-danger">₹{{ number_format($totalSales, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="text-center mt-5 d-flex justify-content-center flex-wrap gap-3">
        <a href="{{ route('admin.users') }}" class="btn btn-primary btn-lg shadow-sm px-4">
            <i class="bi bi-people-fill me-1"></i> Manage Users
        </a>

        <a href="{{ route('admin.categories.index') }}" class="btn btn-dark btn-lg shadow-sm px-4">
            <i class="bi bi-tags-fill me-1"></i> Manage Categories
        </a>

        <a href="{{ route('admin.products.create') }}" class="btn btn-success btn-lg shadow-sm px-4">
            <i class="bi bi-box-seam me-1"></i> Add Product
        </a>

        <a href="{{ route('admin.products.index') }}" class="btn btn-info btn-lg shadow-sm px-4">
            <i class="bi bi-boxes me-1"></i> Manage Products
        </a>

        <a href="{{ route('orders.index') }}" class="btn btn-warning btn-lg shadow-sm px-4 text-white">
            <i class="bi bi-clipboard-data me-1"></i> Manage Orders
        </a>
    </div>

    <!-- Top Selling Products -->
    <div class="mt-5 bg-white p-4 rounded shadow-sm">
        <h4 class="fw-semibold mb-3"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Top 5 Selling Products</h4>
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Product</th>
                    <th scope="col">Quantity Sold</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topProducts as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->title ?? 'Deleted Product' }}</td>
                        <td>{{ $item->total_qty }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">No sales data available yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="text-center mt-5 text-muted">
        <hr>
        <small>© {{ date('Y') }} E-commerce Admin Panel | Designed with ❤️ by Avi</small>
    </div>
</div>

{{-- Custom Hover Effect --}}
<style>
.hover-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.15);
}
</style>
@endsection
