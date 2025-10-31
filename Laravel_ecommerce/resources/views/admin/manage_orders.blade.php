@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="mb-2 mb-md-0"><i class="bi bi-box-seam"></i> All Orders</h4>
            
            <form method="GET" action="{{ route('admin.manage.orders') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    class="form-control form-control-sm"
                    style="min-width: 250px; flex: 1;" 
                    placeholder="Search by user or order ID">
                
                <select name="status" class="form-select form-select-sm" style="width: 160px;">
                    <option value="">All Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Shipped" {{ request('status') == 'Shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <button type="submit" class="btn btn-light btn-sm px-4">Filter</button>
            </form>
        </div>

        <div class="card-body">
            @if(session('success'))
                <p class="text-success fw-semibold mb-4">{{ session('success') }}</p>
            @endif

            @if($orders->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="mt-3 text-secondary fs-5">No orders found</p>
                </div>
            @else
            <div class="table-responsive">
                <table class="table align-middle table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Change Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td class="fw-bold">{{ $order->id }}</td>
                            <td>{{ $order->user->name ?? 'N/A' }}</td>
                            <td>₹{{ number_format($order->total, 2) }}</td>
                            <td>
                                @php
                                    $badgeClass = match($order->status) {
                                        'Pending' => 'bg-warning text-dark',
                                        'Shipped' => 'bg-info text-dark',
                                        'Delivered' => 'bg-success',
                                        'Cancelled' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} px-3 py-2">{{ $order->status }}</span>
                            </td>
                            <td>
                                <form action="{{ route('admin.update.order.status', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option {{ $order->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option {{ $order->status == 'Shipped' ? 'selected' : '' }}>Shipped</option>
                                        <option {{ $order->status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option {{ $order->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <a href="{{ route('admin.order.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection