<?php
// src/Service/InfobipSmsService.php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface; // Import the HttpClientInterface 1 

class InfobipSmsService
{
    private $httpClient; // bech nokhrjou bih l bara 2
    private $apiUrl = 'https://1gww19.api.infobip.com/sms/2/text/advanced'; // The URL to send the SMS 3
    private $apiKey = 'e617539eb1169f8f26fad7da60a9ffb3-5cc647a8-e44c-4a3a-957d-7008a0ba4631'; // The API key 4

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function sendSms(string $to, string $message)
    {
        $response = $this->httpClient->request('POST', $this->apiUrl, [
            'headers' => [
                'Authorization' => 'App ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'messages' => [
                    [
                        'destinations' => [
                            ['to' => $to]
                        ],
                        'from' => '447491163443',
                        'text' => $message,
                    ]
                ]
            ]
        ]);

        
        if ($response->getStatusCode() === 200) {
            return $response->getContent();
        } else {
            return 'Error: ' . $response->getStatusCode() . ' ' . $response->getStatusText();
        }
    }
}
