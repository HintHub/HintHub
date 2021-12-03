<?php

namespace App\Controller\Admin;

use App\Entity\Fehler;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;


class FehlerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Fehler::class;
    }

    //TODO field fuer status
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('modul'),
            ChoiceField::new('status')->setChoices($this->getStatusChoices())
        ];
    }

    public function getStatusChoices() {
        return [
        'choices'  => [
            'geschlossen' => 'CLOSED',
            'offen' => 'OPEN',
            'abgelehnt' => 'REJECTED',
            'eskaliert' => 'ESCALATED',
            'wartend' => 'WAITING'
        ]];
    }
}
///'CLOSED', 'ESCALATED', 'OPEN', 'REJECTED', 'WAITING'