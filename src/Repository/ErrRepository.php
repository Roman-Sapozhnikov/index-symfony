<?php

namespace App\Repository;

use App\Entity\Err;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Err|null find($id, $lockMode = null, $lockVersion = null)
 * @method Err|null findOneBy(array $criteria, array $orderBy = null)
 * @method Err[]    findAll()
 * @method Err[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ErrRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Err::class);
    }

    // /**
    //  * @return Err[] Returns an array of Err objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Err
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
