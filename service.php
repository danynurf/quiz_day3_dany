<?php

require_once "NikiShoes.php";
require_once "Store.php";

class ProductionService 
{
    public function validateProductionPhase(NikiShoes $nikiShoes, string $phase)
    {
        $productions = $nikiShoes->getProductions();
        foreach($productions as $production) {
            if($production->getPhase() == $phase) {
                return false;
            }   
        }
        return true;
    }

    public function getProductionPhase(int $phase)
    {
        switch ($phase) {
            case 1: return 'Planning';
            case 2: return 'Cutting';
            case 3: return 'Sewing';
            case 4: return 'Outsole';
            case 5: return 'Insole';
            case 6: return 'Assembling';
            case 7: return 'Finishing';
        }
    }

    public function findProductionIDbyPhase(NikiShoes $nikiShoes, string $prodID, string $phase)
    {
        $productions = $nikiShoes->getProductions();
        $idx = -1;

        for($i = 0; $i < count($productions); $i++)
        {
            if($productions[$i]->getPhase() == $phase && $productions[$i]->getID() == $prodID)
            {
                $idx = $i;
            }
        }
        return $idx;
    }

    public function updateProductionPhase(NikiShoes $nikiShoes, string $prodID, int $idx)
    {
        $production = $nikiShoes->getProductions()[$idx];
        $phase = $production->getPhase();

        switch ($phase) {
            case 'Planning':
                $production->setPhase('Cutting');
                break;
            case 'Cutting':
                $production->setPhase('Sewing');
                break;
            case 'Sewing':
                $production->setPhase('Outsole');
                break;
            case 'Outsole':
                $production->setPhase('Insole');
                break;
            case 'Insole':
                $production->setPhase('Assembling');
                break;
            case 'Assembling':
                $production->setPhase('Finishing');
                break;
            default:
                $production->setPhase('Finish');
                break;
        }
    }
}

class SeedingService
{
    public function seedStore(NikiShoes $nikiShoes)
    {
        $store1 = new Store("TGR", "Niki Shoes Tangerang", "Jl. Boulevard, Tangerang", "Dessy Maharani", 12);
        $nikiShoes->setStores($store1);

        $store2 = new Store("TGL", "Niki Shoes Tegal", "Jl. Mawar, Tegal", "Dany Nur Ferdiansyah", 8);
        $nikiShoes->setStores($store2);

        $store3 = new Store("BDG", "Niki Shoes Bandung", "Jl. Kakatua, Bandung", "Deddy Mahendra", 11);
        $nikiShoes->setStores($store3);
    }
}