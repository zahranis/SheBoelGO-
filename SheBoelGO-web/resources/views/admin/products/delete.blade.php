@extends('layouts.app')

@section('title', 'Hapus Produk')

@section('content')
<div class="container mx-auto p-6 bg-white">
    <h1 class="text-xl font-bold mb-6">Hapus Produk</h1>

    @foreach($products as $product)
        <div class="flex justify-between items-center border-b py-2">
            <span>{{ $product['name'] }}</span>
            <form action="{{ route('admin.products.destroy', $product['id']) }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Hapus</button>
            </form>
        </div>
    @endforeach
</div>
@endsection
