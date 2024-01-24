<?php

class Store 
{
    private string $storeID;
    private string $storeName;
    private string $address;
    private string $leaderName;
    private int $totalEmployee;
    private $sales = [];
    private $shoes = [];

    public function __construct(string $storeID, string $storeName, string $address, string $leaderName, int $totalEmployee)
    {
        $this->storeID = $storeID;
        $this->storeName = $storeName;
        $this->address = $address;
        $this->leaderName = $leaderName;
        $this->totalEmployee = $totalEmployee;
    }

    public function setStoreID(string $ID)
    {
        $this->storeID = $ID;
    }

    public function getStoreID()
    {
        return $this->storeID;
    }

    public function setStoreName($name)
    {
        $this->storeName = $name;
    }

    public function getStoreName()
    {
        return $this->storeName;
    }

    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setLeaderName($name)
    {
        $this->leaderName = $name;
    }

    public function getLeaderName()
    {
        return $this->leaderName;
    }

    public function setTotalEmployee($amount)
    {
        $this->totalEmployee = $amount;
    }

    public function getTotalEmployee()
    {
        return $this->totalEmployee;
    }

    public function setSales(Sale $sale)
    {
        $this->sales[] = $sale;
    }

    public function getSales()
    {
        return $this->sales;
    }

    public function setShoes(Shoes $shoe)
    {
        $this->shoes[] = $shoe;
    }

    public function getShoes()
    {
        return $this->shoes;
    }
}