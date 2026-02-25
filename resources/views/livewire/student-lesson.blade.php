<div>
    <!-- Session Flash Messages -->
    @if (session('success'))
        <div class="fixed top-4 right-4 bg-green-50 border border-green-200 rounded-lg text-green-700 p-4 max-w-md z-50"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="fixed top-4 right-4 bg-red-50 border border-red-200 rounded-lg text-red-700 p-4 max-w-md z-50"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            ✗ {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 px-6 py-10 max-w-7xl mx-auto mb-32">
        <!-- Sidebar Chapters -->
        <div class="lg:col-span-1">
            <div class="sticky top-24 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    {{ $course['title'] ?? 'Loading...' }}
                </h2>
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @if (!empty($chapters))
                        @foreach ($chapters as $chapter)
                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-tight">
                                    {{ $chapter['title'] }}</h3>
                                <div class="space-y-1">
                                    @foreach ($chapter['lessons'] as $chapterLesson)
                                        @php
                                            $isCompleted = in_array($chapterLesson['id'], $completedLessonIds);
                                            $isActive = isset($lesson) && $chapterLesson['id'] === $lesson->id;
                                        @endphp
                                        <a href="{{ route('student.lesson', [$courseSlug, $chapterLesson['slug']]) }}"
                                            class="block p-2 rounded-md text-sm transition-all {{ $isActive ? 'bg-jti-yellow text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }}"
                                            wire:navigate>
                                            <span class="inline-block mr-2">
                                                @if ($isCompleted)
                                                    ✓
                                                @else
                                                    •
                                                @endif
                                            </span>
                                            {{ $chapterLesson['title'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            @if (isset($lesson))
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
                    @if ($lesson->type === 'video' && $this->videoEmbedUrl)
                        <div class="relative w-full" style="padding-bottom: 56.25%;">
                            <iframe src="{{ $this->videoEmbedUrl }}"
                                class="absolute top-0 left-0 w-full h-full border-0" allowfullscreen=""
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

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-2">Status Pelajaran</p>
                            @if ($isLessonCompleted)
                                <span
                                    class="inline-block px-4 py-2 bg-green-100 text-green-800 rounded-lg font-medium">✓
                                    Selesai</span>
                            @else
                                <span
                                    class="inline-block px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg font-medium">Belum
                                    Selesai</span>
                            @endif
                        </div>
                        @if (!$isLessonCompleted)
                            <button wire:click="markAsCompleted" wire:loading.attr="disabled"
                                class="px-6 py-2.5 bg-jti-yellow hover:bg-jti-maroon text-white font-medium rounded-lg transition-colors disabled:opacity-50">
                                <span wire:loading.remove>Tandai Selesai</span>
                                <span wire:loading>
                                    <svg class="animate-spin h-5 w-5 inline" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </span>
                            </button>
                        @else
                            <button wire:click="markAsIncomplete" wire:loading.attr="disabled"
                                class="px-6 py-2.5 bg-gray-400 hover:bg-gray-500 text-white font-medium rounded-lg transition-colors disabled:opacity-50">
                                <span wire:loading.remove>Tandai Belum Selesai</span>
                                <span wire:loading>
                                    <svg class="animate-spin h-5 w-5 inline" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Progress -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <p class="text-sm text-gray-600 mb-2">Progress Kursus</p>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-jti-yellow h-2 rounded-full transition-all" style="width: {{ $progress }}%">
                        </div>
                    </div>
                    <p class="text-sm text-gray-700 mt-2 font-semibold">{{ $progress }}% Selesai
                        ({{ count($completedLessonIds) }}/{{ $course['total_lessons'] ?? 0 }} pelajaran)</p>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <p class="text-gray-600">Loading materi...</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Navigation -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                @if ($previousLesson)
                    <a href="{{ route('student.lesson', [$courseSlug, $previousLesson['slug']]) }}"
                        class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-900 font-medium rounded-lg transition-colors"
                        wire:navigate>
                        ← Sebelumnya
                    </a>
                @else
                    <div></div>
                @endif

                @if ($nextLesson)
                    <a href="{{ route('student.lesson', [$courseSlug, $nextLesson['slug']]) }}"
                        class="px-6 py-2.5 bg-jti-yellow hover:bg-jti-maroon text-white font-medium rounded-lg transition-colors"
                        wire:navigate>
                        Selanjutnya →
                    </a>
                @else
                    <div class="text-gray-600 font-medium">
                        ✓ Kursus Selesai!
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

