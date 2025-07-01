@extends('layouts.app')

@section('title', $isEdit ? 'Edit Produk' : 'Tambah Produk')

@section('content')
<div class="container max-w-lg mx-auto p-6 bg-white">
    <h1 class="text-xl font-bold mb-6">{{ $isEdit ? 'Edit Produk' : 'Tambah Produk' }}</h1>

    @if(session('error'))
        <div class="mb-4 text-red-600">{{ session('error') }}</div>
    @endif

    <form action="{{ $isEdit ? route('admin.products.update', $product['id']) : route('admin.products.store') }}" method="POST">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="mb-4">
            <label class="block mb-1">Nama Produk</label>
            <input type="text" name="name" value="{{ old('name', $product['name'] ?? '') }}"
                   class="w-full p-2 border rounded" required>
        </div>

        <div class="mb-4">
            <label class="block mb-1">Kategori</label>
            <input type="text" name="category" value="{{ old('category', $product['category'] ?? '') }}"
                   class="w-full p-2 border rounded" required>
        </div>

        <div class="mb-4">
            <label class="block mb-1">Deskripsi</label>
            <textarea name="description" class="w-full p-2 border rounded" required>{{ old('description', $product['description'] ?? '') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block mb-1">Image Res</label>
            <input type="text" name="image_res" value="{{ old('image_res', $product['image_res'] ?? '') }}"
                   class="w-full p-2 border rounded" required>
        </div>

        <div class="mb-6">
            <label class="block mb-1">Harga</label>
            <input type="number" name="price" value="{{ old('price', $product['price'] ?? '') }}"
                   class="w-full p-2 border rounded" required>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
            {{ $isEdit ? 'Update Produk' : 'Tambah Produk' }}
        </button>
    </form>
</div>
@endsection
