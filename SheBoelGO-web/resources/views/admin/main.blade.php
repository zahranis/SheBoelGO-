@extends('layouts.app')

@section('title', 'Admin Panel')

@section('content')
<div class="container mx-auto px-4 py-8 bg-white">

    {{-- Top Bar --}}
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Admin Panel</h1>
        <a href="{{ url()->previous() }}" class="text-blue-600 hover:underline flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L6.414 9H17a1 1 0 110 2H6.414l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Kembali
        </a>
    </div>

    {{-- Tombol Menu --}}
    <div class="max-w-md mx-auto space-y-4">
        <a href="{{ route('admin.products.create') }}"
           class="block w-full text-center bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
            Tambah Produk
        </a>

        <a href="{{ route('admin.products.delete') }}"
           class="block w-full text-center bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
            Hapus Produk
        </a>

        <a href="{{ route('admin.products.edit') }}"
           class="block w-full text-center bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-600 transition">
            Edit Produk
        </a>

        <a href="{{ route('admin.sales_report') }}"
           class="block w-full text-center bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
            Laporan Penjualan
        </a>
    </div>

</div>
@endsection
