<?php

namespace Database\Factories;

use App\Models\ScrapeResult;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScrapeResult>
 */
class ScrapeResultFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => fake()->url(),
            'status' => 'completed',
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'image_url' => fake()->optional()->imageUrl(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'title' => null,
            'description' => null,
            'image_url' => null,
        ]);
    }

    public function failed(string $message = 'Scrape failed'): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'title' => null,
            'description' => null,
            'image_url' => null,
            'error_message' => $message,
        ]);
    }

    public function withScrapingBee(): static
    {
        return $this->state(fn (array $attributes) => [
            'used_scrapingbee' => true,
        ]);
    }

    public function withPremiumProxy(): static
    {
        return $this->state(fn (array $attributes) => [
            'used_scrapingbee' => true,
            'used_premium_proxy' => true,
        ]);
    }
}
