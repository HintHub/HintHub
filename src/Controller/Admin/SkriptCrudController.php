<?php

namespace App\Controller\Admin;

use App\Entity\Skript;
use App\Service\UserService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
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
            // TODO SkriptCrudController configureCrud isTutor
        }

        if ( $user -> isExtern () )
        {
            // TODO SkriptCrudController configureCrud isExtern
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
            // TODO SkriptCrudController configureFields isTutor
        }

        if ( $user -> isExtern () )
        {
            // TODO SkriptCrudController configureFields isExtern
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
            //TODO SkriptCrudController configureActions isTUtor
        }
        
        if ( $user -> isExtern () )
        {
            //TODO SkriptCrudController configureActions isExtern
        }

        return $actions;
    }
}
