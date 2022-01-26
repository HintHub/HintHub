<?php

namespace App\Service;

use App\Service\UserService;
use App\Entity\Benachrichtigung;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use App\Repository\BenachrichtigungRepository;

/**

 */
class BenachrichtigungService
{ 

    private $entityManager;

    private $benachrichtigungRepository;

    private $userService;

    public function __construct (
        BenachrichtigungRepository $benachrichtigungRepository,
        EntityManagerInterface $entityManager,
        UserService $userService
    ) 
    {
        $this -> entityManager              = $entityManager;
        $this -> benachrichtigungRepository = $benachrichtigungRepository;
        $this -> userService                = $userService;
    }

    public function findById( int $id ): Benachrichtigung 
    {
        return $this -> benachrichtigungRepository -> find ($id);
    }

    public function findAll(): array 
    {
        return $this -> benachrichtigungRepository -> findAll ();
    }

    public function save ( Benachrichtigung $benachrichtigung ): Benachrichtigung 
    {
        $this -> entityManager -> persist ($benachrichtigung);
        $this -> entityManager -> flush   ();

        return $benachrichtigung;
    }

    public function update ( Benachrichtigung $benachrichtigung ): Benachrichtigung 
    {
        $toUpdate = $this -> findById ( $benachrichtigung -> getId () );

        $toUpdate = $this -> setText ( $benachrichtigung -> getText() );

        return $toUpdate;
    }

    public function delete ( int $id ): int 
    {
        $toDelete = $this -> findById ( $id );
        
        $this -> entityManager  -> remove ( $toDelete );
        $this -> entityManager  -> flush ();

        return $toDelete -> getId ();
    }

    //creates the "same" notification twice - once for student once for tutor
    public function fireBenachrichtigungen ( $fehler, $text ) 
    {
        $einreicher = $fehler -> getEinreicher ();
        $tutor      = $fehler -> getSkript () -> getModul () -> getTutor ();

        $this -> saveBenachrichtigung ( $fehler, $text, $einreicher );
        $this -> saveBenachrichtigung ( $fehler, $text, $tutor      );
    }

    private function saveBenachrichtigung($fehler, $text, $user) 
    {
        $dt = new \DateTime ();
        $benachrichtigung = new Benachrichtigung ();
        $benachrichtigung -> setText                  ( $text   );
        $benachrichtigung -> setUser                  ( $user   ) ;
        $benachrichtigung -> setFehler                ( $fehler );
        $benachrichtigung -> setDatumErstellt         ( $dt     );
        $benachrichtigung -> setDatumLetzteAenderung  ( $dt     );
        $benachrichtigung -> setGelesen               ( false   );

        //persist on Flush!
        $unitOfWork     = $this -> entityManager -> getUnitOfWork   ();
        $this -> entityManager  ->  persist( $benachrichtigung );
        $classMetadata  = $this -> entityManager -> getClassMetadata ( Benachrichtigung::class );
        $unitOfWork     -> computeChangeSet( $classMetadata, $benachrichtigung );

    }

    public function getCountUnreadBenachrichtigungen ()
    {

        $currentUser    = $this -> userService -> getCurrentUser ();

        $isAdmin        = $currentUser -> isAdmin       ();
        $isExtern       = $currentUser -> isExtern      ();
        $isVerwaltung   = $currentUser -> isVerwaltung  ();

        $notNeeded      = $currentUser === null || $isAdmin || $isExtern || $isVerwaltung;

        if ( $notNeeded )
            return 0;

        $userId       = $currentUser -> getId ();

        $countUnreadBenachrichtigungen = $this -> entityManager 
            -> createQueryBuilder       ()
            -> select                   ( 'COUNT(b.id)'                           ) 
            -> from                     ( Benachrichtigung::class,          'b'   )
            -> where                    ( 'b.user IN (:userId) AND b.gelesen = 0' )
            -> setParameter             ( 'userId', $userId                       )
            -> getQuery                 ()
            -> useQueryCache            ( true )
            -> getSingleScalarResult    ();
        
        return $countUnreadBenachrichtigungen;
    }


    /**
     * Sets the Read Flag of b to true
     */
    public function markRead ( $bId )
    {
        if ($bId === null || $bId < 1)
            return false;
        
        try 
        {
            $b = $this -> benachrichtigungRepository -> find ( $bId );
            
            if ( $b === null )
                return false;

            $b -> setGelesen (true);

            $this -> entityManager -> flush   ();

            return true;
        } 
        catch ( Exception $e )
        {
            dd($e);
            return false;
        }
    }
}