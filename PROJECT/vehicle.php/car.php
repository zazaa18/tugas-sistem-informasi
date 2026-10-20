<?php
require_once 'vehicle.php';

class Car extends Vehicle {
    private $numDoors;

    public function __construct($brand, $model, $year, $numDoors) {
        parent::__construct($brand, $model, $year);
        $this->numDoors = $numDoors;
    }

    public function getInfo() {
        return parent::getInfo() .
               "Number of Doors: " . $this->numDoors . "<br>";
    }
}
?>