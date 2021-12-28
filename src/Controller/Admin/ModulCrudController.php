<?php

namespace App\Controller\Admin;

use App\Entity\Modul;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * CrudController for Modul generated via php bin/console make:admin:crud
 * compare https://symfony.com/bundles/EasyAdminBundle/current/crud.html
 * 
 * @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de )
 */
class ModulCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Modul::class;
    }

    public function configureCrud ($crud): Crud
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

    public function configureFields(string $pageName): iterable
    {
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
        return [
            IdField::new            ( 'id'              ) -> hideOnForm(),
            IdField::new            ( 'id'              ) -> onlyOnForms() ->  hideWhenCreating() -> setFormTypeOption ( 'disabled', 'disabled' ),
            TextField::new          ( 'name'            ),
            TextField::new          ( 'kuerzel'         ),
            AssociationField::new   ( 'skripte'         ),
            AssociationField::new   ( 'aktuellesSkript' ),
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
