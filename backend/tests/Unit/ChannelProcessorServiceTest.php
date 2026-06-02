<?php

namespace Tests\Unit;

use App\Contracts\ChannelInterface;
use App\DTOs\ChannelResultDTO;
use App\Models\DeliveryLog;
use App\Models\Message;
use App\Services\ChannelProcessorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ChannelProcessorServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_delivery_log_for_each_channel(): void
    {
        $message = Message::create([
            'title'            => 'Test',
            'original_content' => 'Content',
            'summary'          => 'Summary',
            'status'           => 'pending',
        ]);

        $channel = Mockery::mock(ChannelInterface::class);
        $channel->expects('send')
            ->with($message)
            ->andReturn(new ChannelResultDTO('email', true, ['to' => 'test@test.com']));

        $service = new ChannelProcessorService();
        $service->process($message, ['email'], ['email' => $channel]);

        $this->assertDatabaseHas('delivery_logs', [
            'message_id' => $message->id,
            'channel'    => 'email',
            'status'     => 'success',
        ]);
    }

    public function test_continues_processing_when_one_channel_fails(): void
    {
        $message = Message::create([
            'title'            => 'Test',
            'original_content' => 'Content',
            'summary'          => 'Summary',
            'status'           => 'pending',
        ]);

        $emailChannel = Mockery::mock(ChannelInterface::class);
        $emailChannel->expects('send')
            ->with($message)
            ->andReturn(new ChannelResultDTO('email', false, null, 'Connection error'));

        $slackChannel = Mockery::mock(ChannelInterface::class);
        $slackChannel->expects('send')
            ->with($message)
            ->andReturn(new ChannelResultDTO('slack', true, ['ok' => true]));

        $service = new ChannelProcessorService();
        $service->process($message, ['email', 'slack'], [
            'email' => $emailChannel,
            'slack' => $slackChannel,
        ]);

        $this->assertDatabaseHas('delivery_logs', [
            'message_id'    => $message->id,
            'channel'       => 'email',
            'status'        => 'failed',
            'error_message' => 'Connection error',
        ]);

        $this->assertDatabaseHas('delivery_logs', [
            'message_id' => $message->id,
            'channel'    => 'slack',
            'status'     => 'success',
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
