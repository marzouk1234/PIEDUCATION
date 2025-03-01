<?php

// src/Controller/ConnectController.php
namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ConnectController extends AbstractController
{
    private ClientRegistry $clientRegistry;

    public function __construct(ClientRegistry $clientRegistry)
    {
        $this->clientRegistry = $clientRegistry;
    }

    #[Route('/connect/google', name: 'connect_google')]
    public function connect(): Response
    {
        $client = $this->clientRegistry->getClient('google');
        return $client->redirect(['profile', 'email']);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function check(): Response
    {
        // Google OAuth callback - Symfony handles authentication automatically
        return $this->redirectToRoute('homepage'); // Redirect to your homepage or another route after login
    }
}
