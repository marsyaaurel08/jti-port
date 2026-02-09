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
        $course = Course::where('slug', $slug)->firstOrFail();

        return view('pages.courses.show', compact('course'));
    }
}
