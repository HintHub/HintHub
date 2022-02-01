<?php
/*
    generated by Symfony (php bin/console make:entity) 
    @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de ) 

    Last edit by karim.saad (karim.saad@iubh.de) 01.02.22 0102
*/

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct ( ManagerRegistry $registry )
    {
        parent::__construct ( $registry, User::class );
    }

    public function getAllByRole ( $role ) 
    {
        if ( empty ( $role ) ) 
            throw new Exception ( "Rolle angeben!" );
        
        return $this -> createQueryBuilder ( 'u' )
                    -> andWhere            ( 'u.ROLES LIKE :role'  )
                    -> setParameter        ( 'role', '%'.$role.'%' )
                    -> getQuery            ()
                    -> useQueryCache       ( true )   
                    -> useResultCache      ( true )
                    -> getResult           ();
    }

    // Alle Studenten ausgeben
    public function getAllStudents      ()
    {
        return $this -> countUsersByRole ( "ROLE_STUDENT"    ); 
    }

    // Alle Tutoren ausgeben
    public function getAllTutors        ()
    {
        return $this -> countUsersByRole ( "ROLE_TUTOR"      ); 
    }

     // Alle Externen ausgeben
     public function getAllExtern       ()
     {
        return $this -> countUsersByRole ( "ROLE_EXTERN"     ); 
     }

     // Alle Verwaltung ausgeben
     public function getAllVerwaltung   ()
     {
        return $this -> countUsersByRole ( "ROLE_VERWALTUNG" ); 
     }

     // count all Users
     public function getAllUsers        () 
     {
        return $this -> createQueryBuilder          ( 'u' )
                        -> select                   ( 'count (u.id)' )
                        -> getQuery                 ()
                        -> useQueryCache            ( true )
                        -> useResultCache           ( true )
                        -> setResultCacheLifetime   ( 60   )
                        -> getSingleScalarResult    ();
     }


     public function countUsersByRole ( $role )
     {
        return $this -> createQueryBuilder          ( 'u' )
                        -> select                   ( 'count(u.id)'         )
                        -> andWhere                 ( 'u.ROLES LIKE :role'  )
                        -> setParameter             ( 'role', '%'.$role.'%' )
                        -> getQuery                 ()
                        -> useQueryCache            ( true )  
                        -> useResultCache           ( true )
                        -> setResultCacheLifetime   ( 60   )
                        -> getSingleScalarResult    ();
     }
}
