<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:500'],
            'history' => ['sometimes', 'array', 'max:10'],
            'history.*.role' => ['required', 'in:user,assistant'],
            'history.*.content' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Pertanyaan tidak boleh kosong.',
            'message.max' => 'Pertanyaan maksimal 500 karakter.',
            'history.max' => 'Riwayat percakapan terlalu panjang.',
        ];
    }
}
