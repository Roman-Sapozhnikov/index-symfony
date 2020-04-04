<?php

namespace App\Repository;

use App\Entity\Prices;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Prices|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prices|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prices[]    findAll()
 * @method Prices[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PricesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prices::class);
    }

    // /**
    //  * @return Prices[] Returns an array of Prices objects
    //  */

    public function findByFilter($filter = [], $order = [], $limit = 10)
    {
        $qb = $this->createQueryBuilder("p");

        $qb->select(
            "p.id",
            "p.cur_id",
            "p.date",
            "p.open",
            "p.high",
            "p.low",
            "p.close",
            "p.volume",
            "p.market_cap"
        );

        //$qb->where("p.date >= '2020-01-03'");

        $qb->where("p.cur_id = ".$filter["cur_id"]);

        if(!empty($order)){
            $sort = array_key_first($order);
            $order = array_shift($order);
            $qb->addOrderBy("p.".$sort, $order);
        }

        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();

    }


}
