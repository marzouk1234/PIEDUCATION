<?php
// src/Security/GoogleAuthenticator.php
namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class GoogleAuthenticator extends AbstractAuthenticator
{
    private ClientRegistry $clientRegistry;
    private RouterInterface $router;
    private EntityManagerInterface $entityManager;

    public function __construct(ClientRegistry $clientRegistry, RouterInterface $router, EntityManagerInterface $entityManager)
    {
        $this->clientRegistry = $clientRegistry;
        $this->router = $router;
        $this->entityManager = $entityManager;
    }

    public function supports(Request $request): bool
    {
        return $request->getPathInfo() === '/connect/google/check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $googleUser = $client->fetchUser();
        $email = $googleUser->getEmail();

        // Check if the user exists in the database
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$existingUser) {
            throw new AuthenticationException("No account is linked to this email. Please register first.");
        }

        return new SelfValidatingPassport(
            new UserBadge($existingUser->getEmail(), function() use ($existingUser) {
                return $existingUser;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
{
    $user = $token->getUser();

    if (!$user instanceof User) {
        throw new \LogicException('Authenticated user is not a valid instance of User.');
    }

    // Redirect user based on their role
    if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
        return new RedirectResponse($this->router->generate('app_admin'));
    } elseif (in_array('ROLE_ENS', $user->getRoles(), true)) {
        return new RedirectResponse($this->router->generate('app_cours'));
    } elseif (in_array('ROLE_ETU', $user->getRoles(), true)) {
        return new RedirectResponse($this->router->generate('app_etudiant'));
    }

    return new RedirectResponse($this->router->generate('app_front')); // Default fallback redirect
}


    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new RedirectResponse($this->router->generate('app_login')); // Redirect back to login on failure
    }
}
