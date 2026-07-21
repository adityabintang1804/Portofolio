<?php

namespace App\Services\AI;

use App\Contracts\AiProviderInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiProvider implements AiProviderInterface
{
    public function generate(string $systemPrompt, string $userPrompt, array $history = []): AiResponse
    {
        $apiKey = config('ai.gemini.api_key');
        if (! $apiKey) {
            throw new RuntimeException('Gemini API key is not configured.');
        }

        $historyText = collect($history)->map(fn (array $message) => $message['role'].': '.$message['content'])->implode("\n");
        $prompt = $systemPrompt."\n\n".$historyText."\n\nPertanyaan: ".$userPrompt;
        $url = rtrim(config('ai.gemini.url'), '/').'/'.config('ai.gemini.model').':generateContent';
        $response = Http::acceptJson()->timeout(config('ai.timeout'))->post($url.'?key='.urlencode($apiKey), [
            'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
            'generationConfig' => ['temperature' => 0.2, 'maxOutputTokens' => config('ai.max_output_tokens')],
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Gemini request failed with status '.$response->status());
        }

        $content = trim((string) $response->json('candidates.0.content.parts.0.text'));
        if ($content === '') {
            throw new RuntimeException('Gemini returned an empty response.');
        }

        return new AiResponse($content, 'gemini', true);
    }
}
