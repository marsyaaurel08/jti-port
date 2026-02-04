<?php

namespace Database\Factories;

use App\Models\Chapter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chapter_id' => Chapter::factory(),
            'title' => $this->faker->sentence(5),
            'type' => $this->faker->randomElement(['video', 'text', 'quiz', 'assignment']),
            'slug' => $this->faker->unique()->slug(),
            'content_url' => $this->faker->optional()->url(),
            'content_text' => $this->faker->optional()->paragraphs(3, true),
            'duration_seconds' => $this->faker->numberBetween(60, 7200),
            'is_free_preview' => $this->faker->boolean(20),
            'sort_order' => $this->faker->numberBetween(1, 20),
        ];
    }

    public function freePreview(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_free_preview' => true,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_free_preview' => false,
        ]);
    }
}
