<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Saves changes to profile
 * 
 * @author Stefan Baltschun (stefan.baltschun@iubh.de)
 */
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(UserService $userService, Request $request): Response
    {
        $user = $userService -> getCurrentUser ();

        if ( $user === null )
            throw new \Exception ( "Nicht eingeloggt" );

        $userForm = $this -> createFormBuilder ( $user )
            -> add  ( 'pfplink',        TextType::class,     [ "label"    => 'Profilbild Link'              ] )
            -> add  ( 'email',          TextType::class,     [ "label"    => "E-Mail"                       ] )
            -> add  ( 'plainPassword',  PasswordType::class, [ "required" => false, "label" => "Passwort"   ] )
            -> add  ( 'save',           SubmitType::class,   [ "label"    => "Speichern"                    ] )
            -> getForm();


        $userForm -> handleRequest ( $request );
        if ( $userForm -> isSubmitted() && $userForm -> isValid () ) 
        {
                // Get Form Data
                $newUser = $userForm -> getData ();

                // PW hashing
                $npw        = $newUser -> getPlainPassword ();

                if ( $npw ) 
                {
                    $hashedPW =  $userService -> getHashedPW ( $newUser, $npw );
                    $newUser -> setPassword ( $hashedPW );
                    $userService -> update ( $newUser );
                    return $this -> redirectToRoute("app_logout");
                }

                // Update
                $u = $userService -> update ( $newUser );
                $success = ( $u ) ? true : false;
                
                // Rendering
                return $this -> renderIt ( $user, $userForm, $success );
        }
    

        return $this -> renderIt ( $user, $userForm );
    }

    private function renderIt ($user, $userForm, $success=null, $templatePath='bundles/EasyAdminBundle/crud/profile/profile.html.twig')
    {
        $ar = [];

        if ( $success !== null )
        {
            $templateVars = [
                'success'         => $success,
                'userForm'        => $userForm,
                'user'            => $user
            ];
        } else {
            $templateVars = [
                'userForm'        => $userForm,
                'user'            => $user
            ];
        } 

        return $this->renderForm(
            $templatePath, 
            $templateVars
        );
    }
}
