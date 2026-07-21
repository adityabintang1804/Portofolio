<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'headline' => ['required', 'string', 'max:180'],
            'hero_badge' => ['nullable', 'string', 'max:100'],
            'hero_description' => ['required', 'string', 'max:1000'],
            'about_description' => ['required', 'string', 'max:10000'],
            'location' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'availability_status' => ['required', 'in:available,unavailable,open_to_work'],
            'availability_text' => ['required', 'string', 'max:180'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'cv_file' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ];
    }
}
