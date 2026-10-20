<?php
require_once "car.php";
require_once "motorcycle.php";
require_once "truck.php";

$car = new Car("Honda", "Civic", 2022, 4);
$motor = new Motorcycle("Yamaha", "Nmax", 2021, "Scooter");
$truck = new Truck("Hino", "Ranger", 2019, 5000);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Kendaraan</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f4f6f9;
        }
        .container {
            width: 600px;
            margin: 30px auto;
        }
        .card {
            background: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0px 3px 8px rgba(0,0,0,0.1);
        }
        h2 {
            margin-top: 0;
            color: #333;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>🚗 Data Kendaraan</h1>

    <div class="card">
        <h2>Mobil</h2>
        <?php echo $car->getInfo(); ?>
    </div>

    <div class="card">
        <h2>Motor</h2>
        <?php echo $motor->getInfo(); ?>
    </div>

    <div class="card">
        <h2>Truk</h2>
        <?php echo $truck->getInfo(); ?>
    </div>
</div>

</body>
</html>