<?php

namespace App\Contracts;

use App\Services\AI\AiResponse;

interface AiProviderInterface
{
    /** @param array<int, array{role: string, content: string}> $history */
    public function generate(string $systemPrompt, string $userPrompt, array $history = []): AiResponse;
}
