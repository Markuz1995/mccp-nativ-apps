<?php

namespace App\Integrations\Gemini;

use App\Contracts\AiClientInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiClient implements AiClientInterface
{
    public function summarize(string $content): string
    {
        $apiKey = config('services.gemini.key');
        $model = config('services.gemini.model', 'gemini-2.0-flash');

        $response = Http::timeout(30)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "Resume el siguiente texto en maximo 100 caracteres:\n\n$content"],
                        ],
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Gemini API error: ' . $response->body());
        }

        $summary = trim($response->json('candidates.0.content.parts.0.text') ?? '');

        return mb_substr($summary, 0, 100);
    }
}
