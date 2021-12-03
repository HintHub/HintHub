<?php

namespace App\Entity;

use App\Model\DatumTrait;
use App\Repository\FehlerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FehlerRepository::class)
 */
class Fehler
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('CLOSED', 'ESCALATED', 'OPEN', 'REJECTED', 'WAITING')")
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $seite;

    /**
     * @ORM\OneToMany(targetEntity=Kommentar::class, mappedBy="fehler", orphanRemoval=true)
     */
    private $kommentare;

    /**
     * @ORM\ManyToMany(targetEntity=Fehler::class)
     */
    private $verwandteFehler;

    /**
     * @ORM\ManyToOne(targetEntity=Modul::class, inversedBy="fehler")
     * @ORM\JoinColumn(nullable=false)
     */
    private $modul;

    use DatumTrait;

    public function __construct()
    {
        $this->kommentare = new ArrayCollection();
        $this->verwandteFehler = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSeite(): ?int
    {
        return $this->seite;
    }

    public function setSeite(int $seite): self
    {
        $this->seite = $seite;

        return $this;
    }

    /**
     * @return Collection|Kommentar[]
     */
    public function getKommentare(): Collection
    {
        return $this->kommentare;
    }

    public function addKommentare(Kommentar $kommentare): self
    {
        if (!$this->kommentare->contains($kommentare)) {
            $this->kommentare[] = $kommentare;
            $kommentare->setFehler($this);
        }

        return $this;
    }

    public function removeKommentare(Kommentar $kommentare): self
    {
        if ($this->kommentare->removeElement($kommentare)) {
            // set the owning side to null (unless already changed)
            if ($kommentare->getFehler() === $this) {
                $kommentare->setFehler(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getVerwandteFehler(): Collection
    {
        return $this->verwandteFehler;
    }

    public function addVerwandteFehler(self $verwandteFehler): self
    {
        if (!$this->verwandteFehler->contains($verwandteFehler)) {
            $this->verwandteFehler[] = $verwandteFehler;
        }

        return $this;
    }

    public function removeVerwandteFehler(self $verwandteFehler): self
    {
        $this->verwandteFehler->removeElement($verwandteFehler);

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

    public function open() {
        $this->setStatus('OPEN');
    }

    public function close() {
        $this->setStatus('CLOSED');
    }

    public function escalate() {
        $this->setStatus('ESCALATED');
    }

    public function wait() {
        $this->setStatus('WAITING');
    }

    public function __toString() {
        return $this->id."";
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
