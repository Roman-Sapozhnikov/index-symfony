<?php

namespace App\Repository;

use App\Entity\Divider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Divider|null find($id, $lockMode = null, $lockVersion = null)
 * @method Divider|null findOneBy(array $criteria, array $orderBy = null)
 * @method Divider[]    findAll()
 * @method Divider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DividerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Divider::class);
    }

    // /**
    //  * @return Divider[] Returns an array of Divider objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Divider
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
