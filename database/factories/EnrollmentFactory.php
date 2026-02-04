<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Order;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $enrolledAt = $this->faker->dateTimeBetween('-6 months');

        return [
            'user_id' => rand(1, 5),
            'course_id' => Course::factory(),
            'class_id' => CourseClass::factory(),
            'order_id' => Order::factory(),
            'enrolled_at' => $enrolledAt,
            'expires_at' => $this->faker->optional()->dateTimeBetween($enrolledAt, '+1 year'),
            'is_completed' => $this->faker->boolean(20),
            'completed_at' => $this->faker->optional()->dateTimeBetween($enrolledAt),
            'certificate_code' => $this->faker->optional()->bothify('##########'),
            'certificate_url' => $this->faker->optional()->url(),
            'completed_lesson_count' => $this->faker->numberBetween(0, 50),
            'last_accessed_lesson_id' => Lesson::factory(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }

    public function notCompleted(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_completed' => false,
            'completed_at' => null,
        ]);
    }
}
