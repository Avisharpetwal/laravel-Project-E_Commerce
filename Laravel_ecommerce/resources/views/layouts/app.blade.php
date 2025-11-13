<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel eCommerce') }}</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-gray-50 text-gray-800">

    {{--  Navbar --}}
    @include('layouts.navigation')

    {{--  Page Content --}}
    <main class="p-6 min-h-screen">
        @yield('content')
    </main>

    {{--  Footer  --}}
    <footer class="bg-black text-white text-center py-3">
        <p class="mb-0 text-sm">&copy; {{ date('Y') }} Laravel eCommerce — All Rights Reserved.</p>
    </footer>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
