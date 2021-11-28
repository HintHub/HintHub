<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Entity\User;

class AppFixtures extends Fixture
{
    private $pwHasher;
    public function __construct(UserPasswordHasherInterface $pwHasher){
    	$this->pwHasher = $pwHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
	$user = new User();
	
	$user->setEmail("test@test.de");
	
	// hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $this->pwHasher->hashPassword(
            $user,
            "test"
        );
        
	$user->setPassword($hashedPassword);
	$user->setRoles(["ROLE_ADMIN"]);
	
	
	
	$manager->persist($user);
        $manager->flush();
    }
}
