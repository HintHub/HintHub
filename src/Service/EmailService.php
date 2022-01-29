<?php

namespace App\Service;

use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

/**
 * Provides the System with Sending E-Mail possibilities
 *
 * @author karim.saad (karim.saad@iubh.de)
 * @date 24.01.2022
 * 
 * you need to insert MAILER_DSN=smtp://user:pass@smtp.example.com:port in .env.local
 */
class EmailService
{
    public static $systemEmail = "noreply@hinthub.de";
    private $mailer            = null;
    
    public function __construct (MailerInterface $mailer)
    {
        $this -> mailer = $mailer;
    }


    public function sendMail ( $to, $from, $title, $data=[], $template="email/default.html.twig" )
    {
        if ( $this -> mailer === null )
            throw new \Exception ("Mailer is null");
        
        $email = ( new TemplatedEmail () )
            -> from         ( $from                 )
            -> to           ( new Address ( $to )   )
            -> subject      ( $title                )
            -> htmlTemplate ( $template )
            -> context      ( $data     )
        ;

        try 
        {
            $this -> mailer -> send ( $email );
            return [
                "send" => true, 
                "data" => $email
            ];
        }
        catch ( TransportExceptionInterface $e )
        {
            return [
                "send"  => false, 
                "error" => $e,
                "data"  => $email,
            ];
        }
    }
}
