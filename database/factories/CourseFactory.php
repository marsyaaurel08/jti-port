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
            'category_id' => rand(1, 5),
            'title' => $this->faker->sentence(4),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->paragraphs(3, true),
            'price' => $this->faker->randomFloat(2, 49999, 999999),
            'level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'thumbnail_url' => 'https://picsum.photos/400/200?random=' . $this->faker->numberBetween(1, 1000),
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),
            'total_duration' => $this->faker->numberBetween(0, 10000),
            'total_lessons' => $this->faker->numberBetween(0, 100),
            'avg_rating' => $this->faker->randomFloat(2, 0, 5),
        ];
    }

    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'published',
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'archived',
        ]);
    }
}
