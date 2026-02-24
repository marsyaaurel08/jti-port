<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\UserCart;
use Illuminate\Support\Facades\Auth;

/**
 * Controller untuk mengelola keranjang belanja user (kursus & kelas).
 *
 * Data utama disimpan di tabel user_carts (database).
 * Session 'cart' hanya sebagai cache untuk tampilan cepat (counter, detail item di frontend).
 * Session disinkronkan setelah setiap perubahan agar tetap konsisten.
 */
class CartController extends Controller
{
    /**
     * Menampilkan halaman keranjang user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Eager load minimal kolom untuk performa
        $cartItems = UserCart::where('user_id', $user->id)
            ->with([
                'course' => fn ($q) => $q->select('id', 'title', 'price', 'thumbnail_url', 'slug', 'category_id'),
                'class' => fn ($q) => $q->select('id', 'title', 'price', 'thumbnail_url', 'slug', 'category_id'),
                'course.category:id,name',
            ])
            ->get();

        $totalPrice = $cartItems->sum(function ($item) {
            return $item->course ? $item->course->price : ($item->class ? $item->class->price : 0);
        });

        $cartCount = $cartItems->count();

        return view('pages.user.cart', compact('user', 'cartItems', 'totalPrice', 'cartCount'));
    }

    /**
     * Menambahkan kursus ke keranjang.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Course $course)
    {
        $userId = Auth::id();

        // Cek duplikat
        if (UserCart::where('user_id', $userId)->where('course_id', $course->id)->exists()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kursus sudah ada di keranjang. Silakan periksa keranjang Anda.',
                    'type' => 'info',
                    'cart_count' => $this->getCartCount($userId),
                ], 200);
            }

            return redirect()->back()->with('info', 'Kursus sudah ada di keranjang. Silakan periksa keranjang Anda.');
        }

        UserCart::create([
            'user_id' => $userId,
            'course_id' => $course->id,
        ]);

        $this->syncCartToSession($userId);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kursus berhasil ditambahkan ke keranjang!',
                'type' => 'success',
                'cart_count' => $this->getCartCount($userId),
            ], 200);
        }

        return redirect()->back()->with('success', 'Kursus berhasil ditambahkan ke keranjang!');
    }

    /**
     * Menambahkan kelas ke keranjang.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function storeClass(CourseClass $courseClass)
    {
        $userId = Auth::id();

        // Cek duplikat berdasarkan class_id
        if (UserCart::where('user_id', $userId)->where('class_id', $courseClass->id)->exists()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kelas sudah ada di keranjang. Silakan periksa keranjang Anda.',
                    'type' => 'info',
                    'cart_count' => $this->getCartCount($userId),
                ], 200);
            }

            return redirect()->back()->with('info', 'Kelas sudah ada di keranjang. Silakan periksa keranjang Anda.');
        }

        UserCart::create([
            'user_id' => $userId,
            'class_id' => $courseClass->id,
        ]);

        $this->syncCartToSession($userId);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil ditambahkan ke keranjang!',
                'type' => 'success',
                'cart_count' => $this->getCartCount($userId),
            ], 200);
        }

        return redirect()->back()->with('success', 'Kelas berhasil ditambahkan ke keranjang!');
    }

    /**
     * Menghapus item dari keranjang berdasarkan ID UserCart.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeFromCart($id)
    {
        $cartItem = UserCart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $cartItem->delete();

        $this->syncCartToSession(Auth::id());

        return redirect()->back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    /**
     * Mendapatkan jumlah item keranjang dari database (sumber kebenaran utama).
     */
    private function getCartCount(int $userId): int
    {
        return UserCart::where('user_id', $userId)->count();
    }

    /**
     * Mensinkronkan isi keranjang dari database ke session 'cart'.
     * Struktur array session mendukung kedua tipe (course & class).
     */
    private function syncCartToSession(int $userId): void
    {
        $items = UserCart::where('user_id', $userId)
            ->with([
                'course' => fn ($q) => $q->select('id', 'title', 'price', 'thumbnail_url', 'slug'),
                'class' => fn ($q) => $q->select('id', 'title', 'price', 'thumbnail_url', 'slug'),
            ])
            ->get()
            ->map(function ($item) {
                if ($item->course) {
                    return [
                        'type' => 'course',
                        'id' => $item->course->id,
                        'title' => $item->course->title,
                        'price' => $item->course->price,
                        'quantity' => 1,
                        'thumbnail' => $item->course->thumbnail_url ?? null,
                        'slug' => $item->course->slug,
                    ];
                }

                if ($item->class) {
                    return [
                        'type' => 'class',
                        'id' => $item->class->id,
                        'title' => $item->class->title,
                        'price' => $item->class->price,
                        'quantity' => 1,
                        'thumbnail' => $item->class->thumbnail_url ?? null,
                        'slug' => $item->class->slug,
                    ];
                }

                return null;
            })
            ->filter()
            ->keyBy('id')
            ->toArray();

        session(['cart' => $items]);
    }
}
