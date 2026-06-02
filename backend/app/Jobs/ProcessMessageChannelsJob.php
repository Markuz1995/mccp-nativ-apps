<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\ChannelProcessorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMessageChannelsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Message $message,
        public array $channels,
    ) {}

    public function handle(ChannelProcessorService $channelProcessor): void
    {
        $channelProcessor->process($this->message, $this->channels);
    }
}
