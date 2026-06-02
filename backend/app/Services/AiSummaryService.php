<?php

namespace App\Services;

use App\Integrations\OpenAI\OpenAIClient;
use Illuminate\Support\Facades\Log;

class AiSummaryService
{
    public function __construct(
        private readonly OpenAIClient $openAIClient,
    ) {}

    public function generateSummary(string $content): string
    {
        try {
            $summary = $this->openAIClient->summarize($content);

            Log::info('[AiSummaryService] Summary generated', [
                'content_length' => mb_strlen($content),
                'summary_length' => mb_strlen($summary),
            ]);

            return $summary;
        } catch (\Exception $e) {
            Log::error('[AiSummaryService] Failed to generate summary', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
