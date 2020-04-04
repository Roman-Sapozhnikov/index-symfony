<?php

namespace App\Controller;

use App\Entity\Indexes;
use App\Entity\Prices;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class IndexesController extends AbstractController
{
    public function index()
    {

        $allIndexes = $this->getAllIndexes();

        foreach ($allIndexes as $i => $arIndex){
            $arIndex["timestamp"] = $arIndex["date"]["timestamp"];
            unset($arIndex["date"]);
            $arIndex["date"] = date('d.m.Y', $arIndex["timestamp"]);

            $arDates[$i] = $arIndex["date"];
            $arIndexes[$i] = round($arIndex["index8848TOP10"]);
            //p($arIndex);
        }

        $dates = "[`".implode("`, `", $arDates)."`]";
        $indexes = "[`".implode("`, `", $arIndexes)."`]";
        $indexes = "[".implode(", ", $arIndexes)."]";


        return $this->render('/indexes/graph.html.twig', [
            "title" => "График",
            "dates" => $dates,
            "indexes" => $indexes
        ]);

    }

    public function getList($filter, $order, $limit){

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $repository = $this->getDoctrine()->getRepository(Prices::class);

        $obj = $repository->findBy($filter, $order, $limit);

        $serializer = new Serializer($normalizers, $encoders);

        $arCurrency = $serializer->normalize($obj);

        return $arCurrency;

    }

    public function getAllIndexes(){

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $repository = $this->getDoctrine()->getRepository(Indexes::class);

        $obj = $repository->findAll();

        $serializer = new Serializer($normalizers, $encoders);

        $arIndexes = $serializer->normalize($obj);

        return $arIndexes;

    }

    public function saveIndexItem($arFields): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $indexItem = new Indexes();

        $indexItem->setDate($arFields["date"]);
        $indexItem->setIndexMarketCap($arFields["market_cap"]);
        $indexItem->setIndex8848TOP10($arFields["market_cap"]/177306468);

        $entityManager->persist($indexItem);

        $entityManager->flush();

        return new Response('Saved new indexItem with id '.$indexItem->getId());
    }

}

?>