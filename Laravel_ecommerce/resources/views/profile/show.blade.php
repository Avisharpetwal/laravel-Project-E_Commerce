<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <div class="max-w-lg mx-auto mt-12 bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold mb-4 text-center">My Profile</h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                       class="w-full border rounded px-3 py-2" required>
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                       class="w-full border rounded px-3 py-2" required>
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">New Password (optional)</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Update Profile
            </button>
        </form>
    </div>

</body>
</html>
