<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Portfolio Aditya', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Portfolio mahasiswa Informatika dengan fokus web development, AI, dan data.', 'type' => 'string', 'group' => 'general'],
            ['key' => 'projects_title', 'value' => 'Proyek Pilihan', 'type' => 'string', 'group' => 'content'],
            ['key' => 'projects_description', 'value' => 'Studi kasus pilihan dari proyek akademik dan pengembangan produk.', 'type' => 'string', 'group' => 'content'],
            ['key' => 'contact_title', 'value' => 'Mari membangun sesuatu bersama', 'type' => 'string', 'group' => 'content'],
            ['key' => 'contact_description', 'value' => 'Saya terbuka untuk kesempatan magang, pekerjaan, dan kolaborasi teknologi.', 'type' => 'string', 'group' => 'content'],
            ['key' => 'footer_text', 'value' => 'Dibangun dengan Laravel, React, dan rasa ingin tahu.', 'type' => 'string', 'group' => 'content'],
            ['key' => 'default_meta_title', 'value' => 'Aditya — Informatics Student & Developer', 'type' => 'string', 'group' => 'seo'],
            ['key' => 'default_meta_description', 'value' => 'Kenali proyek, pengalaman, dan kemampuan Aditya di bidang web, AI, dan data.', 'type' => 'string', 'group' => 'seo'],
            ['key' => 'default_og_image', 'value' => 'placeholders/project-placeholder.svg', 'type' => 'string', 'group' => 'seo'],
            ['key' => 'chatbot_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'chatbot'],
            ['key' => 'chatbot_welcome_message', 'value' => 'Halo! Saya dapat membantu Anda mengenal profil, skill, dan proyek Aditya.', 'type' => 'string', 'group' => 'chatbot'],
            ['key' => 'chatbot_suggested_questions', 'value' => json_encode(['Apa skill utama Aditya?', 'Tampilkan proyek Laravel', 'Bagaimana cara menghubungi Aditya?']), 'type' => 'json', 'group' => 'chatbot'],
            ['key' => 'default_theme', 'value' => 'system', 'type' => 'string', 'group' => 'appearance'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::query()->updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
