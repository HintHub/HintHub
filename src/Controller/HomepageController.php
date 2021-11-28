<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * HomepageController manages the index page
 * 
 * since project start
 * @author Karim S // SAAD-IT
 **/
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
