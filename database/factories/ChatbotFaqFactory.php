<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChatbotFaqFactory extends Factory
{
    public function definition(): array
    {
        return [
            'question' => fake()->sentence().'?',
            'answer' => fake()->paragraph(),
            'keywords' => fake()->words(3),
            'priority' => fake()->numberBetween(1, 20),
            'is_active' => true,
        ];
    }
}
