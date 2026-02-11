<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\UserCart;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cartItems = UserCart::where('user_id', $user->id)
            ->with(['course.category', 'course.instructor'])
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
            return redirect()->back()->with('info', 'Kursus sudah ada di keranjang.');
        }

        // Simpan ke Database
        UserCart::create([
            'user_id' => $userId,
            'course_id' => $course->id,
        ]);

        return redirect()->route('student.cart')->with('success', 'Berhasil ditambahkan ke keranjang!');
    }

    public function removeFromCart($id)
    {
        $cartItem = UserCart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $cartItem->delete();

        return redirect()->back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }
}
