<?php

namespace Database\Seeders;

use App\Models\CertificateCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CertificateCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Artificial Intelligence', 'Web Development', 'Data', 'Professional'] as $order => $name) {
            CertificateCategory::query()->updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name, 'display_order' => $order + 1, 'is_active' => true,
            ]);
        }
    }
}
