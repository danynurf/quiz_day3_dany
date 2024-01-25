<?php

require_once "util.php";
require_once "NikiShoes.php";
require_once "Production.php";
require_once "Distribution.php";
require_once "Store.php";
require_once "Shoes.php";
require_once "Sale.php";
require_once "service.php";

class HomeView
{
    private NikiShoes $nikiShoes;
    private ProductionService $productionService;
    private StoreService $storeService;
    private ProductionView $productionView;
    private DistributionView $distributionView;
    private StoreView $storeView;

    public function __construct() 
    {
        $this->nikiShoes = new NikiShoes();
        $this->productionService = new ProductionService();
        $this->storeService = new StoreService();

        $this->productionView = new ProductionView($this->productionService);
        $this->storeView = new StoreView($this->storeService);
        $this->distributionView = new DistributionView($this->productionService, $this->productionView, $this->storeView, $this->storeService);
        
        $seedingService = new SeedingService;
        $seedingService->seedStore($this->nikiShoes);
    }

    public function showMainMenu()
    {
        echo<<<show

        NIKI SHOES

        1.  Production Planning
        2.  Check Production
        3.  Show Production List
        4.  Production History
        5.  Distribution Planning
        6.  Check Distribution
        7.  Show Distribution List
        8.  Distribution History
        9.  Daily Sales Reporting
        10. Check Shoes Availability
        11. Sales Report
        12. Add Store
        13. Update Store Information
        14. Show All Store
        15. Quit Program
        
        show;

        $choosenMenu = scanner('Choose Menu', 15);

        switch ($choosenMenu) {
            case 1:
                $this->productionView->productionPlanning($this->nikiShoes);
                break;
            case 2:
                $this->productionView->checkProduction($this->nikiShoes);
                break;
            case 3:
                $this->productionView->showProductionList($this->nikiShoes);
                break;
            case 4:
                $this->productionView->productionHistory($this->nikiShoes);
                break;
            case 5:
                $this->distributionView->distributionPlanning($this->nikiShoes);
                break;
            case 6:
                $this->distributionView->checkDistribution($this->nikiShoes);
                break;
            case 7:
                $this->distributionView->showDistributionList($this->nikiShoes);
                break;
            case 8:
                $this->distributionView->distributionHistory($this->nikiShoes);
                break;
            case 9:
                $this->storeView->checkShoesAvailabilty($this->nikiShoes);
                break;
            case 10:
                $this->storeView->dailySalesReporting($this->nikiShoes);
                break;
            case 11:
                $this->storeView->salesReport($this->nikiShoes);
                break;
            case 12:
                $this->storeView->addStore($this->nikiShoes);
                break;
            case 13:
                $this->storeView->updateStoreInformation($this->nikiShoes);
                break;
            case 14:
                $this->storeView->showAllStore($this->nikiShoes);
                break;
            case 15:
                exit("\nThank You\n");
        }
        $this->showMainMenu();
    }
}

class ProductionView
{
    private ProductionService $productionService;

    public function __construct(ProductionService $productionService)
    {
        $this->productionService = $productionService;
    }

    public function productionPlanning(NikiShoes $nikiShoes)
    {
        $production = new Production();
        
        echo "\nProduction Planning\n";

        echo "Example Start Date : 1 Januari 2024\n";
        $startDate = readline('Enter Start Date : ');
        $production->setStartDate($startDate);
        
        $materialCost = scannerNumber('Enter Material Cost');
        $production->setMaterialCost($materialCost);

        echo<<<show

        Choose the category you want to produce
        1. Men's Shoe
        2. Women's Shoe
        3. Kids Shoe
        4. Sport Shoe
        5. Sneakers
        
        show;
        $category = scanner('Choose Category', 5);
        $production->setShoeCategory($category);

        $modelName = readline('Enter Model Name : ');
        $production->setModelName($modelName);

        $quantityTarget = scannerNumber('Enter the quantity target');
        $production->setQtyTarget($quantityTarget);
        $production->setCurrentQty($quantityTarget);

        static $ID = 1;
        $production->setID($ID);
        echo "\nThe Production ID is set to " . $production->getID() . "\n";

        $production->setPhase('Planning');
        $nikiShoes->setProductions($production);
        $ID++;
    }

