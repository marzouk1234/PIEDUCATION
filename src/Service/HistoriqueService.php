<?php
namespace App\Service;

use App\Entity\HistoriqueEvaluation;
use Doctrine\ORM\EntityManagerInterface;

class HistoriqueService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addHistorique(string $action, string $details, int $evaluationId, ?\DateTime $dateAction = null): void
    {
        $dateAction = $dateAction ?? new \DateTime(); // Set the current date and time if not provided
    
        $historique = new HistoriqueEvaluation();
        $historique->setAction($action)
                   ->setDetails($details)
                   ->setEvaluationId($evaluationId)
                   ->setDateAction($dateAction);  // Ensure date_action is set
    
        $this->entityManager->persist($historique);
        $this->entityManager->flush();
    }

}
