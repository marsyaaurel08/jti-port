@props([
    'progress' => 0,
    'activeLesson' => null,
    'chapters' => [],
    'completedLessonIds' => [],
    'courseSlug' => ''
])

<aside id="sidebar" class="fixed left-0 top-16 bottom-0 w-72 bg-white border-r z-40 sidebar-scroll overflow-y-auto">

    {{-- Progress Bar --}}
    <div class="p-6 border-b">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-semibold text-gray-500">Course Progress</h3>
            <span class="text-sm font-semibold text-yellow-600">{{ $progress }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-yellow-500 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
        </div>
    </div>

    <div class="p-6">
        <h3 class="text-sm font-semibold text-gray-500 mb-4">Course Content</h3>

        <ul class="space-y-4 text-sm">
            @foreach ($chapters as $chapter)
                <li>
                    {{-- Chapter Title --}}
                    <div class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                        <span class="text-gray-400">{{ $loop->iteration }}.</span>
                        {{ $chapter->title }}
                    </div>

                    {{-- Lessons --}}
                    @if ($chapter->lessons->isNotEmpty())
                        <ul class="ml-4 space-y-1">
                            @foreach ($chapter->lessons as $lesson)
                                <li>
                                    <a
                                        href="{{ route('student.lesson', ['courseSlug' => $courseSlug, 'lessonSlug' => $lesson->slug]) }}"
                                        class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors
                                            {{ $lesson->id == $activeLesson
                                                ? 'bg-yellow-100 text-yellow-700 font-semibold'
                                                : 'text-gray-600 hover:bg-gray-100' }}"
                                    >
                                        {{-- Completion Status --}}
                                        @if (in_array($lesson->id, $completedLessonIds))
                                            <i class="ph-fill ph-check-circle text-green-500 text-sm flex-shrink-0"></i>
                                        @else
                                            <i class="ph ph-circle text-gray text-xs flex-shrink-0"></i>
                                        @endif

                                        <span class="truncate">{{ $lesson->title }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="ml-4 text-xs text-gray-400 italic">No lessons yet</p>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

</aside>
