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
    public function __construct ( ManagerRegistry $registry )
    {
        parent::__construct ( $registry, Benachrichtigung::class );
    }

    public function getCountUnreadBenachrichtigungen ( $userId )
    {
        return $this -> createQueryBuilder          ('b')
                        -> select                   ( 'COUNT(b.id)'                           ) 
                        -> where                    ( 'b.user IN (:userId) AND b.gelesen = 0' )
                        -> setParameter             ( 'userId', $userId                       )
                        -> getQuery                 ()
                        -> useQueryCache            ( true )
                        -> getSingleScalarResult    ();
    }
}
