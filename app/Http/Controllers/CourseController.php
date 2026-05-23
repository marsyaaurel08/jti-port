<?php

namespace App\Http\Controllers;

use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        return view('pages.courses.index');
    }

    public function show($slug)
    {
        $user_id = auth()->id();
        $is_user_enrolled = $user_id ? Course::where('slug', $slug)->whereHas('enrollments', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->exists() : false;

        // Load course from Redis cache first, fallback to database
        $cacheKey = "course_detail:{$slug}";
        $course = cache()->remember($cacheKey, 3600, function () use ($slug) {
            return Course::where('slug', $slug)
                ->with([
                    'category',
                    'chapters' => function ($query) {
                        $query->orderBy('id', 'asc'); // Urutkan bab
                    },
                    'chapters.lessons', // Ambil materi di setiap bab
                    'reviews.user'      // Ambil review beserta nama pemberinya
                ])
                ->withCount('enrollments') // Hitung jumlah peserta
                ->firstOrFail();
        });

        return view('pages.courses.show', compact('course', 'is_user_enrolled'));
    }
}
