<?php

namespace Tests\Unit;

use App\Factories\ChannelFactory;
use App\Integrations\Channels\EmailChannel;
use App\Integrations\Channels\SlackChannel;
use App\Integrations\Channels\SmsChannel;
use Tests\TestCase;

class ChannelFactoryTest extends TestCase
{
    public function test_resolves_email_channel(): void
    {
        $channel = ChannelFactory::make('email');
        $this->assertInstanceOf(EmailChannel::class, $channel);
    }

    public function test_resolves_slack_channel(): void
    {
        $channel = ChannelFactory::make('slack');
        $this->assertInstanceOf(SlackChannel::class, $channel);
    }

    public function test_resolves_sms_channel(): void
    {
        $channel = ChannelFactory::make('sms');
        $this->assertInstanceOf(SmsChannel::class, $channel);
    }

    public function test_throws_exception_for_unknown_channel(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown channel: fax');

        ChannelFactory::make('fax');
    }
}
