<?php

namespace Database\Seeders;

use App\Models\Education;
use Illuminate\Database\Seeder;

class EducationSeeder extends Seeder
{
    public function run(): void
    {
        Education::query()->updateOrCreate(['institution' => 'Universitas Ahmad Dahlan', 'study_program' => 'Informatika'], [
            'degree' => 'Sarjana (S1)', 'location' => 'Yogyakarta', 'logo' => 'placeholders/organization-placeholder.svg',
            'start_year' => 2022, 'end_year' => null,
            'description' => 'Mempelajari software engineering, pengembangan web, artificial intelligence, machine learning, dan pengolahan data.',
            'display_order' => 1, 'is_active' => true,
        ]);
    }
}
