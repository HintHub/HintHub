<?php

namespace App\Controller\Admin;

use App\Entity\Benachrichtigung;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BenachrichtigungCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Benachrichtigung::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
