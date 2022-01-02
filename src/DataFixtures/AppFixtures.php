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
    
    public function load ( ObjectManager $manager ): void
    {
        $data = $this -> loadTestModuleSkriptTestData ();

        $createdUsers = $this -> createUser       ();
        $module       = $this -> createModule     ( $data );
        $skripte      = $this -> createSkripte    ( $data, $module );
        // $kommentare   = $this -> createKommentare ();
        // $fehler       = $this -> createFehler     ();
    }

    public function createUser ()
    {
        // email, pw, role
        $admin      = $this -> addUser  ( 'admin@hinthub.de',      'test',     'ROLE_ADMIN'      );
        $student    = $this -> addUser  ( 'student@hinthub.de',    'test',     'ROLE_STUDENT'    );
        $tutor      = $this -> addUser  ( 'tutor@hinthub.de',      'test',     'ROLE_TUTOR'      );
        $extern     = $this -> addUser  ( 'extern@hinthub.de',     'test',     'ROLE_EXTERN'     );
        $verwaltung = $this -> addUser  ( 'verwaltung@hinthub.de', 'test',     'ROLE_VERWALTUNG' );

        return [ $admin, $student, $tutor, $extern, $verwaltung ];
    }
    
    public function createModule ( $data )
    {
        $module = [];

        for ( $i=0; $i < count ( $data )-1; $i++ )
        {
            if ( $i == 0 ) continue;
            
            $row     = $data [$i];
            $name    = $row  [0];
            $kuerzel = $row  [1];

            $version = \rand ( 0, 10 );

            $modul = $this -> addModul ( "$name", $kuerzel, $tutor=null, $studenten=null, $skript=null );

            array_push( $module, $modul );
        }

        return $module;
    }

    public function createSkripte ( $data, $module )
    {
        $skripte = [];

        for ( $i=0; $i < count ( $module ); $i++ )
        {
            if ($i == 0) continue;
            $row     = $data [ $i ];
            $kuerzel = $row  [ 1 ];
            $version = \rand ( 0, 10 );
            
            $skript  = $this -> addSkript ( "$kuerzel Skript", $version, $fehler=null, $modul=$module[$i-1] );
            
            array_push ( $skripte, $skript );
        }
        
        return $skripte;
    }

    public function createKommentare ()
    {
        for ( $i=0; $i < 4; $i++ )
        {
            $text = $this -> getRandomText ( 40 ); // 40 Words
            //$this ->  addKommentar ( $text, $fehler=null, $einreicher=null, $datum=null );
        }
    }

    public function createFehler ()
    {
        for ( $i=0; $i < 4; $i++ )
        {
            $seite               = rand ( 0, 250 );
            $text                = $this -> getRandomText (4); // 40 Words
            $statusChoicesValues = array_values ( $this -> fehlerService -> getStatusChoices () ); 
            $status              = rand ( 0, count ( $statusChoicesValues )-1 );
            
            $this -> addFehler ( $text, $status, $seite, null, $kommentare=null, $verwandteFehler=null, $skript=null, $einreicher=null, $datum=null );
        }
    }

    public function addFehler ( $name, $status, $seite, $initKommentar=null, $kommentare=null, $verwandteFehler=null, $skript=null, $einreicher=null, $datum=null ) 
    {
        $fehler  = new Fehler ();

        if ( $datum === null )
            $datum = new \DateTime();

        $fehler -> setName              ( $name   );
        $fehler -> setStatus            ( $status );
        $fehler -> setSeite             ( $seite  );

        if ( $kommentare !== null )
            $fehler -> addKommentare        ( $kommentare      );
        
        if ( $verwandteFehler !== null )
            $fehler -> setVerwandteFehler   ( $verwandteFehler );

        if ( $skript !== null )
            $fehler -> setSkript            ( $skript          );

        if ( $einreicher !== null )
            $fehler -> setEinreicher ( $einreicher );

        if ( $einreicher !== null && $initKommentar !== null )
        {
            $fehler -> setKommentar ( $initKommentar );
            $this -> fehlerService -> openWithKommentar ( $fehler, $einreicher );
        }
        
        $fehler -> setDatumErstellt        ( $datum );
        $fehler -> setDatumLetzteAenderung ( $datum );

        $this -> fehlerService -> save ($fehler);
        return $fehler;
    }

    public function addKommentar ( $text, $fehler=null, $einreicher=null, $datum=null )
    {
        $kommentar  = new Kommentar ();

        if ( $datum === null )
            $datum = new \DateTime();

        $kommentar -> setText           ( $text       );

        if ( $fehler !== null )
            $kommentar -> setFehler         ( $fehler     );

        if ( $einreicher !== null)
            $kommentar -> setEinreicher     ( $einreicher );

        $kommentar -> setDatumErstellt        ( $datum );
        $kommentar -> setDatumLetzteAenderung ( $datum );
        
        $this -> kommentarService -> save ($kommentar);
        return $kommentar;
    }

    public function addModul ( $name, $kuerzel, $tutor=null, $studenten=null, $skript=null, $datum=null )
    {
        $modul = new Modul ();
    
        $modul -> setName       ( $name      );
        $modul -> setKuerzel    ( $kuerzel   );
        
        if ( $tutor !== null )
            $modul -> setTutor      ( $tutor );
        
        if ( $studenten !== null )
            $modul -> addStudenten  ( $studenten );

        if ( $modul !== null)
            $modul -> setSkript     ( $skript );

        $this -> modulService -> save ($modul);
        return $modul;
    }

    public function addSkript ( $name, $version, $fehler=null, $modul=null )
    {
        $skript = new Skript ();

        $skript -> setName      ( $name     );
        $skript -> setVersion   ( $version  );

        if ( $fehler !== null )
            $skript -> setFehler    ( $fehler );

        if ( $modul !== null )
            $skript -> setModul     ( $modul );

        $this -> skriptService -> save ( $skript );
        
        return $skript;
    }

    public function addUser ( $email, $pw, $role=[], $isVerified=true, $isActive=true )
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
    

    private function getRandomText ( $wordCount=10 )
    {
        $loremIpsum = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.';
        $spl  = explode ( ' ', $loremIpsum );
        $cSpl = count   ( $spl )-1;
        $newSt = "";
        
        for ( $i=0; $i < $wordCount; $i++ )
        {
            $rNumber = rand ( 0, $cSpl );
            $newSt .= $loremIpsum [ $rNumber ];
        }

        return $newSt;
    }

    private function loadTestModuleSkriptTestData ()
    {
        $fname   = "testmodule_skript.txt";
        $newData = [];

        $data = $this -> loadTestData ( $fname );

        if ( count ( $data ) == 0 )
            throw new \Exception ( "'$fname' has no data " );

        $head = explode ( ',', $data[0] );

        array_push ( $newData, $head );

        for ( $i = 0; $i < count ( $data ); $i++ )
        {
            $row = $data [$i];

            $spl = explode ( ',', $row );
            if ( count ( $spl ) !== 2 || $i==0 )
                continue;
            
            array_push ( $newData, $spl );
        }
        return $newData;
    }

    private function loadTestData ( $fname )
    {
        $dir      = dirname(__FILE__).'/testData/'; 
        $fullpath = $dir.$fname;

        if ( ! file_exists ( $fullpath ) )
            throw new \Exception ("'$fullpath' existiert nicht!");
        
        // dirname(__FILE__).'/testData/testmodule_skript.txt'
        $csv = file_get_contents ($fullpath);
        $newData = [];
        $data = explode(PHP_EOL,$csv);
        return $data;
    }
}
