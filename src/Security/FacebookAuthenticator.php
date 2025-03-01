<?php
namespace App\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;

use Symfony\Component\Security\Http\Util\TargetPathTrait;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;

class FacebookAuthenticator extends OAuth2Authenticator
{
    private $client;
    private $urlGenerator;
    private $entityManager;
    private $userRepository;
    
    public function __construct(FacebookClient $client, UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->client = $client;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'connect_facebook_check';
    }

    
    
    public function authenticate(Request $request): Passport
{
    $accessToken = $this->fetchAccessToken($this->client);
    $facebookUser = $this->client->fetchUserFromToken($accessToken);

    return new Passport(
        new UserBadge($facebookUser->getId(), function () use ($facebookUser) {
            return $this->loadOrCreateUser($facebookUser);
        }),
        []
    );
}

private function loadOrCreateUser($facebookUser): UserInterface
{
    $existingUser = $this->userRepository->findOneBy(['facebookId' => $facebookUser->getId()]);

    if (!$existingUser) {
        // Vérifier si un utilisateur existe déjà avec cet email (connexion hybride)
        $existingUser = $this->userRepository->findOneBy(['email' => $facebookUser->getEmail()]);

        if (!$existingUser) {
            // Créer un nouvel utilisateur
            $existingUser = new User();
            $existingUser->setEmail($facebookUser->getEmail());
            $existingUser->setFacebookId($facebookUser->getId());
            $existingUser->setRoles(['ROLE_USER']);
            $this->entityManager->persist($existingUser);
            $this->entityManager->flush();
        } else {
            // Associer l'ID Facebook si l'utilisateur existait déjà
            $existingUser->setFacebookId($facebookUser->getId());
            $this->entityManager->flush();
        }
    }

    return $existingUser;
}



public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
{
    if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
        return new RedirectResponse($targetPath);
    }

    return new RedirectResponse($this->urlGenerator->generate('app_home'));
}


    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}
