<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Modul;
use App\Entity\Fehler;
use App\Entity\Skript;
use App\Entity\Kommentar;

use App\Service\UserService;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Monolog\Logger;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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
    private $cssFile     = 'admin.css';
    private $logoPath    = 'hinthub_logo.png';

    // Twig Templates
    private $controllerTwigLocation = 'bundles/EasyAdminBundle/crud/DashboardController.html.twig';
    private $profileTwigLocation    = 'bundles/EasyAdminBundle/crud/profile/profile.html.twig';

    // services
    private UserService $userService;

    private Logger $logger;

    // constructor
    public function __construct ( LoggerInterface $loggerI, UserService $userService ) 
    {
        $this -> logger      = $loggerI;
        $this -> userService = $userService;
    }
    
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this -> render ( $this -> controllerTwigLocation );
        // return parent::index();
    }
    
    /**
     * @Route("/profile", name="profile")
     * 
     * Author: Stefan Baltschun (stefan.baltschun@iubh.de)
     * Date: 05.01.2022
     * TODO: schick machen
     */
    public function profile ( Request $request  ): Response
    {
        $user = $this -> userService -> getCurrentUser ();

        if ( $user === null )
            throw new \Exception ( "Nicht eingeloggt" );

        $userForm = $this -> createFormBuilder ( $user )
            -> add  ( 'email',          TextType     ::class )
            -> add  ( 'plainPassword',  PasswordType ::class )
            -> add  ( 'save',           SubmitType   ::class )
            -> getForm();


        $userForm -> handleRequest ( $request );
        if ( $userForm -> isSubmitted () && $userForm -> isValid () ) 
        {
                // Get Form Data
                $newUser = $userForm -> getData();

                // PW hashing
                $newPW    =  $newUser -> getPlainPassword ();
                $hashedPW =  $this -> userService -> getHashedPW ( $newUser, $newPW );
                $newUser  = $newUser -> setPassword ( $hashedPW );

                // Update
                $newUser = $this -> userService -> update ($newUser);
                $success = ( $newUser !== null && $newUser -> getId() > 0 ) ? true : false;

                // Rendering
                return $this -> renderIt ( $newUser, $userForm, $success );
        }
    

        return $this -> renderIt ( $user, $userForm );
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


    public function configureUserMenu(UserInterface $user): UserMenu
    {
        // Usually it's better to call the parent method because that gives you a
        // user menu with some menu items already created ("sign out", "exit impersonation", etc.)
        // if you prefer to create the user menu from scratch, use: return UserMenu::new()->...
        return parent::configureUserMenu($user)
            // use the given $user object to get the user name
            // ->setName($user->getEmail())
            // // use this method if you don't want to display the name of the user
            // ->displayUserName(false)

            // // you can return an URL with the avatar image
            // ->setAvatarUrl('https://...')
            // ->setAvatarUrl($user->getProfileImageUrl())
            // // use this method if you don't want to display the user image
            // ->displayUserAvatar(false)
            // // you can also pass an email address to use gravatar's service
            // ->setGravatarEmail($user->getMainEmailAddress())

            // you can use any type of menu item, except submenus
            -> addMenuItems([
                MenuItem::linkToRoute('Profil bearbeiten', 'fa fa-id-card', 'profile', []),
                // MenuItem::section(),
                // MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
            ]);
    }


    private function renderIt ($user, $userForm, $success=null)
    {
        $ar = [];

        if ( $success !== null )
        {
            $templateVars = [
                'success'         => $success,
                'userForm'        => $userForm,
                'user'            => $user
            ];
        } 
        else
        {
            $templateVars = [
                'userForm'        => $userForm,
                'user'            => $user
            ];
        } 

        return $this -> renderForm (
            $this -> profileTwigLocation, 
            $templateVars
        );
    }
}
