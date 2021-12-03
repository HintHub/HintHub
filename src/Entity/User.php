<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;


// For password Hashing
use Symfony\Component\PasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Security\Core\Encoder\PasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
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
    private $email;

    /**
     * @ORM\Column(type="text")
     */
    private $password;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $ROLES = [];

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $salt;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * internally used, not db
     * @var string
     * */
    private $plainPassword = "";

    /**
     * @ORM\OneToMany(targetEntity=Fehler::class, mappedBy="einreicher")
     */
    private $eingereichteFehler;

    /**
     * @ORM\OneToMany(targetEntity=Kommentar::class, mappedBy="einreicher")
     */
    private $eingereichteKommentare;


    /**
     * @ORM\OneToMany(targetEntity=Modul::class, mappedBy="tutor", orphanRemoval=true)
     */
    private $module;

    public function __construct()  {
        $this->isActive = true;
        $this->salt = hash('sha512', uniqid(null, true));
        $this->eingereichteFehler = new ArrayCollection();
        $this->eingereichteKommentare = new ArrayCollection();
    }

    public function setID(int $i) {
        return true;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getROLES(): ?array
    {
        return $this->ROLES;
    }

    public function setRolesString(array $t){
        $this->ROLES = $t; // array($t);
        return $this;
    }

    public function getRolesString(): ?array
    {
        return $this->ROLES;
        //return join(',', $this->ROLES);
    }

    public function setROLES(?array $ROLES): self
    {
        $this->ROLES = $ROLES;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSalt(){
        return $this->salt;
    }
 
    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        // added for easy admin bundle
        $this->plainPassword = null;
    }

    /**
     * @inheritDoc
     */
    public function equals(UserInterface $user)
    {
        return $this->id === $user->getId();
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
        ) = unserialize($serialized);
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
    public function getIsVerified() : bool {
        return $this->isVerified;
    }


    public function __toString() : string {
        return $this->email;
    }

    public function getUserIdentifier() : string {
        return $this->email;
    }

    public function setIsActive(bool $isActive) {
        return $this->isActive = $isActive;
    }

    public function getIsActive() : bool {
        return $this->isActive;
    }

    public function setSalt($salt) {
        $this->salt = $salt;
        return $this;
    }

    // used internally in Symfony Admin Bundle
    public function setPlainPassword($pw) {
        return $this->plainPassword = $pw;
    }

    // used internally in Symfony Admin Bundle
    public function getPlainPassword() {
        return $this->plainPassword;
    }

    /**
     * @return Collection|Fehler[]
     */
    public function getEingereichteFehler(): Collection
    {
        return $this->eingereichteFehler;
    }

    public function addEingereichteFehler(Fehler $eingereichteFehler): self
    {
        if (!$this->eingereichteFehler->contains($eingereichteFehler)) {
            $this->eingereichteFehler[] = $eingereichteFehler;
            $eingereichteFehler->setEinreicher($this);
        }

        return $this;
    }

    public function removeEingereichteFehler(Fehler $eingereichteFehler): self
    {
        if ($this->eingereichteFehler->removeElement($eingereichteFehler)) {
            // set the owning side to null (unless already changed)
            if ($eingereichteFehler->getEinreicher() === $this) {
                $eingereichteFehler->setEinreicher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Kommentar[]
     */
    public function getEingereichteKommentare(): Collection
    {
        return $this->eingereichteKommentare;
    }

    public function addEingereichteKommentare(Kommentar $eingereichteKommentare): self
    {
        if (!$this->eingereichteKommentare->contains($eingereichteKommentare)) {
            $this->eingereichteKommentare[] = $eingereichteKommentare;
            $eingereichteKommentare->setEinreicher($this);
        }

        return $this;
    }

    public function removeEingereichteKommentare(Kommentar $eingereichteKommentare): self
    {
        if ($this->eingereichteKommentare->removeElement($eingereichteKommentare)) {
            // set the owning side to null (unless already changed)
            if ($eingereichteKommentare->getEinreicher() === $this) {
                $eingereichteKommentare->setEinreicher(null);
            }
        }

        return $this;
    }

    public function isUniPerson() {
        return !$this->isAdmin() && ($this->isTutor() || $this->isStudent());
    }

    public function isTutor() {
        return in_array("Tutor", $this->getROLES());
    }

    public function isStudent() {
        return in_array("Student", $this->getROLES());
    }

    public function isAdmin() {
        return in_array("Admin", $this->getROLES());
    }

    public function setAdmin() {
        $this->setRole('Admin');
    }

    public function setStudent() {
        $this->setRole('Student');
    }

    public function setTutor() {
        $this->setRole('Tutor');
    }

    private function setRole($role) {
        $this->setROLES($role);
    }

    public function addModule(Modul $modul): self
    {
        if (!$this->module->contains($modul)) {
            $this->module[] = $modul;
            $modul->setTutor($this);
        }

        return $this;
    }

    public function removeModule(Modul $modul): self
    {
        if ($this->module->removeElement($modul)) {
            // set the owning side to null (unless already changed)
            if ($modul->getTutor() === $this) {
                $modul->setTutor(null);
            }
        }

        return $this;
    }
}