    public function checkProduction(NikiShoes $nikiShoes)
    {
        echo<<<show

        Check Production

        What phase will you check
        1. Planning
        2. Cutting
        3. Sewing
        4. Outsole
        5. Insole
        6. Assembling
        7. Finishing
        
        show;
        
        $phase = scanner('Choose Phase', 7);
        $phase = $this->productionService->getProductionPhase($phase);
        $isEmpty = $this->productionService->validateProductionPhase($nikiShoes, $phase);

        if($isEmpty) {
            echo "\nThere is no production in this phase\n";
            return;
        }

        $this->showProductionByPhase($nikiShoes, $phase);

        echo "\nEnter Production ID you want to check\n";
        $prodID = readline('Enter Production ID : ');
        $idx = $this->productionService->findProductionIDbyPhase($nikiShoes, $prodID, $phase);
        
        if($idx == -1) {
            echo "\nThere is no production work with that ID\n";
            return;
        }

        $production = $nikiShoes->getProductions()[$idx];
        $defectsAmount = 0;
        
        if($production->getPhase() != 'Planning') {
            $defectsAmount = scannerNumber('How many defects');
        }

        $production->setDefectsAmount($defectsAmount);

        if($defectsAmount > $production->getCurrentQty()) {
            $defectsAmount = $production->getCurrentQty();

            echo<<<show

            Defects amount is bigger than Current Quantity
            Defects amount is set to {$defectsAmount}
            
            show;
        }
        $production->calcCurrentQty($defectsAmount);

        if($production->getCurrentQty() == 0) {
            echo<<<show

            Production failed
            Curent production quantity is 0
            
            show;
            $production->setPhase('Failed');
            return;
        }

        $this->productionService->updateProductionPhase($nikiShoes, $prodID, $idx);

        if($production->getPhase() != 'Finish') {
            echo "\nThe production moves to the {$production->getPhase()} phase\n";
            return;
        }

        echo "\nExample End Date : 7 Dec 2023\n";
        $endDate = readline('Enter End Date : ');
        $production->setEndDate($endDate);

        $overheadCost = scannerNumber('Enter Overhead Cost');
        $production->setOverheadCost($overheadCost);
        $production->setTotalProductionCost();
        $production->setCostOfGoodSold();

        $productPriceHint = $production->getCostOfGoodSold() / $production->getCurrentQty();
        echo "\nProduct Price Hint : {$productPriceHint}\n";

        $productPrice = scannerNumber('Enter Product Price');

        $shoe = new Shoes();
        $shoe->setCategory($production->getShoeCategory());
        $shoe->setModelName($production->getModelName());
        $shoe->setStock($production->getCurrentQty());
        $shoe->setPrice($productPrice);
        $nikiShoes->setShoes($shoe);

        echo "\nThe production is Finish\n";
    }

    public function productionHistory(NikiShoes $nikiShoes)
    {
        $this->showProductionByPhase($nikiShoes, 'Finish');
        $this->showProductionByPhase($nikiShoes, 'Failed');
    }
    
    public function showProductionList(NikiShoes $nikiShoes)
    {
        $productions = $nikiShoes->getProductions();

        if(count($productions) == 0) {
            echo "\nThere are no production is in progress\n";
        } else {
            echo "\nProduction List\n";
        }
        
        $this->showProductionByPhase($nikiShoes, 'Planning');
        $this->showProductionByPhase($nikiShoes, 'Cutting');
        $this->showProductionByPhase($nikiShoes, 'Sewing');
        $this->showProductionByPhase($nikiShoes, 'Outsole');
        $this->showProductionByPhase($nikiShoes, 'Insole');
        $this->showProductionByPhase($nikiShoes, 'Assembling');
        $this->showProductionByPhase($nikiShoes, 'Finishing');
    }

