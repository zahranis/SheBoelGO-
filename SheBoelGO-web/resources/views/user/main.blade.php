@extends('layouts.app')
@section('title', 'Home')
@section('content')

<div class="bg-blue-400 min-h-screen">
    {{-- Header Section --}}
    <div class="bg-blue-400 px-4 py-4">
        {{-- Search Bar and Icons --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex-1 relative mr-2">
                <input 
                    type="text" 
                    placeholder="Search" 
                    class="w-full bg-white rounded-2xl px-4 py-3 pl-12 text-black placeholder-gray-600 border-0 focus:outline-none focus:ring-0"
                >
                <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <div class="flex space-x-2">
                <button class="text-white p-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <button class="text-white p-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <button class="text-white p-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        {{-- App Title --}}
        <h1 class="text-white text-2xl font-bold">SheBoelGo!</h1>
    </div>

    {{-- Main Content Card --}}
    <div class="bg-gray-100 rounded-t-[32px] min-h-screen px-3 py-2 shadow-lg">
        
        {{-- Categories --}}
        <div class="flex justify-center px-2 py-6">
            <div class="flex space-x-4">
                @foreach ([
                    'Snacks' => 'snack.png',
                    'Meal' => 'meal.png', 
                    'Drinks' => 'drinks.png'
                ] as $label => $icon)
                <div class="category-btn flex flex-col items-center bg-gray-300 rounded-xl p-3 cursor-pointer transition-all duration-300 hover:bg-blue-500 hover:text-white min-w-[80px]" 
                     data-category="{{ $label }}">
                    <img src="{{ asset('storage/images/' . $icon) }}" alt="{{ $label }}" class="w-7 h-7 mb-1">
                    <span class="text-sm font-medium">{{ $label }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Category Items (Hidden by default) --}}
        <div id="category-items" class="hidden">
            {{-- Back to Home Button --}}
            <div class="flex justify-between items-center mb-4 px-2">
                <button id="back-to-home" class="flex items-center text-blue-500 hover:text-blue-700 font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Home
                </button>
                <span id="selected-category" class="text-lg font-bold text-gray-700"></span>
            </div>
            
            @if(isset($categoryItems))
            <div class="space-y-4">
                @foreach($categoryItems as $item)
                <div class="bg-white rounded-lg p-4 cursor-pointer" onclick="window.location.href='/item/{{ $item['id'] }}'">
                    <img src="{{ asset('storage/images/' . $item['image_res'] . '.png') }}" 
                         alt="{{ $item['name'] }}" 
                         class="w-full h-44 object-cover rounded-lg mb-2">
                    <div class="flex justify-between items-center mb-1">
                        <h3 class="font-bold text-lg">{{ $item['name'] }}</h3>
                        <div class="flex items-center space-x-2">
                            <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded">⭐ 5.0</span>
                            <span class="text-blue-500 font-semibold">Rp{{ number_format($item['price']) }}</span>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm">{{ $item['description'] ?? 'Delicious food item' }}</p>
                    <hr class="border-yellow-400 mt-3">
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Default Content (Best Seller & Recommended) --}}
        <div id="default-content">
            {{-- Divider --}}
            <hr class="border-orange-200 mx-3 mb-6">
            
            {{-- Best Seller Section --}}
            <h2 class="text-2xl font-extrabold mb-2 px-2">Best Seller</h2>
            
            {{-- Best Seller Horizontal Row 1 --}}
            <div class="flex overflow-x-auto space-x-3 px-2 mb-4 scrollbar-hide">
                @foreach($bestSellers as $item)
                <div class="flex-shrink-0 w-32 cursor-pointer" onclick="window.location.href='/item/{{ $item['id'] }}'">
                    <div class="bg-white rounded-lg p-4 h-48">
                        <img src="{{ asset('storage/images/' . $item['image_res'] . '.png') }}" 
                             alt="{{ $item['name'] }}" 
                             class="w-full h-28 object-cover rounded mb-3">
                        <p class="text-sm text-center font-medium leading-tight">{{ $item['name'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Best Seller Horizontal Row 2 (Wide Cards) --}}
            <div class="flex overflow-x-auto space-x-3 px-2 mb-6 scrollbar-hide">
                @foreach($bestSellers as $item)
                <div class="flex-shrink-0 w-80 cursor-pointer" onclick="window.location.href='/item/{{ $item['id'] }}'">
                    <div class="bg-white rounded-lg p-4 h-36 flex items-center">
                        <img src="{{ asset('storage/images/' . $item['image_res'] . '.png') }}" 
                             alt="{{ $item['name'] }}" 
                             class="w-28 h-28 object-cover rounded mr-4">
                        <div class="flex-1">
                            <h3 class="font-bold text-base mb-2">{{ $item['name'] }}</h3>
                            <p class="text-blue-500 font-semibold text-sm">Rp{{ number_format($item['price']) }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Recommended Section --}}
            <h3 class="text-xl font-bold mb-4 px-2">Recommended</h3>
            <div class="grid grid-cols-2 gap-2 px-2 max-h-96 overflow-y-auto">
                @foreach($bestSellers as $item)
                <div class="bg-white rounded-lg p-3 cursor-pointer" onclick="window.location.href='/item/{{ $item['id'] }}'">
                    <img src="{{ asset('storage/images/' . $item['image_res'] . '.png') }}" 
                         alt="{{ $item['name'] }}" 
                         class="w-full h-28 object-cover rounded mb-2">
                    <h4 class="font-bold text-sm mb-1">{{ $item['name'] }}</h4>
                    <p class="text-blue-500 font-semibold text-sm">Rp{{ number_format($item['price']) }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryBtns = document.querySelectorAll('.category-btn');
    const categoryItems = document.getElementById('category-items');
    const defaultContent = document.getElementById('default-content');
    const backToHomeBtn = document.getElementById('back-to-home');
    const selectedCategorySpan = document.getElementById('selected-category');
    
    // Category button click handler
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.dataset.category;
            
            // Reset all buttons
            resetCategoryButtons();
            
            // Activate clicked button
            this.classList.remove('bg-gray-300', 'text-black');
            this.classList.add('bg-blue-500', 'text-white');
            
            // Show category items and hide default content
            if (category) {
                categoryItems.classList.remove('hidden');
                defaultContent.classList.add('hidden');
                selectedCategorySpan.textContent = category;
                
                // Fetch category items
                fetchCategoryItems(category);
            }
        });
    });
    
    // Back to home button click handler
    if (backToHomeBtn) {
        backToHomeBtn.addEventListener('click', function() {
            resetToDefault();
        });
    }
    
    // Double click on active category to reset (alternative method)
    categoryBtns.forEach(btn => {
        btn.addEventListener('dblclick', function() {
            if (this.classList.contains('bg-blue-500')) {
                resetToDefault();
            }
        });
    });
    
    // Function to reset category buttons
    function resetCategoryButtons() {
        categoryBtns.forEach(b => {
            b.classList.remove('bg-blue-500', 'text-white');
            b.classList.add('bg-gray-300', 'text-black');
        });
    }
    
    // Function to reset to default view
    function resetToDefault() {
        resetCategoryButtons();
        categoryItems.classList.add('hidden');
        defaultContent.classList.remove('hidden');
        selectedCategorySpan.textContent = '';
    }
    
    // Function to fetch category items (you'll need to implement this)
    function fetchCategoryItems(category) {
    fetch(`/api/category/${category}`)
        .then(response => response.json())
        .then(data => {
            // Kosongkan konten lama
            const container = document.getElementById('category-items');
            container.innerHTML = `
                <div class="flex justify-between items-center mb-4 px-2">
                    <button id="back-to-home" class="flex items-center text-blue-500 hover:text-blue-700 font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to Home
                    </button>
                    <span id="selected-category" class="text-lg font-bold text-gray-700">${category}</span>
                </div>
                <div class="space-y-4">
                    ${data.map(item => `
                        <div class="bg-white rounded-lg p-4 cursor-pointer" onclick="window.location.href='/item/${item.id}'">
                            <img src="/storage/images/${item.image_res}.png" class="w-full h-44 object-cover rounded-lg mb-2">
                            <div class="flex justify-between items-center mb-1">
                                <h3 class="font-bold text-lg">${item.name}</h3>
                                <div class="flex items-center space-x-2">
                                    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded">⭐ 5.0</span>
                                    <span class="text-blue-500 font-semibold">Rp${Number(item.price).toLocaleString('id-ID')}</span>
                                </div>
                            </div>
                            <p class="text-gray-500 text-sm">${item.description}</p>
                            <hr class="border-yellow-400 mt-3">
                        </div>
                    `).join('')}
                </div>
            `;

            document.getElementById('back-to-home').addEventListener('click', resetToDefault);
        })
        .catch(error => {
            console.error('Error fetching category items:', error);
        });
    }

    
    // Optional: Reset when clicking outside category area (advanced)
    document.addEventListener('click', function(e) {
        const isClickInsideCategory = e.target.closest('.category-btn') || 
                                    e.target.closest('#category-items') || 
                                    e.target.closest('#back-to-home');
        
        // Uncomment the lines below if you want to reset when clicking outside
        // if (!isClickInsideCategory && !categoryItems.classList.contains('hidden')) {
        //     resetToDefault();
        // }
    });
});
</script>

<style>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>

@endsection