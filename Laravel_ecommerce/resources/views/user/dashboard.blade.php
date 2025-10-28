<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="bg-gray-900 text-white px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-semibold">User Dashboard</h1>

        <div class="flex items-center space-x-4">
            <a href="{{ route('profile.show') }}" 
               class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-sm font-medium">
               View Profile
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg text-sm font-medium">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex-grow flex items-center justify-center">
        <h2 class="text-2xl font-semibold text-gray-800">Welcome, {{ Auth::user()->name }} 🎉</h2>
    </div>
</body>
</html>
