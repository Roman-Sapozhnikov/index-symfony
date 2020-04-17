<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\FreeFloat;
use App\Entity\Prices;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;


use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PricesController extends AbstractController
{
    public function index($curId = 1){

        if(!empty($_REQUEST["cur_id"])){
            $curId = $_REQUEST["cur_id"];
        }

        $arCur = $this->getCurByID($curId);

        $arAllCur = $this->getAllCurrency();

        foreach ($arAllCur as $key => $arrCur){
            if($arrCur["code"] == "USDT"){
                unset($arAllCur[$key]);
            }
        }

        $repository = $this->getDoctrine()->getRepository(Prices::class);

        $filter = Array(
            "cur_id" => $curId,
        );

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


        $arPrices = $repository->findByFilter($filter, $order, 100000);

        if(count($arPrices)){
            $firstClose = $arPrices[count($arPrices)-1]["close"];

            foreach ($arPrices as $k => $arPrice) {
                $prc = round(($arPrice["close"] - $firstClose)*100/$firstClose, 2);
                $arPrices[$k]["prc"] = $prc;
            }
        }else{
            $arPrices = [];
        }

        return $this->render("/prices/index.html.twig", [
            "prices" => $arPrices,
            "currency" => $arCur["name"],
            "currencies" => $arAllCur,
            "filter" => $filter
        ]);
    }

    public function parser($curId){

        /*

         
        $filter = [
            "cur_id" => $curId,
        ];

        $order = [
            "date" => "asc"
        ];

        $arrPrices = $this->getList($filter, $order, 1000000);

        foreach($arrPrices as $arPrice){
            $arPrice["date"] = date('d.m.Y', $arPrice["date"]["timestamp"]);
            $arPrice["quarter"] = intval((date('n', strtotime($arPrice["date"]))+2)/3);
            $arPrice["year"] = date("Y", strtotime($arPrice["date"]));

            $arFF = $this->getFreeFloat([
                "cur_id" => $curId,
                "quarter" => $arPrice["quarter"],
                "year" => $arPrice["year"],
            ]);

            p($arPrice);

            $mc = round($arPrice["close"]*$arFF[0]["value"], 2);

            $this->setMarketCap($arPrice["id"], $mc);

        }


        
          

        //$quarter = intval((date('n', strtotime($arFields["date"]))+2)/3);
        //$year = date("Y", strtotime($arFields["date"]));


        //$arr = $this->reCalcMarketCap(1);


        $arFFfilter = [
            "cur_id" => $curId
        ];

        $arFF = $this->getFreeFloat($arFFfilter);

        p($arFF);


        /*

        $arrPrices = $this->parserPricesCPC("monero");

        p($arrPrices);

        foreach ($arrPrices as $arPrice){
            $arPrice["cur_id"] = 18;

            $this->createPricesItem($arPrice);
        }

        */

        return new Response();
    }

    public function getAllCurrency(){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $repository = $this->getDoctrine()->getRepository(Currency::class);

        $obj = $repository->findAll();

        $serializer = new Serializer($normalizers, $encoders);

        $arCurrency = $serializer->normalize($obj);

        return $arCurrency;
    }

    public function getCurByID($id){

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $repository = $this->getDoctrine()->getRepository(Currency::class);

        $obj = $repository->find($id);

        $serializer = new Serializer($normalizers, $encoders);

        $arCurrency = $serializer->normalize($obj);

        return $arCurrency;

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

    public function parserPricesCPC($currency){

        if(!$currency){
            $currency = "bitcoin";
        }

        $url = "https://coinmarketcap.com/currencies/".$currency."/historical-data/?start=20170702&end=20200328";

        echo $url;

        $response = file_get_contents($url);

        $crawler = new Crawler($response);

        //$curPrice = $crawler->filterXPath('//span[@class="cmc-details-panel-price__price"]');

        //$currentPrice = $curPrice->text();

        $path_to_table = '//table/tbody/';

        $full_path = "";

        for($i=1002;$i>=1;$i--) {
            $full_path = $path_to_table . "tr[".$i."]/";

            $full_total_path = "";

            for($j=1;$j<=6;$j++){

                $full_total_path = $full_path . "td[".$j."]/div";

                $elements = $crawler->filterXPath($full_total_path);

                if($j==1){
                    $field = "date";
                }elseif($j==2){
                    $field = "open";
                }elseif($j==3){
                    $field = "high";
                }elseif($j==4){
                    $field = "low";
                }elseif($j==5){
                    $field = "close";
                }elseif($j==6){
                    $field = "volume";
                }

                if($elements->text()){
                    $arFields[$i][$field] = $elements->text();
                }

            }

        }

        return $arFields;

        return new Response();
    }

    public function createPricesItem($arFields): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $pricesItem = new Prices();

        $quarter = intval((date('n', strtotime($arFields["date"]))+2)/3);
        $year = date("Y", strtotime($arFields["date"]));

        $filter = Array(
            "cur_id" => $arFields["cur_id"],
            "quarter" => $quarter,
            "year" => $year,
        );

        $arFF = $this->getFreeFloat($filter, [], 1);

        $freeFloat = $arFF[0]["value"];


        $date = \DateTime::createFromFormat("M d, Y", $arFields["date"]);

        $pricesItem->setCurId(intval($arFields["cur_id"]));
        $pricesItem->setDate($date);
        $pricesItem->setOpen($this->formatNumber($arFields["open"]));
        $pricesItem->setLow($this->formatNumber($arFields["low"]));
        $pricesItem->setHigh($this->formatNumber($arFields["high"]));
        $pricesItem->setClose($this->formatNumber($arFields["close"]));
        $pricesItem->setVolume($this->formatNumber($arFields["volume"]));

        $marketCap = $this->formatNumber($arFields["close"]) * $this->formatNumber($freeFloat);

        $pricesItem->setMarketCap($marketCap);

        $entityManager->persist($pricesItem);

        $entityManager->flush();

        return new Response('Saved new pricesItem with id '.$pricesItem->getId());

    }

    public function getFreeFloat($filter, $order = Array(), $limit = 10){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $repository = $this->getDoctrine()->getRepository(FreeFloat::class);

        $obj = $repository->findBy($filter, $order, $limit);

        $serializer = new Serializer($normalizers, $encoders);

        $arFf = $serializer->normalize($obj);

        return $arFf;
    }

    public function setMarketCap($id, $mc){

        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Prices::class);

        $price = $repository->find($id);

        $price->setMarketCap($mc);

        $entityManager->persist($price);
        $entityManager->flush();
    }

    public function formatNumber($number){
        $number = str_replace(",", "", $number);
        return $number;
    }

}

?>