@extends('layouts.app')

@section('title', 'Leave a Review')

@section('content')
<div class="bg-blue-500 text-white py-4 px-6 flex items-center justify-between">
    <a href="{{ url()->previous() }}" class="text-white text-lg font-bold flex items-center">
        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 18l-1.4-1.4 6.6-6.6H2v-2h13.2l-6.6-6.6L10 2l8 8-8 8z"/>
        </svg>
        Back
    </a>
    <h1 class="text-xl font-bold text-center w-full">Leave a Review</h1>
</div>

<div class="bg-white rounded-t-2xl shadow-lg p-6">
    @if ($item)
        <div class="text-center">
            <img src="{{ asset('storage/images/' . $item['image_res'] . '.png') }}" alt="{{ $item['name'] }}"
                 class="w-32 h-32 object-cover rounded-xl mx-auto">
            <h2 class="text-lg font-bold mt-4">{{ $item['name'] }}</h2>
        </div>

        <form action="{{ route('rating.submit') }}" method="POST" class="mt-6">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item['id'] }}">
            <input type="hidden" name="rating" id="ratingInput" value="{{ $rating }}">

            <div id="stars" class="flex justify-center items-center space-x-2 cursor-pointer mb-4">
            <div id="stars" class="flex justify-center items-center space-x-2 cursor-pointer mb-4">
            @for ($i = 1; $i <= 5; $i++)
                <svg data-value="{{ $i }}"
                    class="w-8 h-8 star transition-colors duration-200 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-400' }}"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1
                            1 0 00.95.69h4.174c.969 0 1.371 1.24.588
                            1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287
                            3.974c.3.921-.755 1.688-1.54 1.118l-3.38-2.454a1
                            1 0 00-1.175 0l-3.38 2.454c-.784.57-1.838-.197-1.54-1.118l1.287-3.974a1
                            1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.174a1
                            1 0 00.95-.69l1.286-3.973z"/>
                </svg>
            @endfor
            </div>

            </div>

            <!-- Comment -->
            <div>
                <label for="comment" class="block font-semibold mb-1">Komentar Anda</label>
                <textarea id="comment" name="comment" rows="5" class="w-full border rounded-lg p-2"
                    {{ $hasRated ? 'readonly' : '' }}>{{ old('comment', $comment ?? '') }}</textarea>
            </div>

            @if (!$hasRated)
                <button type="submit" class="w-full mt-6 bg-blue-800 text-white py-2 rounded-lg">
                    Submit
                </button>
            @else
                <p class="text-center text-gray-500 mt-4">
                    Anda sudah memberikan ulasan untuk produk ini.
                </p>
            @endif
        </form>
    @else
        <p class="text-center text-gray-500">Produk tidak ditemukan.</p>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('ratingInput');

        stars.forEach(star => {
            star.addEventListener('click', () => {
                const value = parseInt(star.getAttribute('data-value'));
                ratingInput.value = value;

                // Update warna semua bintang
                stars.forEach(s => {
                    const sValue = parseInt(s.getAttribute('data-value'));
                    s.classList.toggle('text-yellow-400', sValue <= value);
                    s.classList.toggle('text-gray-400', sValue > value);
                });
            });
        });
    });
</script>
@endsection
