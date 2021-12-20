<?php

namespace App\Controller\Admin;

use App\Entity\Modul;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

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
        ];
    }

}
