<?php

// CLASS PARENT
class Orang {

    public $nama;          // Bisa diakses dari mana saja
    protected $umur;       // Bisa diakses di class ini & turunannya
    private $password;     // HANYA bisa diakses di class ini saja

    public function __construct($nama, $umur, $password) {
        $this->nama = $nama;
        $this->umur = $umur;
        $this->password = $password;
    }

    public function tampilData() {
        echo "Nama: " . $this->nama . "<br>";
        echo "Umur: " . $this->umur . "<br>";
        echo "Password: " . $this->password . "<br>";`
    }
}


// CLASS CHILD (Inheritance)
class Mahasiswa extends Orang {

    public $jurusan;

    public function __construct($nama, $umur, $password, $jurusan) {
        parent::__construct($nama, $umur, $password);
        $this->jurusan = $jurusan;
    }

    public function tampilMahasiswa() {
        echo "Nama: " . $this->nama . "<br>";       // ✅ public (boleh)
        echo "Umur: " . $this->umur . "<br>";       // ✅ protected (boleh karena turunan)
        echo "Jurusan: " . $this->jurusan . "<br>";

        // echo $this->password; ❌ ERROR (private tidak bisa diwariskan)
    }
}


// Membuat objek
$mhs1 = new Mahasiswa("Zaza", 20, "123456", "Informatika");

echo "<h3>Akses dari Method:</h3>";
$mhs1->tampilMahasiswa();

echo "<hr>"

echo "<h3>Akses dari Luar Class:</h3>";
echo "Nama: " . $mhs1->nama . "<br>";   // ✅ Bisa (public)

// echo $mhs1->umur;    ❌ ERROR (protected)
// echo $mhs1->password; ❌ ERROR (private)

?>