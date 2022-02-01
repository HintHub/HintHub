<?php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Provides Entity Properties like "datumErstellt", "datumGeschlossen" etc.
 * 
 * @author ali-kemal.yalama (ali-kemal.yalama@iubh.de)
 * 
 * Last Edit: 01.02.2022 (code formatting fix)
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
     * Gets the created date
     * @return DateTime
     */
    public function getDatumErstellt () : \DateTime
    {
        return $this -> datumErstellt;
    }

    /**
     * Sets the created date
     * @param mixed $datumErstellt
     * @return Entity
     */
    public function setDatumErstellt ( $datumErstellt )
    {
        $this -> datumErstellt = $datumErstellt;
        return $this;
    }

    /**
     * Gets the closed date
     * @return DateTime datumGeschlossen
     */
    public function getDatumGeschlossen () : \DateTime
    {
        return $this->datumGeschlossen;
    }

    /**
     * Sets the closed date
     * @param DateTime $datumGeschlossen
     * @return mixed Entity
     */
    public function setDatumGeschlossen ( \DateTime $datumGeschlossen )
    {
        $this -> datumGeschlossen = $datumGeschlossen;
        return $this;
    }

    /**
     * Gets the last changed date
     * @return DateTime datumLetzteAenderung
     */
    public function getDatumLetzteAenderung() : \DateTime
    {
        return $this -> datumLetzteAenderung;
    }

    /**
     * Sets the last changed date
     * @param DateTime $datumLetzteAenderung
     * @return mixed Entity
     */
    public function setDatumLetzteAenderung ( \DateTime $datumLetzteAenderung )
    {
        $this -> datumLetzteAenderung = $datumLetzteAenderung;
        return $this;
    }

    /**
     * updates the last changed date
     * @return mixed Entity
     */
    public function aktualisiereDatum ( \DateTime $date )
    {
        $this -> datumLetzteAenderung = $date;
        return $this;
    }
}
