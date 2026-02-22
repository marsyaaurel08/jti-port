<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseClass>
 */
class CourseClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'instructor_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $this->faker->words(3, true),
            'slug' => $this->faker->slug(),
            'thumbnail_url' => 'https://picsum.photos/400/200?random='.$this->faker->numberBetween(1, 1000),
            'description' => $this->faker->paragraph(),
            'level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'price' => $this->faker->randomFloat(2, 49999, 999999),
            'start_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+1 month', '+3 months'),
            'max_capacity' => $this->faker->numberBetween(10, 100),
            'current_enrolled' => $this->faker->numberBetween(0, 50),
            'meeting_link' => $this->faker->optional()->url(),
            'is_active' => $this->faker->boolean(80),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
