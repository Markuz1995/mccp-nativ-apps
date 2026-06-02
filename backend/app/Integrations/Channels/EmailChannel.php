<?php

namespace App\Integrations\Channels;

use App\Contracts\ChannelInterface;
use App\DTOs\ChannelResultDTO;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

class EmailChannel implements ChannelInterface
{
    public function send(Message $message): ChannelResultDTO
    {
        $payload = [
            'to'      => 'user@example.com',
            'subject' => $message->title,
            'body'    => $message->summary ?? $message->original_content,
        ];

        Log::info('[EmailChannel] Simulated email sent', $payload);

        return new ChannelResultDTO(
            channel: 'email',
            success: true,
            payload: $payload,
        );
    }
}
