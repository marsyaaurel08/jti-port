<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
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
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->paragraph(4),
        ];
    }

    public function excellent(): static
    {
        return $this->state(fn(array $attributes) => [
            'rating' => 5,
        ]);
    }

    public function good(): static
    {
        return $this->state(fn(array $attributes) => [
            'rating' => 4,
        ]);
    }

    public function average(): static
    {
        return $this->state(fn(array $attributes) => [
            'rating' => 3,
        ]);
    }

    public function poor(): static
    {
        return $this->state(fn(array $attributes) => [
            'rating' => $this->faker->numberBetween(1, 2),
        ]);
    }
}
