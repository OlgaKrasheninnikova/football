<?php
include 'MatchPrediction.php';


function getData(){
    require_once 'data.php';
    return $data;
}


/**
 * @param int $c1
 * @param int $c2
 * @throws InvalidArgumentException
 * @return array
 */
function match(int $c1, int $c2) {
    $prediction = new MatchPrediction( getData());

    return $prediction->predicate($c1, $c2);
}

//execute examlpe:
//$result = match(15, 0);

