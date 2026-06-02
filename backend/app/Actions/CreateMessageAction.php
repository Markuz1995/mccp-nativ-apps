<?php

namespace App\Actions;

use App\DTOs\MessageDTO;
use App\Jobs\ProcessMessageChannelsJob;
use App\Models\Message;
use App\Services\AiSummaryService;

class CreateMessageAction
{
    public function __construct(
        private readonly AiSummaryService $aiSummaryService,
    ) {}

    public function execute(MessageDTO $dto): Message
    {
        $summary = $this->aiSummaryService->generateSummary($dto->content);

        $message = Message::create([
            'title'            => $dto->title,
            'original_content' => $dto->content,
            'summary'          => $summary,
            'status'           => 'pending',
        ]);

        ProcessMessageChannelsJob::dispatch($message, $dto->channels);

        return $message;
    }
}
