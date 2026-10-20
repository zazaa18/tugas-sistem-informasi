<?php
class Vehicle {
    protected $brand;
    protected $model;
    protected $year;

    public function __construct($brand, $model, $year) {
        $this->brand = $brand;
        $this->model = $model;
        $this->year = $year;
    }

    public function getInfo() {
        return "Brand: " . $this->brand . "<br>" .
               "Model: " . $this->model . "<br>" .
               "Year: " . $this->year . "<br>";
    }
}
?>