<?php

class Shoes 
{
    private string $category;
    private string $modelName;
    private int $price;
    private int $stock;

    public function setCategory(string $category)
    {
        $this->category = $category;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setModelName(string $modelName)
    {
        $this->modelName = $modelName;
    }

    public function getModelName()
    {
        return $this->modelName;
    }

    public function setPrice(int $price)
    {
        $this->price = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setStock(int $stock)
    {
        $this->stock = $stock;
    }

    public function getStock()
    {
        return $this->stock;
    }
}