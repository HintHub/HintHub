<?php

namespace App\Controller;

use App\Service\EmailService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * HomepageController provides the Main route and renders the template
 * 
 * @author karim.saad ( karim.saad@iubh.de )
 */
class HomepageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index () : Response
    {
        return new RedirectResponse('/admin');
    }
}
