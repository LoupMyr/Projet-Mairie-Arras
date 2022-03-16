<?php

namespace App\Repository;

use App\Entity\Cantine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cantine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cantine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cantine[]    findAll()
 * @method Cantine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CantineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cantine::class);
    }

    // /**
    //  * @return Cantine[] Returns an array of Cantine objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cantine
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
