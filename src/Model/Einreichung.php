<?php
namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

abstract class Einreichung {

    /**
     * @ORM\Column(type="datetime")
     */
    protected $datumErstellt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $datumGeschlossen;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $datumLetzteAenderung;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="eingereichteFehler")
     * @ORM\JoinColumn(nullable=false)
     */
    private $einreicher;

    public function aktualisiereDatum($date) {
        $this->datumLetzteAenderung = $date;
    }
}
