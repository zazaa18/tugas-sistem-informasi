<?php
session_start();

// ================= INTERFACE =================
interface CetakLaporan {
    public function cetak();
}

// ================= ABSTRACT =================
abstract class User {
    protected $id;
    protected $nama;

    public function __construct($id, $nama) {
        $this->id = $id;
        $this->nama = $nama;
    }

    public function getId() {
        return $this->id;
    }

    abstract public function infoUser();
}

// ================= MATA KULIAH =================
class MataKuliah {
    private $nama;
    private $sks;

    public function __construct($nama, $sks) {
        $this->nama = $nama;
        $this->sks = $sks;
    }

    public function getNama() {
        return $this->nama;
    }

    public function getSks() {
        return $this->sks;
    }
}

// ================= MAHASISWA =================
class Mahasiswa extends User implements CetakLaporan {
    private $jurusan;
    private $semester;
    private $nilai = [];

    public function __construct($nim, $nama, $jurusan, $semester) {
        parent::__construct($nim, $nama);
        $this->jurusan = $jurusan;
        $this->semester = $semester;
    }

    public function tambahNilai($mk, $nilai) {
        $this->nilai[] = [
            'mk' => $mk->getNama(),
            'sks' => $mk->getSks(),
            'nilai' => $nilai
        ];
    }

    private function bobot($nilai) {
        if ($nilai >= 85) return 4;
        if ($nilai >= 75) return 3;
        if ($nilai >= 65) return 2;
        if ($nilai >= 50) return 1;
        return 0;
    }

    private function grade($nilai) {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 65) return 'C';
        if ($nilai >= 50) return 'D';
        return 'E';
    }

    public function totalMutu() {
        $total = 0;
        foreach ($this->nilai as $n) {
            $total += $this->bobot($n['nilai']) * $n['sks'];
        }
        return $total;
    }

    public function totalSKS() {
        $total = 0;
        foreach ($this->nilai as $n) {
            $total += $n['sks'];
        }
        return $total;
    }

    public function hitungIPK() {
        return $this->totalSKS() > 0 ? $this->totalMutu() / $this->totalSKS() : 0;
    }

    public function getGrade($nilai) {
        return $this->grade($nilai);
    }

    public function getMutu($nilai, $sks) {
        return $this->bobot($nilai) * $sks;
    }

    public function cetak() {
        return $this->nilai;
    }

    public function infoUser() {
        return $this->nama;
    }

    public function getJurusan() { return $this->jurusan; }
    public function getSemester() { return $this->semester; }
}

// ================= SESSION =================
if (!isset($_SESSION['nilai'])) $_SESSION['nilai'] = [];

if (!isset($_SESSION['mhs'])) {
    $_SESSION['mhs'] = [
        'nim' => '',
        'nama' => '',
        'jurusan' => '',
        'semester' => ''
    ];
}

if (!isset($_SESSION['form_nilai'])) {
    $_SESSION['form_nilai'] = [
        'kode_mk' => '',
        'mk' => '',
        'sks' => '',
        'nilai' => ''
    ];
}

// ================= HANDLE FORM =================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['reset_all'])) {
        $_SESSION['mhs'] = [
            'nim' => '',
            'nama' => '',
            'jurusan' => '',
            'semester' => ''
        ];

        $_SESSION['nilai'] = [];

        $_SESSION['form_nilai'] = [
            'kode_mk' => '',
            'mk' => '',
            'sks' => '',
            'nilai' => ''
        ];
    }

    elseif (isset($_POST['reset_mhs'])) {
        $_SESSION['mhs'] = [
            'nim' => '',
            'nama' => '',
            'jurusan' => '',
            'semester' => ''
        ];
    }

    elseif (isset($_POST['submit_mhs'])) {
        $_SESSION['mhs'] = [
            'nim' => $_POST['nim'],
            'nama' => $_POST['nama'],
            'jurusan' => $_POST['jurusan'],
            'semester' => $_POST['semester']
        ];
    }

    elseif (isset($_POST['submit_nilai'])) {
        $_SESSION['nilai'][] = [
            'kode_mk' => $_POST['kode_mk'],
            'mk' => $_POST['mk'],
            'sks' => $_POST['sks'],
            'nilai' => $_POST['nilai']
        ];

        $_SESSION['form_nilai'] = [
            'kode_mk' => $_POST['kode_mk'],
            'mk' => $_POST['mk'],
            'sks' => $_POST['sks'],
            'nilai' => $_POST['nilai']
        ];
    }

    elseif (isset($_POST['reset'])) {
        $_SESSION['nilai'] = [];

        $_SESSION['form_nilai'] = [
            'kode_mk' => '',
            'mk' => '',
            'sks' => '',
            'nilai' => ''
        ];
    }
}

