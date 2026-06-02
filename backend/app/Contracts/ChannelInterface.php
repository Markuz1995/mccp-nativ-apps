<?php

namespace App\Contracts;

use App\DTOs\ChannelResultDTO;
use App\Models\Message;

interface ChannelInterface
{
    public function send(Message $message): ChannelResultDTO;
}
