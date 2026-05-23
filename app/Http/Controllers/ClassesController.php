<?php

namespace App\Http\Controllers;

use App\Models\CourseClass;

class ClassesController extends Controller
{
    public function index()
    {
        return view('pages.classes.index');
    }

    public function show($slug)
    {
        $user_id = auth()->id();
        $is_user_enrolled = $user_id ? CourseClass::withTrashed()
            ->whereHas('course', function ($q) use ($slug) {
                $q->where('slug', $slug)->withTrashed();
            })->whereHas('enrollments', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->exists() : false;

        // Load course class from Redis cache first, fallback to database
        $cacheKey = "class_detail:{$slug}";
        $courseClass = cache()->remember($cacheKey, 3600, function () use ($slug) {
            return CourseClass::withTrashed()
                ->where('slug', $slug)
                ->with([
                    'course' => function ($query) {
                        $query->withTrashed();
                    },
                    'course.chapters.lessons',
                    'course.category',
                    'instructor'
                ])
                ->firstOrFail();
        });

        return view('pages.classes.show', ['class' => $courseClass, 'is_user_enrolled' => $is_user_enrolled]);
    }
}
