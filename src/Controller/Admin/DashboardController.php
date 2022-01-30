<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Modul;
use App\Entity\Fehler;
use App\Entity\Skript;
use App\Entity\Kommentar;
use App\Service\UserService;

use App\Service\FehlerService;
use App\Entity\Benachrichtigung;

use App\Repository\UserRepository;
use App\Repository\ModulRepository;
use App\Repository\FehlerRepository;

use App\Service\BenachrichtigungService;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;

use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

/**
 * DashboardController for AdminDashboard generated via php bin/console make:admin:dashboard
 * compare https://symfony.com/doc/current/EasyAdminBundle/dashboards.html
 * 
 * @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de )
 */
class DashboardController extends AbstractDashboardController
{
    // css
    private $cssFile     = 'css/admin.css';
    private $logoPath    = 'hinthub_logo.png';

    private $controllerTwigLocation = 'bundles/EasyAdminBundle/crud/DashboardController.html.twig';
    private $profileTwigLocation    = 'bundles/EasyAdminBundle/crud/profile/profile.html.twig';

    //repositories
    private FehlerRepository $fehlerRepository;
    private ModulRepository $modulRepository;
    private UserRepository $userRepository;

    // services
    private UserService             $userService;
    private BenachrichtigungService $benachrichtigungService;
    private FehlerService           $fehlerService;

    // constructor
    public function __construct (
        UserService             $userService,
        FehlerRepository        $fehlerRepository,
        ModulRepository         $modulRepository,
        UserRepository          $userRepository,
        BenachrichtigungService $benachrichtigungService
    ) 
    {
        $this -> userService             = $userService;
        $this -> fehlerRepository        = $fehlerRepository;
        $this -> modulRepository         = $modulRepository;
        $this -> userRepository          = $userRepository;
        $this -> benachrichtigungService = $benachrichtigungService;

        //$this -> fehlerService -> escalateFehler();
    }
    
    /**
     * @Route("/admin", name="admin")
     */
    public function index (): Response
    {
        $currentUser = $this -> userService -> getCurrentUser   ();
        $variables   = $this -> getVariables ( $currentUser );
        return $this -> render ( $this -> controllerTwigLocation, $variables );
        // return parent::index();
    }

    //TODO vom userservice das array nehmen (getRoleArray()) statt String abgleich
    private function getVariables ( User $user ) 
    {
        switch ( $user -> getRolesString () )
        {
            case "ROLE_ADMIN":
                return $this -> getAdminVariables       ( $user );
            case "ROLE_VERWALTUNG":
                return $this -> getVerwaltungVariables  ( $user );
            case "ROLE_EXTERN":
                return $this -> getExternVariables      ( $user );
            case "ROLE_TUTOR":
                return $this -> getTutorVariables       ( $user );
            case "ROLE_STUDENT":
                return $this -> getStudentVariables     ( $user );
            default:
                return [];
        }
    }
    
    //VARIABLES BY CURRENTUSER 
    private function getAdminVariables ( User $user ) 
    {

        $moduls                 = $this -> getCountModules   ();
        $roles                  = $this -> userService -> getRoleArray  ( $user );
        $countFehlerNachStatus  = $this -> getFehlerStatusCountArray    ( $user );
        $moduls                 = [ "moduls" => $moduls ];
        $userFrequencies        = $this -> getUserFrequencies ( $user );
        $variables              = array_merge ( $roles, $countFehlerNachStatus, $moduls, $userFrequencies );
        return $variables;
    }

    private function getVerwaltungVariables ( User $user ) 
    {
        return $this -> getAdminVariables ( $user );
    }

    private function getExternVariables ( User $user ) 
    {
        $moduls    = $this -> getCountModules ();
        $roles     = $this -> userService       -> getRoleArray ( $user );
        $users     = $this -> userRepository    -> getAllUsers  ();
        $moduls = [ "moduls" => $moduls ];
        $userFrequencies = $this -> getUserFrequencies ( $user );
        $variables = array_merge( $moduls, $userFrequencies, $roles, [ "users" => $users ] );
        return $variables;
    }

