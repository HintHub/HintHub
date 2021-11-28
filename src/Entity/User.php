<?php

namespace App\Entity;

use App\Repository\UserRepository;
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


    public function __construct()  {
        $this->isActive = true;
        $this->salt = hash('sha512', uniqid(null, true));
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

    // used internally in Symfony Admin Bundle
    public function setPlainPassword($pw) {
        return $this->plainPassword = $pw;
    }

    // used internally in Symfony Admin Bundle
    public function getPlainPassword() {
        return $this->plainPassword;
    }
}
