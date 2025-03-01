<?php
// src/Security/AuthenticationSuccessHandler.php
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;


class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface, EventSubscriberInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
{
    $user = $token->getUser();

    // Redirect user based on their role
    if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
        return new RedirectResponse($this->urlGenerator->generate('app_admin'));
    } elseif (in_array('ROLE_ENS', $user->getRoles(), true)) {
        return new RedirectResponse($this->urlGenerator->generate('app_cours'));
    } elseif (in_array('ROLE_ETU', $user->getRoles(), true)) {
        return new RedirectResponse($this->urlGenerator->generate('app_etudiant'));
    }

    return new RedirectResponse($this->urlGenerator->generate('app_front'));
}



    // ✅ Fix: Handle the AuthenticationSuccessEvent separately
    public function onAuthenticationSuccessEvent(AuthenticationSuccessEvent $event): void
{
    $token = $event->getAuthenticationToken();
    $user = $token->getUser();

    // Example: Log authentication success
    error_log("User " . $user->getEmail() . " authenticated successfully.");
}


    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $request = $event->getRequest();
        $user = $event->getAuthenticationToken()->getUser();

        // Store user session to persist login
        if ($user) {
            $request->getSession()->set('_security_main', serialize($event->getAuthenticationToken()));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationSuccessEvent::class => 'onAuthenticationSuccessEvent',
            InteractiveLoginEvent::class => 'onInteractiveLogin',
        ];
    }
}
