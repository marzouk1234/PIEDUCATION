<?php

namespace App\Controller;

use App\Entity\FormP;
use App\Form\AddEditFormPType;
use App\Form\Search\SearchFormType;
use App\Repository\FormPRepository;
use App\Service\InappropriateWordFilter; // Importez le service
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;

#[Route('/formp')]
class FormPController extends AbstractController
{
    private $wordFilter;

    // Injection du service via le constructeur
    public function __construct(InappropriateWordFilter $wordFilter)
    {
        $this->wordFilter = $wordFilter;
    }

    #[Route('/', name: 'app_form_p')]
    public function index(): Response
    {
        return $this->render('formp/index.html.twig', [
            'controller_name' => 'FormPController',
        ]);
    }

    #[Route('/list', name: 'app_formp_list')]
    public function listFormP(FormPRepository $formPRepository, Request $request, PaginatorInterface $paginator): Response
    {
        // Récupération de la requête de recherche
        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);

        $query = $formPRepository->createQueryBuilder('f');

        // Si le formulaire de recherche est soumis et valide
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();

            // Ajout des conditions de recherche
            if ($data['sujet']) {
                $query->andWhere('f.sujet LIKE :sujet')
                    ->setParameter('sujet', '%' . $data['sujet'] . '%');
            }

            if ($data['contenu']) {
                $query->andWhere('f.contenu LIKE :contenu')
                    ->setParameter('contenu', '%' . $data['contenu'] . '%');
            }

            if ($data['auteur']) {
                $query->andWhere('f.auteur LIKE :auteur')
                    ->setParameter('auteur', '%' . $data['auteur'] . '%');
            }

            if ($data['datePub']) {
                $query->andWhere('f.date_pub = :datePub')
                    ->setParameter('datePub', $data['datePub']);
            }
        }

        // Pagination des résultats
        $pagination = $paginator->paginate(
            $query->getQuery(), // La requête
            $request->query->getInt('page', 1), // Numéro de page (1 par défaut)
            5 // Nombre d'éléments par page
        );

        return $this->render('formp/list.html.twig', [
            'pagination' => $pagination,
            'searchForm' => $searchForm->createView(),
        ]);
    }

    #[Route('/new', name: 'app_formp_new')]
    public function newFormP(Request $request, EntityManagerInterface $em): Response
    {
        $formP = new FormP();
        $form = $this->createForm(AddEditFormPType::class, $formP);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le contenu du formulaire
            $contenu = $formP->getContenu();

            // Vérifier si le contenu contient des mots inappropriés
            if ($this->wordFilter->containsInappropriateWords($contenu)) {
                $this->addFlash('warning', 'Votre texte contient des mots inappropriés.');
            }

            // Filtrer les mots inappropriés
            $contenuFiltre = $this->wordFilter->filterInappropriateWords($contenu);
            $formP->setContenu($contenuFiltre);

            // Enregistrer le formulaire
            $em->persist($formP);
            $em->flush();

            $this->addFlash('success', 'Formulaire ajouté avec succès !');

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
            // Récupérer le contenu du formulaire
            $contenu = $formP->getContenu();

            // Vérifier si le contenu contient des mots inappropriés
            if ($this->wordFilter->containsInappropriateWords($contenu)) {
                $this->addFlash('warning', 'Votre texte contient des mots inappropriés.');
            }

            // Filtrer les mots inappropriés
            $contenuFiltre = $this->wordFilter->filterInappropriateWords($contenu);
            $formP->setContenu($contenuFiltre);

            // Enregistrer les modifications
            $em->flush();

            $this->addFlash('success', 'Formulaire mis à jour avec succès !');

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