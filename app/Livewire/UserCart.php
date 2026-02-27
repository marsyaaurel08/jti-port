<?php

namespace App\Livewire;

use App\Models\UserCart as UserCartModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]

class UserCart extends Component
{
    public $cartItems = [];

    public $totalPrice = 0;

    public $cartCount = 0;

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    public function mount(): void
    {
        $this->loadCart();
        $this->syncCartToSession(Auth::id());
    }

    public function loadCart(): void
    {
        $userId = Auth::id();

        $cartItems = UserCartModel::where('user_id', $userId)
            ->with([
                'course' => fn($q) => $q->select('id', 'title', 'price', 'thumbnail_url', 'slug', 'category_id'),
                'class' => fn($q) => $q->select('id', 'title', 'price', 'thumbnail_url', 'slug', 'category_id'),
                'course.category:id,name',
            ])
            ->get();

        $this->cartItems = $cartItems->map(fn($item) => [
            'id' => $item->id,
            'course_id' => $item->course_id,
            'class_id' => $item->class_id,
            'course' => $item->course ? $item->course->toArray() : null,
            'class' => $item->class ? $item->class->toArray() : null,
        ])->toArray();

        $this->cartCount = count($this->cartItems);
        $this->calculateTotalPrice();
    }

    public function calculateTotalPrice(): void
    {
        $this->totalPrice = collect($this->cartItems)->sum(function ($item) {
            $course = $item['course'] ?? null;
            $class = $item['class'] ?? null;

            if ($course && isset($course['price'])) {
                return $course['price'];
            }
            if ($class && isset($class['price'])) {
                return $class['price'];
            }

            return 0;
        });
    }

    public function removeItem($id): void
    {
        try {
            UserCartModel::destroy($id);
            $this->syncCartToSession(Auth::id());
            $this->loadCart();
            $this->dispatch('cart-updated', count: $this->cartCount);
            session()->flash('success', 'Item berhasil dihapus dari keranjang!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus item!');
        }
    }

    /**
     * Handle refresh cart dari cross-tab communication atau page visibility
     * Listener ini di-trigger oleh event 'refreshCart' dari JavaScript
     */
    #[On('refreshCart')]
    public function refreshCart(): void
    {
        $this->loadCart();
    }

    private function syncCartToSession(int $userId): void
    {
        $items = UserCartModel::where('user_id', $userId)
            ->with([
                'course' => fn($q) => $q->select('id', 'title', 'price', 'thumbnail_url', 'slug'),
                'class' => fn($q) => $q->select('id', 'title', 'price', 'thumbnail_url', 'slug'),
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

    public function render()
    {
        return view('livewire.user-cart');
    }
}
