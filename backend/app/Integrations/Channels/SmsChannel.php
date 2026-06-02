<?php

namespace App\Integrations\Channels;

use App\Contracts\ChannelInterface;
use App\DTOs\ChannelResultDTO;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

class SmsChannel implements ChannelInterface
{
    public function send(Message $message): ChannelResultDTO
    {
        $body = $message->summary ?? $message->original_content;

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
    <soapenv:Body>
        <sms:send xmlns:sms="http://example.com/sms">
            <message>{$body}</message>
            <to>+1234567890</to>
        </sms:send>
    </soapenv:Body>
</soapenv:Envelope>
XML;

        Log::info('[SmsChannel] SOAP XML', ['xml' => $xml]);

        return new ChannelResultDTO(
            channel: 'sms',
            success: true,
            payload: ['xml' => $xml],
        );
    }
}
