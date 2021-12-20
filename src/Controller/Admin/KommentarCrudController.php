<?php

namespace App\Controller\Admin;

use App\Entity\Kommentar;
use App\Service\UserService;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * CrudController for Kommentar generated via php bin/console make:admin:crud
 * compare https://symfony.com/bundles/EasyAdminBundle/current/crud.html
 * 
 * @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de )
 */
class KommentarCrudController extends AbstractCrudController
{
    private UserService $userService;

    public function __construct (UserService $userService) 
    {
        $this -> userService = $userService;
    }
    
    public static function getEntityFqcn(): string
    {
        return Kommentar::class;
    }

    public function configureFields(string $pageName): iterable
    {
        /*
            Fields:
                id,
                text,
                fehler
        */
        return [
            IdField::new('id') -> hideOnForm(),
            IdField::new('id') -> onlyOnForms() ->  hideWhenCreating() -> setFormTypeOption('disabled','disabled'),
            TextEditorField::new('text'),
            AssociationField::new('fehler')
        ];
    }

        /*
        When a Fehler gets added to DB (persisted)
        @author Karim Saad (karim.saad@iubh.de)
        @date 20.12.2021 03:05
    */
    public function createEntity (string $entityFqcn) {
        $currentUser    = $this -> userService -> getCurrentUser();
        $entity          = new Kommentar    ();
        $currentDateTime = new \DateTime ();

        // Datum Trait
        $entity -> setDatumLetzteAenderung   ( $currentDateTime );
        $entity -> setDatumErstellt          ( $currentDateTime );

        // Einreicher Trait
        $entity -> setEinreicher             ( $currentUser     );

        return $entity;
    }

}
