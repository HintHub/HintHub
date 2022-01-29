<?php
// generated by Symfony (php bin/console make:entity) 
// @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de ) 
namespace App\Repository;

use App\Entity\User;
use App\Entity\Fehler;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    //Wir nutzen die Repositories, da ja dinge wie der QueryBuilder geerbt werden

    /**
     * Admin und Verwaltung sollen zugriff auf alle Fehler haben
     * Um Fehler aller Zustaende abzugreifen, nimm einfach "all" fuer status
     * 
     * status ist ein string
     */
    public function findAllByUserAndStatus(User $user, $status) 
    {
        $response = $this -> createQueryBuilder ('f');
        $response = $this -> addWheres ( $response, $user );
        $response -> setParameter ( 'status',  $status  );
        $repsonse -> getQuery() -> useQueryCache(true) -> useResultCache(true);  // and here
        return $response;
    }


    /**
     * Counts all Fehler by Status
     */
    public function countAllByUserAndStatus ( $user, $status ) 
    {
        $response = $this -> createQueryBuilder ('f');
        $response -> select ( "count (f.id) " );
        $response = $this -> addWheres ( $response, $user );
        $response -> setParameter ( 'status', $status     );
        $response -> getQuery()  -> useQueryCache(true)   -> useResultCache(true);
        return $response -> getQuery() -> useQueryCache(true)   -> useResultCache(true) -> getSingleScalarResult();  
    }

    private function addWheres  ($response, $user )
    {
        if ( $user -> isAdmin () || $user -> isVerwaltung () )
            $response -> andWhere     ( 'f.status IN (:status)' );

        if ( $user -> isStudent () )
            $response
                -> andWhere     ( 'f.status IN (:status) AND f.einreicher IN (:studentId)' )
                -> setParameter ( 'studentId', $user -> getId ()     );

        if ( $user -> isTutor () )
        {
            $tutorModule = $user -> getOnlyIdsFromTutorIn ();
            $response
                -> andWhere     ( 'f.status IN (:status) AND f.einreicher IN (:module)' )
                -> setParameter('module', $tutorModule, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);
        }

        return $response;
    }

    //TODO: Absprechen mit Stefan: Hat die obere Methode gebuggt?

    // Fehler by User und jeweils nach Status
    // Offene Fehler

    public function countAllByUserAndOpen       ( User $user ) 
    {
        return $this -> countAllByUserAndStatus ($user, "OPEN"       );
    }

    // Fehler by User und jeweils nach Status
    // Geschlossene Fehler

    public function countAllByUserAndClosed     ( User $user )
    {
        return $this -> countAllByUserAndStatus ($user, "CLOSED"     );
    }
    // Fehler by User und jeweils nach Status
    // Wartend Fehler

    public function countAllByUserAndWaiting    ( User $user ) 
    {
        return $this -> countAllByUserAndStatus ($user, "WAITING"    );
    }
    // Fehler by User und jeweils nach Status
    // Eskalierte Fehler

    public function countAllByUserAndESCALATED  ( User $user ) 
    {
        return $this -> countAllByUserAndStatus ($user, "ESCALATED"  );
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

    public function getAllFehlerForEscalation() {
        $queryBuilder = $this->createQueryBuilder('f');

        $result = $queryBuilder
            -> andWhere    ("f.status in ('OPEN', 'WAITING')")
            -> getQuery ()
            -> useQueryCache(true)   
            -> useResultCache(true)
            -> getResult()
        ;
        
        return $result;
    }
}
