<?php

namespace Database\Factories;

use App\Enums\PollStatus;
use App\Models\Poll;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Poll>
 */
class PollFactory extends Factory
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
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'status' => PollStatus::Open,
        ];
    }

    /**
     * Indicate that the poll is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PollStatus::Closed,
            'closed_at' => now(),
        ]);
    }
}
