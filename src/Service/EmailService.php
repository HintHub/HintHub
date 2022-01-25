<?php

namespace App\Service;

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
    public function __construct ()
    {
        
    }


    public function sendMail ( $to, $from, $title, $data=[], $template="email/default.html.twig" )
    {
        $email = ( new TemplatedEmail () )
            -> from         ( $from                 )
            -> to           ( new Address ( $to )   )
            -> subject      ( $title                )
            -> htmlTemplate ( $template )
            -> context      ( $data     )
        ;
    }
}