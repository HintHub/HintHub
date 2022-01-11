<?php

namespace App\EventSubscriber;

use Monolog\Logger;
use App\Entity\Fehler;
use App\Service\UserService;
use Psr\Log\LoggerInterface;
use App\Service\FehlerService;
use App\Controller\Admin\FehlerCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;


class EasyAdminSubscriber implements EventSubscriberInterface
{

    private FehlerService   $fehlerService;
    private UserService     $userService;
    private Logger $logger;
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(FehlerService $fehlerService, UserService $userService, LoggerInterface $logger, AdminUrlGenerator $adminUrlGenerator)
    {
        $this -> adminUrlGenerator = $adminUrlGenerator;
        $this -> userService   = $userService;
        $this -> fehlerService = $fehlerService;
        $this -> logger = $logger;
    }

    public static function getSubscribedEvents ()
    {
        return [
            //BeforeEntityUpdatedEvent    ::class     => ['onUpdateEvent'],
            //BeforeEntityPersistedEvent  ::class     => ['onBeforePersisted'],
            AfterEntityPersistedEvent   ::class     => ['afterPersisted'],
            //BeforeEntityDeletedEvent    ::class     => ['beforeDeletion'],
            //AfterEntityDeletedEvent     ::class     => ['afterDeletion'],
            //AfterEntityUpdatedEvent        ::class => ['afterUpdated']
        ];
    }

    /*public function onBeforeUpdate ( BeforeEntityUpdatedEvent $event )
    {

    }*/

    /*public function afterUpdated(AfterEntityUpdatedEvent $event) {
        $this->logger->info($event);
    }*/

    /*public function onBeforePersisted(BeforeEntityPersistedEvent $event) 
    {

        $entity = $event->getEntityInstance();

        try 
        {
            if ($entity instanceof Fehler) {
                
                dd("hallo");

                $currentUser         = $this -> userService -> getCurrentUser ();

                $entity -> setDatumErstellt(new \DateTime());

                $currentFehler       = $this -> fehlerService -> openWithKommentar ( $entity, $currentUser );
            }
        } 
        catch (\Throwable $e)
        {
            //$this->logger->error($e);
            
        }
        catch (\Exception $e)
        {
            //$this->logger->error($e);
        }

        return;
    }*/

    public function afterPersisted(AfterEntityPersistedEvent $event) 
    {
        $entity = $event->getEntityInstance();
        //$this->logger->critical("hallo");
        try 
        {

            if ($entity instanceof Fehler) {
                


                $currentUser         = $this -> userService -> getCurrentUser ();

                //$entity -> setDatumErstellt(new \DateTime());

                //$currentFehler       = $this -> fehlerService -> openWithKommentar ( $entity, $currentUser );

                $url = $this->adminUrlGenerator
                ->setController(FehlerCrudController::class)
                ->setAction(Crud::PAGE_DETAIL)
                ->setEntityId($entity->getId())
                ->generateUrl();

                //dd(gettype($url));

                //dd($url);
                // https://github.com/EasyCorp/EasyAdminBundle/issues/4247
                //$response = new RedirectResponse($this->router->generate('app_logout', ["is_fahrschulkunde" => true]));
                $response = new RedirectResponse($url);
                return ($response)->send();
                
            
            }
        } 
        catch (\Throwable $e)
        {
            $this->logger->error($e);
        }
        catch (\Exception $e)
        {
            $this->logger->error($e);
        }

        return;
    }

}