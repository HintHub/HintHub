<?php

namespace App\Controller\Admin;

use App\Entity\Skript;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
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
    public static function getEntityFqcn(): string
    {
        return Skript::class;
    }

    public function configureCrud ($crud): Crud
    {
        return Crud::new()
            -> setPageTitle ( 'index',  'Skripte'  )
            -> setPageTitle ( 'new',    'Skript anlegen'     )
            -> setPageTitle ( 'detail', fn ( Skript $skript ) => sprintf ( 'Skript <b>%s</b> betrachten',    $skript -> __toString() ) )
            -> setPageTitle ( 'edit',   fn ( Skript $skript ) => sprintf ( 'Skript <b>%s</b> bearbeiten',    $skript -> __toString() ) )

            -> overrideTemplate ( 'crud/detail', 'bundles/EasyAdminBundle/crud/FehlerCrudDetail.html.twig' )

            // ->overrideTemplates([
            //     'crud/index' => 'admin/pages/index.html.twig',
            //     'crud/field/textarea' => 'admin/fields/dynamic_textarea.html.twig',
            // ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        /*
            Fields:
                id,
                version,
                modul,
                fehler
        */
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
