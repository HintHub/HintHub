<?php

namespace App\Entity;

use App\Model\DatumTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BenachrichtigungRepository;

/**
 * @ORM\Entity(repositoryClass=BenachrichtigungRepository::class)
 */
class Benachrichtigung
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="benachrichtigungen")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Fehler::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $fehler;

    /**
     * @ORM\Column(type="boolean")
     */
    private $gelesen;

    use DatumTrait;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getGelesen(): ?bool
    {
        return $this->gelesen;
    }

    public function setGelesen(bool $gelesen): self
    {
        $this->gelesen = $gelesen;

        return $this;
    }
}
