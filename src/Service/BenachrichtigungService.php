<?php

namespace App\Service;

use App\Service\UserService;
use App\Service\EmailService;
use App\Entity\Benachrichtigung;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use App\Controller\Admin\FehlerCrudController;
use App\Repository\BenachrichtigungRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

/**

 */
class BenachrichtigungService
{ 

    private $entityManager;

    private $benachrichtigungRepository;

    private $userService;

    public function __construct (
        BenachrichtigungRepository      $benachrichtigungRepository,
        EntityManagerInterface          $entityManager,
        UserService                     $userService,
        EmailService                    $emailService,
        AdminUrlGenerator               $adminUrlGenerator
    ) 
    {
        $this -> entityManager              = $entityManager;
        $this -> benachrichtigungRepository = $benachrichtigungRepository;
        $this -> userService                = $userService;
        $this -> emailService               = $emailService;
        $this -> adminUrlGenerator          = $adminUrlGenerator;
    }

    public function findById( int $id ): Benachrichtigung 
    {
        return $this -> benachrichtigungRepository -> find ($id);
    }

    public function findAll(): array 
    {
        return $this -> benachrichtigungRepository -> findAll ();
    }

    public function save ( Benachrichtigung $benachrichtigung ): Benachrichtigung 
    {
        $this -> entityManager -> persist ($benachrichtigung);
        $this -> entityManager -> flush   ();

        return $benachrichtigung;
    }

    public function update ( Benachrichtigung $benachrichtigung ): Benachrichtigung 
    {
        $toUpdate = $this -> findById ( $benachrichtigung -> getId () );

        $toUpdate = $this -> setText ( $benachrichtigung -> getText() );

        return $toUpdate;
    }

    public function delete ( int $id ): int 
    {
        $toDelete = $this -> findById ( $id );
        
        $this -> entityManager  -> remove ( $toDelete );
        $this -> entityManager  -> flush ();

        return $toDelete -> getId ();
    }

    //creates the "same" notification twice - once for student once for tutor
    public function fireBenachrichtigungen ( $fehler, $text, $flush=true ) 
    {
        $einreicher = $fehler -> getEinreicher ();
        $tutor      = $fehler -> getSkript () -> getModul () -> getTutor ();

        $a = $this -> saveBenachrichtigung ( $fehler, $text, $einreicher, $flush );
        $b = $this -> saveBenachrichtigung ( $fehler, $text, $tutor     , $flush );

        $eMailEinreicherAddress = $einreicher  -> getEmail ();
        $eMailTutorAddress      = $tutor       -> getEmail ();

        $currentUser    = $this -> userService -> getCurrentUser();

        $sysMailAddress = EmailService::$systemEmail;
        $title          = "'$currentUser' hat '$fehler' geändert";

        $linkUrl        = $this -> generateFehlerDetailUrl ( $fehler );
        $linkText       = "$fehler";

        $data = [ 
            "realMessage" => true,
            "title"       => $title,
            "text"        => $text,
            "linkUrl"     => $linkUrl,
            "linkText"    => $linkText
        ];
        
        $t2 = $title . " (An '$eMailEinreicherAddress')";
        $email1 = $this -> emailService -> sendMail ( $eMailEinreicherAddress,  $sysMailAddress, $t2, $data );

        $t2 = $title . " (An '$eMailTutorAddress')";
        $email2 = $this -> emailService -> sendMail ( $eMailTutorAddress,       $sysMailAddress, $t2, $data );

        return [
            $a,
            $b
        ];
    }

    private function saveBenachrichtigung ( $fehler, $text, $user, $flush=true ) 
    {
        $dt = new \DateTime ();
        $benachrichtigung = new Benachrichtigung ();
        
        $benachrichtigung -> setText                  ( $text   );
        $benachrichtigung -> setUser                  ( $user   ) ;
        $benachrichtigung -> setFehler                ( $fehler );
        $benachrichtigung -> setDatumErstellt         ( $dt     );
        $benachrichtigung -> setDatumLetzteAenderung  ( $dt     );
        $benachrichtigung -> setGelesen               ( false   );

        if ( $flush )
        {
            //persist on Flush!
            $unitOfWork     = $this -> entityManager -> getUnitOfWork   ();
            $this -> entityManager  ->  persist( $benachrichtigung );
            $classMetadata  = $this -> entityManager -> getClassMetadata ( Benachrichtigung::class );
            $unitOfWork     -> computeChangeSet( $classMetadata, $benachrichtigung );
        }
        else
        {
            $worked = [
                $this -> entityManager -> persist   ( $benachrichtigung ),
                $this -> entityManager -> flush     ()
            ];
        }
        
        return $benachrichtigung;
    }

    public function getCountUnreadBenachrichtigungen ()
    {
        $currentUser    = $this -> userService -> getCurrentUser ();

        $isAdmin        = $currentUser -> isAdmin       ();
        $isExtern       = $currentUser -> isExtern      ();
        $isVerwaltung   = $currentUser -> isVerwaltung  ();

        $notNeeded      = $currentUser === null; //$currentUser === null || $isAdmin || $isExtern || $isVerwaltung;

        if ( $notNeeded )
            return 0;

        $userId       = $currentUser -> getId ();

        $countUnreadBenachrichtigungen = $this -> entityManager 
            -> createQueryBuilder       ()
            -> select                   ( 'COUNT(b.id)'                           ) 
            -> from                     ( Benachrichtigung::class,          'b'   )
            -> where                    ( 'b.user IN (:userId) AND b.gelesen = 0' )
            -> setParameter             ( 'userId', $userId                       )
            -> getQuery                 ()
            -> useQueryCache            ( true )
            -> getSingleScalarResult    ();
        
        return $countUnreadBenachrichtigungen;
    }


    /**
     * Sets the Read Flag of b to true
     */
    public function markRead ( $bId )
    {
        if ($bId === null || $bId < 1)
            return false;
        
        try 
        {
            $b = $this -> benachrichtigungRepository -> find ( $bId );
            
            if ( $b === null )
                return false;

            $b -> setGelesen (true);
            $this -> entityManager -> flush   ();

            return true;
        } 
        catch ( Exception $e )
        {
            dd($e);
            return false;
        }
    }


    public function generateFehlerDetailUrl ( $fehler )
    {
        if (!$fehler)
            return "";
        
        $id = $fehler -> getId();

        return $this
        -> adminUrlGenerator
        -> setController ( FehlerCrudController::class   )
        -> setAction     ( Action::DETAIL                )
        -> setEntityId   ( $id                           )
        -> generateUrl   ( );
    }
}