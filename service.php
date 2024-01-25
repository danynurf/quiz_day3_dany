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

        for($i = 0; $i < count($productions); $i++) {
            if(
                $productions[$i]->getPhase() == $phase && $productions[$i]->getID() == $prodID) {
                return $i;
            }
        }
        return -1;
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

class DistributionService
{
    public function validateDistributionPhase(NikiShoes $nikiShoes, string $phase)
    {
        $distributions = $nikiShoes->getDistributions();

        foreach($distributions as $dis) {
            if($dis->getPhase() == $phase) return false;
        }
        return true;
    }

    public function findDistributionByPhase(NikiShoes $nikiShoes, string $phase, string $id)
    {
        $distributions = $nikiShoes->getDistributions();

        for($i = 0; $i < count($distributions); $i++) {
            if($distributions[$i]->getPhase() == $phase && $distributions[$i]->getID() == $id) {
                return $i;
            }
        }
        return -1;
    }

    public function getDistributionPhase(int $phase)
    {
        switch ($phase) {
            case 1: return 'Packing';
            case 2: return 'On Delivery';
        }
    }
    
    public function validateDistribution(NikiShoes $nikiShoes, string $storeID)
    {
        $distributions = $nikiShoes->getDistributions();
        for($i = 0; $i < count($distributions); $i++) {
            $store = $distributions[$i]->getStore();
            if($store->getStoreID() == $storeID && $distributions[$i]->getPhase() == 'Packing') {
                return $i;
            }
        }
        return -1;
    }

    public function updateDistributionPhase(NikiShoes $nikiShoes, string $id, int $idx)
    {
        $phase = $nikiShoes->getDistributions()[$idx]->getPhase();

        switch ($phase) {
            case 'Packing': return 'On Delivery';
            case 'On Delivery': return 'Arrived';
        }
    }
}

class StoreService
{
    public function findStoreByID(NikiShoes $nikiShoes, string $storeID)
    {
        $stores = $nikiShoes->getStores();

        for($i = 0; $i < count($stores); $i++) {
            if($storeID == $stores[$i]) return $i;
        }

        return -1;
    }

    public function findShoeInStore(NikiShoes $nikiShoes, Store $store, string $modelName)
    {
        $shoes = $store->getShoes();

        for($i = 0; $i < count($shoes); $i++) {
            if($shoes[$i]->getModelName() == $modelName) return $i;
        }
        return -1;
    }

    public function validateStoreID(NikiShoes $nikiShoes, string $id)
    {
        $stores = $nikiShoes->getStores();
        foreach($stores as $store) {
            if($store->getStoreID() == $id) {
                return true;
            }
        }
        return false;
    }

    public function validateShoeModelName(NikiShoes $nikiShoes, Store $store, string $modelName)
    {
        $shoes = $store->getShoes();
        for($i = 0; $i < count($shoes); $i++) {
            if($shoes[$i]->getModelName == $modelName) return $i;
        }
        return -1;
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