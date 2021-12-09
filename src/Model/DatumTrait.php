<?php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Provides Entity Properties like "datumErstellt", "datumGeschlossen" etc.
 * 
 * @author ali-kemal.yalama (ali-kemal.yalama@iubh.de)
 */
trait DatumTrait
{

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
     * @return mixed
     */
    public function getDatumErstellt()
    {
        return $this->datumErstellt;
    }

    /**
     * @param mixed $datumErstellt
     */
    public function setDatumErstellt($datumErstellt): void
    {
        $this->datumErstellt = $datumErstellt;
    }

    /**
     * @return mixed
     */
    public function getDatumGeschlossen()
    {
        return $this->datumGeschlossen;
    }

    /**
     * @param mixed $datumGeschlossen
     */
    public function setDatumGeschlossen($datumGeschlossen): void
    {
        $this->datumGeschlossen = $datumGeschlossen;
    }

    /**
     * @return mixed
     */
    public function getDatumLetzteAenderung()
    {
        return $this->datumLetzteAenderung;
    }

    /**
     * @param mixed $datumLetzteAenderung
     */
    public function setDatumLetzteAenderung($datumLetzteAenderung): void
    {
        $this->datumLetzteAenderung = $datumLetzteAenderung;
    }

    /**
     * @return mixed
     */
    public function getEinreicher()
    {
        return $this->einreicher;
    }

    /**
     * @param mixed $einreicher
     */
    public function setEinreicher($einreicher): void
    {
        $this->einreicher = $einreicher;
    }

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="eingereichteFehler")
     * @ORM\JoinColumn(nullable=false)
     */
    private $einreicher;

    public function aktualisiereDatum($date) {
        $this->datumLetzteAenderung = $date;
    }
}
