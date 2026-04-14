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
        //
    }

    public function storeCourse(Course $course)
    {
        return $this->storeToCart(
            'course_id',
            $course->id,
            'Kursus',
            $course
        );
    }

    public function storeClass(CourseClass $courseClass)
    {
        return $this->storeToCart(
            'class_id',
            $courseClass->id,
            'Kelas',
            $courseClass
        );
    }

    public function removeFromCart($id)
    {
        $cartItem = UserCart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $cartItem->delete();

        $this->syncCartToSession(Auth::id());

        return redirect()->back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    private function storeToCart(string $column, int $id, string $type, $item): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $userId = Auth::id();

        if (UserCart::where('user_id', $userId)->where($column, $id)->exists()) {
            return $this->cartResponse(
                false,
                "{$type} sudah ada di keranjang. Silakan periksa keranjang Anda.",
                'info',
                $userId
            );
        }

        UserCart::create([
            'user_id' => $userId,
            $column => $id,
        ]);

        $this->syncCartToSession($userId);

        return $this->cartResponse(
            true,
            "{$type} berhasil ditambahkan ke keranjang!",
            'success',
            $userId
        );
    }

    private function cartResponse(bool $success, string $message, string $type, int $userId): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $cartCount = $this->getCartCount($userId);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $message,
                'type' => $type,
                'cart_count' => $cartCount,
            ], 200);
        }

        return redirect()->back()->with($type, $message);
    }

    private function getCartCount(int $userId): int
    {
        return UserCart::where('user_id', $userId)->count();
    }

    private function syncCartToSession(int $userId): void
    {
        $items = UserCart::where('user_id', $userId)
            ->with([
                'course' => fn ($q) => $q->select('id', 'title', 'price', 'thumbnail_url', 'slug'),
                'class' => fn ($q) => $q->select('id', 'title', 'price', 'thumbnail_url', 'slug'),
            ])
            ->get()
            ->map(fn ($item) => $this->formatCartItem($item))
            ->filter()
            ->keyBy('id')
            ->toArray();

        session(['cart' => $items]);
    }

    private function formatCartItem($item): ?array
    {
        $type = $item->course ? 'course' : ($item->class ? 'class' : null);

        if (!$type) return null;

        $data = $item->{$type};

        return [
            'type' => $type,
            'id' => $data->id,
            'title' => $data->title,
            'price' => $data->price,
            'quantity' => 1,
            'thumbnail' => $data->thumbnail_url ?? null,
            'slug' => $data->slug,
        ];
    }
}
