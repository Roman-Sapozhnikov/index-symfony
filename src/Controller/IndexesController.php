<?php

namespace App\Controller;

use App\Entity\Divider;
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

        $repository = $this->getDoctrine()->getRepository(Indexes::class);

        if(!empty($_REQUEST["dateFrom"])){
            $filter["dateFrom"] = $_REQUEST["dateFrom"];
        }else{
            $filter["dateFrom"] = "01.01.".date("Y");
        }

        if(!empty($_REQUEST["dateTo"])){
            $filter["dateTo"] = $_REQUEST["dateTo"];
        }else{
            $filter["dateTo"] = date("d.m.Y");
        }

        $order = [
            "date" => "desc"
        ];

        $allIndexes = $repository->findByFilter($filter, $order);

        if(count($allIndexes) > 0){
            $firstIndex = $allIndexes[count($allIndexes)-1]["index8848TOP10"];

            foreach ($allIndexes as $key => $arIndex){
                $index["timestamp"] = $arIndex["date"]["timestamp"];
                unset($allIndexes[$key]["date"]);
                $allIndexes[$key]["date"] = date('d.m.Y', $index["timestamp"]);
                $allIndexes[$key]["index8848TOP10"] = round($allIndexes[$key]["index8848TOP10"], 2);

                $prc = round(($arIndex["index8848TOP10"] - $firstIndex)*100/$firstIndex, 2);
                $allIndexes[$key]["prc"] = $prc;
            }


        }else{
            $allIndexes = [];
        }

        return $this->render('/indexes/index.html.twig', [
            "title" => "Индексы",
            "indexes" => $allIndexes,
            "filter" => $filter
        ]);



    }

    public function graph()
    {

        $repository = $this->getDoctrine()->getRepository(Indexes::class);

        $arIntervals = [
            0 => [
                "code" => "1jan",
                "value" => "С 1 янв",
                "codeKey" => "secondary",
            ],
            1 => [
                "code" => "3mes",
                "value" => "3 мес",
                "codeKey" => "secondary",
            ],
            2 => [
                "code" => "6mes",
                "value" => "6 мес",
                "codeKey" => "secondary",
            ],
            3 => [
                "code" => "year",
                "value" => "1 год",
                "codeKey" => "secondary",
            ],
            4 => [
                "code" => "max",
                "value" => "все",
                "codeKey" => "secondary",
            ],
        ];

        $dateFormat = "j M y";

        if(!empty($_REQUEST["interval"])){

            $interval = $_REQUEST["interval"];

            if($interval == "1jan"){
                $dateTo = date("d.m.Y");
                $dateFrom = "01.01.".date("Y");
                $arIntervals[0]["codeKey"] = "primary";
                $dateFormat = "j M";
            }elseif($interval == "3mes"){
                $dateTo = date("d.m.Y");
                $dateFrom = date("d.m.Y", strtotime($dateTo." - 3 months"));
                $arIntervals[1]["codeKey"] = "primary";
                if(date("Y", strtotime($dateFrom)) == date("Y")){
                    $dateFormat = "j M";
                }
            }elseif($interval == "6mes"){
                $dateTo = date("d.m.Y");
                $dateFrom = date("d.m.Y", strtotime($dateTo." - 6 months"));
                $arIntervals[2]["codeKey"] = "primary";
                if(date("Y", strtotime($dateFrom)) == date("Y")){
                    $dateFormat = "j M";
                }
            }elseif($interval == "year"){
                $dateTo = date("d.m.Y");
                $dateFrom = date("d.m.Y", strtotime($dateTo." - 1 year"));
                $arIntervals[3]["codeKey"] = "primary";
            }elseif($interval == "max"){
                $dateFrom = false;
                $dateTo = date("d.m.Y");
                $arIntervals[4]["codeKey"] = "primary";
            }

        }else{
            $dateTo = date("d.m.Y");
            $dateFrom = "01.01.".date("Y");
            $arIntervals[0]["codeKey"] = "primary";

            $dateFormat = "j M";
        }

        $filter = [
            "dateFrom" => $dateFrom,
            "dateTo" => $dateTo,
        ];

        $order = [
            "date" => "asc"
        ];

        $allIndexes = $repository->findByFilter($filter, $order);

        foreach ($allIndexes as $i => $arIndex){
            $arIndex["timestamp"] = $arIndex["date"]["timestamp"];
            unset($arIndex["date"]);

            $arIndex["date"] = date($dateFormat, $arIndex["timestamp"]);

            $arDates[$i] = $arIndex["date"];
            $arIndexes[$i] = round($arIndex["index8848TOP10"]);
        }

        $dates = "[`".implode("`, `", $arDates)."`]";
        $indexes = "[".implode(", ", $arIndexes)."]";

        return $this->render('/indexes/graph.html.twig', [
            "title" => "Основной график",
            "dates" => $dates,
            "indexes" => $indexes,
            "intervals" => $arIntervals,
        ]);

    }


    public function compare() {


        $repository = $this->getDoctrine()->getRepository(Indexes::class);

        $arIntervals = [
            0 => [
                "code" => "1jan",
                "value" => "С 1 янв",
                "codeKey" => "secondary",
            ],
            1 => [
                "code" => "3mes",
                "value" => "3 мес",
                "codeKey" => "secondary",
            ],
            2 => [
                "code" => "6mes",
                "value" => "6 мес",
                "codeKey" => "secondary",
            ],
            3 => [
                "code" => "year",
                "value" => "1 год",
                "codeKey" => "secondary",
            ],
            4 => [
                "code" => "max",
                "value" => "все",
                "codeKey" => "secondary",
            ],
        ];

        $dateFormat = "j M y";

        if(!empty($_REQUEST["interval"])){

            $interval = $_REQUEST["interval"];

            if($interval == "1jan"){
                $dateTo = date("d.m.Y");
                $dateFrom = "01.01.".date("Y");
                $arIntervals[0]["codeKey"] = "primary";
                $dateFormat = "j M";
            }elseif($interval == "3mes"){
                $dateTo = date("d.m.Y");
                $dateFrom = date("d.m.Y", strtotime($dateTo." - 3 months"));
                $arIntervals[1]["codeKey"] = "primary";
                if(date("Y", strtotime($dateFrom)) == date("Y")){
                    $dateFormat = "j M";
                }
            }elseif($interval == "6mes"){
                $dateTo = date("d.m.Y");
                $dateFrom = date("d.m.Y", strtotime($dateTo." - 6 months"));
                $arIntervals[2]["codeKey"] = "primary";
                if(date("Y", strtotime($dateFrom)) == date("Y")){
                    $dateFormat = "j M";
                }
            }elseif($interval == "year"){
                $dateTo = date("d.m.Y");
                $dateFrom = date("d.m.Y", strtotime($dateTo." - 1 year"));
                $arIntervals[3]["codeKey"] = "primary";
            }elseif($interval == "max"){
                $dateFrom = "02.07.2017";
                $dateTo = date("d.m.Y");
                $arIntervals[4]["codeKey"] = "primary";
            }

        }else{
            $dateTo = date("d.m.Y");
            $dateFrom = "01.01.".date("Y");
            $arIntervals[0]["codeKey"] = "primary";

            $dateFormat = "j M";
        }

        $filter = [
            "dateFrom" => $dateFrom,
            "dateTo" => $dateTo,
        ];

        $order = [
            "date" => "asc"
        ];

        $allIndexes = $repository->findByFilter($filter, $order);

        $firstIndex = $allIndexes[0]["index8848TOP10"];
        
        foreach ($allIndexes as $i => $arIndex){
            $arIndex["timestamp"] = $arIndex["date"]["timestamp"];
            unset($arIndex["date"]);
            $arIndex["date"] = date($dateFormat, $arIndex["timestamp"]);
            $arDates[$i] = $arIndex["date"];
            $arIndexesPrc[$i] = round(($arIndex["index8848TOP10"] - $firstIndex)*100/$firstIndex, 2);
        }

        $dates = "[`".implode("`, `", $arDates)."`]";
        $indexesPrc = "[".implode(", ", $arIndexesPrc)."]";






        $repository = $this->getDoctrine()->getRepository(Prices::class);

        $filter["cur_id"] = 1;

        $arPrices = $repository->findByFilter($filter, $order, 100000);

        if(count($arPrices)){
            $firstClose = $arPrices[0]["close"];

            foreach ($arPrices as $k => $arPrice) {
                $arPrc[$k] = round(($arPrice["close"] - $firstClose)*100/$firstClose, 2);
            }
        }

        $prcBtc = "[".implode(", ", $arPrc)."]";




        $filter["cur_id"] = 2;

        $arPrices = $repository->findByFilter($filter, $order, 100000);

        if(count($arPrices)){
            $firstClose = $arPrices[0]["close"];

            foreach ($arPrices as $k => $arPrice) {
                $arPrc[$k] = round(($arPrice["close"] - $firstClose)*100/$firstClose, 2);
            }
        }

        $prcEth = "[".implode(", ", $arPrc)."]";





        $filter["cur_id"] = 3;

        $arPrices = $repository->findByFilter($filter, $order, 100000);

        if(count($arPrices)){
            $firstClose = $arPrices[0]["close"];

            foreach ($arPrices as $k => $arPrice) {
                $arPrc[$k] = round(($arPrice["close"] - $firstClose)*100/$firstClose, 2);
            }
        }

        $prcXrp = "[".implode(", ", $arPrc)."]";










        $filter["cur_id"] = 5;

        $arPrices = $repository->findByFilter($filter, $order, 100000);

        if(count($arPrices)){
            $firstClose = $arPrices[0]["close"];

            foreach ($arPrices as $k => $arPrice) {
                $arPrc[$k] = round(($arPrice["close"] - $firstClose)*100/$firstClose, 2);
            }
        }

        $prcLtc = "[".implode(", ", $arPrc)."]";





        $filter["cur_id"] = 7;

        $arPrices = $repository->findByFilter($filter, $order, 100000);

        if(count($arPrices)){
            $firstClose = $arPrices[0]["close"];

            foreach ($arPrices as $k => $arPrice) {
                $arPrc[$k] = round(($arPrice["close"] - $firstClose)*100/$firstClose, 2);
            }
        }

        $prcEos = "[".implode(", ", $arPrc)."]";



        return $this->render('/indexes/compare.html.twig', [
            "title" => "График сравнения",
            "indexes" => $indexesPrc,
            "dates" => $dates,
            "prcBtc" => $prcBtc,
            "prcEth" => $prcEth,
            "prcXrp" => $prcXrp,
            "prcLtc" => $prcLtc,
            "prcEos" => $prcEos,
            "intervals" => $arIntervals,
        ]);

    }

    public function saveIndex(){



        /*
        $date = "02.07.2017";

        for($i=668;$i<=1001;$i++){
            $arDate[$i] = date("d.m.Y", strtotime($date)+86400*$i);
        }

        foreach ($arDate as $date){
            $divider = $this->getDividerByDate($date);

            $filter = array(
                "date" => \DateTime::createFromFormat("d.m.Y", $date)
            );

            $arrPrices = $this->getList($filter, ["cur_id" => "asc"], 100);

            $mc = 0;

            foreach ($arrPrices as $arrPrice){
                $mc = $mc + $arrPrice["marketCap"];
            }

            $arFields = Array(
                "date" => $date,
                "market_cap" => $mc,
                "index_8848" => round($mc/$divider, 7),
            );

            //p($arFields);

            //$this->saveIndexItem($arFields);
        }
        */

        return new Response();
    }

    public function getList($filter, $order = [], $limit = 1){

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

    public function getDividerByDate($date){
        $quarter = intval((date('n', strtotime($date))+2)/3);
        $year = date("Y", strtotime($date));

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $repository = $this->getDoctrine()->getRepository(Divider::class);

        $obj = $repository->findBy([
            "quarter" => $quarter,
            "year" => $year,
        ]);

        $serializer = new Serializer($normalizers, $encoders);

        $arDivider = $serializer->normalize($obj);

        return $arDivider[0]["value"];

    }

}

?>