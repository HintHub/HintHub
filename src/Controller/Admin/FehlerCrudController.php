<?php

namespace App\Controller\Admin;

use App\Entity\Fehler;
use App\Entity\Kommentar;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
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
 * @author karim.saad@iubh.de ( karim.saad@iubh.de ) - since UserReporting & easyadmincrud configs
 */
class FehlerCrudController extends AbstractCrudController
{
    private UserService $userService;

            //TODO isExtern check - siehe anderes TODO

    public function __construct ( UserService $userService ) 
    {
        $this -> userService = $userService;
        // dd ($this->userService->getCurrentUser()->getRoles());
    }
    
    public static function getEntityFqcn (): string
    {
        return Fehler::class;
    }


    public function configureCrud ( $crud ): Crud
    {
        return Crud::new()
            -> setPageTitle ( 'index',  'Fehlermeldungen'   )
            -> setPageTitle ( 'new',    'Fehler melden'     )
            -> setPageTitle ( 'detail', fn ( Fehler $fehler ) => sprintf ( 'Fehlermeldung <b>%s</b> betrachten',    $fehler -> __toString () ) )
            -> setPageTitle ( 'edit',   fn ( Fehler $fehler ) => sprintf ( 'Fehler <b>%s</b> bearbeiten',           $fehler -> __toString () ) )
 
            -> overrideTemplate ( 'crud/detail', 'bundles/EasyAdminBundle/crud/FehlerCrudDetail.html.twig' )

            // ->overrideTemplates([
            //     'crud/index' => 'admin/pages/index.html.twig',
            //     'crud/field/textarea' => 'admin/fields/dynamic_textarea.html.twig',
            // ])
        ;
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
            return $actions
                // ...
                -> add ( Crud::PAGE_INDEX,  Action::DETAIL               )
                -> add ( Crud::PAGE_EDIT,   Action::SAVE_AND_ADD_ANOTHER )
            ;
        }

