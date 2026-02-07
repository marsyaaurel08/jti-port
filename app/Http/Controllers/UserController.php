<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Transaction;

class UserController extends Controller
{
    /**
     * Dashboard Utama (Index)
     */
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id ?? 1;

        // Data Statistik sederhana untuk Dashboard JTIport
        $stats = [
            'total_course' => Enrollment::where('user_id', $userId)->count(),
            'completed'    => Enrollment::where('user_id', $userId)->where('is_completed', true)->count(),
            'transactions' => Order::where('user_id', $userId)->where('status', 'success')->count(),
        ];

        // Ambil 3 kursus terakhir yang dipelajari
        $recent_courses = Enrollment::where('user_id', $userId)
                            ->with('course')
                            ->latest()
                            ->take(3)
                            ->get();

        return view('pages.user.index', compact('user', 'stats', 'recent_courses'));
    }

    /**
     * Daftar Kursus Aktif
     */
    public function enrolledCourses()
    {
        $user = Auth::user();

        $userId = $user->id ?? 1;
        $enrolled = Enrollment::where('user_id', $userId)
                        ->with('course.category')
                        ->latest()
                        ->paginate(6);

        return view('pages.user.enrolled', compact('user', 'enrolled'));
    }

    /**
     * Pengaturan Profil
     */
    public function profile()
    {
        $user = Auth::user();

        $userId = $user->id ?? 1;
        return view('pages.user.profile', compact('user'));
    }

    /**
     * Riwayat Transaksi
     */
    public function transactions()
    {
        $user = Auth::user();

        $userId = $user->id ?? 1;
        $transactions = Order::where('user_id', $userId)
                            ->latest()
                            ->paginate(10);

        return view('pages.user.transactions', compact('user', 'transactions'));
    }
}
