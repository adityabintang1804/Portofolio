<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        Experience::query()->updateOrCreate(['position' => 'AI Engineer Learning Path', 'organization' => 'Coding Camp powered by DBS Foundation'], [
            'location' => 'Remote', 'logo' => 'placeholders/organization-placeholder.svg',
            'start_date' => '2025-02-01', 'end_date' => '2025-07-31', 'is_current' => false,
            'description' => 'Program pembelajaran intensif yang mencakup machine learning, pengembangan solusi AI, dan praktik proyek kolaboratif.',
            'responsibilities' => ['Menyelesaikan modul machine learning dan deep learning.', 'Mengembangkan proyek capstone secara kolaboratif.', 'Mempresentasikan hasil dan evaluasi model.'],
            'technologies' => ['Python', 'TensorFlow', 'Scikit-learn'], 'display_order' => 1, 'is_active' => true,
        ]);

        Experience::query()->updateOrCreate(['position' => 'Practicum Assistant', 'organization' => 'Universitas Ahmad Dahlan'], [
            'location' => 'Yogyakarta', 'logo' => 'placeholders/organization-placeholder.svg',
            'start_date' => '2024-09-01', 'end_date' => null, 'is_current' => true,
            'description' => 'Mendampingi mahasiswa dalam memahami materi praktikum pemrograman dan menyelesaikan kendala teknis.',
            'responsibilities' => ['Mendampingi sesi praktikum.', 'Memeriksa tugas dan memberikan umpan balik.', 'Membantu pemecahan masalah kode.'],
            'technologies' => ['Programming', 'Git'], 'display_order' => 2, 'is_active' => true,
        ]);
    }
}
