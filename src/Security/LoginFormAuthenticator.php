<?php
// Handles LoginFormAuthentication - generated by Symfony (php bin/console make:auth) run by karim.saad ( karim.saad@iubh.de ) 

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Psr\Log\LoggerInterface;

/**
 * Created by Symfony LoginFormAuthenticator::class handles the LoginAuthentication 
 * part of php bin/console make:auth (compare https://symfony.com/doc/current/security.html#the-user)
 * 
 * @author karim.saad ( karim.saad@ubh.de )
 */
class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface   $urlGenerator;
    private LoggerInterface         $logger;


    public function __construct ( UrlGeneratorInterface $urlGenerator, LoggerInterface $logger )
    {
        $this -> urlGenerator = $urlGenerator;
        $this -> logger       = $logger;
    }

    public function authenticate ( Request $request ): PassportInterface
    {
        $email = $request -> request -> get('email', '');

        $request -> getSession() -> set ( Security::LAST_USERNAME, $email );

        return new Passport(
            new UserBadge ( $email ),
            new PasswordCredentials ( $request -> request -> get ( 'password', '' ) ),
            [
                new CsrfTokenBadge ( 'authenticate', $request -> get ( '_csrf_token' ) ),
            ]
        );
    }

    public function onAuthenticationSuccess ( Request $request, TokenInterface $token, string $firewallName ): ?Response
    {
        if ( $targetPath = $this -> getTargetPath ( $request -> getSession (), $firewallName ) ) 
        {
            return new RedirectResponse ( $targetPath );
        }

        $user  = $token -> getUser  ();
        $roles = $user  -> getROLES ();
        $roles = array_values ( $roles );

        if ( count ( $roles ) > 0 )
        {
            if ( $roles[0] == "ROLE_ADMIN" ) 
            {
                return new RedirectResponse ( $this -> urlGenerator -> generate ( 'admin' ) );
            } 
            else if ( $roles[0] == "ROLE_KUNDE" )
            {
                return new RedirectResponse ( $this -> urlGenerator -> generate ( 'kundenbereich' ) );
            }
            else
            {
                //throw new \Exception('TODO: provide a valid redirect inside for other than ROLE_ADMIN or ROLE_KUNDE');
                $message = "redirect after login failed, no route found for the USER ROLE";
                $this -> logger -> error ( $message );
                return new RedirectResponse ( $this -> urlGenerator -> generate ( 'app_logout' ) );
            }
        } 
        else
        {
            $message = "USER ROLE array is empty! (After login)";
            $this -> logger -> error ( $message );
            return new RedirectResponse ( $this -> urlGenerator -> generate ( 'app_logout' ) );
        }
    }

    protected function getLoginUrl ( Request $request ): string
    {
        return $this -> urlGenerator -> generate ( self::LOGIN_ROUTE );
    }
}
