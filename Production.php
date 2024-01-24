<?php

require_once "Process.php";
require_once "Shoes.php";

class Production extends Process 
{
    private Shoes $shoes;
    private string $shoeCategory;
    private string $modelName;
    private int $quantityTarget;
    private int $currentQuantity = 0;
    private int $defectsAmount = 0;
    private int $totalEmployee = 0;
    private int $totalEmployeeCost = 0;
    private int $materialCost;
    private int $totalProductionCost = 0;
    private int $overheadCost = 0;
    private int $costOfGoodSold = 0;

    public function setID(string $ID) 
    {
        $prodID = "PRO-" . $ID;
        parent::setID($prodID);
    }

    public function setShoe(Shoes $shoe)
    {
        $this->shoes = $shoe;
    }

    public function getShoe()
    {
        return $this->shoes;
    }

    public function setShoeCategory(int $category)
    {
        switch ($category) {
            case 1:
                $this->shoeCategory = "Men's Shoe";
                break;
            case 2:
                $this->shoeCategory = "Women's Shoe";
                break;
            case 3:
                $this->shoeCategory = "Kids Shoe";
                break;
            case 4:
                $this->shoeCategory = "Sport Shoe";
                break;
            case 5:
                $this->shoeCategory = "Sneakers";
                break;
        }
    }

    public function getShoeCategory()
    {
        return $this->shoeCategory;
    }

    public function setModelName(string $modelName)
    {
        $this->modelName = $modelName;
    }

    public function getModelName()
    {
        return $this->modelName;
    }

    public function setCurrentQty($qty)
    {
        $this->currentQuantity = $qty;
    }

    public function calcCurrentQty(int $defect)
    {
        $this->currentQuantity -= $defect;
    }

    public function getCurrentQty()
    {
        return $this->currentQuantity;
    }

    public function setQtyTarget(int $qty)
    {
        $this->quantityTarget = $qty;
    }

    public function getQtyTarget()
    {
        return $this->quantityTarget;
    }

    public function setDefectsAmount(int $defect)
    {
        $this->defectsAmount += $defect;
    }

    public function getDefectsAmount()
    {
        return $this->defectsAmount;
    }

    public function setTotalEmployee(int $total)
    {
        $this->totalEmployee = $total;
    }

    public function getTotalEmployee()
    {
        return $this->totalEmployee;
    }

    public function setTotalEmployeeCost()
    {
        $this->totalEmployeeCost = $this->totalEmployee * 4000000;
    }

    public function getTotalEmployeeCost()
    {
        return $this->totalEmployeeCost;
    }

    public function setMaterialCost($cost)
    {
        $this->materialCost = $cost;
    }

    public function getMaterialCost()
    {
        return $this->materialCost;
    }

    public function setTotalProductionCost() 
    {
        $this->totalProductionCost = $this->materialCost + $this->totalEmployeeCost + $this->overheadCost;
    }

    public function getTotalProductionCost() 
    {
        return $this->totalProductionCost;
    }

    public function setOverheadCost($cost)
    {
        $this->overheadCost = $cost;
    }

    public function getOverheadCost()
    {
        return $this->overheadCost;
    }

    public function setCostOfGoodSold()
    {
        $this->costOfGoodSold = $this->totalProductionCost + (0.2 * $this->totalProductionCost);
    }

    public function getCostOfGoodSold()
    {
        return $this->costOfGoodSold;
    }
}