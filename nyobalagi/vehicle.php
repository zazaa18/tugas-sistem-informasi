<?php
class Vehicle {
    protected $brand;
    protected $model;
    protected $year;

    // Constructor
    public function __construct($brand, $model, $year) {
        $this->brand = $brand;
        $this->model = $model;
        $this->year = $year;
    }

    // Method untuk menampilkan info kendaraan
    public function getInfo() {
        return "Brand: {$this->brand}<br>
                Model: {$this->model}<br>
                Year: {$this->year}<br>";
    }
}
?>