<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Form\EvaluationType;
use App\Repository\EvaluationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\MailerService;
use App\Entity\Etudiant;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\HistoriqueService;


use Twilio\Rest\Client;

#[Route('/eval')]
final class EvalController extends AbstractController
{
    private HistoriqueService $historiqueService;

    public function __construct(HistoriqueService $historiqueService)
    {
        $this->historiqueService = $historiqueService;
    }

    #[Route(name: 'app_eval_index', methods: ['GET'])]
    public function index(EvaluationRepository $evaluationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $evaluationRepository->createQueryBuilder('e')->getQuery();
        $evaluations = $paginator->paginate(
            $query, 
            $request->query->getInt('page', 1), 
            5
        );

        return $this->render('eval/index.html.twig', [
            'evaluations' => $evaluations,
        ]);
    }

    #[Route('/back', name: 'app_eval_indexb', methods: ['GET'])]
    public function indexb(EvaluationRepository $evaluationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $evaluationRepository->createQueryBuilder('e')->getQuery();
        $evaluations = $paginator->paginate(
            $query, 
            $request->query->getInt('page', 1), 
            5
        );

        return $this->render('eval/indexb.html.twig', [
            'evaluations' => $evaluations,
        ]);
    }


    #[Route('/new', name: 'app_eval_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $evaluation = new Evaluation();
        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evaluation);
            $entityManager->flush();
            $this->historiqueService->addHistorique('Modification', $evaluation->getTitre(), $evaluation->getId());

    
            // 📌 Vérification des variables Twilio
            $sid = $_ENV['TWILIO_SID'] ?? null;
            $authToken = $_ENV['TWILIO_AUTH_TOKEN'] ?? null;
            $twilioPhoneNumber = $_ENV['TWILIO_PHONE_NUMBER'] ?? null;
    
            if (!$sid || !$authToken || !$twilioPhoneNumber) {
                throw new \Exception("⚠️ Erreur: Les informations Twilio ne sont pas définies dans .env");
            }
    
            $client = new Client($sid, $authToken);
    
            foreach ($evaluation->getEtudiants() as $etudiant) {
                $studentPhoneNumber = $etudiant->getTel();
    
                // 📌 Vérifier si le numéro est vide
                if (!$studentPhoneNumber) {
                    dump("⚠️ Numéro manquant pour l'étudiant : " . $etudiant->getNom());
                    continue;
                }
    
                // 📌 Ajouter +216 si le numéro est en format local
                if (strpos($studentPhoneNumber, "+216") !== 0) {
                    $studentPhoneNumber = "+216" . $studentPhoneNumber;
                }
    
                try {
                    $message = $client->messages->create(
                        $studentPhoneNumber, // 📌 Numéro du destinataire
                        [
                            'from' => $twilioPhoneNumber,
                            'body' => "Tu seras évalué prochainement un ". $evaluation->getType() ." en matière " . $evaluation->getTitre() . "' le " . $evaluation->getDate()->format('Y-m-d') . " 📅."
                        ]
                    );
    
                    dump("✅ Message envoyé à " . $studentPhoneNumber . " avec SID: " . $message->sid);
                } catch (\Exception $e) {
                    dump("❌ Erreur lors de l'envoi du message WhatsApp: " . $e->getMessage());
                }
            }
    
            // 📧 Envoi d'un e-mail (facultatif)
            $email = (new Email())
                ->from('yo.yotalent7@gmail.com')
                ->to('marammelki12@gmail.com')
                ->subject('Nouvelle évaluation assignée')
                ->html('<h1>Tu seras évalué</h1>');
    
            $mailer->send($email);
    
            return $this->redirectToRoute('app_eval_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('eval/new.html.twig', [
            'evaluation' => $evaluation,
            'form' => $form->createView(),
        ]);
    }
    
    



   

    #[Route('/newb', name: 'app_eval_newb', methods: ['GET', 'POST'])]
    public function newb(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evaluation = new Evaluation();
        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evaluation);
            $entityManager->flush();

            return $this->redirectToRoute('app_eval_indexb', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('eval/newb.html.twig', [
            'evaluation' => $evaluation,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_eval_show', methods: ['GET'])]
    public function show(int $id, EvaluationRepository $evaluationRepository): Response
    {
        $evaluation = $evaluationRepository->find($id);
    
        if (!$evaluation) {
            throw $this->createNotFoundException('L’évaluation demandée n’existe pas.');
        }
    
        return $this->render('eval/show.html.twig', [
            'evaluation' => $evaluation,
        ]);
    }
    

    #[Route('/back/{id}', name: 'app_eval_showb', methods: ['GET'])]
    public function showb(Evaluation $evaluation): Response
    {
        return $this->render('eval/showb.html.twig', [
            'evaluation' => $evaluation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_eval_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evaluation $evaluation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->historiqueService->addHistorique('Modification', 'test1', (int) $evaluation->getId());


            return $this->redirectToRoute('app_eval_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('eval/edit.html.twig', [
            'evaluation' => $evaluation,
            'form' => $form,
        ]);
    }

    #[Route('/back/{id}/edit', name: 'app_eval_editb', methods: ['GET', 'POST'])]
    public function editb(Request $request, Evaluation $evaluation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_eval_indexb', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('eval/editb.html.twig', [
            'evaluation' => $evaluation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_eval_delete', methods: ['POST'])]
    public function delete(Request $request, Evaluation $evaluation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $evaluation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evaluation);
            $entityManager->flush();
            $this->historiqueService->addHistorique('Suppression', 'test mathematique', $evaluation->getId());

        }

        return $this->redirectToRoute('app_eval_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/back/{id}', name: 'app_eval_deleteb', methods: ['POST'])]
    public function deleteb(Request $request, Evaluation $evaluation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $evaluation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evaluation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_eval_indexb', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/eval/search-evaluation', name: 'app_eval_search', methods: ['GET'])]
    public function searchEvaluation(Request $request, EvaluationRepository $evaluationRepository, SerializerInterface $serializer): JsonResponse
    {
        $query = $request->query->get('q', '');
        if (!$query) {
            return new JsonResponse([]);
        }
    
        // Récupérer uniquement les titres commençant par la lettre saisie
        $evaluations = $evaluationRepository->createQueryBuilder('e')
            ->where('LOWER(e.titre) LIKE :query')
            ->setParameter('query', strtolower($query) . '%') // % après pour "commence par"
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    
        // Utilisation du Serializer pour transformer les entités en JSON
        $data = $serializer->normalize($evaluations, null, ['attributes' => ['id', 'titre']]);
    
        return $this->json($data);
    }
    
}