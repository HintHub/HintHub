<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Created by Symfony RegistrationFormType::class handles the Registration form 
 * part of php bin/console make:auth (compare https://symfony.com/doc/current/security.html#the-user)
 * 
 * @author karim.saad ( karim.saad@ubh.de )
 * 
 * Last Edit: 01.02.2022 0049
 */
class RegistrationFormType extends AbstractType
{
    public function buildForm ( FormBuilderInterface $builder, array $options )
    {
        $builder
            -> add ( 'email',       null, [ 'translation_domain' => 'register', 'label' => 'email' ] )
            -> add ( 'agreeTerms', 
                CheckboxType::class, [
                    'mapped'      => false,
                    'constraints' => [
                        new IsTrue (
                            [
                                'message' => 'You should agree to our terms.',
                            ]
                        ),
                    ],
                ]
            )
            -> add ( 'plainPassword',
                PasswordType::class, [
                    'translation_domain' => 'register', 
                    'label'              => 'password',
                    'mapped'             => false,
                    'attr'               => [ 'autocomplete' => 'new-password' ],

                    'constraints'        => [
                        new NotBlank(
                            [
                                'message' => 'Please enter a password',
                            ]
                        ),
                        new Length(
                            [
                                'min' => 6,
                                'minMessage' => 'Your password should be at least {{ limit }} characters',
                                // max length allowed by Symfony for security reasons
                                'max' => 4096,
                            ]
                        ),
                    ],
                ]
            );
    }

    public function configureOptions ( OptionsResolver $resolver )
    {
        $resolver->setDefaults (
            [
                'data_class' => User::class,
            ]
        );
    }
}
