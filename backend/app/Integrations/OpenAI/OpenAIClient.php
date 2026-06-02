<?php

namespace App\Integrations\OpenAI;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAIClient
{
    public function summarize(string $content): string
    {
        $response = Http::withToken(config('services.openai.key'))
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'    => config('services.openai.model', 'gpt-4o-mini'),
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => "Resume el siguiente texto en máximo 100 caracteres:\n\n$content",
                    ],
                ],
                'max_tokens' => 150,
            ]);

        if ($response->failed()) {
            throw new RuntimeException('OpenAI API error: ' . $response->body());
        }

        $summary = trim($response->json('choices.0.message.content') ?? '');

        return mb_substr($summary, 0, 100);
    }
}
