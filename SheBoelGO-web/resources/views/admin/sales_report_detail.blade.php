@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Detail Item - {{ $item['id'] }}</h2>

    <p><strong>Status Saat Ini:</strong> {{ $item['status'] }}</p>

    <form action="{{ route('admin.sales_report.update_status', ['collection' => $collection, 'id' => $item['id']]) }}" method="POST" class="mt-4">
        @csrf
        @method('PUT')

        <label for="status" class="block mb-2">Ubah Status:</label>
        <select name="status" id="status" class="border rounded px-4 py-2 w-full">
            @foreach (['Diproses', 'Dalam Perjalanan', 'Selesai'] as $option)
                <option value="{{ $option }}" {{ $item['status'] === $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>

        <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">Simpan Perubahan</button>
    </form>

    <hr class="my-6">

    <h3 class="font-semibold mb-2">Pindahkan ke Koleksi Lain</h3>
    <div class="flex gap-2">
        @foreach ([
            'cart_items_aktif' => 'Aktif',
            'cart_items_selesai' => 'Selesai',
            'cart_items_dibatalkan' => 'Dibatalkan'
        ] as $targetCollection => $label)
            @if ($collection !== $targetCollection)
                <form action="{{ route('admin.sales_report.move', ['from' => $collection, 'to' => $targetCollection, 'id' => $item['id']]) }}" method="POST">
                    @csrf
                    <button class="bg-gray-600 text-white px-3 py-1 rounded">{{ $label }}</button>
                </form>
            @endif
        @endforeach
    </div>
</div>
@endsection
