<?php

// require_once "Process.php";
require_once "Shoes.php";
require_once "Production.php";

// int distributionQuantity;
//         Shoes shoes;
//         string storeID;

class Distribution extends Process 
{
    private Shoes $shoes;
    private Store $store;
    private int $distributionQty;
}