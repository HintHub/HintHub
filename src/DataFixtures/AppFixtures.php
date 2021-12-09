<?php

namespace App\DataFixtures;

use App\Repository\FehlerRepository;
use App\Repository\KommentarRepository;
use App\Repository\ModulRepository;
use App\Repository\SkriptRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Entity\User;

/**
 * Loads all the AppFixtures defined here
 * It's for dev testing purposes (filling it with most possible randomized data)
 * 
 * @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de  )
 * @author karim.saad       ( karim.saad@iubh.de        )
 */
class AppFixtures extends Fixture
{
    private $pwHasher;
    private $userRepository;
    private $fehlerRepository;
    private $kommentarRepository;
    private $modulRepository;
    private $skriptRepository;


    public function __construct(UserPasswordHasherInterface $pwHasher, UserRepository $userRepository,
                                FehlerRepository $fehlerRepository, KommentarRepository $kommentarRepository,
                                ModulRepository $modulRepository, SkriptRepository $skriptRepository)
    {
    	$this->pwHasher = $pwHasher;
        $this->fehlerRepository = $fehlerRepository;
        $this->userRepository = $userRepository;
        $this->kommentarRepository = $kommentarRepository;
        $this->modulRepository = $modulRepository;
        $this->skriptRepository = $skriptRepository;
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

    private function mockData(): void {
        //TODO TEST ENTITIES

        //ID 2 User

        //Id 1 modul

        //Id 1,2 Skript

        //skript loeschen

        //ID neu 2 Skript

        //Fehler ID1

        //Kommentare 1-3

        //Fehler bearbeiten

        //Fehler loeschen

        //ID 1 modul loeschen
    }
}
