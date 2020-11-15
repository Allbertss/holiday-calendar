<?php

namespace App\Repository;

use App\Entity\HolidayQuery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HolidayQuery|null find($id, $lockMode = null, $lockVersion = null)
 * @method HolidayQuery|null findOneBy(array $criteria, array $orderBy = null)
 * @method HolidayQuery[]    findAll()
 * @method HolidayQuery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HolidayQueryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HolidayQuery::class);
    }

    // /**
    //  * @return HolidayQuery[] Returns an array of HolidayQuery objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HolidayQuery
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