    private function getStudentVariables ( User $user ) 
    {
        $moduls                 = $this -> getCountModules();
        $roles                  = $this -> userService -> getRoleArray  ( $user );
        $countFehlerNachStatus  = $this -> getFehlerStatusCountArray    ( $user );
        $moduls                 = [ "moduls" => $moduls ];
        $userFrequencies        = $this -> getUserFrequencies ( $user );
        $variables              = array_merge ( $roles, $countFehlerNachStatus, $moduls, $userFrequencies );
        return $variables;
    }

    private function getTutorVariables(User $user) {

        $moduls                 = $this -> getCountModules ();
        $roles                  = $this -> userService -> getRoleArray  ( $user );
        $countFehlerNachStatus  = $this -> getFehlerStatusCountArray    ( $user );
        $moduls                 = [ "moduls" => $moduls ];
        $userFrequencies        = $this -> getUserFrequencies   ( $user );
        
        $variables = array_merge ( $roles, $countFehlerNachStatus, $moduls, $userFrequencies );
        return $variables;
    }

    private function getFehlerStatusCountArray ( User $user ) 
    {
   
        $offeneFehlerCount          = $this -> getCountOpen         ( $user );
        $geschlosseneFehlerCount    = $this -> getCountClosed       ( $user );
        $eskaliertFehlerCount       = $this -> getCountEscalated    ( $user );      
        $wartendFehlerCount         = $this -> getCountWaiting      ( $user );         
        $abgelehntFehlerCount       = $this -> getCountRejected     ( $user );

        $counts = 
        [
            'opens'         => $offeneFehlerCount,
            'closed'        => $geschlosseneFehlerCount,
            'escalated'     => $eskaliertFehlerCount,
            'waiting'       => $wartendFehlerCount,
            'rejected'      => $abgelehntFehlerCount
        ];

        return $counts;
    }

    // Aufruf aus dem FehlerRepository für jeden Status
    // Offene
    private function getCountOpen ( User $user )
    {
        return $this -> fehlerRepository -> countAllByUserAndOpen ( $user );
    }

    // Aufruf aus dem FehlerRepository für jeden Status
    // Geschlossen
    private function getCountClosed ( User $user )
    {
        return $this -> fehlerRepository -> countAllByUserAndClosed ( $user );
    }

    // Aufruf aus dem FehlerRepository für jeden Status
    // Wartende
    private function getCountWaiting ( User $user )
    {
        return $this -> fehlerRepository -> countAllByUserAndWaiting ( $user );
    }

    // Aufruf aus dem FehlerRepository für jeden Status
    // Eskaliert
    private function getCountEscalated ( User $user )
    {
        return $this -> fehlerRepository -> countAllByUserAndEscalated ( $user );
    }


    // Aufruf alle Module aus dem ModulRepository
    // Alle Module
    private function getCountModules()
    {
        return $this -> modulRepository -> getAllModules ();
    }

    // count rejected
    private function getCountRejected ($user)
    {
        return $this -> fehlerRepository -> countAllByUserAndStatus ( $user, 'REJECTED' );
    }


    // count roles
    private function getUserFrequencies ( User $user ) 
    {
        $students   = $this -> getAllStudents   ();
        $tutors     = $this -> getAllTutors     ();
        $extern     = $this -> getAllExtern     ();
        $verwaltung = $this -> getAllVerwaltung ();

        return [
            "students"      => $students, 
            "tutors"        => $tutors, 
            "verwaltung"    => $verwaltung, 
            "extern"        => $extern
        ];
    }

    // Aufruf alle Studenten aus dem UserRepository
    // Alle Studenten
    private function getAllStudents ()
    {
        return $this -> userRepository -> getAllStudents ();
    }

    // Aufruf alle Tutoren aus dem UserRepository
    // Alle Tutoren
    private function getAllTutors ()
    {
        return $this -> userRepository -> getAllTutors ();
    }

    // Aufruf alle Externen aus dem UserRepository
    // Alle Externen
    private function getAllExtern ()
    {
        return $this -> userRepository -> getAllExtern ();
    }

