@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="mb-2 mb-md-0">
                <i class="bi bi-receipt"></i> Order #{{ $order->id }} Details
            </h4>
            <a href="{{ route('admin.manage.orders') }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Orders
            </a>
        </div>

        <div class="card-body">
            {{--  Order Information --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="fw-semibold text-primary mb-3">Customer Info</h5>
                    <p><strong>Name:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ $order->phone }}</p>
                    <p><strong>Address:</strong> {{ $order->address }}</p>
                </div>

                <div class="col-md-6">
                    <h5 class="fw-semibold text-primary mb-3">Order Info</h5>
                    <p><strong>Order ID:</strong> {{ $order->id }}</p>
                    <p><strong>Status:</strong> 
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
                    </p>
                    <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
                    <p><strong>Total:</strong> ₹{{ number_format($order->total, 2) }}</p>
                    <p><strong>Placed On:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>

            {{--  Notes --}}
            @if($order->notes)
                <div class="alert alert-secondary">
                    <strong>Customer Notes:</strong><br>
                    {{ $order->notes }}
                </div>
            @endif

            {{--  Items Table --}}
            <h5 class="fw-semibold text-primary mt-4 mb-3">Order Items</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Image</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                            <tr>
                                <td>{{ $item->product->title ?? 'Product Deleted' }}</td>
                                <td>
                                    @if($item->product && $item->product->images->isNotEmpty())
                                        <img src="{{ asset('storage/' . $item->product->images->first()->path) }}" 
                                             alt="{{ $item->product->name }}" 
                                             width="60" height="60" 
                                             class="rounded border">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>₹{{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">Total:</th>
                            <th>₹{{ number_format($order->total, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{--  Change Status --}}
            <div class="mt-4">
                <form action="{{ route('admin.update.order.status', $order->id) }}" method="POST" class="d-flex align-items-center gap-2">
                    @csrf
                    @method('PUT')
                    <label for="status" class="fw-semibold">Change Status:</label>
                    <select name="status" id="status" class="form-select form-select-sm" style="width: 180px;" onchange="this.form.submit()">
                        <option {{ $order->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option {{ $order->status == 'Shipped' ? 'selected' : '' }}>Shipped</option>
                        <option {{ $order->status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                        <option {{ $order->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
