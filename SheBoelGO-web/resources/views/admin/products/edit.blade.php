@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
<div class="container mx-auto p-6 bg-white">
    <h1 class="text-xl font-bold mb-6">Edit Produk</h1>

    @foreach($products as $product)
        <div class="flex justify-between items-center border-b py-2">
            <span>{{ $product['name'] }}</span>
            <a href="{{ route('admin.products.editForm', $product['id']) }}"
               class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                Edit
            </a>
        </div>
    @endforeach
</div>
@endsection
