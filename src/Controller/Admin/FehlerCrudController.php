<?php

namespace App\Controller\Admin;

use App\Entity\Fehler;
use App\Entity\Kommentar;
use App\Service\UserService;
use App\Service\FehlerService;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * CrudController for Fehler generated via php bin/console make:admin:crud
 * compare https://symfony.com/bundles/EasyAdminBundle/current/crud.html
 * 
 * @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de )
 * @author karim.saad@iubh.de ( karim.saad@iubh.de ) - since UserReporting & easyadmincrud configs
 */
class FehlerCrudController extends AbstractCrudController
{
    private UserService $userService;
    private FehlerService $fehlerService;

    public function __construct ( 
        UserService   $userService,
        FehlerService $fehlerService,
    ) 
    {
        $this -> userService   = $userService;
        $this -> fehlerService = $fehlerService;
    }
    
    public static function getEntityFqcn (): string
    {
        return Fehler::class;
    }


    public function configureCrud ( $crud ): Crud
    {
        $user = $this -> userService -> getCurrentUser ();

        if ( $user -> isAdmin () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Fehlermeldungen'   )
                -> setPageTitle ( 'new',    'Fehler melden'     )
                -> setPageTitle ( 'detail', fn ( Fehler $fehler ) => sprintf ( 'Fehlermeldung <b>%s</b> betrachten',    $fehler -> __toString () ) )
                -> setPageTitle ( 'edit',   fn ( Fehler $fehler ) => sprintf ( 'Fehler <b>%s</b> bearbeiten',           $fehler -> __toString () ) )
    
                -> overrideTemplate ( 'crud/detail', 'bundles/EasyAdminBundle/crud/FehlerCrudDetail.html.twig' )

                // ->overrideTemplates([
                //     'crud/index' => 'admin/pages/index.html.twig',
                //     'crud/field/textarea' => 'admin/fields/dynamic_textarea.html.twig',
                // ])
            ;
        }

        if ( $user -> isStudent () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Fehlermeldungen'   )
                -> setPageTitle ( 'new',    'Fehler melden'     )
                -> setPageTitle ( 'detail', fn ( Fehler $fehler ) => sprintf ( 'Fehlermeldung <b>%s</b> betrachten',    $fehler -> __toString () ) )
                -> setPageTitle ( 'edit',   fn ( Fehler $fehler ) => sprintf ( 'Fehler <b>%s</b> bearbeiten',           $fehler -> __toString () ) )
    
                -> overrideTemplate ( 'crud/detail', 'bundles/EasyAdminBundle/crud/FehlerCrudDetail.html.twig' )
            ;
        }

        if ( $user -> isTutor () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Fehlermeldungen'   )
                -> setPageTitle ( 'new',    'Fehler melden'     )
                -> setPageTitle ( 'detail', fn ( Fehler $fehler ) => sprintf ( 'Fehlermeldung <b>%s</b> betrachten',    $fehler -> __toString () ) )
                -> setPageTitle ( 'edit',   fn ( Fehler $fehler ) => sprintf ( 'Fehler <b>%s</b> bearbeiten',           $fehler -> __toString () ) )
    
                -> overrideTemplate ( 'crud/detail', 'bundles/EasyAdminBundle/crud/FehlerCrudDetail.html.twig' )
            ;
        }

        if ( $user -> isExtern () )
        {
            //TODO FehlerCrudController configureCrud isExtern
        }
        
    }

    public function configureActions ( Actions $actions ): Actions
    {
        $user = $this -> userService -> getCurrentUser ();

        if ( $user -> isAdmin () )
        {
            return $actions
                // ...
                -> add ( Crud::PAGE_INDEX,  Action::DETAIL               )
                -> add ( Crud::PAGE_EDIT,   Action::SAVE_AND_ADD_ANOTHER )
            ;
        }

        if ( $user -> isStudent () )
        {
            return $actions
            // ...
            -> add ( Crud::PAGE_INDEX,  Action::DETAIL               )
            -> add ( Crud::PAGE_EDIT,   Action::SAVE_AND_ADD_ANOTHER )
        ;
        }

        if ( $user -> isTutor () )
        {
            //TODO FehlerCrudController configureActions isTUtor
        }
        
        if ( $user -> isExtern () )
        {
            //TODO FehlerCrudController configureActions isExtern
        }

        return $actions;
    }

