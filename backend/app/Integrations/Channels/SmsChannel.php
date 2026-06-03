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
        $xml = <<<XML
<soapenv:Envelope
    xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:sms="http://ultracem.com/sms">
    <soapenv:Header/>
    <soapenv:Body>
        <sms:SendSmsRequest>
            <sms:destination>+570000000000</sms:destination>
            <sms:message>{$message->summary}</sms:message>
            <sms:reference>{$message->title}</sms:reference>
        </sms:SendSmsRequest>
    </soapenv:Body>
</soapenv:Envelope>
XML;

        Log::info('[SmsChannel] SOAP XML generated', ['xml' => $xml]);

        return new ChannelResultDTO(
            channel: 'sms',
            success: true,
            payload: ['xml' => $xml],
        );
    }
}
