<nav class="bg-gray-800 p-4 text-white flex justify-between">
    <div>
        <a href="{{ url('/') }}" class="font-semibold text-lg">MyApp</a>
    </div>
    <div>
        @auth
        <a href="{{ route('profile.show') }}" class="mx-2 hover:underline">Profile</a>

            <a href="{{ route('profile.edit') }}" class="mx-2 hover:underline">View Profile</a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="mx-2 text-red-400 hover:text-red-600">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="mx-2 hover:underline">Login</a>
            <a href="{{ route('register') }}" class="mx-2 hover:underline">Register</a>
        @endauth
    </div>
</nav>
