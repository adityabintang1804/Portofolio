<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\CertificateCategory;
use Illuminate\Database\Seeder;

class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        $certificates = [
            ['Belajar Machine Learning untuk Pemula', 'Dicoding Indonesia', 'artificial-intelligence', '2025-03-15', true],
            ['Belajar Dasar Pemrograman Web', 'Dicoding Indonesia', 'web-development', '2024-08-20', true],
            ['Memulai Pemrograman dengan Python', 'Dicoding Indonesia', 'data', '2024-11-10', false],
        ];

        foreach ($certificates as $order => [$title, $issuer, $category, $issuedAt, $featured]) {
            Certificate::query()->updateOrCreate(['title' => $title, 'issuer' => $issuer], [
                'certificate_category_id' => CertificateCategory::query()->where('slug', $category)->firstOrFail()->id,
                'issued_at' => $issuedAt, 'credential_id' => 'DEMO-'.str_pad((string) ($order + 1), 4, '0', STR_PAD_LEFT),
                'credential_url' => null, 'certificate_image' => 'placeholders/certificate-placeholder.svg',
                'is_featured' => $featured, 'display_order' => $order + 1, 'is_active' => true,
            ]);
        }
    }
}
