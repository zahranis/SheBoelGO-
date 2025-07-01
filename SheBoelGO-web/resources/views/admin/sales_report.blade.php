@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="container mx-auto px-4 py-6 bg-white">

    {{-- Tab Navigation --}}
    <div class="mb-6">
        <ul class="flex border-b">
            @foreach (['cart_items_aktif' => 'Aktif', 'cart_items_selesai' => 'Selesai', 'cart_items_dibatalkan' => 'Dibatalkan'] as $key => $label)
                <li class="-mb-px mr-1">
                    <a href="{{ route('admin.sales_report', ['status' => $key]) }}"
                       class="inline-block py-2 px-4 {{ $status === $key ? 'text-blue-600 border-b-2 border-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-800' }}">
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Item List --}}
    <div class="space-y-4">
        @forelse ($items as $item)
            <div class="p-4 border rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold">{{ $item['product']['name'] ?? 'Nama Tidak Tersedia' }}</h3>
                <p><strong>Jumlah:</strong> {{ $item['quantity'] }}</p>
                <p><strong>Status:</strong> {{ $item['status'] }}</p>
                <p><strong>Email Pengguna:</strong> {{ $item['user_email'] ?? 'Tidak Diketahui' }}</p>
                <p><strong>Tanggal:</strong> {{ $item['date'] }}</p>
                <a href="{{ route('admin.sales_report.detail', ['collection' => $status, 'id' => $item['id']]) }}"
                   class="mt-2 inline-block text-blue-600 hover:underline">Lihat Detail</a>
            </div>
        @empty
            <p>Tidak ada item ditemukan.</p>
        @endforelse
    </div>
</div>
@endsection
