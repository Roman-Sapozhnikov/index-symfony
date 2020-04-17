<?php

namespace App\Controller;

use App\Entity\FreeFloat;
use App\Entity\Currency;
use App\Entity\Prices;
use App\Entity\Indexes;
use App\Entity\Divider;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class FreeFloatController extends AbstractController
{
    public function index(){
        return new Response("Free Float");
    }

    public function add()
    {

        if(!empty($_REQUEST["cur_id"])){

            $arFields = Array(
                "cur_id" => $_REQUEST["cur_id"],
                "quarter" => $_REQUEST["quarter"],
                "year" => $_REQUEST["year"],
                "value" => $_REQUEST["value"]
            );

            $this->saveFFItem($arFields);

        }else{
            $_REQUEST["cur_id"] = 1;
            $_REQUEST["year"] = date("Y");
        }

        $arCur = $this->getAllCurrency();

        return $this->render('/freeFloat/add.html.twig', [
            "currency" => $arCur,
            "request" => $_REQUEST
        ]);
    }

    public function list()
    {
        
        if(!empty($_REQUEST["setFilter"])){
            $filter = $_REQUEST;
            if(!$filter["cur_id"]){
                $filter["cur_id"] = 0;
            }
            $allFf = $this->getAllFreeFloat($filter);
        }else{
            $filter = Array(
                "cur_id" => 0,
                "year" => date("Y")
            );
            $allFf = $this->getAllFreeFloat();
        }

        $arCur = $this->getAllCurrency();

        foreach($allFf as $kFF => $arFf){
            $allFf[$kFF]["curName"] = $arCur[$arFf["curId"]]["name"];
        }

        return $this->render('/freeFloat/list.html.twig', [
            "freeFloats" => $allFf,
            "currency" => $arCur,
            "filter" => $filter
        ]);
    }

    public function edit($id)
    {

        if(count($_REQUEST["update"]) > 0){
            unset($_REQUEST["update"]["id"]);
            $this->editFreeFloat($id, $_REQUEST["update"]);
        }

        return new Response();

    }

    public function saveFFItem($arFields)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $ffItem = new FreeFloat();

        $ffItem->setCurId($arFields["cur_id"]);
        $ffItem->setQuarter($arFields["quarter"]);
        $ffItem->setYear($arFields["year"]);
        $ffItem->setValue(formatNumber($arFields["value"]));

        $entityManager->persist($ffItem);

        $entityManager->flush();

        return new Response('Saved new indexItem with id '.$ffItem->getId());
    }

    public function getAllCurrency(){

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $repository = $this->getDoctrine()->getRepository(Currency::class);

        $obj = $repository->findAll();

        $serializer = new Serializer($normalizers, $encoders);

        $arCurrency = $serializer->normalize($obj);

        foreach ($arCurrency as $key => $arCur){
            $arCurrency[$arCur["id"]] = $arCur;
        }

        unset($arCurrency[0]);

        return $arCurrency;

    }

    public function getAllFreeFloat($filter = Array()){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $repository = $this->getDoctrine()->getRepository(FreeFloat::class);

        if(empty($filter["cur_id"])){
            unset($filter["cur_id"]);
        }

        if(empty($filter["year"])){
            unset($filter["year"]);
        }

        if(empty($filter["quarter"])){
            unset($filter["quarter"]);
        }

        if(!empty($filter)){
            unset($filter["setFilter"]);
            $obj = $repository->findBy($filter, Array("id" => "desc"));
        }else{
            $obj = $repository->findAll();
        }

        $serializer = new Serializer($normalizers, $encoders);

        $arCurrency = $serializer->normalize($obj);

        return $arCurrency;
    }

    public function editFreeFloat($id, $arFreeFloat){
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(FreeFloat::class);

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);


        $freefloat = $repository->find($id);
        $arrFreeFloat = $serializer->normalize($freefloat);


        $freefloat->setYear($arFreeFloat["year"]);
        $freefloat->setQuarter($arFreeFloat["quarter"]);
        $freefloat->setValue($arFreeFloat["value"]);

        $entityManager->persist($freefloat);
        $entityManager->flush();
        

        /*
         * Обновление MarketCap для всех цен данной валюты по данному кварталу и году
         */

        $arQuarter = get_dates_of_quarter(intval($arFreeFloat["quarter"]), intval($arFreeFloat["year"]), "d.m.Y");

        $filter = [
            "cur_id" => $arrFreeFloat["curId"],
            "dateFrom" => $arQuarter["start"],
            "dateTo" => $arQuarter["end"],
        ];

        $order = [
            "date" => "asc"
        ];

        $arPrices = $this->getDoctrine()->getRepository(Prices::class)->findByFilter($filter, $order, 100000);

        foreach ($arPrices as $arPrice){
            $mc = round($arPrice["close"]*$arFreeFloat["value"], 2);
            $arDates[] = $arPrice["date"]->format("d.m.Y");
            $this->setMarketCap($arPrice["id"], $mc);
        }

        $divider = $this->getDividerByDate($arQuarter["end"]);

        foreach ($arDates as $date){

            $filter = array(
                "date" => \DateTime::createFromFormat("d.m.Y", $date)
            );

            $arrPrices = $this->getPrices($filter, ["cur_id" => "asc"], 100);
            
            $mc = 0;

            foreach ($arrPrices as $arrPrice){
                $mc = $mc + $arrPrice["marketCap"];
            }

            $arFields = Array(
                "date" => $date,
                "market_cap" => $mc,
                "index_8848" => round($mc/$divider, 7),
            );
            
            $this->updateIndexItem($arFields);

        }

    }

    public function getPrices($filter, $order = [], $limit=1){

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $repository = $this->getDoctrine()->getRepository(Prices::class);

        $obj = $repository->findBy($filter, $order, $limit);

        $serializer = new Serializer($normalizers, $encoders);

        $arCurrency = $serializer->normalize($obj);

        return $arCurrency;

    }

    public function setMarketCap($id, $mc){
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Prices::class);

        $price = $repository->find($id);

        $price->setMarketCap($mc);

        $entityManager->persist($price);
        $entityManager->flush();
    }

    public function updateIndexItem($arFields)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Indexes::class);

        $indexItem = $repository->findBy([
            "date" => \DateTime::createFromFormat("d.m.Y", $arFields["date"])
        ]);

        $indexItem[0]->setIndexMarketCap($arFields["market_cap"]);
        $indexItem[0]->setIndex8848TOP10($arFields["index_8848"]);

        $entityManager->persist($indexItem[0]);

        $entityManager->flush();

        return new Response('Saved new indexItem with id '.$indexItem[0]->getId());
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