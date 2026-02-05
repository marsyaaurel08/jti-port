<?php

namespace App\Http\Controllers;

use App\Models\Course;

class ClassesController extends Controller
{
    public function index()
    {
        return view('pages.classes.index');
    }

    public function show($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        return view('pages.classes.show', compact('course'));
    }
}
