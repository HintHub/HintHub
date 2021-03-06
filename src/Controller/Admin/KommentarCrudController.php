<?php

namespace App\Controller\Admin;

use App\Entity\Kommentar;
use App\Service\UserService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
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
    private $userService;

    public function __construct ( UserService $userService ) 
    {
        $this -> userService = $userService;
    }
    
    public static function getEntityFqcn (): string
    {
        return Kommentar::class;
    }

    public function configureCrud ( $crud ): Crud
    {
        $user = $this -> userService -> getCurrentUser ();

        if ( $user -> isAdmin () )
        {         
            return Crud::new()
                -> setPageTitle ( 'index',  'Kommentare'          )
                -> setPageTitle ( 'new',    'Kommentar erstellen' )
                -> setPageTitle ( 'detail', fn ( Kommentar $kommentar ) => sprintf ( 'Kommentar <b>%s</b> betrachten',    $kommmentar -> __toString () ) )
                -> setPageTitle ( 'edit',   fn ( Kommentar $kommentar ) => sprintf ( 'Kommentar <b>%s</b> bearbeiten',    $kommentar  -> __toString () ) )
            ;
        }

        if ( $user -> isStudent () )
        {
            // TODO KommentarCrud configureCrud isStudent
        }

        if ( $user -> isTutor () )
        {
            return Crud::new()
                -> setPageTitle ( 'index',  'Kommentare'          )
                -> setPageTitle ( 'new',    'Kommentar erstellen' )
                -> setPageTitle ( 'detail', fn ( Kommentar $kommentar ) => sprintf ( 'Kommentar <b>%s</b> betrachten',    $kommmentar -> __toString () ) )
                -> setPageTitle ( 'edit',   fn ( Kommentar $kommentar ) => sprintf ( 'Kommentar <b>%s</b> bearbeiten',    $kommentar  -> __toString () ) )
            ;
        }

        if ( $user -> isExtern () )
        {
            // TODO KommentarCrud configureCrud isExtern
        }

        if ( $user -> isVerwaltung () )
        {
            return Crud::new()
            -> setPageTitle ( 'index',  'Kommentare'          )
            -> setPageTitle ( 'new',    'Kommentar erstellen' )
            -> setPageTitle ( 'detail', fn ( Kommentar $kommentar ) => sprintf ( 'Kommentar <b>%s</b> betrachten',    $kommmentar -> __toString () ) )
            -> setPageTitle ( 'edit',   fn ( Kommentar $kommentar ) => sprintf ( 'Kommentar <b>%s</b> bearbeiten',    $kommentar  -> __toString () ) )
        ;
        }

    }

    public function configureFields ( string $pageName ): iterable
    {
        $user = $this -> userService -> getCurrentUser ();
        /*
            Fields:
                id,
                text,
                fehler
        */

        if ( $user -> isAdmin () )
        {
            return [
                IdField::new            ( 'id'     ) -> hideOnForm  (),
                IdField::new            ( 'id'     ) -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextEditorField::new    ( 'text'   ),
                AssociationField::new   ( 'fehler' )
            ];
        }

        if ( $user -> isVerwaltung () )
        {
            return [
                IdField::new            ( 'id'     ) -> hideOnForm  (),
                IdField::new            ( 'id'     ) -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextEditorField::new    ( 'text'   ),
                AssociationField::new   ( 'fehler' )
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

        return $actions;
    }

    /*
        When a Fehler gets added to DB (persisted)
        @author Karim Saad (karim.saad@iubh.de)
        @date 20.12.2021 03:05
    */
    public function createEntity ( string $entityFqcn ) 
    {
        $currentUser     = $this -> userService -> getCurrentUser ();
        $entity          = new Kommentar ();
        $currentDateTime = new \DateTime ();

        // Datum Trait
        $entity -> setDatumLetzteAenderung   ( $currentDateTime );
        $entity -> setDatumErstellt          ( $currentDateTime );

        // Einreicher Trait
        $entity -> setEinreicher             ( $currentUser     );

        return $entity;
    }

}
