<?php

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $technologies = [
            ['React', 'react', '#61DAFB'], ['TypeScript', 'typescript', '#3178C6'], ['Tailwind CSS', 'tailwindcss', '#06B6D4'],
            ['Laravel', 'laravel', '#FF2D20'], ['PHP', 'php', '#777BB4'], ['MySQL', 'mysql', '#4479A1'],
            ['Python', 'python', '#3776AB'], ['TensorFlow', 'tensorflow', '#FF6F00'], ['Scikit-learn', 'scikitlearn', '#F7931E'],
            ['Pandas', 'pandas', '#150458'], ['Git', 'git', '#F05032'], ['GitHub', 'github', '#181717'],
            ['Figma', 'figma', '#F24E1E'], ['Docker', 'docker', '#2496ED'], ['Flutter', 'flutter', '#02569B'],
        ];

        foreach ($technologies as $order => [$name, $icon, $color]) {
            Technology::query()->updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name, 'icon_key' => $icon, 'brand_color' => $color,
                'display_order' => $order + 1, 'is_active' => true,
            ]);
        }
    }
}
