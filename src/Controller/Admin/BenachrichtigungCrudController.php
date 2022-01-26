<?php

namespace App\Controller\Admin;

use App\Service\UserService;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Benachrichtigung;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BenachrichtigungCrudController extends AbstractCrudController
{
    
    private $userService;
    
    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }
    
    public static function getEntityFqcn(): string
    {
        return Benachrichtigung::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */

    public function configureActions ( Actions $actions ): Actions
    {
        return $actions
            // ...
            -> add    ( Crud::PAGE_INDEX , Action::DETAIL   )
            -> remove ( Crud::PAGE_INDEX , Action::NEW      )
            -> remove ( Crud::PAGE_INDEX , Action::EDIT     )
            -> remove ( Crud::PAGE_DETAIL, Action::EDIT     )
        ;
        
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $user = $this -> userService -> getCurrentUser ();
        $userId = $user->getId();

        $response = $this -> get ( EntityRepository::class ) -> createQueryBuilder ( $searchDto, $entityDto, $fields, $filters );

        if( $user -> isTutor() || $user -> isStudent() ) 
        {
            $response
                -> andWhere     ( 'entity.user = :userId')
                -> setParameter ( 'userId', $userId         )
                ;
        }

        //-> addOrderBy('entity.status', 'ASC');
        return $response;
    }
}
