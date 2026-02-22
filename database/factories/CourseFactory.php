<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'title' => $this->faker->words(4, true),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 49999, 999999),
            'level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'thumbnail_url' => 'https://picsum.photos/400/200?random='.$this->faker->numberBetween(1, 1000),
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),
            'total_duration' => $this->faker->numberBetween(0, 100),
            'total_lessons' => $this->faker->numberBetween(0, 50),
            'avg_rating' => $this->faker->randomFloat(2, 0, 5),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }
}
