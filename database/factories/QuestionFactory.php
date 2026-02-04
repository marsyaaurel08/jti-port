<?php

namespace Database\Factories;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quiz_id' => Quiz::factory(),
            'type' => $this->faker->randomElement(['single_choice', 'multiple_choice', 'boolean', 'essay']),
            'question_config' => [
                'question' => $this->faker->sentence(10),
                'options' => $this->faker->randomElements(['A', 'B', 'C', 'D'], 4),
            ],
            'score_weight' => $this->faker->numberBetween(1, 5),
        ];
    }

    public function singleChoice(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'single_choice',
            'question_config' => [
                'question' => $this->faker->sentence(10),
                'options' => ['A', 'B', 'C', 'D'],
            ],
        ]);
    }

    public function multipleChoice(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'multiple_choice',
            'question_config' => [
                'question' => $this->faker->sentence(10),
                'options' => ['A', 'B', 'C', 'D', 'E'],
            ],
        ]);
    }

    public function boolean(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'boolean',
            'question_config' => [
                'question' => $this->faker->sentence(10),
                'options' => ['true', 'false'],
            ],
        ]);
    }

    public function essay(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'essay',
            'question_config' => [
                'question' => $this->faker->sentence(10),
            ],
        ]);
    }
}