        if ( $user -> isTutor () )
        {
            return $actions
                // ...
                -> add ( Crud::PAGE_INDEX,  Action::DETAIL               )
                -> add ( Crud::PAGE_EDIT,   Action::SAVE_AND_ADD_ANOTHER )
            ;
        }
        return $actions;
    }

    //TODO field fuer status
    public function configureFields ( string $pageName ): iterable
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
        $user                = $this -> userService -> getCurrentUser ();
        $statusChoices       = $this -> getStatusChoices ();
        $statusChoicesKeys   = array_keys   ($statusChoices);
        $statusChoicesValues = array_values ($statusChoices);

        // dd([ $statusChoicesKeys[0] => $statusChoicesValues[0] ]);
        if ( $user -> isAdmin () )
        {
            return [
                 
            ];
        }

        if ( $user -> isStudent () )
        {
            return [
                IdField::new            (   'id'               )    -> hideOnForm  (),
                IdField::new            (   'id'               )    -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          (   'name'             ),
                ChoiceField::new        (   'status'           )    -> hideWhenCreating () -> setChoices ( $statusChoices ),
                NumberField::new        (   'seite'            ),
                AssociationField::new   (   'skript'           ),
                TextEditorField::new    (   'kommentar'        )    -> onlyWhenCreating  (),
                AssociationField::new   (   'kommentare'       )    -> hideWhenCreating  (),
                AssociationField::new   (   'verwandteFehler'  )    -> hideWhenCreating  (),
                AssociationField::new   (   'einreicher'       )    -> hideWhenCreating  (),
                DateField::new          (   'datum_erstellt'   )    -> hideWhenCreating  (),
            ];
        }

        if ( $user -> isTutor () )
        {
            return [
                IdField::new            (   'id'               )    -> hideOnForm  (),
                IdField::new            (   'id'               )    -> onlyOnForms () ->  hideWhenCreating () -> setFormTypeOption ( 'disabled', 'disabled' ),
                TextField::new          (   'name'             ),
                ChoiceField::new        (   'status'           )    -> setChoices ( $statusChoices ),
                NumberField::new        (   'seite'            ),
                AssociationField::new   (   'skript'           ),
                TextEditorField::new    (   'kommentar'        )    -> onlyWhenCreating  (),
                AssociationField::new   (   'kommentare'       )    -> hideWhenCreating() ,
                AssociationField::new   (   'verwandteFehler'  ),
                AssociationField::new   (   'einreicher'       )    -> hideWhenCreating  (),
                DateField::new          (   'datum_erstellt'   )    -> hideWhenCreating(),
            ];
        }
    }

    /**
     * @author ali-kemal.yalama (ali-kemal.yalama@iubh.de)
     */
    public function getStatusChoices () 
    {
        $user = $this -> userService -> getCurrentUser ();

        if ( $user -> isAdmin() )
        {
            return [
                'offen'         =>  'OPEN',
            ];
        }


        if ( $user -> isTutor() )
        {
            return [
                'offen'         =>  'OPEN',
                'geschlossen'   =>  'CLOSED',
                'abgelehnt'     =>  'REJECTED',
                'eskaliert'     =>  'ESCALATED',
                'wartend'       =>  'WAITING'
            ];
        }


        if ( $user -> isStudent() )
        {
            return [
                'offen'         =>  'OPEN',
                'geschlossen'   =>  'CLOSED'
            ];
        }
    }

    /*
        When a Fehler gets added to DB (persisted)
        @author Karim Saad ( karim.saad@iubh.de )
        @date 20.12.2021 03:05
    */
    public function createEntity ( string $entityFqcn ) 
    {
        $currentUser     = $this -> userService -> getCurrentUser ();
        $entity          = new Fehler    ();
        $currentDateTime = new \DateTime ();

        // Datum Trait
        $entity -> setDatumLetzteAenderung   ( $currentDateTime );
        $entity -> setDatumErstellt          ( $currentDateTime );

        // Einreicher Trait
        $entity -> setEinreicher             ( $currentUser     );

        return $entity;
    }

    /*
        After the insertation to DB / persistence
        
        @author karim.saad ( karim.saad@iubh.de )
        @date 22.12.2021 13:45
    */
    public function persistEntity ( EntityManagerInterface $em, $entity) : void
    {
        $currentUser     = $this -> userService -> getCurrentUser ();
        $currentDateTime = new \DateTime ();

        $statusChoices       = $this -> getStatusChoices ();
        $statusChoicesKeys   = array_keys   ( $statusChoices );
        $statusChoicesValues = array_values ( $statusChoices );

        //TODO isExtern check
        
        if ( $currentUser -> isStudent () )
        {
            // Here read the initial kommentar text and convert it to kommentar
            $dt   = new \DateTime();
            $text = "User (ID:". $currentUser -> getId () . ") hat ein Ticket erstellt";
            
            $kommentar  = new Kommentar ( );
            $kommentar
            -> setFehler                ( $entity          )
            -> setText                  ( $text            )
            -> setDatumLetzteAenderung  ( $currentDateTime )
            -> setDatumErstellt         ( $currentDateTime )
            -> setEinreicher            ( $currentUser     );
            
            $kommentar1 = new Kommentar ( );
            $kommentar1 
            -> setFehler                    ( $entity                    )
            -> setText                      ( $entity -> getKommentar () )
            -> setDatumLetzteAenderung      ( $dt                        )
            -> setDatumErstellt             ( $dt                        )
            -> setEinreicher                ( $currentUser               );

            $entity -> addKommentare ( $kommentar1 );
            $entity -> addKommentare ( $kommentar  );

            // set status opened
            $entity -> setStatus ( $statusChoicesValues [0] );
        
        }

        if ( $currentUser -> isTutor () )
        {
            // Here read the initial kommentar text and convert it to kommentar
            $dt   = new \DateTime();
            $text = "User (ID:". $currentUser -> getId () . ") hat ein Ticket erstellt";
            
            $kommentar  = new Kommentar ( );
            $kommentar
            -> setFehler                ( $entity          )
            -> setText                  ( $text            )
            -> setDatumLetzteAenderung  ( $currentDateTime )
            -> setDatumErstellt         ( $currentDateTime )
            -> setEinreicher            ( $currentUser     );
            
            $kommentar1 = new Kommentar ( );
            $kommentar1 
            -> setFehler                    ( $entity                    )
            -> setText                      ( $entity -> getKommentar () )
            -> setDatumLetzteAenderung      ( $dt                        )
            -> setDatumErstellt             ( $dt                        )
            -> setEinreicher                ( $currentUser               );

            $entity -> addKommentare ( $kommentar1 );
            $entity -> addKommentare ( $kommentar  );

            // set status opened
            $entity -> setStatus ( $statusChoicesValues [0] );
        
        }

        // $this -> updateSlug     ( $entity );
        parent::persistEntity   ( $em, $entity );
    }
    
}