    // Aufruf alle Verwaltung aus dem UserRepository
    // Alle Verwaltung
    private function getAllVerwaltung ()
    {
        return $this -> userRepository -> getAllVerwaltung ();
    }

    //COUNT FREQUENCIES ROLES

    // INDEX METHODS END

    public function configureDashboard (): Dashboard
    {
    	@$appName = !empty ( $_ENV['APP_NAME'] ) ? $_ENV['APP_NAME'] : 'Missing in env';
    	
        return Dashboard::new()
            -> setTitle('<img class="logo" src="'. $this -> logoPath . '" alt="'.$appName.'"/>' );
        ;
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $mail    = $this -> userService -> getCurrentUser () -> getEmail    ();
        $pfpLink = $this -> userService -> getCurrentUser () -> getPfplink  ();

        return parent::configureUserMenu ( $user )
            // use the given $user object to get the user name
            -> setName          ( $mail    )
            -> displayUserName  ( true     )
            -> setAvatarUrl     ( $pfpLink )
            // you can use any type of menu item, except submenus
            -> addMenuItems(
                [
                    MenuItem::linkToRoute ( 'Profil bearbeiten', 'fa fa-id-card', 'profile', [] ),
                    // MenuItem::section(),
                    // MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
                ]
            );
    }


    public function configureAssets(): Assets
    {
        return Assets::new() -> addCssFile ( $this -> cssFile );
    }

    // Dashboard Link Menu Item
    private function lDashboard  ($text = 'Home', $icon = 'fa fa-home') 
    {
      return MenuItem::linktoDashboard   ( $text, $icon );
    }

    // Helper for MenuItems
    private function getMenuItem ( $text='', $icon='', $class=null ) 
    {
        return MenuItem::linkToCrud ( $text, $icon,  $class); 
    }

    // Fehler Menu Item
    private function mFehler    ( $text = 'Fehlermeldungen', $icon = 'fas fa-exclamation' )  
    {
        return $this -> getMenuItem ( $text, $icon,     Fehler::class );
    }

    // User Menu Item
    private function mUser      ( $text = 'Benutzer', $icon = 'fas fa-users' )  
    {
        return $this -> getMenuItem ($text, $icon,      User::class );
    }

    // Modul Menu Item
    private function mModul     ( $text = 'Module', $icon = 'fas fa-layer-group' )   
    {
        return $this -> getMenuItem ($text, $icon,      Modul::class );
    }

    // Skript Menu Item
    private function mSkript    ( $text = 'Skripte', $icon = 'fas fa-scroll' )   
    {
        return $this -> getMenuItem ( $text, $icon,     Skript::class );
    }

    // Kommentar Menu Item
    private function mKommentar ( $text = 'Kommentare', $icon = 'fas fa-comments' )  :CrudMenuItem
    {
        return $this -> getMenuItem ( $text, $icon,     Kommentar::class );
    }

    // Kommentar Menu Item
    private function mBenachrichtigung ( $text = 'Benachrichtigungen', $icon = 'fas fa-bell' ) 
    {
        $numberBenachrichtigungen = $this -> benachrichtigungService -> getCountUnreadBenachrichtigungen ();
        
        if ($numberBenachrichtigungen == 0)
            return null;
        
        return $this -> getMenuItem ( "$text ($numberBenachrichtigungen)", $icon,     Benachrichtigung::class );
    }

    private function mEditProfile ()
    {
        return MenuItem::linkToRoute('Profil bearbeiten', 'fa fa-id-card', 'profile');
    }

    private function mSectionProfile ()
    {
        return MenuItem::section('Mein Profil');
    }

    private function mSectionSystem ()
    {
        return MenuItem::section('System');
    }

