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

    //needed for associations
    private $tutoren;
    private $studenten;
    private $fehler;
    private $module;
    private $skripte;


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

        
        $createdUsers = $this -> flatten          ( $this -> createUser       ());
        $module       = $this -> createModule     ( $data );
        $skripte      = $this -> createSkripte    ( $data, $module );
        $fehler       = $this -> createFehler     ( $createdUsers, $module, $skripte );

        $this->module    =     $module;
        $this->skripte   =    $skripte;
        $this->fehler    = $fehler;
        //$kommentare   = $this -> createKommentare ();

        //associations

        $this->assignTutoren();
        $this->assignStudenten();
        $this->assignFehler();
    }

    private function flatten(array $array) {
        $return = array();
        array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
        return $return;
    }

    //main assign methods

    private function assignTutoren() {
        foreach ($this->module as &$modul) {
            $modul->setTutor($this->getRandomTutor());
            //$this->modulService->update($modul);
            $this->modulService->save($modul);
        }
    }

    private function assignStudenten() {
        foreach ($this->module as &$modul) {
            $studentenArr = $this->getRandomStudenten();

            foreach($studentenArr as &$student) {
                $modul->addStudenten($student);
            }

            //$this->modulService->update($modul);
            $this->modulService->save($modul);
        }
    }

    private function assignFehler() {
        foreach ($this->fehler as &$fehler) {
            for($i = 0; $i < 5; $i++) {
                $fehler->addVerwandteFehler($this->getRandomFehler());
            }
            
            $this->fehlerService->save($fehler);
        }
    }


    //main rand methods for student, tutor and fehler
    private function getRandomTutor(): User {
        $len = count($this->tutoren);
        $index = rand(0, $len-1);
        return $this->tutoren[$index];
    }

    private function getRandomStudenten(): array {
        $studenten = [];
        for($i = 0; $i < 5; $i++) 
        {
            array_push($studenten, $this->getRandomStudent());
        }
        return $studenten;
    }

    private function getRandomStudent(): User {
        $len = count($this->studenten);
        $index = rand(0, $len-1);
        return $this->studenten[$index];
    }

    private function getRandomFehler(): Fehler {
        $len = count($this->fehler);
        $index = rand(0, $len-1);
        return $this->fehler[$index];
    }

    //rand methods end

    public function createUser ()
    {
        // email, pw, role
        /*$admin      = $this -> addUser  ( 'admin@hinthub.de',      'test',     'ROLE_ADMIN'      );
        $student    = $this -> addUser  ( 'student@hinthub.de',    'test',     'ROLE_STUDENT'    );
        $tutor      = $this -> addUser  ( 'tutor@hinthub.de',      'test',     'ROLE_TUTOR'      );
        
        $extern     = $this -> addUser  ( 'extern@hinthub.de',     'test',     'ROLE_EXTERN'     );
        $verwaltung = $this -> addUser  ( 'verwaltung@hinthub.de', 'test',     'ROLE_VERWALTUNG' );*/

        $tutoren        =   $this->createTutoren        ();
        $studenten      =   $this->createStudenten      ();
        $externe        =   $this->createExterne        ();
        $verwaltungen   =   $this->createVerwaltungen   ();
        $admins         =   $this->createAdmins         ();

        $this   ->  tutoren      =   $tutoren    ;
        $this   ->  studenten    =   $studenten  ;

        return [ $admins, $studenten, $tutoren, $externe, $verwaltungen ];
    }

    private function createStudenten()    :   array 
    {
        $studenten  =   [];
        $student    =   $this -> addUser  ( 'student@hinthub.de',    'test',     'ROLE_STUDENT'    );
        array_push      (   $studenten, $student    );
        for (   $i = 0;     $i < 10;    $i++   ) 
        {
            $student = $this -> addUser  ( sprintf('student%d@hinthub.de', $i),      'test',     'ROLE_STUDENT'      );
            array_push  (   $studenten, $student    );
        }
        return $studenten;
    }

    private function createTutoren()    :   array 
    {   
        $tutoren    =   [];
        $tutor      =   $this -> addUser  ( 'tutor@hinthub.de',      'test',     'ROLE_TUTOR'      );
        array_push      ( $tutoren, $tutor    );
        for (   $i = 0;     $i < 10;    $i++    ) 
        {
            $tutor = $this -> addUser  ( sprintf('tutor%d@hinthub.de', $i),      'test',     'ROLE_TUTOR'      );
            array_push  (   $tutoren, $tutor    );
        }
        return $tutoren;
    }

    private function createExterne()    :   array 
    {
        $externe   = [];
        $extern    = $this -> addUser  ( 'extern@hinthub.de',    'test',     'ROLE_EXTERN'    );
        array_push  (   $externe, $extern   );
        for (   $i = 0;     $i < 10;    $i++    ) 
        {
            $extern = $this -> addUser  ( sprintf('extern%d@hinthub.de', $i),      'test',     'ROLE_EXTERN'      );
            array_push  (   $externe, $extern   );
        }
        return $externe;
    }

    private function createVerwaltungen()    :   array 
    {
        $verwaltungen      = [];
        $verwaltung    = $this -> addUser  ( 'verwaltung@hinthub.de',    'test',     'ROLE_VERWALTUNG'    );
        array_push      (   $verwaltungen, $verwaltung  );
        for (   $i = 0;     $i < 10;    $i++    ) 
        {
            $extern = $this -> addUser  ( sprintf('verwaltung%d@hinthub.de', $i),      'test',     'ROLE_VERWALTUNG'      );
            array_push  (   $verwaltungen, $verwaltung  );
        }
        return $verwaltungen;
    }

    private function createAdmins()    :   array 
    {
        $admins  = [];
        $admin    = $this -> addUser  ( 'admin@hinthub.de',    'test',     'ROLE_ADMIN'    );
        array_push      ( $admins, $admin );
        for (   $i = 0;     $i < 10;    $i++    ) 
        {
            $extern = $this -> addUser  ( sprintf('admin%d@hinthub.de', $i),      'test',     'ROLE_ADMIN'      );
            array_push  ( $admins, $admin );
        }
        return $admins;
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

        $this   ->  skripte  = $skripte;
        $this   ->  module   = $module;
        
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

    public function createFehler ( $user, $module, $skripte )
    {
        if ( count($user) == 0 )
            throw new \Exception ( "No User given" );

        if ( count($module) == 0 )
            throw new \Exception ( "No Modules given" );
        
        if ( count($skripte) == 0 )
            throw new \Exception ( "No Skripte given" );


        $fehlerAr = [];

        for ( $i=0; $i < 1000; $i++ )
        {
            $seite               = rand ( 0, 250 );
            $name                = $this -> getRandomText (40); // 40 Words
            $statusChoicesValues = array_values ( $this -> fehlerService -> getStatusChoices () ); 
            $status              = rand ( 0, count ( $statusChoicesValues )-1 );
            
            $randomModul         = $module  [ rand ( 0, count ( $module  )-1 ) ];
            $randomSkript        = $skripte [ rand ( 0, count ( $skripte )-1 ) ];
            $randomUser          = $user    [ rand ( 0, count ( $user    )-1 ) ];

            $initKommentar = $this->getRandomText ( 150 );
            
            $fehler = $this -> addFehler ( $name, $status, $seite, $initKommentar, $kommentare=null, $verwandteFehler=null, $skript=$randomSkript, $einreicher=$randomUser, $datum=new \DateTime() );
            array_push($fehlerAr, $fehler);
        }

        return $fehlerAr;
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
            //$skript -> setModul ($modul);

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
