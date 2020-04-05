<?php

namespace App\Controller;

use App\Entity\FreeFloat;
use App\Entity\Currency;

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

    public function saveFFItem($arFields): Response
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

}

?>