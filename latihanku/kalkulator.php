<?php

class Kalkulator {

    public function tambah($a, $b) {
        return $a + $b;
    }

    public function kurang($a, $b) {
        return $a - $b;
    }

    public function kali($a, $b) {
        return $a * $b;
    }

    public function bagi($a, $b) {
        if ($b == 0) {
            return "Tidak bisa dibagi 0!";
        }
        return $a / $b;
    }
}

$kalkulator = new Kalkulator();

echo "Tambah: " . $kalkulator->tambah(10, 5) . "<br>";
echo "Kurang: " . $kalkulator->kurang(10, 5) . "<br>";
echo "Kali: " . $kalkulator->kali(10, 5) . "<br>";
echo "Bagi: " . $kalkulator->bagi(10, 5);

?>