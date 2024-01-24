<?php

class Process 
{
    private string $ID;
    private string $phase;
    private string $startDate;
    private string $endDate = '';

    public function setID(string $ID) 
    {
        $this->ID = $ID;
    }

    public function getID()
    {
        return $this->ID;
    }

    public function setPhase(string $phase)
    {
        $this->phase = $phase;
    }

    public function getPhase()
    {
        return $this->phase;
    }

    public function setStartDate(string $startDate)
    {
        $this->startDate = $startDate;
    }

    public function getStartDate() 
    {
        return $this->startDate;
    }

    public function setEndDate(string $endDate)
    {
        $this->endDate = $endDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }
}