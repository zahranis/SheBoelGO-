<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Landing Page</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-[#6DA0FF] min-h-screen flex flex-col justify-between px-6 py-6">

    <div class="flex justify-center mt-24">
        <img src="{{ asset('storage/images/sheboel.png') }}" alt="SheBoel Logo" class="w-48 h-48 object-contain">
    </div>

    <div class="mt-auto space-y-4">
        <a href="{{ route('auth.page', ['mode' => 'register']) }}" class="block">
            <button class="w-full h-14 border border-white text-white rounded-lg">
                Register
            </button>
        </a>
        <a href="{{ route('auth.page', ['mode' => 'login']) }}" class="block">
            <button class="w-full h-14 bg-white text-[#6DA0FF] rounded-lg">
                Login
            </button>
        </a>
    </div>

</body>
</html>
