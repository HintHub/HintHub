<?php

namespace App\Repository;

use App\Entity\Benachrichtigung;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Benachrichtigung|null find($id, $lockMode = null, $lockVersion = null)
 * @method Benachrichtigung|null findOneBy(array $criteria, array $orderBy = null)
 * @method Benachrichtigung[]    findAll()
 * @method Benachrichtigung[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BenachrichtigungRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Benachrichtigung::class);
    }

    // /**
    //  * @return Benachrichtigung[] Returns an array of Benachrichtigung objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Benachrichtigung
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
