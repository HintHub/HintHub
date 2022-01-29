<?php

namespace App\Service;

use App\Entity\Fehler;
use App\Entity\Kommentar;

use Doctrine\ORM\EntityManager;
use App\Repository\FehlerRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Provides the Fehler Service
 *
 * @author Ali Kemal Yalama (ali-kemal.yalama@iubh.de)
 * @date 18.12.2021
 */
class FehlerService
{
    public FehlerRepository $fehlerRepository;
    public EntityManager    $entityManager;

    public function __construct ( FehlerRepository $fehlerRepository, EntityManagerInterface $entityManager ) 
    {
        $this -> entityManager      = $entityManager;
        $this -> fehlerRepository   = $fehlerRepository;
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
            -> setText                  ( $text."\n\n".$entity -> getKommentar ()       )
            -> setDatumLetzteAenderung  ( $dt          )
            -> setDatumErstellt         ( $dt          )
            -> setEinreicher            ( $currentUser );
            
            /*$kommentar1 = new Kommentar ( );
            $kommentar1 
            -> setFehler                    ( $entity                    )
            -> setText                      (  )
            -> setDatumLetzteAenderung      ( $dt                        )
            -> setDatumErstellt             ( $dt                        )
            -> setEinreicher                ( $currentUser               );

            $entity -> addKommentare ( $kommentar1 );*/
            $entity -> addKommentare ( $kommentar  );

            // set status opened
            $entity -> setStatus ( $statusChoicesValues [0] );
        }

        return $entity;
    }

    public static function getFehlerStatusTextByType ($type)
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

    public function save ( Fehler $fehler): Fehler
    {
        $this -> entityManager ->  persist ( $fehler );
        $this -> entityManager ->  flush   ();

        return $fehler;
    }

    public function update ( Fehler $fehler ): Fehler
    {
        $toUpdate   =   $this -> fehlerRepository -> find   ( $fehler -> getId () );

        $toUpdate   ->  setStatus                   ( $fehler    ->  getStatus               () );
        //$toUpdate ->  setEinreicher               ( $fehler    ->  getEinreicher           () );
        //$toUpdate ->  setModul                    ( $fehler    ->  getModul                () );
        $toUpdate   ->  setSeite                    ( $fehler    ->  getSeite                () );
        $toUpdate   ->  setStatus                   ( $fehler    ->  getStatus               () );
        //$toUpdate ->  setDatumErstellt            ( $fehler    ->  getDatumErstellt        () );
        $toUpdate   ->  setDatumLetzteAenderung     ( $fehler    ->  getDatumLetzteAenderung () );
        $toUpdate   ->  setDatumGeschlossen         ( $fehler    ->  getDatumGeschlossen     () );

        return $toUpdate;
    }

    public function delete ( int $id ): Fehler
    {
        $toDelete = $this -> findById   ( $id );
        $this -> entityManager -> remove ( $toDelete );
        
        return $toDelete -> getId ();
    }
}