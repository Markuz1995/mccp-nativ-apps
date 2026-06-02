<?php

namespace App\Factories;

use App\Contracts\ChannelInterface;
use App\Integrations\Channels\EmailChannel;
use App\Integrations\Channels\SlackChannel;
use App\Integrations\Channels\SmsChannel;
use InvalidArgumentException;

class ChannelFactory
{
    public static function make(string $channel): ChannelInterface
    {
        return match ($channel) {
            'email' => app(EmailChannel::class),
            'slack' => app(SlackChannel::class),
            'sms'   => app(SmsChannel::class),
            default => throw new InvalidArgumentException("Unknown channel: $channel"),
        };
    }
}
