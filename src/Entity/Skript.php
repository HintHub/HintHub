<?php
/*
    Generated by Symfony php bin/console make:entity 
    @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de )

    Last edit by karim.saad (karim.saad@iubh.de) 01.02.22 0102
*/
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
     * @ORM\OneToOne(targetEntity=Modul::class, inversedBy="skript", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $modul;

    private $platzhalter = false;

    public function __construct ()
    {
        $this -> fehler      = new ArrayCollection ();
        $this -> platzhalter = ( $this -> name == 'Platzhalter' ); 
    }

    public function isPlatzhalter()
    {
        $this -> platzhalter = ( $this -> name == 'Platzhalter' ); 
        return $this -> platzhalter;
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
            // Edit by KS 19.01.22 (Test Fixing II); add a blank script if removed  (last edit code formatting fix 01.02.22)
            if ( $fehler -> getSkript () === $this ) 
            {
                $skript = new Skript ();

                $skript -> setName      ("Platzhalter");
                $fehler -> setSkript    ( $skript );

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

    public function getModul (): ?Modul
    {
        return $this->modul;
    }

    public function setModul ( ?Modul $modul ): self
    {
        $this->modul = $modul;

        return $this;
    }
}
