<?php

namespace App\Controller\Admin;

use App\Entity\User;

use App\Service\UserService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\PasswordHasher;
use Symfony\Component\Form\FormEvents;


use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Symfony\Component\Form\FormBuilderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

/* add */
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

// For password Hashing
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ArrayFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Security\Core\Encoder\PasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;

/**
 * CrudController for user generated via php bin/console make:admin:crud
 * compare https://symfony.com/bundles/EasyAdminBundle/current/crud.html
 * 
 * @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de )
 */
class UserCrudController extends AbstractCrudController
{
    private $userService;
    private $passwordHasher;

    public function __construct ( UserService $userService ) 
    {
        $this->passwordHasher = new PasswordHasherFactory (
            [
                "user" => [ 'algorithm' => 'auto' ]
            ]
        );

        $this -> userService = $userService;
    }

    public static function getEntityFqcn (): string
    {
        return User::class;
    }

    public function configureCrud ( $crud ): Crud
    {
        $user = $this -> userService -> getCurrentUser ();

        if ( $user -> isAdmin () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Benutzer'         )
                -> setPageTitle ( 'new',    'Benutzer anlegen' )
                -> setPageTitle ( 'detail', fn ( User $user ) => sprintf ( 'Benutzer <b>%s</b> betrachten',    $user -> __toString () ) )
                -> setPageTitle ( 'edit',   fn ( User $user ) => sprintf ( 'Benutzer <b>%s</b> bearbeiten',    $user -> __toString () ) )
            ;
        }

        if ( $user -> isTutor () ||  $user -> isStudent () ||  $user -> isExtern () )
        {
            return Crud::new ()
                -> setPageTitle ( 'detail', fn ( User $user ) => sprintf ( 'Benutzer <b>%s</b> betrachten',    $user -> __toString () ) )
            ;
        }

        if ( $user -> isVerwaltung () )
        {
            return Crud::new ()
                -> setPageTitle ( 'index',  'Benutzer'         )
                -> setPageTitle ( 'new',    'Benutzer anlegen' )
                -> setPageTitle ( 'detail', fn ( User $user ) => sprintf ( 'Benutzer <b>%s</b> betrachten',    $user -> __toString () ) )
                -> setPageTitle ( 'edit',   fn ( User $user ) => sprintf ( 'Benutzer <b>%s</b> bearbeiten',    $user -> __toString () ) )
            ;
        }
    }

    public function configureFields(string $pageName): iterable
    {
        $user = $this -> userService -> getCurrentUser ();

        /*
            Fields:
                id
                email
                password
                ROLES
                salt
                isActive
                isVerified
                plainPassword
                eingereichteFehler
                eingereichteKommentare
                module
        */
        if ( $user -> isAdmin () )
        {
            return [
                IdField::new            ( 'id'            ) -> hideOnForm  (),
                IdField::new            ( 'id'            ) -> onlyOnForms () -> hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'email'         ),
                ChoiceField::new        ( 'ROLESSTRING'   ) -> setChoices   ( $this -> userService -> getRoles () ) -> setLabel ( "Rolle/Funktion" ),
                TextField::new          ( 'plainPassword' ) -> setFormType  ( PasswordType::class ) -> onlyOnforms (),
                TextField::new          ( 'pfplink'       ) -> hideOnIndex (),

                AssociationField::new   ( 'tutorIn'       )
                    -> hideOnIndex () 
                    -> setLabel    ( 'Tutor in' )
                    -> setFormTypeOptions 
                    (
                        [
                            'by_reference' => false,
                        ]
                    ),
                
                AssociationField::new   ( 'studentIn' )
                    -> hideOnIndex  ()
                    -> setLabel     ( 'Student in' )
                    -> setFormTypeOptions 
                    (
                        [
                            'by_reference' => false,
                        ]
                    ),
                
                TextEditorField::new ( 'tutorAndStudentIn' )
                    -> setTemplatePath  ( 'bundles/EasyAdminBundle/crud/TextEditorField.html.twig' ) 
                    -> hideWhenUpdating ()
                    -> hideWhenCreating ()
            ];

        }

        if ( $user -> isTutor () ||  $user -> isStudent () ||  $user -> isExtern () )
        {
            return [
                IdField::new            ( 'id'            ) -> hideOnForm  (),
                TextField::new          ( 'pfplink'       ) -> hideOnIndex (),
                TextField::new          ( 'email'         ) -> setFormTypeOption ( 'disabled', 'disabled' ) -> hideOnForm (),
            ];
        }

