@props([
    'image' => 'https://picsum.photos/400/200',
    'title' => '-',
    'description' => '-',
    'progress' => 0,
    'status' => 'in-progress',
    'slug' => '',
    'first_lesson_slug' => null,
    'last_accessed_lesson_slug' => null,
])

@php
    $isCompleted = $status === 'completed' || $progress >= 100;
    $lessonSlug = $last_accessed_lesson_slug ?? $first_lesson_slug;
    $canNavigate = !empty($slug) && !empty($lessonSlug);
@endphp

<div class="border border-gray-100 rounded-xl overflow-hidden bg-white">
    <div class="h-40 bg-gray-200">
        <img src="{{ $image }}" class="w-full h-full object-cover" alt="{{ $title }}">
    </div>
    <div class="p-4">
        <h3 class="font-bold text-sm mt-1">{{ $title }}</h3>
        <p class="text-sm text-gray-400 mt-1 line-clamp-2">{{ $description }}</p>
        <div class="flex justify-between text-sm mt-4">
            <span>Progress</span>
            <span class="font-semibold">{{ $progress }}%</span>
        </div>
        <div class="w-full h-2 bg-gray-200 rounded-full mt-2">
            <div class="h-2 rounded-full {{ $isCompleted ? 'bg-green-500' : 'bg-yellow-400' }}"
                style="width: {{ $progress }}%"></div>
        </div>
        <div class="flex justify-between items-center mt-4">
            <span class="text-sm {{ $isCompleted ? 'text-green-500 font-semibold' : 'text-gray-400' }}">
                {{ $isCompleted ? 'Selesai' : 'Dalam Proses' }}
            </span>
            @if ($canNavigate)
                <a href="{{ route('student.lesson', ['courseSlug' => $slug, 'lessonSlug' => $lessonSlug]) }}"
                    class="{{ $isCompleted ? 'border px-4 py-2 text-sm rounded-md hover:bg-gray-100 transition-colors' : 'bg-jti-primary text-white text-sm font-semibold px-4 py-2 rounded-md hover:bg-jti-primary-hover transition-colors' }}">
                    {{ $isCompleted ? 'Review' : 'Lanjutkan' }}
                </a>
            @else
                <button class="bg-gray-300 text-sm font-semibold px-4 py-2 rounded-md cursor-not-allowed" disabled>No
                    Lessons</button>
            @endif
        </div>
    </div>
</div>
