<?php

// src/Controller/ChatbotController.php
namespace App\Controller;

use App\Service\FlaskChatbotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChatbotController extends AbstractController
{
    private $chatbotservice;

    public function __construct(FlaskChatbotService $chatbotservice)
    {
        $this->chatbotservice = $chatbotservice;
    }

    /**
     * @Route("/chat", name="chat", methods={"POST"})
     */
    public function chat(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $message = $data['message'] ?? "";

        if (empty($message)) {
            return new JsonResponse(['error' => 'Message is required'], 400);
        }

        $reply = $this->chatbotservice->sendMessage($message);

        return new JsonResponse(['reply' => $reply]);
    }
}