<?php

namespace App\Controller\Admin;

use App\Entity\Fehler;
use App\Entity\Kommentar;
use App\Service\UserService;
use App\Service\FehlerService;
use Doctrine\ORM\QueryBuilder;
use App\Filter\UnbearbeitetTageFilter;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
    private $userService;
    private $fehlerService;

    public function __construct ( 
        UserService      $userService,
        FehlerService    $fehlerService,
    ) 
    {
        $this -> userService   = $userService;
        $this -> fehlerService = $fehlerService;
        $this -> fehlerService -> escalateFehler ();
    }
    
    public static function getEntityFqcn (): string
    {
        return Fehler::class;
    }


    public function configureCrud ( $crud ): Crud
    {
        $crud = Crud::new();

        $crud 
            -> setDateFormat ( 'd.m.Y' )
            -> setTimeFormat ( 'H:i:s' );

        $user = $this -> userService -> getCurrentUser ();

        if ( $user -> isAdmin () )
        {
            return $crud
                -> setPageTitle ( 'index',  'Fehlermeldungen'   )
                -> setPageTitle ( 'new',    'Fehler melden'     )
                -> setPageTitle ( 'detail', fn ( Fehler $fehler ) => sprintf ( 'Fehlermeldung <b>%s</b> betrachten',    $fehler -> __toString () ) )
                -> setPageTitle ( 'edit',   fn ( Fehler $fehler ) => sprintf ( 'Fehler <b>%s</b> bearbeiten',           $fehler -> __toString () ) )
    
                -> overrideTemplate ( 'crud/detail', 'bundles/EasyAdminBundle/crud/FehlerCrudDetail.html.twig' )
            ;
        }

        if ( $user -> isStudent () )
        {
            return $crud
                -> setPageTitle ( 'index',  'Fehlermeldungen'   )
                -> setPageTitle ( 'new',    'Fehler melden'     )
                -> setPageTitle ( 'detail', fn ( Fehler $fehler ) => sprintf ( 'Fehlermeldung <b>%s</b> betrachten',    $fehler -> __toString () ) )
                -> setPageTitle ( 'edit',   fn ( Fehler $fehler ) => sprintf ( 'Fehler <b>%s</b> bearbeiten',           $fehler -> __toString () ) )
    
                -> overrideTemplate ( 'crud/detail', 'bundles/EasyAdminBundle/crud/FehlerCrudDetail.html.twig' )
            ;
        }

        if ( $user -> isTutor () )
        {
            return $crud
                -> setPageTitle ( 'index',  'Fehlermeldungen'   )
                -> setPageTitle ( 'new',    'Fehler melden'     )
                -> setPageTitle ( 'detail', fn ( Fehler $fehler ) => sprintf ( 'Fehlermeldung <b>%s</b> betrachten',    $fehler -> __toString () ) )
                -> setPageTitle ( 'edit',   fn ( Fehler $fehler ) => sprintf ( 'Fehler <b>%s</b> bearbeiten',           $fehler -> __toString () ) )
    
                -> overrideTemplate ( 'crud/detail', 'bundles/EasyAdminBundle/crud/FehlerCrudDetail.html.twig' )
            ;
        }
    }

    public function configureActions ( Actions $actions ): Actions
    {
        $user = $this -> userService -> getCurrentUser ();

        if ( $user -> isAdmin () )
        {
            return $actions
                // ...
                -> add    ( Crud::PAGE_INDEX,    Action::DETAIL               )
                -> add    ( Crud::PAGE_EDIT,     Action::SAVE_AND_ADD_ANOTHER )
                -> remove ( Crud::PAGE_DETAIL,   Action::DELETE               )
            ;
        }

        if ( $user -> isTutor () || $user -> isStudent () )
        {
            return $actions
                -> add    ( Crud::PAGE_INDEX,    Action::DETAIL )
                -> remove ( Crud::PAGE_INDEX,    Action::DELETE )
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
        $statusChoicesKeys   = array_keys   ( $statusChoices );
        $statusChoicesValues = array_values ( $statusChoices );

        if ( $user -> isAdmin () )
        {
            return [
                IdField::new            ( 'id'     ) -> hideOnForm  (),
                IdField::new            ( 'id'     ) -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'name'   ),
                ChoiceField::new        ( 'status' ) -> setChoices  ( $statusChoices ) -> hideOnIndex(),
                TextField::new          ( 'status' ) 
                    -> formatValue ( 
                        function ( $val,$entity ) 
                        { 
                            return $this -> formatFehlerStatus ( $val,$entity );
                        }
                    ) 
                    -> onlyOnIndex  (),

                NumberField::new        ( 'seite'           ),
                AssociationField::new   ( 'skript'          ),
                TextEditorField::new    ( 'kommentar'       ) -> onlyWhenCreating  (),

                AssociationField::new   ( 'verwandteFehler' ) -> hideOnIndex       ()
                    -> setFormTypeOptions 
                    (
                        [
                        'by_reference' => false,
                        ]
                    )
                    -> hideOnIndex(),

                TextEditorField::new    ( 'verwandteFehler' )
                    -> formatValue (
                        function ( $value, $entity )
                        {
                            return join ( "\n", $value -> getValues () );
                        }
                    ) 
                    -> setSortable ( false )
                    -> hideOnForm(),
                
                AssociationField::new   ( 'einreicher'      ) -> hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),

                DateTimeField::new      ( 'datumLetzteAenderung', 'Unbearbeitet (Tage)') -> hideWhenCreating () 
                    -> formatValue (
                        function ( $val, $entity )
                        {
                            return $this -> getUnbearbeitetTage ( $entity );
                        }
                    )
                    -> hideOnForm (),
                            
                DateTimeField::new      ( 'datumLetzteAenderung', 'Erstellungsdatum' ) 
                    -> onlyWhenUpdating  ()
                    -> setFormTypeOption ( 'disabled', 'disabled' )
                ,
                
                DateTimeField::new      ( 'datumErstellt',        'Erstellungsdatum'  ) 
                    -> hideWhenCreating () 
                    -> setSortable ( true )
                    -> setFormTypeOption ( 'disabled', 'disabled' )
            ];
        }

        if ( $user -> isStudent () )
        {
            return [
                IdField::new            ( 'id'                   ) -> hideOnForm  (),
                IdField::new            ( 'id'                   ) -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'name'                 ),
                ChoiceField::new        ( 'status'               ) -> hideWhenCreating () -> hideOnIndex () -> setChoices  ( $statusChoices ),

                TextField::new          ( 'status'               )    
                    -> formatValue ( 
                        function ( $val,$entity )
                        { 
                            return $this -> formatFehlerStatus ( $val,$entity );
                        }
                    ) 
                    -> onlyOnIndex  (),

                NumberField::new        ( 'seite'                ),
                AssociationField::new   ( 'skript'               ),

                TextField::new          ( 'descriptionKommentar' )    
                    -> hideWhenCreating  () 
                    -> setFormTypeOption ( 'disabled', 'disabled' ) 
                    -> formatValue(
                        function ( $val, $obj )
                        {
                            return $obj -> getDescriptionKommentar ();
                        }
                    ),
                
                TextEditorField::new    ( 'kommentar'      )  -> onlyWhenCreating (),
                AssociationField::new   ( 'einreicher'     )  -> hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),


                DateTimeField::new      ( 'datumLetzteAenderung', 'Unbearbeitet (Tage)') -> hideWhenCreating () 
                    -> formatValue (
                        function ( $val, $entity )
                        {
                            return $this -> getUnbearbeitetTage ( $entity );
                        }
                    )
                    -> hideOnForm (),
                            
                DateTimeField::new      ( 'datumLetzteAenderung', 'Erstellungsdatum' ) 
                    -> onlyWhenUpdating  ()
                    -> setFormTypeOption ( 'disabled', 'disabled' )
                ,
                
                DateTimeField::new      ( 'datumErstellt',        'Erstellungsdatum'  ) 
                    -> hideWhenCreating () 
                    -> setSortable ( true )
                    -> setFormTypeOption ( 'disabled', 'disabled' )
            ];
        }

        if ( $user -> isTutor () )
        {
            return [
                IdField::new            ( 'id'              ) -> hideOnForm  (),
                IdField::new            ( 'id'              ) -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'name'            ),
                ChoiceField::new        ( 'status'          ) -> setChoices  ( $statusChoices ) -> hideOnIndex (),

                TextField::new          ( 'status'          )
                    -> formatValue (
                        function ( $val,$entity )
                        {
                            return $this -> formatFehlerStatus ( $val,$entity ); 
                        } 
                    ) -> onlyOnIndex  (),
                
                NumberField::new        ( 'seite'           ),
                AssociationField::new   ( 'skript'          ),
                TextEditorField::new    ( 'kommentar'       ) -> onlyWhenCreating  (),

                AssociationField::new   ( 'verwandteFehler' )
                    -> setFormTypeOptions (
                        [
                        'by_reference' => false,
                        ]
                    ) 
                    -> hideOnIndex(),

                TextEditorField::new    ( 'verwandteFehler' )
                    ->formatValue ( 
                        function  ( $value, $entity ) 
                        {
                            return join( "\n", $value -> getValues () );
                        }
                    )
                    -> setSortable ( false )
                    -> hideOnForm(),
                
                AssociationField::new   ( 'einreicher'     ) -> hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                
                DateTimeField::new      ( 'datumLetzteAenderung', 'Unbearbeitet (Tage)') -> hideWhenCreating () 
                    -> formatValue (
                        function ( $val, $entity )
                        {
                            return $this -> getUnbearbeitetTage ( $entity );
                        }
                    )
                    -> hideOnForm (),
                            
                DateTimeField::new      ( 'datumLetzteAenderung', 'Erstellungsdatum' ) 
                    -> onlyWhenUpdating  ()
                    -> setFormTypeOption ( 'disabled', 'disabled' )
                ,
                
                DateTimeField::new      ( 'datumErstellt',        'Erstellungsdatum'  ) 
                    -> hideWhenCreating () 
                    -> setSortable ( true )
                    -> setFormTypeOption ( 'disabled', 'disabled' )
            ];
        }
    }

    /**
     * @author ali-kemal.yalama (ali-kemal.yalama@iubh.de)
     */
    public function getStatusChoices () 
    {
        $user = $this -> userService   -> getCurrentUser ();
        return  $this -> fehlerService -> getStatusChoices ( $user );
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

        $query = $this -> get ( EntityRepository::class ) -> createQueryBuilder ( $searchDto, $entityDto, $fields, $filters );

        if ( $user -> isAdmin () )
        {
            $query = $this -> addOrderByToResponse ( $query );
        }


        if ( $user -> isStudent () )
        {
            $query 
                -> andWhere     ( 'entity.einreicher = :userId' )
                -> setParameter ( 'userId', $userId             )
            ;

            $query = $this -> addOrderByToResponse ( $query );
        }

        if ( $user -> isTutor () )
        {
            $userModuleIds = $user -> getOnlyIdsFromTutorIn ();

            if  ( count($userModuleIds) == 0  ) 
            {
                // Tutor hat keine Module
                throw new \Exception("keine Module zugewiesen");
            }

            $query
                -> join         ( 'entity.skript', 's' )
                -> andWhere     ( "s.modul IN (:module) AND entity.status <> 'CLOSED' ");
            
            $query = $this -> addOrderByToResponse ( $query );

            $query -> setParameter( ':module', $userModuleIds, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY );
        }

        //-> addOrderBy('entity.status', 'ASC');
        return $query;
    }

    private function addOrderByToResponse ( $query )
    {
        return $query 
            -> addOrderBy (
                'CASE entity.status 
                when \'OPEN\' THEN 4 
                when \'WAITING\' THEN 3 
                when \'ESCALATED\' THEN 2 
                when \'REJECTED\' THEN 1 
                ELSE 0 END', 'DESC'
            )
        ;
    }

    private function getArrayFilterStatusChoices () 
    {
        return ArrayFilter::new ( 'status' ) 
            -> setChoices (
                $this -> getStatusChoices ()
            )
        ;
    }

    public function configureFilters ( Filters $filters ): Filters
    {
        $statusChoices = $this -> getArrayFilterStatusChoices ();

        return $filters
            -> add ( 'name'         )
            -> add ( 'seite'        )
            -> add ( 'skript'       )
            -> add ( $statusChoices )
            -> add ( 
                UnbearbeitetTageFilter::new('unbearbeitetTage')
                    -> setFormTypeOption ( 'mapped', false )
            
            )
        ;
    }

    public function formatFehlerStatus ( $val, $entity )
    {
        $badgeInfo = $entity -> badgeByStatus ();
        
        if($badgeInfo === null)
            throw new \Exception ("badgeStatus is Null (FelerCrudController)");
        
        $badgeType = $badgeInfo [0]; // Type
        $badgeText = $badgeInfo [1]; // Text

        return '<span class="badge '.$badgeType.'">'.$badgeText.'</span>';
    }

    private function getUnbearbeitetTage ( $entity )
    {
        $d = $this -> fehlerService -> loadUnbearbeitetTage ( $entity );
        $entity        -> setUnbearbeitetTage ( $d -> getUnbearbeitetTage () );
        return $entity -> getUnbearbeitetTage ();
    }
}