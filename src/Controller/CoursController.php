<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Form\CoursType;
use App\Repository\CoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cours')]
final class CoursController extends AbstractController
{ 
    #[Route(name: 'cours_index', methods: ['GET'])]
    public function index(CoursRepository $coursRepository): Response
    {
        return $this->render('cours/index.html.twig', [
            'cours' => $coursRepository->findAll(),
        ]);
    }

    #[Route('/back', name: 'cours_indexb', methods: ['GET'])]
    public function indexb(CoursRepository $coursRepository): Response
    {
        return $this->render('cours/indexb.html.twig', [
            'cours' => $coursRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'cours_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cours = new Cours();
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cours);
            $entityManager->flush();

            return $this->redirectToRoute('cours_index');
        }

        return $this->render('cours/new.html.twig', [
            'cours' => $cours,
            'form' => $form,
        ]);
    }

    #[Route('/back/new', name: 'cours_newb', methods: ['GET', 'POST'])]
    public function newb(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cours = new Cours();
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cours);
            $entityManager->flush();

            return $this->redirectToRoute('cours_indexb');
        }

        return $this->render('cours/newb.html.twig', [
            'cours' => $cours,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'cours_show', methods: ['GET'])]
    public function show(Cours $cours): Response
    {
        return $this->render('cours/show.html.twig', [
            'cours' => $cours,
        ]);
    }

    #[Route('/back/{id}', name: 'cours_showb', methods: ['GET'])]
    public function showb(Cours $cours): Response
    {
        return $this->render('cours/showb.html.twig', [
            'cours' => $cours,
        ]);
    }

    #[Route('/{id}/edit', name: 'cours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cours $cours, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('cours_index');
        }

        return $this->render('cours/edit.html.twig', [
            'cours' => $cours,
            'form' => $form,
        ]);
    }

    #[Route('/back/{id}/editb', name: 'cours_editb', methods: ['GET', 'POST'])]
    public function editb(Request $request, Cours $cours, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('cours_indexb');
        }

        return $this->render('cours/editb.html.twig', [
            'cours' => $cours,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'cours_delete', methods: ['POST'])]
    public function delete(Request $request, Cours $cours, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $cours->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cours);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cours_index');
    }

    #[Route('/back/{id}', name: 'cours_deleteb', methods: ['POST'])]
    public function deleteb(Request $request, Cours $cours, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $cours->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cours);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cours_indexb');
    }
}
