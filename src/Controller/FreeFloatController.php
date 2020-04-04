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

        return $this->render('/freeFloat/add.html.twig', [

        ]);
    }

    public function list()
    {
        $allFf = $this->getAllFreeFloat();

        $arCur = $this->getAllCurrency();

        foreach($allFf as $kFF => $arFf){
            $allFf[$kFF]["curName"] = $arCur[$arFf["curId"]]["name"];
        }

        return $this->render('/freeFloat/list.html.twig', [
            "freeFloats" => $allFf
        ]);
    }

    public function saveFFItem($arFields): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $ffItem = new FreeFloat();

        $ffItem->setCurId($arFields["cur_id"]);
        $ffItem->setQuarter($arFields["quarter"]);
        $ffItem->setYear($arFields["year"]);
        $ffItem->setValue($arFields["value"]);

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

        return $arCurrency;

    }

    public function getAllFreeFloat(){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $repository = $this->getDoctrine()->getRepository(FreeFloat::class);

        $obj = $repository->findAll();

        $serializer = new Serializer($normalizers, $encoders);

        $arCurrency = $serializer->normalize($obj);

        return $arCurrency;
    }

}

?>