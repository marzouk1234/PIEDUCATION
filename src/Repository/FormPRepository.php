<?php

namespace App\Repository;

use App\Entity\FormP;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FormP>
 *
 * @method FormP|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormP|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormP[]    findAll()
 * @method FormP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormPRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormP::class);
    }

    /**
     * Méthode pour rechercher des formulaires en fonction de critères.
     *
     * @param string|null $sujet     Critère de recherche sur le sujet.
     * @param string|null $contenu   Critère de recherche sur le contenu.
     * @param string|null $auteur    Critère de recherche sur l'auteur.
     * @param \DateTimeImmutable|null $datePub Critère de recherche sur la date de publication.
     * @return FormP[] Retourne un tableau d'objets FormP correspondant aux critères.
     */
    public function search(?string $sujet = null, ?string $contenu = null, ?string $auteur = null, ?\DateTimeImmutable $datePub = null): array
    {
        // Création du QueryBuilder
        $qb = $this->createQueryBuilder('f');

        // Ajout des conditions de recherche en fonction des critères fournis
        if ($sujet) {
            $qb->andWhere('f.sujet LIKE :sujet')
               ->setParameter('sujet', '%' . $sujet . '%');
        }

        if ($contenu) {
            $qb->andWhere('f.contenu LIKE :contenu')
               ->setParameter('contenu', '%' . $contenu . '%');
        }

        if ($auteur) {
            $qb->andWhere('f.auteur LIKE :auteur')
               ->setParameter('auteur', '%' . $auteur . '%');
        }

        if ($datePub) {
            $qb->andWhere('f.date_pub = :datePub')
               ->setParameter('datePub', $datePub);
        }

        // Exécution de la requête et retour des résultats
        return $qb->getQuery()->getResult();
    }

    // Vous pouvez conserver les méthodes existantes générées par Symfony si nécessaire

    // /**
    //  * @return FormP[] Returns an array of FormP objects
    //  */
    // public function findByExampleField($value): array
    // {
    //     return $this->createQueryBuilder('f')
    //         ->andWhere('f.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->orderBy('f.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    // /**
    //  * @return FormP|null Returns a single FormP object
    //  */
    // public function findOneBySomeField($value): ?FormP
    // {
    //     return $this->createQueryBuilder('f')
    //         ->andWhere('f.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }
}