<?php

namespace App\Http\Controllers;

use App\Models\CourseClass;

class ClassesController extends Controller
{
    public function index()
    {
        return view('pages.classes.index');
    }

    public function show(CourseClass $courseClass)
    {
        $courseClass->load(['course', 'instructor']);

        return view('pages.classes.show', ['class' => $courseClass]);
    }
}
