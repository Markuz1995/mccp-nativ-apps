<?php

namespace App\DTOs;

class MessageDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $content,
        public readonly array $channels,
    ) {}
}
