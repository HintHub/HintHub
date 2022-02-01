<?php
/*
    generated by Symfony (php bin/console make:entity) 
    @author ali-kemal.yalama ( ali-kemal.yalama@iubh.de) 

    Last edit by karim.saad (karim.saad@iubh.de) 01.02.22 0102
*/

namespace App\Repository;

use App\Entity\Kommentar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Kommentar|null find($id, $lockMode = null, $lockVersion = null)
 * @method Kommentar|null findOneBy(array $criteria, array $orderBy = null)
 * @method Kommentar[]    findAll()
 * @method Kommentar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KommentarRepository extends ServiceEntityRepository
{
    public function __construct ( ManagerRegistry $registry )
    {
        parent::__construct ( $registry, Kommentar::class );
    }
}
