<?php
require_once "vehicle.php";

class Truck extends Vehicle {
    private $capacity;

    public function __construct($brand, $model, $year, $capacity) {
        parent::__construct($brand, $model, $year);
        $this->capacity = $capacity;
    }

    public function getInfo() {
        return parent::getInfo() .
               "Capacity: {$this->capacity} kg<br><br>";
    }
}
?>