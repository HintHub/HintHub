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
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ArrayFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
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
    private UserService   $userService;
    private FehlerService $fehlerService;

    public function __construct ( 
        UserService   $userService,
        FehlerService $fehlerService,
    ) 
    {
        $this -> userService    = $userService;
        $this -> fehlerService  = $fehlerService;
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
                -> remove ( Crud::PAGE_DETAIL,   Action::DELETE )
            ;
        }

        if ( $user -> isTutor () || $user -> isStudent () )
        {
            return $actions
                // ...
                -> add ( Crud::PAGE_INDEX,  Action::DETAIL               )
                //-> add ( Crud::PAGE_EDIT,   Action::SAVE_AND_ADD_ANOTHER )
                -> remove ( Crud::PAGE_INDEX,   Action::DELETE )
                -> remove ( Crud::PAGE_DETAIL,   Action::DELETE )
            ;
        }
        
        if ( $user -> isExtern () )
        {
            return $actions
                -> remove ( Crud::PAGE_DETAIL,   Action::DELETE )
                -> remove ( Crud::PAGE_EDIT,     Action::DELETE )    
            ;
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
                IdField::new            (   'id'               )    -> hideOnForm  (),
                IdField::new            (   'id'               )    -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          (   'name'             ),
                ChoiceField::new        (   'status'           )    -> setChoices ( $statusChoices ) -> hideOnIndex  (),
                TextField::new          (   'status'           )    -> formatValue ( function ($val,$entity) { return $this -> formatFehlerStatus ($val,$entity); } ) -> onlyOnIndex  (),
                NumberField::new        (   'seite'            ),
                AssociationField::new   (   'skript'           ),
                TextEditorField::new    (   'kommentar'        )    -> onlyWhenCreating  (),
                AssociationField::new   (   'verwandteFehler'  )    -> hideOnIndex()
                -> setFormTypeOptions 
                (
                    [
                    'by_reference' => false,
                    ]
                ),

                TextEditorField::new('verwandteFehler')
                // callables also receives the entire entity instance as the second argument
                ->formatValue(function ($value, $entity) {
                    return join("\n", $value->getValues());
                }) 

                -> hideOnForm(),
                AssociationField::new   (   'einreicher'       )    -> hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                DateTimeField::new      (   'datum_erstellt'   )    -> hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' )
            ];
        }

        if ( $user -> isStudent () )
        {
            return [
                IdField::new            (   'id'                    )    -> hideOnForm  (),
                IdField::new            (   'id'                    )    -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          (   'name'                  ),
                ChoiceField::new        (   'status'                )    -> hideWhenCreating () -> hideOnIndex () -> setChoices  ( $statusChoices ),
                TextxField::new         (   'status'                )    -> formatValue ( function ($val,$entity) { return $this -> formatFehlerStatus ($val,$entity); } ) -> onlyOnIndex  (),
                NumberField::new        (   'seite'                 ),
                AssociationField::new   (   'skript'                ),
                TextField::new          (   'descriptionKommentar'  )    
                    -> hideWhenCreating  () 
                    -> setFormTypeOption ( 'disabled', 'disabled' ) 
                    -> formatValue(
                        function ( $val, $obj )
                        {
                            return $obj -> getDescriptionKommentar ();
                        }
                    )
                ,
                TextEditorField::new    (   'kommentar'        )    -> onlyWhenCreating  (),
                AssociationField::new   (   'einreicher'       )    -> hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                DateTimeField::new      (   'datum_erstellt'   )    -> hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' )
            ];
        }

        if ( $user -> isTutor () )
        {
            return [
                IdField::new            (   'id'               )    -> hideOnForm  (),
                IdField::new            (   'id'               )    -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          (   'name'             ),
                ChoiceField::new        (   'status'           )    -> setChoices  ( $statusChoices ) -> hideOnIndex (),
                TextxField::new         (   'status'           )    -> formatValue ( function ($val,$entity) { return $this -> formatFehlerStatus ($val,$entity); } ) -> onlyOnIndex  (),
                NumberField::new        (   'seite'            ),
                AssociationField::new   (   'skript'           ),
                TextEditorField::new    (   'kommentar'        )    -> onlyWhenCreating  (),
                AssociationField::new   (   'verwandteFehler'  )
                -> setFormTypeOptions 
                (
                    [
                    'by_reference' => false,
                    ]
                ) 
                -> hideOnIndex(),
                TextEditorField::new    ( 'verwandteFehler'    )
                // callables also receives the entire entity instance as the second argument
                ->formatValue ( 
                    function  ( $value, $entity ) 
                    {
                        return join( "\n", $value -> getValues () );
                    }
                ) -> hideOnForm (),
                AssociationField::new   (   'einreicher'       )    -> hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                DateTimeField::new      (   'datum_erstellt'   )    -> hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' )
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

    public function persistEntity ( EntityManagerInterface $em, $entity) : void     
    {         
        $currentUser = $this -> userService   -> getCurrentUser ();          
        $entity      = $this -> fehlerService -> openWithKommentar ( $entity, $currentUser );
        
        parent::persistEntity   ( $em, $entity );     
    }
    

    public function createIndexQueryBuilder ( SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters ): QueryBuilder
    {
        parent::createIndexQueryBuilder ( $searchDto, $entityDto, $fields, $filters );

        $user   = $this -> userService -> getCurrentUser ();
        $userId = $user -> getId();

        $response = $this -> get ( EntityRepository::class ) -> createQueryBuilder ( $searchDto, $entityDto, $fields, $filters );

        if ( $user -> isAdmin () )
        {
            $response
                -> addOrderBy (
                    'CASE entity.status 
                    when \'OPEN\' THEN 4 
                    when \'WAITING\' THEN 3 
                    when \'ESCALATED\' THEN 2 
                    when \'REJECTED\' THEN 1 
                    ELSE 0 END', 'DESC')
            ;
        }


        if ( $user -> isStudent () )
        {
            $response   -> andWhere     ( 'entity.einreicher = :userId' )
                        -> setParameter ( 'userId', $userId             )
                        -> addOrderBy (
                            'CASE entity.status 
                            when \'OPEN\' THEN 4 
                            when \'WAITING\' THEN 3 
                            when \'ESCALATED\' THEN 2 
                            when \'REJECTED\' THEN 1 
                            ELSE 0 END', 'DESC')
            ;
        }

        if ( $user -> isTutor () )
        {
            $userModuleIds = $user -> getOnlyIdsFromTutorIn ();

            //$userModuleIdsString = implode(",", $userModuleIds);
            
            if  ( count($userModuleIds) == 0  ) 
            {
                //dd("tutor hat keine module");
                throw new \Exception("Sie haben keine Module zugewiesen");
            }

            $response
                -> join('entity.skript', 's')
                //-> add ( 'andWhere', $response->expr() -> in ( 's.modul', $userModuleIds ) ) //bug here
                -> andWhere('s.modul IN (:module) AND entity.status <> \'CLOSED\'')
                
                -> addOrderBy('CASE entity.status 
                when \'OPEN\' THEN 4 
                when \'WAITING\' THEN 3 
                when \'ESCALATED\' THEN 2 
                when \'REJECTED\' THEN 1 
                ELSE 0 END', 'DESC')
                
                -> setParameter(':module', $userModuleIds, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                ;
        }

        //-> addOrderBy('entity.status', 'ASC');
        return $response;
    }


    private function getArrayFilterStatusChoices () 
    {
        return ArrayFilter::new ( 'status' ) -> setChoices ( $this -> getStatusChoices () );
    }

    public function configureFilters ( Filters $filters ): Filters
    {
        $statusChoices = $this -> getArrayFilterStatusChoices ();

        return $filters
            -> add ( 'name'         )
            -> add ( 'seite'        )
            -> add ( 'skript'       )
            -> add ( $statusChoices )
        ;
    }

    public function formatFehlerStatus ($val, $entity)
    {
        $badgeInfo = $entity -> badgeByStatus ();
        if($badgeInfo === null)
            dd($entity);
        $badgeType = $badgeInfo [0];
        $badgeText = $badgeInfo [1];

        return '<span class="badge '.$badgeType.'">'.$badgeText.'</span>';
    }
}