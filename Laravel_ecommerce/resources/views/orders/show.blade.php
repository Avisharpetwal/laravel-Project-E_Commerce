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

        <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method ?? 'N/A') }}</p>
        <p><strong>Address:</strong> {{ $order->address ?? 'N/A' }}</p>
        <p><strong>Notes:</strong> {{ $order->notes ?? '—' }}</p>
        <p><strong>Placed On:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>

        {{--  Cancel Order Button (only for Pending orders) --}}
        @if($order->status === 'Pending')
            <form action="{{ route('orders.cancel', $order->id) }}" method="POST" 
                  onsubmit="return confirm('Are you sure you want to cancel this order?');" 
                  class="mt-3">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-danger">
                    ❌ Cancel Order
                </button>
            </form>
        @endif
    </div>

    {{--  Ordered Items --}}
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
                @php $subtotal = 0; @endphp
                @foreach($order->orderItems as $item)
                    @php
                        $lineTotal = $item->price * $item->quantity;
                        $subtotal += $lineTotal;
                    @endphp
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
                        <td>{{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td><strong>₹{{ number_format($lineTotal, 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $tax = $subtotal * 0.10;
            $discount = $order->discount_amount ?? 0;
            $grandTotal = $subtotal + $tax-$discount;
        @endphp

        <div class="text-end mt-4 pe-2">
            <p><strong>Subtotal:</strong> ₹{{ number_format($subtotal, 2) }}</p>
            <p><strong>Tax (10%):</strong> ₹{{ number_format($tax, 2) }}</p>
            <p class="text-xl font-bold text-green-700">
                Grand Total: ₹{{ number_format($grandTotal, 2) }}
            </p>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">← Back to My Orders</a>
    </div>

</div>
@endsection
