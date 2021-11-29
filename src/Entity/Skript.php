<?php

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
     * @ORM\ManyToOne(targetEntity=Modul::class, inversedBy="skripte")
     * @ORM\JoinColumn(nullable=false)
     */
    private $modul;

    /**
     * @ORM\OneToMany(targetEntity=Fehler::class, mappedBy="skript", orphanRemoval=true)
     */
    private $fehler;

    public function __construct()
    {
        $this->fehler = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(int $version): self
    {
        $this->version = $version;

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

    /**
     * @return Collection|Fehler[]
     */
    public function getFehler(): Collection
    {
        return $this->fehler;
    }

    public function addFehler(Fehler $fehler): self
    {
        if (!$this->fehler->contains($fehler)) {
            $this->fehler[] = $fehler;
            $fehler->setSkript($this);
        }

        return $this;
    }

    public function removeFehler(Fehler $fehler): self
    {
        if ($this->fehler->removeElement($fehler)) {
            // set the owning side to null (unless already changed)
            if ($fehler->getSkript() === $this) {
                $fehler->setSkript(null);
            }
        }

        return $this;
    }
}
