<?php

return [
    'primary_provider' => env('AI_PRIMARY_PROVIDER', 'groq'),
    'fallback_provider' => env('AI_FALLBACK_PROVIDER', 'gemini'),
    'timeout' => (int) env('AI_TIMEOUT', 15),
    'max_output_tokens' => (int) env('AI_MAX_OUTPUT_TOKENS', 400),
    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'llama-3.1-8b-instant'),
        'url' => env('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions'),
    ],
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
        'url' => env('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models'),
    ],
];
