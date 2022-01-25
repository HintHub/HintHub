<?php

namespace App\Controller\Tests;

use App\Service\EmailService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * TestEmailController Tests E-Mail Delivery by route /testmail
 * 
 * @author karim.saad ( karim.saad@iubh.de )
 */
class TestEmailController extends AbstractController
{
    #[Route('/testmail', name: 'testmail')]
    public function testMail ( EmailService $emailService ) : Response
    {
        if ( $this -> getParameter('app.app_env') != 'dev' )
            throw new \Exception ( "this is not a dev env" );
        
        $to    = "testto@test.de";
        $from  = "testfrom@test.de";
        $title = "test";
        $data  = [];

        $mail = $emailService -> sendMail ( $to, $from, $title, $data, $template="email/default.html.twig" );
        
        return new JsonResponse ( $mail );
    }
}
