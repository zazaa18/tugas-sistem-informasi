<?php

class Pengunjung {

    public static $jumlah = 0;

    public function __construct() {
        self::$jumlah++;
    }

}

$p1 = new Pengunjung();
$p2 = new Pengunjung();
$p3 = new Pengunjung();

echo "Jumlah Pengunjung: " . Pengunjung::$jumlah;

?>