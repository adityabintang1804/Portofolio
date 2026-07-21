<?php

namespace App\Services\AI;

use Throwable;

class AiRouterService
{
    public function __construct(
        private readonly GroqProvider $groq,
        private readonly GeminiProvider $gemini,
    ) {}

    public function generate(string $systemPrompt, string $userPrompt, array $history = []): AiResponse
    {
        try {
            return $this->groq->generate($systemPrompt, $userPrompt, $history);
        } catch (Throwable) {
            try {
                return $this->gemini->generate($systemPrompt, $userPrompt, $history);
            } catch (Throwable) {
                return new AiResponse(
                    'Maaf, layanan chatbot sedang tidak tersedia. Anda tetap dapat membuka halaman proyek, CV, atau kontak untuk mengenal Aditya lebih lanjut.',
                    'static',
                    true,
                );
            }
        }
    }
}
