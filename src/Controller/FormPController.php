<?php

namespace App\Controller;

use App\Entity\FormP;
use App\Form\AddEditFormPType;
use App\Repository\FormPRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;






#[Route('/formp')]
class FormPController extends AbstractController
{
    #[Route('/', name: 'app_form_p')]
    public function index(): Response
    {
        return $this->render('formp/index.html.twig', [
            'controller_name' => 'FormPController',
        ]);
    }



    #[Route('/list', name: 'app_formp_list')]
    public function listFormP(FormPRepository $formPRepository): Response
    {
        $formPsDB = $formPRepository->findAll();
        return $this->render('formp/list.html.twig', ['formPs' => $formPsDB]);
    }

    #[Route('/new', name: 'app_formp_new')]
    public function newFormP(Request $request, EntityManagerInterface $em): Response
    {
        $formP = new FormP();
        $form = $this->createForm(AddEditFormPType::class, $formP);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($formP);
            $em->flush();
            return $this->redirectToRoute('app_formp_list');
        }

        return $this->render('formp/form.html.twig', [
            'title' => 'Add FormP',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_formp_edit')]
    public function editFormP($id, Request $request, EntityManagerInterface $em, FormPRepository $formPRepository): Response
    {
        $formP = $formPRepository->find($id);
        if (!$formP) {
            throw $this->createNotFoundException('FormP not found');
        }

        $form = $this->createForm(AddEditFormPType::class, $formP);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_formp_list');
        }

        return $this->render('formp/form.html.twig', [
            'title' => 'Update FormP',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/remove/{id}', name: 'app_formp_remove')]
    public function removeFormP($id, FormPRepository $formPRepository, EntityManagerInterface $em): Response
    {
        $formP = $formPRepository->find($id);
        if (!$formP) {
            throw $this->createNotFoundException('FormP not found');
        }

        $em->remove($formP);
        $em->flush();

        return $this->redirectToRoute('app_formp_list');
    }
}
