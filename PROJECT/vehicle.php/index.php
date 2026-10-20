<?php
require_once 'car.php';
require_once 'motorcycle.php';
require_once 'truck.php';

$car = new Car("Honda", "Civic", 2022, 4);
$motor = new Motorcycle("Yamaha", "Nmax", 2021, "Scooter");
$truck = new Truck("Hino", "Ranger", 2019, 5000);

echo $car->getInfo();
echo "<br>";

echo $motor->getInfo();
echo "<br>";

echo $truck->getInfo();
?>