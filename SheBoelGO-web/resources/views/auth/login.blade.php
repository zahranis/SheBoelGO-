@extends('layouts.app')
@section('title', $isLogin ? 'Login' : 'Register')
@section('content')
    <div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-4 text-center">
            {{ $isLogin ? 'Login' : 'Register' }}
        </h2>

        @if(session('error'))
            <div class="text-red-500 text-sm mb-4">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('auth.submit') }}">
            @csrf
            <input type="hidden" name="isLogin" value="{{ $isLogin ? '1' : '0' }}">

            <div class="mb-4">
                <label class="block font-semibold mb-1">Email</label>
                <input type="email" name="email"
                    class="w-full border border-gray-300 rounded px-3 py-2"
                    required value="{{ old('email') }}">
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Password</label>
                <input type="password" name="password"
                    class="w-full border border-gray-300 rounded px-3 py-2"
                    required>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">
                {{ $isLogin ? 'Login' : 'Register' }}
            </button>
        </form>

        <div class="mt-4 text-center">
            @if($isLogin)
                <a href="{{ route('auth.page', ['mode' => 'register']) }}"
                   class="text-sm text-blue-500 hover:underline">
                    Don't have an account? Register
                </a>
            @else
                <a href="{{ route('auth.page', ['mode' => 'login']) }}"
                   class="text-sm text-blue-500 hover:underline">
                    Already have an account? Login
                </a>
            @endif
        </div>
    </div>
@endsection
