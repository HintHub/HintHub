<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * HomepageController provides the Main route and renders the template
 * 
 * @author karim.saad ( karim.saad@iubh.de )
 */
class HomepageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        @$appName = $_ENV['APP_NAME'];
        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
            'APP_NAME' => $appName,
        ]);
    }
}
