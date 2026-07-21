<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return $this->projectRules();
    }

    protected function projectRules(?int $projectId = null): array
    {
        return [
            'project_category_id' => ['required', 'exists:project_categories,id'],
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'alpha_dash', 'max:180', Rule::unique('projects')->ignore($projectId)],
            'short_description' => ['required', 'string', 'max:500'],
            'overview' => ['required', 'string', 'max:20000'],
            'background' => ['nullable', 'string', 'max:20000'],
            'problem' => ['nullable', 'string', 'max:20000'],
            'goal' => ['nullable', 'string', 'max:20000'],
            'role' => ['nullable', 'string', 'max:500'],
            'process' => ['nullable', 'string', 'max:20000'],
            'challenges' => ['nullable', 'string', 'max:20000'],
            'solution' => ['nullable', 'string', 'max:20000'],
            'result' => ['nullable', 'string', 'max:20000'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'demo_url' => ['nullable', 'url', 'max:255'],
            'status' => ['required', 'in:draft,published,archived'],
            'is_featured' => ['boolean'],
            'display_order' => ['required', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:180'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'technology_ids' => ['array'],
            'technology_ids.*' => ['integer', 'exists:technologies,id'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'gallery_images' => ['array', 'max:10'],
            'gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
