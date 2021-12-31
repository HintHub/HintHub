<?php

namespace App\DataFixtures;

// Doctrine
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

// Entities
use App\Entity\User;
use App\Entity\Modul;
use App\Entity\Fehler;
use App\Entity\Skript;

// Services
use App\Entity\Kommentar;
use App\Service\UserService;
use App\Service\ModulService;
use App\Service\FehlerService;
use App\Service\SkriptService;
use App\Service\KommentarService;

/**
 * Loads all the AppFixtures defined here
 * It's for dev testing purposes (filling it with most possible randomized or pre-usage data)
 * 
 * @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de  )
 * @author karim.saad       ( karim.saad@iubh.de        )
 */
class AppFixtures extends Fixture
{
    private $pwHasher;

    // services
    private $fehlerService;
    private $kommentarService;
    private $modulService;
    private $skriptService;
    private $userService;


    public function __construct (
        FehlerService               $fehlerService,
        KommentarService            $kommentarService,
        ModulService                $modulService,
        SkriptService               $skriptService,
        UserService                 $userService,
        
    )
    {
        // Services
        $this -> fehlerService      = $fehlerService;
        $this -> kommentarService   = $kommentarService;
        $this -> modulService       = $modulService;
        $this -> skriptService      = $skriptService;
        $this -> userService        = $userService;
    }
    
    public function load(ObjectManager $manager): void
    {
        // email, pw, role
        $admin   = $this -> addUser ( 'admin@hinthub.de',    'test',     'ROLE_ADMIN'   );
        $student = $this -> addUser ( 'student@hinthub.de',  'test',     'ROLE_STUDENT' );
        $tutor   = $this -> addUser ( 'tutor@hinthub.de',    'test',     'ROLE_TUTOR'   );
        $extern  = $this -> addUser ( 'extern@hinthub.de',   'test',     'ROLE_EXTERN'  );
    }
    
    public function addFehler ($name, $status, $seite, $kommentare, $verwandteFehler, $einreicher, $datum=null) 
    {
        $fehler  = new Fehler ();

        if ( $datum === null )
        {
            $datum = new \DateTime();
        }

        $fehler -> setName              ( $name             );
        $fehler -> setStatus            ( $status           );
        $fehler -> setSeite             ( $seite            );
        $fehler -> setKommentare        ( $kommentare       );
        $fehler -> setVerwandteFehler   ( $verwandteFehler  );
        $fehler -> setSkript            ( $skript           );

        $fehler -> setErstellDatum      ( $datum );

        $this -> fehlerService -> save ($fehler);
    }

    public function addKommentar ( $text, $fehler, $einreicher=null, $datum=null )
    {
        $kommentar  = new Kommentar ();

        if ( $datum === null )
        {
            $datum = new \DateTime();
        }

        $kommentar -> setText           ( $text       );
        $kommentar -> setFehler         ( $fehler     );
        $kommentar -> setEinreicher     ( $einreicher );
        $kommentar -> setDatumErstellt  ( $datum      );

        $this -> kommentarService -> save ($kommentar);
    }

    public function addModul ( $name, $kuerzel, $tutor, $studenten, $skript, $datum=null )
    {
        $modul = new Modul ();
    
        $modul -> setName       ( $name      );
        $modul -> setKuerzel    ( $kuerzel   );
        $modul -> setTutor      ( $tutor     );
        $modul -> setStudenten  ( $studenten );
        $modul -> setSkript     ( $skript    );

        $this -> modulService -> save ($modul);
    }

    public function addSkript ( $name, $version, $fehler, $modul )
    {
        $skript = new Skript ();

        $skript -> setName      ( $name     );
        $skript -> setVersion   ( $version  );
        $skript -> setFehler    ( $fehler   );
        $skript -> setModul     ( $modul    );

        $this -> skriptService -> save ( $skript );
    }

    public function addUser ( $email, $pw, $role=[], $isVerified=true, $isActive=true)
    {
        if ( gettype ( $role ) === "string" ) $role = [ $role ];

        $user = new User ();
        
        $user -> setEmail       ( $email  );
        $user -> setPassword    ( $this -> userService -> getHashedPW ( $user, $pw ) );
        $user -> setRoles       ( $role );

        $user -> setIsVerified  ( $isVerified );
        $user -> setIsActive    ( $isActive   );
        
        $this -> userService -> save ($user);

        return $user;
    }
    
}
