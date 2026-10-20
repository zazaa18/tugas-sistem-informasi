<?php

class Matematika {

    public static function kali($a, $b) {
        return $a * $b;
    }

    public static function bagi($a, $b) {
        return $a / $b;
    }

    public static function tambah($a, $b) {
        return $a + $b;
    }

    public static function kurang($a, $b) {
        return $a - $b;
    }

    public static function luasPersegi($sisi) {
        return $sisi * $sisi;
    }
}

$hasil = "";
if (isset($_POST['hitung'])) {
    $a = $_POST['a'];
    $b = $_POST['b'];
    $sisi = $_POST['sisi'];

    $hasil = "
    <h3>Hasil Perhitungan</h3>

    <div style='border:1px solid black; padding:10px; margin-bottom:10px;'>
        <b>Perkalian</b><br>
        $a × $b = " . Matematika::kali($a, $b) . "
    </div>

    <div style='border:1px solid black; padding:10px; margin-bottom:10px;'>
        <b>Pembagian</b><br>
        $a ÷ $b = " . Matematika::bagi($a, $b) . "
    </div>

    <div style='border:1px solid black; padding:10px; margin-bottom:10px;'>
        <b>Penjumlahan</b><br>
        $a + $b = " . Matematika::tambah($a, $b) . "
    </div>

    <div style='border:1px solid black; padding:10px; margin-bottom:10px;'>
        <b>Pengurangan</b><br>
        $a - $b = " . Matematika::kurang($a, $b) . "
    </div>

    <div style='border:1px solid black; padding:10px;'>
        <b>Luas Persegi</b><br>
        Sisi = $sisi <br>
        Luas = " . Matematika::luasPersegi($sisi) . "
    </div>
    ";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Praktikum 2</title>
</head>
<body>

<h2>Program Matematika</h2>

<form method="POST">
    Nilai A: <input type="number" name="a" required><br><br>
    Nilai B: <input type="number" name="b" required><br><br>
    Sisi Persegi: <input type="number" name="sisi" required><br><br>

    <button type="submit" name="hitung">Hitung</button>
</form>

<br>

<?php
echo $hasil;
?>

</body>
</html>