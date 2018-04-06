<?php
require_once 'data.php';
include 'MatchPrediction.php';

function match(int $c1, int $c2) {
    $prediction = new MatchPrediction( getData(), $c1, $c2 );

    $prediction->execute();
    return [$prediction->getTeam1Goals(), $prediction->getTeam2Goals()];
}

$a = match(1,0);
var_dump($a);