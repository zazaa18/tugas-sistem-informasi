<?php
require_once "User.php";
require_once "Cetak.php";

class Dosen extends User implements CetakLaporan {
    private $mataKuliah;

    public function __construct($nama, $nim, $mataKuliah = "-") {
        parent::__construct($nama, $nim);
        $this->mataKuliah = $mataKuliah;
    }

    // ✅ TAMBAHAN (PENTING BANGET)
    public function getNama(){
        return $this->nama;
    }

    public function getNim(){
        return $this->nim;
    }

    public function getInfo() {
        return "
            <p>Nama Dosen : {$this->nama}</p>
            <p>NIDN : {$this->nim}</p>
        ";
    }

    public function cetakLaporan() {
        return "
            <div style='margin-top:20px'>
                <p><b>Dosen Wali</b></p>
                <p>Nama : {$this->nama}</p>
                <p>NIDN : {$this->nim}</p>
                <p>Mata Kuliah : {$this->mataKuliah}</p>
            </div>
        ";
    }
}
?>