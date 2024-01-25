<?php

class Sale 
{
    private Shoes $shoes;
    private Store $store;
    private string $saleDate;
    private int $saleQuantity;
    private int $totalSale;

    public function setShoe(Shoes $shoe)
    {
        $this->$shoes = $shoe;
    }

    public function getShoe()
    {
        return $this->$shoes;
    }

    public function setStore(Store $store)
    {
        $this->store = $store;
    }

    public function getStore()
    {
        return $this->store;
    }

    public function setSaleDate(string $date)
    {
        $this->saleDate = $date;
    }

    public function getSaleDate()
    {
        return $this->saleDate;
    }

    public function setSaleQty($quantity)
    {
        $this->saleQuantity = $quantity;
    }

    public function getSaleQty()
    {
        return $this->saleQuantity;
    }

    public function setTotalSale(int $price)
    {
        $this->totalSale = $this->saleQuantity * $price;
    }

    public function getTotalSale()
    {
        return $this->totalSale;
    }
}