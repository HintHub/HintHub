<?php
// Generated by Symfony php bin/console make:entity 
// @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de )

namespace App\Entity;

use App\Repository\SkriptRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SkriptRepository::class)
 */
class Skript
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @ORM\OneToMany(targetEntity=Fehler::class, mappedBy="skript", orphanRemoval=true)
     */
    private $fehler;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity=Modul::class, inversedBy="skript", cascade={"persist", "remove"})
     */
    private $modul;

    public function __construct ()
    {
        $this -> fehler = new ArrayCollection();
    }


    public function __toString() 
    {
        $id     = $this -> getId    ();
        $name   = $this -> getName  ();
        return $name . ' ('. $id. ')';
    }

    public function getId (): ?int
    {
        return $this -> id;
    }

    public function getVersion (): ?int
    {
        return $this -> version;
    }

    public function setVersion ( int $version ): self
    {
        $this -> version = $version;

        return $this;
    }

    /**
     * @return Collection|Fehler[]
     */
    public function getFehler (): Collection
    {
        return $this -> fehler;
    }

    public function addFehler ( Fehler $fehler ): self
    {
        if ( ! $this -> fehler -> contains ( $fehler ) )
        {
            $this   -> fehler[] = $fehler;
            $fehler -> setSkript  ( $this );
        }

        return $this;
    }

    public function removeFehler ( Fehler $fehler ): self
    {
        if ( $this -> fehler -> removeElement ( $fehler ) )
        {
            // set the owning side to null (unless already changed)
            if ( $fehler -> getSkript () === $this ) 
            {
                $fehler -> setSkript    ( null );
            }
        }

        return $this;
    }

    public function getName (): ?string
    {
        return $this -> name;
    }

    public function setName ( ?string $name ): self
    {
        $this -> name = $name;
        return $this;
    }

    public function getModul(): ?Modul
    {
        return $this->modul;
    }

    public function setModul(?Modul $modul): self
    {
        $this->modul = $modul;

        return $this;
    }
}
