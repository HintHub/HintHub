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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="module")
     */
    private $tutor;

    public function __construct()
    {
        $this->skripte = new ArrayCollection();
        $this->tutor = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKuerze�l(): ?string
    {
        return $this->kuerze�l;
    }

    public function setKuerze�l(string $kuerze�l): self
    {
        $this->kuerze�l = $kuerze�l;

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

    /**
     * @return Collection|User[]
     */
    public function getTutor(): Collection
    {
        return $this->tutor;
    }

    public function addTutor(User $tutor): self
    {
        if (!$this->tutor->contains($tutor)) {
            $this->tutor[] = $tutor;
            $tutor->setModule($this);
        }

        return $this;
    }

    public function removeTutor(User $tutor): self
    {
        if ($this->tutor->removeElement($tutor)) {
            // set the owning side to null (unless already changed)
            if ($tutor->getModule() === $this) {
                $tutor->setModule(null);
            }
        }

        return $this;
    }
}
