<?php

namespace App\DTOs;

class ChannelResultDTO
{
    public function __construct(
        public readonly string $channel,
        public readonly bool $success,
        public readonly ?array $payload = null,
        public readonly ?string $error = null,
    ) {}
}
