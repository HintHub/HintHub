<?php

namespace App\Controller\Admin;

use App\Service\UserService;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Benachrichtigung;
use App\Service\BenachrichtigungService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BenachrichtigungCrudController extends AbstractCrudController
{
    private $bService;      // benachrichtigung
    private $userService;
    
    public function __construct ( UserService $userService, BenachrichtigungService $bService ) 
    {
        $this -> bService        = $bService;
        $this -> userService     = $userService;
    }
    
    public static function getEntityFqcn (): string
    {
        return Benachrichtigung::class;
    }

    public function configureCrud ($crud): Crud
    {
        $user = $this -> userService -> getCurrentUser ();

        return Crud::new()
            -> setPageTitle     ( 'index',  'Benachrichtigungen' )
            -> setPageTitle     ( 'new',    'Benachrichtigungen anlegen' )
            -> setPageTitle     ( 'detail', fn ( Benachrichtigung $b ) => sprintf ( 'Benachrichtigung <b>%s</b> betrachten',    $b -> __toString() ) )
            -> setPageTitle     ( 'edit',   fn ( Benachrichtigung $b ) => sprintf ( 'Benachrichtigung <b>%s</b> bearbeiten',    $b -> __toString() ) )
            -> overrideTemplate ( 'crud/index', 'bundles/EasyAdminBundle/crud/Benachrichtigung.html.twig' )
            ;
    }

    public function configureFields ( string $pageName ): iterable
    {
        //       IdField::new             (  'id'             ),
        return [
            AssociationField::new    (  'fehler'         ),
            TextField::new           (  'text'           ),
            DateTimeField::new       (  'datumErstellt'  ),
        ];
    }
    

    public function configureActions ( Actions $actions ): Actions
    {
        return $actions
            // ...
            -> add    ( Crud::PAGE_INDEX , Action::DETAIL   )
            -> remove ( Crud::PAGE_INDEX , Action::NEW      )
            -> remove ( Crud::PAGE_INDEX , Action::EDIT     )
            -> remove ( Crud::PAGE_DETAIL, Action::EDIT     )
        ;
        
    }

    public function createIndexQueryBuilder ( SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters ): QueryBuilder
    {
        parent::createIndexQueryBuilder ( $searchDto, $entityDto, $fields, $filters );

        $user   = $this -> userService -> getCurrentUser ();
        $userId = $user -> getId ();

        $response = $this -> get ( EntityRepository::class ) -> createQueryBuilder ( $searchDto, $entityDto, $fields, $filters );

        if( $user -> isTutor() || $user -> isStudent() ) 
        {
            $response
                -> where        ( 'entity.user = :userId AND entity.gelesen <> 1'   )
                -> setParameter ( 'userId', $userId )
                ;
        }
        else
        {
            // if not false
            //$response -> andWhere ( ' 0=1 ');
            throw new \Exception ( " Auf diesen Bereich haben Sie keinen Zugriff! " );
        }

        $response -> addOrderBy('entity.datumErstellt', 'ASC');
        return $response;
    }

    public function index (AdminContext $adminContext)
    {
        // If unreads is 0 try to forward to /
        if ( $this -> bService -> getCountUnreadBenachrichtigungen () == 0 )
        {
            return $this -> redirect ('/');
        }

        return parent::index($adminContext);
    }
}
