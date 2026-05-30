<?php

namespace App\Livewire\Pages;

use App\Models\CourseClass;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Class Details')]
class ClassShow extends Component
{
    public string $slug = '';
    public ?CourseClass $class = null;
    public bool $isUserEnrolled = false;
    public ?Order $pendingOrder = null;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->loadClassData();
    }

    public function loadClassData()
    {
        $userId = auth()->id();

        // Check if user is enrolled
        $this->isUserEnrolled = $userId ? CourseClass::withTrashed()
            ->whereHas('course', function ($q) {
                $q->where('slug', $this->slug)->withTrashed();
            })->whereHas('enrollments', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->exists() : false;

        // Load course class from Redis cache first, fallback to database
        $cacheKey = "class_detail:{$this->slug}";
        $this->class = cache()->remember($cacheKey, 3600, function () {
            return CourseClass::withTrashed()
                ->where('slug', $this->slug)
                ->with('course.chapters.lessons', 'course.category', 'instructor')
                ->firstOrFail();
        });

        // Check for pending order
        if ($userId && $this->class) {
            $this->pendingOrder = Order::where('user_id', $userId)
                ->where('status', 'pending')
                ->whereHas('items', function ($query) {
                    $query->where('class_id', $this->class->id);
                })
                ->latest()
                ->first();
        }
    }

    public function cancelPendingOrder(): void
    {
        try {
            $user = Auth::user();
            if (!$user || !$this->pendingOrder) {
                $this->dispatch('error', message: 'Pesanan tidak valid atau tidak dapat dibatalkan.');
                return;
            }

            // Verify ownership and status
            if ($this->pendingOrder->user_id !== $user->id || $this->pendingOrder->status !== 'pending') {
                $this->dispatch('error', message: 'Pesanan tidak valid atau tidak dapat dibatalkan.');
                return;
            }

            // Cancel order
            $this->pendingOrder->update(['status' => 'cancelled']);

            // Activity log
            activity('Order')
                ->causedBy($user)
                ->performedOn($this->pendingOrder)
                ->withProperties(['reason' => 'User membatalkan untuk order baru'])
                ->log('cancelled');

            // Reload pending order data and redirect to checkout
            $this->loadClassData();

            $this->redirect(route('student.checkout.class', $this->slug));
        } catch (\Exception $e) {
            Log::error('Failed to cancel pending order: ' . $e->getMessage());
            $this->dispatch('error', message: 'Terjadi kesalahan saat membatalkan pesanan.');
        }
    }

    public function render()
    {
        return view('livewire.pages.class-show', [
            'class' => $this->class,
            'is_user_enrolled' => $this->isUserEnrolled,
            'pendingOrder' => $this->pendingOrder,
        ]);
    }
}
