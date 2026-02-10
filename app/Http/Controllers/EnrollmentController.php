<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrolled = Enrollment::where('user_id', Auth::id())
            ->with('course.category')
            ->latest()
            ->paginate(6);


        return view('pages.user.enrolled', compact('enrolled', 'cartCount'));
    }
}
