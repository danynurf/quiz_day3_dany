<?php

require_once "view.php";

$main = new class
{
    public function __construct()
    {
        $homeView = new HomeView();
        $homeView->showMainMenu();
    }
};