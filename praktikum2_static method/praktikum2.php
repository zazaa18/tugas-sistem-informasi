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

    // Fungsi luas persegi
    public static function luasPersegi($sisi) {
        return $sisi * $sisi;
    }
}

// Proses dari form
$hasil = "";
if (isset($_POST['hitung'])) {
    $a = $_POST['a'];
    $b = $_POST['b'];
    $sisi = $_POST['sisi'];

    $hasil .= "Kali: " . Matematika::kali($a, $b) . "<br>";
    $hasil .= "Bagi: " . Matematika::bagi($a, $b) . "<br>";
    $hasil .= "Tambah: " . Matematika::tambah($a, $b) . "<br>";
    $hasil .= "Kurang: " . Matematika::kurang($a, $b) . "<br>";
    $hasil .= "Luas Persegi: " . Matematika::luasPersegi($sisi);
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