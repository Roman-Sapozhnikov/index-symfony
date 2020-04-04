<?php

namespace App\Controller;

use App\Entity\Currency;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CurrencyController extends AbstractController
{

    public function form()
    {

        if(!empty($_REQUEST["name"])){

            $arFields = Array(
                "name" => $_REQUEST["name"],
                "code" => $_REQUEST["code"],
                "path_cmc" => $_REQUEST["pathCmc"],
            );

            $this->addCurrency($arFields);

        }

        return $this->render('/currency/add.html.twig', [

        ]);
    }

    public function list()
    {

        $allCur = $this->getAllCurrency();

        return $this->render('/currency/list.html.twig', [
            "currencies" => $allCur
        ]);
    }

    public function addCurrency($arFields):Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $currency = new Currency();

        $currency->setName($arFields["name"]);
        $currency->setCode($arFields["code"]);
        $currency->setPathCmc($arFields["path_cmc"]);

        $entityManager->persist($currency);

        $entityManager->flush();

        return new Response('Saved new currency with id '.$currency->getId());

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

    public function getByID($id){

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $repository = $this->getDoctrine()->getRepository(Currency::class);

        $obj = $repository->find($id);

        $serializer = new Serializer($normalizers, $encoders);

        $arCurrency = $serializer->normalize($obj);

        return $arCurrency;

    }
}

?>