<?php

namespace Database\Factories;

use App\Models\Option;
use App\Models\Poll;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Option>
 */
class OptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'poll_id' => Poll::factory(),
            'name' => fake()->sentence(2),
            'image_url' => fake()->optional()->imageUrl(),
            'description' => fake()->optional()->sentence(),
            'sort_order' => 0,
        ];
    }
}
