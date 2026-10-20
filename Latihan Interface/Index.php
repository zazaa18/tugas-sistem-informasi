<?php
// Include semua file
require_once "LatihanInterface.php";
require_once "multiple interface.php";
require_once "PolymorfismInterface.php";

// Contoh penggunaan tambahan (optional biar kelihatan semua jalan)

// Motor (multiple interface)
$motor = new Motor();
echo $motor->hidupkan();
echo "<br>";
echo $motor->berjalan();
echo "<br><br>";

// Pembayaran (polymorphism sudah jalan di file PolymorfismInterface.php)
?>