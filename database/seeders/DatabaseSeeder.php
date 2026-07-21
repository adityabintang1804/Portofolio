<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ProfileSeeder::class,
            SiteSettingSeeder::class,
            ProjectCategorySeeder::class,
            TechnologySeeder::class,
            ProjectSeeder::class,
            SkillCategorySeeder::class,
            SkillSeeder::class,
            ExperienceSeeder::class,
            EducationSeeder::class,
            CertificateCategorySeeder::class,
            CertificateSeeder::class,
            ChatbotFaqSeeder::class,
        ]);
    }
}
