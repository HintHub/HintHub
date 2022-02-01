<?php

namespace App\Model;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Provides Entity property Einreicher
 * 
 * @author ali-kemal.yalama (ali-kemal.yalama@iubh.de)
 * @author karim.saad       (karim.saad@iubh.de)
 * 
 * Last Edit: 01.02.2022 (code formatting fix)
 */
trait EinreicherTrait
{
    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="eingereichteFehler", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
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
