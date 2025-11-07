@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Checkout</h2>

    <form action="{{ route('checkout.place') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                required value="{{ old('name', auth()->user()->name) }}">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Phone with country code -->
        <div class="mb-3">
            <label>Phone</label>
            <div class="input-group">
                <select class="form-select" style="max-width: 150px;" name="country_code">
                    <option value="+91" selected>+1 (US)</option>
                     <option value="+91" selected>+91 (IND)</option>
                </select>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" required
                       value="{{ old('phone') }}">
            </div>
            @error('phone')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Pincode -->
        <div class="mb-3">
            <label>Pincode</label>
            <input type="text" id="pincode" name="pincode" class="form-control @error('pincode') is-invalid @enderror" required
                   value="{{ old('pincode') }}">
            @error('pincode')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Address</label>
            <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address') }}</textarea>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Payment Method</label>
            <select name="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                <option value="COD">Cash on Delivery</option>
                <option value="Card">Credit/Debit Card</option>
                <option value="UPI">UPI</option>
            </select>
            @error('payment_method')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Notes (Optional)</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
        </div>

        <h4>Order Summary</h4>
        <p>Subtotal: ₹{{ $subtotal }}</p>
        <p>Tax (10%): ₹{{ $tax }}</p>

        @if(isset($discount) && $discount > 0)
            <p>Discount: -₹{{ $discount }}</p>
        @endif

        @php
            $finalTotal = $subtotal + $tax - ($discount ?? 0);
        @endphp
        <h5>Total: ₹{{ $finalTotal }}</h5>

        <button type="submit" class="btn btn-success mt-3">Place Order</button>
    </form>
</div>

<script>
    document.getElementById('pincode').addEventListener('blur', function(){
        let pincode = this.value;
        if(pincode.length === 6){
            fetch(`https://api.postalpincode.in/pincode/${pincode}`)
                .then(res => res.json())
                .then(data => {
                    if(data[0].Status === "Success"){
                        let postOffice = data[0].PostOffice[0];
                        document.getElementById('address').value = postOffice.Name + ', ' + postOffice.District + ', ' + postOffice.State;
                    } else {
                        alert("Invalid Pincode!");
                        document.getElementById('address').value = '';
                    }
                })
                .catch(err => console.log(err));
        }
    });
</script>
@endsection
