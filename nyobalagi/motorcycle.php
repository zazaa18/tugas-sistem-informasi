<?php
require_once "vehicle.php";

class Motorcycle extends Vehicle {
    private $type;

    public function __construct($brand, $model, $year, $type) {
        parent::__construct($brand, $model, $year);
        $this->type = $type;
    }

    public function getInfo() {
        return parent::getInfo() .
               "Type: {$this->type}<br><br>";
    }
}
?>