<x-layouts.app :showFooter="false">
    <div class="px-6 py-10 ml-72 mb-32">
        <!-- Sidebar Chapters -->
        <div class="fixed left-4 top-24 bottom-4 w-64 bg-white shadow-xl rounded-2xl p-6 overflow-y-auto z-40">
            <h2 class="text-lg font-bold text-gray-900 mb-4">{{ $course->title }}</h2>
            <div class="space-y-2">
                @foreach ($chapters as $chapter)
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-tight">
                            {{ $chapter->title }}</h3>
                        <div class="space-y-1">
                            @foreach ($chapter->lessons as $chapterLesson)
                                @php
                                    $isCompleted = in_array($chapterLesson->id, $completedLessonIds);
                                    $isActive = $chapterLesson->id === $lesson->id;
                                @endphp
                                <a href="{{ route('student.lesson', [$course->slug, $chapterLesson->slug]) }}"
                                    class="block p-2 rounded-md text-sm transition-all {{ $isActive ? 'bg-jti-yellow text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                    <span class="inline-block mr-2">
                                        @if ($isCompleted)
                                            ✓
                                        @else
                                            •
                                        @endif
                                    </span>
                                    {{ $chapterLesson->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Main Content -->
        <div>
            <!-- Lesson Header -->
            <div class="bg-white rounded-lg shadow-md p-8 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $lesson->title }}</h1>
                    <span
                        class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">{{ ucfirst($lesson->type) }}</span>
                </div>
                <p class="text-gray-600">
                    {{ $lesson->duration_seconds ? floor($lesson->duration_seconds / 60) . ' menit' : 'Durasi tidak ditentukan' }}
                </p>
            </div>

            <!-- Video/Content Section -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                @if ($lesson->type === 'video')
                    @if ($lesson->youtube_embed_url)
                        <div class="relative w-full" style="padding-bottom: 56.25%;">
                            <iframe
                                src="{{ $lesson->youtube_embed_url }}?enablejsapi=1&rel=0&modestbranding=1"
                                class="absolute top-0 left-0 w-full h-full border-0"
                                title="{{ $lesson->title }}"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allowfullscreen></iframe>
                        </div>
                    @else
                        <div class="p-8 text-center text-gray-600">
                            <div class="mb-4">
                                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-lg font-semibold text-gray-700 mb-2">Video tidak dapat diputar</p>
                            <p class="text-sm text-gray-500">URL video tidak valid atau video tidak tersedia untuk embedding.</p>
                            @if ($lesson->content_url)
                                <p class="text-xs text-gray-400 mt-2">URL: {{ $lesson->content_url }}</p>
                            @endif
                        </div>
                    @endif
                @elseif ($lesson->type === 'text' && $lesson->content_text)
                    <div class="p-8 prose prose-sm max-w-none">
                        {!! nl2br(htmlspecialchars($lesson->content_text)) !!}
                    </div>
                @else
                    <div class="p-8 text-center text-gray-600">
                        <p>Konten tidak tersedia</p>
                    </div>
                @endif
            </div>

            <!-- Progress -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <p class="text-sm text-gray-600 mb-2">Progress Kursus</p>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-jti-yellow h-2 rounded-full" style="width: {{ $progress }}%"></div>
                </div>
                <p class="text-sm text-gray-700 mt-2 font-semibold">{{ $progress }}% Selesai</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <x-student.lesson-navigation :prev="$previousLesson ? route('student.lesson', [$course->slug, $previousLesson->slug]) : null" :next="$nextLesson ? route('student.lesson', [$course->slug, $nextLesson->slug]) : null" />
</x-layouts.app>
