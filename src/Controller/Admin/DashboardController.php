<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Modul;
use App\Entity\Fehler;
use App\Entity\Skript;
use App\Entity\Kommentar;

use App\Service\UserService;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
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

    // services
    private UserService $userService;

    // constructor
    public function __construct ( UserService $userService ) 
    {
        $this -> userService = $userService;
    }
    
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        // test
        return $this -> render ( $this -> controllerTwigLocation );
        // return parent::index();
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