    //TODO field fuer status
    public function configureFields ( string $pageName ): iterable
    {
        /*
            Fields:
                id
                status
                seite
                kommentare
                verwandteFehler
                skript - fixed by KS, 19 Dez 2021 01:13
        */
        $user                = $this -> userService -> getCurrentUser ();
        $statusChoices       = $this -> getStatusChoices ();
        $statusChoicesKeys   = array_keys   ($statusChoices);
        $statusChoicesValues = array_values ($statusChoices);

        // dd([ $statusChoicesKeys[0] => $statusChoicesValues[0] ]);
        if ( $user -> isAdmin () )
        {
            return [
                 
            ];
        }

        if ( $user -> isStudent () )
        {
            return [
                IdField::new            (   'id'               )    -> hideOnForm  (),
                IdField::new            (   'id'               )    -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          (   'name'             ),
                ChoiceField::new        (   'status'           )    -> hideWhenCreating () -> setChoices ( $statusChoices ),
                NumberField::new        (   'seite'            ),
                AssociationField::new   (   'skript'           ),
                TextEditorField::new    (   'kommentar'        )    -> onlyWhenCreating  (),
                AssociationField::new   (   'kommentare'       )    -> hideWhenCreating  (),
                AssociationField::new   (   'verwandteFehler'  )    -> hideWhenCreating  () -> setFormTypeOption ( 'disabled', 'disabled' ),
                AssociationField::new   (   'einreicher'       )    -> hideWhenCreating  () -> setFormTypeOption ( 'disabled', 'disabled' ),
                DateField::new          (   'datum_erstellt'   )    -> hideWhenCreating  () -> setFormTypeOption ( 'disabled', 'disabled' ),
            ];
        }

        if ( $user -> isTutor () )
        {
            return [
                IdField::new            (   'id'               )    -> hideOnForm  (),
                IdField::new            (   'id'               )    -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          (   'name'             ),
                ChoiceField::new        (   'status'           )    -> setChoices ( $statusChoices ),
                NumberField::new        (   'seite'            ),
                AssociationField::new   (   'skript'           ),
                TextEditorField::new    (   'kommentar'        )    -> onlyWhenCreating  (),
                AssociationField::new   (   'kommentare'       )    -> hideWhenCreating() ,
                AssociationField::new   (   'verwandteFehler'  ),
                AssociationField::new   (   'einreicher'       )    -> hideWhenCreating  (),
                DateField::new          (   'datum_erstellt'   )    -> hideWhenCreating(),
            ];
        }

        if ( $user -> isExtern () )
        {
            //TODO FehlerCrudController configureFields
        }
    }

    /**
     * @author ali-kemal.yalama (ali-kemal.yalama@iubh.de)
     */
    public function getStatusChoices () 
    {
        $user = $this -> userService -> getCurrentUser ();
        return $this -> fehlerService -> getStatusChoices ( $user );
    }

    /*
        When a Fehler gets added to DB (persisted)
        @author Karim Saad ( karim.saad@iubh.de )
        @date 20.12.2021 03:05
    */
    public function createEntity ( string $entityFqcn ) 
    {
        $currentUser     = $this -> userService -> getCurrentUser ();
        $entity          = new Fehler    ();
        $currentDateTime = new \DateTime ();

        // Datum Trait
        $entity -> setDatumLetzteAenderung   ( $currentDateTime );
        $entity -> setDatumErstellt          ( $currentDateTime );

        // Einreicher Trait
        $entity -> setEinreicher             ( $currentUser     );

        return $entity;
    }

    /*
        After the insertation to DB / persistence
        
        @author karim.saad ( karim.saad@iubh.de )
        @date 22.12.2021 13:45
    */
    public function persistEntity ( EntityManagerInterface $em, $entity) : void
    {
        $currentUser         = $this -> userService -> getCurrentUser ();

        $entity = $this -> fehlerService -> openWithKommentar ( $entity, $currentUser );

        // $this -> updateSlug     ( $entity );
        parent::persistEntity   ( $em, $entity );
    }
    

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $user = $this->userService->getCurrentUser();
        $userId = $user->getId();


        $response = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if($user->isStudent()) {
            $response   ->where('entity.einreicher = :userId')
                        ->setParameter('userId', $userId);
        }

        if($user->isTutor()) {
            //TODO transitivitaet
        }

        return $response;
    }
}