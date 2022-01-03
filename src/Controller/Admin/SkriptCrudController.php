<?php

namespace App\Controller\Admin;

use App\Entity\Skript;
use App\Service\UserService;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * CrudController for Skript generated via php bin/console make:admin:crud
 * compare https://symfony.com/bundles/EasyAdminBundle/current/crud.html
 * 
 * @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de )
 */
class SkriptCrudController extends AbstractCrudController
{
    private UserService $userService;
    
    public function __construct ( UserService $userService )
    {
        $this -> userService = $userService;
    }
    
    public static function getEntityFqcn(): string
    {
        return Skript::class;
    }

    public function configureCrud ($crud): Crud
    {
        $user = $this -> userService -> getCurrentUser ();

        if ( $user -> isAdmin () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Skripte'  )
                -> setPageTitle ( 'new',    'Skript anlegen'     )
                -> setPageTitle ( 'detail', fn ( Skript $skript ) => sprintf ( 'Skript <b>%s</b> betrachten',    $skript -> __toString() ) )
                -> setPageTitle ( 'edit',   fn ( Skript $skript ) => sprintf ( 'Skript <b>%s</b> bearbeiten',    $skript -> __toString() ) )

                //-> overrideTemplate ( 'crud/detail', 'bundles/EasyAdminBundle/crud/FehlerCrudDetail.html.twig' )

                // ->overrideTemplates([
                //     'crud/index' => 'admin/pages/index.html.twig',
                //     'crud/field/textarea' => 'admin/fields/dynamic_textarea.html.twig',
                // ])
            ;
        }

        if ( $user -> isStudent () )
        {
            // TODO SkriptCrudController configureCrud isStudent
        }

        if ( $user -> isTutor () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Skripte'  )
                -> setPageTitle ( 'new',    'Skript anlegen'     )
                -> setPageTitle ( 'detail', fn ( Skript $skript ) => sprintf ( 'Skript <b>%s</b> betrachten',    $skript -> __toString() ) )
                -> setPageTitle ( 'edit',   fn ( Skript $skript ) => sprintf ( 'Skript <b>%s</b> bearbeiten',    $skript -> __toString() ) )
            ;
        }

        if ( $user -> isExtern () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Skripte'  )
                -> setPageTitle ( 'new',    'Skript anlegen'     )
                -> setPageTitle ( 'detail', fn ( Skript $skript ) => sprintf ( 'Skript <b>%s</b> betrachten',    $skript -> __toString() ) )
                -> setPageTitle ( 'edit',   fn ( Skript $skript ) => sprintf ( 'Skript <b>%s</b> bearbeiten',    $skript -> __toString() ) )
            ;
        }

        if ( $user -> isVerwaltung () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Skripte'  )
                -> setPageTitle ( 'new',    'Skript anlegen'     )
                -> setPageTitle ( 'detail', fn ( Skript $skript ) => sprintf ( 'Skript <b>%s</b> betrachten',    $skript -> __toString() ) )
                -> setPageTitle ( 'edit',   fn ( Skript $skript ) => sprintf ( 'Skript <b>%s</b> bearbeiten',    $skript -> __toString() ) )
            ;
        }
    }

    public function configureFields(string $pageName): iterable
    {
        $user = $this -> userService -> getCurrentUser ();
        /*
            Fields:
                id,
                version,
                modul,
                fehler
        */

        if ( $user -> isAdmin () )
        {
            return [
                IdField::new            ( 'id'      ) -> hideOnForm(),
                IdField::new            ( 'id'      ) -> onlyOnForms() ->  hideWhenCreating() -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'name'    ),
                NumberField::new        ( 'version' ),
                AssociationField::new   ( 'fehler'  ),
                AssociationField::new   ( 'modul'   )
            ];
        }


        if ( $user -> isStudent () )
        {
            // TODO SkriptCrudController configureFields isStudent
        }

        if ( $user -> isTutor () )
        {
            return [
                IdField::new            ( 'id'      ) -> hideOnForm(),
                IdField::new            ( 'id'      ) -> onlyOnForms() ->  hideWhenCreating() -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'name'    ),
                NumberField::new        ( 'version' ),
                AssociationField::new   ( 'fehler'  ),
                AssociationField::new   ( 'modul'   )
            ];
        }

        if ( $user -> isExtern () )
        {
            return [
                IdField::new            ( 'id'      ) -> hideOnForm(),
                IdField::new            ( 'id'      ) -> onlyOnForms() ->  hideWhenCreating() -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'name'    ),
                NumberField::new        ( 'version' ),
                AssociationField::new   ( 'fehler'  ),
                AssociationField::new   ( 'modul'   )
            ];
        }

        if ( $user -> isVerwaltung () )
        {
            return [
                IdField::new            ( 'id'      ) -> hideOnForm(),
                IdField::new            ( 'id'      ) -> onlyOnForms() ->  hideWhenCreating() -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          ( 'name'    ),
                NumberField::new        ( 'version' ),
                AssociationField::new   ( 'fehler'  ),
                AssociationField::new   ( 'modul'   )
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
            //TODO SkriptCrudController configureActions isStudent
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

        //TODO: Transitivitaet
        /*if($user->isTutor()) {
            $response -> where('entity.modul.tutor = :userId')
            ->setParameter('userId', $userId);
        }*/

        return $response;
    }
}
