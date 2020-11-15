<?php

namespace App\Repository;

use App\Entity\Query2;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Query2|null find($id, $lockMode = null, $lockVersion = null)
 * @method Query2|null findOneBy(array $criteria, array $orderBy = null)
 * @method Query2[]    findAll()
 * @method Query2[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Query2Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Query2::class);
    }

    // /**
    //  * @return Query2[] Returns an array of Query2 objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Query2
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
