<?php

namespace App\Services\AI;

use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Technology;
use Illuminate\Support\Str;

class PortfolioDirectAnswerService
{
    public function resolve(string $message): ?array
    {
        $text = Str::lower($message);
        $profile = Profile::query()->first();

        if (Str::contains($text, ['email', 'kontak', 'hubungi']) && $profile) {
            return $this->answer("Email Aditya adalah {$profile->email}. Anda juga dapat menggunakan formulir kontak.", 'database', [['label' => 'Buka kontak', 'url' => '/contact']]);
        }
        if (Str::contains($text, ['download cv', 'unduh cv', 'resume', 'curriculum vitae'])) {
            return $this->answer('CV Aditya dapat dilihat atau diunduh melalui halaman CV.', 'database', [['label' => 'Buka CV', 'url' => '/cv']]);
        }
        if (Str::contains($text, ['skill', 'kemampuan', 'keahlian'])) {
            $skills = Skill::query()->where('is_active', true)->orderBy('display_order')->limit(12)->pluck('name')->implode(', ');

            return $this->answer('Skill utama Aditya meliputi '.$skills.'.', 'database', [['label' => 'Lihat profil', 'url' => '/about']]);
        }
        if (Str::contains($text, ['proyek', 'project'])) {
            $technology = Technology::query()->where('is_active', true)->get()->first(fn (Technology $technology) => Str::contains($text, [Str::lower($technology->name), $technology->slug]));
            $projects = Project::query()->published()
                ->when($technology, fn ($query) => $query->whereHas('technologies', fn ($query) => $query->whereKey($technology->id)))
                ->orderByDesc('is_featured')->limit(4)->pluck('title');
            $prefix = $technology ? "Proyek yang menggunakan {$technology->name}" : 'Beberapa proyek Aditya';
            $content = $projects->isEmpty() ? 'Belum ada proyek published yang sesuai.' : $prefix.': '.$projects->implode(', ').'.';

            return $this->answer($content, 'database', [['label' => 'Lihat proyek', 'url' => '/projects']]);
        }
        if (Str::contains($text, ['pengalaman', 'experience', 'terbaru'])) {
            $experience = Experience::query()->where('is_active', true)->orderBy('display_order')->first();
            if ($experience) {
                return $this->answer("Pengalaman terbaru Aditya adalah {$experience->position} di {$experience->organization}. {$experience->description}", 'database', [['label' => 'Lihat pengalaman', 'url' => '/experience']]);
            }
        }

        return null;
    }

    private function answer(string $message, string $source, array $actions): array
    {
        return compact('message', 'source', 'actions');
    }
}
