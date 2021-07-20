<?php

namespace App\Repository;

use App\Entity\AdminCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdminCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminCode[]    findAll()
 * @method AdminCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminCode::class);
    }

    // /**
    //  * @return AdminCode[] Returns an array of AdminCode objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AdminCode
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
