<?php
require_once "Mahasiswa.php";
require_once "Dosen.php";
session_start();

/* SESSION */
$_SESSION['mahasiswa'] = $_SESSION['mahasiswa'] ?? [];
$_SESSION['matakuliah'] = $_SESSION['matakuliah'] ?? [];
$_SESSION['last_mhs'] = $_SESSION['last_mhs'] ?? [
    'nama'=>'','nim'=>'','jurusan'=>'','semester'=>''
];
$_SESSION['last_matkul'] = $_SESSION['last_matkul'] ?? [
    'kodeMatkul'=>'','matkul'=>'','sks'=>''
];
$_SESSION['last_nilai'] = $_SESSION['last_nilai'] ?? '';

$dosenDefault = new Dosen("Rina Lestari", "198765");

/* RESET */
if(isset($_POST['resetAll'])){
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

if(isset($_POST['resetMhs'])){
    $_SESSION['mahasiswa'] = [];
    $_SESSION['last_mhs'] = [
        'nama'=>'','nim'=>'','jurusan'=>'','semester'=>''
    ];
}

if(isset($_POST['resetMatkul'])){
    $_SESSION['matakuliah'] = [];
    $_SESSION['last_matkul'] = [
        'kodeMatkul'=>'','matkul'=>'','sks'=>''
    ];
}

if(isset($_POST['resetNilai'])){
    foreach($_SESSION['mahasiswa'] as $mhs){
        $mhs->resetNilai();
    }
    $_SESSION['last_nilai'] = '';
}

/* SIMPAN MAHASISWA */
if(isset($_POST['addMhs'])){
    $m = new Mahasiswa(
        $_POST['nama'],
        $_POST['nim'],
        $_POST['jurusan'],
        $_POST['semester'],
        $dosenDefault
    );

    $_SESSION['mahasiswa'][$_POST['nim']] = $m;
    $_SESSION['last_mhs'] = $_POST;
}

/* SIMPAN MATKUL */
if(isset($_POST['addMatkul'])){
    $_SESSION['matakuliah'][$_POST['kodeMatkul']] = [
        'nama'=>$_POST['matkul'],
        'sks'=>$_POST['sks']
    ];

    $_SESSION['last_matkul'] = $_POST;
}

/* INPUT NILAI */
if(isset($_POST['inputNilai'])){
    $_SESSION['last_nilai'] = ''; // 🔥 bikin input kosong setelah submit

    $nim = $_POST['nim'];
    $kode = $_POST['kodeMatkul'];
    $nilai = $_POST['nilai'];

    $matkul = $_SESSION['matakuliah'][$kode]['nama'];
    $sks = $_SESSION['matakuliah'][$kode]['sks'];

    $_SESSION['mahasiswa'][$nim]->addNilai($matkul,$nilai,$sks);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sistem Akademik Mahasiswa</title>
    <link rel="stylesheet" href="style.css">

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .khs-area, .khs-area * {
                visibility: visible;
            }
            .khs-area {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <h1>SISTEM AKADEMIK MAHASISWA</h1>
        <p class="kampus-text">Politeknik Negeri Jember</p>
    </div>

    <form method="post" style="text-align:right;">
        <button type="submit" name="resetAll" class="reset-btn">
            Reset Semua Data
        </button>
    </form>

    <h2>Data Mahasiswa</h2>
    <form method="post" class="form-card">
        <div class="form-group">
            <label>Nama</label><span class="colon">:</span>
            <input type="text" name="nama" value="<?= $_SESSION['last_mhs']['nama'] ?>" required>
        </div>

        <div class="form-group">
            <label>NIM</label><span class="colon">:</span>
            <input type="text" name="nim" value="<?= $_SESSION['last_mhs']['nim'] ?>" required>
        </div>

        <div class="form-group">
            <label>Jurusan</label><span class="colon">:</span>
            <input type="text" name="jurusan" value="<?= $_SESSION['last_mhs']['jurusan'] ?>" required>
        </div>

        <div class="form-group">
            <label>Semester</label><span class="colon">:</span>
            <input type="number" name="semester" value="<?= $_SESSION['last_mhs']['semester'] ?>" required>
        </div>

        <div class="button-group">
            <button type="submit" name="addMhs">Simpan Data</button>
            <button type="submit" name="resetMhs" class="reset-btn">Reset Mahasiswa</button>
        </div>
    </form>

    <h2>Mata Kuliah</h2>
    <form method="post" class="form-card">
        <div class="form-group">
            <label>Kode Mata Kuliah</label><span class="colon">:</span>
            <input type="text" name="kodeMatkul" value="<?= $_SESSION['last_matkul']['kodeMatkul'] ?>" required>
        </div>

        <div class="form-group">
            <label>Nama Mata Kuliah</label><span class="colon">:</span>
            <input type="text" name="matkul" value="<?= $_SESSION['last_matkul']['matkul'] ?>" required>
        </div>

        <div class="form-group">
            <label>SKS</label><span class="colon">:</span>
            <input type="number" name="sks" value="<?= $_SESSION['last_matkul']['sks'] ?>" required>
        </div>

        <div class="button-group">
            <button type="submit" name="addMatkul">Simpan</button>
            <button type="submit" name="resetMatkul" class="reset-btn">Reset Mata Kuliah</button>
        </div>
    </form>

    <h2>Input Nilai</h2>
    <form method="post" class="form-card">
        <div class="form-group">
            <label>Pilih Mahasiswa</label><span class="colon">:</span>
            <select name="nim">
                <?php foreach($_SESSION['mahasiswa'] as $nim=>$mhs){ ?>
                    <option value="<?= $nim ?>">
                        <?= $nim ?> - <?= $mhs->getNama() ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Pilih Mata Kuliah</label><span class="colon">:</span>
            <select name="kodeMatkul">
                <?php foreach($_SESSION['matakuliah'] as $kode=>$mk){ ?>
                    <option value="<?= $kode ?>">
                        <?= $kode ?> - <?= $mk['nama'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Nilai</label><span class="colon">:</span>
            <input type="number" name="nilai" value="<?= $_SESSION['last_nilai'] ?>" required>
        </div>

        <div class="button-group">
            <button type="submit" name="inputNilai">Simpan Nilai</button>
            <button type="submit" name="resetNilai" class="reset-btn">Reset Nilai</button>
        </div>
    </form>

    <!-- KHS -->
    <?php
    foreach($_SESSION['mahasiswa'] as $mhs){

        echo "<div class='khs-area'>";

        echo "<h2>Kartu Hasil Studi</h2>";

        echo "<div class='info-khs'>";
        echo $mhs->getInfo();
        echo "</div>";

        $mhs->cetakLaporan();

        echo "</div>";

        echo "<button class='print-btn' onclick='window.print()'>Cetak KHS</button><hr>";
    }
    ?>

</div>
</body>
</html>