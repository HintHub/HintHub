<?php

namespace App\Controller;

use App\Entity\Kommentar;
use App\Service\UserService;
use App\Service\FehlerService;
use App\Service\KommentarService;
use App\Service\BenachrichtigungService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class KommentarController extends AbstractController
{
    #[Route('kommentar/add', name: 'kommentar_add')]
    public function add ( 
        
        Request                 $request,
        KommentarService        $kommentarService,
        BenachrichtigungService $benachrichtigungService,
        UserService             $userService,
        FehlerService           $fehlerService 

    ): Response
    {
        $parameters = json_decode ( $request -> getContent (), true );
        
        $currentUser = $userService -> getCurrentUser ();

        if  (  $currentUser === null 
                || $currentUser -> isAdmin      () 
                || $currentUser -> isVerwaltung () 
                || $currentUser -> isExtern     () 
            ) 
        {
            return new JsonResponse ( [ "status" => "failed", "message" => "no permission!" ], 500 );
        }

        $token       = $parameters [ 'token'    ];
        $fehlerId    = $parameters [ 'fehlerId' ];
        $text        = $parameters [ 'text'     ];

        if ( ! $this -> isCsrfTokenValid ( 'addKommentar', $token ) ) 
        {
            return new JsonResponse ( [ "status" => "failed", "message" => "token wrong!"     ], 500 );
        }

        if ( $currentUser   === null || empty ( $currentUser ) ) return new JsonResponse ( [ "status" => "failed", "message" => "User not set!"     ], 500 );
        if ( $fehlerId      === null || empty ( $fehlerId    ) ) return new JsonResponse ( [ "status" => "failed", "message" => "fehlerId not set!" ], 500 );
        if ( $text          === null || empty ( $text        ) ) return new JsonResponse ( [ "status" => "failed", "message" => "text not set!"     ], 500 );

        $fehler = $fehlerService -> findById ( $fehlerId );
        if ( $fehler === null ) return new JsonResponse ( [ "status" => "failed", "message" => "Fehler object is null" ], 500 );

        $dt = new \DateTime();
        $kommentar = new Kommentar ();
        $kommentar -> setFehler               ( $fehler      );
        $kommentar -> setText                 ( $text        );
        $kommentar -> setEinreicher           ( $currentUser );
        $kommentar -> setDatumErstellt        ( $dt          );
        $kommentar -> setDatumLetzteAenderung ( $dt          );

        if ( $kommentarService -> save ( $kommentar ) )
        {
            $c = $benachrichtigungService -> fireBenachrichtigungen ( $fehler, "$currentUser hat einen Kommentar hinterlassen.", false );
            
            return new JsonResponse ( [ "status" => "ok" ], 200 );
        }

        return new JsonResponse ( [ "status" => "failed", "message" => "saving failed!" ], 500 );
    }
}