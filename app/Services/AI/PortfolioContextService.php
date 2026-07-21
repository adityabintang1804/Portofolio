<?php

namespace App\Services\AI;

use App\Models\Certificate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\SkillCategory;

class PortfolioContextService
{
    public function build(): string
    {
        $profile = Profile::query()->first();
        $context = [
            'profile' => $profile?->only(['name', 'headline', 'about_description', 'location', 'email', 'availability_text']),
            'projects' => Project::query()->published()->with(['category:id,name', 'technologies:id,name'])->orderBy('display_order')->limit(12)->get()->map(fn (Project $project) => [
                'title' => $project->title, 'slug' => $project->slug, 'description' => $project->short_description,
                'role' => $project->role, 'category' => $project->category?->name,
                'technologies' => $project->technologies->pluck('name'), 'result' => $project->result,
            ]),
            'skills' => SkillCategory::query()->where('is_active', true)->with(['skills' => fn ($query) => $query->where('is_active', true)])->get()->map(fn (SkillCategory $category) => ['category' => $category->name, 'skills' => $category->skills->pluck('name')]),
            'experiences' => Experience::query()->where('is_active', true)->orderBy('display_order')->get(['position', 'organization', 'description', 'responsibilities', 'technologies']),
            'educations' => Education::query()->where('is_active', true)->get(['institution', 'degree', 'study_program', 'description']),
            'certificates' => Certificate::query()->where('is_active', true)->orderBy('display_order')->limit(12)->get(['title', 'issuer', 'issued_at']),
        ];

        return json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: '{}';
    }
}
