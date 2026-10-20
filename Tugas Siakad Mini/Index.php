<?php
// ================= OOP =================

// Interface
interface CetakLaporan {
    public function cetak();
}

// Abstract Class
abstract class User {
    protected $id;
    protected $nama;

    public function __construct($id, $nama) {
        $this->id = $id;
        $this->nama = $nama;
    }

    abstract public function infoUser();
}

// Class Mahasiswa
class Mahasiswa extends User implements CetakLaporan {
    private $jurusan;
    private $semester;
    private $nilai = [];

    public function __construct($nim, $nama, $jurusan, $semester) {
        parent::__construct($nim, $nama);
        $this->jurusan = $jurusan;
        $this->semester = $semester;
    }

    public function tambahNilai($mk, $nilai, $sks) {
        $this->nilai[] = [
            "mk" => $mk,
            "nilai" => $nilai,
            "sks" => $sks
        ];
    }

    private function bobot($nilai) {
        if ($nilai >= 85) return 4;
        elseif ($nilai >= 75) return 3;
        elseif ($nilai >= 65) return 2;
        elseif ($nilai >= 50) return 1;
        else return 0;
    }

    private function grade($nilai) {
        if ($nilai >= 85) return "A";
        elseif ($nilai >= 75) return "B";
        elseif ($nilai >= 65) return "C";
        elseif ($nilai >= 50) return "D";
        else return "E";
    }

    public function hitungIPK() {
        $totalMutu = 0;
        $totalSKS = 0;

        foreach ($this->nilai as $n) {
            $totalMutu += $this->bobot($n["nilai"]) * $n["sks"];
            $totalSKS += $n["sks"];
        }

        return $totalMutu / $totalSKS;
    }

    public function totalMutu() {
        $total = 0;
        foreach ($this->nilai as $n) {
            $total += $this->bobot($n["nilai"]) * $n["sks"];
        }
        return $total;
    }

    public function totalSKS() {
        $total = 0;
        foreach ($this->nilai as $n) {
            $total += $n["sks"];
        }
        return $total;
    }

    public function cetak() {
        return $this->nilai;
    }

    public function infoUser() {
        return $this->nama;
    }

    public function getJurusan() {
        return $this->jurusan;
    }

    public function getSemester() {
        return $this->semester;
    }

    public function getGrade($nilai) {
        return $this->grade($nilai);
    }

    public function getMutu($nilai, $sks) {
        return $this->bobot($nilai) * $sks;
    }
}

// Class Dosen
class Dosen extends User {
    private $mataKuliah;

    public function __construct($id, $nama, $mataKuliah) {
        parent::__construct($id, $nama);
        $this->mataKuliah = $mataKuliah;
    }

    public function infoUser() {
        return $this->nama . " - " . $this->mataKuliah;
    }
}

// ================= DATA =================
$mhs = new Mahasiswa("I43251032", "Muhammad Yosi", "Bisnis Digital", 2);

$mhs->tambahNilai("Jaringan Komputer", 85, 2);
$mhs->tambahNilai("Literasi Digital", 88, 2);
$mhs->tambahNilai("Bahasa Indonesia", 84, 3);
$mhs->tambahNilai("Manajemen Pemasaran Digital", 85, 2);
$mhs->tambahNilai("Pendidikan Pancasila", 90, 4);
$mhs->tambahNilai("Analisis data", 88, 2);

$nilai = $mhs->cetak();
?>

<!DOCTYPE html>
<html>
<head>
    <title>SIAKAD MINI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h1>SISTEM AKADEMIK MINI</h1>

    <div class="card">
        <h2>Data Mahasiswa</h2>
        <p>NIM : I43251032</p>
        <p>Nama : <?= $mhs->infoUser(); ?></p>
        <p>Jurusan : <?= $mhs->getJurusan(); ?></p>
        <p>Semester : <?= $mhs->getSemester(); ?></p>
        <p class="ipk">IPK : <?= number_format($mhs->hitungIPK(), 2); ?></p>
    </div>

    <div class="card">
        <h2>Kartu Hasil Studi</h2>

        <table>
            <tr>
                <th>Mata Kuliah</th>
                <th>Nilai</th>
                <th>Grade</th>
                <th>Mutu</th>
                <th>SKS</th>
            </tr>

            <?php foreach($nilai as $n): ?>
            <tr>
                <td><?= $n["mk"]; ?></td>
                <td><?= $n["nilai"]; ?></td>
                <td><?= $mhs->getGrade($n["nilai"]); ?></td>
                <td><?= $mhs->getMutu($n["nilai"], $n["sks"]); ?></td>
                <td><?= $n["sks"]; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <p>Total Mutu : <?= $mhs->totalMutu(); ?></p>
        <p>Total SKS : <?= $mhs->totalSKS(); ?></p>
        <p class="ipk">IPK : <?= number_format($mhs->hitungIPK(), 2); ?></p>

        <button class="btn">Cetak KHS</button>
        <button class="btn">Input Nilai</button>
    </div>

</div>

</body>
</html>