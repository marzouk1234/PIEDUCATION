<?php

namespace App\Controller;

use App\Entity\Resultat;
use App\Form\ResultatType;
use App\Repository\ResultatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Knp\Component\Pager\PaginatorInterface; 


#[Route('/resultat')]
final class ResultatController extends AbstractController
{
    #[Route(name: 'app_resultat_index', methods: ['GET'])] 
    public function index(Request $request, ResultatRepository $resultatRepository): Response
    {
        // Pagination and result handling
        $perPage = 10;
        $page = $request->query->getInt('page', 1);
        $query = $resultatRepository->createQueryBuilder('r')->getQuery();
        $adapter = new QueryAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setCurrentPage($page);
        $pagerfanta->setMaxPerPage($perPage);
        $results = $pagerfanta->getCurrentPageResults();

        return $this->render('resultat/index.html.twig', [
            'resultats' => $results,
            'pager' => $pagerfanta,
        ]);
    }

    #[Route('/back', name: 'app_resultat_indexb')]
    public function indexb(ResultatRepository $resultatRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupérer tous les résultats
        $query = $resultatRepository->createQueryBuilder('r')->getQuery();

        // Paginer les résultats
        $resultats = $paginator->paginate(
            $query, // Requête à paginer
            $request->query->getInt('page', 1), // Numéro de page (par défaut : 1)
            10 // Nombre d'éléments par page
        );

        // Calculer les statistiques
        $notes = array_map(function ($resultat) {
            return $resultat->getNote();
        }, $resultats->getItems());

        $stats = [
            'total' => $resultats->getTotalItemCount(),
            'moyenne' => count($notes) > 0 ? round(array_sum($notes) / count($notes), 2) : 'N/A',
            'max' => count($notes) > 0 ? max($notes) : 'N/A',
            'min' => count($notes) > 0 ? min($notes) : 'N/A',
        ];

        // Données pour le Pie Chart (répartition des notes)
        $pieChartData = [
            'excellent' => 0,
            'bon' => 0,
            'moyen' => 0,
            'faible' => 0,
        ];

        foreach ($notes as $note) {
            if ($note >= 16) {
                $pieChartData['excellent']++;
            } elseif ($note >= 12) {
                $pieChartData['bon']++;
            } elseif ($note >= 8) {
                $pieChartData['moyen']++;
            } else {
                $pieChartData['faible']++;
            }
        }

        // Passer les variables au template
        return $this->render('resultat/indexb.html.twig', [
            'resultats' => $resultats, // Contient les données paginées
            'stats' => $stats,
            'pieChartData' => $pieChartData,
        ]);
    }


    
    #[Route('/new', name: 'app_resultat_new', methods: ['GET', 'POST'])] 
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $resultat = new Resultat();
        $form = $this->createForm(ResultatType::class, $resultat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($resultat);
            $entityManager->flush();
            return $this->redirectToRoute('app_resultat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('resultat/new.html.twig', [
            'resultat' => $resultat,
            'form' => $form,
        ]);
    }

    #[Route('/newb', name: 'app_resultat_newb', methods: ['GET', 'POST'])] 
    public function newb(Request $request, EntityManagerInterface $entityManager): Response
    {
        $resultat = new Resultat();
        $form = $this->createForm(ResultatType::class, $resultat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($resultat);
            $entityManager->flush();
            return $this->redirectToRoute('app_resultat_indexb', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('resultat/newb.html.twig', [
            'resultat' => $resultat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_resultat_show', methods: ['GET'])] 
    public function show(Resultat $resultat): Response
    {
        return $this->render('resultat/show.html.twig', [
            'resultat' => $resultat,
        ]);
    }

    #[Route('/back/{id}', name: 'app_resultat_showb', methods: ['GET'])] 
    public function showb(Resultat $resultat, EntityManagerInterface $entityManager): Response
    {
        
        $entityManager->initializeObject($resultat->getEvaluation());
        return $this->render('resultat/showb.html.twig', [
            'resultat' => $resultat,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_resultat_edit', methods: ['GET', 'POST'])] 
    public function edit(Request $request, Resultat $resultat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ResultatType::class, $resultat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_resultat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('resultat/edit.html.twig', [
            'resultat' => $resultat,
            'form' => $form,
        ]);
    }

    #[Route('/back/{id}/edit', name: 'app_resultat_editb', methods: ['GET', 'POST'])] 
    public function editb(Request $request, Resultat $resultat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ResultatType::class, $resultat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_resultat_indexb', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('resultat/editb.html.twig', [
            'resultat' => $resultat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_resultat_delete', methods: ['POST'])] 
    public function delete(Request $request, Resultat $resultat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $resultat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($resultat);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_resultat_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/back/{id}', name: 'app_resultat_deleteb', methods: ['POST'])] 
    public function deleteb(Request $request, Resultat $resultat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $resultat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($resultat);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_resultat_indexb', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/statistiques', name: 'app_resultat_statistiques', methods: ['GET'])] 
    public function statistiques(ResultatRepository $resultatRepository): JsonResponse
    {
        $stats = [
            'moins_de_10' => $resultatRepository->countByNoteRange(0, 10),
            'entre_10_et_15' => $resultatRepository->countByNoteRange(10, 15),
            'plus_de_15' => $resultatRepository->countByNoteRange(15, 20),
        ];
        return $this->json($stats);
    }

#[Route('/trier', name: 'app_resultat_trier', methods: ['GET'])] 
public function trier(ResultatRepository $resultatRepository): Response
{
    // Sort by 'note' (ascending order)
    $resultats = $resultatRepository->findBy([], ['note' => 'ASC']); // Correct sorting by note

    return $this->render('resultat/index.html.twig', [
        'resultats' => $resultats,
    ]);
}

}
