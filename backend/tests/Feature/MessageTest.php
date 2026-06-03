<?php

namespace Tests\Feature;

use App\Actions\CreateMessageAction;
use App\Models\DeliveryLog;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_message_and_dispatches_job(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'Resumen de prueba']]]],
                ],
            ]),
        ]);

        $response = $this->postJson('/api/messages', [
            'title'    => 'Test Title',
            'content'  => 'This is the original content for testing purposes.',
            'channels' => ['email', 'slack'],
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Message created successfully');

        $this->assertDatabaseHas('messages', [
            'title'   => 'Test Title',
            'summary' => 'Resumen de prueba',
            'status'  => 'pending',
        ]);
    }

    public function test_returns_422_when_gemini_fails(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response('Unauthorized', 401),
        ]);

        $response = $this->postJson('/api/messages', [
            'title'    => 'Test Title',
            'content'  => 'Some content that will fail.',
            'channels' => ['email'],
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('message', 'AI processing failed');

        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseCount('delivery_logs', 0);
    }

    public function test_can_list_messages_with_delivery_logs(): void
    {
        $message = Message::create([
            'title'            => 'History Test',
            'original_content' => 'Content for history',
            'summary'          => 'Summary',
            'status'           => 'completed',
        ]);

        DeliveryLog::create([
            'message_id'      => $message->id,
            'channel'         => 'email',
            'status'          => 'success',
            'request_payload' => ['to' => 'test@example.com'],
        ]);

        $response = $this->getJson('/api/messages');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', 'History Test');
        $response->assertJsonPath('data.0.delivery_logs.0.channel', 'email');
    }

    public function test_validation_requires_all_fields(): void
    {
        $response = $this->postJson('/api/messages', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'content', 'channels']);
    }

    public function test_validation_rejects_invalid_channel(): void
    {
        $response = $this->postJson('/api/messages', [
            'title'    => 'Test',
            'content'  => 'Content',
            'channels' => ['invalid_channel'],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['channels.0']);
    }
}
