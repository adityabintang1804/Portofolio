<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'headline' => 'Mahasiswa Informatika dan Web Developer',
            'hero_badge' => 'Terbuka untuk kesempatan baru',
            'hero_description' => fake()->paragraph(),
            'about_description' => fake()->paragraphs(3, true),
            'location' => fake()->city(),
            'email' => fake()->unique()->safeEmail(),
            'availability_status' => 'available',
            'availability_text' => 'Terbuka untuk magang dan kolaborasi',
        ];
    }
}
