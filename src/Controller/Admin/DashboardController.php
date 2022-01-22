<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Modul;
use App\Entity\Fehler;
use App\Entity\Skript;
use App\Entity\Kommentar;

use App\Service\UserService;

use App\Repository\FehlerRepository;
use App\Repository\ModulRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
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

    //repositories
    private FehlerRepository $fehlerRepository;
    private ModulRepository $modulRepository;
    private UserRepository $userRepository;

    // services
    private UserService $userService;

    // constructor
    public function __construct ( UserService $userService, FehlerRepository $fehlerRepository, ModulRepository $modulRepository, UserRepository $userRepository) 
    {
        $this -> userService        = $userService;
        $this -> fehlerRepository   = $fehlerRepository;
        $this -> modulRepository    = $modulRepository;
        $this -> userRepository     = $userRepository;
    }
    
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $currentUser = $this->userService->getCurrentUser();
        
        $counts    = $this -> getCountArray ($currentUser);
        $roles     = $this -> getRoleArray  ($currentUser);
        $opens     = $this -> getCountOpen  ($currentUser);
        $closed    = $this -> getCountClosed ($currentUser);
        $waiting   = $this -> getCountWaiting ($currentUser);
        $escalated = $this -> getCountEscalated ($currentUser);
        $isAdmin = $this->userService->getCurrentUser()->isAdmin();
        $moduls    = $this -> getCountModules();
        $students  = $this -> getAllStudents();
        $tutors    = $this -> getAllTutors();
        $extern    = $this -> getAllExtern();
        $verwaltung = $this -> getAllVerwaltung();

        $variables = array_merge($counts, $roles);

        //dd($variables);

        // test t
        return $this -> 
            render ( $this -> controllerTwigLocation,["opens" => $opens, "closed" => $closed, "waiting" => $waiting, "escalated" => $escalated, "isAdmin" => $isAdmin,
                                                    "moduls" => $moduls, "students" => $students, "tutors" => $tutors, "verwaltung" => $verwaltung, "extern" => $extern] );
        // return parent::index();
    }

    // Aufruf aus dem FehlerRepository für jeden Status
    // Offene
    private function getCountOpen(User $user)
    {
        $openByUser                =
            $this->fehlerRepository->countAllByUserAndOpen($user);
        return $openByUser;
    }
    // Aufruf aus dem FehlerRepository für jeden Status
    // Geschlossen
    private function getCountClosed(User $user)
    {
        $closedByUser                =
            $this->fehlerRepository->countAllByUserAndClosed($user);
        return $closedByUser;
    }
    // Aufruf aus dem FehlerRepository für jeden Status
    // Wartende
    private function getCountWaiting(User $user)
    {
        $waitingByUser                =
            $this->fehlerRepository->countAllByUserAndWaiting($user);
        return $waitingByUser;
    }
    // Aufruf aus dem FehlerRepository für jeden Status
    // Eskaliert
    private function getCountEscalated(User $user)
    {
        $escalatedByUser                =
            $this->fehlerRepository->countAllByUserAndEscalated($user);
        return $escalatedByUser;
    }
    // Aufruf alle Module aus dem ModulRepository
    // Alle Module
    private function getCountModules()
    {
        $allModuls                      =
            $this->modulRepository->getAllModules();
        return $allModuls;
    }
    // Aufruf alle Studenten aus dem UserRepository
    // Alle Studenten

    private function getAllStudents()
    {
        $allStudents                    =
            $this->userRepository->getAllStudents();
        return $allStudents;
    }
    // Aufruf alle Tutoren aus dem UserRepository
    // Alle Tutoren

    private function getAllTutors()
    {
        $allTutors                    =
            $this->userRepository->getAllTutors();
        return $allTutors;
    }
    // Aufruf alle Externen aus dem UserRepository
    // Alle Externen

    private function getAllExtern()
    {
        $allExtern                   =
            $this->userRepository->getAllExtern();
        return $allExtern;
    }
    // Aufruf alle Verwaltung aus dem UserRepository
    // Alle Verwaltung

    private function getAllVerwaltung()
    {
        $allVerwaltung               =
            $this->userRepository->getAllVerwaltung();
        return $allVerwaltung;
    }






    private function getCountArray(User $user) 
    {
   
        $offeneFehlerCount          = 
            $this->fehlerRepository->countAllByUserAndStatus($user, 'OPEN');
        
        $geschlosseneFehlerCount    = 
            $this->fehlerRepository->countAllByUserAndStatus($user, 'CLOSED');
        
        $eskaliertFehlerCount       = 
            $this->fehlerRepository->countAllByUserAndStatus($user, 'ESCALATED');
        
        $wartendFehlerCount         = 
            $this->fehlerRepository->countAllByUserAndStatus($user, 'WAITING');
        
        $abgelehntFehlerCount       = 
            $this->fehlerRepository->countAllByUserAndStatus($user, 'REJECTED');

        $counts = 
        [
            'offeneFehlerCount'         => $offeneFehlerCount,
            'geschlosseneFehlerCount'   => $geschlosseneFehlerCount,
            'eskaliertFehlerCount'      => $eskaliertFehlerCount,
            'wartendFehlerCount'        => $wartendFehlerCount,
            'abgelehntFehlerCount'      => $abgelehntFehlerCount
        ];

        return $counts;
    }

    private function getRoleArray(User $user) 
    {
        $isAdmin        = $user -> isAdmin       ();
        $isTutor        = $user -> isTutor       ();
        $isStudent      = $user -> isStudent     ();
        $isVerwaltung   = $user -> isVerwaltung  ();
        $isExtern       = $user -> isExtern      ();

        $roles = 
        [
            'isAdmin'       => $isAdmin,
            'isTutor'       => $isTutor,
            'isStudent'     => $isStudent,
            'isVerwaltung'  => $isVerwaltung,
            'isExtern'      => $isExtern,
            'isDisplayChart'=> !$isExtern
        ];

        return $roles;
    }

    public function configureDashboard(): Dashboard
    {
    	// @$appName = !empty ( $_ENV['APP_NAME'] ) ? $_ENV['APP_NAME'] : 'Missing in env';
    	
        return Dashboard::new()
            ->setTitle('<img class="logo" src="'. $this -> logoPath . '" alt="HintHub"/>' );
        ;
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

    private function menuAll()
    {
        return 
        [
            $this -> lDashboard  (),
            
            $this -> mFehler        (),
            $this -> mUser          (),
            $this -> mKommentar     (),
            $this -> mModul         (),
            $this -> mSkript        (),
        ];
    }

    private function menuAdmin ()
    {
        return $this -> menuAll();
    }

    private function menuStudent ()
    {
        return 
        [
            $this -> lDashboard  (),
            
            $this -> mFehler     (),
        ];
    }

    private function menuTutor () 
    {
        return 
        [
            $this -> lDashboard  (),

            $this -> mFehler     (),
            $this -> mModul      (),
            $this -> mSkript     (),
        ];
    }

    private function menuExtern () 
    {
        return 
        [
            $this -> lDashboard  (),
            
            $this -> mModul      (),
            $this -> mSkript     (),
        ];
    }

    private function menuVerwaltung () 
    {
        return 
        [
            $this -> lDashboard  (),
            
            $this -> mUser       (),
            $this -> mModul      (),
            $this -> mSkript     (),
        ];
    }

    public function configureMenuItems(): iterable
    {
        $user = $this -> userService -> getCurrentUser ();
        
        // for testing use menuAll();

        if ( $user -> isAdmin () )
            return $this -> menuAdmin ();

        if ( $user -> isStudent () )
            return $this -> menuStudent    ();

        if ( $user -> isTutor() )
            return $this -> menuTutor      ();

        if ( $user -> isExtern () )
            return $this -> menuExtern     ();

        if ( $user -> isVerwaltung () )
            return $this -> menuVerwaltung ();

        // always return "something"
        return $this -> menuAll();
    }
}
