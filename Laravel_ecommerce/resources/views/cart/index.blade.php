@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@extends('layouts.app')

@section('content')

<div class="container py-5">
    <h1 class="mb-4">Your Shopping Cart</h1>

    @if(session('cart') && count(session('cart')) > 0)
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Price (₹)</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $id => $item)
                    <tr>
                        <td>
                            @if($item['image'])
                                <img src="{{ asset('storage/'.$item['image']) }}" width="60">
                            @endif
                        </td>
                        <td>{{ $item['name'] }}</td>
                        <td>₹{{ $item['price'] }}</td>
                        <td>
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" 
       max="{{ \App\Models\Product::find($id)->stock_qty }}" 
       style="width:60px;" class="cart-quantity" data-id="{{ $id }}">

                        </td>
                        <td class="item-subtotal">₹{{ $item['price'] * $item['quantity'] }}</td>
                        <td>
                            <form action="{{ route('cart.remove', $id) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Coupon Section -->
        <div class="mt-4">
            <form action="{{ route('cart.applyCoupon') }}" method="POST" class="row g-2">
                @csrf
                <div class="col-md-4">
                    <input type="text" name="coupon_code" class="form-control" placeholder="Enter Coupon Code" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-success">Apply Coupon</button>
                </div>
            </form>
        </div>

        @if(session('coupon'))
            <div class="alert alert-info mt-3">
                Coupon Applied: <strong>{{ session('coupon')['code'] }}</strong>  
                (Discount: ₹{{ session('coupon')['discount_amount'] }})
                <form action="{{ route('cart.removeCoupon') }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-danger">Remove</button>
                </form>
            </div>
        @endif

        <div class="mt-4">
            <h5>Subtotal: <span id="subtotal">₹{{ $subtotal }}</span></h5>
            <h6>Tax (10%): <span id="tax">₹{{ $tax }}</span></h6>
            @if(session('coupon'))
                <h6>Discount: <span id="discount">-₹{{ session('coupon')['discount_amount'] }}</span></h6>
            @else
                <h6>Discount: <span id="discount">₹0</span></h6>
            @endif
            <h4 class="fw-bold">Total: <span id="total">₹{{ $total }}</span></h4>
        </div>

    @else
        <h1>Your cart is empty!</h1>
    @endif
</div>

<div class="mt-4">
    <a href="{{ route('checkout.form') }}" class="btn btn-success">Proceed to Checkout</a>
</div>

<!-- AJAX Script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.cart-quantity').on('change', function() {
        let id = $(this).data('id');
        let quantity = $(this).val();
        let token = "{{ csrf_token() }}";

        $.ajax({
            url: '/cart/update/' + id,
            method: 'POST',
            data: {
                _token: token,
                quantity: quantity
            },
            success: function(response) {
                if(response.success) {
                    // Update item subtotal
                    $('input[data-id="'+id+'"]').closest('tr').find('.item-subtotal').text('₹' + response.itemSubtotal);

                    // Update cart totals
                    $('#subtotal').text('₹' + response.subtotal);
                    $('#tax').text('₹' + response.tax);
                    $('#total').text('₹' + response.total);

                    // Update discount if coupon applied
                    if(response.coupon) {
                        $('#discount').text('-₹' + response.coupon.discount_amount);
                    } else {
                        $('#discount').text('₹0');
                    }
                } else {
                    alert(response.message);
                }
            }
        });
    });
});
</script>

@endsection