    public function showProductionByPhase(NikiShoes $nikiShoes, string $phase)
    {
        $productions = $nikiShoes->getProductions();
        $isEmpty = $this->productionService->validateProductionPhase($nikiShoes, $phase);

        if($isEmpty) {
            if($phase == 'Finish' && $phase == 'Failed')
                echo "\nThere is no production history yet\n";
            return;
        }

        switch ($phase) {
            case 'Finish':
                echo "\nProduction History\n";
                break;
            case 'Failed':
                echo "\nProduction Failed\n";
                break;
            default:
                echo "\nProduction List in $phase Phase\n";
                break;
        }

        foreach($productions as $production) {
            if($production->getPhase() == $phase) {
                echo<<<show
                
                ID : {$production->getID()}
                Start Date       : {$production->getStartDate()} 
                End Date         : {$production->getEndDate()}
                Model Name       : {$production->getModelName()}
                Quantity Target  : {$production->getQtyTarget()}
                Current Quantity : {$production->getCurrentQty()}
                Defects Amount   : {$production->getDefectsAmount()}
                ----------------------------------
                
                show;
            }
        }
    }
}

class DistributionView 
{
    private ProductionService $productionService;
    private DistributionService $distributionService;
    private ProductionView $productionView;
    private StoreView $storeView;
    private StoreService $storeService;

    public function __construct(ProductionService $productionService, ProductionView $productionView, StoreView $storeView, StoreService $storeService)
    {
        $this->productionService = $productionService;
        $this->productionView = $productionView;
        $this->storeView = $storeView;
        $this->storeService = $storeService;
        $this->distributionService = new DistributionService();
    }
    
    public function distributionPlanning(NikiShoes $nikiShoes)
    {
        $productions = $nikiShoes->getProductions();
        $isEmpty = $this->productionService->validateProductionPhase($nikiShoes, 'Finish');

        if($isEmpty || count($productions) == 0) {
            echo "\nThere are no products ready for distribution\n";
            return;
        }

        echo "\nDistribution Planning\n";
        $this->productionView->showProductionByPhase($nikiShoes, 'Finish');
        echo "\nSelect the product you want to distribute\n";
        $prodID = readline('Enter Production ID : ');
        $idxPro = $this->productionService->findProductionIDbyPhase($nikiShoes, $prodID, 'Finish');

        if($idxPro == -1) {
            echo "\nInvalid Production ID\n";
            return;
        }

        $distribution = new Distribution();
        $stores = $nikiShoes->getStores();
        
        $this->storeView->showAllStore($nikiShoes);
        echo "\nEnter the destination Store ID\n";
        $storeID = readline('Enter ID : ');
        $idxSto = $this->storeService->findStoreByID($nikiShoes, $storeID);

        if($idxSto == -1) {
            echo "\nInvalid Store ID\n";
            return;
        }
        $distribution->setStore($store[$idxSto]);

        echo "\nEnter the quantity of products you want to distribute\n";
        $distributionQty = scannerNumber('Quantity');
        $currentQty = $productions[$idxPro]->getCurrentQty();

        if($currentQty < $distributionQty) {
            $distributionQty = $currentQty;
            echo "\nThe Distribution Quantity is set to {$distributionQty}\n";
        }
        
        $currentQty -= $distributionQty;
        $productions[$idxPro]->setCurrentQty($currentQty);
        $idxDis = $this->distributionService->validateDistribution($nikiShoes, $idxSto);

        if($idxDis > -1) {
            $dis = $nikiShoes->getDistribution()[$idxDis];
            $newQty = $dis->getDistributionQty() + $distributionQty;
            $dis->setDistributionQty($newQty);
            return;
        }

        $startDate = readline('Enter Start Date : ');
        $distribution->setStartDate($startDate);

        static $ID = 1;
        $distribution->setID($ID);

        $distribution->setShoe($productions[$idxPro]->getShoe());
        $distribution->setDistributionQty($distributionQty);
        $distribution->setPhase('Packing');
        $nikiShoes->setDistributions($distribution);

        echo "\nDistribution plan added succesfully with status Packing\n";
        $ID++;
    }