        if ( $user -> isVerwaltung () )
        {
            return [
                IdField::new            ( 'id'            ) -> hideOnForm  (),
                IdField::new            ( 'id'            ) -> onlyOnForms () -> hideWhenCreating() -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'email'         ),
                ChoiceField::new        ( 'ROLESSTRING'   ) -> setChoices  ( $this -> userService  -> getRoles() ) -> setLabel ( "Rolle/Funktion" ),
                TextField::new          ( 'plainPassword' ) -> setFormType ( PasswordType::class                 ) -> onlyOnforms (),
                TextField::new          ( 'pfplink'       ),

                AssociationField::new   ( 'tutorIn'       )
                    -> hideWhenCreating ()
                    -> hideOnIndex      ()
                    -> setLabel         ( 'Tutor in' )
                    -> setFormTypeOptions 
                    (
                        [
                        'by_reference' => false,
                        ]
                    ),
                
                AssociationField::new   ( 'studentIn'    ) -> hideWhenCreating () -> setLabel ( 'Student in' ) -> hideOnIndex ()
                    -> setFormTypeOptions 
                    (
                        [
                            'by_reference' => false,
                        ]
                    ),
                
                TextEditorField::new ( 'tutorAndStudentIn' )
                    -> setTemplatePath  ( 'bundles/EasyAdminBundle/crud/TextEditorField.html.twig' )
                    -> hideWhenUpdating ()
                    -> hideWhenCreating ()
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

        if ( $user -> isTutor () ||  $user -> isStudent () ||  $user -> isExtern () )
        {
            return $actions
                -> remove ( Crud::PAGE_DETAIL,   Action::EDIT )
                -> remove ( Crud::PAGE_DETAIL,   Action::DELETE )
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


    public function createEditFormBuilder ( EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context ): FormBuilderInterface
    {
        // Look at: https://github.com/EasyCorp/EasyAdminBundle/issues/3349#issuecomment-695214741
        $formBuilder = parent::createEditFormBuilder ( $entityDto, $formOptions, $context );

        $this -> addHashPasswordEventListener ( $formBuilder );

        return $formBuilder;
    }

    public function createNewFormBuilder ( EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context ): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder ( $entityDto, $formOptions, $context );

        $this -> addHashPasswordEventListener ( $formBuilder );

        return $formBuilder;
    }

    protected function addHashPasswordEventListener ( FormBuilderInterface $formBuilder )
    {
        $formBuilder -> addEventListener (
            FormEvents::SUBMIT, 
            function ( FormEvent $event ) 
            {
                /** @var User $user */
                $obj = $event -> getData ();
                if ($obj instanceof User) 
                {
                    $user     = $obj;
                    $plainPW  = $user -> getPlainPassword ();
                    if ( $plainPW ) 
                    {
                        $hash = $this -> passwordHasher -> getPasswordHasher ( 'user' ) -> hash ( $plainPW );
                        $user -> setPassword ( $hash );
                    }
                }
        
            }
        );
    }

    public function createIndexQueryBuilder ( SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters ) :QueryBuilder
    {
        parent::createIndexQueryBuilder ( $searchDto, $entityDto, $fields, $filters );
        
        $request     = $searchDto -> getRequest ();
        $qb = $this -> get ( EntityRepository::class ) -> createQueryBuilder ( $searchDto, $entityDto, $fields, $filters );

        // manipulation of query (via QB) is here possible
        return $qb;
    }

    private function getRolesArrayFilter ()
    {
        $roles  = $this -> getRolesArrayManipulated ();
        return ChoiceFilter::new ( 'ROLES' ) -> setChoices ( $roles );
    }

    private function getRolesArrayManipulated () 
    {
        $roles = $this -> userService -> getRoles ();
        
        foreach ($roles as $key => $value) 
        {
            $roles[$key] = '["'.$value.'"]';
        }

        return $roles;
    }

    public function configureFilters ( Filters $filters ): Filters
    {
        $rolesArrayFilter = $this -> getRolesArrayFilter ();
        return $filters
            -> add ( 'email'              )
            -> add ( $rolesArrayFilter    )
            -> add ( 'isVerified'         )
            -> add ( 'isActive'           )
            -> add ( 'eingereichteFehler' )
            -> add ( 'studentIn'          )
            -> add ( 'tutorIn'            )
        ;
    }
}
