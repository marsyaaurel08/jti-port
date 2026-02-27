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
        $courseClass = CourseClass::where('slug', $slug)
            ->with(['course.chapters.lessons', 'category', 'instructor'])
            ->firstOrFail();

        return view('pages.classes.show', ['class' => $courseClass]);
    }
}
