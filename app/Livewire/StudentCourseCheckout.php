<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class StudentCourseCheckout extends BaseProductCheckout
{
    public Course $course;

    public string $productType = 'course';

    protected function getProduct()
    {
        return $this->course;
    }

    protected function getProductType(): string
    {
        return $this->productType;
    }

    protected function getCacheKey(string $slug): string
    {
        return "course_checkout:{$slug}";
    }

    protected function getPromoProductField(): string
    {
        return 'course_id';
    }

    protected function getProductIdField(): string
    {
        return 'course_id';
    }

    protected function getProductShowRoute(): string
    {
        return 'courses.show';
    }

    protected function loadProduct(string $slug): void
    {
        // Load course from Redis cache first, fallback to database
        $cacheKey = $this->getCacheKey($slug);
        $this->course = cache()->remember($cacheKey, 3600, function () use ($slug) {
            return Course::where('slug', $slug)->firstOrFail();
        });
    }

    protected function clearCartItems(): void
    {
        // Remove cart item for this course
        \App\Models\UserCart::where('user_id', Auth::id())
            ->where('course_id', $this->course->id)
            ->delete();
    }

    protected function createOrderItems(Order $order): void
    {
        // Buat Order Item
        OrderItem::create([
            'order_id' => $order->id,
            'course_id' => $this->course->id,
            'price_at_purchase' => $this->getTotalAmount(),
            'item_name' => $this->course->title,
        ]);
    }
}
