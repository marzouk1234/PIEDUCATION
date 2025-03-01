<?php

// src/Controller/EtudiantController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EtudiantController extends AbstractController
{
    #[Route('/etudiant', name: 'app_etudiant')]
    public function index(): Response
    {
        return $this->render('etudiant/index.html.twig', [
            'controller_name' => 'EtudiantController',
        ]);
    }

    #[IsGranted('ROLE_ETU')]
#[Route('/profile', name: 'app_profile')]
public function profile(Request $request, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();

    if (!$user instanceof User) {
        throw $this->createNotFoundException("User not found.");
    }

    $form = $this->createForm(ProfileType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Profile updated successfully.');
        return $this->redirectToRoute('app_etudiant');
    }

    return $this->render('etudiant/profile.html.twig', [
        'form' => $form->createView(),
    ]);
}

}
