<?php

namespace App\Repository;

use App\Entity\FreeFloat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FreeFloat|null find($id, $lockMode = null, $lockVersion = null)
 * @method FreeFloat|null findOneBy(array $criteria, array $orderBy = null)
 * @method FreeFloat[]    findAll()
 * @method FreeFloat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FreeFloatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FreeFloat::class);
    }

    // /**
    //  * @return FreeFloat[] Returns an array of FreeFloat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FreeFloat
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
