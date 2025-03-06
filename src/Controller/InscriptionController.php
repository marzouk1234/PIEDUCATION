<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Cours;
use App\Form\InscriptionType;
use App\Repository\InscriptionRepository;
use App\Repository\CoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/inscription')] 
class InscriptionController extends AbstractController
{
    // 📌 Route pour la liste des inscriptions en front
    #[Route('/', name: 'inscription_index', methods: ['GET'])]
    public function index(InscriptionRepository $inscriptionRepository): Response
    {
        return $this->render('inscription/index.html.twig', [
            'inscriptions' => $inscriptionRepository->findAll(),
        ]);
    }

    // 📌 Route pour la liste des inscriptions en back
    #[Route('/back', name: 'inscription_indexb', methods: ['GET'])]
    public function indexb(InscriptionRepository $inscriptionRepository): Response
    {
        return $this->render('inscription/indexb.html.twig', [
            'inscriptions' => $inscriptionRepository->findAll(),
        ]);
    }

    // 📌 Route pour ajouter une inscription en front
    #[Route('/new/{coursId}', name: 'inscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CoursRepository $coursRepository, EntityManagerInterface $entityManager, int $coursId): Response
    {
        $cours = $coursRepository->find($coursId);
        if (!$cours) {
            $this->addFlash('error', 'Cours non trouvé.');
            return $this->redirectToRoute('cours_index');
        }

        $inscription = new Inscription();
        $inscription->addCours($cours);
        $inscription->setDateInscription(new \DateTime()); // Ensure date is set
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($inscription);
            $entityManager->flush();
            $this->addFlash('success', 'Inscription créée avec succès.');
            return $this->redirectToRoute('inscription_index');
        }

        return $this->render('inscription/new.html.twig', [
            'form' => $form->createView(),
            'cours' => $cours,
        ]);
    }

    // 📌 Route pour ajouter une inscription en back
    #[Route('/back/new/{coursId}', name: 'inscription_newb', methods: ['GET', 'POST'])]
    public function newb(Request $request, CoursRepository $coursRepository, EntityManagerInterface $entityManager, int $coursId): Response
    {
        $cours = $coursRepository->find($coursId);
        if (!$cours) {
            $this->addFlash('error', 'Cours non trouvé.');
            return $this->redirectToRoute('inscription_indexb');
        }

        $inscription = new Inscription();
        $inscription->addCours($cours);
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($inscription);
            $entityManager->flush();
            $this->addFlash('success', 'Inscription créée avec succès.');
            return $this->redirectToRoute('inscription_indexb');
        }

        return $this->render('inscription/newb.html.twig', [
            'form' => $form->createView(),
            'cours' => $cours,
        ]);
    }

    // 📌 Route pour afficher une inscription en front
    #[Route('/{id}', name: 'inscription_show', methods: ['GET'])]
    public function show(Inscription $inscription): Response
    {
        return $this->render('inscription/show.html.twig', [
            'inscription' => $inscription,
        ]);
    }

    // 📌 Route pour afficher une inscription en back
    #[Route('/back/{id}', name: 'inscription_showb', methods: ['GET'])]
    public function showb(Inscription $inscription): Response
    {
        return $this->render('inscription/showb.html.twig', [
            'inscription' => $inscription,
        ]);
    }

    // 📌 Route pour modifier une inscription en front
    #[Route('/{id}/edit', name: 'inscription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Inscription $inscription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Inscription mise à jour avec succès.');
            return $this->redirectToRoute('inscription_index');
        }

        return $this->render('inscription/edit.html.twig', [
            'form' => $form->createView(),
            'inscription' => $inscription,
        ]);
    }

    // 📌 Route pour modifier une inscription en back
    #[Route('/back/{id}/edit', name: 'inscription_editb', methods: ['GET', 'POST'])]
    public function editb(Request $request, Inscription $inscription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Inscription mise à jour avec succès.');
            return $this->redirectToRoute('inscription_indexb');
        }

        return $this->render('inscription/editb.html.twig', [
            'form' => $form->createView(),
            'inscription' => $inscription,
        ]);
    }

    // 📌 Route pour supprimer une inscription en front
    #[Route('/{id}/delete', name: 'inscription_delete', methods: ['POST'])]
    public function delete(Request $request, Inscription $inscription, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $inscription->getId(), $request->request->get('_token'))) {
            $entityManager->remove($inscription);
            $entityManager->flush();
            $this->addFlash('success', 'Inscription supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('inscription_index');
    }

    // 📌 Route pour supprimer une inscription en back
    #[Route('/back/{id}/delete', name: 'inscription_deleteb', methods: ['POST'])]
    public function deleteb(Request $request, Inscription $inscription, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $inscription->getId(), $request->request->get('_token'))) {
            $entityManager->remove($inscription);
            $entityManager->flush();
            $this->addFlash('success', 'Inscription supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('inscription_indexb');
    }
}
