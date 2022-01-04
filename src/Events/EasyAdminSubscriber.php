<?php
namespace App\EventSubscriber;

use App\Entity\Fehler;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $slugger;

    public function __construct($slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityUpdatedEvent::class => ['onBeforeUpdate'],
        ];
    }

    public function onBeforeUpdate ( BeforeEntityUpdatedEvent $event )
    {

    }
}