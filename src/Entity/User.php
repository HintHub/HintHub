<?php
// Generated by Symfony php bin/console make:entity (run by ali-kemal.yalama ( ali-kemal.yalama@iubh.de ) )

namespace App\Entity;

use App\Entity\Modul;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

// For password Hashing
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
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
     * @ORM\OneToMany(targetEntity=Fehler::class, mappedBy="einreicher", cascade={"persist", "remove"})
     */
    private $eingereichteFehler;

    /**
     * @ORM\OneToMany(targetEntity=Kommentar::class, mappedBy="einreicher", cascade={"persist", "remove"})
     */
    private $eingereichteKommentare;

    /**
     * @ORM\OneToMany(targetEntity=Modul::class, mappedBy="tutor", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $tutorIn;

    /**
     * @ORM\ManyToMany(targetEntity=Modul::class, inversedBy="studenten")
     */
    private $studentIn;

    /**
     * @ORM\OneToMany(targetEntity=Benachrichtigung::class, mappedBy="user")
     */
    private $benachrichtigungen;

    public function __construct ()  
    {
        $this -> isActive               = true;
        $this -> salt                   = hash                  ( 'sha512', uniqid ( null, true ) );
        $this -> eingereichteFehler     = new ArrayCollection   ();
        $this -> eingereichteKommentare = new ArrayCollection   ();
        $this -> tutorIn                = new ArrayCollection   ();
        $this -> studentIn              = new ArrayCollection   ();
        $this->benachrichtigungen = new ArrayCollection();
    }   

    public function setID ( int $i )
    {
        return true;
    }
    
    public function getId (): ?int
    {
        return $this -> id;
    }

    public function getEmail (): ?string
    {
        return $this -> email;
    }

    public function setEmail ( string $email ): self
    {
        $this -> email = $email;
        return $this;
    }

    public function getPassword (): ?string
    {
        return $this -> password;
    }

    public function setPassword ( string $password ): self
    {
        $this -> password = $password;
        return $this;
    }

    public function getROLES (): ?array
    {
        return $this -> ROLES;
    }

    public function setRolesString ( string $t )
    {
        $this -> ROLES = [$t]; // array($t);
        return $this;
    }

    public function getRolesString (): ?string
    {
        if (count($this->ROLES) > 0)return $this -> ROLES[0];
        return "";
        //return join(',', $this->ROLES);
    }

    public function setROLES ( ?array $ROLES ): self
    {
        $this -> ROLES = $ROLES;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSalt ()
    {
        return $this -> salt;
    }
 
    /**
     * @inheritDoc
     */
    public function eraseCredentials ()
    {
        // added for easy admin bundle
        $this -> plainPassword = null;
    }

    /**
     * @inheritDoc
     */
    public function equals ( UserInterface $user )
    {
        return $this -> id === $user -> getId     ();
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize ()
    {
        return serialize(
            [
                $this->id,
            ]
        );
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize ( $serialized )
    {
        list (
            $this -> id,
        ) = unserialize ( $serialized );
    }

    /**
     * @inheritDoc
     */
    public function getUsername ()
    {
        return $this -> getEmail ();
    }

    public function isVerified (): bool
    {
        return $this -> isVerified;
    }

    public function setIsVerified ( bool $isVerified ): self
    {
        $this -> isVerified = $isVerified;
        return $this;
    }

    public function getIsVerified () : bool
    {
        return $this -> isVerified;
    }


    public function __toString () : string
    {
        return $this -> email;
    }

    public function getUserIdentifier () : string
    {
        return $this -> email;
    }

    public function setIsActive ( bool $isActive )
    {
        return $this -> isActive = $isActive;
    }

    public function getIsActive () : bool
    {
        return $this -> isActive;
    }

    public function setSalt ( $salt )
    {
        $this -> salt = $salt;
        return $this;
    }

    // used internally in Symfony Admin Bundle
    public function setPlainPassword ( $pw )
    {
        return $this -> plainPassword = $pw;
    }

    // used internally in Symfony Admin Bundle
    public function getPlainPassword ()
    {
        return $this -> plainPassword;
    }

    /**
     * @return Collection|Fehler[]
     */
    public function getEingereichteFehler (): Collection
    {
        return $this -> eingereichteFehler;
    }

    public function addEingereichteFehler ( Fehler $eingereichteFehler ): self
    {
        if ( !$this -> eingereichteFehler -> contains ( $eingereichteFehler ) ) 
        {
            $this -> eingereichteFehler[] = $eingereichteFehler;
            $eingereichteFehler -> setEinreicher ( $this );
        }

        return $this;
    }

    public function removeEingereichteFehler ( Fehler $eingereichteFehler ): self
    {
        if ( $this -> eingereichteFehler -> removeElement ( $eingereichteFehler ) )
        {
            // set the owning side to null (unless already changed)
            if ( $eingereichteFehler -> getEinreicher () === $this )
            {
                $eingereichteFehler -> setEinreicher ( null );
            }
        }
        return $this;
    }

    /**
     * @return Collection|Kommentar[]
     */
    public function getEingereichteKommentare (): Collection
    {
        return $this -> eingereichteKommentare;
    }

    public function addEingereichteKommentare ( Kommentar $eingereichteKommentare ): self
    {
        if ( !$this -> eingereichteKommentare -> contains ( $eingereichteKommentare ) ) 
        {
            $this -> eingereichteKommentare[] = $eingereichteKommentare;
            $eingereichteKommentare -> setEinreicher ( $this );
        }
        return $this;
    }

    public function removeEingereichteKommentare ( Kommentar $eingereichteKommentare ): self
    {
        if ( $this -> eingereichteKommentare -> removeElement ( $eingereichteKommentare ) ) 
        {
            // set the owning side to null (unless already changed)
            if ( $eingereichteKommentare -> getEinreicher() === $this ) 
            {
                $eingereichteKommentare -> setEinreicher    ( null );
            }
        }

        return $this;
    }

    public function isUniPerson () 
    {
        return $this -> isTutor() || $this -> isStudent();
    }

    public function isTutor () 
    {
        return in_array ( "ROLE_TUTOR",      $this -> getROLES () );
    }

    public function isStudent () 
    {
        return in_array ( "ROLE_STUDENT",    $this -> getROLES () );
    }

    public function isAdmin ()
    {
        return in_array ( "ROLE_ADMIN",      $this -> getROLES () );
    }

    public function isExtern() 
    {
        return in_array ( "ROLE_EXTERN",     $this -> getROLES () );
    }

    public function isVerwaltung() 
    {
        return in_array ( "ROLE_VERWALTUNG", $this -> getROLES () );
    }

    public function setAdmin () 
    {
        $this -> setRole ( 'ROLE_ADMIN' );
    }

    public function setStudent () 
    {
        $this -> setRole ( 'ROLE_STUDENT' );
    }

    public function setTutor () 
    {
        $this -> setRole ( 'ROLE_TUTOR' );
    }

    public function setExtern () 
    {
        $this -> setRole ( 'ROLE_EXTERN' );
    }

    public function setVerwaltung () 
    {
        $this -> setRole ( 'ROLE_VERWALTUNG' );
    }

    private function setRole ( $role )
    {
        $this -> setROLES ( $role );
    }

    /**
     * @return Collection|Modul[]
     */
    public function getTutorIn(): Collection
    {
        return $this->tutorIn;
    }

    public function addTutorIn(Modul $tutorIn): self
    {
        if (!$this->tutorIn->contains($tutorIn)) {
            $this->tutorIn[] = $tutorIn;
            $tutorIn->setTutor($this);
        }

        return $this;
    }

    public function removeTutorIn(Modul $tutorIn): self
    {
        if ($this->tutorIn->removeElement($tutorIn)) {
            // set the owning side to null (unless already changed)
            if ($tutorIn->getTutor() === $this) {
                $tutorIn->setTutor(null);
            }
        }

        return $this;
    }

    public function getOnlyIdsFromTutorIn () 
    {
        return $this -> tutorIn -> map ( 
            function ( $obj ) 
            {
                return $obj -> getId ();
            }
        ) -> getValues();
    }

    /**
     * @return Collection|Modul[]
     */
    public function getStudentIn(): Collection
    {
        return $this->studentIn;
    }

    public function addStudentIn(Modul $studentIn): self
    {
        
        if (!$this->isStudent()) {
            return $this;
        }

        if (!$this->studentIn->contains($studentIn)) {
            $this->studentIn[] = $studentIn;
        }

        return $this;
    }

    public function removeStudentIn(Modul $studentIn): self
    {
        $this->studentIn->removeElement($studentIn);

        return $this;
    }

    public function getTutorAndStudentIn()
    {
        $tutorIn   = $this -> tutorIn   -> getValues ();
        $studentIn = $this -> studentIn -> getValues ();

        if ( count ( $tutorIn ) == 0 && count ( $studentIn ) == 0 )
            return null;

        $str1 = implode ( '<br/>', $tutorIn   );
        $str2 = implode ( '<br/>', $studentIn );

        $str4 = "<b>StudentIn:</b><br/>".$str2."<br/><br/><b>TutorIn:</b><br/>".$str2;
        return $str4;

    
    }

    /**
     * @return Collection|Benachrichtigung[]
     */
    public function getBenachrichtigungen(): Collection
    {
        return $this->benachrichtigungen;
    }

    public function addBenachrichtigungen(Benachrichtigung $benachrichtigungen): self
    {
        if (!$this->benachrichtigungen->contains($benachrichtigungen)) {
            $this->benachrichtigungen[] = $benachrichtigungen;
            $benachrichtigungen->setUser($this);
        }

        return $this;
    }

    public function removeBenachrichtigungen(Benachrichtigung $benachrichtigungen): self
    {
        if ($this->benachrichtigungen->removeElement($benachrichtigungen)) {
            // set the owning side to null (unless already changed)
            if ($benachrichtigungen->getUser() === $this) {
                $benachrichtigungen->setUser(null);
            }
        }

        return $this;
    }
}
