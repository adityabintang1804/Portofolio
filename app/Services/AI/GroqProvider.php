<?php

namespace App\Services\AI;

use App\Contracts\AiProviderInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GroqProvider implements AiProviderInterface
{
    public function generate(string $systemPrompt, string $userPrompt, array $history = []): AiResponse
    {
        $apiKey = config('ai.groq.api_key');
        if (! $apiKey) {
            throw new RuntimeException('Groq API key is not configured.');
        }

        $messages = [['role' => 'system', 'content' => $systemPrompt], ...$history, ['role' => 'user', 'content' => $userPrompt]];
        $response = Http::withToken($apiKey)->acceptJson()->timeout(config('ai.timeout'))->post(config('ai.groq.url'), [
            'model' => config('ai.groq.model'),
            'messages' => $messages,
            'temperature' => 0.2,
            'max_tokens' => config('ai.max_output_tokens'),
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Groq request failed with status '.$response->status());
        }

        $content = trim((string) $response->json('choices.0.message.content'));
        if ($content === '') {
            throw new RuntimeException('Groq returned an empty response.');
        }

        return new AiResponse($content, 'groq');
    }
}
