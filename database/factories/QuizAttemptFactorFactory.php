<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuizAttempt>
 */
class QuizAttemptFactory extends Factory
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
            'quiz_id' => Quiz::factory(),
            'score' => $this->faker->randomFloat(2, 0, 100),
            'started_at' => $this->faker->dateTimeBetween('-3 months'),
            'finished_at' => $this->faker->optional()->dateTimeBetween('-3 months'),
        ];
    }

    public function passed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'score' => $this->faker->numberBetween(70, 100),
                'finished_at' => now(),
            ];
        });
    }

    public function failed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'score' => $this->faker->numberBetween(0, 60),
                'finished_at' => now(),
            ];
        });
    }
}
