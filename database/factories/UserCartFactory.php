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
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'class_id' => CourseClass::factory(),
            'started_at' => $this->faker->dateTimeBetween('-6 months'),
            'finished_at' => $this->faker->optional()->dateTimeBetween('-6 months'),
        ];
    }

    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'finished_at' => null,
        ]);
    }

    public function finished(): static
    {
        return $this->state(fn(array $attributes) => [
            'finished_at' => now(),
        ]);
    }
}