// ================= DATA =================
$mhsData = $_SESSION['mhs'];
$formNilai = $_SESSION['form_nilai'];

$mhs = new Mahasiswa(
    $mhsData['nim'],
    $mhsData['nama'],
    $mhsData['jurusan'],
    $mhsData['semester']
);

$nilai = [];
foreach ($_SESSION['nilai'] as $n) {
    $mk = new MataKuliah($n['mk'], $n['sks']);
    $mhs->tambahNilai($mk, $n['nilai']);
    $nilai[] = $n;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SIAKAD MINI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrapper">

    <div class="header">
        <h1>SISTEM AKADEMIK</h1>
        <p>Politeknik Negeri Jember</p>
    </div>

    <!-- FORM DATA MAHASISWA -->
    <div class="profile-card">
        <h2>Data Mahasiswa</h2>
        <form method="POST">
            <input type="text" name="nim" placeholder="NIM" value="<?= $mhsData['nim']; ?>">
            <input type="text" name="nama" placeholder="Nama" value="<?= $mhsData['nama']; ?>">
            <input type="text" name="jurusan" placeholder="Jurusan" value="<?= $mhsData['jurusan']; ?>">
            <input type="number" name="semester" placeholder="Semester" value="<?= $mhsData['semester']; ?>">

            <button name="submit_mhs" class="btn">Simpan</button>
            <button name="reset_mhs" class="btn">Reset</button>
        </form>
    </div>

    <!-- INPUT NILAI -->
    <div class="khs-card">
        <h2>Form Input Nilai</h2>
        <form method="POST" style="display:flex; gap:10px; align-items:center;">
            <input type="text" name="kode_mk" placeholder="Kode MK" required value="<?= $formNilai['kode_mk']; ?>">
            <input type="text" name="mk" placeholder="Mata Kuliah" required value="<?= $formNilai['mk']; ?>">
            <input type="number" name="sks" placeholder="SKS" required value="<?= $formNilai['sks']; ?>">
            <input type="number" name="nilai" placeholder="Nilai" required value="<?= $formNilai['nilai']; ?>">

            <button name="submit_nilai" class="btn">Simpan</button>
            <button name="reset" class="btn">Reset Data</button>
        </form>
    </div>

    <!-- KHS -->
    <div class="khs-card">
        <h2>Cetak KHS</h2>

        <div style="margin-bottom:15px;">
            <p>NIM: <?= $mhsData['nim']; ?></p>
            <p>Nama: <?= $mhsData['nama']; ?></p>
            <p>Jurusan: <?= $mhsData['jurusan']; ?></p>
            <p>Semester: <?= $mhsData['semester']; ?></p>
        </div>

        <h3>Kartu Hasil Studi</h3>

        <table>
            <tr>
                <th>Mata Kuliah</th>
                <th>Nilai</th>
                <th>Mutu</th>
                <th>SKS</th>
            </tr>

            <?php foreach($nilai as $n): ?>
            <tr>
                <td><?= $n['mk']; ?></td>
                <td><?= $n['nilai']; ?></td>
                <td><?= $mhs->getMutu($n['nilai'], $n['sks']); ?></td>
                <td><?= $n['sks']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <p><strong>IPK: <?= number_format($mhs->hitungIPK(), 2); ?></strong></p>

        <div class="button-group">
            <button class="btn" onclick="window.print()">Cetak KHS</button>

            <form method="POST" style="display:inline;">
                <button name="reset_all" class="btn">Reset Semua Data</button>
            </form>
        </div>
    </div>

</div>
</body>
</html>