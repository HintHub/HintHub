<?php

namespace App\Controller\Admin;

use App\Entity\Modul;
use App\Service\UserService;
use Doctrine\ORM\QueryBuilder;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * CrudController for Modul generated via php bin/console make:admin:crud
 * compare https://symfony.com/bundles/EasyAdminBundle/current/crud.html
 * 
 * @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de )
 */
class ModulCrudController extends AbstractCrudController
{
    private                 $choices = [];
    
    private UserService     $userService;
    private UserRepository  $userRepository;

    public function __construct ( UserService $userService, UserRepository $userRepository )
    {
        $this -> userService    = $userService;
        $this -> userRepository = $userRepository;
    }
    
    public static function getEntityFqcn(): string
    {
        return Modul::class;
    }

    public function configureCrud ($crud): Crud
    {
        $user = $this -> userService -> getCurrentUser ();

        if ( $user -> isAdmin () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Module'        )
                -> setPageTitle ( 'new',    'Modul anlegen' )
                -> setPageTitle ( 'detail', fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> betrachten',    $modul -> __toString() ) )
                -> setPageTitle ( 'edit',   fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> bearbeiten',    $modul -> __toString() ) )
            ;
        }

        if ( $user -> isStudent () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Module'        )
                -> setPageTitle ( 'new',    'Modul anlegen' )
                -> setPageTitle ( 'detail', fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> betrachten',    $modul -> __toString() ) )
                -> setPageTitle ( 'edit',   fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> bearbeiten',    $modul -> __toString() ) )
            ;
        }

        if ( $user -> isTutor () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Module'        )
                -> setPageTitle ( 'new',    'Modul anlegen' )
                -> setPageTitle ( 'detail', fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> betrachten',    $modul -> __toString() ) )
                -> setPageTitle ( 'edit',   fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> bearbeiten',    $modul -> __toString() ) )
            ;
        }

        if ( $user -> isExtern () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Module'         )
                -> setPageTitle ( 'new',    'Modul anlegen'  )
                -> setPageTitle ( 'detail', fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> betrachten',    $modul -> __toString() ) )
                -> setPageTitle ( 'edit',   fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> bearbeiten',    $modul -> __toString() ) )
            ;
        }

        if ( $user -> isVerwaltung () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Module'        )
                -> setPageTitle ( 'new',    'Modul anlegen' )
                -> setPageTitle ( 'detail', fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> betrachten',    $modul -> __toString() ) )
                -> setPageTitle ( 'edit',   fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> bearbeiten',    $modul -> __toString() ) )
            ;
        }
    }

    public function configureFields (string $pageName): iterable
    {
        $user = $this -> userService -> getCurrentUser ();

        /*
            Fields:
                id,
                kuerzel,
                name
                skripte,
                aktuellesSkript,
                tutor,
                fehler
        */
        if ( $user -> isAdmin () )
        {
            return [
                IdField::new            ( 'id'              ) -> hideOnForm  (),
                IdField::new            ( 'id'              ) -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'name'            ),
                TextField::new          ( 'kuerzel'         ),
                AssociationField::new   ( 'skript'          ),
                AssociationField::new   ( 'tutor'           ) 
                    -> setQueryBuilder ( 
                        function ( $queryBuilder ) 
                        {
                            $queryBuilder
                            -> andWhere      ( 'entity.ROLES LIKE :role'    )
                            -> setParameter  ( 'role', '%ROLE_TUTOR%'       )
                            ;
                        }
                    ) 
                    -> onlyOnForms(),

                    AssociationField::new   ( 'studenten' )  
                        -> setQueryBuilder ( function ( $queryBuilder )               
                            {
                                $queryBuilder
                                -> andWhere      ( 'entity.ROLES LIKE :role'    )
                                -> setParameter  ( 'role', '%ROLE_STUDENT%'     )
                                ;
                            })
                        -> setFormTypeOptions 
                            (
                                [
                                'by_reference' => false,
                                ]
                            ) 
                        -> onlyOnForms(),


                    TextEditorField::new ( 'tutor' )
                        -> formatValue (
                            function ( $value, $entity )
                            {
                                return $value;
                            }
                        ) -> hideOnForm (),

                    TextEditorField::new ( 'studenten' )
                        -> formatValue (
                            function ( $value, $entity ) 
                            {
                                return join ( "\n", $value -> getValues () );
                            }
                        ) -> hideOnForm (),


            ];
        }

        if ( $user -> isStudent () )
        {
            return [
                IdField::new            ( 'id'              ) -> hideOnForm  (),
                IdField::new            ( 'id'              ) -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'name'            ),
                TextField::new          ( 'kuerzel'         ),
                AssociationField::new   ( 'skript'          ),

                AssociationField::new   ( 'tutor'           ) 
                    -> setQueryBuilder ( 
                        function ( $queryBuilder ) 
                        {
                            $queryBuilder
                            -> andWhere      ( 'entity.ROLES LIKE :role'    )
                            -> setParameter  ( 'role', '%ROLE_TUTOR%'       )
                            ;
                        }
                    ) 
                    -> onlyOnForms (),

                AssociationField::new   ( 'studenten'       )  
                    -> setQueryBuilder ( 
                        function ( $queryBuilder )               
                        {
                            $queryBuilder
                            -> andWhere      ( 'entity.ROLES LIKE :role'    )
                            -> setParameter  ( 'role', '%ROLE_STUDENT%'     )
                            ;
                        }
                    )
                    -> setFormTypeOptions 
                        (
                            [
                                'by_reference' => false,
                            ]
                        ) 
                    -> onlyOnForms (),


                TextEditorField::new ( 'tutor' )
                    ->formatValue (
                        function ( $value, $entity )
                        {
                            return $value;
                        }
                    ) -> hideOnForm (),

                TextEditorField::new ( 'studenten' )
                    ->formatValue (
                        function ( $value, $entity )
                        {
                            return join ( "\n", $value -> getValues () );
                        }
                    ) -> hideOnForm (),

            ];
        }

        if ( $user -> isTutor () )
        {
            return [
                IdField::new            ( 'id'              ) -> hideOnForm  (),
                IdField::new            ( 'id'              ) -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'name'            ),
                TextField::new          ( 'kuerzel'         ),
                AssociationField::new   ( 'skript'          ),
                
                AssociationField::new   ( 'tutor'           ) 
                    -> setQueryBuilder ( 
                        function ( $queryBuilder ) 
                        {
                            $queryBuilder
                            -> andWhere      ( 'entity.ROLES LIKE :role'    )
                            -> setParameter  ( 'role', '%ROLE_TUTOR%'       )
                            ;
                        }
                    ) 
                    -> onlyOnForms (),

                AssociationField::new   ( 'studenten'       )  
                    -> setQueryBuilder ( 
                        function ( $queryBuilder )               
                        {
                            $queryBuilder
                            -> andWhere      ( 'entity.ROLES LIKE :role'    )
                            -> setParameter  ( 'role', '%ROLE_STUDENT%'     )
                            ;
                        }
                    )
                    -> setFormTypeOptions 
                        (
                            [
                            'by_reference' => false,
                            ]
                        ) 
                    -> onlyOnForms (),


                TextEditorField::new ( 'tutor' )
                    ->formatValue (
                        function ( $value, $entity )
                        {
                            return $value;
                        }
                    ) -> hideOnForm (),

                TextEditorField::new ( 'studenten' )
                    ->formatValue (
                        function ( $value, $entity )
                        {
                            return join ( "\n", $value -> getValues () );
                        }
                    ) -> hideOnForm (),

            ];
        }

        if ( $user -> isExtern () )
        {
            return [
                IdField::new            ( 'id'              ) -> hideOnForm  (),
                IdField::new            ( 'id'              ) -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'name'            ),
                TextField::new          ( 'kuerzel'         ),
                AssociationField::new   ( 'skript'          ),

                AssociationField::new   ( 'tutor'           ) 
                    -> setQueryBuilder ( 
                        function ( $queryBuilder ) 
                        {
                            $queryBuilder
                            -> andWhere      ( 'entity.ROLES LIKE :role'    )
                            -> setParameter  ( 'role', '%ROLE_TUTOR%'       )
                            ;
                        }
                    ) 
                    -> onlyOnForms (),
                
                AssociationField::new   ( 'studenten'       )  
                    -> setQueryBuilder ( 
                        function ( $queryBuilder )               
                        {
                            $queryBuilder
                            -> andWhere      ( 'entity.ROLES LIKE :role'    )
                            -> setParameter  ( 'role', '%ROLE_STUDENT%'     )
                            ;
                        }
                    )
                    -> setFormTypeOptions 
                        (
                            [
                            'by_reference' => false,
                            ]
                        ) 
                    -> onlyOnForms(),


                TextEditorField::new ( 'tutor' )
                    ->formatValue (
                        function ( $value, $entity )
                        {
                            return $value;
                        }
                    ) -> hideOnForm (),

                TextEditorField::new ( 'studenten' )
                    ->formatValue (
                        function ( $value, $entity )
                        {
                            return join ( "\n", $value -> getValues () );
                        }
                    ) -> hideOnForm (),

            ];
        }

        if ( $user -> isVerwaltung () )
        {
            return [
                IdField::new            ( 'id'              ) -> hideOnForm  (),
                IdField::new            ( 'id'              ) -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'name'            ),
                TextField::new          ( 'kuerzel'         ),
                AssociationField::new   ( 'skript'          ),
                
                AssociationField::new   ( 'tutor'           ) 
                    -> setQueryBuilder ( function ( $queryBuilder ) 
                        {
                            $queryBuilder
                            -> andWhere      ( 'entity.ROLES LIKE :role'    )
                            -> setParameter  ( 'role', '%ROLE_TUTOR%'       )
                            ;
                        }) 
                    -> onlyOnForms(),

                AssociationField::new   ( 'studenten'       )  
                    -> setQueryBuilder ( function ( $queryBuilder )               
                        {
                            $queryBuilder
                            -> andWhere      ( 'entity.ROLES LIKE :role'    )
                            -> setParameter  ( 'role', '%ROLE_STUDENT%'     )
                            ;
                        })
                    -> setFormTypeOptions 
                        (
                            [
                            'by_reference' => false,
                            ]
                        ) 
                    -> onlyOnForms(),


                TextEditorField::new ( 'tutor' )
                    ->formatValue (
                        function ( $value, $entity )
                        {
                            return $value;
                        }
                    ) -> hideOnForm (),

                TextEditorField::new ( 'studenten' )
                    ->formatValue (
                        function ( $value, $entity )
                        {
                            return join ( "\n", $value -> getValues () );
                        }
                    ) -> hideOnForm (),

            ];
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
                -> add    ( Crud::PAGE_INDEX,    Action::DETAIL )

                -> remove ( Crud::PAGE_DETAIL,   Action::EDIT   )
                -> remove ( Crud::PAGE_DETAIL,   Action::DELETE )
        ;
        }

        if ( $user -> isTutor () )
        {
            return $actions
                // ...
                -> add    ( Crud::PAGE_INDEX,    Action::DETAIL )
                -> remove ( Crud::PAGE_INDEX,    Action::NEW    )

                -> remove ( Crud::PAGE_INDEX,    Action::EDIT   )
                -> remove ( Crud::PAGE_INDEX,    Action::DELETE )

                -> remove ( Crud::PAGE_DETAIL,   Action::EDIT   )
                -> remove ( Crud::PAGE_DETAIL,   Action::DELETE )
            ;
        }
        
        if ( $user -> isExtern () )
        {
            return $actions
                // ...
                -> add    ( Crud::PAGE_INDEX,    Action::DETAIL               )
                -> add    ( Crud::PAGE_EDIT,     Action::SAVE_AND_ADD_ANOTHER )
                -> remove ( Crud::PAGE_INDEX,    Action::DELETE               )
                -> remove ( Crud::PAGE_DETAIL,   Action::DELETE               )
            ;
        }

        if ( $user -> isVerwaltung () )
        {
            return $actions
                // ...
                -> add ( Crud::PAGE_INDEX,  Action::DETAIL               )
                -> add ( Crud::PAGE_EDIT,   Action::SAVE_AND_ADD_ANOTHER )
            ;
        }

        return $actions;
    }

    public function createIndexQueryBuilder ( SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters ): QueryBuilder
    {
        parent::createIndexQueryBuilder ( $searchDto, $entityDto, $fields, $filters );

        $user = $this -> userService -> getCurrentUser ();
        $userId = $user -> getId ();

        $query = $this -> get ( EntityRepository::class ) -> createQueryBuilder ( $searchDto, $entityDto, $fields, $filters );

        if( $user->isTutor() ) 
        {
            $userModuleIds = $user -> getOnlyIdsFromTutorIn ();

            if  (   count($userModuleIds) == 0  ) 
            {
                throw new \Exception("Sie haben keine Module zugewiesen");
            }

            $query 
                -> where        ( 'entity.tutor = :userId' )
                -> setParameter ( 'userId', $userId );
        }

        return $query;
    }

    public function configureFilters ( Filters $filters ): Filters
    {
        return $filters
            -> add ( 'name'     )
            -> add ( 'kuerzel'  )
            -> add ( 'skript'   )
            -> add ( 'tutor'    )
        ;
    }
}
