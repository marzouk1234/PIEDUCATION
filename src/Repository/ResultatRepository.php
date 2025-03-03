<?php

namespace App\Repository;

use App\Entity\Resultat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Resultat>
 */
class ResultatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resultat::class);
    }
    public function findWithEvaluation($id): ?Resultat
    {
    return $this->createQueryBuilder('r')
        ->leftJoin('r.evaluation', 'e')
        ->addSelect('e')
        ->where('r.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult();
}

 
    public function countResultsByNote(): array
    {
        return $this->createQueryBuilder('r')
            ->select("
                CASE 
                    WHEN r.note < 10 THEN '< 10'
                    WHEN r.note BETWEEN 10 AND 15 THEN '10 - 15'
                    ELSE '> 15'
                END as category, COUNT(r.id) as count
            ")
            ->groupBy('category')
            ->getQuery()
            ->getResult();
    }
    

}


    //    /**
    //     * @return Resultat[] Returns an array of Resultat objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Resultat
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

