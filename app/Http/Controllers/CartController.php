<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\UserCart;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cartItems = UserCart::where('user_id', $user->id)
            ->with(['course.category'])
            ->get();

        $totalPrice = $cartItems->sum(function ($item) {
            return $item->course->price;
        });

        $cartCount = $cartItems->count();

        // Mengirim 'user' juga agar Sidebar tidak error
        return view('pages.user.cart', compact('user', 'cartItems', 'totalPrice', 'cartCount'));
    }

    public function store(Course $course)
    {
        $userId = Auth::id();

        // Cek jika kursus sudah ada di keranjang database
        $exists = UserCart::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->exists();

        if ($exists) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kursus sudah ada di keranjang. Silakan periksa keranjang Anda.',
                    'type' => 'info',
                ], 200);
            }

            return redirect()->back()->with('info', 'Kursus sudah ada di keranjang. Silakan periksa keranjang Anda.');
        }

        // Simpan ke Database
        UserCart::create([
            'user_id' => $userId,
            'course_id' => $course->id,
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kursus berhasil ditambahkan ke keranjang!',
                'type' => 'success',
            ], 200);
        }

        return redirect()->back()->with('success', 'Kursus berhasil ditambahkan ke keranjang!');
    }

    public function storeClass(CourseClass $courseClass)
    {
        $userId = Auth::id();

        // Cek jika kelas sudah ada di keranjang database
        $exists = UserCart::where('user_id', $userId)
            ->where('class_id', $courseClass->id)
            ->exists();

        if ($exists) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kelas sudah ada di keranjang. Silakan periksa keranjang Anda.',
                    'type' => 'info',
                ], 200);
            }

            return redirect()->back()->with('info', 'Kelas sudah ada di keranjang. Silakan periksa keranjang Anda.');
        }

        // Simpan ke Database
        UserCart::create([
            'user_id' => $userId,
            'class_id' => $courseClass->id,
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil ditambahkan ke keranjang!',
                'type' => 'success',
            ], 200);
        }

        return redirect()->back()->with('success', 'Kelas berhasil ditambahkan ke keranjang!');
    }

    public function removeFromCart($id)
    {
        $cartItem = UserCart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $cartItem->delete();

        return redirect()->back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }
}
