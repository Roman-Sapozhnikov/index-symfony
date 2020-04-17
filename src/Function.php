<?php
function p($array){
    echo "<pre>";print_r($array);echo "</pre>";
}

function formatNumber($number){
    $number = str_replace(",", ".", $number);
    return $number;
}
?>