<?php

// require_once "Process.php";
require_once "Shoes.php";
require_once "Production.php";

// int distributionQuantity;
//         Shoes shoes;
//         string storeID;

class Distribution extends Process 
{
    private Shoes $shoe;
    private Store $store;
    private int $distributionQty;

    public function setID(string $ID) 
    {
        $prodID = "DIS-" . $ID;
        parent::setID($prodID);
    }

    public function setShoe(Shoes $shoe)
    {
        $this->shoe = $shoe;
    }

    public function getShoe()
    {
        return $this->shoe;
    }

    public function setStore(Store $store)
    {
        $this->store = $store;
    }

    public function getStore()
    {
        return $this->store;
    }

    public function setDistributionQty(int $qty)
    {
        $this->distributionQty = $qty;
    }

    public function getDistributionQty()
    {
        return $this->distributonQty;
    }
}