<?php

namespace App\Events;

use Exception;
use App\Entity\Fehler;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class FehlerEventListener 
{
    public function preRemove(LifecycleEventArgs $args): void
    {
        
        $entity = $args -> getObject();

        if ( !$entity instanceof Fehler ) 
        {
            return;
        }

        $entityManager = $args->getObjectManager();

        if ( !$entity -> isClosed() ) 
        {
            return;
        }

        /* Idee: Loeschen tut er ohnehin. 
         * Du willst ja jetzt nurnoch sorgen dass die nicht geschlossenen nicht mehr betroffen sind!
         */
        $entity         ->  detachNotClosedChildren();

        $entityManager  ->  flush();
    }

    public function onFlush(OnFlushEventArgs $onFlushEventArgs): void
    {
        $entityManager  = $onFlushEventArgs ->  getEntityManager();
        $unitOfWork     = $entityManager    ->     getUnitOfWork();


        $toDelete = $unitOfWork->getScheduledEntityDeletions(); //TOO FAT - not enough memory for 128MB - need 4GB

        if( count ( $toDelete ) > 10 ) 
        {
            throw new Exception(    "Too many operations"   );
        }

        foreach ( $toDelete as $entity ) 
        {

            if ($entity instanceof Fehler && !$entity->isClosed()) 
            {
                $entityManager -> persist( $entity );
            }

        }
    }
}