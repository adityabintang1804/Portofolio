<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        Profile::query()->updateOrCreate(['id' => 1], [
            'name' => 'Aditya',
            'headline' => 'Informatics Student, AI Enthusiast, and Web Developer',
            'hero_badge' => 'Halo, saya Aditya',
            'hero_description' => 'Saya membangun pengalaman digital melalui pengembangan web, artificial intelligence, dan pengolahan data.',
            'about_description' => 'Mahasiswa Informatika yang berfokus pada pengembangan aplikasi web, kecerdasan buatan, machine learning, analisis data, dan software development. Saya senang mengubah masalah nyata menjadi produk digital yang terukur dan mudah digunakan.',
            'profile_image' => 'placeholders/profile-placeholder.svg',
            'location' => 'Yogyakarta, Indonesia',
            'email' => 'aditya@example.com',
            'phone' => null,
            'linkedin_url' => 'https://www.linkedin.com/',
            'github_url' => 'https://github.com/',
            'availability_status' => 'available',
            'availability_text' => 'Terbuka untuk magang dan kolaborasi',
            'cv_file' => null,
        ]);
    }
}
