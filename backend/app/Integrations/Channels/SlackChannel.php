<?php

namespace App\Integrations\Channels;

use App\Contracts\ChannelInterface;
use App\DTOs\ChannelResultDTO;
use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackChannel implements ChannelInterface
{
    public function send(Message $message): ChannelResultDTO
    {
        $webhookUrl = config('services.slack.webhook_url');

        if (!$webhookUrl) {
            Log::warning('[SlackChannel] No webhook URL configured');

            return new ChannelResultDTO(
                channel: 'slack',
                success: false,
                error: 'SLACK_WEBHOOK_URL not configured',
            );
        }

        $payload = [
            'text' => "*{$message->title}*\n{$message->summary}",
        ];

        try {
            $response = Http::timeout(15)->post($webhookUrl, $payload);

            if ($response->successful()) {
                Log::info('[SlackChannel] Message sent successfully');

                return new ChannelResultDTO(
                    channel: 'slack',
                    success: true,
                    payload: $payload,
                );
            }

            Log::error('[SlackChannel] Failed to send', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return new ChannelResultDTO(
                channel: 'slack',
                success: false,
                payload: $payload,
                error: $response->body(),
            );
        } catch (\Exception $e) {
            Log::error('[SlackChannel] Exception', ['error' => $e->getMessage()]);

            return new ChannelResultDTO(
                channel: 'slack',
                success: false,
                payload: $payload,
                error: $e->getMessage(),
            );
        }
    }
}
