<?php

namespace App\Controller\Admin;

use App\Entity\Fehler;
use App\Entity\Kommentar;
use App\Entity\Modul;
use App\Entity\Skript;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
    	@$appName = !empty($_ENV['APP_NAME']) ? $_ENV['APP_NAME'] : 'Missing in env';
    	
        return Dashboard::new()
            ->setTitle('Www');
    }

    public function configureMenuItems(): iterable
    {
        return [
         MenuItem::linktoDashboard('Dashboard', 'fa fa-home'),
         MenuItem::linkToCrud('User Crud Controller', 'fas fa-list', User::class),
            MenuItem::linkToCrud('Modul Crud Controller', 'fas fa-list', Modul::class),
            MenuItem::linkToCrud('Skript Crud Controller', 'fas fa-list', Skript::class),
            MenuItem::linkToCrud('Fehler Crud Controller', 'fas fa-list', Fehler::class),
            MenuItem::linkToCrud('Kommentar Crud Controller', 'fas fa-list', Kommentar::class),
        ];
    }
}
