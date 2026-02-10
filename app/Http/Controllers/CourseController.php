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
        $course = Course::where('slug', $slug)
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

        return view('pages.courses.show', compact('course'));
    }
}
