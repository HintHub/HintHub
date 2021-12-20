<?php

namespace App\Controller\Admin;

use App\Entity\User;

use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;


use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\PasswordHasher;
use Symfony\Component\Form\FormEvents;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Symfony\Component\Form\FormBuilderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;

/* add */
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

// For password Hashing
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Security\Core\Encoder\PasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;

/**
 * CrudController for user generated via php bin/console make:admin:crud
 * compare https://symfony.com/bundles/EasyAdminBundle/current/crud.html
 * 
 * @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de )
 */
class UserCrudController extends AbstractCrudController
{
    private $passwordHasher;

    public function __construct () {
        $this->passwordHasher = new PasswordHasherFactory ([
            "user" => ['algorithm' => 'auto']
        ]);
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        /*
            Fields:
                id
                email
                password
                ROLES
                salt
                isActive
                isVerified
                plainPassword
                eingereichteFehler
                eingereichteKommentare
                module
        */
        return [
            IdField::new            ( 'id'            ) -> hideOnForm(),
            IdField::new            ( 'id'            ) -> onlyOnForms() ->  hideWhenCreating() -> setFormTypeOption ( 'disabled', 'disabled' ),
            TextField::new          ( 'email'         ),
            TextEditorField::new    ( 'salt'          ),
            ChoiceField::new        ( 'ROLES'         ) -> setChoices ( $this -> getRoleChoices() ) -> allowMultipleChoices(),
            TextField::new          ( 'plainPassword' ) -> setFormType ( PasswordType::class ) -> onlyOnforms(),
            AssociationField::new   ( 'module'        )
        ];
    }

    public function getRoleChoices() 
    {
        return [
            'Admin'     => 'ROLE_ADMIN',
            'Student'   => 'ROLE_STUDENT',
            'Tutor'     => 'Tutor'
        ];
    }

    /*
        Taken out of:
        https://github.com/EasyCorp/EasyAdminBundle/issues/3349#issuecomment-695214741
    */

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder ($entityDto, $formOptions, $context);

        $this -> addHashPasswordEventListener ($formBuilder);

        return $formBuilder;
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder ($entityDto, $formOptions, $context);

        $this -> addHashPasswordEventListener ($formBuilder);

        return $formBuilder;
    }

    protected function addHashPasswordEventListener(FormBuilderInterface $formBuilder)
    {
        $formBuilder -> addEventListener (
            FormEvents::SUBMIT, 
            function (FormEvent $event) 
            {
                /** @var User $user */
                $obj = $event -> getData();
                if ($obj instanceof User) 
                {
                    $user = $obj;
                    $plainPW = $user -> getPlainPassword();
                    if ( $plainPW ) 
                    {
                        $user -> setPassword ( $this -> passwordHasher -> getPasswordHasher ( 'user' ) -> hash ( $plainPW ) );
                    }
                }
        
            }
        );
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters) :QueryBuilder
    {
        parent::createIndexQueryBuilder ($searchDto, $entityDto, $fields, $filters);

        $request     = $searchDto -> getRequest();

        $qb = $this -> get ( EntityRepository::class ) -> createQueryBuilder ($searchDto, $entityDto, $fields, $filters);

        /*$deletedOnly = $request->query->get('deletedOnly') == 1;
        if ($deletedOnly)
        {
            $qb->andWhere('entity.deleted_at IS NOT NULL');
        }
        else
        {
            $qb->andWhere('entity.deleted_at IS NULL');
        }*/
        return $qb;
    }
}
