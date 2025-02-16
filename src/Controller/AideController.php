<?php

namespace App\Controller;

use App\Entity\Aide;
use App\Form\AideType;
use App\Repository\AideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/aide')]
final class AideController extends AbstractController
{
    #[Route('/', name: 'app_aide_index', methods: ['GET'])]
    public function index(AideRepository $aideRepository): Response
    {
        return $this->render('aide/index.html.twig', [
            'aides' => $aideRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_aide_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $aide = new Aide();
        $form = $this->createForm(AideType::class, $aide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $aide->setDateCreation(new \DateTime()); // Ajoute la date actuelle
            $entityManager->persist($aide);
            $entityManager->flush();

            $this->addFlash('success', 'Aide ajoutée avec succès !');

            return $this->redirectToRoute('app_aide_index', [], Response::HTTP_SEE_OTHER);
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
            $entityManager->flush();

            $this->addFlash('success', 'Aide mise à jour avec succès !');

            return $this->redirectToRoute('app_aide_index', [], Response::HTTP_SEE_OTHER);
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

        return $this->redirectToRoute('app_aide_index', [], Response::HTTP_SEE_OTHER);
    }
}
