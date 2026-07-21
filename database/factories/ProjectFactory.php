<?php

namespace Database\Factories;

use App\Models\ProjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'project_category_id' => ProjectCategory::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'short_description' => fake()->sentence(15),
            'overview' => fake()->paragraphs(2, true),
            'status' => 'published',
            'published_at' => now(),
        ];
    }
}
