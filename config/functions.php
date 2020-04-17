<?php
function p($array){
    echo "<pre>";print_r($array);echo "</pre>";
}

function formatNumber($number){
    $number = str_replace(",", ".", $number);
    return $number;
}

function get_dates_of_quarter($quarter = 'current', $year = null, $format = null)
{

    if ( !is_int($year) ) {
        $year = (new DateTime)->format('Y');
    }
    $current_quarter = ceil((new DateTime)->format('n') / 3);

    switch (  strtolower($quarter) ) {
        case 'this':
        case 'current':
            $quarter = ceil((new DateTime)->format('n') / 3);
            break;

        case 'previous':
            $year = (new DateTime)->format('Y');
            if ($current_quarter == 1) {
                $quarter = 4;
                $year--;
            } else {
                $quarter =  $current_quarter - 1;
            }
            break;

        case 'first':
            $quarter = 1;
            break;

        case 'last':
            $quarter = 4;
            break;

        default:
            $quarter = (!is_int($quarter) || $quarter < 1 || $quarter > 4) ? $current_quarter : $quarter;
            break;
    }
    if ( $quarter === 'this' ) {
        $quarter = ceil((new DateTime)->format('n') / 3);
    }
    $start = new DateTime($year.'-'.(3*$quarter-2).'-1 00:00:00');
    $end = new DateTime($year.'-'.(3*$quarter).'-'.($quarter == 1 || $quarter == 4 ? 31 : 30) .' 23:59:59');

    return array(
        'start' => $format ? $start->format($format) : $start,
        'end' => $format ? $end->format($format) : $end,
    );
}
?>