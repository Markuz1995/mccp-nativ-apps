<?php

namespace App\Services;

use App\Contracts\ChannelInterface;
use App\Models\DeliveryLog;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

class ChannelProcessorService
{
    /** @param array<string, ChannelInterface> $channels */
    public function process(Message $message, array $channels, array $channelMap = []): void
    {
        foreach ($channels as $channelName) {
            try {
                $channel = $channelMap[$channelName] ?? \App\Factories\ChannelFactory::make($channelName);
                $result = $channel->send($message);

                DeliveryLog::create([
                    'message_id'      => $message->id,
                    'channel'         => $result->channel,
                    'status'          => $result->success ? 'success' : 'failed',
                    'request_payload' => $result->payload,
                    'response_payload' => $result->payload,
                    'error_message'   => $result->error,
                ]);

                Log::info('[ChannelProcessorService] Channel processed', [
                    'channel' => $channelName,
                    'success' => $result->success,
                ]);
            } catch (\Exception $e) {
                Log::error('[ChannelProcessorService] Channel failed', [
                    'channel' => $channelName,
                    'error'   => $e->getMessage(),
                ]);

                DeliveryLog::create([
                    'message_id'    => $message->id,
                    'channel'       => $channelName,
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
        }
    }
}
