<?php
// Generated by Symfony php bin/console make:entity (run by ali-kemal.yalama ( ali-kemal.yalama@iubh.de ) )

namespace App\Entity;

use App\Model\DatumTrait;
use App\Model\EinreicherTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\KommentarRepository;
/**
 * @ORM\Entity(repositoryClass=KommentarRepository::class)
 */
class Kommentar
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

    use DatumTrait;
    use EinreicherTrait;

    public function __toString ()
    {
        $id = $this -> getId ();
        return "$id";
    }

    public function getId (): ?int
    {
        return $this -> id;
    }

    public function getText (): ?string
    {
        return $this -> text;
    }

    public function setText ( string $text ): self
    {
        $this -> text = $text;
        return $this;
    }

    public function getFehler (): ?Fehler
    {
        return $this -> fehler;
    }

    public function setFehler ( ?Fehler $fehler ): self
    {
        $this -> fehler = $fehler;
        return $this;
    }
}
