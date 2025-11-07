<nav class="flex justify-between items-center bg-black text-white px-6 py-3">
    <h1 class="text-lg font-semibold">
        <a href="{{ url('/') }}">Laravel eCommerce</a>
    </h1>

    <div class="flex items-center space-x-4">
        @auth
            {{--  Check User Role --}}
            @if(Auth::user()->role === 'admin')
                {{--  Admin Navbar --}}
                <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-300">Dashboard</a>
                <a href="{{ route('profile.show') }}" class="hover:text-gray-300">Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-red-400 hover:text-red-600">Logout</button>
                </form>

            @else
                {{--  Normal User Navbar --}}
                <a href="{{ route('user.dashboard') }}" class="hover:text-gray-300">Home</a>
                <a href="{{ route('profile.show') }}" class="hover:text-gray-300">Profile</a>

                {{--  Cart --}}
                <a href="{{ route('cart.index') }}" class="relative hover:text-gray-300">
                    🛒 Cart
                    @if(session('cart') && count(session('cart')) > 0)
                        <span class="absolute -top-2 -right-3 bg-red-500 text-white rounded-full text-xs px-1.5">
                            {{ count(session('cart')) }}
                        </span>
                    @endif
                </a>

                {{--  My Orders --}}
               <a href="{{ route('orders.index') }}" class="hover:text-gray-300">
                       📦 My Orders
                 </a>

                {{--  Wishlist --}}
                <a href="{{ route('wishlist.index') }}" class="relative hover:text-gray-300">
                    ❤️ Wishlist
                    @php
                        $wishlistCount = \App\Models\Wishlist::where('user_id', Auth::id())->count();
                    @endphp
                    @if($wishlistCount > 0)
                        <span class="absolute -top-2 -right-3 bg-green-500 text-white rounded-full text-xs px-1.5">
                            {{ $wishlistCount }}
                        </span>
                    @endif
                </a>

                {{--  Logout --}}
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-red-400 hover:text-red-600">Logout</button>
                </form>

            @endif
        @else
            {{--  Guest Navbar --}}
            <a href="{{ route('login') }}" class="text-white hover:text-gray-300">Login</a>
            <a href="{{ route('register') }}" class="text-white hover:text-gray-300">Register</a>
        @endauth
    </div>
</nav>
