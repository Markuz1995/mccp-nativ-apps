<?php

namespace App\Services;

use App\Contracts\AiClientInterface;
use Illuminate\Support\Facades\Log;

class AiSummaryService
{
    public function __construct(
        private readonly AiClientInterface $aiClient,
    ) {}

    public function generateSummary(string $content): string
    {
        try {
            $summary = $this->aiClient->summarize($content);

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
