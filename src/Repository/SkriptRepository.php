<?php

namespace App\Repository;

use App\Entity\Skript;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Skript|null find($id, $lockMode = null, $lockVersion = null)
 * @method Skript|null findOneBy(array $criteria, array $orderBy = null)
 * @method Skript[]    findAll()
 * @method Skript[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkriptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Skript::class);
    }

    // /**
    //  * @return Skript[] Returns an array of Skript objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Skript
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
