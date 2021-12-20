<?php

namespace App\Service;

use App\Entity\Skript;
use App\Repository\SkriptRepository;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Provides the Skript Service
 *
 * @author Ali Kemal Yalama (ali-kemal.yalama@iubh.de)
 * @date 18.12.2021
 */
class SkriptService {

    private SkriptRepository $skriptRepository;
    private EntityManager    $entityManager;

    public function __construct ( SkriptRepository $skriptRepository, EntityManagerInterface $entityManager )
    {
        $this -> skriptRepository   = $skriptRepository;
        $this -> entityManager      = $entityManager;
    }

    public function findById ( int $id ): Skript
    {
        return $this -> skriptRepository -> find    ( $id );
    }

    public function findAll (): array 
    {
        return $this -> skriptRepository -> findAll ();
    }

    public function save ( Skript $skript ): Skript
    {
        $this -> entityManager -> persist ( $skript );
        $this -> entityManager -> flush   ();

        return $skript;
    }

    public function update ( Skript $skript ): Skript 
    {
        $toUpdate = $this -> skriptRepository -> find ( $skript -> getId () );
        $toUpdate -> setVersion  ( $toUpdate -> getVersion () );

        return      $toUpdate;
    }

    public function delete ( int $id ): int 
    {
        $toDelete = $this -> findById ( $id );
        $this -> entityManager -> remove    ( $toDelete );
        $this -> entityManager -> flush     ();
        return $id;
    }
}