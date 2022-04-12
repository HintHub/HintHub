<?php
namespace App\Filter;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\NumericFilterType;

/**
* UnbearbeitetTageFilter
* 
* It's not the best.. but it works :-D
*
* @author karim.saad (karim.saad@iubh.de)
*/
class UnbearbeitetTageFilter implements FilterInterface
{
    use FilterTrait;

    public static function new ( string $propertyName, $label = null ): self
    {
        return ( new self () )         
            -> setFilterFqcn        ( __CLASS__                     )
            -> setProperty          ( $propertyName                 )
            -> setLabel             ( $label                        )
            -> setFormType          ( NumericFilterType::class      )
            // -> setFormTypeOption    ( 'mapped', false            )
        ;
    }

    public function apply ( QueryBuilder $queryBuilder, FilterDataDto $filterDataDto, ?FieldDto $fieldDto, EntityDto $entityDto ): void
    {
        $property       = $filterDataDto -> getProperty         ();
        $comparison     = $filterDataDto -> getComparison       ();
        $parameterName  = $filterDataDto -> getParameterName    ();
        $tage           = $filterDataDto -> getValue            ();

        $where = sprintf ( 'date_diff(current_date(), entity.datumLetzteAenderung) %s :%s', $comparison, $parameterName );
        
        $queryBuilder
            -> andWhere     ( $where                )
            -> setParameter ( $parameterName, $tage )
            -> orderBy ('date_diff(current_date(), entity.datumLetzteAenderung)', 'ASC')
            ;
    }
}