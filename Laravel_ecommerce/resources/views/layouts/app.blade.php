<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel eCommerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

    <nav class="flex justify-between items-center bg-black text-white px-6 py-3">
        <h1 class="text-lg font-semibold">Laravel eCommerce</h1>
        <div>
            @auth
                <a href="{{ url('/dashboard') }}" class="mr-4">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="mr-4 text-white">Login</a>
                <a href="{{ route('register') }}" class="text-white">Register</a>
            @endauth
        </div>
    </nav>

    <main class="p-6">


    
        @yield('content')
    </main>

</body>
</html>
