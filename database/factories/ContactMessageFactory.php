<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ContactMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'organization' => fake()->company(),
            'subject' => fake()->sentence(5),
            'message' => fake()->paragraph(),
            'status' => 'unread',
        ];
    }
}
