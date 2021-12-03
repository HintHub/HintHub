<?php

namespace App\Controller\Admin;

use App\Entity\Skript;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class SkriptCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Skript::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('fehler'),
            AssociationField::new('modul')
        ];
    }
}
