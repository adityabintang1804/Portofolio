<?php

namespace Database\Seeders;

use App\Models\SkillCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Frontend', 'Backend', 'Artificial Intelligence', 'Data', 'Mobile', 'Tools'] as $order => $name) {
            SkillCategory::query()->updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name, 'description' => "Kemampuan dan teknologi bidang {$name}.",
                'display_order' => $order + 1, 'is_active' => true,
            ]);
        }
    }
}
