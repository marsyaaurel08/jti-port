<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'course_id' => Course::factory(),
            'class_id' => CourseClass::factory(),
            'price_at_purchase' => $this->faker->randomFloat(2, 49999, 999999),
            'item_name' => $this->faker->words(3, true),
        ];
    }

    public function course(): static
    {
        return $this->state(fn(array $attributes) => [
            'course_id' => Course::factory(),
            'class_id' => null,
        ]);
    }

    public function class(): static
    {
        return $this->state(fn(array $attributes) => [
            'course_id' => null,
            'class_id' => CourseClass::factory(),
        ]);
    }
}
