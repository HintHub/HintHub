<?php
namespace App\Service;

use App\Entity\Kommentar;

use App\Repository\KommentarRepository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @author Karim Saad ( karim.saad@iubh.de )
 * @date 09.12.2021 0200
 * 
 * lastEdit: 01.02.2022 0133 by karim.saad (karim.saad@iub.de) (code format fixing)
 */
class KommentarService 
{

    private $kommentarRepository;
    private $entityManager;

    public function __construct (
        KommentarRepository     $kommentarRepository,
        EntityManagerInterface  $entityManager
    )
    {
        $this -> kommentarRepository = $kommentarRepository;
        $this -> entityManager       = $entityManager;
    }

    //findById
    public function findById ( int $id ): Kommentar 
    {
        return $this -> kommentarRepository -> find ( $id );
    }

    //findAll
    public function findAll (): array 
    {
        return $this -> kommentarRepository -> findAll ();
    }

    //save
    public function save ( Kommentar $kommentar ): Kommentar 
    {
        $this -> entityManager -> persist ( $kommentar );
        $this -> entityManager -> flush   ();
        return $kommentar;
    }

    // update
    public function update ( Kommentar $kommentar )
    {
        $toUpdate  = $this -> findById          ( $kommentar -> getId()                     );
        $toUpdate -> setText                    ( $kommentar -> getText()                   );
        $toUpdate -> setFehler                  ( $kommentar -> getFehler()                 );

        // Setting the DatumTrait Properties
        $toUpdate -> setDatumLetzteAenderung    ( $kommentar -> getDatumLetzteAenderung ()  );
        
        return $toUpdate;
    }

    //delete
    public function delete ( int $id ): int 
    {
        $toDelete = $this -> findById ( $id );
        
        $this -> entityManager -> remove    ( $toDelete );
        $this -> entityManager -> flush     ();
        
        return $id;
    }
}
