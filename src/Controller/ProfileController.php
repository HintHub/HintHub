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
 * Profil bearbeiten
 * 
 * @author Stefan Baltschun (stefan.baltschun@iubh.de)
 *
 * TODO: schick machen
 */
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index ( UserService $userService, Request $request ): Response
    {
        $user = $userService -> getCurrentUser ();

        if ( $user === null )
            throw new \Exception ( "Nicht eingeloggt" );

        $userForm = $this -> createFormBuilder ( $user )
            -> add  ( 'email',          TextType     ::class )
            -> add  ( 'plainPassword',  PasswordType ::class )
            -> add  ( 'save',           SubmitType   ::class )
            -> getForm();


        $userForm -> handleRequest ( $request );
        if ( $userForm -> isSubmitted () && $userForm -> isValid () ) 
        {
                // Get Form Data
                $newUser = $userForm -> getData();

                // PW hashing
                $newPW    =  $newUser -> getPlainPassword ();
                $hashedPW =  $userService -> getHashedPW ( $newUser, $newPW );
                $newUser -> setPassword ( $hashedPW );

                // Update
                $success = ( $userService -> update ($newUser) ) ? true : false;

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
        } 
        else
        {
            $templateVars = [
                'userForm'        => $userForm,
                'user'            => $user
            ];
        } 

        return $this -> renderForm (
            $templatePath, 
            $templateVars
        );
    }
}
