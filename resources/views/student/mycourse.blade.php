<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Courses</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-800">

<x-student.topbar />

<div class="max-w-7xl mx-auto px-6 py-6">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">My Courses</h1>
        <p class="text-gray-500">
            Manage and track your certification progress at JTI PORT
        </p>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-3 mb-8">
        <a href="{{ route('student.my-courses', ['filter' => 'all']) }}"
           class="px-4 py-2 rounded-full text-sm {{ request('filter', 'all') === 'all' ? 'bg-yellow-400 font-semibold' : 'border' }}">
            All
        </a>
        <a href="{{ route('student.my-courses', ['filter' => 'in-progress']) }}"
           class="px-4 py-2 rounded-full text-sm {{ request('filter') === 'in-progress' ? 'bg-yellow-400 font-semibold' : 'border' }}">
            In Progress
        </a>
        <a href="{{ route('student.my-courses', ['filter' => 'completed']) }}"
           class="px-4 py-2 rounded-full text-sm {{ request('filter') === 'completed' ? 'bg-yellow-400 font-semibold' : 'border' }}">
            Completed
        </a>
    </div>

    {{-- Grid --}}
    @if($courses->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($courses as $course)
                <x-student.course-card
                    :image="$course['image']"
                    {{-- :lecturer="$course['lecturer']" --}}
                    :title="$course['title']"
                    :description="$course['description']"
                    :progress="$course['progress']"
                    :status="$course['status']"
                />
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">No courses found.</p>
            <p class="text-gray-400 mt-2">Start by enrolling in a course to track your progress.</p>
        </div>
    @endif

</div>

</body>
</html>
