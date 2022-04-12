<?php

namespace App\Controller;

use App\Entity\Kommentar;
use App\Service\UserService;
use App\Service\FehlerService;
use App\Service\BenachrichtigungService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller 4 Benachrichtigungen
 * 
 * @author ali-kemal.yalama (ali-kemal.yalama@iubh.de)
 * @author karim.saad       (karim.saad@iubh.de)
 */
class BenachrichtigungController extends AbstractController
{
    #[Route('benachrichtigung/read', name: 'benachrichtigung_read')]
    public function markBenachrichtigungRead ( Request $request, BenachrichtigungService $benachrichtigungService, UserService $userService, FehlerService $fehlerService ): Response
    {
        $parameters  = json_decode ( $request -> getContent (), true );
        $currentUser = $userService -> getCurrentUser ();

        /*
            TODO: add configured permissions
            $notAllowed   = $currentUser -> isStudent () || $currentUser -> isVerwaltung () || $currentUser -> isExtern ();

            if  ( $notAllowed )
                return new JsonResponse ( [ "status" => "failed", "message" => "no permission!" ], 500 );
        */
        
        $token        = $parameters [ 'token'    ];
        $bId          = $parameters [ 'bId'      ];

        $isValidToken = $this -> isCsrfTokenValid ( 'benachrichtigungMarkRead', $token );
        $userValid    = ! ( $currentUser   === null || empty ( $currentUser ) );
        $bIdValid     = ! ( $bId === null || empty ( $bId ) );

        if ( ! $isValidToken ) 
            return new JsonResponse ( [ "status" => "failed", "message" => "token wrong!"     ], 500 );
        
        if ( ! $userValid )
            return new JsonResponse ( [ "status" => "failed", "message" => "User not set!"     ], 500 );

        if ( ! $bIdValid )
            return new JsonResponse ( [ "status" => "failed", "message" => "bId not set!"      ], 500 );

        if ( $benachrichtigungService -> markRead ( $bId ) )
            return new JsonResponse ( [ "status" => "ok" ], 200 );

        return new JsonResponse ( [ "status" => "failed", "message" => "saving failed!" ], 500 );
    }
}