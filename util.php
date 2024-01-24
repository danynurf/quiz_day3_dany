<?php

function scanner($message, $lengthMenu) {
    do {
        $choose = readline("$message : ");
    } while ($choose < 1 || $choose > $lengthMenu);

    return $choose;
}

function scannerNumber($message) {
    do {
        $num = readline("$message : ");
    } while(!is_numeric($num) || $num < 0);

    return (int)$num;
}