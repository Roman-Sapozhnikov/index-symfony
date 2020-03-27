<?php

namespace App\Controller;

use App\Entity\Bitcoin;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;

class DefaultController extends AbstractController
{
    public function index(){

        $currency = "bitcoin";

        if(!$currency){
            $currency = "bitcoin";
        }

        $url = "https://coinmarketcap.com/currencies/".$currency."/historical-data/?start=20200101";


        $arCurrencies = Array(
            "bitcoin",
            "ethereum",
            "xrp",
            "tether",
            "bitcoin-cash",
            "litecoin",
            "eos",
            "stellar",
            "cardano",
            "iota",
            "tron",
            "neo",
            "nem",
            "dash",
            "monero",
            "binance-coin"
        );

        $response = file_get_contents($url);


        $crawler = new Crawler($response);



        $path = '//*[@id="__next"]/div/div[2]/div[1]/div[2]/div[1]/div/div[1]/span[1]/span[1]';

        $curPrice = $crawler->filterXPath($path);

        $currentPrice = $curPrice->text();

        $path_to_table = '//*[@id="__next"]/div/div[2]/div[1]/div[2]/div[3]/div/ul[2]/li[5]/div/div/div[2]/div[3]/div/table/tbody/';

        $full_path = "";

        for($i=83;$i>=1;$i--) {
            $full_path = $path_to_table . "tr[".$i."]/";

            $full_total_path = "";

            $arFields = Array();

            for($j=1;$j<=7;$j++){

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
                    $arFields[$field] = $elements->text();
                }

            }

            $this->createBitcoinItem($arFields);

            print_r($arFields);
            echo "<hr>";

        }

        return new Response();
    }

    public function createBitcoinItem($arFields): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $bitcoinItem = new Bitcoin();

        $date = \DateTime::createFromFormat("M d, Y", $arFields["date"]);

        $bitcoinItem->setDate($date);
        $bitcoinItem->setOpen($this->formatNumber($arFields["open"]));
        $bitcoinItem->setLow($this->formatNumber($arFields["low"]));
        $bitcoinItem->setHigh($this->formatNumber($arFields["high"]));
        $bitcoinItem->setClose($this->formatNumber($arFields["close"]));
        $bitcoinItem->setVolume($this->formatNumber($arFields["volume"]));

        $entityManager->persist($bitcoinItem);

        $entityManager->flush();

        return new Response('Saved new bitcoinItem with id '.$bitcoinItem->getId());

    }

    public function formatNumber($number){
        $number = str_replace(",", "", $number);
        return $number;
    }

}

?>