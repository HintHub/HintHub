<?php
namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Provides the User Service
 *  
 * @author Ali Kemal Yalama (ali-kemal.yalama@iubh.de)
 * @date 03.12.2021
 */
class UserService 
{

    private UserRepository $userRepository;
    private EntityManager $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    //findById
    public function findById(int $id): User {
        return $this->userRepository->find($id);
    }

    //findAll
    public function findAll(): array {
        return $this->userRepository->findAll();
    }

    //save
    public function save(User $user): User {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    //update
    public function update(User $user) {
        $toUpdate = $this->findById($user->getId());
        $toUpdate->setROLES($user->getROLES());
        $toUpdate->setSalt();
        $toUpdate->setEmail();
        $toUpdate->setIsActive($user->getIsActive());
        $toUpdate->setIsVerified($user->getIsVerified());
        return $toUpdate;
    }

    //delete
    public function delete(int $id): int {
        $toDelete = $this->findById($id);
        $this->entityManager->remove($toDelete);
        $this->entityManager->flush();
        return $id;
    }
}
