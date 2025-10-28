@extends('layouts.app')

@section('content')
<div class="container">
    <h3>My Profile</h3>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        <label>Name:</label>
        <input type="text" name="name" value="{{ $user->name }}">

        <label>Change Password:</label>
        <input type="password" name="password">
        <input type="password" name="password_confirmation" placeholder="Confirm Password">

        <button type="submit">Update</button>
    </form>
</div>
@endsection
