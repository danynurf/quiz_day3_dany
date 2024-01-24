<?php

require_once "util.php";
require_once "NikiShoes.php";
require_once "Production.php";
require_once "service.php";

class HomeView
{
    private NikiShoes $nikiShoes;
    private ProductionService $productionService;
    private ProductionView $productionView;
    private DistributionView $distributionView;
    private StoreView $storeView;

    public function __construct() 
    {
        $this->nikiShoes = new NikiShoes();
        $this->productionService = new ProductionService();
        $this->productionView = new ProductionView($this->productionService);
        $this->distributionView = new DistributionView($this->productionService, $this->productionView, $this->storeView);
        $this->storeView = new StoreView();
        
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
                // distributionPlanning();
                break;
            case 6:
                // checkDistribution();
                break;
            case 7:
                // showDistributionList();
                break;
            case 8:
                // distributionHistory();
                break;
            case 9:
                // checkShoesAvailabilty();
                break;
            case 10:
                // dailySalesReporting();
                break;
            case 11:
                // salesReport();
                break;
            case 12:
                // addStore();
                break;
            case 13:
                // updateStoreInformation();
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

    public function getProductionService()
    {
        return $productionService;
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
        
        if($idx == -1)
        {
            echo "\nThere is no production work with that ID\n";
            return;
        }

        $production = $nikiShoes->getProductions()[$idx];
        $defectsAmount = 0;
        
        if($production->getPhase() != 'Planning')
        {
            $defectsAmount = scannerNumber('How many defects');
        }

        $production->setDefectsAmount($defectsAmount);

        if($defectsAmount > $production->getCurrentQty())
        {
            $defectsAmount = $production->getCurrentQty();

            echo<<<show

            Defects amount is bigger than Current Quantity
            Defects amount is set to {$defectsAmount}
            
            show;
        }
        $production->calcCurrentQty($defectsAmount);

        if($production->getCurrentQty() == 0)
        {
            echo<<<show

            Production failed
            Curent production quantity is 0
            
            show;
            $production->setPhase('Failed');
            return;
        }

        $this->productionService->updateProductionPhase($nikiShoes, $prodID, $idx);

        if($production->getPhase() != 'Finish')
        {
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

        $productPriceHint = $production->getCostOfGoodSold() / $production->getCurrentQuantity();
        echo "Product Price Hint : {$producPriceHint}";

        $productPrice = scannerNumber('Enter Product Price');

        $shoe = new Shoes();
        $shoe->setCategory($production->getShoeCategory());
        $shoe->setModelName($production->getModelName());
        $shoe->setStock($production->getCurrentQty());
        $shoe->setPrice($productPrice);
        $nikiShoes->setShoes($shoe);

        echo "\nThe production is Finish\n";
    }

    public function productionHistory()
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

        foreach($productions as $production)
        {
            if($production->getPhase() == $phase)
            {
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
    private ProductionView $productionView;
    private StoreView $storeView;

    public function __construct(ProductionService $productionService, ProductionView $productionView, StoreView $storeView)
    {
        $this->productionService = $productionService;
        $this->productionView = $productionView;
    }
    
    public function distributionPlanning(NikiShoes $nikiShoes)
    {
        $productions = $nikiShoes->getProductions();
        $isEmpty = $this->productionService->validateProductionPhase($nikiShoes, 'Finish');

        if($isEmpty || count($productions) == 0)
        {
            echo "\nThere are no products ready for distribution\n";
            return;
        }

        echo "\nDistribution Planning\n";
        $this->productionView->showProductionByPhase($nikiShoes, 'Finish');
        echo "\nSelect the product you want to distribute\n";
        $prodID = readline('Enter Production ID : ');
        $idx = $this->productionService->findProductionIDbyPhase($nikiShoes, $prodID, 'Finish');

        if($idx == -1)
        {
            echo "\nInvalid Production ID\n";
            return;
        }

        $distribution = new Distribution();
        $stores = $nikiShoes->getStores();
        
        $this->storeView->showAllStore($nikiShoes);
    }
}

class StoreView
{
    public function showAllStore(NikiShoes $nikiShoes)
    {
        echo "\nStore List\n";
        $stores = $nikiShoes->getStores();

        foreach($stores as $store)
        {
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