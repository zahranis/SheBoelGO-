@extends('layouts.app')
@section('title', 'Orderan Saya')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-400 to-blue-500 text-white px-4 pt-8 pb-4">
        <div class="flex items-center relative">
            <button onclick="history.back()" class="p-2 hover:bg-white hover:bg-opacity-10 rounded-full transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <h1 class="absolute left-1/2 transform -translate-x-1/2 text-xl font-extrabold">
                Orderan Saya
            </h1>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white px-4 py-2">
        <div class="flex justify-center space-x-4">
            @php
                $tabs = [
                    'aktif' => 'Aktif',
                    'selesai' => 'Selesai', 
                    'dibatalkan' => 'Dibatalkan'
                ];
            @endphp
            
            @foreach($tabs as $key => $label)
                <a href="{{ route('cart', ['tab' => $key]) }}" 
                   class="px-4 py-2 rounded-2xl font-semibold text-white transition-colors
                          {{ $selectedTab == $key ? 'bg-blue-600' : 'bg-blue-300 hover:bg-blue-400' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-gray-100 rounded-t-3xl mt-0 min-h-screen shadow-lg">
        <div class="p-4">
            @if(empty($cartItems))
                <!-- Empty State -->
                <div class="flex items-center justify-center h-96">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg">Tidak ada order pada tab ini</p>
                    </div>
                </div>
                <div class="border-t border-blue-400 mt-4"></div>
            @else
                <!-- Cart Items List -->
                <div class="space-y-4">
                    @foreach($cartItems as $item)
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <!-- Item Row -->
                            <div class="flex items-center space-x-4 mb-4">
                                <!-- Product Image -->
                                <img src="{{ asset('storage/images/' . $item['image_res'] . '.png') }}" 
                                     alt="{{ $item['name'] }}" 
                                     class="w-16 h-16 object-cover rounded-lg">
                                
                                <!-- Item Details -->
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-800">{{ $item['name'] }}</h3>
                                    <p class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($item['created_at'])->format('d F, h.i A') }}
                                    </p>
                                </div>
                                
                                <!-- Price and Quantity -->
                                <div class="text-right">
                                    <p class="font-bold text-blue-600">Rp {{ number_format($item['price']) }}</p>
                                    <p class="text-sm text-gray-500">{{ $item['quantity'] }} items</p>
                                    <p class="text-xs text-gray-400">{{ $item['status'] }}</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                @if($selectedTab == 'aktif')
                                    <!-- Active Order Buttons -->
                                    <button onclick="cancelOrder('{{ $item['id'] }}')" 
                                            class="flex-1 bg-red-500 hover:bg-red-600 text-white text-xs py-2 px-4 rounded-lg transition-colors">
                                        Batalkan Order
                                    </button>
                                    <button onclick="trackDriver('{{ $item['id'] }}')" 
                                            class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-xs py-2 px-4 rounded-lg transition-colors">
                                        Track Driver
                                    </button>
                                @elseif($selectedTab == 'selesai')
                                    <!-- Completed Order Button -->
                                    <button onclick="window.location.href='/rating/{{ $item['item_id'] }}'" 
                                            class="w-full bg-green-500 hover:bg-green-600 text-white text-xs py-2 px-4 rounded-lg transition-colors">
                                        Beri Penilaian
                                    </button>
                                @elseif($selectedTab == 'dibatalkan')
                                    <!-- Cancelled Order Button -->
                                    <button onclick="window.location.href='/item/{{ $item['item_id'] }}'" 
                                            class="w-full bg-blue-500 hover:bg-blue-600 text-white text-xs py-2 px-4 rounded-lg transition-colors">
                                        Pesan Lagi
                                    </button>
                                @endif
                            </div>

                            <!-- Divider -->
                            @if(!$loop->last)
                                <div class="border-t border-blue-400 mt-4"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Track Driver Modal -->
<div id="trackModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
        <h3 class="text-lg font-bold mb-4">Status Pengantaran</h3>
        <p id="statusText" class="text-gray-600 mb-4">Sedang memuat status...</p>
        <button onclick="closeTrackModal()" 
                class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition-colors">
            Tutup
        </button>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div id="success-message" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div id="error-message" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    {{ session('error') }}
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto hide messages
    const messages = document.querySelectorAll('#success-message, #error-message');
    messages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.remove();
            }, 300);
        }, 3000);
    });
});

function cancelOrder(orderId) {
    if (confirm('Apakah Anda yakin ingin membatalkan order ini?')) {
        fetch('/cart/cancel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                order_id: orderId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal membatalkan order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    }
}

function trackDriver(orderId) {
    const modal = document.getElementById('trackModal');
    const statusText = document.getElementById('statusText');
    
    modal.classList.remove('hidden');
    statusText.textContent = 'Sedang memuat status...';
    
    fetch('/cart/track-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            order_id: orderId
        })
    })
    .then(response => response.json())
    .then(data => {
        statusText.textContent = data.status + '\n' + (data.message || '');
    })
    .catch(error => {
        statusText.textContent = 'Gagal memuat status pengantaran';
    });
}

function closeTrackModal() {
    document.getElementById('trackModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('trackModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTrackModal();
    }
});
</script>
@endpush

@push('styles')
<style>
#success-message, #error-message {
    transition: opacity 0.3s ease;
}
</style>
@endpush
@endsection