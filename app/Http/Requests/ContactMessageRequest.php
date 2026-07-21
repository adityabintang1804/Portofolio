<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'organization' => ['nullable', 'string', 'max:180'],
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'website' => ['nullable', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.', 'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.', 'subject.required' => 'Subjek wajib diisi.',
            'message.required' => 'Pesan wajib diisi.', 'message.min' => 'Pesan minimal 10 karakter.',
            'website.max' => 'Permintaan tidak dapat diproses.',
        ];
    }
}
