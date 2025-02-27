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

// Importation des classes pour générer le QR Code
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;

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
            'title' => 'Ajouter un formulaire',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_formp_edit')]
    public function editFormP($id, Request $request, EntityManagerInterface $em, FormPRepository $formPRepository): Response
    {
        $formP = $formPRepository->find($id);
        if (!$formP) {
            throw $this->createNotFoundException('FormP non trouvé');
        }

        $form = $this->createForm(AddEditFormPType::class, $formP);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_formp_list');
        }

        return $this->render('formp/form.html.twig', [
            'title' => 'Modifier un formulaire',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/remove/{id}', name: 'app_formp_remove')]
    public function removeFormP($id, FormPRepository $formPRepository, EntityManagerInterface $em): Response
    {
        $formP = $formPRepository->find($id);
        if (!$formP) {
            throw $this->createNotFoundException('FormP non trouvé');
        }

        $em->remove($formP);
        $em->flush();

        return $this->redirectToRoute('app_formp_list');
    }

    #[Route('/form/qrcode/{id}', name: 'form_qrcode')]
    public function generateFormQrCode(int $id, FormPRepository $formPRepository): Response
    {
        $form = $formPRepository->find($id);

        if (!$form) {
            throw $this->createNotFoundException('Formulaire non trouvé.');
        }

        // Construire le contenu du QR Code avec toutes les informations
        $content = sprintf(
            "Sujet: %s\nAuteur: %s\nDate: %s\nContenu: %s",
            $form->getSujet() ?? 'N/A',
            $form->getAuteur() ?? 'N/A',
            $form->getDatePub() ? $form->getDatePub()->format('d/m/Y') : 'N/A',
            $form->getContenu() ?? 'Aucune donnée'
        );

        // Générer le QR Code
        $qrCode = Builder::create()
            ->writer(new PngWriter())
            ->data($content)
            ->encoding(new Encoding('UTF-8'))
            ->size(300)
            ->margin(10)
            ->labelText('Scan Me')
            ->build();

        return new Response($qrCode->getString(), 200, ['Content-Type' => 'image/png']);
    }
}
