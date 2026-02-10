<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = Auth::id();

        $stats = [
            'total_course' => Enrollment::where('user_id', $userId)->count(),
            'completed'    => Enrollment::where('user_id', $userId)->where('is_completed', true)->count(),
            'transactions' => Order::where('user_id', $userId)->where('status', 'success')->count(),
        ];

        $recent_courses = Enrollment::where('user_id', $userId)
            ->with('course')
            ->latest()
            ->take(3)
            ->get();


        return view('pages.user.index', compact('user', 'stats', 'recent_courses'));
    }
}
