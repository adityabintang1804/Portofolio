<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'image' => 'placeholders/project-placeholder.svg',
            'alt_text' => fake()->sentence(5),
        ];
    }
}
