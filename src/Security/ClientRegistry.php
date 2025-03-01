<?php
namespace App\Security;

use KnpU\OAuth2ClientBundle\Client\ClientInterface;

class ClientRegistry
{
    private array $clients;

    public function __construct(iterable $clients = [])
    {
        $this->clients = is_array($clients) ? $clients : iterator_to_array($clients);
    }

    public function getClient(string $name): ClientInterface
{
    if (!isset($this->clients[$name])) {
        throw new \LogicException(sprintf('OAuth client "%s" not found. Check your knpu_oauth2_client configuration.', $name));
    }

    return $this->clients[$name];
}

}
