<?php

namespace App\Service;

use App\Entity\Fehler;
use App\Entity\Kommentar;

use App\Repository\FehlerRepository;

use App\Service\BenachrichtigungService;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Provides the Fehler Service
 *
 * @author Ali Kemal Yalama (ali-kemal.yalama@iubh.de)
 * @date 18.12.2021
 * 
 * lastEdit: 01.02.2022 0133 by karim.saad (karim.saad@iub.de) (code format fixing)
 */
class FehlerService
{
    private FehlerRepository        $fehlerRepository;
    private EntityManager           $entityManager;
    private BenachrichtigungService $benachrichtigungService;

    public function __construct ( 
        FehlerRepository        $fehlerRepository,
        EntityManagerInterface  $entityManager,
        BenachrichtigungService $benachrichtigungService) 
    {
        $this -> entityManager              = $entityManager;
        $this -> fehlerRepository           = $fehlerRepository;
        $this -> benachrichtigungService    = $benachrichtigungService;
    }

    public function findById ( int $id ): Fehler 
    {
        return $this -> fehlerRepository -> find ( $id );
    }

    public function findAll (): array
    {
        return $this -> fehlerRepository -> findAll ();
    }

    public function openWithKommentar ( $entity, $currentUser ) 
    {
        if ( $currentUser -> isStudent () || $currentUser -> isTutor () )
        {
            $statusChoicesValues = array_values ( $this -> getStatusChoices () );

            $dt   = new \DateTime ();
            $text = "User (ID:". $currentUser -> getId () . ") hat eine Fehlermeldung erstellt";
            
            $kommentar  = new Kommentar ( );

            $kommentar
            -> setFehler                ( $entity      )
            -> setText                  ( $text."\n\n".$entity -> getKommentar () )
            -> setDatumLetzteAenderung  ( $dt          )
            -> setDatumErstellt         ( $dt          )
            -> setEinreicher            ( $currentUser );

            $entity -> addKommentare ( $kommentar );

            // set status opened
            $entity -> setStatus ( $statusChoicesValues [0] );
        }

        return $entity;
    }

    public static function getFehlerStatusTextByType ( $type )
    {
        return [
            'OPEN'          =>  'offen',
            'CLOSED'        =>  'geschlossen',
            'REJECTED'      =>  'abgelehnt',
            'ESCALATED'     =>  'eskaliert',
            'WAITING'       =>  'wartend'
        ][$type];
    }

    public function getStatusChoices ( $user = null )
    {
        if ( $user === null )
        {
            // full
            return 
            [
                'offen'         =>  'OPEN',
                'geschlossen'   =>  'CLOSED',
                'abgelehnt'     =>  'REJECTED',
                'eskaliert'     =>  'ESCALATED',
                'wartend'       =>  'WAITING'
            ];
        }

        // when user is set
        
        if ( $user -> isAdmin () )
        {
            return
            [
                'offen'         =>  'OPEN',
                'geschlossen'   =>  'CLOSED',
                'abgelehnt'     =>  'REJECTED',
                'eskaliert'     =>  'ESCALATED',
                'wartend'       =>  'WAITING'
            ];
        }

        if ( $user -> isStudent () )
        {
            return 
            [
                'offen'         =>  'OPEN',
                'geschlossen'   =>  'CLOSED'
            ];
        }

        if ( $user -> isTutor () )
        {
            return 
            [
                'offen'         =>  'OPEN',
                'geschlossen'   =>  'CLOSED',
                'abgelehnt'     =>  'REJECTED',
                'eskaliert'     =>  'ESCALATED',
                'wartend'       =>  'WAITING'
            ];
        }

        if ( $user -> isExtern () )
        {
            //TODO FehlerCrudController getStatusChoices isExtern
            return [
                'geschlossen'   =>  'CLOSED',
                'abgelehnt'     =>  'REJECTED',
                'eskaliert'     =>  'ESCALATED',
                'wartend'       =>  'WAITING'
            ];
        }
    }

    public function save ( Fehler $fehler ): Fehler
    {
        $this -> entityManager ->  persist ( $fehler );
        $this -> entityManager ->  flush   ();

        return $fehler;
    }

    public function update ( Fehler $fehler ): Fehler
    {
        $toUpdate   =   $this -> fehlerRepository -> find   ( $fehler -> getId () );

        $toUpdate   ->  setStatus                   ( $fehler    ->  getStatus               ()  );
        $toUpdate   ->  setSeite                    ( $fehler    ->  getSeite                ()  );
        $toUpdate   ->  setStatus                   ( $fehler    ->  getStatus               ()  );
        $toUpdate   ->  setDatumLetzteAenderung     ( $fehler    ->  getDatumLetzteAenderung ()  );

        $toUpdate    -> setUnbearbeitetTage          ( $this -> loadUnbearbeitetTage ( $toUpdate ) -> getUnbearbeitetTage () );

        return $toUpdate;
    }

    public function delete ( int $id ): Fehler
    {
        $toDelete = $this -> findById   ( $id );
        $this -> entityManager -> remove ( $toDelete );
        
        return $toDelete -> getId ();
    }

    public function escalateFehler () 
    {
        $toEscalate = $this -> fehlerRepository -> getAllFehlerForEscalation ();
        
        foreach ( $toEscalate as $fehler ) 
        {   
            $fId = $fehler -> getId ();
            //echo "[+] updating $fId";
            $fehler -> setSystemUpdate ( true );
            $fehler -> escalate ();
            $this   -> update ( $fehler );
            $this -> entityManager -> flush ();
            //echo "[i] update complete";
        }
    }

    public function loadUnbearbeitetTage ( $entity )
    {
        $tage = $this -> fehlerRepository -> getUnbearbeitetTage ( $entity -> getId () );
        $entity -> setUnbearbeitetTage ( $tage );
        return $entity;
    }
}