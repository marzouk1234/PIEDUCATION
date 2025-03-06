<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class FlaskChatbotService
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function sendMessage(string $message): string
    {
        $response = $this->httpClient->request(
            'POST',
            'http://localhost:5000/chat', // URL de l'API Flask
            [
                'json' => ['message' => $message],
            ]
        );

        $content = $response->toArray();
        return $content['reply'];
    }
}