<?php

namespace App\Http\Controllers;

use App\Contract\CourseStudentContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{

    protected CourseStudentContract $courseStudentService;

    public function __construct(CourseStudentContract $courseStudentService)
    {
        $this->courseStudentService = $courseStudentService;
    }

    public function myCourses(Request $request)
    {
        $user = Auth::user();

        $filter = $request->get('filter', 'all');
        // For demonstration, using a fixed user ID of 3
        $courses = $this->courseStudentService->getStudentCourses(3, $filter);

        return view('student.mycourse', compact('courses'));
    }
}
