<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'frontend' => ['React', 'TypeScript', 'Tailwind CSS'],
            'backend' => ['Laravel', 'PHP', 'MySQL'],
            'artificial-intelligence' => ['Python', 'TensorFlow', 'Scikit-learn'],
            'data' => ['Pandas', 'Data Analysis'],
            'mobile' => ['Flutter'],
            'tools' => ['Git', 'GitHub', 'Figma', 'Docker'],
        ];

        $order = 1;
        foreach ($groups as $categorySlug => $skills) {
            $category = SkillCategory::query()->where('slug', $categorySlug)->firstOrFail();
            foreach ($skills as $name) {
                Skill::query()->updateOrCreate(['slug' => Str::slug($name)], [
                    'skill_category_id' => $category->id, 'name' => $name,
                    'icon_key' => Str::slug($name, ''), 'description' => "Pengalaman menggunakan {$name} dalam proyek dan pembelajaran.",
                    'display_order' => $order++, 'is_active' => true,
                ]);
            }
        }
    }
}
