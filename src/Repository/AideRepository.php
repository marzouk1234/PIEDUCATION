<?php

namespace App\Repository;

use App\Entity\Aide;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class AideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Aide::class);
    }

    /**
     * Recherche des aides avec tri et pagination.
     * 
     * @param string $query Recherche par sujet ou description.
     * @param string $sort Champ de tri (ex: 'date_creation', 'sujet').
     * @param string $order Ordre de tri ('ASC' ou 'DESC').
     * @return Query Requête Doctrine pour la pagination.
     */
    public function searchAides(string $query = '', string $sort = 'date_creation', string $order = 'DESC'): Query
    {
        $qb = $this->createQueryBuilder('a');

        if (!empty($query)) {
            $qb->andWhere('a.sujet LIKE :query OR a.description LIKE :query')
               ->setParameter('query', '%' . $query . '%');
        }

        if (in_array($sort, ['date_creation', 'sujet'])) {
            $qb->orderBy('a.' . $sort, $order);
        }

        return $qb->getQuery(); // Retourne la requête pour la pagination
    }

    /**
     * Récupère les aides les plus récentes (ex: les 5 dernières aides).
     * 
     * @param int $limit Nombre de résultats à récupérer.
     * @return Aide[] Tableau d'objets Aide.
     */
    public function findRecentAides(int $limit = 5): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.date_creation', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les aides associées à un formulaire spécifique.
     * 
     * @param int $formId ID du formulaire.
     * @return Aide[] Tableau d'objets Aide.
     */
    public function findByForm(int $formId): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.form = :formId')
            ->setParameter('formId', $formId)
            ->orderBy('a.date_creation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
