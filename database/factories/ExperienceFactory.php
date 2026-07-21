<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExperienceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'position' => fake()->jobTitle(),
            'organization' => fake()->company(),
            'location' => fake()->city(),
            'start_date' => fake()->dateTimeBetween('-3 years', '-1 year'),
            'end_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'description' => fake()->paragraph(),
            'responsibilities' => [fake()->sentence(), fake()->sentence()],
            'technologies' => ['Laravel', 'React'],
            'is_active' => true,
        ];
    }
}
