<div class="flex flex-col min-h-screen">
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

    <style>
        .article-content h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #1a202c;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.5rem;
        }
        .article-content h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: #2d3748;
        }
        .article-content p {
            margin-bottom: 1rem;
            line-height: 1.75;
            color: #4a5568;
        }
        .article-content ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .article-content li {
            margin-bottom: 0.5rem;
        }
        .article-content pre {
            background-color: #f7fafc;
            padding: 1rem;
            border-radius: 0.375rem;
            overflow-x: auto;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
        }
        .article-content code {
            background-color: #f7fafc;
            padding: 0.125rem 0.25rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            font-family: Menlo, Monaco, Consolas, monospace;
        }
        .article-content hr {
            border: 0;
            border-top: 2px solid #e2e8f0;
            margin: 2rem 0;
        }
        .article-content strong {
            font-weight: 600;
            color: #1a202c;
        }
    </style>

    {{-- Mobile Sidebar Toggle Bar --}}
    <div class="lg:hidden sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm"
         x-data="{ open: false }">

        <button
            @click="open = !open"
            class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h7" />
                </svg>
                <span>Daftar Materi</span>
                @if(isset($course))
                    <span class="text-xs text-gray-400 font-normal">— {{ $course['title'] ?? '' }}</span>
                @endif
            </div>
            <svg class="w-4 h-4 text-gray-500 transition-transform duration-300"
                 :class="open ? 'rotate-180' : 'rotate-0'"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        {{-- Collapsible Sidebar Content (mobile only) --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="border-t border-gray-100 bg-white max-h-72 overflow-y-auto px-4 py-4">

            {{-- Progress Bar --}}
            <div class="mb-4 pb-4 border-b border-gray-100">
                <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide">Progress Kursus</p>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                    <div class="bg-jti-yellow h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                </div>
                <p class="text-xs text-gray-500">{{ count($completedLessonIds) }}/{{ $course['total_lessons'] ?? 0 }} pelajaran</p>
            </div>

            <div class="space-y-2">
                @if (!empty($chapters))
                    @foreach ($chapters as $chapter)
                        <div class="mb-3">
                            <h3 class="text-xs font-semibold text-gray-500 mb-1 uppercase tracking-tight">
                                {{ $chapter['title'] }}</h3>
                            <div class="space-y-1">
                                @foreach ($chapter['lessons'] as $chapterLesson)
                                    @php
                                        $isCompleted = in_array($chapterLesson['id'], $completedLessonIds);
                                        $isActive = isset($lesson) && $chapterLesson['id'] === $lesson->id;
                                    @endphp
                                    <a href="{{ route('student.lesson', [$courseSlug, $chapterLesson['slug']]) }}"
                                       @click="open = false"
                                       class="block p-2 rounded-md text-sm transition-all {{ $isActive ? 'bg-jti-yellow text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                        <span class="inline-block mr-2">
                                            @if ($isCompleted) ✓ @else • @endif
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

    {{-- Main Layout --}}
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-4 gap-6 px-2 py-6 lg:py-10 max-w-screen-2xl mx-auto pb-20 w-full">

        <!-- Desktop Sidebar (hidden on mobile) -->
        <div class="hidden lg:block lg:col-span-1">
            <div class="sticky top-4" style="height: calc(100vh - 2rem);">
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 overflow-y-auto h-full pb-24">

                    <!-- Progress Bar -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <p class="text-xs text-gray-500 mb-2 uppercase tracking-wide">Progress Kursus</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                            <div class="bg-jti-yellow h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500">{{ count($completedLessonIds) }}/{{ $course['total_lessons'] ?? 0 }} pelajaran</p>
                    </div>

                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        {{ $course['title'] ?? 'Loading...' }}
                    </h2>

                    <div class="space-y-2">
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
                                               class="block p-2 rounded-md text-sm transition-all {{ $isActive ? 'bg-jti-yellow text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                                                <span class="inline-block mr-2">
                                                    @if ($isCompleted) ✓ @else • @endif
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
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3 h-full overflow-y-auto pb-24">
            @if (isset($lesson))
                <!-- Lesson Header -->
                <div class="bg-white rounded-lg shadow-md p-5 md:p-8 mb-6">
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                        <h1 class="text-xl md:text-3xl font-bold text-gray-900 leading-tight">{{ $lesson->title }}</h1>
                        <span class="px-3 py-1 md:px-4 md:py-2 bg-blue-100 text-blue-800 rounded-full text-xs md:text-sm font-semibold whitespace-nowrap">
                            {{ ucfirst($lesson->type) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600">
                        {{ $lesson->duration_seconds ? floor($lesson->duration_seconds / 60) . ' menit' : 'Durasi tidak ditentukan' }}
                    </p>
                </div>

                <!-- Video/Content Section -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                    @if ($lesson->type === 'video')
                        @if (in_array($lesson->text_position, ['before', 'both']) && $lesson->content_text)
                             <div class="p-5 md:p-8 w-full mx-auto">
                                <div class="article-content">
                                    {!! $lesson->content_text !!}
                                </div>
                            </div>
                        @endif

                        @if ($this->videoEmbedUrl)
                            <div class="relative w-full" style="padding-bottom: 56.25%;">
                                <iframe src="{{ $this->videoEmbedUrl }}"
                                    class="absolute top-0 left-0 w-full h-full border-0"
                                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"></iframe>
                            </div>
                        @endif

                        @if (in_array($lesson->text_position, ['after', 'both']) && $lesson->text_after_video)
                            <div class="p-5 md:p-8 w-full mx-auto">
                                <div class="article-content">
                                    {!! $lesson->text_after_video !!}
                                </div>
                            </div>
                        @endif

                    @elseif ($lesson->type === 'text' && $lesson->content_text)
                        <div class="p-5 md:p-8 w-full mx-auto">
                            <div class="article-content">
                                {!! $lesson->content_text !!}
                            </div>
                        </div>

                    @elseif ($lesson->type === 'quiz' && $lesson->quiz)
                        <div class="p-6 md:p-8 text-center">
                            <div class="mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 md:w-16 md:h-16 mx-auto text-jti-yellow mb-4"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Quiz Tersedia</h3>
                                <p class="text-gray-600 mb-4">
                                    Kuis ini memiliki {{ $lesson->quiz->questions()->count() }} pertanyaan
                                    @if($lesson->quiz->time_limit_minutes)
                                        dengan waktu {{ $lesson->quiz->time_limit_minutes }} menit
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500 mb-6">
                                    Nilai minimum untuk lulus: {{ $lesson->quiz->pass_score }}%
                                </p>
                            </div>
                            <a href="{{ route('student.quiz.attempt', $lesson->quiz->id) }}"
                               class="inline-flex items-center px-5 py-3 bg-jti-yellow hover:bg-yellow-500 text-white font-semibold rounded-lg transition-colors text-sm md:text-base">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Mulai Quiz
                            </a>
                        </div>

                        @if ($lesson->quiz)
                            <livewire:quiz-score-history :quizId="$lesson->quiz->id" />
                        @endif

                    @else
                        <div class="p-8 text-center text-gray-600">
                            <p>Konten tidak tersedia</p>
                        </div>
                    @endif
                </div>

            @else
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <p class="text-gray-600">Loading materi...</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Fixed Footer -->
    <footer class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-3 md:px-8 py-3 md:py-4 flex justify-between items-center z-50">
        @if ($previousLesson)
            <a href="{{ route('student.lesson', [$courseSlug, $previousLesson['slug']]) }}"
               class="flex items-center px-3 md:px-6 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors font-semibold text-sm">
                <svg class="w-5 h-5 mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span class="hidden sm:inline">Materi Sebelumnya</span>
                <span class="sm:hidden">Previous</span>
            </a>
        @else
            <div class="flex items-center px-3 md:px-6 py-2 rounded-lg text-gray-400 font-semibold text-sm opacity-50 cursor-not-allowed">
                <svg class="w-5 h-5 mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span class="hidden sm:inline">Materi Sebelumnya</span>
                <span class="sm:hidden">Previous</span>
            </div>
        @endif

        @if ($nextLesson)
            <button wire:click="markAsCompletedAndNext"
                    wire:loading.attr="disabled"
                    class="flex items-center px-3 md:px-6 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="hidden sm:inline" wire:loading.remove>Materi Selanjutnya</span>
                <span class="sm:hidden" wire:loading.remove>Next</span>
                <svg class="w-5 h-5 ml-1 md:ml-2" wire:loading.remove fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span wire:loading class="animate-spin h-5 w-5 ml-2">
                    <svg fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        @else
            <div class="flex items-center px-3 md:px-6 py-2 rounded-lg bg-jti-yellow text-white font-semibold text-sm">
                <span class="hidden sm:inline">Materi Selesai</span>
                <span class="sm:hidden">Selesai!</span>
                <svg class="w-5 h-5 ml-1 md:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        @endif
    </footer>
</div>
