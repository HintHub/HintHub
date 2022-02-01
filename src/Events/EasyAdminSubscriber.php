<?php

namespace App\Events;

use Monolog\Logger;
use App\Entity\Fehler;
use App\Model\DatumTrait;
use App\Service\UserService;
use Psr\Log\LoggerInterface;
use App\Service\FehlerService;
use App\Controller\Admin\FehlerCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;


/**
 * EasyAdminEventSubscriber
 * 
 * currently looksout for afterPersisted Event and runs afterPersisted () if found
 *
 * @author ali-kemal.yalama (ali-kemal.yalama@iuhb.de)
 * @author karim.saad       (karim.saad@iuhb.de)
 */
class EasyAdminSubscriber implements EventSubscriberInterface
{
    private FehlerService       $fehlerService;
    private UserService         $userService;
    private Logger              $logger;
    private AdminUrlGenerator   $adminUrlGenerator;

    public function __construct ( FehlerService $fehlerService, UserService $userService, LoggerInterface $logger, AdminUrlGenerator $adminUrlGenerator )
    {
        $this -> adminUrlGenerator  = $adminUrlGenerator;
        $this -> userService        = $userService;
        $this -> fehlerService      = $fehlerService;
        $this -> logger             = $logger;
    }

    public static function getSubscribedEvents ()
    {
        return [
            BeforeEntityUpdatedEvent  :: class => [ 'beforeUpdate'   ],
            AfterEntityPersistedEvent :: class => [ 'afterPersisted' ],
        ];
    }



    public function beforeUpdate ( BeforeEntityUpdatedEvent $event ) 
    {
        $entity = $event -> getEntityInstance ();

        if ( in_array ( DatumTrait::class, class_uses ( get_class ( $entity ) ), true ))
        {
            if ( ! $entity -> isnoUpdateDatumAenderung () )
            {
                // Not locked, so do update!
                $entity -> setDatumLetzteAenderung ( new \DateTime () );
            }
            else 
            {
                // Unlock
                $entity -> setNoUpdateDatumAenderung ( false );
            }
        }
    }

    public function afterPersisted ( AfterEntityPersistedEvent $event ) 
    {
        $entity = $event -> getEntityInstance ();

        try 
        {
            if ( $entity instanceof Fehler ) 
            {
                $currentUser         = $this -> userService -> getCurrentUser ();

                $url = $this -> adminUrlGenerator
                -> setController ( FehlerCrudController::class )
                -> setAction     ( Crud::PAGE_DETAIL           )
                -> setEntityId   ( $entity -> getId ()         )
                -> generateUrl   ();

                $response = new RedirectResponse ( $url );
                return ( $response ) -> send ();
            }
        }
        catch ( \Throwable $e )
        {
            $this -> logger -> error ( $e );
        }
        catch ( \Exception $e )
        {
            $this -> logger -> error ( $e );
        }

        return;
    }

}