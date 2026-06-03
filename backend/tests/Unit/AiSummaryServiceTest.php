<?php

namespace Tests\Unit;

use App\Integrations\Gemini\GeminiClient;
use App\Services\AiSummaryService;
use Mockery;
use Tests\TestCase;

class AiSummaryServiceTest extends TestCase
{
    public function test_generates_summary_successfully(): void
    {
        config(['services.gemini.enabled' => true]);

        $client = Mockery::mock(GeminiClient::class);
        $client->expects('summarize')
            ->with('Long content here')
            ->andReturn('Short summary');

        $service = new AiSummaryService($client);
        $result = $service->generateSummary('Long content here');

        $this->assertSame('Short summary', $result);
    }

    public function test_returns_placeholder_when_ai_disabled(): void
    {
        config(['services.gemini.enabled' => false]);

        $client = Mockery::mock(GeminiClient::class);
        $client->shouldNotReceive('summarize');

        $service = new AiSummaryService($client);
        $result = $service->generateSummary('Any content');

        $this->assertStringContainsString('[AI desactivado]', $result);
    }

    public function test_forwards_exception_when_gemini_fails(): void
    {
        config(['services.gemini.enabled' => true]);

        $client = Mockery::mock(GeminiClient::class);
        $client->expects('summarize')
            ->andThrow(new \RuntimeException('API error'));

        $service = new AiSummaryService($client);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('API error');

        $service->generateSummary('Any content');
    }



    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
