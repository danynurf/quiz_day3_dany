<?php

class NikiShoes 
{
    private $shoes = [];
    private $stores = [];
    private $sales = [];
    private $distributions = [];
    private $productions = [];

    public function setShoes(Shoes $shoe)
    {
        $this->shoes[] = $shoe;
    }

    public function getShoes()
    {
        return $this->shoes;
    }

    public function setStores(Store $store)
    {
        $this->stores[] = $store;
    }

    public function getStores()
    {
        return $this->stores;
    }

    public function setSales(Sale $sale)
    {
        $this->sales[] = $sale;
    }

    public function getSales()
    {
        return $this->sales;
    }
    
    public function setDistributions(Distribution $distribution)
    {
        $this->distributions[] = $distribution;
    }

    public function getDistributions()
    {
        return $this->distributions;
    }

    public function setProductions(Production $production)
    {
        $this->productions[] = $production;
    }

    public function getProductions()
    {
        return $this->productions;
    }
}