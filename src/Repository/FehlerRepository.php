<?php

namespace App\Repository;

use App\Entity\Fehler;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fehler|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fehler|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fehler[]    findAll()
 * @method Fehler[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FehlerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fehler::class);
    }

    // /**
    //  * @return Fehler[] Returns an array of Fehler objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Fehler
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
