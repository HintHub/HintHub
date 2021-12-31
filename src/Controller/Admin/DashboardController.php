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
    private UserService $userService;

    public function __construct ( UserService $userService ) 
    {
        $this -> userService = $userService;
    }
    
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('bundles/EasyAdminBundle/crud/DashboardController.html.twig');
        // return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
    	// @$appName = !empty ( $_ENV['APP_NAME'] ) ? $_ENV['APP_NAME'] : 'Missing in env';
    	
        return Dashboard::new()
            ->setTitle('<img class="logo" src="hinthub_logo.png" alt="HintHub"/>')
        ;
    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('admin.css');
    }

    public function configureMenuItems(): iterable
    {
        $user = $this -> userService -> getCurrentUser ();

        if ( $user -> isAdmin () )
        {
            return [
                MenuItem::linktoDashboard   ( 'Home', 'fa fa-home' ),
    
                MenuItem::linkToCrud ( 'Fehler Meldungen',      'fas fa-exclamation',   Fehler      ::class ),
                MenuItem::linkToCrud ( 'Benutzer',              'fas fa-users',         User        ::class ),
                MenuItem::linkToCrud ( 'Module',                'fas fa-layer-group',   Modul       ::class ),
                MenuItem::linkToCrud ( 'Skripte',               'fas fa-scroll',        Skript      ::class ),
                MenuItem::linkToCrud ( 'Kommentare',            'fas fa-comments',      Kommentar   ::class ),
            ];
        }
    }
}
