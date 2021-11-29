<?php

namespace App\Repository;

use App\Entity\Kommentar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Kommentar|null find($id, $lockMode = null, $lockVersion = null)
 * @method Kommentar|null findOneBy(array $criteria, array $orderBy = null)
 * @method Kommentar[]    findAll()
 * @method Kommentar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KommentarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Kommentar::class);
    }

    // /**
    //  * @return Kommentar[] Returns an array of Kommentar objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Kommentar
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
