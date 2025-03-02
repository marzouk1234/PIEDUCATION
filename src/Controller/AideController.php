<?php

namespace App\Controller;

use App\Entity\Aide;
use App\Form\AideType;
use App\Repository\AideRepository;
use App\Service\InappropriateWordFilter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/aide')]
final class AideController extends AbstractController
{
    private $wordFilter;

    public function __construct(InappropriateWordFilter $wordFilter)
    {
        $this->wordFilter = $wordFilter;
    }

    #[Route('/', name: 'app_aide_index', methods: ['GET'])]
    public function index(Request $request, AideRepository $aideRepository, PaginatorInterface $paginator): Response
    {
        $query = $request->query->get('q', '');
        $sort = $request->query->get('sort', 'date_creation');
        $order = $request->query->get('order', 'DESC');

        // Requête pour récupérer les aides avec critères
        $aidesQuery = $aideRepository->searchAides($query, $sort, $order);

        // Pagination
        $aides = $paginator->paginate(
            $aidesQuery,
            $request->query->getInt('page', 1),
            10 // Nombre d'éléments par page
        );

        return $this->render('aide/index.html.twig', [
            'aides' => $aides,
            'query' => $query,
            'sort' => $sort,
            'order' => $order,
        ]);
    }

    #[Route('/new', name: 'app_aide_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $aide = new Aide();
        $form = $this->createForm(AideType::class, $aide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contenu = $aide->getDescription();

            if ($this->wordFilter->containsInappropriateWords($contenu)) {
                $this->addFlash('warning', 'Votre texte contient des mots inappropriés.');
            }

            $descriptionFiltree = $this->wordFilter->filterInappropriateWords($contenu);
            $aide->setDescription($descriptionFiltree);
            $aide->setDateCreation(new \DateTime());

            $entityManager->persist($aide);
            $entityManager->flush();

            $this->addFlash('success', 'Aide ajoutée avec succès !');
            return $this->redirectToRoute('app_aide_index');
        }

        return $this->render('aide/new.html.twig', [
            'aide' => $aide,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_aide_show', methods: ['GET'])]
    public function show(Aide $aide): Response
    {
        return $this->render('aide/show.html.twig', [
            'aide' => $aide,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_aide_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Aide $aide, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AideType::class, $aide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contenu = $aide->getDescription();

            if ($this->wordFilter->containsInappropriateWords($contenu)) {
                $this->addFlash('warning', 'Votre texte contient des mots inappropriés.');
            }

            $contenuFiltre = $this->wordFilter->filterInappropriateWords($contenu);
            $aide->setDescription($contenuFiltre);

            $entityManager->flush();

            $this->addFlash('success', 'Aide mise à jour avec succès !');
            return $this->redirectToRoute('app_aide_index');
        }

        return $this->render('aide/edit.html.twig', [
            'aide' => $aide,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_aide_delete', methods: ['POST'])]
    public function delete(Request $request, Aide $aide, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $aide->getId(), $request->get('_token'))) {
            $entityManager->remove($aide);
            $entityManager->flush();
            $this->addFlash('success', 'Aide supprimée avec succès !');
        }

        return $this->redirectToRoute('app_aide_index');
    }
}
