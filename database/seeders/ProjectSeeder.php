<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectImage;
use App\Models\Technology;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'title' => 'SkillMatch', 'slug' => 'skillmatch-ai-career-platform', 'category' => 'artificial-intelligence',
                'short_description' => 'Platform rekomendasi karier yang mencocokkan skill pengguna dengan kebutuhan posisi menggunakan AI.',
                'overview' => 'SkillMatch membantu mahasiswa memahami kesiapan karier melalui pemetaan kemampuan, rekomendasi posisi, dan rencana belajar yang relevan.',
                'problem' => 'Informasi lowongan sering tidak menjelaskan kesenjangan skill secara personal sehingga kandidat kesulitan menentukan prioritas belajar.',
                'goal' => 'Menyediakan rekomendasi karier yang transparan, relevan, dan dapat ditindaklanjuti.',
                'role' => 'Full-stack developer dan AI engineer',
                'technologies' => ['react', 'typescript', 'laravel', 'python', 'mysql'], 'featured' => true,
            ],
            [
                'title' => 'Editorial Desk Dashboard', 'slug' => 'editorial-desk-dashboard', 'category' => 'web-development',
                'short_description' => 'Dashboard kolaborasi editorial untuk mengelola ide, jadwal, status, dan publikasi artikel.',
                'overview' => 'Aplikasi ini memusatkan alur editorial dari perencanaan hingga publikasi dengan tampilan status yang mudah dipindai.',
                'problem' => 'Koordinasi konten yang tersebar membuat status tulisan dan tanggung jawab editor sulit dilacak.',
                'goal' => 'Membuat workflow editorial yang konsisten dan mengurangi pekerjaan koordinasi manual.',
                'role' => 'Frontend developer',
                'technologies' => ['react', 'typescript', 'tailwind-css', 'figma'], 'featured' => true,
            ],
            [
                'title' => 'Pneumonia Detection', 'slug' => 'pneumonia-detection', 'category' => 'machine-learning',
                'short_description' => 'Eksperimen klasifikasi citra X-ray untuk mendeteksi indikasi pneumonia menggunakan deep learning.',
                'overview' => 'Proyek akademik untuk mengevaluasi pipeline preprocessing, pelatihan, serta pengukuran performa model klasifikasi citra medis.',
                'problem' => 'Interpretasi citra membutuhkan keahlian dan sistem pendukung harus diuji secara hati-hati sebelum digunakan.',
                'goal' => 'Mempelajari penerapan convolutional neural network dan evaluasi model pada citra medis.',
                'role' => 'Machine learning engineer',
                'technologies' => ['python', 'tensorflow', 'pandas'], 'featured' => true,
            ],
            [
                'title' => 'Analisis Sentimen MyXL', 'slug' => 'analisis-sentimen-myxl', 'category' => 'data-analysis',
                'short_description' => 'Analisis sentimen ulasan aplikasi MyXL untuk menemukan tema masalah dan persepsi pengguna.',
                'overview' => 'Data ulasan dibersihkan, dieksplorasi, dan diklasifikasikan untuk menghasilkan ringkasan sentimen yang dapat digunakan sebagai masukan produk.',
                'problem' => 'Volume ulasan yang tinggi menyulitkan identifikasi tema keluhan secara manual.',
                'goal' => 'Mengubah ulasan tidak terstruktur menjadi insight yang mudah dipahami.',
                'role' => 'Data analyst',
                'technologies' => ['python', 'scikit-learn', 'pandas'], 'featured' => false,
            ],
            [
                'title' => 'Website SD Muhammadiyah Komplek Kolombo', 'slug' => 'website-sd-muhammadiyah-komplek-kolombo', 'category' => 'web-development',
                'short_description' => 'Website sekolah dengan pengelolaan berita, profil, prestasi, guru, dan konten akademik melalui dashboard.',
                'overview' => 'Platform informasi sekolah yang menyatukan halaman publik responsif dengan dashboard pengelolaan konten.',
                'problem' => 'Informasi sekolah sulit diperbarui ketika seluruh perubahan bergantung pada pengembang.',
                'goal' => 'Memberikan sistem publikasi mandiri yang mudah digunakan pengelola sekolah.',
                'role' => 'Full-stack developer',
                'technologies' => ['laravel', 'php', 'mysql'], 'featured' => true,
            ],
            [
                'title' => 'Speech Recognition', 'slug' => 'speech-recognition-experiment', 'category' => 'artificial-intelligence',
                'short_description' => 'Eksperimen pengenalan suara untuk mempelajari preprocessing audio dan inferensi model.',
                'overview' => 'Proyek eksplorasi untuk memahami pipeline data audio, ekstraksi fitur, dan evaluasi pengenalan ucapan.',
                'problem' => 'Variasi suara dan kondisi lingkungan menyebabkan hasil transkripsi tidak konsisten.',
                'goal' => 'Mengevaluasi pendekatan preprocessing audio pada beberapa kondisi rekaman.',
                'role' => 'AI engineer',
                'technologies' => ['python', 'tensorflow'], 'featured' => false, 'status' => 'draft',
            ],
        ];

        foreach ($projects as $order => $data) {
            $project = Project::query()->updateOrCreate(['slug' => $data['slug']], [
                'project_category_id' => ProjectCategory::query()->where('slug', $data['category'])->valueOrFail('id'),
                'title' => $data['title'],
                'short_description' => $data['short_description'],
                'overview' => $data['overview'],
                'background' => 'Proyek dikembangkan dari kebutuhan nyata dan proses eksplorasi teknis yang terdokumentasi.',
                'problem' => $data['problem'],
                'goal' => $data['goal'],
                'role' => $data['role'],
                'process' => 'Riset kebutuhan, perancangan solusi, implementasi bertahap, pengujian, dan evaluasi hasil.',
                'challenges' => 'Menyeimbangkan akurasi teknis, kemudahan penggunaan, dan keterbatasan waktu pengembangan.',
                'solution' => 'Membagi implementasi menjadi iterasi kecil dengan validasi hasil pada setiap tahap.',
                'result' => 'Menghasilkan prototipe yang dapat didemonstrasikan dan menjadi dasar pengembangan lanjutan.',
                'thumbnail' => 'placeholders/project-placeholder.svg',
                'status' => $data['status'] ?? 'published',
                'is_featured' => $data['featured'],
                'display_order' => $order + 1,
                'published_at' => isset($data['status']) ? null : now()->subDays($order),
                'seo_title' => $data['title'].' — Studi Kasus',
                'seo_description' => $data['short_description'],
            ]);

            $technologyIds = Technology::query()->whereIn('slug', $data['technologies'])->pluck('id');
            $project->technologies()->sync($technologyIds);
            ProjectImage::query()->updateOrCreate(
                ['project_id' => $project->id, 'display_order' => 1],
                ['image' => 'placeholders/project-placeholder.svg', 'alt_text' => 'Tampilan proyek '.$project->title, 'caption' => 'Placeholder galeri proyek'],
            );
        }
    }
}
