<?php

namespace App\Controller\Admin;

use App\Entity\Modul;
use App\Service\UserService;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
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
    private UserService $userService;
    private             $choices = [];

    public function __construct ( UserService $userService )
    {
        $this -> userService = $userService;
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
                -> setPageTitle ( 'index',  'Module'  )
                -> setPageTitle ( 'new',    'Modul anlegen'     )
                -> setPageTitle ( 'detail', fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> betrachten',    $modul -> __toString() ) )
                -> setPageTitle ( 'edit',   fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> bearbeiten',    $modul -> __toString() ) )

                //-> overrideTemplate ( 'crud/detail', 'bundles/EasyAdminBundle/crud/FehlerCrudDetail.html.twig' )

                // ->overrideTemplates([
                //     'crud/index' => 'admin/pages/index.html.twig',
                //     'crud/field/textarea' => 'admin/fields/dynamic_textarea.html.twig',
                // ])
            ;
        }

        if ( $user -> isStudent () )
        {
            // TODO ModulCrudController configureCrud  isStudent
        }

        if ( $user -> isTutor () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Module'  )
                -> setPageTitle ( 'new',    'Modul anlegen'     )
                -> setPageTitle ( 'detail', fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> betrachten',    $modul -> __toString() ) )
                -> setPageTitle ( 'edit',   fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> bearbeiten',    $modul -> __toString() ) )

                -> overrideTemplate ( 'crud/detail', 'bundles/EasyAdminBundle/crud/FehlerCrudDetail.html.twig' )

                // ->overrideTemplates([
                //     'crud/index' => 'admin/pages/index.html.twig',
                //     'crud/field/textarea' => 'admin/fields/dynamic_textarea.html.twig',
                // ])
            ;
        }

        if ( $user -> isExtern () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Module'  )
                -> setPageTitle ( 'new',    'Modul anlegen'     )
                -> setPageTitle ( 'detail', fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> betrachten',    $modul -> __toString() ) )
                -> setPageTitle ( 'edit',   fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> bearbeiten',    $modul -> __toString() ) )

                -> overrideTemplate ( 'crud/detail', 'bundles/EasyAdminBundle/crud/FehlerCrudDetail.html.twig' )
            ;
        }

        if ( $user -> isVerwaltung () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Module'  )
                -> setPageTitle ( 'new',    'Modul anlegen'     )
                -> setPageTitle ( 'detail', fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> betrachten',    $modul -> __toString() ) )
                -> setPageTitle ( 'edit',   fn ( Modul $modul ) => sprintf ( 'Modul <b>%s</b> bearbeiten',    $modul -> __toString() ) )

                -> overrideTemplate ( 'crud/detail', 'bundles/EasyAdminBundle/crud/FehlerCrudDetail.html.twig' )
            ;
        }
    }

    /*public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        // +dd("test");
        $formBuilder = parent::createEditFormBuilder ($entityDto, $formOptions, $context);

        $entity = $context->getEntity()->getInstance();

        //$this->choices = $entity->getSkripte();
        $formBuilder->add('aktuellesSkript', EntityType::class, ['class' => 'App\Entity\Skript','choices' => $entity->getSkripte()]);
        //$formBuilder->add('aktuellesSkript', ChoiceType::class, ['choices' => ['hi', 'aaa']]);
        return $formBuilder;
    }*/

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
                IdField::new            ( 'id'              ) -> hideOnForm(),
                IdField::new            ( 'id'              ) -> onlyOnForms() ->  hideWhenCreating() -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'name'            ),
                TextField::new          ( 'kuerzel'         ),
                AssociationField::new   ( 'skript'          ),
                AssociationField::new   ( 'tutor'           ),
                AssociationField::new   ( 'studenten'       )
                    -> setFormTypeOptions 
                        (
                            [
                            'by_reference' => false,
                            ]
                        ),
            ];
        }

        if ( $user -> isStudent () )
        {
            // TODO ModulCrudController configureFields  isStudent
        }

        if ( $user -> isTutor () )
        {
            return [
                IdField::new            ( 'id'              ) -> hideOnForm(),
                IdField::new            ( 'id'              ) -> onlyOnForms() ->  hideWhenCreating() -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'name'            ),
                TextField::new          ( 'kuerzel'         ),
                AssociationField::new   ( 'skript'          ),
                AssociationField::new   ( 'tutor'           ),
                AssociationField::new   ( 'studenten'       )
                    -> setFormTypeOptions 
                        (
                            [
                            'by_reference' => false,
                            ]
                        ),
            ];
        }

        if ( $user -> isExtern () )
        {
            return [
                IdField::new            ( 'id'              ) -> hideOnForm(),
                IdField::new            ( 'id'              ) -> onlyOnForms() ->  hideWhenCreating() -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'name'            ),
                TextField::new          ( 'kuerzel'         ),
                AssociationField::new   ( 'skript'          ),
                AssociationField::new   ( 'tutor'           ),
                AssociationField::new   ( 'studenten'       )
                    -> setFormTypeOptions 
                        (
                            [
                            'by_reference' => false,
                            ]
                        ),
            ];
        }

        if ( $user -> isVerwaltung () )
        {
            return [
                IdField::new            ( 'id'              ) -> hideOnForm(),
                IdField::new            ( 'id'              ) -> onlyOnForms() ->  hideWhenCreating() -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'name'            ),
                TextField::new          ( 'kuerzel'         ),
                AssociationField::new   ( 'skript'          ),
                AssociationField::new   ( 'tutor'           ),
                AssociationField::new   ( 'studenten'       )
                    -> setFormTypeOptions 
                        (
                            [
                            'by_reference' => false,
                            ]
                        ),
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
            //TODO ModulCrudController configureActions isStudent
        }

        if ( $user -> isTutor () )
        {
            return $actions
                // ...
                -> add ( Crud::PAGE_INDEX,  Action::DETAIL               )
                -> remove ( Crud::PAGE_INDEX,   Action::NEW )
                -> remove ( Crud::PAGE_INDEX,   Action::EDIT )
                -> remove ( Crud::PAGE_INDEX,   Action::DELETE )
            ;
        }
        
        if ( $user -> isExtern () )
        {
            return $actions
                // ...
                -> add ( Crud::PAGE_INDEX,  Action::DETAIL               )
                -> add ( Crud::PAGE_EDIT,   Action::SAVE_AND_ADD_ANOTHER )
                -> remove ( Crud::PAGE_INDEX,   Action::DELETE )
            ;
        }

        if ( $user -> isVerwaltung () )
        {
            return $actions
                // ...
                -> add ( Crud::PAGE_INDEX,  Action::DETAIL               )
                -> add ( Crud::PAGE_EDIT,   Action::SAVE_AND_ADD_ANOTHER )
                -> remove ( Crud::PAGE_INDEX,   Action::DELETE )
            ;
        }

        return $actions;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $user = $this->userService->getCurrentUser();
        $userId = $user->getId();


        $response = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if($user->isTutor()) {
            $response -> where('entity.tutor = :userId')
            ->setParameter('userId', $userId);
        }

        return $response;
    }
}
