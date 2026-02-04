<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseProgress>
 */
class CourseProgressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'enrollment_id' => Enrollment::factory(),
            'lesson_id' => Lesson::factory(),
            'is_completed' => $this->faker->boolean(30),
            'last_watched_at' => $this->faker->optional()->dateTimeBetween('-1 month'),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_completed' => true,
            'last_watched_at' => now(),
        ]);
    }

    public function notCompleted(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_completed' => false,
            'last_watched_at' => null,
        ]);
    }
}
