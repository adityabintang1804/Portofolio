<?php

namespace Database\Seeders;

use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Artificial Intelligence', 'Machine Learning', 'Data Analysis', 'Web Development', 'Mobile Development', 'Academic Project'];
        foreach ($categories as $order => $name) {
            ProjectCategory::query()->updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'description' => "Kumpulan proyek bidang {$name}.",
                'display_order' => $order + 1,
                'is_active' => true,
            ]);
        }
    }
}
