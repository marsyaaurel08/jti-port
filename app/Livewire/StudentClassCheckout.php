<?php

namespace App\Livewire;

use App\Models\CourseClass;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StudentClassCheckout extends BaseProductCheckout
{
    public CourseClass $class;

    public string $productType = 'class';

    protected function getProduct()
    {
        return $this->class;
    }

    protected function getProductType(): string
    {
        return $this->productType;
    }

    protected function getCacheKey(string $slug): string
    {
        return "class_checkout:{$slug}";
    }

    protected function getPromoProductField(): string
    {
        return 'class_id';
    }

    protected function getProductIdField(): string
    {
        return 'class_id';
    }

    protected function getProductShowRoute(): string
    {
        return 'classes.show';
    }

    protected function loadProduct(string $slug): void
    {
        $cacheKey = $this->getCacheKey($slug);
        $this->class = Cache::remember($cacheKey, 3600, function () use ($slug) {
            return CourseClass::where('slug', $slug)->firstOrFail();
        });
    }

    protected function clearCartItems(): void
    {
        // Remove cart item for this class
        \App\Models\UserCart::where('user_id', Auth::id())
            ->where('class_id', $this->class->id)
            ->delete();
    }

    protected function createOrderItems(Order $order): void
    {
        // Buat Order Item
        OrderItem::create([
            'order_id' => $order->id,
            'course_id' => $this->class->course_id,
            'class_id' => $this->class->id,
            'price_at_purchase' => $this->getTotalAmount(),
            'item_name' => $this->class->title,
        ]);
    }
}
