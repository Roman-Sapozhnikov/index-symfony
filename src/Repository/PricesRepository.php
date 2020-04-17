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

        if(!empty($filter["dateFrom"])){
            $dateFrom = date("Y-m-d", strtotime($filter["dateFrom"]));
        }else{
            $dateFrom = date("Y")."-01-01";
        }

        if(!empty($filter["dateTo"])){
            $dateTo = date("Y-m-d", strtotime($filter["dateTo"]));
        }else{
            $dateTo = date("Y-m-d");
        }

        $qb = $this->createQueryBuilder("p")->addSelect(
            "p.id",
            "p.cur_id",
            "p.date",
            "p.open",
            "p.high",
            "p.low",
            "p.close",
            "p.volume",
            "p.market_cap"
        )->andWhere("p.cur_id = ".$filter["cur_id"])->andWhere("p.date >= '".$dateFrom."'")->andWhere("p.date <= '".$dateTo."'");

        if(!empty($order)){
            $sort = array_key_first($order);
            $order = array_shift($order);
            $qb->addOrderBy("p.".$sort, $order);
        }

        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();

    }


}
