<?php

namespace App\Livewire\Pages;

use App\Models\Course;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Course Details')]
class CourseShow extends Component
{
    public string $slug = '';
    public ?Course $course = null;
    public bool $isUserEnrolled = false;
    public ?Order $pendingOrder = null;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->loadCourseData();
    }

    public function loadCourseData()
    {
        $userId = auth()->id();

        // Check if user is enrolled
        $this->isUserEnrolled = $userId ? Course::where('slug', $this->slug)
            ->whereHas('enrollments', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->exists() : false;

        // Load course from Redis cache first, fallback to database
        $cacheKey = "course_detail:{$this->slug}";
        $this->course = cache()->remember($cacheKey, 3600, function () {
            return Course::where('slug', $this->slug)
                ->with([
                    'category',
                    'chapters' => function ($query) {
                        $query->orderBy('id', 'asc');
                    },
                    'chapters.lessons',
                    'reviews.user',
                ])
                ->withCount('enrollments')
                ->firstOrFail();
        });

        // Check for pending order
        if ($userId && $this->course) {
            $this->pendingOrder = Order::where('user_id', $userId)
                ->where('status', 'pending')
                ->whereHas('items', function ($query) {
                    $query->where('course_id', $this->course->id);
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
            $this->loadCourseData();

            $this->redirect(route('student.checkout.course', $this->slug));
        } catch (\Exception $e) {
            Log::error('Failed to cancel pending order: ' . $e->getMessage());
            $this->dispatch('error', message: 'Terjadi kesalahan saat membatalkan pesanan.');
        }
    }

    public function render()
    {
        return view('livewire.pages.course-show', [
            'course' => $this->course,
            'is_user_enrolled' => $this->isUserEnrolled,
            'pendingOrder' => $this->pendingOrder,
        ]);
    }
}
