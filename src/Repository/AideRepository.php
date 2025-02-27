<?php

namespace App\Repository;

use App\Entity\Aide;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Aide>
 */
class AideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Aide::class);
    }
  
   public function searchAides(string $query = '', string $sort = 'date_creation', string $order = 'DESC')
{
    $qb = $this->createQueryBuilder('a');

    if (!empty($query)) {
        $qb->andWhere('a.sujet LIKE :query OR a.description LIKE :query')
           ->setParameter('query', '%' . $query . '%');
    }

    if (in_array($sort, ['date_creation', 'sujet'])) {
        $qb->orderBy('a.' . $sort, $order);
    }

    return $qb->getQuery()->getResult();
}


    
    //    /**
    //     * @return Aide[] Returns an array of Aide objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Aide
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
