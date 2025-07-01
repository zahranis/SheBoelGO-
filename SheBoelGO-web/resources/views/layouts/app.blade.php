<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SheBoel')</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    {{-- Tambahkan CSS tambahan jika perlu --}}
    @stack('styles')
</head>
<body class="bg-blue-400 min-h-screen flex flex-col justify-between">

    <header class="bg-white shadow p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-xl font-bold text-blue-600">SheBoel</div>
            <nav class="flex space-x-4">
                <a href="{{ route('main') }}" class="text-blue-600 hover:underline">Home</a>
                <a href="{{ route('cart') }}" class="text-blue-600 hover:underline">Cart</a>
            </nav>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 p-4">
        @yield('content')
    </main>

    <!-- {{-- Footer --}}
    <footer class="text-center text-white py-4">
        &copy; {{ date('Y') }} SheBoel. All rights reserved.
    </footer> -->

    @stack('scripts')
</body>
</html>
