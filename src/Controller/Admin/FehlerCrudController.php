<?php

namespace App\Controller\Admin;

use App\Entity\Fehler;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * CrudController for Fehler generated via php bin/console make:admin:crud
 * compare https://symfony.com/bundles/EasyAdminBundle/current/crud.html
 * 
 * @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de )
 */
class FehlerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Fehler::class;
    }

    //TODO field fuer status
    public function configureFields(string $pageName): iterable
    {
        /*
            Fields:
                id
                status
                seite
                kommentare
                verwandteFehler
                skript - fixed by KS, 19 Dez 2021 01:13
        */
        return [
            IdField::new('id') -> hideOnForm(),
            IdField::new('id') -> onlyOnForms() ->  hideWhenCreating() -> setFormTypeOption('disabled','disabled'),
            TextField::new('name'),
            ChoiceField::new('status') -> setChoices ( $this -> getStatusChoices() ),
            NumberField::new('seite'),
            AssociationField::new('kommentare'),
            AssociationField::new('verwandteFehler'),
            AssociationField::new('skript')
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