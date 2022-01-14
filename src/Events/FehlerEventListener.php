<?php

namespace App\Events;

use Exception;
use App\Entity\Fehler;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class FehlerEventListener 
{
    
    private $logger;
    
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
        $this->logshit("amgous");
    }
    
    private function logshit(string $s) {
        $this->logger->info($s);
    }
    
    // the listener methods receive an argument which gives you access to
    // both the entity object of the event and the entity manager itself
    public function preRemove(LifecycleEventArgs $args): void
    {
        
        $entity = $args->getObject();

        // if this listener only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if (!$entity instanceof Fehler) {
            return;
        }

        

        $entityManager = $args->getObjectManager();


        //dd($toDelete->getValues());

        if(!$entity->isClosed()) {
            return;
        }

        
        /* Idee: Loeschen tut er ohnehin. 
         * Du willst ja jetzt nurnoch sorgen dass die nicht geschlossenen nicht mehr betroffen sind!
         */
        $entity->detachNotClosedChildren();

        $entityManager->flush();

        //dd($entity->getVerwandteFehler() -> getValues());
    }

    public function onFlush(OnFlushEventArgs $onFlushEventArgs): void
    {
        $entityManager = $onFlushEventArgs->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();


        $toDelete = $unitOfWork->getScheduledEntityDeletions(); //TOO FAT - not enough memory for 128MB - need 4GB

        if(count($toDelete) > 10) {
            throw new Exception("Too many operations");
        }

        //dd($toDelete);

        foreach ($toDelete as $entity) {

            if ($entity instanceof Fehler && !$entity->isClosed()) {
                //throw new Exception("amogus");
                $entityManager->persist($entity);
            }

        }
    }
}