<x-layouts.app>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 px-6 py-10 max-w-7xl mx-auto mb-32">
        <!-- Sidebar Chapters -->
        <div class="lg:col-span-1">
            <div class="sticky top-24 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">{{ $course->title }}</h2>
                <div class="space-y-2 max-h-96 overflow-y-auto">
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
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
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
                @if ($lesson->type === 'video' && $lesson->content_url)
                    <div class="relative w-full" style="padding-bottom: 56.25%;">
                        <iframe src="{{ $lesson->content_url }}" class="absolute top-0 left-0 w-full h-full border-0"
                            allowfullscreen=""
                            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"></iframe>
                    </div>
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