    private function menuAll()
    {
        $hasBenachrichtigungen = $this -> mBenachrichtigung  ();

        if ( $hasBenachrichtigungen === null )
        {
            return 
            [
                $this -> lDashboard         (),
                
                $this -> mSectionProfile    (),
                $this -> mEditProfile       (),

                $this -> mSectionSystem     (),
                $this -> mFehler            (),
                $this -> mUser              (),
                //$this -> mKommentar       (),
                $this -> mModul             (),
                $this -> mSkript            (),
            ];
        }
        else
        {
            return 
            [
                $this -> lDashboard         (),
                
                $this -> mSectionProfile    (),
                $hasBenachrichtigungen,
                $this -> mEditProfile       (),

                $this -> mSectionSystem     (),
                $this -> mFehler            (),
                $this -> mUser              (),
                //$this -> mKommentar         (),
                $this -> mModul             (),
                $this -> mSkript            ()
            ];
        }
    }

    private function menuAdmin ()
    {
        return $this -> menuAll();
    }

    private function menuStudent ()
    {
        $hasBenachrichtigungen = $this -> mBenachrichtigung  ();

        if ( $hasBenachrichtigungen === null )
        {
            return 
            [
                $this -> lDashboard         (),

                $this -> mSectionProfile    (),
                $this -> mEditProfile       (),

                $this -> mSectionSystem     (),
                $this -> mFehler            ()
            ];
        }
        else
        {
            return 
            [
                $this -> lDashboard         (),
                
                $this -> mSectionProfile    (),
                $hasBenachrichtigungen,
                $this -> mEditProfile       (),

                $this -> mSectionSystem     (),
                $this -> mFehler            ()
            ];
        }
    }

    private function menuTutor () 
    {
        $hasBenachrichtigungen = $this -> mBenachrichtigung  ();

        if ( $hasBenachrichtigungen === null )
        {
            return 
            [
                $this -> lDashboard         (),

                $this -> mSectionProfile    (),
                $this -> mEditProfile       (),

                $this -> mSectionSystem     (),
                $this -> mFehler            (),
                $this -> mModul             (),
                $this -> mSkript            (),
            ];
        }
        else
        {
            return 
            [
                $this -> lDashboard         (),

                $this -> mSectionProfile    (),
                $hasBenachrichtigungen,
                $this -> mEditProfile       (),

                $this -> mSectionSystem     (),
                $this -> mFehler            (),
                $this -> mModul             (),
                $this -> mSkript            (),
            ];  
        }
    }

    private function menuExtern () 
    {
        return 
        [
            $this -> lDashboard         (),
            
            $this -> mSectionProfile    (),
            $this -> mEditProfile       (),

            $this -> mSectionSystem     (),
            $this -> mModul             (),
            $this -> mSkript            (),
        ];
    }

    private function menuVerwaltung () 
    {
        $hasBenachrichtigungen = $this -> mBenachrichtigung ();

        if ( $hasBenachrichtigungen === null )
        {
            return 
            [
                $this -> lDashboard         (),
                
                $this -> mSectionProfile    (),
                $this -> mEditProfile       (),

                $this -> mSectionSystem     (),
                $this -> mUser              (),
                $this -> mModul             (),
                $this -> mSkript            (),
                $this -> mKommentar         ()
            ];
        }
        else
        {
            return 
            [
                $this -> lDashboard         (),
                
                $this -> mSectionProfile    (),
                $hasBenachrichtigungen,
                $this -> mEditProfile       (),

                $this -> mSectionSystem     (),
                $this -> mUser              (),
                $this -> mModul             (),
                $this -> mSkript            (),
                $this -> mKommentar         ()
            ];           
        }
    }

    public function configureMenuItems(): iterable
    {
        $user = $this -> userService -> getCurrentUser ();
        
        // for testing use menuAll();

        if ( $user -> isAdmin       () )
            return $this -> menuAdmin      ();

        if ( $user -> isStudent     () )
            return $this -> menuStudent    ();

        if ( $user -> isTutor       () )
            return $this -> menuTutor      ();

        if ( $user -> isExtern      () )
            return $this -> menuExtern     ();

        if ( $user -> isVerwaltung  () )
            return $this -> menuVerwaltung ();

        // always return "something"
        return $this -> menuAll ();
    }
}
