<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FrontController extends AbstractController
{
    #[Route('/front', name: 'app_front')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }
    #[Route('/frontt', name: 'app_frontt', methods: ['GET'])]
    public function indexb(CoursRepository $coursRepository): Response
    {
        return $this->render('frontt/index.html.twig', [
            'cours' => $coursRepository->findAll(),
        ]);
    }
    #[Route('/new/{coursId}', name: 'inscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CoursRepository $coursRepository, EntityManagerInterface $entityManager, int $coursId): Response
    {
        $cours = $coursRepository->find($coursId);
        if (!$cours) {
            $this->addFlash('error', 'Cours non trouvé.');
            return $this->redirectToRoute('app_frontt');
        }

        $inscription = new Inscription();
        $inscription->addCours($cours);
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($inscription);
            $entityManager->flush();
            $this->addFlash('success', 'Inscription créée avec succès.');
            return $this->redirectToRoute('app_frontt');
        }

        return $this->render('inscription/new.html.twig', [
            'form' => $form->createView(),
            'cours' => $cours,
        ]);
    }
}
