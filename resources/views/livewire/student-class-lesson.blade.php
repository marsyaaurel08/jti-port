<div class="flex flex-col min-h-screen">
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

    <div class="flex-1 grid grid-cols-1 lg:grid-cols-4 gap-6 px-2 py-10 max-w-screen-2xl mx-auto pb-20">
        <div class="lg:col-span-1">
            <div class="">
                <div class="sticky -top-40 bg-white rounded-lg shadow-md border border-gray-200 p-6 h-full overflow-y-auto pb-24">
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <p class="text-xs text-gray-500 mb-2 uppercase tracking-wide">Progress Kursus</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                            <div class="bg-jti-yellow h-2 rounded-full transition-all" style="width: {{ $progress }}%">
                            </div>
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

                                            <a href="{{ route('student.classLesson', [$classSlug, $chapterLesson['slug']]) }}"
                                                class="block p-2 rounded-md text-sm transition-all {{ $isActive ? 'bg-jti-yellow text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
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
        </div>

        <div class="lg:col-span-3 h-full overflow-y-auto pb-24">
            @if (isset($lesson))
                <div class="bg-white rounded-lg shadow-md p-4 mb-6 flex flex-wrap items-center justify-between gap-4 border-l-4 border-jti-yellow">
                    <div class="flex items-center gap-3">
                        <div class="bg-gray-100 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider">Instruktur</p>
                            <p class="text-sm font-bold text-gray-900">{{ $instructor->name ?? 'Nama Instruktur tidak tersedia' }}</p>
                        </div>
                    </div>

                    @if(!empty($zoomLink))
                        <a href="{{ $zoomLink }}" target="_blank" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Join Zoom Meeting
                        </a>
                    @else
                        <div class="text-xs text-gray-400 italic">
                            Sesi Live tidak tersedia saat ini
                        </div>
                    @endif
                </div>

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

                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                    @if ($lesson->type === 'video' && $this->videoEmbedUrl)
                        <div class="relative w-full" style="padding-bottom: 56.25%;">
                            <iframe src="{{ $this->videoEmbedUrl }}"
                                class="absolute top-0 left-0 w-full h-full border-0"
                                allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"></iframe>
                        </div>
                    @elseif ($lesson->type === 'text' && $lesson->content_text)
                        <div class="p-8 prose prose-sm max-w-none">
                            {!! nl2br(htmlspecialchars($lesson->content_text)) !!}
                        </div>
                    @elseif ($lesson->type === 'quiz' && $lesson->quiz)
                        <div class="p-8 text-center">
                            <div class="mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto text-jti-yellow mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
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
                               class="inline-flex items-center px-6 py-3 bg-jti-yellow hover:bg-yellow-500 text-white font-semibold rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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

    <footer class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 md:px-8 py-3 md:py-4 flex justify-between items-center z-50">
        @if ($previousLesson)
            <a href="{{ route('student.classLesson', [$classSlug, $previousLesson['slug']]) }}"
               class="flex items-center px-4 md:px-6 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors font-semibold text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="hidden sm:inline">Materi Sebelumnya</span>
                <span class="sm:hidden">Previous</span>
            </a>
        @else
            <div class="flex items-center px-4 md:px-6 py-2 rounded-lg text-gray-400 font-semibold text-sm opacity-50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="hidden sm:inline">Materi Sebelumnya</span>
                <span class="sm:hidden">Previous</span>
            </div>
        @endif

        @if ($nextLesson)
            <button wire:click="markAsCompletedAndNext"
                    wire:loading.attr="disabled"
                    class="flex items-center px-4 md:px-6 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="hidden sm:inline wire:loading.remove">Materi Selanjutnya</span>
                <span class="sm:hidden wire:loading.remove">Next</span>
                <svg class="w-5 h-5 ml-2 wire:loading.remove" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span wire:loading class="animate-spin h-5 w-5 ml-2">
                    <svg fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        @else
            <div class="flex items-center px-4 md:px-6 py-2 rounded-lg bg-jti-yellow text-white font-semibold text-sm">
                <span class="hidden sm:inline">Materi Selesai</span>
                <span class="sm:hidden">Completed!</span>
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        @endif
    </footer>
</div>
