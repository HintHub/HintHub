<?php
namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

abstract class Einreichung {

    /**
     * @ORM\Column(type="datetime")
     */
    protected $datumErstellt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $datumGeschlossen;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $datumLetzteAenderung;

    public function aktualisiereDatum($date) {
        $this->datumLetzteAenderung = $date;
    }
}
