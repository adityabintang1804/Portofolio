<?php

namespace Database\Factories;

use App\Models\SkillCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SkillFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'skill_category_id' => SkillCategory::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'is_active' => true,
        ];
    }
}
