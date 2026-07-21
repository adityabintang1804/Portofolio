<?php

namespace App\Services\AI;

final readonly class AiResponse
{
    public function __construct(
        public string $content,
        public string $provider,
        public bool $fallbackUsed = false,
    ) {}
}
