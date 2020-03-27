<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function index(){


        $data = file_get_contents("https://coinmarketcap.com/currencies/bitcoin/historical-data/");







        return new Response();
    }
}

?>