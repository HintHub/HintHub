<?php

namespace App\Entity;

use App\Repository\ModulRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ModulRepository::class)
 */
class Modul
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $kuerzel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Skript::class, mappedBy="modul", orphanRemoval=true)
     */
    private $skripte;

    /**
     * @ORM\OneToOne(targetEntity=Skript::class, cascade={"persist", "remove"})
     */
    private $aktuellesSkript;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="Module")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tutor;

    /**
     * @ORM\OneToMany(targetEntity=Fehler::class, mappedBy="modul", orphanRemoval=true)
     */
    private $fehler;

    public function __construct()
    {
        $this->skripte = new ArrayCollection();
        $this->fehler = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKuerzel(): ?string
    {
        return $this->kuerzel;
    }

    public function setKuerzel(string $kuerzel): self
    {
        $this->kuerzel = $kuerzel;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Skript[]
     */
    public function getSkripte(): Collection
    {
        return $this->skripte;
    }

    public function addSkripte(Skript $skripte): self
    {
        if (!$this->skripte->contains($skripte)) {
            $this->skripte[] = $skripte;
            $skripte->setModul($this);
        }

        return $this;
    }

    public function removeSkripte(Skript $skripte): self
    {
        if ($this->skripte->removeElement($skripte)) {
            // set the owning side to null (unless already changed)
            if ($skripte->getModul() === $this) {
                $skripte->setModul(null);
            }
        }

        return $this;
    }

    public function getAktuellesSkript(): ?Skript
    {
        return $this->aktuellesSkript;
    }

    public function setAktuellesSkript(?Skript $aktuellesSkript): self
    {
        $this->aktuellesSkript = $aktuellesSkript;

        return $this;
    }

    public function getTutor(): ?User
    {
        return $this->tutor;
    }

    public function setTutor(?User $tutor): self
    {
        $this->tutor = $tutor;

        return $this;
    }

    public function __toString() {
        return $this->id."";
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
            $fehler->setModul($this);
        }

        return $this;
    }

    public function removeFehler(Fehler $fehler): self
    {
        if ($this->fehler->removeElement($fehler)) {
            // set the owning side to null (unless already changed)
            if ($fehler->getModul() === $this) {
                $fehler->setModul(null);
            }
        }

        return $this;
    }
}
