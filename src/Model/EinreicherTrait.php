<?php

namespace App\Model;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Provides Entity property Einreicher
 * 
 * @author ali-kemal.yalama (ali-kemal.yalama@iubh.de)
 * @author_edit karim.saad  (karim.saad@iubh.de)
 */
trait EinreicherTrait
{
    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="eingereichteFehler", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $einreicher;
    
    /**
     * Gets the Einreicher of the Entity
     * @return User einreicher
     */
    public function getEinreicher() : User
    {
        return $this -> einreicher;
    }

    /**
     * Sets the Einreicher of the Entity
     * @param User $einreicher
     */
    public function setEinreicher (User $einreicher)
    {
        $this -> einreicher = $einreicher;
        return $this;
    }
}
