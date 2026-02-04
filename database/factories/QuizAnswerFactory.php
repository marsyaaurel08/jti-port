<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\QuizAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuizAnswer>
 */
class QuizAnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quiz_attempt_id' => QuizAttempt::factory(),
            'question_id' => Question::factory(),
            'user_answer' => $this->faker->optional()->sentence(8),
            'is_correct' => $this->faker->boolean(50),
            'score_obtained' => $this->faker->randomFloat(2, 0, 10),
        ];
    }

    public function correct(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_correct' => true,
            'score_obtained' => $this->faker->randomFloat(2, 5, 10),
        ]);
    }

    public function incorrect(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_correct' => false,
            'score_obtained' => 0,
        ]);
    }
}
