<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SessionController extends AbstractController
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/session', name: 'app_session')]
    public function index(): Response
    {
        $request = $this->requestStack->getCurrentRequest(); // Récupérer la requête actuelle
        if (!$request) {
            return new Response('Aucune requête en cours.', Response::HTTP_BAD_REQUEST);
        }

        $session = $request->getSession();

        // Vérifier si un utilisateur est en session
        $userEmail = $session->get('user_email');
        

        // Gestion du nombre de visites
        $nbreVisite = $session->get('NbVisite', 0) + 1;
        $session->set('NbVisite', $nbreVisite);

        return $this->render('session/index.html.twig', [
            'user_email' => $userEmail,
            'nbre_visite' => $nbreVisite,
        ]);
    }
    

    #[Route('/session/clear', name: 'app_session_clear')]
    public function clearSession(): Response
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $session = $request->getSession();
            $session->clear();
        }

        return new Response('Session supprimée.');
    }
}
