@extends('layouts.app')
@section('title', $item['name'])

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-400 to-blue-500 text-white px-4 py-3 flex items-center relative">
        <button onclick="history.back()" class="p-2 hover:bg-white hover:bg-opacity-10 rounded-full transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        
        <h1 class="absolute left-1/2 transform -translate-x-1/2 text-xl font-bold">
            {{ $item['name'] }}
        </h1>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-t-3xl mt-0 min-h-screen shadow-lg">
        <div class="p-6">
            <!-- Product Image -->
            <div class="flex justify-center mb-4">
                <img src="{{ asset('/storage/images/' . $item['image_res'] . '.png') }}" 
                     alt="{{ $item['name'] }}" 
                     class="w-48 h-48 object-cover rounded-2xl shadow-md">
            </div>

            <!-- Price and Quantity Row -->
            <div class="flex justify-between items-center mb-3">
                <!-- Price -->
                <div class="text-2xl font-bold text-blue-600">
                    Rp {{ number_format($item['price']) }}
                </div>

                <!-- Quantity Controls -->
                <div class="flex items-center space-x-3">
                    <button id="decrease-btn" 
                            class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                        <span class="text-xl font-bold">-</span>
                    </button>
                    
                    <span id="quantity-display" class="text-xl font-medium px-3">1</span>
                    
                    <button id="increase-btn" 
                            class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                        <span class="text-xl font-bold">+</span>
                    </button>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t-2 border-yellow-400 my-3"></div>

            <!-- Product Name -->
            <h2 class="text-xl font-bold mb-1">{{ $item['name'] }}</h2>

            <!-- Product Description -->
            <p class="text-gray-600 text-sm mb-8">{{ $item['description'] }}</p>

            <!-- Add to Cart Button -->
            <form method="POST" action="{{ route('cart.add') }}" id="add-to-cart-form">
                @csrf
                <input type="hidden" name="item_id" value="{{ $item['id'] }}">
                <input type="hidden" name="quantity" id="quantity-input" value="1">
                
                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-4 px-6 rounded-lg transition-colors duration-200">
                    Tambah ke Keranjang
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Success Message (jika ada) -->
@if(session('success'))
<div id="success-message" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    {{ session('success') }}
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let quantity = 1;
    const quantityDisplay = document.getElementById('quantity-display');
    const quantityInput = document.getElementById('quantity-input');
    const decreaseBtn = document.getElementById('decrease-btn');
    const increaseBtn = document.getElementById('increase-btn');

    // Decrease quantity
    decreaseBtn.addEventListener('click', function() {
        if (quantity > 1) {
            quantity--;
            updateQuantity();
        }
    });

    // Increase quantity
    increaseBtn.addEventListener('click', function() {
        quantity++;
        updateQuantity();
    });

    function updateQuantity() {
        quantityDisplay.textContent = quantity;
        quantityInput.value = quantity;
    }

    // Auto hide success message
    const successMessage = document.getElementById('success-message');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0';
            setTimeout(() => {
                successMessage.remove();
            }, 300);
        }, 3000);
    }
});
</script>
@endpush

@push('styles')
<style>
#success-message {
    transition: opacity 0.3s ease;
}
</style>
@endpush
@endsection