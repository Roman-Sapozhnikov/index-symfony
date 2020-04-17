<?php

namespace App\Controller;

use App\Entity\Divider;
use App\Entity\Indexes;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DividerController extends AbstractController
{
    public function list(){
        $allDivider = $this->getAllDivider();

        return $this->render('/divider/list.html.twig', [
            "dividers" => $allDivider,
            "title" => "Список Divider"
        ]);
    }

    public function add(){
        if(!empty($_REQUEST["value"])){

            $arFields = Array(
                "quarter" => $_REQUEST["quarter"],
                "year" => $_REQUEST["year"],
                "value" => $_REQUEST["value"]
            );

            $this->saveDividerItem($arFields);

        }else{
            $_REQUEST["year"] = date("Y");
        }

        return $this->render('/divider/add.html.twig', [
            "request" => $_REQUEST,
            "title" => "Добавление Divider"
        ]);
    }

    public function edit($id)
    {

        if(count($_REQUEST["update"]) > 0){
            unset($_REQUEST["update"]["id"]);
            $this->editDivider($id, $_REQUEST["update"]);
        }

        return new Response();

    }

    public function getAllDivider(){
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $repository = $this->getDoctrine()->getRepository(Divider::class);

        $obj = $repository->findAll();

        $serializer = new Serializer($normalizers, $encoders);

        $arDividers = $serializer->normalize($obj);

        return $arDividers;
    }

    public function saveDividerItem($arFields)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $dividerItem = new Divider();

        $dividerItem->setQuarter($arFields["quarter"]);
        $dividerItem->setYear($arFields["year"]);
        $dividerItem->setValue(formatNumber($arFields["value"]));

        $entityManager->persist($dividerItem);

        $entityManager->flush();

        return new Response('Saved new dividerItem with id '.$dividerItem->getId());
    }

    public function editDivider($id, $arDivider){
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Divider::class);

        $divider = $repository->find($id);

        $divider->setYear($arDivider["year"]);
        $divider->setQuarter($arDivider["quarter"]);
        $divider->setValue($arDivider["value"]);

        $entityManager->persist($divider);
        $entityManager->flush();


        $arQuarter = get_dates_of_quarter(intval($arDivider["quarter"]), intval($arDivider["year"]), "d.m.Y");

        $filter = [
            "dateFrom" => $arQuarter["start"],
            "dateTo" => $arQuarter["end"],
        ];

        $order = [
            "date" => "asc"
        ];

        $arIndexes = $this->getDoctrine()->getRepository(Indexes::class)->findByFilter($filter, $order, 100000);

        foreach ($arIndexes as $arIndex){
            $mc = $arIndex["indexMarketCap"];
            $arFields = Array(
                "index_8848" => round($mc/$arDivider["value"], 7),
            );
            $this->updateIndexItem($arIndex["id"], $arFields);
        }

    }

    public function updateIndexItem($id, $arFields)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Indexes::class);

        $indexItem = $repository->find($id);

        $indexItem->setIndex8848TOP10($arFields["index_8848"]);

        $entityManager->persist($indexItem);

        $entityManager->flush();

        return new Response('Saved new indexItem with id '.$indexItem->getId());
    }
}

?>