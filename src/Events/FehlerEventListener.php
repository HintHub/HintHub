<?php

namespace App\Events;

use Exception;
use App\Entity\Fehler;
use Psr\Log\LoggerInterface;
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

    public function __construct ( Session $session )
    {
        $this -> session = $session;
    }

    public function preRemove   ( LifecycleEventArgs $args ): void
    {
        $entityManager = $args->getObjectManager();
        $entity = $args -> getObject();

        if ( !$entity instanceof Fehler ) 
        {
            return;
        }

        $fehlerId = $entity -> getId();

        if ( !$entity -> isClosed() || ! $entity -> isRejected() ) 
        {
            $id = $entity -> getId ();
            
            array_push($this -> notClosedOrRejectedIds, $id);

            return;
        }

        // Detachen der offenen Fehler -> Löschen der closed/rejected
        $entity         ->  detachNotClosedChildren();

        // Flush Entity Manager
        $entityManager  ->  flush();
    }

    public function onFlush ( OnFlushEventArgs $onFlushEventArgs ): void
    {
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


        $idsStr = implode(',', $this -> notClosedOrRejectedIds);

        if ( strlen ( $idsStr ) > 0 )
        {
            $this -> session -> getFlashBag() -> add (
                'danger',
                "Fehler (IDs: $idsStr) wurden entkoppelt (NICHT abgelehnt oder geschlossen)"
            );
        }
    }
}