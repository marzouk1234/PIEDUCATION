<?php
namespace App\Controller;

use App\Repository\HistoriqueEvaluationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HistoryController extends AbstractController
{
    #[Route('/history', name: 'app_history')]
    public function index(HistoriqueEvaluationRepository $historiqueRepo): Response
    {
        $historique = $historiqueRepo->findBy([], ['dateAction' => 'DESC']); // Trier par date décroissante

        return $this->render('history/index.html.twig', [
            'historique' => $historique,
        ]);
    }
}
