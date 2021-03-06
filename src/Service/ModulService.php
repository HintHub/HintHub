<?php
namespace App\Service;

use App\Entity\Modul;
use App\Repository\ModulRepository;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Provides the Modul Service
 *
 * @author Ali Kemal Yalama (ali-kemal.yalama@iubh.de)
 * @date 18.12.2021
 * 
 * lastEdit: 01.02.2022 0133 by karim.saad (karim.saad@iub.de) (code format fixing)
 */
class ModulService 
{

    private $modulRepository;
    private $entityManager;

    public function __construct ( 
        ModulRepository         $modulRepository, 
        EntityManagerInterface  $entityManager 
    )
    {
        $this -> modulRepository = $modulRepository;
        $this -> entityManager   = $entityManager;
    }

    public function findById ( int $id ): Modul
    {
        return $this -> modulRepository -> find ( $id );
    }

    public function findAll(): array 
    {
        return $this -> modulRepository -> findAll ();
    }

    public function save ( Modul $modul ): Modul
    {
        $this -> entityManager -> persist ( $modul );
        $this -> entityManager -> flush   ();

        return $modul;
    }

    public function update ( Modul $modul ): Modul 
    {
        $toUpdate  =   $this -> findById ( $modul ->  getId () );

        $toUpdate  ->  setSkript   ( $modul -> getSkript  () );
        $toUpdate  ->  setKuerzel  ( $modul -> getKuerzel () );
        $toUpdate  ->  setName     ( $modul -> getName    () );
        $toUpdate  ->  setTutor    ( $modul -> getTutor   () );

        return $toUpdate;
    }

    public function delete ( int $id ): int 
    {
        $toDelete = $this -> findById ( $id );
        $this ->  entityManager -> remove      ( $toDelete );
        $this ->  entityManager -> flush       ();

        return $toDelete -> getId   ();
    }
}