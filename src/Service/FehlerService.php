<?php

namespace App\Service;

use App\Entity\Fehler;
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

    public function save ( Fehler $fehler ): Fehler
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