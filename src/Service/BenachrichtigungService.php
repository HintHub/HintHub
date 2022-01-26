<?php

namespace App\Service;

use App\Entity\Benachrichtigung;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use App\Repository\BenachrichtigungRepository;

/**

 */
class BenachrichtigungService
{ 

    private $entityManager;

    private $benachrichtigungRepository;

    public function __construct(BenachrichtigungRepository $benachrichtigungRepository, EntityManagerInterface $entityManager) 
    {
        $this -> entityManager              = $entityManager;
        $this -> benachrichtigungRepository = $benachrichtigungRepository;
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
        $this -> entityManager -> remove ($toDelete);
        $this -> entityManager -> flush ();

        return $toDelete -> getId ();
    }

    //creates the "same" notification twice - once for student once for tutor
    public function fireBenachrichtigungen ( $fehler, $text ) 
    {
        $einreicher = $fehler->getEinreicher();

        $tutor = $fehler->getSkript()->getModul()->getTutor();

        $this -> saveBenachrichtigung ( $fehler, $text, $einreicher );
        $this -> saveBenachrichtigung ( $fehler, $text, $tutor );
    }

    private function saveBenachrichtigung($fehler, $text, $user) 
    {
        $dt = new \DateTime();
        $benachrichtigung = new Benachrichtigung();
        $benachrichtigung -> setText ( $text );
        $benachrichtigung -> setUser( $user) ;
        $benachrichtigung -> setFehler( $fehler );
        $benachrichtigung -> setDatumErstellt ($dt);
        $benachrichtigung -> setDatumLetzteAenderung ($dt);

        //persist on Flush!

        $unitOfWork     = $this -> entityManager -> getUnitOfWork   ();

        $this -> entityManager  ->  persist( $benachrichtigung );

        $benachrichtigungClass = get_class( $benachrichtigung );
        
        $classMetadata  = $this -> entityManager -> getClassMetadata ( $benachrichtigungClass );
        
        $unitOfWork     -> computeChangeSet( $classMetadata, $benachrichtigung );

    }
}