    public function checkDistribution(NikiShoes $nikiShoes)
    {
        echo <<<show

        Check Distribution

        What phase will you check
        1. Packing
        2. On Delivery

        
        show;

        $phase = scanner('Choose', 2);
        $phase = $this->distributionService->getDistributionPhase($phase);
        $isEmpty = $this->distributionService->validateDistributionPhase($nikiShoes, $phase);
        
        if($isEmpty) {
            echo "\nThere is no distribution in this phase\n";
            return;
        }
        
        $this->showDistributionByPhase($nikiShoes, $phase);
        echo "\nEnter Distribution ID you want to check\n";
        $idDis = readline('Enter : ');

        $idxDis = $this->distributionService->findDistributionByPhase($nikiShoes, $phase, $idDis);
        if($idxDis == -1) {
            echo "\nThere is no distribution work with that ID\n";
            return;
        }

        $this->distributionService->updateDistributionPhase($nikiShoes, $idDis, $idxDis);
        $distribution = $nikiShoes->getDistributions()[$idx];

        if($distribution->getPhase() != 'Arrived') {
            echo "\nThe distribution moves to {$distribution->getPhase()} phase\n";
            return;
        }

        echo "\nExample End Date : 14 Dec 2023\n";
        $endDate = readline('Enter End Date : ');
        $distribution->setEndDate($endDate);

        $idSto = $distribution->getStore()->getStoreID();
        $idxSto = $this->storeService->findStoreByID($nikiShoes, $idSto);
        $store = $nikiShoes->getStores()[$idxSto];

        $shoe = $nikiShoes->getShoe();
        $idxShoe = $this->storeService->findShoeInStore($nikiShoes, $store, $shoe->getModelName());

        if($idxShoe == -1) {
            $stock = $distribution->getDistributionQty();
            $shoesInStore = $store->getShoes();
            $store->setShoes($shoe);
            $store->getShoes[count($shoesInStore) - 1]->setStock($stock);
        } else {
            $shoeStockInStore = $store->getShoes()[$idxShoe]->getStock();
            $stock = $distribution->getDistributioQty();
            $newStock = $shoeStockInStore + $stock;
            $store->getShoes()[$idxShoe]->setStock($newStock);
        }

        echo "\nThe product is arrived to the destination store\n";
    }

    public function showDistributionList(NikiShoes $nikiShoes)
    {
        $distributions = $nikiShoes->getDistributions();
        if(count($distributions) == 0) {
            echo "\nThere are no distributon is in progress\n";
            return;
        }

        echo "\nDistribution List\n";

        $this->showDistributionByPhase($nikiShoes, 'Packing');
        $this->showDistributionByPhase($nikiShoes, 'On Delivery');
    }

    public function distributionHistory(NikiShoes $nikiShoes)
    {
        $this->showDistributionByPhase($nikiShoes, 'Arrived');
    }

