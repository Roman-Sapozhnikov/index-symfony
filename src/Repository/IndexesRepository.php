<?php

namespace App\Repository;

use App\Entity\Indexes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Indexes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Indexes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Indexes[]    findAll()
 * @method Indexes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndexesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Indexes::class);
    }

    // /**
    //  * @return Indexes[] Returns an array of Indexes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Indexes
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
