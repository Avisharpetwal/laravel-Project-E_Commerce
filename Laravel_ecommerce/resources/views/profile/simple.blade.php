<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">👤 User Profile</h1>

        {{-- Success / Error Message --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">{{ session('error') }}</div>
        @endif

        {{-- User Info --}}
        <div class="mb-6">
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
        </div>

        {{-- Change Password Form --}}
        <h2 class="text-lg font-semibold mb-3 text-gray-700">🔒 Change Password</h2>

        <form method="POST" action="{{ route('profile.updatePassword') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-gray-600 mb-1">Current Password</label>
                <input type="password" name="current_password" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label class="block text-gray-600 mb-1">New Password</label>
                <input type="password" name="new_password" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label class="block text-gray-600 mb-1">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" class="w-full border rounded p-2" required>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Update Password</button>
        </form>

        <div class="text-center mt-6">
            <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline">← Back to Dashboard</a>
        </div>
    </div>

</body>
</html>
