@extends('layouts.app')

@section('content')
<div class="container text-center py-5">
    <h1 class="text-success fw-bold mb-4"> Order Placed Successfully!</h1>

    <!-- @if(session('success'))
        <div class="alert alert-success w-50 mx-auto">
            {{ session('success') }}
        </div>
    @endif -->

    <p class="lead">Thank you for your purchase! Your order has been received and is being processed.</p>
    <p>You can check your order status from your <a href="{{ route('user.dashboard') }}" class="text-primary fw-semibold">dashboard</a>.</p>

    <a href="{{ route('user.dashboard') }}" class="btn btn-primary mt-4">Go to Dashboard</a>
</div>
@endsection
