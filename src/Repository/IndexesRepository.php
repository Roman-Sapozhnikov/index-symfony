<?php

namespace App\Repository;

use App\Entity\Indexes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

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

    public function findByFilter($filter = [], $order = [], $limit = false)
    {

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        if(!empty($filter["dateFrom"])){
            $dateFrom = date("Y-m-d", strtotime($filter["dateFrom"]));
        }else{
            $dateFrom = "2017-07-02";
        }

        if(!empty($filter["dateTo"])){
            $dateTo = date("Y-m-d", strtotime($filter["dateTo"]));
        }else{
            $dateTo = date("Y-m-d");
        }

        $qb = $this->createQueryBuilder("i")->andWhere("i.date >= '".$dateFrom."'")->andWhere("i.date <= '".$dateTo."'");

        if(!empty($order)){
            $sort = array_key_first($order);
            $order = array_shift($order);
            $qb->addOrderBy("i.".$sort, $order);
        }

        if($limit){
            $qb->setMaxResults($limit);
        }

        $obj = $qb->getQuery()->getResult();

        $serializer = new Serializer($normalizers, $encoders);

        $arIndexes = $serializer->normalize($obj);

        return $arIndexes;

    }

    public function saveIndexItem($arFields)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $indexItem = new Indexes();

        $indexItem->setDate(\DateTime::createFromFormat("d.m.Y", $arFields["date"]));
        $indexItem->setIndexMarketCap($arFields["market_cap"]);
        $indexItem->setIndex8848TOP10($arFields["index_8848"]);

        $entityManager->persist($indexItem);

        $entityManager->flush();
        
        return new Response('Saved new indexItem with id '.$indexItem->getId());
    }

}
