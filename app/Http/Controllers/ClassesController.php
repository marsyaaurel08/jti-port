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
        $is_user_enrolled = $user_id ? CourseClass::whereHas('course', function ($q) use ($slug) {
            $q->where('slug', $slug);
        })->whereHas('enrollments', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->exists() : false;
        $courseClass = CourseClass::where('slug', $slug)
            ->with(['course.chapters.lessons', 'course.category', 'instructor'])
            ->firstOrFail();

        return view('pages.classes.show', ['class' => $courseClass, 'is_user_enrolled' => $is_user_enrolled]);
    }
}
