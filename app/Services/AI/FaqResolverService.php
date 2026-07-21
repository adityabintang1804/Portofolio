<?php

namespace App\Services\AI;

use App\Models\ChatbotFaq;
use Illuminate\Support\Str;

class FaqResolverService
{
    public function resolve(string $message): ?array
    {
        $normalized = Str::lower($message);
        $best = ChatbotFaq::query()->where('is_active', true)->orderBy('priority')->get()
            ->map(function (ChatbotFaq $faq) use ($normalized): array {
                $score = Str::contains($normalized, Str::lower($faq->question)) ? 10 : 0;
                foreach ($faq->keywords ?? [] as $keyword) {
                    if (Str::contains($normalized, Str::lower($keyword))) {
                        $score++;
                    }
                }

                return ['faq' => $faq, 'score' => $score];
            })->sortByDesc('score')->first();

        if (! $best || $best['score'] < 1) {
            return null;
        }

        $faq = $best['faq'];

        return [
            'message' => $faq->answer,
            'source' => 'faq',
            'actions' => $faq->action_label && $faq->action_url ? [['label' => $faq->action_label, 'url' => $faq->action_url]] : [],
        ];
    }
}
