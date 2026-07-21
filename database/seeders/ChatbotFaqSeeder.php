<?php

namespace Database\Seeders;

use App\Models\ChatbotFaq;
use Illuminate\Database\Seeder;

class ChatbotFaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['Apa skill utama Aditya?', 'Aditya berfokus pada Laravel, React, TypeScript, Python, machine learning, dan analisis data.', ['skill', 'kemampuan', 'teknologi'], 'Lihat skill', '/about', 10],
            ['Apa proyek unggulan Aditya?', 'Beberapa proyek unggulan Aditya adalah SkillMatch, Editorial Desk Dashboard, Pneumonia Detection, dan website sekolah berbasis CMS.', ['proyek', 'project', 'unggulan'], 'Lihat proyek', '/projects', 20],
            ['Apakah Aditya tersedia untuk magang?', 'Ya, status portfolio saat ini menunjukkan Aditya terbuka untuk kesempatan magang dan kolaborasi.', ['magang', 'internship', 'tersedia'], 'Hubungi Aditya', '/contact', 30],
            ['Bagaimana cara menghubungi Aditya?', 'Anda dapat menggunakan formulir kontak atau membuka tautan LinkedIn yang tersedia pada website.', ['kontak', 'email', 'hubungi'], 'Buka kontak', '/contact', 40],
            ['Bagaimana cara mengunduh CV?', 'Gunakan tombol unduh CV pada navbar atau halaman CV. Jika file belum tersedia, website akan menampilkan pemberitahuan.', ['cv', 'resume', 'download'], 'Buka CV', '/cv', 50],
        ];

        foreach ($faqs as [$question, $answer, $keywords, $label, $url, $priority]) {
            ChatbotFaq::query()->updateOrCreate(['question' => $question], [
                'answer' => $answer, 'keywords' => $keywords, 'action_label' => $label,
                'action_url' => $url, 'priority' => $priority, 'is_active' => true,
            ]);
        }
    }
}
