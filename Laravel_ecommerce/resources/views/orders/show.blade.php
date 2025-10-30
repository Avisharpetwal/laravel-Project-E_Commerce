@extends('layouts.app')

@section('content')
<div class="container py-5">

    <h1 class="mb-4 text-2xl font-bold text-gray-800">Order Details</h1>

    <div class="bg-white shadow-md rounded-2xl p-4 border mb-5">
        <h5 class="font-semibold mb-3 text-lg">Order #{{ $order->id }}</h5>

        <p><strong>Status:</strong> 
            <span class="px-3 py-1 rounded-full text-sm font-semibold
                @if($order->status === 'Pending') bg-yellow-100 text-yellow-700
                @elseif($order->status === 'Shipped') bg-blue-100 text-blue-700
                @elseif($order->status === 'Delivered') bg-green-100 text-green-700
                @elseif($order->status === 'Cancelled') bg-red-100 text-red-700
                @endif">
                {{ $order->status }}
            </span>
        </p>

        <p><strong>Total Amount:</strong> ₹{{ $order->total }}</p>
        <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method ?? 'N/A') }}</p>
        <p><strong>Address:</strong> {{ $order->address ?? 'N/A' }}</p>
        <p><strong>Notes:</strong> {{ $order->notes ?? '—' }}</p>
        <p><strong>Placed On:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
    </div>

    {{-- 🛍️ Ordered Items --}}
    <div class="bg-white shadow-sm rounded-2xl p-4 border">
        <h4 class="text-lg font-semibold mb-3">Items in this Order</h4>

        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Product</th>
                    <th>Image</th>
                    <th>Price (₹)</th>
                    <th>Qty</th>
                    <th>Subtotal (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderItems as $item)
                    <tr>
                        <td>{{ $item->product->title ?? 'Product Deleted' }}</td>
                        <td>
                            @if($item->product && $item->product->images->first())
                                <img src="{{ asset('storage/'.$item->product->images->first()->path) }}" 
                                     width="70" class="rounded shadow">
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td>{{ $item->price }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td><strong>₹{{ number_format(($item->price * $item->quantity) *1.10,2)}}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- 🔙 Back Button --}}
    <div class="mt-4">
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">← Back to My Orders</a>
    </div>

</div>
@endsection
