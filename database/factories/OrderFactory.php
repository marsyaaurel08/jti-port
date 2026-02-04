<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
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
            'invoice_number' => 'INV-' . $this->faker->unique()->numerify('##########'),
            'subtotal' => $this->faker->randomFloat(2, 50000, 1000000),
            'discount_amount' => $this->faker->randomFloat(2, 0, 100000),
            'total_amount' => $this->faker->randomFloat(2, 50000, 1000000),
            'status' => $this->faker->randomElement(['pending', 'paid', 'failed', 'expired']),
            'payment_method' => $this->faker->randomElement(['credit_card', 'bank_transfer', 'e_wallet']),
            'payment_proof' => $this->faker->optional()->url(),
            'paid_at' => $this->faker->optional()->dateTimeBetween('-1 month'),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'failed',
        ]);
    }
}
