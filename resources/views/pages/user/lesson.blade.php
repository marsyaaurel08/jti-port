<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $lesson->title }} - {{ $course->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        /* Custom scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
    </style>
</head>
<body class="bg-white text-gray-800">

{{-- TOP BAR --}}
<header class="fixed top-0 left-0 right-0 z-50 bg-white border-b h-16">
    <div class="max-w-full px-6 h-full flex items-center justify-between">

        <div class="flex items-center gap-4 text-sm">
            <a href="{{ route('student.my-courses') }}" class="text-gray-400 hover:text-black">← Back To Course</a>
            <span class="font-semibold">{{ $course->title }}</span>
        </div>

        <div class="flex items-center gap-4">
            <input
                type="text"
                placeholder="Search Courses..."
                class="border rounded-md px-3 py-1 text-sm"
            >

            <div class="flex items-center gap-2 text-sm">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover">
                @else
                    <div class="w-8 h-8 bg-gray-300 rounded-full"></div>
                @endif
                <div>
                    <p class="font-semibold leading-none">{{ $user->name }}</p>
                    <p class="text-xs text-gray-400">Student</p>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- BODY --}}
<div class="flex pt-16 min-h-screen">

    {{-- Sidebar --}}
    <x-student.course-sidebar
        :progress="$progress"
        :activeLesson="$lesson->id"
        :chapters="$chapters"
        :completedLessonIds="$completedLessonIds"
        :courseSlug="$course->slug"
    />

    {{-- Content --}}
    <main id="mainContent" class="flex-1 flex flex-col h-[calc(100vh-4rem)] ml-72">
        {{-- Scrollable Content Area --}}
        <div class="flex-1 px-10 py-6 overflow-y-auto">
            <p class="text-sm text-gray-400 mb-2">
                {{ $lesson->chapter->title }} <span class="mx-1">›</span>
                <span class="text-yellow-500 font-semibold">{{ $lesson->title }}</span>
            </p>

            <h1 class="text-2xl font-bold mb-4">
                {{ $lesson->title }}
            </h1>

            {{-- Video Content --}}
            @if($lesson->type === 'video' && $lesson->content_url)
                <div class="flex justify-center items-start" style="height: calc(100vh - 240px); min-height: 300px;">
                    <div class="rounded-lg overflow-hidden h-full flex items-center justify-center">
                        <video
                            controls
                            class="h-full object-contain"
                            poster="{{ $course->thumbnail_url ?? '' }}"
                        >
                            <source src="{{ $lesson->content_url }}" type="video/mp4">
                            Your browser does not support video tag.
                        </video>
                    </div>
                </div>
            @endif

            {{-- Text Content --}}
            @if($lesson->content_text)
                <article class="prose max-w-none text-gray-700">
                    {!! $lesson->content_text !!}
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Id ex sit corrupti cumque voluptas accusamus accusantium in doloremque placeat numquam similique quidem, tenetur cupiditate, hic vel. Aliquam alias veritatis at? Lorem ipsum dolor sit amet consectetur adipisicing elit. Adipisci expedita commodi vel error dolore aspernatur corporis voluptate dicta quasi sed similique quaerat corrupti enim eaque blanditiis consequuntur cumque necessitatibus, maiores neque eligendi dolorem praesentium. Qui, libero necessitatibus consequatur iusto asperiores ad amet quibusdam rem nobis doloremque consectetur dicta enim voluptatem nesciunt eos, veniam voluptatum corporis! Ea ad pariatur voluptas natus maxime commodi maiores veritatis? Perspiciatis similique tempora libero iste quisquam ab iusto aspernatur, ex pariatur atque non architecto ratione laudantium optio. Explicabo beatae quam vitae perspiciatis praesentium tempora consequuntur accusantium doloremque libero eum exercitationem molestiae, architecto iure magnam cum molestias!
                </article>
            @endif

            {{-- Quiz Content --}}
            @if($lesson->type === 'quiz')
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">Quiz</h3>
                    <p class="text-yellow-700">This lesson contains a quiz. Complete quiz to mark this lesson as complete.</p>
                    <a href="#" class="inline-block mt-4 px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                        Start Quiz
                    </a>
                </div>
            @endif

            {{-- Assignment Content --}}
            @if($lesson->type === 'assignment')
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">Assignment</h3>
                    <p class="text-blue-700">This lesson contains an assignment. Complete assignment to mark this lesson as complete.</p>
                    <a href="#" class="inline-block mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        View Assignment
                    </a>
                </div>
            @endif
        </div>

        {{-- Sticky Navigation --}}
        <div class="sticky bottom-0 bg-white border-t px-10 py-4 flex justify-between text-sm text-gray-400 z-40">
            @if($previousLesson)
                <a href="{{ route('student.lesson', ['courseSlug' => $course->slug, 'lessonSlug' => $previousLesson->slug]) }}" class="hover:text-black flex items-center gap-2">
                    <i class="ph ph-caret-left"></i>
                    {{ $previousLesson->title }}
                </a>
            @else
                <span></span>
            @endif

            <span class="font-semibold text-gray-800">{{ $lesson->title }}</span>

            @if($nextLesson)
                <a href="{{ route('student.lesson', ['courseSlug' => $course->slug, 'lessonSlug' => $nextLesson->slug]) }}" class="hover:text-black flex items-center gap-2">
                    {{ $nextLesson->title }}
                    <i class="ph ph-caret-right"></i>
                </a>
            @else
                <span></span>
            @endif
        </div>
    </main>
</div>

</body>
</html>
