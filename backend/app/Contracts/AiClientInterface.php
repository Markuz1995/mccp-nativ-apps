<?php

namespace App\Contracts;

interface AiClientInterface
{
    public function summarize(string $content): string;
}
