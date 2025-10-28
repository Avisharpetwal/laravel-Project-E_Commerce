<x-app-layout>
    <!-- Top Navbar -->
    <div class="flex justify-between items-center bg-gray-900 text-white px-6 py-4">
        <h1 class="text-xl font-semibold">Welcome, {{ Auth::user()->name }}</h1>
        <a href="{{ route('profile.edit') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
           View Profile
        </a>
    </div>

    <!-- Main Content -->
    <div class="flex justify-center items-center h-[70vh] bg-gray-100">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">You are logged in 🎉</h2>
            <p class="text-gray-600">Welcome to your dashboard!</p>
        </div>
    </div>
</x-app-layout>
