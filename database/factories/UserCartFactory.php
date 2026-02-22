<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserCart>
 */
class UserCartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $course = $this->faker->boolean() ? Course::factory() : null;

        return [
            'user_id' => User::factory(),
            'course_id' => $course,
            'class_id' => ! $course ? CourseClass::factory() : null,
        ];
    }

    public function withCourse(): static
    {
        return $this->state(fn (array $attributes) => [
            'course_id' => Course::factory(),
            'class_id' => null,
        ]);
    }

    public function withClass(): static
    {
        return $this->state(fn (array $attributes) => [
            'course_id' => null,
            'class_id' => CourseClass::factory(),
        ]);
    }
}
