<?php

namespace App\Events;

use Exception;
use App\Entity\Fehler;
use App\Entity\Kommentar;
use App\Service\UserService;
use Psr\Log\LoggerInterface;
use App\Service\FehlerService;
use App\Service\KommentarService;
use App\Repository\FehlerRepository;
use App\Service\BenachrichtigungService;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Listens to Doctrine Events
 * 
 * @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de  )
 * @author karim.saad       ( karim.saad@iubh.de        )
 * 
 * Laste Edit 19.01.22
 */
class FehlerEventListener 
{
    private     $session;

    private     $maximumDeleteOperations = 100;
    private     $notClosedOrRejectedIds  = [];

    private     $fehlerService;
    private     $fehlerRepository;

    private $userService;
    private $kommentarService;
    private $benachrichtigungService;
    

    public function __construct ( 
        Session                 $session,
        FehlerService           $fehlerService,
        FehlerRepository        $fehlerRepository, 
        UserService             $userService,
        KommentarService        $kommentarService,
        BenachrichtigungService $benachrichtigungService
    )
    {
        $this -> session                    = $session;
        $this -> fehlerService              = $fehlerService;
        $this -> fehlerRepository           = $fehlerRepository;
        $this -> userService                = $userService;
        $this -> kommentarService           = $kommentarService;
        $this -> benachrichtigungService    = $benachrichtigungService;
    }

    public function preRemove   ( LifecycleEventArgs $args ): void
    {
        $entityManager  = $args -> getObjectManager ();
        $entity         = $args -> getObject        ();

        if ( !$entity instanceof Fehler ) 
            return;

        $fehlerId = $entity -> getId ();

        if ( !$entity -> isClosed () || ! $entity -> isRejected () ) 
        {
            $id = $entity -> getId ();
            array_push ( $this -> notClosedOrRejectedIds, $id );
            return;
        }

        // Detachen der offenen Fehler -> Löschen der closed/rejected
        $entity         ->  detachNotClosedChildren ();

        // Flush Entity Manager
        $entityManager  ->  flush ();
    }

    public function fehlerChangeEvent ( OnFlushEventArgs $args ) 
    {
        $entityManager  = $args          -> getEntityManager    ();
        $unitOfWork     = $entityManager -> getUnitOfWork       ();
        
        $entities       = $unitOfWork    -> getScheduledEntityUpdates ();

        $foo = [];

        $currentUser    = $this ->  userService -> getCurrentUser ();

        if ( $currentUser === null ) 
            return;
      
        foreach ( $entities as $entity ) 
        {
            //continue only if the object to be updated is a Fehler
            if ( $entity instanceof Fehler ) 
            {
                
                //get all the changed properties of the Fehler object
                $changes_set       = $unitOfWork -> getEntityChangeSet ( $entity );
                $changes           = array_keys ( $changes_set );
                $message           = $this -> generateStatusMessage ( $changes_set );
                $message           = "$currentUser hat die Fehlermeldung geändert:\n$message";
                $kommentarInstance = $this -> createKommentar ( $message, $entity, $currentUser );
                array_push( $foo, $kommentarInstance );
            }
        }

        if( !isset ( $foo[0] ) ) 
            return;
        
        $entityManager  ->  persist ( $foo [0] );
        $kommentarClass = get_class ( $foo [0] );
        $classMetadata  = $entityManager -> getClassMetadata ( $kommentarClass );
        $unitOfWork     -> computeChangeSet ( $classMetadata, $foo[0] );

        // TRIGGER BENACHRICHTIGUNG HIER
        $fehler = $foo[0] -> getFehler  ();
        $text   = $foo[0] -> getText    ();

        $this -> benachrichtigungService -> fireBenachrichtigungen ( $fehler, $text );
    }

    private function generateStatusMessage ( $changeSet )  
    {
        $message = "";

        try 
        {
            foreach ( $changeSet as $key => $value ) 
            {
                $subMessage = "'$key' wurde von '$value[0]' auf '$value[1]' gesetzt\n";
                $message .= $subMessage;
            }

        } 
        catch ( Exception $e ) 
        {
            dd( $changeSet );
        }

        return $message;
    }

    private function createKommentar ( $text, $fehler, $currentUser ) 
    {
        $dt = new \DateTime ();
        $kommentar = new Kommentar ();
        $kommentar -> setFehler               ( $fehler      );
        $kommentar -> setText                 ( $text        );
        $kommentar -> setEinreicher           ( $currentUser );
        $kommentar -> setDatumErstellt        ( $dt          );
        $kommentar -> setDatumLetzteAenderung ( $dt          );

        return $kommentar;
    }

    public function onFlush ( OnFlushEventArgs $onFlushEventArgs ): void
    {
        $this -> fehlerChangeEvent ( $onFlushEventArgs );
        
        $rePersistedIds = [];

        $entityManager  = $onFlushEventArgs ->  getEntityManager            ();
        $unitOfWork     = $entityManager    ->  getUnitOfWork               ();
        $toDelete       = $unitOfWork       ->  getScheduledEntityDeletions (); 

        // ISSUE: RAM Limit needs to be > 128 MB and operations time needs to be increased. (otherwise crash, therefore the check)
        if( count ( $toDelete ) > $this -> maximumDeleteOperations ) 
        {

            $this -> session -> getFlashBag() -> add (
                'danger',
                "Zu viele Operationen! (Abbruch)"
            );

            return;
        }

        foreach ( $toDelete as $entity ) 
        {
            // Check if Fehler is Fehler and if anything else than closed or rejected => DELETE REJECTED, CLOSED
            if ($entity instanceof Fehler && !( $entity -> isClosed () || $entity -> isRejected () ) )
            {
                // Persist the not closed errors again, so they don't get wiped (very DIRTY)
                $e = $entityManager -> persist ( $entity );
                
                if ($e === null)
                    continue;

                array_push ( $rePersistedIds, $e -> getId () );
            }
        }


        $amount = count ( $rePersistedIds );

        if ( $amount > 0 )
        {
            $str = "Es wurden $i Fehler wiederhergestellt, da diese nicht geschlossen/abgelehnt waren.";

            $this -> session -> getFlashBag() -> add (
                'warning',
                $str
            );
        } 
        else
        {
            if ( count ( $toDelete ) > 0 )
            {
                $str = "Der Fehler wurde gelöscht.";

                $this -> session -> getFlashBag() -> add (
                    'success',
                    $str
                );
            }
        }


        $idsStr = implode ( ',', $this -> notClosedOrRejectedIds );

        if ( strlen ( $idsStr ) > 0 )
        {
            $this -> session -> getFlashBag() -> add (
                'danger',
                "Fehler (IDs: $idsStr) wurden entkoppelt (NICHT abgelehnt oder geschlossen)"
            );
        }
    }
}
