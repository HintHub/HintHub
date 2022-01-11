<?php
namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManager;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
 
/**
 * Provides the User Service
 *  
 * @author Ali Kemal Yalama (ali-kemal.yalama@iubh.de)
 * @date 03.12.2021
 */
class UserService 
{
    private UserPasswordHasherInterface $pwHasher;
    private UserRepository              $userRepository;
    private                             $entityManager;
    private                             $tokenStorage;

    public function __construct ( 
        UserPasswordHasherInterface $pwHasher, 
        UserRepository              $userRepository, 
        EntityManagerInterface      $entityManager,
        TokenStorageInterface       $tokenStorage 
    )
    {
        $this   ->  pwHasher        = $pwHasher;

        $this   ->  userRepository  = $userRepository;
        $this   ->  entityManager   = $entityManager;
        $this   ->  tokenStorage    = $tokenStorage;
    }

    //findById
    public function findById(int $id): User 
    {
        return $this -> userRepository -> find ( $id );
    }

    //findAll
    public function findAll (): array
    {
        return $this -> userRepository -> findAll ();
    }

    //save
    public function save ( User $user ): User 
    {
        $this -> entityManager -> persist ( $user );
        $this -> entityManager -> flush   ();

        return $user;
    }

    //update
    public function update ( User $user )
    {
        $toUpdate = $this -> findById ( $user -> getId ( ) );

        $toUpdate   ->  setROLES        ($user  ->  getROLES        () );
        $toUpdate   ->  setEmail        (); 
        $toUpdate   ->  setIsActive     ($user  ->  getIsActive     () );
        $toUpdate   ->  setIsVerified   ($user  ->  getIsVerified   () );

        return $toUpdate;
    }

    //delete
    public function delete ( int $id ): int
    {
        $toDelete = $this -> findById ($id);

        $this -> entityManager -> remove      ( $toDelete );
        $this -> entityManager -> flush       ();

        return $id;
    }

    /**
     * Gets the current User via tokenStorage object
     * @return User currentUser
     */
    public function getCurrentUser ()
    {
        if ( $this -> tokenStorage === null                               ) return null;
        if ( $this -> tokenStorage -> getToken () === null                ) return null;
        if ( $this -> tokenStorage -> getToken () -> getUser () === null  ) return null;

        return $this -> tokenStorage -> getToken () -> getUser ();
    }

    /**
     * Get Roles (centralized)
     */
    public function getRoles () 
    {
        return [
            'Admin'      => 'ROLE_ADMIN',
            'Student'    => 'ROLE_STUDENT',
            'Tutor'      => 'ROLE_TUTOR',
            'Extern'     => 'ROLE_EXTERN',
            'Verwaltung' => 'ROLE_VERWALTUNG'
        ];
    }

    /**
     * Gets the Hashed PW
     */
    public function getHashedPW ($user, $pw)
    {
        return $this -> pwHasher -> hashPassword
        (
            $user,
            "test"
        );
    }

    //ich probier mal was

    public function getStudenten() 
    {
        return $this 
            -> userRepository 
            -> getAllByRole ( 'ROLE_STUDENT' );
    }

    public function getTutoren() 
    {
        return $this 
            -> userRepository 
            -> getAllByRole ( 'ROLE_TUTOR' );
    }
}
