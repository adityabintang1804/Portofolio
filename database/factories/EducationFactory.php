<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EducationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'institution' => fake()->company(),
            'degree' => 'Sarjana',
            'study_program' => 'Informatika',
            'location' => fake()->city(),
            'start_year' => 2022,
            'end_year' => null,
            'description' => fake()->paragraph(),
            'is_active' => true,
        ];
    }
}