    public function showDistributionByPhase(NikiShoes $nikiShoes, string $phase)
    {
        $distributions = $nikiShoes->getDistributions();
        $isEmpty = $this->distributionService->validateDistributionPhase($nikiShoes, $phase);

        if(count($distributions) == 0) {
            if($phase == 'Arrived') {
                echo "\nThere is no distribution history yet\n";
                return;
            }
        }

        if($phase == 'Arrived') {
            echo "\nDistribution History\n";
        } else {
            echo "\nDistribution List in {$phase} Phase\n";
        }

        foreach($distributions as $dis) {
            if($dis->getPhase() == $phase) {
                echo<<<show

                Distribution ID   : {$dis->getID()}
                Delivery Date     : {$dis->getStartDate()}
                Arrived Date      : {$dis->getEndDate()}
                Quantity Product  : {$dis->getDistributionQty()}
                Model Shoe        : {$dis->getShoe()->getModelName()}
                Shoe Category     : {$dis->getShoe()->getCategory()}

                Store ID          : {$dis->getStore()->getStoreID()}
                Store Name        : {$dis->getStore()->getStoreName()}
                Store Address     : {$dis->getStore()->getAddress()}
                
                show;
            }
        }
    }
}

class StoreView
{
    private StoreService $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }
    
    public function checkShoesAvailabilty(NikiShoes $nikiShoes)
    {
        echo "\nAvailabililty of Shoes in Several Stores\n";
        $stores = $nikiShoes->getStores();
        $this->showAllStore($nikiShoes);
        
        echo "\nEnter the Store ID you want to check\n";
        $idSto = readline('Enter ID : ');
        $idxSto = $this->storeService->findStoreByID($nikiShoes, $idSto);

        if($idxSto == -1) {
            echo "\nInvalid Store ID\n";
            return;
        }

        echo "\nAvailability Shoes in {$store[$idxSto]->getStoreName()}\n";
        $this->showShoesByStore($nikiShoes, $idxSto);
    }

    public function showShoesByStore(NikiShoes $nikiShoes, int $idx)
    {
        $shoes = $nikiShoes->getStores()[$idx]->getShoes();

        if(count($shoes) == 0) {
            echo "\nThere are no shoes available in this store\n";
            return;
        }

        foreach($shoes as $shoe) {
            echo<<<show

            Model Name : {$shoe->getModelName()}
            Category   : {$shoe->getCategory()}
            Price      : IDR {$shoe->getPrice()}
            Stock      : {$shoe->getStock()}
            ----------------------------------

            show;
        }
    }

    public function dailySalesReporting(NikiShoes $nikiShoes)
    {
        $sale = new Sale();
        echo "\nSales Reporting in Several Stores\n";
        $this->showAllStore($nikiShoes);
        
        echo "\nEnter the Store ID whose sales report will be recorded\n";
        $idSto = readline('Enter Store ID : ');
        $idx = $this->storeService->findStoreByID($nikiShoes, $idSto);

        if($idx == -1) {
            echo "\nInvalid Store ID\n";
            return;
        }

        $store = $nikiShoes->getStores()[$idx];
        if(count($store->getShoes()) == 0) {
            echo "\nThere are no shoes in this store\n";
            return;
        }

        $sale->setStore($store);
        echo "\nAvailability Shoes in {$store->getStoreName()}\n";
        $this->showShoesByStore($nikiShoes, $idx);

        echo "\nEnter the name of the shoe model sold today\n";
        $modelName = readline('Model Name : ');
        $idxShoe = $this->storeService->validateShoeModelName($nikiShoes, $store, $modelName);

        if($idxShoe == -1) {
            echo "\nThere are no product with this model name\n";
            return;
        }

        $shoe = $store->getShoes()[$idxShoe];
        if($shoe->getStock() == 0) {
            echo "\nOut of stock\n";
            return;
        }

        $sale->setShoe($shoe);
        $saleQty = scannerNumber('Enter sale quantity');

        if($saleQty > $shoe->getStock()) {
            $saleQty = $shoe->getStock();
            echo "\nSale Quantity is bigger than shoe stock\n";
            echo "\nSale Quantity is set to $saleQty\n";
        }

        $stock = $shoe->getStock();
        $newQty = $stock - $saleQty;
        $shoe->setStock($newQty);

        $price = $shoe->getPrice();
        $sale->setSaleQty($saleQty);
        $sale->setTotalSale($price);

        echo "\nExample date : 1 Jan 2024\n";
        $saleDate = readline('Enter date   : ');
        $sale->setSaleDate($saleDate);
        $store->setSales($sale);
        $nikiShoes->setSales($sale);
    }

    public function salesReport(NikiShoes $nikiShoes)
    {
        echo "\nSales Report in several stores\n";
        $stores = $nikiShoes->getStores();
        $this->showAllStore($nikiShoes);

        echo "\nEnter the Store ID you want to check\n";
        $idSto = readline('Enter ID : ');
        $idx = $this->storeService->findStoreByID($nikiShoes, $idSto);

        if($idx == -1) {
            echo "\nInvalid Store ID\n";
            return;
        }

        echo "\nSales Report\n";
        $sales = $nikiShoes->getStores()[$idx]->getSales();

        foreach($sales as $sale) {
            echo<<<show

            Sale Date     : {$sale->getSaleDate()}
            Model Name    : {$sale->getShoe()->getModelName()}
            Shoe Category : {$sale->getShoe()->getCategory()}
            Shoe Price    : IDR {$sale->getShoe()->getPrice()}
            Sale Quantity : {$sale->getSaleQty()}
            Total Sales   : IDR {$sale->getTotalSale()}
            ----------------------------------
            
            show;
        }
    }

    public function addStore(NikiShoes $nikiShoes)
    {
        echo "\nAdd New Store\n";

        $newStore = new Store();
        $idSto = readline('Enter Store ID : ');
        $isAny = $this->storeService->validateStoreID($nikiShoes, $idSto);

        if($isAny) {
            echo "\nThe store ID already exis\n";
            return;
        }
        $newStore->setStoreID($idSto);

        $storeName = readline('Enter Store Name  : ');
        $newStore->setStoreName($storeName);

        $address = readline('Enter Store Address  : ');
        $newStore->setAddress($address);

        $totalEmp = scannerNumber('Employee Amount   ');
        $newStore->setTotalEmployee($totalEmp);

        $nikiShoes->setStores($newStore);
        echo "\nAdd new store was successfully\n";
    }

    public function updateStoreInformation(NikiShoes $nikiShoes)
    {
        echo "\nUpdate Store Information\n";
        $this->showAllStore($nikiShoes);

        $idSto = readline('Enter Store ID : ');
        $idxSto = $this->storeService->findStoreByID($nikiShoes, $idSto);

        if($idxSto == -1) {
            echo "\nStore ID is invalid\n";
            return;
        }

        $store = $nikiShoes->getStores()[$idxSto];
        $confirm = 'y';

        while($confirm == 'y') {
            echo<<<show

            Choose information you want to edit
            1. Edit Store Name
            2. Edit Address
            3. Edit Leader Name
            4. Edit Employee Amount
            
            show;
            $choosen = scanner('Choose', 4);

            switch ($choosen) {
                case 1:
                    $storeName = readline('Update Store Name : ');
                    $store->setStoreName($storeName);
                    break;
                case 2:
                    $address = readline('Update Address : ');
                    $store->setAddress($address);
                    break;
                case 3:
                    $leaderName = readline('Update Leader Name : ');
                    $store->setLeaderName($leaderName);
                    break;
                case 4:
                    $totalEmp = scannerNumber('Update total employee');
                    $store->setTotalEmployee($totalEmp);
                    break;
            }
            $confirm = readline('Do you want to update information again? (y/n) : ');
        }
    }
    
    public function showAllStore(NikiShoes $nikiShoes)
    {
        echo "\nStore List\n";
        $stores = $nikiShoes->getStores();

        foreach($stores as $store) {
            echo<<<show

            Store ID        : {$store->getStoreID()}
            Store Name      : {$store->getStoreName()}
            Store Address   : {$store->getAddress()}
            Leader Name     : {$store->getLeaderName()}
            Employee Amount : {$store->getTotalEmployee()} Employee
            ----------------------------------
            
            show;
        }
    }
}