<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?RedirectResponse
    {
        $user = $token->getUser();

        // Rediriger selon le rôle de l'utilisateur
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_admin'));
        } elseif (in_array('ROLE_ENS', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_cours'));
        } elseif (in_array('ROLE_ETU', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_etudiant'));
        }

        // Redirection par défaut
        return new RedirectResponse($this->urlGenerator->generate('app_front'));
    }
}
