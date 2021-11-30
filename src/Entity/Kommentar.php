<?php

namespace App\Entity;

use App\Model\Einreichung;
use App\Repository\KommentarRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=KommentarRepository::class)
 */
class Kommentar extends Einreichung
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity=Fehler::class, inversedBy="kommentare")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fehler;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getFehler(): ?Fehler
    {
        return $this->fehler;
    }

    public function setFehler(?Fehler $fehler): self
    {
        $this->fehler = $fehler;

        return $this;
    }

    public function getEinreicher(): ?User
    {
        return $this->einreicher;
    }

    public function setEinreicher(?User $einreicher): self
    {
        $this->einreicher = $einreicher;

        return $this;
    }
}
