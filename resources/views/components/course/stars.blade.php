@props(['rating' => 0])

@php
    $fullStars = floor($rating);
    $hasHalfStar = $rating - $fullStars >= 0.5;
    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
    // Unique ID untuk gradient agar tidak konflik jika ada banyak rating di satu page
    $gradientId = 'grad-' . uniqid();
@endphp

<div class="flex items-center gap-1">
    {{-- Render Bintang Penuh --}}
    @for ($i = 0; $i < $fullStars; $i++)
        <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
        </svg>
    @endfor

    {{-- Render Bintang Setengah --}}
    @if ($hasHalfStar)
        <svg class="w-4 h-4" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="{{ $gradientId }}">
                    <stop offset="50%" stop-color="#fbbf24" /> {{-- amber-400 --}}
                    <stop offset="50%" stop-color="#e5e7eb" /> {{-- gray-200 --}}
                </linearGradient>
            </defs>
            <path fill="url(#{{ $gradientId }})" d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
        </svg>
    @endif

    {{-- Render Bintang Kosong --}}
    @for ($i = 0; $i < $emptyStars; $i++)
        <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
        </svg>
    @endfor

    @if($rating > 0)
        <span class="ml-2 text-sm font-medium text-gray-600">{{ number_format($rating, 1) }}</span>
    @endif
</div>
