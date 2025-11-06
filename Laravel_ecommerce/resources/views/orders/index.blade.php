@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4 text-2xl font-bold text-gray-800">📦 My Orders</h1>

    @if($orders->count() > 0)
        @foreach($orders as $order)
            <div class="bg-white shadow-md rounded-xl p-4 mb-4 border hover:shadow-lg transition">
                <div class="flex justify-between items-center">
                    <div>
                        <h5 class="font-semibold text-lg">Order #{{ $order->id }}</h5>
                        <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                        <p class="mt-2"><strong>Total:</strong> ₹{{ number_format($order->total, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            @if($order->status === 'Pending') bg-yellow-100 text-yellow-700
                            @elseif($order->status === 'Shipped') bg-blue-100 text-blue-700
                            @elseif($order->status === 'Delivered') bg-green-100 text-green-700
                            @elseif($order->status === 'Cancelled') bg-red-100 text-red-700
                            @endif">
                            {{ $order->status }}
                        </span>
                        <br>
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-sm mt-2">View Details</a>

                        <!-- ✅ Add Review Buttons for each product -->
                        @if($order->orderItems && $order->orderItems->count())
                            @foreach($order->orderItems as $item)
                                @php $product = $item->product; @endphp
                                @if($product)
                                    <a href="{{ route('products.show', $product->id) }}#reviewSection" 
                                       class="btn btn-success btn-sm mt-2">
                                       Add Review for {{ $product->title }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <p class="text-gray-600">You haven’t placed any orders yet.</p>
    @endif
</div>
@endsection
